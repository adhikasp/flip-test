<?php
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => "https://nextar.flip.id/disburse",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "bank_code=x&account_number=x&amount=x&remark=x",
    CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Authorization: basic xxx",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "Content-Type: application/x-www-form-urlencoded",
        "Host: nextar.flip.id",
        "User-Agent: PostmanRuntime/7.15.0",
        "accept-encoding: gzip, deflate",
        "cache-control: no-cache",
        "content-length: 46",
    ),
    CURLOPT_SSL_VERIFYHOST => 0, // TODO hack to avoid SSL setup
    CURLOPT_SSL_VERIFYPEER => 0,
));

$response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}
