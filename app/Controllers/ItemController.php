<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;
use Config\Database;
use Config\Services;
use App\Models\ItemModel;
use App\Models\AssetManagerModel;
use App\Models\AssetCategoryModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ItemController extends BaseController
{
    protected $db;
    protected $itemModel;
    protected $assetManagerModel;
    protected $assetCategoryModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->itemModel = new ItemModel();
        $this->assetManagerModel = new AssetManagerModel();
        $this->assetCategoryModel = new AssetCategoryModel();
        $this->validator = Services::validation();
    }

    public function getItemDt()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Only AJAX requests allowed']);
        }

        $builder = $this->db->table('items')
            ->select('items.id, m.name as managers_name, c.code as categories_code, c.name as categories_name, items.asset_categories_id, items.name, items.brand, items.model, items.serial_number, items.vendor, items.image, items.description, items.acquisition_date, items.created_at')
            ->join('asset_managers m', 'm.id = items.asset_managers_id', 'left')
            ->join('asset_categories c', 'c.id = items.asset_categories_id', 'left')
            ->where('items.deleted_at', null)
            ->orderBy('items.created_at', 'desc');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                if ($request->managers_filter) {
                    $builder->where('items.asset_managers_id', $request->managers_filter);
                }
                if ($request->year_filter) {
                    $builder->where('YEAR(items.acquisition_date)', $request->year_filter);
                }
            })
            ->addNumbering()
            ->toJson(true);
    }

    public function getItemCounter()
    {
        return $this->response->setJson($this->itemModel->itemCounter());
    }

    public function index()
    {
        $webProperties = [
            'titleHeader' => 'Daftar Barang',
            'titlePage' => 'Daftar Barang',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Daftar Barang']
            ]
        ];
        $itemCounter = $this->itemModel->itemCounter();

        return view('/item/index', ['webProperties' => $webProperties, 'itemCounter' => $itemCounter]);
    }

    public function create()
    {
        $webProperties = [
            'titleHeader' => 'Buat Barang',
            'titlePage' => 'Buat Barang',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Daftar Barang', 'url' => base_url('/items')],
                ['label' => 'Buat']
            ]
        ];
        $assetCategories = $this->db->table('asset_categories')->select('id, asset_managers_id, name, code')->where('deleted_at', null)->limit(5)->get()->getResult();

        return view('/item/create', [
            'webProperties' => $webProperties,
            'assetCategories' => $assetCategories
        ]);
    }

    public function store()
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);

            $imageFile = $this->request->getFile('image');

            $this->validator->setRules([
                'asset_managers_id'     => 'required|integer|is_natural_no_zero',
                'asset_categories_id'   => 'required|integer|is_natural_no_zero',
                'name'                  => 'required|min_length[3]|max_length[100]',
                'brand'                 => 'required|min_length[3]|max_length[50]',
                'model'                 => 'required|min_length[3]|max_length[50]',
                'serial_number'         => 'required|min_length[3]|max_length[50]',
                'description'           => 'permit_empty|max_length[500]',
                'vendor'                => 'required|min_length[3]|max_length[100]',
                'acquisition_date'      => 'required|valid_date[Y-m-d]',
                'image'                 => 'permit_empty|is_image[image]|max_size[image,2048]', // max size 2MB
                'image'                 => [
                    'rules' => 'permit_empty|uploaded[image]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]|max_size[image,2048]',
                    'errors' => [
                        'uploaded' => 'Gambar harus diunggah.',
                        'is_image' => 'File yang diunggah harus berupa gambar.',
                        'mime_in'  => 'Format gambar harus JPG, JPEG, atau PNG',
                        'max_size' => 'Ukuran gambar tidak boleh lebih dari 2MB.'
                    ]
                ]
            ]);

            if (!$this->validator->run($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->validator->getErrors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $imageName = null;
                if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                    $imageName = $imageFile->getRandomName();
                    $uploadPath = ROOTPATH . 'public/uploads/images/barang';

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    if ($imageFile->move($uploadPath, $imageName)) {
                        $data['image'] = $imageName;
                    } else {
                        throw new \Exception('Gagal mengunggah gambar');
                    }
                }

                $insertId = $this->itemModel->insert($data);

                if (!$insertId) {
                    if ($imageName && file_exists($uploadPath . $imageName)) {
                        unlink($uploadPath . $imageName);
                    }
                    throw new \Exception('Gagal menyimpan data ke database');
                }

                return $this->jsonResponse('success', 'Barang berhasil dibuat', $insertId, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Barang berhasil dibuat',
                ], 201);
            } catch (\Exception $e) {
                if (isset($imageName) && $imageName && file_exists(ROOTPATH . 'public/uploads/images/barang' . $imageName)) {
                    unlink(ROOTPATH . 'public/uploads/images/barang' . $imageName);
                }

                return $this->jsonResponse('error', 'Gagal membuat barang', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal membuat barang, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function show($id)
    {
        $webProperties = [
            'titleHeader' => 'Detil Barang',
            'titlePage' => 'Detil Barang',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Daftar Barang', 'url' => base_url('/items')],
                ['label' => 'Detil']
            ]
        ];
        $itemData = $this->itemModel->find($id);
        $assetManager = $this->assetManagerModel->find($itemData['asset_managers_id']);
        $assetCategory = $this->assetCategoryModel->find($itemData['asset_categories_id']);

        return view('item/show', ['webProperties' => $webProperties, 'itemData' => $itemData, 'assetManagerData' => $assetManager, 'assetCategoryData' => $assetCategory]);
    }

    public function edit($id)
    {
        $webProperties = [
            'titleHeader' => 'Ubah Barang',
            'titlePage' => 'Ubah Barang',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Daftar Barang', 'url' => base_url('/items')],
                ['label' => 'Ubah']
            ]
        ];

        if (!$id || !is_numeric($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Item tidak ditemukan');
        }

        $item = $this->itemModel->find($id);

        if (!$item) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Item tidak ditemukan');
        }

        $assetManagers = $this->db->table('asset_managers')
            ->select('id, name, code')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResult();

        $assetCategories = $this->db->table('asset_categories')
            ->select('id, asset_managers_id, name, code')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResult();

        return view('item/edit', [
            'webProperties' => $webProperties,
            'item' => $item,
            'assetManagers' => $assetManagers,
            'assetCategories' => $assetCategories
        ]);
    }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);
            $imageFile = $this->request->getFile('image');

            $this->validator->setRules([
                'asset_managers_id' => 'required|numeric',
                'asset_categories_id' => 'required|numeric',
                'name' => 'required|min_length[3]|max_length[255]',
                'brand' => 'required|min_length[2]|max_length[100]',
                'model' => 'required|min_length[2]|max_length[100]',
                'serial_number' => 'required|min_length[3]|max_length[100]',
                'vendor' => 'required|min_length[3]|max_length[255]',
                'acquisition_date' => 'required|valid_date',
                'image' => 'if_exist|uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'
            ]);

            if (!$this->validator->run($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->validator->getErrors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $existingData = $this->itemModel->find($id);
                if (!$existingData) {
                    throw new \Exception('Data tidak ditemukan');
                }

                $oldImageName = $existingData['image'];
                $imageName = $oldImageName;
                $uploadPath = realpath(ROOTPATH . 'public/uploads/images/barang/');

                if (!$uploadPath) {
                    throw new \Exception('Upload path tidak valid');
                }
                $uploadPath .= '/';

                // Jika ada file gambar baru yang diupload
                if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved() && $imageFile->getSize() > 0) {
                    // Validasi extension tambahan
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    $fileExtension = strtolower($imageFile->getClientExtension());
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        throw new \Exception('Format file tidak didukung');
                    }

                    $imageName = $imageFile->getRandomName();

                    if (!is_dir($uploadPath)) {
                        if (!mkdir($uploadPath, 0755, true)) {
                            throw new \Exception('Gagal membuat direktori upload');
                        }
                    }

                    if (!$imageFile->move($uploadPath, $imageName)) {
                        throw new \Exception('Gagal mengunggah gambar baru');
                    }

                    $data['image'] = $imageName;
                }

                $updateResult = $this->itemModel->update($id, $data);
                if (!$updateResult) {
                    // Jika update gagal dan ada gambar baru, hapus gambar baru
                    if (isset($data['image']) && $data['image'] !== $oldImageName && file_exists($uploadPath . $imageName)) {
                        unlink($uploadPath . $imageName);
                    }
                    throw new \Exception('Gagal mengupdate data ke database');
                }

                // Jika update berhasil dan ada gambar baru, hapus gambar lama
                if (isset($data['image']) && $data['image'] !== $oldImageName && $oldImageName && file_exists($uploadPath . $oldImageName)) {
                    unlink($uploadPath . $oldImageName);
                }

                return $this->jsonResponse('success', 'Barang berhasil diubah', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Barang berhasil diubah',
                ], 200);
            } catch (\Throwable $th) {
                // Jika ada error dan sudah upload gambar baru, hapus gambar baru
                if (isset($data['image']) && isset($imageName) && $imageName !== $oldImageName && file_exists($uploadPath . $imageName)) {
                    unlink($uploadPath . $imageName);
                }

                return $this->jsonResponse('error', 'Gagal mengubah barang', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal mengubah barang, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->itemModel->delete($id);

                return $this->jsonResponse('success', 'Barang berhasil dihapus', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Barang berhasil dihapus',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal menghapus barang', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal menghapus barang, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header template
            $sheet->setCellValue('A1', 'Pengelola Aset');
            $sheet->setCellValue('B1', 'Kategori Aset');
            $sheet->setCellValue('C1', 'Nama Barang');
            $sheet->setCellValue('D1', 'Merek');
            $sheet->setCellValue('E1', 'Model');
            $sheet->setCellValue('F1', 'Nomor Seri');
            $sheet->setCellValue('G1', 'Vendor');
            $sheet->setCellValue('H1', 'Spesifikasi');
            $sheet->setCellValue('I1', 'Tanggal Perolehan');

            // Styling header
            $sheet->getStyle('A1:I1')->getFont()->setBold(true);
            $sheet->getStyle('A1:I1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');

            $this->createHelperTable($sheet);

            // Contoh data
            $sheet->setCellValue('A2', '1');
            $sheet->setCellValue('B2', '1');
            $sheet->setCellValue('C2', 'Laptop Dell XPS 13');
            $sheet->setCellValue('D2', 'Dell');
            $sheet->setCellValue('E2', 'XPS 13');
            $sheet->setCellValue('F2', 'SN123456789');
            $sheet->setCellValue('G2', 'Dell Inc.');
            $sheet->setCellValue('H2', 'Laptop ultrabook dengan performa tinggi');
            $sheet->setCellValue('I2', '2024-01-01');

            $sheet->setCellValue('A3', '2');
            $sheet->setCellValue('B3', '3');
            $sheet->setCellValue('C3', 'EPSON Printer L3110');
            $sheet->setCellValue('D3', 'Epson');
            $sheet->setCellValue('E3', 'L3110');
            $sheet->setCellValue('F3', 'SN9876543210');
            $sheet->setCellValue('G3', 'Epson Inc.');
            $sheet->setCellValue('H3', 'Printer dengan resolusi tinggi');
            $sheet->setCellValue('I3', '2025-02-01');

            $sheet->setCellValue('A4', '3');
            $sheet->setCellValue('B4', '5');
            $sheet->setCellValue('C4', 'HIKVISION CCTV');
            $sheet->setCellValue('D4', 'HIKVISION');
            $sheet->setCellValue('E4', 'DS-2CD2347G1-LU');
            $sheet->setCellValue('F4', 'SN987654321');
            $sheet->setCellValue('G4', 'HIKVISION');
            $sheet->setCellValue('H4', 'CCTV dengan resolusi tinggi');
            $sheet->setCellValue('I4', '2026-03-01');

            // Auto resize columns
            foreach (range('A', 'I') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_import_barang.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Throwable $th) {
            return $this->jsonResponse('error', 'Error download template', [], [$th->getMessage()], [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error download template, error: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function importExcel()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'excel_file' => [
                'label' => 'File Excel',
                'rules' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,2048]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        if ($file->isValid() && !$file->hasMoved()) {
            try {
                // Pindahkan file ke folder sementara
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);
                $filePath = WRITEPATH . 'uploads/' . $newName;

                // Load file Excel
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                $data = [];
                $errors = [];
                $successCount = 0;

                // Loop mulai dari baris 2 (skip header)
                for ($row = 2; $row <= $highestRow; $row++) {
                    $managers_id = $worksheet->getCell('A' . $row)->getValue();
                    $categories_id = $worksheet->getCell('B' . $row)->getValue();
                    $name = $worksheet->getCell('C' . $row)->getValue();
                    $brand = $worksheet->getCell('D' . $row)->getValue();
                    $model = $worksheet->getCell('E' . $row)->getValue();
                    $serial_number = $worksheet->getCell('F' . $row)->getValue();
                    $vendor = $worksheet->getCell('G' . $row)->getValue();
                    $description = $worksheet->getCell('H' . $row)->getValue();
                    $acquisition_date = $worksheet->getCell('I' . $row)->getValue();

                    // Validasi data
                    if (empty($managers_id)) {
                        $errors[] = "Baris $row: Pengelola aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($categories_id)) {
                        $errors[] = "Baris $row: Kategori aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($name)) {
                        $errors[] = "Baris $row: Nama produk tidak boleh kosong";
                        continue;
                    }
                    if (empty($brand)) {
                        $errors[] = "Baris $row: Merek tidak boleh kosong";
                        continue;
                    }
                    if (empty($model)) {
                        $errors[] = "Baris $row: Model tidak boleh kosong";
                        continue;
                    }
                    if (empty($serial_number)) {
                        $errors[] = "Baris $row: Nomor seri tidak boleh kosong";
                        continue;
                    }
                    if (empty($vendor)) {
                        $errors[] = "Baris $row: Vendor tidak boleh kosong";
                        continue;
                    }
                    if (empty($acquisition_date) || !\DateTime::createFromFormat('Y-m-d', $acquisition_date)) {
                        $errors[] = "Baris $row: Tanggal perolehan tidak valid";
                        continue;
                    }

                    // Siapkan data untuk insert
                    $rowData = [
                        'asset_managers_id' => $managers_id,
                        'asset_categories_id' => $categories_id,
                        'name' => $name,
                        'brand' => $brand,
                        'model' => $model,
                        'serial_number' => $serial_number,
                        'vendor' => $vendor,
                        'description' => $description ?? '',
                        'acquisition_date' => date('Y-m-d', strtotime($acquisition_date)),
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    // Insert data
                    if ($this->itemModel->insert($rowData)) {
                        $successCount++;
                    } else {
                        $errors[] = "Baris $row: Gagal menyimpan data";
                    }
                }

                // Hapus file sementara
                unlink($filePath);

                // Pesan hasil import
                $message = "Import selesai. $successCount data berhasil diimport.";
                if (!empty($errors)) {
                    $message .= " " . count($errors) . " data gagal.";
                }

                return redirect()->to('/items')->with('message', $message)->with('errors', $errors);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
            }
        }

        return $this->jsonResponse('error', 'File tidak valid', [], [], [
            'type' => 'error',
            'title' => 'Error',
            'message' => 'File tidak valid',
        ], 500);
    }

    public function exportExcel()
    {
        try {
            $items = $this->db->table('items')
                ->select('items.*, am.name as managers_name, ac.code as categories_code, ac.name as categories_name')
                ->join('asset_managers as am', 'am.id = items.asset_managers_id', 'left')
                ->join('asset_categories as ac', 'ac.id = items.asset_categories_id', 'left')
                ->where('items.deleted_at', null)
                ->get()->getResultArray();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'Pengelola Aset');
            $sheet->setCellValue('B1', 'Kategori Aset');
            $sheet->setCellValue('C1', 'Nama Barang');
            $sheet->setCellValue('D1', 'Merek');
            $sheet->setCellValue('E1', 'Model');
            $sheet->setCellValue('F1', 'Nomor Seri');
            $sheet->setCellValue('G1', 'Vendor');
            $sheet->setCellValue('H1', 'Spesifikasi');
            $sheet->setCellValue('I1', 'Tanggal Perolehan');

            // Styling header
            $sheet->getStyle('A1:I1')->getFont()->setBold(true);
            $sheet->getStyle('A1:I1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');

            // Data
            $row = 2;
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $item['managers_name']);
                $sheet->setCellValue('B' . $row, $item['categories_code'] . ' - ' . $item['categories_name']);
                $sheet->setCellValue('C' . $row, $item['name']);
                $sheet->setCellValue('D' . $row, $item['brand']);
                $sheet->setCellValue('E' . $row, $item['model']);
                $sheet->setCellValue('F' . $row, $item['serial_number']);
                $sheet->setCellValue('G' . $row, $item['vendor']);
                $sheet->setCellValue('H' . $row, strip_tags($item['description']));
                $sheet->setCellValue('I' . $row, $item['acquisition_date']);
                $row++;
            }

            // Auto resize columns
            foreach (range('A', 'I') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Generate file
            $writer = new Xlsx($spreadsheet);
            $filename = 'export_barang_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Set headers untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error export: ' . $e->getMessage());
        }
    }

    public function createHelperTable($sheet)
    {
        // tabel bantuan
        $sheet->setCellValue('L2', 'Tabel bantuan:');
        $sheet->setCellValue('L3', 'Pengelola Aset');
        $sheet->setCellValue('M3', 'Kategori Aset');

        $sheet->getStyle('L3:M3')->getFont()->setBold(true);
        $sheet->getStyle('L3:M3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF87CEEB');

        // Auto resize columns
        foreach (range('L', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $assetManagers = $this->assetManagerModel->where('deleted_at', null)->findAll();
        $row = 4;
        foreach ($assetManagers as $assetManager) {
            $sheet->setCellValue("L{$row}", $assetManager['id'] . ' - ' . $assetManager['name']);
            $row++;
        }

        $assetCategories = $this->assetCategoryModel->where('deleted_at', null)->findAll();
        $row = 4;
        foreach ($assetCategories as $assetCategory) {
            $sheet->setCellValue("M{$row}", $assetCategory['id'] . ' - ' . $assetCategory['name']);
            $row++;
        }
    }
}
