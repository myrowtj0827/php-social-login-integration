<?php
class Hareerdeals implements Import
{
	
	private $startDate;
	private $endDate;
	private $NetBooster_key;
	private $xmlPass;
	private $siteId;
	private $oXML;
	private $retMessage;
	private $aResults;
	
	public function __construct($bmillon_LoginToken, $startDate = '', $endDate = '')
	{

		/*if($startDate != '')
		{
			$tempDate 	= explode('-', $startDate);
			$startDate 	= $tempDate[2].'/'.$tempDate[1].'/'.$tempDate[0];
		}
		if($endDate != '')
		{
			$tempDate 	= explode('-', $endDate);
			$endDate 	= $tempDate[2].'/'.$tempDate[1].'/'.$tempDate[0];
		}*/
		
		
		// if($startDate == '')
		//$startDate = date('2016-10-13');

			
		//if((date('Y')-2012)==0)	$startDate = '2012-01-01';

		//if($endDate == '')
		//	$endDate = date("Y-m-d", time()+86400);

		// $startDate = str_replace('-','/',$startDate);
		// $endDate = str_replace('-','/',$endDate);
		

		$this->startDate 			= $startDate;
		$this->endDate 				= $endDate;
		$this->adService_PID    	= $adService_PID;
		$this->adService_LoginToken = $adService_LoginToken;
	}

	
	public function loadFeedXML()
	{

		//echo 'miss u '.$this->startDate;exit;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://arabclicks.api.hasoffers.com/Apiv3/json?api_key=5c84485082cd02fecf3df98be20c42f329ef447f7ad57ed104dcadf495d77af2&Target=Affiliate_Report&Method=getConversions&fields[]=PayoutGroup.name&fields[]=Offer.name&fields[]=Stat.affiliate_info1&fields[]=Goal.name&fields[]=Stat.conversion_status&fields[]=Stat.currency&fields[]=Stat.ad_id&fields[]=OfferUrl.name&fields[]=OfferUrl.id&fields[]=PayoutGroup.id&fields[]=Stat.date&fields[]=Stat.approved_payout&fields[]=OfferUrl.preview_url&filters[Stat.datetime][conditional]=BETWEEN&fields[]=Stat.sale_amount&filters[Stat.datetime][values][]=2020-06-01+00%3A00%3A00+&filters[Stat.datetime][values][]='.$this->endDate.'+00%3A00%3A00+&sort[Stat.year]=asc');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Decode the response into a PHP associative array
$response = json_decode($response, true);

// Make sure that there wasn't a problem decoding the repsonse
if(json_last_error()!==JSON_ERROR_NONE){
	throw new RuntimeException(
		'API response not well-formed (json error code: '.json_last_error().')'
	);
}

// Print out the response details or, any error messages
if(isset($response['response']['status']) && $response['response']['status']===1){
	echo 'API call successful';
	echo PHP_EOL;
	//echo 'Response Data: <pre>'.print_r($response['response']['data'], true).'';
	echo PHP_EOL;
}else{
	echo 'API call failed'.(isset($response['response']['errorMessage'])?' ('.$response['response']['errorMessage'].')':'').'';
	echo PHP_EOL;
	echo 'Errors: <pre>'.print_r($response['response']['errors'], true).'';
	echo PHP_EOL;
}

	   


		$this->oXML = $response['response']['data']['data'];
       echo 'testing';
	  // echo '<pre>';  print_r($this->oXML); echo '</pre>';exit;

		if(sizeof($this->oXML) < 1)
			return false;
		else
			return true;

	}
	
	
	public function retResultsArray()
	{  

		 


		
			//echo $exchange_rate_usd;exit;

		//echo '<pre>';print_r( $this->oXML ); echo '</pre>';exit;
		$arrStatus = array('new' => 1, 'extended' => 1, 'closed' => 2, 'locked' => 3);
		$i = 0;
		$commission_amount 	= 'commission-amount';
		$commission_id 		= 'commission-id';
		$sale_amount 		= 'sale-amount';
		$event_date 		= 'event-date';
		$action_status 		= 'action-status';
		$order_id			= 'order-id';
		//echo "testing";
		 foreach($this->oXML as $oTransaction[$i]) 
		{ //echo '<pre>';print_r( $oTransaction[$i] ); echo '</pre>';exit;
			//echo 'testing';echo 'testing';echo 'testing';
	    //echo $oTransaction[$i]['data']['data'][$i]['OfferUrl']['id'].'test'.$i;
		//echo 'aaaaaaa'.$oTransaction[$i]['Goal']['name'];exit;
		   //if(isset($oTransaction[$i]['Goal']['name']) && $oTransaction[$i]['Goal']['name'] == 'Deposit')
		   if(isset($oTransaction[$i]['Stat']['sale_amount']) && $oTransaction[$i]['Stat']['affiliate_info1']>0)
		   {//echo '<pre>';print_r( $oTransaction[$i] ); echo '</pre>';
			//echo '<pre>' ;print_r($oTransaction[$i]);
			//$oTransaction[$i] = $this->oXML[$i];
			// $commission = floatval(preg_replace('~[^0-9.,]~', null, $oTransaction[$i]['Stat']['approved_payout']));
			// $commission = round($commission, 2);
			// $orderAmount = floatval(preg_replace('~[^0-9.,]~', null, $oTransaction[$i]['Stat']['approved_payout']));
			// $orderAmount = round($orderAmount, 2);

			$commission = floatval(preg_replace('~[^0-9.,]~', null, $oTransaction[$i]['Stat']['sale_amount']));
			$commission = round($commission, 2);
			$orderAmount = floatval(preg_replace('~[^0-9.,]~', null, $oTransaction[$i]['Stat']['sale_amount']));
			$orderAmount = round($orderAmount, 2);


			
			$ID = trim((string) $oTransaction[$i]['Stat']['ad_id']);
			if($ID == '')
				$ID = (string) $oTransaction[$i]['Stat']['ad_id']; 

			$user_id     = '';
			$retailer_id = '';
			$cashback    = 0;
			$titles      = '';
			$vat_value  = 1;
			$objCallback = $GLOBALS['DB']->queryUniqueObject("select * from cashbackengine_clickhistory where click_id = '".(string)$oTransaction[$i]['Stat']['affiliate_info1']."'");
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

                 	$cashback_res = (explode("%",$cashback));


                 	if(strpos($cashback, '%') !== false){
						
						 $cashback = $orderAmount*$cashback_res[0] /100;
 					}else{

                 		$cashback = $cashback_res[0];
                 	}
                 }

			}
			$status = (string)$oTransaction[$i]['Stat']['conversion_status'];
 			$arrStatus = array('Approved' => 'pending', 'Failed' => 'failed', 'Confirmed' => 'confirmed', 'Denied' => 'declined');
			$updated_status = $arrStatus[(string)$status];
	        $p_time = date('Y-m-d' , strtotime($oTransaction[$i]['Stat']['date']));
				
				 
			
			    $this->aResults[$i]['clickref'] = (string)$oTransaction[$i]['Stat']['affiliate_info1'];
				$this->aResults[$i]['network'] = 'Hareerdeals';
				$this->aResults[$i]['network_id'] = $network_id;
				$this->aResults[$i]['details'] = $titles;
				$this->aResults[$i]['commission'] = $commission;
				$this->aResults[$i]['ordervalue'] = $orderAmount;
				$this->aResults[$i]['cwhen'] = substr((string)$oTransaction[$i]['Stat']['date'], 0, 19);
				$this->aResults[$i]['reference_id'] = 'HRMD'.$ID;
				$this->aResults[$i]['program_id'] = $ID;
				$this->aResults[$i]['eventName']  = (string)$oTransaction[$i]['Goal']['name'];
				$this->aResults[$i]['user_id']  =  $user_id;
				$this->aResults[$i]['retailer_id']  = $retailer_id;
				$this->aResults[$i]['cashback']  = round($cashback ,2);
				$this->aResults[$i]['this_time_cashback']  = $this_time_cashback;
				$this->aResults[$i]['payment_type']  = 'Cashback '.$titles.' '. $p_time. ' (' . $user_id . ')';
				$this->aResults[$i]['status'] = 'pending';

    
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