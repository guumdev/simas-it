<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $webProperties = [
            'titleHeader' => 'Dashboard',
            'titlePage' => 'Dashboard',
            'breadcrumbs' => [
                ['label' => 'Dashboard']
            ]
        ];

        return view('/dashboard/index', ['webProperties' => $webProperties]);
    }
}
