<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;
use Config\Database;
use Config\Services;
use App\Models\AssetFixedModel;
use App\Models\AssetManagerModel;
use App\Models\AssetLocationModel;
use App\Models\AssetCategoryModel;
use App\Models\ItemModel;
use App\Models\QrCodeModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class AssetFixedController extends BaseController
{
    protected $db;
    protected $assetFixedModel;
    protected $assetManagerModel;
    protected $assetLocationModel;
    protected $assetCategoryModel;
    protected $qrCodeModel;
    protected $itemModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetFixedModel = new AssetFixedModel();
        $this->assetManagerModel = new AssetManagerModel();
        $this->assetLocationModel = new AssetLocationModel();
        $this->assetCategoryModel = new AssetCategoryModel();
        $this->itemModel = new ItemModel();
        $this->qrCodeModel = new QrCodeModel();
        $this->validator = Services::validation();
    }

    public function getAssetFixedDt()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Only AJAX requests are allowed.']);
        }

        $builder = $this->db->table('asset_fixed as af')
            ->select('af.id, af.unit, af.condition, af.qr_code_id, af.responsible_person, af.economic_life, af.acquisition_cost, am.name as managers_name, al.name as locations_name, i.name as items_name, i.acquisition_date as item_acquisition_date, qr.content as qr_codes, qr.image as qr_image')
            ->join('items as i', 'af.item_id = i.id', 'left')
            ->join('qr_codes as qr', 'af.qr_code_id = qr.id', 'left')
            ->join('asset_managers as am', 'af.asset_manager_id = am.id', 'left')
            ->join('asset_locations as al', 'af.asset_location_id = al.id', 'left')
            ->where('af.deleted_at', null)
            ->orderBy('af.created_at', 'desc');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                if ($request->locations_filter) {
                    $builder->where('af.asset_location_id', $request->locations_filter);
                }
                if ($request->year_filter) {
                    $builder->where('YEAR(i.acquisition_date)', $request->year_filter);
                }
            })
            ->addNumbering()
            ->toJson(true);
    }

    public function index()
    {
        $webProperties = [
            'titleHeader' => 'Aset Berwujud',
            'titlePage' => 'Aset Berwujud',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Aset Berwujud']
            ]
        ];

        return view('asset-fixed/index', ['webProperties' => $webProperties]);
    }

    public function create()
    {
        $webProperties = [
            'titleHeader' => 'Buat Aset Berwujud',
            'titlePage' => 'Buat Aset Berwujud',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Aset Berwujud', 'url' => base_url('/asset-fixed')],
                ['label' => 'Buat']
            ]
        ];
        $items = $this->db->table('items')->select('id, asset_managers_id, name')->where('deleted_at', null)->limit(5)->get()->getResult();

        return view('/asset-fixed/create', [
            'webProperties' => $webProperties,
            'items' => $items,
        ]);
    }

    public function store()
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);

            $this->validator->setRules([
                'asset_manager_id'     => 'required|integer|is_natural_no_zero',
                'asset_location_id'    => 'required|integer|is_natural_no_zero',
                'item_id'               => 'required|integer|is_natural_no_zero',
                'quantity'              => 'required|integer|is_natural_no_zero',
                'unit'                  => 'required|string|max_length[50]',
                'condition'             => 'required|in_list[baru,bekas,lama,rusak,hilang]',
                'responsible_person'    => 'required|string|max_length[100]',
                'economic_life'         => 'required|integer|is_natural_no_zero',
                'acquisition_cost'      => 'required|decimal',
                'generate_qr'           => 'permit_empty|in_list[1,0]',
            ]);

            if (!$this->validator->run($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->validator->getErrors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            $quantity = (int) $data['quantity'];
            $createdAssets = [];

            $this->db->transStart();

            try {
                for ($i = 1; $i <= $quantity; $i++) {
                    $assetCode = $this->assetFixedModel->generateAssetCode(
                        $data['item_id'],
                        $data['asset_manager_id'],
                        $data['asset_location_id']
                    );

                    $qrCodeData = [
                        'content'   => $assetCode,
                        'image'     => null,
                    ];

                    $qrCodeId = $this->qrCodeModel->insert($qrCodeData);
                    if (!$qrCodeId) {
                        throw new \Exception('Gagal membuat QR code');
                    }

                    $assetData = [
                        'asset_manager_id'      => $data['asset_manager_id'],
                        'asset_location_id'     => $data['asset_location_id'],
                        'item_id'               => $data['item_id'],
                        'qr_code_id'            => $qrCodeId,
                        'unit'                  => $data['unit'],
                        'condition'             => $data['condition'],
                        'responsible_person'    => $data['responsible_person'],
                        'economic_life'         => $data['economic_life'],
                        'acquisition_cost'      => $data['acquisition_cost'],
                    ];

                    $insertedId = $this->assetFixedModel->insert($assetData);

                    if ($insertedId) {
                        if (isset($data['generate_qr']) && $data['generate_qr'] == 1) {
                            // generate qr code image
                            $siteUrl = base_url('aset-berwujud/detail/');
                            $qrUrl = base64_encode($assetCode);
                            $combine = $siteUrl . $qrUrl;
                            $qrImagePath = $this->generateQRCodeImage($combine, $qrCodeId);

                            // Update qr_codes table with image path
                            $this->qrCodeModel->update($qrCodeId, [
                                'image' => $qrImagePath,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }

                        $createdAssets[] = [
                            'id'            => $insertedId,
                            'asset_code'    => $assetCode
                        ];
                    }
                }

                $this->db->transComplete();

                if ($this->db->transStatus() === false) {
                    throw new \Exception('Gagal menyimpan data aset');
                }

                return $this->jsonResponse('success', 'Aset berhasil dibuat', [
                    'total_created' => count($createdAssets),
                    'assets' => $createdAssets,
                    'original_quantity' => $quantity,
                ], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Aset berhasil dibuat',
                ], 201);
            } catch (\Throwable $th) {
                $this->db->transRollback();

                return $this->jsonResponse('error', 'Gagal membuat aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal membuat aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function show($id)
    {
        $webProperties = [
            'titleHeader' => 'Detil Aset Berwujud',
            'titlePage' => 'Detil Aset Berwujud',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Daftar Aset Berwujud', 'url' => base_url('/asset-fixed')],
                ['label' => 'Detil']
            ]
        ];
        $assetFixedData = $this->assetFixedModel->find($id);
        $itemData = $this->itemModel->find($assetFixedData['item_id']);
        $assetManager = $this->assetManagerModel->find($assetFixedData['asset_manager_id']);
        $assetCategory = $this->assetCategoryModel->find($itemData['asset_categories_id']);
        $assetLocation = $this->assetLocationModel->find($assetFixedData['asset_location_id']);
        $qrCode = $this->qrCodeModel->find($assetFixedData['qr_code_id']);

        return view('asset-fixed/show', ['webProperties' => $webProperties, 'itemData' => $itemData, 'assetManagerData' => $assetManager, 'assetFixedData' => $assetFixedData, 'assetCategoryData' => $assetCategory, 'assetLocationData' => $assetLocation, 'qrCode' => $qrCode]);
    }

    public function showByCode($content)
    {
        $cleanContent = base64_decode($content);
        $qrCode = $this->qrCodeModel->where('content', $cleanContent)->first();
        $assetFixed = $this->assetFixedModel->where('qr_code_id', $qrCode['id'])->first();
        $item = $this->itemModel->find($assetFixed['item_id']);
        $assetManager = $this->assetManagerModel->find($assetFixed['asset_manager_id']);
        $assetCategory = $this->assetCategoryModel->find($item['asset_categories_id']);
        $assetLocation = $this->assetLocationModel->find($assetFixed['asset_location_id']);

        return view('asset-fixed/show_public', ['assetFixedData' => $assetFixed, 'itemData' => $item, 'assetManagerData' => $assetManager, 'assetCategoryData' => $assetCategory, 'assetLocationData' => $assetLocation, 'qrCode' => $qrCode]);
    }

    public function edit($id)
    {
        $webProperties = [
            'titleHeader' => 'Edit Aset Berwujud',
            'titlePage' => 'Edit Aset Berwujud',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Aset Berwujud', 'url' => base_url('/asset-fixed')],
                ['label' => 'Edit']
            ]
        ];
        $assetFixedData = $this->assetFixedModel->find($id);
        $itemData = $this->itemModel->select('id, name, asset_categories_id')->find($assetFixedData['item_id']);
        $assetManager = $this->assetManagerModel->find($assetFixedData['asset_manager_id']);
        $assetCategory = $this->assetCategoryModel->find($itemData['asset_categories_id']);
        $assetLocation = $this->assetLocationModel->find($assetFixedData['asset_location_id']);
        $qrCode = $this->qrCodeModel->find($assetFixedData['qr_code_id']);

        return view('asset-fixed/edit', ['webProperties' => $webProperties, 'itemData' => $itemData, 'assetManagerData' => $assetManager, 'assetFixedData' => $assetFixedData, 'assetCategoryData' => $assetCategory, 'assetLocationData' => $assetLocation, 'qrCode' => $qrCode]);
    }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);

            $this->validator->setRules([
                'unit'                  => 'required|string|max_length[50]',
                'condition'             => 'required|in_list[baru,bekas,lama,rusak,hilang]',
                'responsible_person'    => 'required|string|max_length[100]',
                'economic_life'         => 'required|integer|is_natural_no_zero',
                'generate_qr'           => 'permit_empty|in_list[1,0]',
            ]);

            if (!$this->validator->run($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->validator->getErrors(), [
                    'type'  => 'error',
                    'title' => 'Validasi gagal',
                    'message'   => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->db->transStart();

                $assetFixedData = $this->assetFixedModel->find($id);
                $qrCode = $this->qrCodeModel->find($assetFixedData['qr_code_id']);

                // jika qr code ada tapi val generate_qr = 0, hapus qr code
                if ($data['generate_qr'] == 0) {
                    if ($qrCode && $qrCode['image']) {
                        $imagePath = ROOTPATH . 'public/uploads/qr_codes/' . $qrCode['image'];

                        if (is_file($imagePath)) {
                            unlink($imagePath);
                        } else {
                            log_message('error', 'QR code image file not found: ' . $imagePath);
                        }

                        $this->qrCodeModel->update($qrCode['id'], ['image' => null]);
                    }
                }

                // jika qr tidak ada dan val generate_qr = 1, buat qr code baru
                if ($qrCode && $qrCode['image'] == null && $data['generate_qr'] == 1) {
                    $siteUrl = base_url('aset-berwujud/detail/');
                    $qrUrl = base64_encode($qrCode['content']);
                    $combine = $siteUrl . $qrUrl;
                    $generatedImage = $this->generateQRCodeImage($combine, $qrCode['id']);

                    $this->qrCodeModel->update($qrCode['id'], ['image' => $generatedImage]);
                }

                $this->db->transComplete();

                if ($this->db->transStatus() === false) {
                    throw new \Exception('Gagal menyimpan data aset');
                }

                return $this->jsonResponse('success', 'Aset berhasil diperbarui', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Aset berhasil diperbarui',
                ], 200);
            } catch (\Throwable $th) {
                $this->db->transRollback();

                return $this->jsonResponse('error', 'Gagal memperbarui aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal memperbarui aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->assetFixedModel->delete($id);

                return $this->jsonResponse('success', 'Aset berhasil dihapus', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Aset berhasil dihapus',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal menghapus aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal menghapus aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function generateQr()
    {
        if ($this->request->isAJAX()) {
            try {
                // get request
                $request = $this->request->getJSON(true);

                // validasi request
                if (!isset($request['id']) || !is_array($request['id']) || empty($request['id'])) {
                    return $this->jsonResponse('error', 'ID tidak valid atau kosong', [], [], [
                        'type' => 'error',
                        'title' => 'Gagal',
                        'message' => 'Data ID tidak valid atau kosong',
                    ], 400);
                }

                // ambil id dari request
                $ids = $request['id'];

                // sanitize id
                $ids = array_filter($ids, function ($id) {
                    return is_numeric($id) && $id > 0;
                });

                if (empty($ids)) {
                    return $this->jsonResponse('error', 'Tidak ada ID yang valid', [], [], [
                        'type' => 'error',
                        'title' => 'Gagal',
                        'message' => 'Tidak ada ID yang valid untuk diproses',
                    ], 400);
                }

                $data = $this->db->table('qr_codes')->select('id, content, image')
                    ->whereIn('id', $ids)->get()->getResultArray();

                if (empty($data)) {
                    return $this->jsonResponse('error', 'Data tidak ditemukan', [], [], [
                        'type' => 'error',
                        'title' => 'Gagal',
                        'message' => 'Data QR code tidak ditemukan',
                    ], 404);
                }

                // set up variable
                $alreadyGenerated = [];
                $toGenerate = [];
                $successCount = 0;
                $errorCount = 0;

                // pisahkan data yang sudah ter-generate dan yang belum
                foreach ($data as $item) {
                    if (!empty($item['image'])) {
                        $alreadyGenerated[] = $item['id'];
                    } else {
                        $toGenerate[] = $item;
                    }
                }

                // proses generate QR hanya untuk yang belum ter-generate
                foreach ($toGenerate as $item) {
                    try {
                        // validate empty content
                        if (empty($item['content'])) {
                            $errorCount++;
                            continue;
                        }

                        $siteUrl = base_url('aset-berwujud/detail/');
                        $qrUrl = base64_encode($item['content']);
                        $combine = $siteUrl . $qrUrl;

                        // generate qr process
                        $generatedImage = $this->generateQRCodeImage($combine, $item['id']);

                        // cek status generate
                        if ($generatedImage) {
                            $this->qrCodeModel->update($item['id'], ['image' => $generatedImage]);
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    } catch (\Throwable $th) {
                        $errorCount++;
                        log_message('error', 'Error generating QR for ID ' . $item['id'] . ': ' . $th->getMessage());
                    }
                }

                $updatedData = $this->db->table('qr_codes')->select('id, content, image')->whereIn('id', $ids)->get()->getResultArray();

                // buat response
                if ($errorCount > 0 && $successCount === 0) {
                    // semua error
                    $status = 'error';
                    $type = 'error';
                    $title = 'Gagal';
                    $message = "Gagal generate {$errorCount} QR code.";
                    $httpCode = 500;
                } elseif ($successCount > 0 && $errorCount === 0 && empty($alreadyGenerated)) {
                    // Semua berhasil, tidak ada yang sudah ter-generate
                    $status = 'success';
                    $type = 'success';
                    $title = 'Berhasil';
                    $message = "Berhasil generate {$successCount} QR code.";
                    $httpCode = 200;
                } elseif ($successCount > 0 && ($errorCount > 0 || !empty($alreadyGenerated))) {
                    // Sebagian berhasil, sebagian gagal/sudah ada
                    $status = 'partial';
                    $type = 'warning';
                    $title = 'Sebagian Berhasil';
                    $message = '';

                    if ($successCount > 0) {
                        $message .= "Berhasil generate {$successCount} QR code. ";
                    }
                    if (!empty($alreadyGenerated)) {
                        $message .= count($alreadyGenerated) . " QR code sudah ter-generate sebelumnya. ";
                    }
                    if ($errorCount > 0) {
                        $message .= "{$errorCount} QR code gagal di-generate.";
                    }
                    $httpCode = 200;
                } elseif ($successCount === 0 && !empty($alreadyGenerated) && $errorCount === 0) {
                    // Semua sudah ter-generate sebelumnya
                    $status = 'info';
                    $type = 'info';
                    $title = 'Informasi';
                    $message = count($alreadyGenerated) . " QR code sudah ter-generate sebelumnya.";
                    $httpCode = 200;
                } else {
                    // Fallback untuk kasus yang tidak terduga
                    $status = 'error';
                    $type = 'error';
                    $title = 'Gagal';
                    $message = 'Terjadi kesalahan dalam proses generate QR code.';
                    $httpCode = 500;
                }

                return $this->jsonResponse($status, trim($message), $updatedData, [], [
                    'type' => $type,
                    'title' => $title,
                    'message' => trim($message),
                    'summary' => [
                        'success' => $successCount,
                        'already_generated' => count($alreadyGenerated),
                        'failed' => $errorCount,
                        'total' => count($ids)
                    ]
                ], $httpCode);
            } catch (\Throwable $th) {
                log_message('error', 'Error in generateQr: ' . $th->getMessage());
                return $this->jsonResponse('error', 'Terjadi kesalahan', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Terjadi kesalahan sistem',
                ], 500);
            }
        } else {
            return $this->jsonResponse('error', 'Request harus AJAX', [], [], [
                'type' => 'error',
                'title' => 'Gagal',
                'message' => 'Request harus AJAX',
            ], 400);
        }
    }

    private function generateQRCodeImage($content, $qrCodeId)
    {
        try {
            $uploadPath = ROOTPATH . 'public/uploads/qr_codes/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $filename = 'qr_' . $qrCodeId . '_' . time() . '.png';
            $filePath = $uploadPath . $filename;

            // Generate QR code using endroid/qr-code Builder
            $builder = new \Endroid\QrCode\Builder\Builder(
                writer: new \Endroid\QrCode\Writer\PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $content,
                encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                roundBlockSizeMode: \Endroid\QrCode\RoundBlockSizeMode::Margin,
            );

            $result = $builder->build();

            file_put_contents($filePath, $result->getString());

            // Return relative path for database storage
            return $filename;
        } catch (\Exception $e) {
            // Log error and return null if QR generation fails
            log_message('error', 'Failed to generate QR code: ' . $e->getMessage());
            return null;
        }
    }

    public function printAssetFixed()
    {
        $ids = $this->request->getGet('ids');
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada QR code dipilih.');
        }

        $assetFixedModel = new AssetFixedModel();
        $qrCodes = $assetFixedModel
            ->select('qr_codes.content, qr_codes.image')
            ->join('qr_codes', 'qr_codes.id = asset_fixed.qr_code_id')
            ->whereIn('asset_fixed.id', $ids)
            ->findAll();

        return view('asset-fixed/print', ['qrCodes' => $qrCodes]);
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header template
            $sheet->setCellValue('A1', 'ID Pengelola Aset');
            $sheet->setCellValue('B1', 'ID Barang');
            $sheet->setCellValue('C1', 'Jumlah Aset');
            $sheet->setCellValue('D1', 'Satuan');
            $sheet->setCellValue('E1', 'Kondisi');
            $sheet->setCellValue('F1', 'ID Lokasi Aset');
            $sheet->setCellValue('G1', 'Penanggung Jawab Aset');
            $sheet->setCellValue('H1', 'Umur Ekonomis (tahun)');
            $sheet->setCellValue('I1', 'Nilai Aset (Rp)');

            // Styling header
            $sheet->getStyle('A1:I1')->getFont()->setBold(true);
            $sheet->getStyle('A1:I1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');

            $this->createHelperTable($sheet);

            // Contoh data
            $sheet->setCellValue('A2', '1');
            $sheet->setCellValue('B2', '1');
            $sheet->setCellValue('C2', '1');
            $sheet->setCellValue('D2', 'Unit');
            $sheet->setCellValue('E2', 'Baru');
            $sheet->setCellValue('F2', '1');
            $sheet->setCellValue('G2', 'Fulana');
            $sheet->setCellValue('H2', '5');
            $sheet->setCellValue('I2', '15000000');

            $sheet->setCellValue('A3', '2');
            $sheet->setCellValue('B3', '2');
            $sheet->setCellValue('C3', '2');
            $sheet->setCellValue('D3', 'Pcs');
            $sheet->setCellValue('E3', 'Bekas');
            $sheet->setCellValue('F3', '2');
            $sheet->setCellValue('G3', 'Fulani Abi');
            $sheet->setCellValue('H3', '2');
            $sheet->setCellValue('I3', '2000000');

            $sheet->setCellValue('A4', '3');
            $sheet->setCellValue('B4', '3');
            $sheet->setCellValue('C4', '3');
            $sheet->setCellValue('D4', 'Unit');
            $sheet->setCellValue('E4', 'Lama');
            $sheet->setCellValue('F4', '3');
            $sheet->setCellValue('G4', 'Fulanu Abu');
            $sheet->setCellValue('H4', '1');
            $sheet->setCellValue('I4', '1500000');

            // Auto resize columns
            foreach (range('A', 'I') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'template_import_aset_berwujud.xlsx';

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

                $this->db->transStart();

                // Loop mulai dari baris 2 (skip header)
                for ($row = 2; $row <= $highestRow; $row++) {
                    $manager_id = $worksheet->getCell('A' . $row)->getValue();
                    $item_id = $worksheet->getCell('B' . $row)->getValue();
                    $quantity = $worksheet->getCell('C' . $row)->getValue();
                    $unit = $worksheet->getCell('D' . $row)->getValue();
                    $condition = $worksheet->getCell('E' . $row)->getValue();
                    $location_id = $worksheet->getCell('F' . $row)->getValue();
                    $responsible = $worksheet->getCell('G' . $row)->getValue();
                    $economic_life = $worksheet->getCell('H' . $row)->getValue();
                    $acquisition_cost = $worksheet->getCell('I' . $row)->getValue();

                    // Validasi data
                    if (empty($manager_id)) {
                        $errors[] = "Baris $row: ID Pengelola aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($item_id)) {
                        $errors[] = "Baris $row: ID Barang tidak boleh kosong";
                        continue;
                    }
                    if (empty($quantity)) {
                        $errors[] = "Baris $row: Jumlah aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($unit)) {
                        $errors[] = "Baris $row: Satuan aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($condition)) {
                        $errors[] = "Baris $row: Kondisi aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($location_id)) {
                        $errors[] = "Baris $row: Lokasi aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($responsible)) {
                        $errors[] = "Baris $row: Penanggung jawab aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($economic_life)) {
                        $errors[] = "Baris $row: Umur ekonomis aset tidak boleh kosong";
                        continue;
                    }
                    if (empty($acquisition_cost)) {
                        $errors[] = "Baris $row: Nilai aset tidak boleh kosong";
                        continue;
                    }

                    $quantity = (int) $quantity;

                    for ($i = 1; $i <= $quantity; $i++) {
                        try {
                            $assetCode = $this->assetFixedModel->generateAssetCode(
                                $item_id,
                                $manager_id,
                                $location_id
                            );

                            $qrCodeData = [
                                'content'   => $assetCode,
                                'image'     => null,
                            ];

                            $qrCodeId = $this->qrCodeModel->insert($qrCodeData);
                            if (!$qrCodeId) {
                                throw new \Exception("Baris $row: Gagal membuat QR code");
                            }

                            $rowData = [
                                'asset_manager_id' => $manager_id,
                                'item_id' => $item_id,
                                'qr_code_id' => $qrCodeId,
                                'unit' => $unit,
                                'condition' => $condition,
                                'asset_location_id' => $location_id,
                                'responsible_person' => $responsible,
                                'economic_life' => $economic_life,
                                'acquisition_cost' => $acquisition_cost,
                                'created_at' => date('Y-m-d H:i:s'),
                            ];

                            if ($this->assetFixedModel->insert($rowData)) {
                                $successCount++;
                            } else {
                                $errors[] = "Baris $row: Gagal menyimpan data aset ke-$i";
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Baris $row: " . $e->getMessage();
                        }
                    }
                }

                // PERBAIKAN: Complete transaction di sini
                $this->db->transComplete();

                if ($this->db->transStatus() === false) {
                    throw new \Exception('Gagal menyimpan data aset - Transaction failed');
                }

                // Hapus file sementara
                unlink($filePath);

                // Pesan hasil import
                $message = "Import selesai. $successCount data berhasil diimport.";
                if (!empty($errors)) {
                    $message .= " " . count($errors) . " data gagal.";
                }

                return redirect()->to('/asset-fixed')->with('message', $message)->with('errors', $errors);
            } catch (\Throwable $th) {
                // PERBAIKAN: Rollback jika ada error
                $this->db->transRollback();

                // Hapus file jika ada error
                if (isset($filePath) && file_exists($filePath)) {
                    unlink($filePath);
                }

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
            $assetFixed = $this->db->table('asset_fixed as af')
                ->select('af.unit, af.condition, af.responsible_person, af.economic_life, af.acquisition_cost, ac.name as categories_name, am.name as managers_name, al.name as locations_name, i.name as items_name, i.brand as items_brand, i.model as items_model, i.serial_number as items_serial_number, i.vendor as items_vendor, i.acquisition_date as item_acquisition_date, i.description as items_description, qr.content as qr_codes, qr.image as qr_image')
                ->join('items as i', 'af.item_id = i.id', 'left')
                ->join('qr_codes as qr', 'af.qr_code_id = qr.id', 'left')
                ->join('asset_managers as am', 'af.asset_manager_id = am.id', 'left')
                ->join('asset_locations as al', 'af.asset_location_id = al.id', 'left')
                ->join('asset_categories as ac', 'i.asset_categories_id = ac.id', 'left')
                ->where('af.deleted_at', null)
                ->get()
                ->getResultArray();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'Kode Aset');
            $sheet->setCellValue('B1', 'Nama Aset');
            $sheet->setCellValue('C1', 'Kategori Aset');
            $sheet->setCellValue('D1', 'Pengelola Aset');
            $sheet->setCellValue('E1', 'Merek');
            $sheet->setCellValue('F1', 'Model');
            $sheet->setCellValue('G1', 'Nomor Seri');
            $sheet->setCellValue('H1', 'Vendor');
            $sheet->setCellValue('I1', 'Tanggal Perolehan');
            $sheet->setCellValue('J1', 'Lokasi Aset');
            $sheet->setCellValue('K1', 'Penanggung Jawab');
            $sheet->setCellValue('L1', 'Satuan');
            $sheet->setCellValue('M1', 'Kondisi');
            $sheet->setCellValue('N1', 'Umur Ekonomis Aset');
            $sheet->setCellValue('O1', 'Nilai Aset');
            $sheet->setCellValue('P1', 'QR Code Generate?');
            $sheet->setCellValue('Q1', 'Spesifikasi');

            // Styling header
            $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
            $sheet->getStyle('A1:Q1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');

            // Data
            $row = 2;
            foreach ($assetFixed as $af) {
                $sheet->setCellValue('A' . $row, $af['qr_codes']);
                $sheet->setCellValue('B' . $row, $af['items_name']);
                $sheet->setCellValue('C' . $row, $af['categories_name']);
                $sheet->setCellValue('D' . $row, $af['managers_name']);
                $sheet->setCellValue('E' . $row, $af['items_brand']);
                $sheet->setCellValue('F' . $row, $af['items_model']);
                $sheet->setCellValue('G' . $row, $af['items_serial_number']);
                $sheet->setCellValue('H' . $row, $af['items_vendor']);
                $sheet->setCellValue('I' . $row, $af['item_acquisition_date']);
                $sheet->setCellValue('J' . $row, $af['locations_name']);
                $sheet->setCellValue('K' . $row, $af['responsible_person']);
                $sheet->setCellValue('L' . $row, $af['unit']);
                $sheet->setCellValue('M' . $row, $af['condition']);
                $sheet->setCellValue('N' . $row, $af['economic_life']);
                $sheet->setCellValue('O' . $row, $af['acquisition_cost']);
                $sheet->setCellValue('P' . $row, $af['qr_image'] ? 'YA' : 'TIDAK');
                $sheet->setCellValue('Q' . $row, strip_tags($af['items_description']));
                $row++;
            }

            // Auto resize columns
            foreach (range('A', 'Q') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Generate file
            $writer = new Xlsx($spreadsheet);
            $filename = 'export_aset_berwujud_' . date('Y-m-d_H-i-s') . '.xlsx';

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
        $sheet->setCellValue('M3', 'Lokasi Aset');
        $sheet->setCellValue('N3', 'Satuan');
        $sheet->setCellValue('O3', 'Kondisi');

        $sheet->getStyle('L3:O3')->getFont()->setBold(true);
        $sheet->getStyle('L3:O3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF87CEEB');

        // Auto resize columns
        foreach (range('L', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $row = 4;
        $assetManagers = $this->assetManagerModel->where('deleted_at', null)->findAll();
        foreach ($assetManagers as $assetManager) {
            $sheet->setCellValue("L{$row}", $assetManager['id'] . ' - ' . $assetManager['name']);
            $row++;
        }

        $row = 4;
        $assetLocations = $this->assetLocationModel->where('deleted_at', null)->findAll();
        foreach ($assetLocations as $assetLocation) {
            $sheet->setCellValue("M{$row}", $assetLocation['id'] . ' - ' . $assetLocation['name']);
            $row++;
        }

        $sheet->setCellValue('N4', 'Box');
        $sheet->setCellValue('N5', 'Pcs');
        $sheet->setCellValue('N6', 'Roll');
        $sheet->setCellValue('N7', 'Set');
        $sheet->setCellValue('N8', 'Unit');

        $sheet->setCellValue('O4', 'Baru');
        $sheet->setCellValue('O5', 'Bekas');
        $sheet->setCellValue('O6', 'Hilang');
        $sheet->setCellValue('O7', 'Lama');
        $sheet->setCellValue('O8', 'Rusak');
    }
}
