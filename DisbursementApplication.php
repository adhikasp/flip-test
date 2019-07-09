<?php
require_once('FlipClient.php');
require_once('DisbursementDatabase.php');

class DisbursementApplication {

    private $db;
    private $flip;

    function __construct() {
        $this->read_env();
        $FLIP_SECRET = getenv("FLIP_SECRET");

        $this->db = new DisbursementDatabase(true);
        $this->flip = new FlipClient($FLIP_SECRET);
    }

    private function read_env() {
        if(file_exists('./env.php')) {
            include('./env.php');
        }
    }

    function createDisbursementRequest($bankCode, $accountNumber, $amount, $remark) {
        $response = $this->flip->postDisbursementRequest($bankCode, $accountNumber, $amount, $remark);
        $this->db->insert_or_update_transaction(
            $response['id'],
            $response['status'],
            $response['receipt'],
            $response['time_served']
        );
        return $response['id'];
    }

    function checkDisbursementStatus($transactionId) {
        $response = $this->flip->checkDisbursementStatus($transactionId);
        $this->db->insert_or_update_transaction(
            $response['id'],
            $response['status'],
            $response['receipt'],
            $response['time_served']
        );
        return $response['status'] == 'SUCCESS';
    }

}
