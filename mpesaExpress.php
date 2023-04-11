<?php 
	session_start();
    $conn = mysqli_connect("localhost", "cashoutc_raccoon", "@Raccoon254", "cashoutc_data");
	
	$errors  = array();
	$errmsg  = '';
	
	$config = array(
		"env"              => "sandbox",
		"BusinessShortCode"=> "174379",
		"key"              => "Xl6TAczGadVkeQGvDpEEpuJljl0tGcUv", //Enter your consumer key here
		"secret"           => "ARoBsLIvgOGX9nIO", //Enter your consumer secret here
		"username"         => "appTest",
		"TransactionType"  => "CustomerPayBillOnline",
		"passkey"          => "MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjMwNDExMDYzNjEy", //Enter your passkey here
		"AccountReference" => "codeHacks™️",
		"TransactionDesc"  => "Payment of X" ,
	);
	
	
	
	if (isset($_POST['phone_number'])) {
	
		$phone = $_POST['phone_number'];
		$orderNo = $_POST['orderNo'];
		$amount = 1;
	
		$phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
		$phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
		$phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;
	
	

		date_default_timezone_set('Africa/Nairobi');
		$access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
		$initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
		
		$headers = ['Content-Type:application/json; charset=utf8'];
		$des = "CustomerPaybillOnline";
		
		$curl = curl_init($access_token_url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_USERPWD, 'daRBcfCR3n323PlzQImG2oqkADVUmPwJ:3Ck43APSfPnGTzi6');
		$result = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$result = json_decode($result);
		curl_close($curl);
		$access_token = $result->access_token;  
		$token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

	
		$timestamp = date("YmdHis");
		$password = base64_encode("174379bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919".$timestamp);
	
		$curl_post_data = array( 
			"BusinessShortCode" => $config['BusinessShortCode'],
			"Password" => $password,
			"Timestamp" => $timestamp,
			"TransactionType" => 'CustomerPayBillOnline',
			"Amount" => $amount,
			"PartyA" => $phone,
			"PartyB" => $config['BusinessShortCode'],
			"PhoneNumber" => $phone,
			"CallBackURL" => "https://cashout.co.ke/safaricom/handler.php",
			"AccountReference" => $config['AccountReference'],
			"TransactionDesc" => $config['TransactionDesc'],
		); 
	
		$data_string = json_encode($curl_post_data);
	
		$endpoint = ($config['env'] == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest"; 
	
		$ch = curl_init($endpoint );
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer '.$token,
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response     = curl_exec($ch);
		curl_close($ch);
	
		$result = json_decode(json_encode(json_decode($response)), true);
	
		if(!preg_match('/^[0-9]{10}+$/', $phone) && array_key_exists('errorMessage', $result)){
			$errors['phone'] = $result["errorMessage"];
		}
	
		if($result['ResponseCode'] === "0"){         //STK Push request successful

			$MerchantRequestID = $result['MerchantRequestID'];
			$CheckoutRequestID = $result['CheckoutRequestID'];
	
			//Saves your request to a database
		   
			$sql = "INSERT INTO `orders`(`OrderNo`, `Amount`, `Phone`, `Status`, `CheckoutID`) VALUES ('".$orderNo."','".$amount."','".$phone."','pending', '".$CheckoutRequestID."');";
			
			if ($conn->query($sql) === TRUE){
				
				$_SESSION["phone"] = $phone;

				header("Location: refresh.php");
	
			}else{
				$errors['database'] = "Unable to initiate your order: ".$conn->error;;  
				foreach($errors as $error) {
					$errmsg .= $error . '<br />';
				} 
			}
			
		}else{
			$errors['mpesastk'] = $result['errorMessage'];
			foreach($errors as $error) {
				$errmsg .= $error . '<br />';
			}
		}
		
	}
	
	?>