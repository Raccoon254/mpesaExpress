<?php
$callbackContent = file_get_contents("php://input");
$callbackData = json_decode($callbackContent, true);

$resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
$resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];
$CheckoutRequestID = $callbackData['Body']['stkCallback']['CheckoutRequestID'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=cashoutc_data;charset=utf8', 'cashoutc_raccoon', '@Raccoon254');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($resultCode == 0) {
        $amount = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $mpesaReceiptNumber = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];

        $query = "UPDATE orders SET Status = 'completed' WHERE CheckoutID = :checkout_request_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':checkout_request_id', $CheckoutRequestID, PDO::PARAM_STR);
        $stmt->execute();

        file_put_contents("success_transactions.txt", "Result Code: " . $mpesaReceiptNumber . "\n" . "Result Description: " . $amount . "\n", FILE_APPEND);
    } else {
        $query = "UPDATE orders SET Status = 'failed' WHERE CheckoutID = :checkout_request_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':checkout_request_id', $CheckoutRequestID, PDO::PARAM_STR);
        $stmt->execute();

        file_put_contents("failed_transactions.txt", "Result Code: " . $resultCode . "\n" . "Result Description: " . $resultDesc . "\n", FILE_APPEND);
    }
} catch (PDOException $e) {
    file_put_contents("failed_transactions.txt", "Error: Failed to connect to database or execute query: " . $e->getMessage() . "\n", FILE_APPEND);
}
?>