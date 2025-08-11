<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AssetManagerModel;
use App\Models\AssetLocationModel;
use Config\Database;

class DataSelectController extends BaseController
{
    protected $db;
    protected $assetManagerModel;
    protected $assetLocationModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetManagerModel = new AssetManagerModel();
        $this->assetLocationModel = new AssetLocationModel();
    }

    public function getAssetManagers()
    {
        $managers = $this->assetManagerModel->select('id, name, code')
            ->where('deleted_at', null)
            ->findAll();

        return $this->response->setJSON($managers);
    }

    public function getAssetLocations()
    {
        $locations = $this->assetLocationModel->select('id, name, code')
            ->where('deleted_at', null)
            ->findAll();

        return $this->response->setJSON($locations);
    }

    public function getAssetFixedByCode()
    {
        $search = $this->request->getGet('q');
        $page = $this->request->getGet('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $builder = $this->db->table('asset_fixeds as af')
            ->select('af.id, af.asset_location_id as items_location_id, item.name as items_name, qr.content as qr_codes, al.name as items_location')
            ->join('qr_codes as qr', 'af.qr_code_id = qr.id', 'left')
            ->join('items as item', 'af.item_id = item.id', 'left')
            ->join('asset_locations as al', 'af.asset_location_id = al.id', 'left')
            ->where('af.deleted_at', null);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('item.name', $search)
                ->orLike('qr.content', $search)
                ->groupEnd();
        }

        // Hitung total data untuk pagination
        $total = $builder->countAllResults(false);

        // Ambil data dengan limit dan offset
        $assets = $builder->limit($limit, $offset)->get()->getResult();

        $response = [
            'results' => $assets,
            'pagination' => [
                'more' => ($offset + $limit) < $total
            ]
        ];

        return $this->response->setJSON($response);
    }
}
