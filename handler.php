<?php
$callbackContent = file_get_contents("php://input");
$callbackData = json_decode($callbackContent, true);

$resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
$resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];

$mysqli = new mysqli("localhost", "root", "pass", "db");
if ($mysqli->connect_errno) {
    // Failed to connect to MySQL
    file_put_contents("failed_transactions.txt", "Error: Failed to connect to MySQL: " . $mysqli->connect_error . "\n", FILE_APPEND);
    exit;
}

if ($resultCode == 0) {
    // Transaction was successful
    $amount = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
    $mpesaReceiptNumber = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
    $transactionDate = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
    $mobileNumber = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

    // Update the transaction status in the database
    $query = "UPDATE orders SET Status = 'completed' WHERE Phone = '$mobileNumber'";
    if ($mysqli->query($query)) {
        // Successfully updated data in the database
        file_put_contents("success_transactions.txt", "Result Code: " .$mpesaReceiptNumber . "\n" . "Result Description: " . $amount . "\n"."Mobile Number:" .$mobileNumber ."\n", FILE_APPEND);
    } else {
        // Failed to update data in the database
        file_put_contents("failed_transactions.txt", "Error: Failed to update data in the database: " . $mysqli->error . "\n", FILE_APPEND);
    }
} else {
    // Transaction was unsuccessful
    $mpesaReceiptNumber = $callbackData['Body']['stkCallback']['CheckoutRequestID'];

    // Update the transaction status in the database as 'failed'
    $update_query = "UPDATE orders SET Status = 'failed' WHERE Phone = '$mobileNumber'";
    $mysqli->query($update_query);

    file_put_contents("failed_transactions.txt", "Result Code: " . $resultCode . "\n" . "Result Description: " . $resultDesc . "\n", FILE_APPEND);
}

$mysqli->close();
?>