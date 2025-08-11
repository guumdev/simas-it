<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;
use Config\Database;
use Config\Services;
use App\Models\AssetMovementModel;
use App\Models\AssetFixedModel;
use App\Models\AssetLocationModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetMovementController extends BaseController
{
    protected $db;
    protected $assetMovementModel;
    protected $assetFixedModel;
    protected $assetLocationModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetMovementModel = new AssetMovementModel();
        $this->assetFixedModel = new AssetFixedModel();
        $this->assetLocationModel = new AssetLocationModel();
        $this->validator = Services::validation();
    }

    public function getAssetMovementDt()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Only AJAX requests are allowed.']);
        }

        $builder = $this->db->table('asset_movement as amv')
            ->select('amv.id, amv.moved_by, amv.movement_type, amv.movement_date, i.name as items_name, qr.content as qr_codes, al.name as from_location_id, al2.name as to_location_id')
            ->join('asset_fixed as af', 'amv.asset_fixed_id = af.id', 'left')
            ->join('items as i', 'af.item_id = i.id', 'left')
            ->join('qr_codes as qr', 'af.qr_code_id = qr.id', 'left')
            ->join('asset_locations as al', 'amv.from_location_id = al.id', 'left')
            ->join('asset_locations as al2', 'amv.to_location_id = al2.id', 'left')
            ->where('amv.deleted_at', null)
            ->orderBy('amv.created_at', 'desc');

        return DataTable::of($builder)
            ->filter(function ($builder, $request) {
                if ($request->date_start_filter) {
                    $builder->where('amv.movement_date >=', $request->date_start_filter);
                }
                if ($request->date_end_filter) {
                    $builder->where('amv.movement_date <=', $request->date_end_filter);
                }
            })
            ->addNumbering()
            ->toJson(true);
    }

    public function index()
    {
        $webProperties = [
            'titleHeader'   => 'Perpindahan Aset',
            'titlePage'     => 'Perpindahan Aset',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Perpindahan Aset']
            ],
        ];

        return view('asset-movement/index', ['webProperties' => $webProperties]);
    }

    public function create()
    {
        $webProperties = [
            'titleHeader'   => 'Buat Perpindahan Aset',
            'titlePage'     => 'Buat Perpindahan Aset',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Perpindahan Aset', 'url' => base_url('/asset-movements')],
                ['label' => 'Buat']
            ],
        ];

        return view('asset-movement/create', ['webProperties' => $webProperties]);
    }

    public function store()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getPost() ?: $this->request->getJSON(true);

            $data = [
                'asset_fixed_id'    => $request['asset_fixed_id'] ?? null,
                'movement_date'     => $request['movement_date'] ?? null,
                'movement_type'     => $request['movement_type'] ?? null,
                'from_location_id'  => $request['from_location_id'] ?? null,
                'to_location_id'    => $request['to_location_id'] ?? null,
                'moved_by'          => $request['moved_by'] ?? null,
                'condition_before'  => $request['condition_before'] ?? null,
                'condition_after'   => $request['condition_after'] ?? null,
                'notes'             => $request['notes'] ?? null,
            ];
            if (!$this->assetMovementModel->validate($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->assetMovementModel->errors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->assetMovementModel->insert($data);

                $this->assetFixedModel->update($request['asset_fixed_id'], [
                    'asset_location_id' => $request['to_location_id'],
                ]);

                return $this->jsonResponse('success', 'Perpindahan aset berhasil dibuat', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Perpindahan aset berhasil dibuat',
                ], 201);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Gagal membuat perpindahan aset', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal membuat perpindahan aset, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function show($id)
    {
        $webProperties = [
            'titleHeader'   => 'Detail Perpindahan Aset',
            'titlePage'     => 'Detail Perpindahan Aset',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Perpindahan Aset', 'url' => base_url('/asset-movements')],
                ['label' => 'Detail']
            ],
        ];

        $datas = $this->db->table('asset_movement amv')
            ->select('i.name as item_name, qr.content as item_code, al.name as from_location_name, al2.name as to_location_name, amv.moved_by, amv.movement_type, amv.movement_date, amv.condition_before, amv.condition_after, amv.notes, i.image as item_image, qr.image as item_qr_image')
            ->join('asset_fixed af', 'af.id = amv.asset_fixed_id', 'left')
            ->join('items i', 'i.id = af.item_id', 'left')
            ->join('qr_codes qr', 'qr.id = af.qr_code_id', 'left')
            ->join('asset_locations al', 'al.id = amv.from_location_id', 'left')
            ->join('asset_locations al2', 'al2.id = amv.to_location_id', 'left')
            ->where('amv.deleted_at', null)
            ->where('amv.id', $id)
            ->get()
            ->getRowArray();

        return view('asset-movement/show', ['webProperties' => $webProperties, 'datas' => $datas]);
    }

    public function edit($id)
    {
        $webProperties = [
            'titleHeader'   => 'Ubah Perpindahan Aset',
            'titlePage'     => 'Ubah Perpindahan Aset',
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Perpindahan Aset', 'url' => base_url('/asset-movements')],
                ['label' => 'Ubah']
            ],
        ];
        $datas = $this->db->table('asset_movement amv')
            ->select('i.name as item_name, qr.content as item_code, al.name as from_location_name, al2.name as to_location_name, amv.*')
            ->join('asset_fixed af', 'af.id = amv.asset_fixed_id', 'left')
            ->join('items i', 'i.id = af.item_id', 'left')
            ->join('qr_codes qr', 'qr.id = af.qr_code_id', 'left')
            ->join('asset_locations al', 'al.id = amv.from_location_id', 'left')
            ->join('asset_locations al2', 'al2.id = amv.to_location_id', 'left')
            ->where('amv.deleted_at', null)
            ->where('amv.id', $id)
            ->get()
            ->getRowArray();

        return view('asset-movement/edit', ['webProperties' => $webProperties, 'datas' => $datas]);
    }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getJSON(true);

            $data = [
                'asset_fixed_id'    => $request['asset_fixed_id'] ?? null,
                'movement_date'     => $request['movement_date'] ?? null,
                'movement_type'     => $request['movement_type'] ?? null,
                'from_location_id'  => $request['from_location_id'] ?? null,
                'to_location_id'    => $request['to_location_id'] ?? null,
                'moved_by'          => $request['moved_by'] ?? null,
                'condition_before'  => $request['condition_before'] ?? null,
                'condition_after'   => $request['condition_after'] ?? null,
                'notes'             => $request['notes'] ?? null,
            ];

            if (!$this->assetMovementModel->validate($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->assetMovementModel->errors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->assetMovementModel->update($id, $data);

                return $this->jsonResponse('success', 'Perpindahan aset berhasil diubah', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Perpindahan aset berhasil diubah',
                ], 200);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Gagal mengubah perpindahan aset', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal mengubah perpindahan aset, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->assetMovementModel->delete($id);

                return $this->jsonResponse('success', 'Perpindahan aset berhasil dihapus', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Perpindahan aset berhasil dihapus',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal menghapus perpindahan aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal menghapus perpindahan aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function exportExcel()
    {
        try {
            //code...
            $datas = $this->db->table('asset_movement as amv')
                ->select('amv.*, qr.content as qr_content, itm.name as item_name, al.name as from_location_name, al2.name as to_location_name')
                ->join('asset_fixed as af', 'amv.asset_fixed_id = af.id', 'left')
                ->join('asset_locations as al', 'amv.from_location_id = al.id', 'left')
                ->join('asset_locations as al2', 'amv.to_location_id = al2.id', 'left')
                ->join('qr_codes as qr', 'af.qr_code_id = qr.id', 'left')
                ->join('items as itm', 'af.item_id = itm.id', 'left')
                ->where('amv.deleted_at', null)
                ->get()->getResultArray();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'Kode Aset');
            $sheet->setCellValue('B1', 'Nama Aset');
            $sheet->setCellValue('C1', 'Jenis Perpindahan');
            $sheet->setCellValue('D1', 'Tanggal Perpindahan');
            $sheet->setCellValue('E1', 'Lokasi Asal');
            $sheet->setCellValue('F1', 'Lokasi Tujuan');
            $sheet->setCellValue('G1', 'Oleh');
            $sheet->setCellValue('H1', 'Kondisi Sebelum Perpindahan');
            $sheet->setCellValue('I1', 'Kondisi Sesudah Perpindahan');
            $sheet->setCellValue('J1', 'Catatan');

            // Styling
            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->getStyle('A1:J1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');

            // Data
            $row = 2;
            foreach ($datas as $data) {
                $sheet->setCellValue('A' . $row, $data['qr_content']);
                $sheet->setCellValue('B' . $row, $data['item_name']);
                $sheet->setCellValue('C' . $row, ucfirst($data['movement_type']));
                $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($data['movement_date'])));
                $sheet->setCellValue('E' . $row, ucfirst($data['from_location_name']));
                $sheet->setCellValue('F' . $row, ucfirst($data['to_location_name']));
                $sheet->setCellValue('G' . $row, ucwords($data['moved_by']));
                $sheet->setCellValue('H' . $row, str_replace('_', ' ', ucfirst($data['condition_before'])));
                $sheet->setCellValue('I' . $row, str_replace('_', ' ', ucfirst($data['condition_after'])));
                $sheet->setCellValue('J' . $row, strip_tags($data['notes']));
                $row++;
            }

            // Auto resize columns
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Generate file
            $writer = new Xlsx($spreadsheet);
            $filename = 'export_perpindahan_aset_' . date('Y-m-d_H-i-s') . '.xlsx';

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
