<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AssetManagerModel;
use App\Models\AssetLocationModel;

class DataSelectController extends BaseController
{
    protected $assetManagerModel;
    protected $assetLocationModel;

    public function __construct()
    {
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
}
