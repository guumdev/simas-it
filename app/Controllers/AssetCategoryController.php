<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;
use Config\Database;
use Config\Services;
use App\Models\AssetManagerModel;
use App\Models\AssetCategoryModel;

class AssetCategoryController extends BaseController
{
    protected $db;
    protected $assetManagerModel;
    protected $assetCategoryModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetManagerModel = new AssetManagerModel();
        $this->assetCategoryModel = new AssetCategoryModel();
        $this->validator = Services::validation();
    }

    public function getAssetCategoryDt()
    {
        $builder = $this->db->table('asset_categories')
            ->select('asset_categories.id, asset_categories.code, asset_categories.name, asset_managers.code as managers_code, asset_managers.name as managers_name, asset_categories.description, asset_categories.created_at')
            ->join('asset_managers', 'asset_managers.id = asset_categories.asset_managers_id', 'left')
            ->where('asset_categories.deleted_at', null)
            ->orderBy('asset_categories.created_at', 'desc');

        return DataTable::of($builder)->addNumbering()->toJson(true);
    }

    public function getAssetCategoryCounter()
    {
        return $this->response->setJSON($this->assetCategoryModel->assetCategoryCounter());
    }

    public function index()
    {
        $assetManagerData = $this->assetManagerModel->where('deleted_at', null)->findAll();
        $assetCategoryCounter = $this->assetCategoryModel->assetCategoryCounter();
        $webProperties = [
            'titleHeader' => 'Kategori Aset',
            'titlePage' => 'Kategori Aset',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => base_url('/')],
                ['label' => 'Kategori Aset']
            ]
        ];

        return view('/asset-category/index', ['webProperties' => $webProperties, 'assetCategoryCounter' => $assetCategoryCounter, 'assetManagerData' => $assetManagerData]);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);

            $this->validator->setRules([
                'code'              => 'required|string|min_length[3]|max_length[255]',
                'asset_managers_id' => 'required|integer|is_natural_no_zero',
                'name'              => 'required|string|min_length[3]|max_length[255]',
                'description'       => 'required|string|min_length[3]',
            ]);

            if (!$this->validator->run($data)) {
                return $this->jsonResponse('error', 'Validasi gagal', [], $this->validator->getErrors(), [
                    'type' => 'error',
                    'title' => 'Validasi gagal',
                    'message' => 'Periksa kembali form anda',
                ], 422);
            }

            try {
                $this->assetCategoryModel->insert($data);

                return $this->jsonResponse('success', 'Kategori aset berhasil dibuat', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Kategori aset berhasil dibuat',
                ], 201);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Gagal membuat kategori aset', [], [$e->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal membuat kategori aset, error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function show($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->assetCategoryModel->find($id);

            if (!$data) {
                return $this->jsonResponse('error', 'Kategori aset tidak ditemukan', [], [], [
                    'type' => 'error',
                    'title' => 'Kategori aset tidak ditemukan',
                    'message' => 'Kategori aset tidak ditemukan',
                ], 404);
            }

            return $this->jsonResponse('success', 'Kategori aset ditemukan', $data, [], [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Kategori aset ditemukan',
            ], 200);
        }
    }

    public function update($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost() ?: $this->request->getJSON(true);

            $this->validator->setRules([
                'code'          => 'required|string|min_length[3]|max_length[255]',
                'asset_managers_id' => 'required|integer|is_natural_no_zero',
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
                $this->assetCategoryModel->update($id, $data);

                return $this->jsonResponse('success', 'Kategori aset berhasil diubah', $data, [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Kategori aset berhasil diubah',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal mengubah kategori aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal mengubah kategori aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->assetCategoryModel->delete($id);

                return $this->jsonResponse('success', 'Kategori aset berhasil dihapus', [], [], [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Kategori aset berhasil dihapus',
                ], 200);
            } catch (\Throwable $th) {
                return $this->jsonResponse('error', 'Gagal menghapus kategori aset', [], [$th->getMessage()], [
                    'type' => 'error',
                    'title' => 'Gagal',
                    'message' => 'Gagal menghapus kategori aset, error: ' . $th->getMessage(),
                ], 500);
            }
        }
    }
}
