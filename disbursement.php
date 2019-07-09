<?php
require_once('FlipClient.php');
require_once('DisbursementDatabase.php');

$FLIP_SECRET = getenv("FLIP_SECRET");

$flip_client = new FlipClient($FLIP_SECRET);
echo $flip_client->postDisbursementRequest("bni", 1234, 1234, "testing");
echo $flip_client->checkDisbursementStatus(123);

$database = new DisbursementDatabase(true);
