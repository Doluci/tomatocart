<?php
      $querystring="?CustomerID=87654321&UserName=TestAccount&AccessPaymentCode=".$_REQUEST['AccessPaymentCode'];
      $posturl="https://payment.ewaygateway.com/Result".$querystring;
        
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $posturl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      if (CURL_PROXY_REQUIRED == 'True'){
        $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
        curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
        curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
      }
      
      $response = curl_exec($ch);
      
	    function fetch_data($string, $start_tag, $end_tag){
	      $position = stripos($string, $start_tag);  
	      $str = substr($string, $position);      
	      $str_second = substr($str, strlen($start_tag));     
	      $second_positon = stripos($str_second, $end_tag);     
	      $str_third = substr($str_second, 0, $second_positon);     
	      $fetch_data = trim($str_third);   
	      return $fetch_data; 
	    } 

	    $responsecode = fetch_data($response, '<responsecode>', '</responsecode>'); 
      $trxnnumber = fetch_data($response, '<trxnnumber>', '</trxnnumber>'); 
			    
		  if($responsecode=="00" || $responsecode=="08" || $responsecode=="10" || $responsecode=="11" || $responsecode=="16") {    
//		    处理订单状态
		  }
?>