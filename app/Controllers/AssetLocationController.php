<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;
use Config\Database;
use Config\Services;
use App\Models\AssetLocationModel;

class AssetLocationController extends BaseController
{
    protected $db;
    protected $assetLocationModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetLocationModel = new AssetLocationModel();
        $this->validator = Services::validation();
    }

    public function getAssetLocationDt()
    {
        $builder = $this->db->table('asset_locations')
            ->select('id, code, name, description, created_at')
            ->where('deleted_at', null)
            ->orderBy('asset_locations.created_at', 'desc');

        return DataTable::of($builder)->addNumbering()->toJson(true);
    }

    public function getAssetLocationCounter()
    {
        return $this->response->setJSON($this->assetLocationModel->assetLocationCounter());
    }

    public function index()
    {
        $assetLocationCounter = $this->assetLocationModel->assetLocationCounter();
        $webProperties = [
            'titleHeader' => 'Lokasi Aset',
            'titlePage' => 'Lokasi Aset',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Lokasi Aset']
            ]
        ];

        return view('/asset-location/index', ['webProperties' => $webProperties, 'assetLocationCounter' => $assetLocationCounter]);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);

            $this->validator->setRules([
                'code'          => 'required|string|min_length[3]|max_length[255]',
                'name'          => 'required|string|min_length[3]|max_length[255]',
                'description'   => 'required|string|min_length[3]',
            ]);

            if (!$this->validator->run($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->validator->getErrors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->assetLocationModel->insert($data);

                return $this->jsonResponse('success', 'Lokasi aset berhasil dibuat', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Lokasi aset berhasil dibuat',
                ], 201);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Gagal membuat lokasi aset', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal membuat lokasi aset, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function show($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->assetLocationModel->find($id);

            if (!$data) {
                return $this->jsonResponse('error', 'Lokasi aset tidak ditemukan', [], [], [
                    'type' => 'error',
                    'title' => 'Lokasi aset tidak ditemukan',
                    'message' => 'Lokasi aset tidak ditemukan',
                ], 404);
            }

            return $this->jsonResponse('success', 'Lokasi aset ditemukan', $data, [], [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Lokasi aset ditemukan',
            ], 200);
        }
    }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);

            $this->validator->setRules([
                'code'          => 'required|string|min_length[3]|max_length[255]',
                'name'          => 'required|string|min_length[3]|max_length[255]',
                'description'   => 'required|string|min_length[3]',
            ]);

            if (!$this->validator->run($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->validator->getErrors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->assetLocationModel->update($id, $data);

                return $this->jsonResponse('success', 'Lokasi aset berhasil diubah', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Lokasi aset berhasil diubah',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal mengubah lokasi aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal mengubah lokasi aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->assetLocationModel->delete($id);

                return $this->jsonResponse('success', 'Lokasi aset berhasil dihapus', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Lokasi aset berhasil dihapus',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal menghapus lokasi aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal menghapus lokasi aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }
}
