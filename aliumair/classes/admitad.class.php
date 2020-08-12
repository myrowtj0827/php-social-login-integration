<?php 
class Admitad implements Import
{
	private $startDate;
	private $endDate;
	private $oResults;
	
	public function __construct($startDate = '', $endDate = '')
	{
		$startDate  = date('d-m-Y', strtotime($startDate));
	   // $startDate = $startDate.'aa';die();
		$endDate = date("d-m-Y", strtotime($endDate));
		$this->startDate = str_replace('-','.',$startDate);
		$this->endDate = str_replace('-','.',$endDate);

	}
	
	private function includeIfExists($file)
	{
		if (file_exists($file)) {
			return include $file;
		}
	}
	
	public function loadFeedXML(){

		//echo __DIR__.'/../admitad/vendor/autoload.php';exit;

		if ((!$loader = $this->includeIfExists(__DIR__.'/../admitad/vendor/autoload.php')) && (!$loader = $this->includeIfExists(__DIR__.'/../../../../autoload.php'))) {
			die('You must set up the project dependencies, run the following commands:'.PHP_EOL.
				'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
				'php composer.phar install'.PHP_EOL);
		}
		
		$loader->add('Admitad\Api\Tests\\', __DIR__);
		
		$api = new Admitad\Api\Api();
		
		$response = $api->authorizeByPassword('BuX9X2gUmdXkHUkKFna10MxRBEFsHW', 'A5t3wHteMmIGJZUegIS94oM7HgziUz', 'statistics', 'halamiles', '{6baAHzF');
		$result = $response->getArrayResult(); // or $response->getArrayResult();
		
		$api1 = new Admitad\Api\Api($result['access_token']);


	// print_r($api1);exit;
		
		/*$data = $api1->get('/statistics/actions/', array(
			'date_start' => $this->startDate,
			'date_end' => $this->endDate,
			'limit' => 100,
			'offset' => 0
		))->getResult();
		echo '<pre>';
		print_r($data);
		echo("</pre>");
		die;*/
		/*foreach($data as $commission){
			$commission = (array)$commission;
				echo '<pre>';
				print_r($commission);
				echo("</pre>");
		}
		die;*/
		
		//echo  $this->startDate.'aaaaaa';	exit;	
		$contents = $api1->getIterator('/statistics/actions/', array(
			'date_start' => $this->startDate,
			'date_end' => $this->endDate,
			'limit' => 200,
			'offset' => 0
		)); 		
		//echo '>>'.$this->endDate;exit;exit;;

		

		if($contents->limit>0){
			$this->oResults = $contents;
			return true;
		}else{
			return false;
		}
		
	}
	
	public function retResultsArray(){

		$arrStatus = array('pending' => 'pending', 'approved_but_stalled' => 'pending', 'declined' => 'declined', 'approved' => 3);


		//$checkcurrency = $GLOBALS['DB']->queryobjects("SELECT * FROM currencies WHERE `date_time` > (NOW() - INTERVAL 240 MINUTE)");
           // echo mysqli_num_rows(($checkcurrency));
   //          if(mysqli_num_rows($checkcurrency) >= 3)
			// { 
			// 		while($rowy = mysqli_fetch_array($checkcurrency))
			// 			{ //print_r($rowy);
			// 				if($rowy['from_currency'] == 'GBP'){
			// 					$exchange_rate = $rowy['value'];
			// 				}elseif($rowy['from_currency'] == 'USD'){
			// 					$exchange_rate_usd = $rowy['value'];
			// 				}elseif($rowy['from_currency'] == 'EUR'){
			// 					$exchange_rate_euro = $rowy['value'];
			// 				}else{
			// 					//$exchange_rate = $GLOBALS['DB']->get_currency('GBP','SEK',1) ;
			// 					//$exchange_rate_usd = $GLOBALS['DB']->get_currency('USD','SEK',1) ;
			// 					//$exchange_rate_euro = $GLOBALS['DB']->get_currency('EUR','SEK',1) ;
			// 				}
			// 			}	
			
			// }else{
			// 	  $exchange_rate = $GLOBALS['DB']->get_currency('GBP','SEK',1) ;
			// 	  $exchange_rate_usd = $GLOBALS['DB']->get_currency('USD','SEK',1) ;
			// 	  $exchange_rate_euro = $GLOBALS['DB']->get_currency('EUR','SEK',1) ;
			// }
		
		$i = 0;

		//print_r($this->oResults);exit;
// 		foreach($this->oResults as $row)
// 		{
// echo 111111;
// 				echo '<pre>';
// 				print_r($row);
// 				echo("</pre>");
// 				//exit;
// 		}
// 		exit;		
		foreach($this->oResults as $row)
		{
    //             echo 222;
				// echo '<pre>';
				// print_r($row);
				// echo("</pre>");
				// exit;


			// if($row->currency == 'SEK'){
			// 	 $exchange_rate = 1;
			//   }elseif($row->currency == 'EUR'){
			// 	 $exchange_rate = $exchange_rate_euro;
			//   }elseif($row->currency == 'GBP'){
			// 	 $exchange_rate = $exchange_rate;
			//   }elseif($row->currency == 'USD'){
			// 	 $exchange_rate = $exchange_rate_usd;
			//   }else{
			// 	 $exchange_rate = 1;
			//   }	
		   
		   	//echo $oTransaction[$i]->commission;
			//echo($oTransaction[$i]->advertiserId);exit;
			//$oTransaction[$i] = $this->oXML[$i];
			$commission = floatval(preg_replace('~[^0-9.,]~', null, $row->payment));
			$commission = round($commission, 2);
			$orderAmount = floatval(preg_replace('~[^0-9.,]~', null, $row->cart)); 
			$orderAmount = round($orderAmount, 2);	

			 
			$clickref = $row->subid;

			$ID = trim((string) $row->order_id);
			if($ID == '')
				$ID = (string) $row->order_id;

			$user_id     = '';
			$retailer_id = '';
			$cashback    = 0;
			$titles      = '';
			$vat_value  = 1;
			$objCallback = $GLOBALS['DB']->queryUniqueObject("select * from cashbackengine_clickhistory where click_id = '".(string)$clickref."'");
			if($objCallback)
			{
			 $event     = $row->action; 
             $user_id = $objCallback->user_id;
             $retailer_id = $objCallback->retailer_id;

                 $objCashback = $GLOBALS['DB']->queryUniqueObject("select cashback,title,network_id from cashbackengine_retailers where retailer_id = '".$retailer_id."'");
                 if($objCashback){

                 	$cashback = $objCashback->cashback;
                 	$this_time_cashback = $cashback;
                 	 
                 	$network_id= $objCashback->network_id;
                 	
                 	$titles    = $objCashback->title;

                 	$cashback_res = (explode("%",$cashback));


                 	if(strpos($cashback, '%') !== false){

                 		 
                 		//echo $cashback_res[0];exit;
                 		//echo $cashback_res[0];exit;

                 		$cashback = $commission*$cashback_res[0] /100;

                 		//$cashback = $commission*50 /100;


                 	}else{

                 		$cashback = $cashback_res[0];
                 	}
                 }

			}

			///special users code ///

			// if($user_id == 2333 && $retailer_id == 195){
			// 	$cashback = $commission*75 * $vat_value/100;
			// 	$this_time_cashback = '75 %';
			// }


			// if($user_id == 2333 && $retailer_id == 1067){
			// 	$cashback = $commission*67 * $vat_value/100;
			// 	$this_time_cashback = '67 %';
			// }

			// if($user_id == 2333 && $retailer_id == 281){
			// 	$cashback = $commission*70.8 * $vat_value/100;
			// 	$this_time_cashback = '70.8 %';
			// }

			// if($user_id == 17210 && $retailer_id == 295){
			// 	$cashback = $orderAmount*1 * $vat_value/100;
			// 	$this_time_cashback = '1 %';
			// }


			//// end special users code ////

			 

			$p_time = date('Y-m-d' , strtotime($row->click_date));
			
			if($clickref!='' && $row->payment>0){
				
				 


				 



				$this->aResults[$i]['clickref'] = $clickref;
				$this->aResults[$i]['network'] = 'Admitad';
				$this->aResults[$i]['network_id'] = $network_id;
				$this->aResults[$i]['details'] = $titles;
				$this->aResults[$i]['commission'] = $commission;
				$this->aResults[$i]['ordervalue'] = $orderAmount;
				$this->aResults[$i]['cwhen'] = $row->click_date;
				$this->aResults[$i]['reference_id'] = 'ADM'.$ID;
				$this->aResults[$i]['eventName']  = $event;
				$this->aResults[$i]['user_id']  =  $user_id;
				$this->aResults[$i]['retailer_id']  = $retailer_id;
				$this->aResults[$i]['program_id']  = 'ADM'.$row->id;
				$this->aResults[$i]['cashback']  = round($cashback ,2);
				$this->aResults[$i]['this_time_cashback']  = $this_time_cashback;
				$this->aResults[$i]['payment_type']  = 'Cashback '.$titles.' '. $p_time. ' (' . $user_id . ')';
				$this->aResults[$i]['status'] = 'pending';
				$this->aResults[$i]['network_currency'] = $row->currency;
				 

				//$this->aResults[$i]['get_status'] = (string)$row->status;


				$i++;
				
			}
		}
		// echo '<pre>';
		// print_r($this->aResults);
		// echo("</pre>");
		// die();
		return $this->aResults;
	}
	
}

?> 