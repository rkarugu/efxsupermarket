<?php

namespace App\Interfaces\Finance;

interface BankReconciliationInterface
{
    public function getPaymentVerifications();
    public function getSinglePaymentVerification($id);
    public function getSinglePaymentVerificationBank($id);
    public function getSinglePaymentVerificationSystem($id);
    public function savePaymentVerification($data);
    public function saveDebtorTrans($data);
    public function saveExtractedBankTrans($data);

    public function getPaymentApprovals();

    public function processReconciliation($data);
    public function checkUploadHeaders($data);

    public function verifyPaymentReconciliations($id,$data);
    public function approvePaymentReconciliations($data);

    public function updateTransactionAndVerify($data);
    
    public function glTransApprovedReconciliation($data, $bank_account);

    public function updateDebtorsTable();
    
}
