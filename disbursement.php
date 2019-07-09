<?php

require_once('DisbursementApplication.php');

$app = new DisbursementApplication();

print('Create disbursement request for following data: ');
print('Bank       : BNI');
print('Account no : 1234');
print('Amount     : 1234');
print('Remark     : testing');
$transaction_id = $app->createDisbursementRequest("bni", 1234, 1234, "testing");

print('Success sending');
print('Transaction id: ${transaction_id}');

print('==================================');

print('Checking status');
$isSuccess = $app->checkDisbursementStatus(123);
print('Is completed : ' . $isSuccess);
