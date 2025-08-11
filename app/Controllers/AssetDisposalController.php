<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use App\Models\AssetDisposalModel;
use App\Models\AssetFixedModel;

class AssetDisposalController extends BaseController
{
    protected $db;
    protected $assetDisposalModel;
    protected $assetFixedModel;
    protected $validator;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->assetDisposalModel = new AssetDisposalModel();
        $this->assetFixedModel = new AssetFixedModel();
        $this->validator = Services::validation();
    }

    public function index()
    {
        //
    }
}
