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

$flipClient = new FlipClient($FLIP_SECRET);
echo $flipClient->postDisbursementRequest("bni", 1234, 1234, "testing");
echo $flipClient->checkDisbursementStatus(123);
