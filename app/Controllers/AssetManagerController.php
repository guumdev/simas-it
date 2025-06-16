<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;
use Config\Database;
use Config\Services;
use App\Models\AssetManagerModel;

class AssetManagerController extends BaseController
{
    protected $db;
    protected $assetManagerModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetManagerModel = new AssetManagerModel();
        $this->validator = Services::validation();
    }

    public function getAssetManagerDt()
    {
        $builder = $this->db->table('asset_managers')
            ->select('id, code, name, description, created_at')
            ->where('deleted_at', null)
            ->orderBy('asset_managers.created_at', 'desc');

        return DataTable::of($builder)->addNumbering()->toJson(true);
    }

    public function getAssetManagerCounter()
    {
        return $this->response->setJSON($this->assetManagerModel->assetManagerCounter());
    }

    public function index()
    {
        $assetManagerCounter = $this->assetManagerModel->assetManagerCounter();
        $webProperties = [
            'titleHeader' => 'Pengelola Aset',
            'titlePage' => 'Pengelola Aset',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Pengelola Aset']
            ]
        ];

        return view('/asset-manager/index', ['webProperties' => $webProperties, 'assetManagerCounter' => $assetManagerCounter]);
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
                $this->assetManagerModel->insert($data);

                return $this->jsonResponse('success', 'Pengelola aset berhasil dibuat', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Pengelola aset berhasil dibuat',
                ], 201);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Gagal membuat pengelola aset', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal membuat pengelola aset, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function show($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->assetManagerModel->find($id);

            if (!$data) {
                return $this->jsonResponse('error', 'Pengelola aset tidak ditemukan', [], [], [
                    'type' => 'error',
                    'title' => 'Pengelola aset tidak ditemukan',
                    'message' => 'Pengelola aset tidak ditemukan',
                ], 404);
            }

            return $this->jsonResponse('success', 'Pengelola aset ditemukan', $data, [], [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Pengelola aset ditemukan',
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
                $this->assetManagerModel->update($id, $data);

                return $this->jsonResponse('success', 'Pengelola aset berhasil diubah', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Pengelola aset berhasil diubah',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal mengubah pengelola aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal mengubah pengelola aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->assetManagerModel->delete($id);

                return $this->jsonResponse('success', 'Pengelola aset berhasil dihapus', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Pengelola aset berhasil dihapus',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal menghapus pengelola aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal menghapus pengelola aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }
}
