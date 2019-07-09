<?php
$FLIP_SECRET = getenv("FLIP_SECRET");

class FlipClient {

    private $curl_opt;

    private static $DEFAULT_CURL_OPT = array(
        CURLOPT_URL => "https://nextar.flip.id/disburse",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "Content-Type: application/x-www-form-urlencoded",
            "Host: nextar.flip.id",
            "accept-encoding: gzip, deflate",
            "cache-control: no-cache",
        ),
        CURLOPT_SSL_VERIFYHOST => 0, // TODO hack to avoid SSL setup
        CURLOPT_SSL_VERIFYPEER => 0,
    );

    function __construct($secret) {
        $this->curl_opt = FlipClient::$DEFAULT_CURL_OPT;
        array_push($this->curl_opt[CURLOPT_HTTPHEADER], "Authorization: basic " . $secret);
    }

    private function mergeCurlOpt($replacement) {
        return $this->curl_opt + $replacement;
    }

    function postDisbursementRequest($bankCode, $accountNumber, $amount, $remark) {
        $ch = curl_init();
        curl_setopt_array($ch, $this->mergeCurlOpt(array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "bank_code=${bankCode}&account_number=${accountNumber}&amount=${amount}&remark=${remark}",
        )));

        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            echo "cURL Error #:" . $err;
            return $err;
        } else {
            return $response;
        }
    }

    function checkDisbursementStatus($transactionId) {
        $ch = curl_init();
        curl_setopt_array($ch, $this->mergeCurlOpt(array(
            CURLOPT_URL => "https://nextar.flip.id/disburse" . $transactionId,
            CURLOPT_CUSTOMREQUEST => "GET",
        )));

        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            echo "cURL Error #:" . $err;
            return $err;
        } else {
            return $response;
        }
    }

}

$flip_client = new FlipClient($FLIP_SECRET);
echo $flip_client->postDisbursementRequest("bni", 1234, 1234, "testing");
echo $flip_client->checkDisbursementStatus(123);

// Ref https://www.if-not-true-then-false.com/2012/php-pdo-sqlite3-example/
$file_db = new PDO('sqlite:disbursement.sqlite3');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$file_db->exec("CREATE TABLE IF NOT EXISTS disbursement (
                    transaction_id INTEGER PRIMARY KEY, 
                    status TEXT, 
                    receipt TEXT, 
                    time_served TEXT)");

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

$insert = "INSERT INTO disbursement (transaction_id, status, receipt, time_served) 
                VALUES (:transaction_id, :status, :receipt, :time_served)";
$stmt = $file_db->prepare($insert);

// Bind parameters to statement variables
$stmt->bindParam(':transaction_id', $transaction_id);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':receipt', $receipt);
$stmt->bindParam(':time_served', $time_served);

// Loop thru all messages and execute prepared insert statement
foreach ($dummy_transaction as $m) {
    // Set values to bound variables
    $transaction_id = $m['transaction_id'];
    $status = $m['status'];
    $receipt = $m['receipt'];
    $time_served = $m['time_served'];

    // Execute statement
    $stmt->execute();
}
