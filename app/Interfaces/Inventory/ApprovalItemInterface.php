<?php

namespace App\Interfaces\Inventory;

interface ApprovalItemInterface
{
    public function getAllApprovalItem();
    public function getAllApprovalItemDatatable($data);
    public function getAllApprovalItemCount();
    public function storeApprovalItem($data);
    public function destroyApprovalItem($id);
    public function getItemHistory();
}
