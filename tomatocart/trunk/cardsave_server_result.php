<?php
	require_once('includes/application_top.php');
	require_once('includes/classes/order.php');

	function addStringToStringList($szExistingStringList, $szStringToAdd){
		$szReturnString = "";
		$szCommaString = "";

		if (strlen($szStringToAdd) == 0){
			$szReturnString = $szExistingStringList;
		}else{
			if (strlen($szExistingStringList) != 0){
				$szCommaString = ", ";
			}
			$szReturnString = $szExistingStringList.$szCommaString.$szStringToAdd;
		}

		return ($szReturnString);
	}   

	$szHashDigest = "";
	$szOutputMessage = "";
	$boErrorOccurred = false;
	$nStatusCode = 30;
	$szMessage = "";
	$nPreviousStatusCode = 0;
	$szPreviousMessage = "";
	$szCrossReference = "";
	$nAmount = 0;
	$nCurrencyCode = 0;
	$szOrderID = "";
	$szTransactionType= "";
	$szTransactionDateTime = "";
	$szOrderDescription = "";
	$szCustomerName = "";
	$szAddress1 = "";
	$szAddress2 = "";
	$szAddress3 = "";
	$szAddress4 = "";
	$szCity = "";
	$szState = "";
	$szPostCode = "";
	$nCountryCode = "";

	try{
		// hash digest
		if (isset($_POST["HashDigest"])){
			$szHashDigest = $_POST["HashDigest"];
		}
	
		// transaction status code
		if (!isset($_POST["StatusCode"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [StatusCode] not received");
			$boErrorOccurred = true;
		}else{
			if ($_POST["StatusCode"] == ""){
				$nStatusCode = null;
			}else{
				$nStatusCode = intval($_POST["StatusCode"]);
			}
		}
	
		// transaction message
		if (!isset($_POST["Message"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [Message] not received");
			$boErrorOccurred = true;
		}else{
			$szMessage = $_POST["Message"];
		}
				
		// status code of original transaction if this transaction was deemed a duplicate
		if (!isset($_POST["PreviousStatusCode"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [PreviousStatusCode] not received");
			$boErrorOccurred = true;
		}else{
			if ($_POST["PreviousStatusCode"] == ""){
				$nPreviousStatusCode = null;
			}else{
				$nPreviousStatusCode = intval($_POST["PreviousStatusCode"]);
			}
		}
					
		// status code of original transaction if this transaction was deemed a duplicate
		if (!isset($_POST["PreviousMessage"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [PreviousMessage] not received");
			$boErrorOccurred = true;
		}else{
			$szPreviousMessage = $_POST["PreviousMessage"];
		}
					
		// cross reference of transaction
		if (!isset($_POST["CrossReference"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [CrossReference] not received");
			$boErrorOccurred = true;
		}else{
			$szCrossReference = $_POST["CrossReference"];
		}
					
		// amount (same as value passed into payment form - echoed back out by payment form)
		if (!isset($_POST["Amount"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [Amount] not received");
			$boErrorOccurred = true;
		}else{
			if ($_POST["Amount"] == null){
				$nAmount = null;
			}else{
				$nAmount = intval($_POST["Amount"]);
			}
		}
					
		// currency code (same as value passed into payment form - echoed back out by payment form)
		if (!isset($_POST["CurrencyCode"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [CurrencyCode] not received");
			$boErrorOccurred = true;
		}else{
			if ($_POST["CurrencyCode"] == null){
				$nCurrencyCode = null;
			}else{
				$nCurrencyCode = intval($_POST["CurrencyCode"]);
			}
		}
					
		// order ID (same as value passed into payment form - echoed back out by payment form)
		if (!isset($_POST["OrderID"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [OrderID] not received");
			$boErrorOccurred = true;
		}else{
			$szOrderID = $_POST["OrderID"];
		}
				
		// transaction type (same as value passed into payment form - echoed back out by payment form)
		if (!isset($_POST["TransactionType"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [TransactionType] not received");
			$boErrorOccurred = true;
		}else{
			$szTransactionType = $_POST["TransactionType"];
		}
					
		// transaction date/time (same as value passed into payment form - echoed back out by payment form)
		if (!isset($_POST["TransactionDateTime"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [TransactionDateTime] not received");
			$boErrorOccurred = true;
		}else{
			$szTransactionDateTime = $_POST["TransactionDateTime"];
		}
					
		// order description (same as value passed into payment form - echoed back out by payment form)
		if (!isset($_POST["OrderDescription"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [OrderDescription] not received");
			$boErrorOccurred = true;
		}else{
			$szOrderDescription = $_POST["OrderDescription"];
		}
					
		// customer name (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["CustomerName"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [CustomerName] not received");
			$boErrorOccurred = true;
		}else{
			$szCustomerName = $_POST["CustomerName"];
		}
					
		// address1 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["Address1"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [Address1] not received");
			$boErrorOccurred = true;
		}else{
			$szAddress1 = $_POST["Address1"];
		}
					
		// address2 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["Address2"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [Address2] not received");
			$boErrorOccurred = true;
		}else{
			$szAddress2 = $_POST["Address2"];
		}
					
		// address3 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["Address3"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [Address3] not received");
			$boErrorOccurred = true;
		}else{
			$szAddress3 = $_POST["Address3"];
		}
					
		// address4 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["Address4"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [Address4] not received");
			$boErrorOccurred = true;
		}else{
			$szAddress4 = $_POST["Address4"];
		}
					
		// city (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["City"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [City] not received");
			$boErrorOccurred = true;
		}else{
			$szCity = $_POST["City"];
		}
					
		// state (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["State"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [State] not received");
			$boErrorOccurred = true;
		}else{
			$szState = $_POST["State"];
		}
					
		// post code (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["PostCode"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [PostCode] not received");
			$boErrorOccurred = true;
		}else{
			$szPostCode = $_POST["PostCode"];
		}
					
		// country code (not necessarily the same as value passed into payment form - as the customer can change it on the form)
		if (!isset($_POST["CountryCode"])){
			$szOutputMessage = addStringToStringList($szOutputMessage, "Expected variable [CountryCode] not received");
			$boErrorOccurred = true;
		}else{
			if ($_POST["CountryCode"] == ""){
				$nCountryCode = null;
			}else{
				$nCountryCode = intval($_POST["CountryCode"]);
			}
		}
	}catch (Exception $e){
		$boErrorOccurred = true;
		$szOutputMessage = "Error";
		if (!isset($_POST["Message"])){
			$szOutputMessage = $_POST["Message"];
		}
	}
	
	// The nOutputProcessedOK should return 0 except if there has been an error talking to the gateway or updating the website order system.
	// Any other process status shown to the gateway will prompt the gateway to send an email to the merchant stating the error.
	// The customer will also be shown a message on the hosted payment form detailing the error and will not return to the merchants website.
	$nOutputProcessedOK = 0;
	$transstatus = "failed";
			
	if (is_null($nStatusCode)){
		$nOutputProcessedOK = 30;		
	}
			
	if ($boErrorOccurred == true){
		$nOutputProcessedOK = 30;
	}
	

	// *********************************************************************************************************
	// You should put your code that does any post transaction tasks
	// (e.g. updates the order object, sends the customer an email etc) in this section
	// *********************************************************************************************************
	if ($nOutputProcessedOK != 30){	
		$nOutputProcessedOK = 0;
		// Alter this line once you've implemented the code.
		//$szOutputMessage = $szMessage."--"."Environment specific function needs to be implemented by merchant developer";
		try{
			switch ($nStatusCode){
				// transaction authorised
				case 0:						
					$transstatus = "passed";
					break;
				// card referred (treat as decline)
				case 4:						
					$transstatus = "failed";
					break;
				// transaction declined
				case 5:
					$transstatus = "failed";
					break;				
				// duplicate transaction
				case 20:
					// need to look at the previous status code to see if the
					// transaction was successful
					if ($nPreviousStatusCode == 0){
						$transstatus = "passed";	
						break;
					} else {
						$transstatus = "failed";
						break;
					}
					break;
					// error occurred
				case 30:
					$transstatus = "failed";	
					break;
				default:
					$transstatus = "failed";
					break;
			}
				
		
			if ($transstatus == "passed") {

				$comments = 'Cardsave_redirect IPN Verified.';

				osC_Order::process($szOrderID, MODULE_PAYMENT_CARDSAVE_REDIRECT_ORDER_STATUS_ID, $comments);
			} 
		}catch (Exception $e){
			$nOutputProcessedOK = 30;
			$szOutputMessage = "Error updating website system, please ask the developer to check code";
		}
  }
		
	if ($nOutputProcessedOK != 0 && $szOutputMessage == ""){
		$szOutputMessage = "Unknown error";
	}	
			
	echo("StatusCode=".$nOutputProcessedOK."&Message=".$szOutputMessage);
    	
    