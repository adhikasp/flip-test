<?php
require_once('FlipClient.php');
require_once('DisbursementDatabase.php');

class DisbursementApplication {

    private $db;
    private $flip;

    function __construct() {
        $FLIP_SECRET = getenv("FLIP_SECRET");

        $this->db = new DisbursementDatabase(true);
        $this->flip = new FlipClient($FLIP_SECRET);
    }

    function createDisbursementRequest($bankCode, $accountNumber, $amount, $remark) {
        $this->flip->postDisbursementRequest($bankCode, $accountNumber, $amount, $remark);
        // TODO save to db
    }

    function checkDisbursementStatus($transactionId) {
        $this->flip->checkDisbursementStatus($transactionId);
        // TODO update DB
    }

}

$app = new DisbursementApplication();
$app->createDisbursementRequest("bni", 1234, 1234, "testing");
$app->checkDisbursementStatus(123);
