<?php
class CJ implements Import
{
	private $startDate;
	private $endDate;
	private $developerKey;
	private $websiteID;
	private $affiliateID;
	private $siteId;
	private $aTransactions;
	private $aClicks;
	private $retMessage;
	private $aResults;
	private $oXML;
	
	public function __construct($developerKey, $websiteID, $startDate = '',$endDate = '')
	{
		if($developerKey != '')			$this->developerKey 	= $developerKey; 
		if($websiteID != '')			$this->websiteID 		= $websiteID; 		
		$this->startDate = $startDate;
		$this->endDate 	 = $endDate;
		
		$ini = ini_set("soap.wsdl_cache_enabled","0");
	}
	
	public function loadFeedXML()
	{
	
	try 
	{
		$cURL = 'https://commission-detail.api.cj.com/v3/commissions?requestor-cid=5284959';
		$cURL .= '&date-type=posting&';
		$cURL .= 'start-date='.$this->startDate.'&';
		$cURL .= 'end-date='.$this->endDate .'&';
		$cURL .= 'website-ids=' .$this->websiteID;
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $cURL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					  'Authorization: Bearer ' . $this->developerKey,
					  'User-Agent: "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.15) Gecko/2009101601 Firefox/3.0.15 GTB6 (.NET CLR 3.5.30729)"'
					));
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$cHTML = curl_exec($ch);
		if (curl_error($ch)) {
			echo "Curl error: " . curl_error($ch);
		} // ends if (curl_error($ch))
		else {
			$cXML = simplexml_load_string($cHTML);
		//var_dump($cXML);
	
			
			
		} // ends else from if (curl_error($ch))
		$this->oXML = $cXML->commissions;
		
		echo "<pre>";
		print_r($this->oXML);
		echo "</pre>";
		// die;
		
		if($this->oXML) 
			return true;
		else
			return false;
    } catch (Exception $e){
        echo "There was an error with your request or the service is unavailable.\n";
        print_r ($e);
    }
	}
	public function retResultsArray()
	{
		$arrStatus = array('new' => 1, 'extended' => 1, 'closed' => 2, 'locked' => 3);
		$i = 0;
		$commission_amount 	= 'commission-amount';
		$commission_id 		= 'commission-id';
		$sale_amount 		= 'sale-amount';
		$event_date 		= 'event-date';
		$action_status 		= 'action-status';
		$order_id			= 'order-id';
		foreach($this->oXML->commission as $oTransaction)
		{	
			


			$commission = floatval(preg_replace('~[^0-9.,]~', null, $oTransaction->$commission_amount));
			$commission = round($commission, 2);
			$orderAmount =  $oTransaction->$sale_amount;
			if($orderAmount<0.01){continue;}
			$orderAmount = round($orderAmount, 2);
			
			echo $ID = (string) $oTransaction->$commission_id;
			if($ID == '')
				$ID = (string) $oTransaction->$order_id;

			$user_id     = '';
			$retailer_id = '';
			$cashback    = 0;
			$titles      = '';
			$vat_value  = 1;


			$objCallback = $GLOBALS['DB']->queryUniqueObject("select * from cashbackengine_clickhistory where click_id = '".(string)$oTransaction->sid."'");
			if($objCallback)
			{ 
             $user_id = $objCallback->user_id;
             $retailer_id = $objCallback->retailer_id;

                 $objCashback = $GLOBALS['DB']->queryUniqueObject("select cashback,title,network_id from cashbackengine_retailers where retailer_id = '".$retailer_id."'");
                 if($objCashback){

                 	$cashback = $objCashback->cashback;
                 	$this_time_cashback = $objCashback->cashback;
                 	
                 	$network_id= $objCashback->network_id;
                 	
                 	$titles    = $objCashback->title;

                 	//$cashback_res = (explode(" ",$cashback));

      //            	if($cashback_res[1] == '%'){
                 		 
 					//    if($retailer_id == 281 && $user_id == 2333)
      //            		{
      //                    $cashback = $orderAmount*3.5 * $vat_value/100;

      //            		}else{
      //            			//echo 'afsd'.$orderAmount;exit;

      //            	    	$cashback = $orderAmount*$cashback_res[0] * $vat_value/100;
						// }
						// // $cashback = $orderAmount*$cashback_res[0] * $vat_value/100;

      //            	}else{

      //            		$cashback = $cashback_res[0];
      //            	}
                 }

			}

			$status = (string)$oTransaction->$action_status;

			$arrStatus = array('P' => 'pending', 'F' => 'failed', 'A' => 'confirmed', 'D' => 'declined');
			$updated_status = $arrStatus[(string)$status];

            $p_time = date('Y-m-d' , strtotime((string)$eventDate_arr[0]));


			//$commission 	= floatval(preg_replace('~[^0-9.,]~', null, $oTransaction->$commission_amount));
			//$orderAmount 	= floatval(preg_replace('~[^0-9.,]~', null, $oTransaction->$sale_amount));
			$eventDate_arr 	= explode('T',$oTransaction->$event_date);

			if($commission <= 0){
				$cashback = 0;
			}

			if((string)$oTransaction->sid>0){
				$this->aResults[$i]['clickref'] 	= (string)$oTransaction->sid;
				$this->aResults[$i]['network'] 		= 'CJ Network';
				$this->aResults[$i]['network_id']	= $network_id;
				$this->aResults[$i]['details'] 		= $titles;
				$this->aResults[$i]['program_id'] 	= $ID;
				$this->aResults[$i]['commission'] 	= $commission;
				$this->aResults[$i]['ordervalue']	= $orderAmount;
				$this->aResults[$i]['cwhen'] 		= (string)$eventDate_arr[0];
				$this->aResults[$i]['reference_id'] = 'CJ'.$ID;
				$this->aResults[$i]['eventName']  	= '';
				$this->aResults[$i]['user_id'] 		=  $user_id;
				$this->aResults[$i]['retailer_id']  = $retailer_id;
				$this->aResults[$i]['cashback']  	= round($cashback ,2);
				$this->aResults[$i]['this_time_cashback']  	= $this_time_cashback;
				$this->aResults[$i]['payment_type'] = 'Cashback '.$titles.' '. (string)$eventDate_arr[0]. ' (' . $user_id . ')';
				$this->aResults[$i]['status'] 		= 'pending';
				//$this->aResults[$i]['get_status'] 		= (string)$oTransaction->$action_status;
				$i++;
			}


		}
		echo "<pre>";
		print_r($this->aResults);
		echo "</pre>";
		//die;
		
	return $this->aResults;
		
	}
}