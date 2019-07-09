<?php

class DisbursementDatabase {

    private $sqlite_db;

    private static $DB_NAME = "disbursement.sqlite3";

    function __construct($seed_database) {
        // Ref https://www.if-not-true-then-false.com/2012/php-pdo-sqlite3-example/
        $this->sqlite_db = new PDO('sqlite:' . DisbursementDatabase::$DB_NAME);
        $this->sqlite_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->create_db_if_missing();
        if ($seed_database) {
            $this->seed_database();
        }
    }

    private function create_db_if_missing() {
        $this->sqlite_db->exec(
            "CREATE TABLE IF NOT EXISTS disbursement (
                transaction_id INTEGER PRIMARY KEY, 
                status TEXT, 
                receipt TEXT, 
                time_served TEXT)");
    }

    private function seed_database() {
        $dummy_transaction = array(
            array(
                'transaction_id' => 1234,
                'status' => 'PENDING',
                'receipt' => '',
                'time_served' => "0000-00-00 00:00:00"),
            array(
                'transaction_id' => 1235,
                'status' => 'SUCCESS!',
                'receipt' => 'https://flip-receipt.oss-ap-southeast-5.aliyuncs.com/debit_receipt/126316_3d07f9fef9612c7275b3c36f7e1e5762.jpg',
                'time_served' => "2019-05-21 09:26:11"),
        );

        foreach ($dummy_transaction as $m) {
            $this->insert_transaction($m['transaction_id'], $m['status'], $m['receipt'], $m['time_served']);
        }
    }

    function insert_or_update_transaction($transaction_id, $status, $receipt, $time_served) {
        $insert = "INSERT OR REPLACE INTO disbursement (transaction_id, status, receipt, time_served) 
            VALUES (:transaction_id, :status, :receipt, :time_served)";
        $stmt = $this->sqlite_db->prepare($insert);

        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':receipt', $receipt);
        $stmt->bindParam(':time_served', $time_served);

        $stmt->execute();
    }

}
