<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetFixedModel extends Model
{
    protected $table            = 'asset_fixeds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['asset_manager_id', 'asset_location_id', 'item_id', 'qr_code_id', 'unit', 'condition', 'responsible_person', 'economic_life', 'acquisition_cost', 'created_at', 'updated_at', 'deleted_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function generateAssetCode($itemId, $assetManagerId, $assetLocationId)
    {
        $manager = $this->db->table('asset_managers')
            ->select('code')
            ->where('id', $assetManagerId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$manager) {
            throw new \Exception('Asset manager not found');
        }

        $location = $this->db->table('asset_locations')
            ->select('code')
            ->where('id', $assetLocationId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$location) {
            throw new \Exception('Asset location not found');
        }

        $item = $this->db->table('items')
            ->select('asset_categories_id, acquisition_date')
            ->where('id', $itemId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$item) {
            throw new \Exception('Item not found');
        }

        $category = $this->db->table('asset_categories')
            ->select('code')
            ->where('id', $item['asset_categories_id'])
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$category) {
            throw new \Exception('Asset category not found');
        }

        // Format acquisition date to MM/YYYY
        $acquisitionDate = date('m-Y', strtotime($item['acquisition_date']));

        // Generate sequential number (5 digits)
        // Count existing assets with same prefix
        $prefix = "AST/{$manager['code']}/{$category['code']}/{$acquisitionDate}/";

        $existingCount = $this->db->table('qr_codes')
            ->where('content LIKE', $prefix . '%')
            ->where('deleted_at', null)
            ->countAllResults();

        $sequentialNumber = str_pad($existingCount + 1, 5, '0', STR_PAD_LEFT);

        // Generate final asset code
        $assetCode = $prefix . $sequentialNumber;

        return $assetCode;
    }
}
