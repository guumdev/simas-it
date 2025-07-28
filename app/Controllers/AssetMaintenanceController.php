<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;
use Config\Database;
use Config\Services;
use App\Models\AssetMaintenanceModel;
use App\Models\AssetFixedModel;
use App\Models\ItemModel;
use App\Models\QrCodeModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetMaintenanceController extends BaseController
{
    protected $db;
    protected $assetMaintenanceModel;
    protected $assetFixedModel;
    protected $itemModel;
    protected $qrCodeModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetMaintenanceModel = new AssetMaintenanceModel();
        $this->assetFixedModel = new AssetFixedModel();
        $this->itemModel = new ItemModel();
        $this->qrCodeModel = new QrCodeModel();
        $this->validator = Services::validation();
    }

    public function getAssetMaintenanceDt()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Only AJAX requests are allowed.']);
        }

        $builder = $this->db->table('asset_maintenance as amt')
            ->select('amt.id, i.name as items_name, qr.content as qr_codes, amt.maintenance_location, amt.performed_by, amt.cost, amt.maintenance_date')
            ->join('asset_fixed as af', 'amt.asset_fixed_id = af.id', 'left')
            ->join('items as i', 'af.item_id = i.id', 'left')
            ->join('qr_codes as qr', 'af.qr_code_id = qr.id', 'left')
            ->where('amt.deleted_at', null)
            ->orderBy('amt.created_at', 'desc');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                if ($request->date_start_filter) {
                    $builder->where('amt.maintenance_date >=', $request->date_start_filter);
                }
                if ($request->date_end_filter) {
                    $builder->where('amt.maintenance_date <=', $request->date_end_filter);
                }
            })
            ->addNumbering()
            ->toJson(true);
    }

    public function index()
    {
        $webProperties = [
            'titleHeader'   => 'Pemeliharaan Aset',
            'titlePage'     => 'Pemeliharaan Aset',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Pemeliharaan Aset']
            ],
        ];

        return view('asset-maintenance/index', ['webProperties' => $webProperties]);
    }

    public function create()
    {
        $webProperties = [
            'titleHeader'   => 'Buat Pemeliharaan Aset',
            'titlePage'     => 'Buat Pemeliharaan Aset',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Pemeliharaan Aset', 'url' => base_url('/asset-maintenances')],
                ['label' => 'Buat']
            ],
        ];

        return view('asset-maintenance/create', ['webProperties' => $webProperties]);
    }

    public function store()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getPost() ?: $this->request->getJSON(true);

            $data = [
                'asset_fixed_id'       => $request['asset_fixed_id'] ?? null,
                'maintenance_type'     => $request['maintenance_type'] ?? null,
                'maintenance_date'     => $request['maintenance_date'] ?? null,
                'next_maintenance'     => $request['next_maintenance'] ?? null,
                'description'          => $request['description'] ?? null,
                'maintenance_action'   => $request['maintenance_action'] ?? null,
                'maintenance_location' => $request['maintenance_location'] ?? null,
                'device_status'        => $request['device_status'] ?? null,
                'performed_by'         => $request['performed_by'] ?? null,
                'cost' => $request['cost'] !== '' ? $request['cost'] : null,
                'duration' => $request['duration'] !== '' ? $request['duration'] : null,
                'notes'                => $request['notes'] ?? null,
            ];
            if (!$this->assetMaintenanceModel->validate($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->assetMaintenanceModel->errors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->assetMaintenanceModel->insert($data);

                return $this->jsonResponse('success', 'Pemeliharaan aset berhasil dibuat', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Pemeliharaan aset berhasil dibuat',
                ], 201);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Gagal membuat pemeliharaan aset', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal membuat pemeliharaan aset, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function show($id)
    {
        $webProperties = [
            'titleHeader' => 'Detil Pemeliharaan Aset',
            'titlePage' => 'Detil Pemeliharaan Aset',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Daftar Pemeliharaan Aset', 'url' => base_url('/asset-maintenances')],
                ['label' => 'Detil']
            ]
        ];
        $maintenance = $this->assetMaintenanceModel->find($id);
        $assetFixed = $this->assetFixedModel->find($maintenance['asset_fixed_id']);
        $item = $this->itemModel->find($assetFixed['item_id']);
        $qrCode = $this->qrCodeModel->find($assetFixed['qr_code_id']);

        return view('asset-maintenance/show', [
            'webProperties' => $webProperties,
            'maintenance' => $maintenance,
            'assetFixed' => $assetFixed,
            'item' => $item,
            'qrCode' => $qrCode
        ]);
    }

    public function edit($id)
    {
        $webProperties = [
            'titleHeader' => 'Ubah Pemeliharaan Aset',
            'titlePage' => 'Ubah Pemeliharaan Aset',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Daftar Pemeliharaan Aset', 'url' => base_url('/asset-maintenances')],
                ['label' => 'Ubah']
            ]
        ];
        $maintenance = $this->assetMaintenanceModel->find($id);
        $assetFixed = $this->assetFixedModel->find($maintenance['asset_fixed_id']);
        $item = $this->itemModel->find($assetFixed['item_id']);
        $qrCode = $this->qrCodeModel->find($assetFixed['qr_code_id']);

        return view('asset-maintenance/edit', [
            'webProperties' => $webProperties,
            'maintenance' => $maintenance,
            'assetFixed' => $assetFixed,
            'item' => $item,
            'qrCode' => $qrCode
        ]);
    }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getJSON(true);

            $data = [
                'maintenance_type'     => $request['maintenance_type'] ?? null,
                'maintenance_date'     => $request['maintenance_date'] ?? null,
                'next_maintenance'     => $request['next_maintenance'] ?? null,
                'description'          => $request['description'] ?? null,
                'maintenance_action'   => $request['maintenance_action'] ?? null,
                'maintenance_location' => $request['maintenance_location'] ?? null,
                'device_status'        => $request['device_status'] ?? null,
                'performed_by'         => $request['performed_by'] ?? null,
                'cost' => $request['cost'] !== '' ? $request['cost'] : null,
                'duration' => $request['duration'] !== '' ? $request['duration'] : null,
                'notes'                => $request['notes'] ?? null,
            ];

            if (!$this->assetMaintenanceModel->validate($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->assetMaintenanceModel->errors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->assetMaintenanceModel->update($id, $data);

                return $this->jsonResponse('success', 'Pemeliharaan aset berhasil diubah', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Pemeliharaan aset berhasil diubah',
                ], 200);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Gagal mengubah pemeliharaan aset', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal mengubah pemeliharaan aset, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->assetMaintenanceModel->delete($id);

                return $this->jsonResponse('success', 'Pemeliharaan aset berhasil dihapus', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Pemeliharaan aset berhasil dihapus',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal menghapus pemeliharaan aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal menghapus pemeliharaan aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function exportExcel()
    {
        try {
            //code...
            $datas = $this->db->table('asset_maintenance as amt')
                ->select('amt.*, qr.content as qr_content, itm.name as item_name')
                ->join('asset_fixed as af', 'amt.asset_fixed_id = af.id', 'left')
                ->join('qr_codes as qr', 'af.qr_code_id = qr.id', 'left')
                ->join('items as itm', 'af.item_id = itm.id', 'left')
                ->where('amt.deleted_at', null)
                ->get()->getResultArray();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'Kode Aset');
            $sheet->setCellValue('B1', 'Nama Aset');
            $sheet->setCellValue('C1', 'Jenis Pemeliharaan');
            $sheet->setCellValue('D1', 'Tanggal Pemeliharaan');
            $sheet->setCellValue('E1', 'Pemeliharaan selanjutnya');
            $sheet->setCellValue('F1', 'Oleh');
            $sheet->setCellValue('G1', 'Lokasi Pemeliharaan');
            $sheet->setCellValue('H1', 'Biaya');
            $sheet->setCellValue('I1', 'Durasi (Hari)');
            $sheet->setCellValue('J1', 'Deskripsi Pemeliharaan');
            $sheet->setCellValue('K1', 'Tindakan Pemeliharaan');
            $sheet->setCellValue('L1', 'Status Perangkat');
            $sheet->setCellValue('M1', 'Catatan');

            // Styling
            $sheet->getStyle('A1:M1')->getFont()->setBold(true);
            $sheet->getStyle('A1:M1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');

            // Data
            $row = 2;
            foreach ($datas as $data) {
                $sheet->setCellValue('A' . $row, $data['qr_content']);
                $sheet->setCellValue('B' . $row, $data['item_name']);
                $sheet->setCellValue('C' . $row, ucfirst($data['maintenance_type']));
                $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($data['maintenance_date'])));
                $sheet->setCellValue('E' . $row, $data['next_maintenance'] ? date('d/m/Y', strtotime($data['next_maintenance'])) : '-');
                $sheet->setCellValue('F' . $row, ucwords($data['performed_by']));
                $sheet->setCellValue('G' . $row, ucwords($data['maintenance_location']));
                $sheet->setCellValue('H' . $row, number_format($data['cost'], 2, ',', '.'));
                $sheet->setCellValue('I' . $row, $data['duration']);
                $sheet->setCellValue('J' . $row, strip_tags($data['description']));
                $sheet->setCellValue('K' . $row, strip_tags($data['maintenance_action']));
                $sheet->setCellValue('L' . $row, str_replace('_', ' ', ucfirst($data['device_status'])));
                $sheet->setCellValue('M' . $row, strip_tags($data['notes']));
                $row++;
            }

            // Auto resize columns
            foreach (range('A', 'M') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Generate file
            $writer = new Xlsx($spreadsheet);
            $filename = 'export_pemeliharaan_aset_' . date('Y-m-d_H-i-s') . '.xlsx';

            $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $this->response->setBody($writer->save('php://output'));

            return $this->response;
        } catch (\Exception $e) {
            //throw $e;
            return redirect()->back()->with('error', 'Error export: ' . $e->getMessage());
        }
    }
}
