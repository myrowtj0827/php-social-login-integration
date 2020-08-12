<?php
interface Import
{
	public function loadFeedXML();
	public function retResultsArray();
}
class InsertData
{
	public function __construct()
	{ 
		
	}
	
	public function enterData($aTransactions)
	{
		$total = 0;
		//if(!sizeof($aTransactions))
			//return 0;
		//echo $exit_click_id = $transaction['clickref'];
		//echo "all is well";exit;
		//exit;
		$objStore = $GLOBALS['DB']->queryobjects("select retailer_id,cashback,title,network_id from cashbackengine_retailers ");
		$stores = array();
		while($store = mysqli_fetch_array($objStore)) {
			$stores[$store['retailer_id']] = $store;
		}


		foreach($aTransactions as $transaction)
		{
			
			
		 //echo "<span style='display:none;";print_r($transaction); echo "</span>";;
			 // $objStore = $GLOBALS['DB']->queryUniqueObject("select cashback,vat_value,title,network_id,related from cashbackengine_retailers where retailer_id = '".$transaction['retailer_id']."'  ");
                
                 if(isset($stores[$transaction['retailer_id']]))
                 {
                 	$objStore = $stores[$transaction['retailer_id']];

                 	 $objStore = (object)$objStore ;


                 	  $objStore->cashback;
                 	  



//print_r($objStore);

				//echo  $status = (int)$transaction['status'];exit;
		  $exit_click_id = $transaction['clickref'];
		  
		  $exit_click = $GLOBALS['DB']->queryUniqueObject("SELECT click_id from cashbackengine_clickhistory where click_id='".$exit_click_id."'");
		  if($exit_click){
			$related = $objStore->related;
		   if($transaction['network'] == 'Tradedoubler' || $transaction['network'] == 'Zanox Network' || $transaction['network'] == 'Tradetracker' || $transaction['network'] =='NetBooster' || $transaction['network'] =='Adtraction' || $transaction['network'] =='Affiliate Window' || $transaction['network'] =='Adservice'|| $transaction['network'] =='Adrecord'|| $transaction['network'] =='Double' || $transaction['network'] =='Bmillions' || $transaction['network'] =='GetCake' || $transaction['network'] =='CJ Network' || $transaction['network'] =='Awin' || $transaction['network'] =='Shareasale' || $transaction['network'] =='Webgains' || $transaction['network'] =='Admitad' || $transaction['network'] =='ArabClicks'|| $transaction['network'] =='Daisycon' || $transaction['network'] =='Vcommission' || $transaction['network'] =='Hareerdeals')
		     {	 //echo $exit_click_id.'teee'; exit;
				 
			 	if($transaction['cashback'] > 0){
		                // echo 'i am herer';exit;
						 $objCallback = $GLOBALS['DB']->queryUniqueObject("select transaction_id,this_time_cashback,transaction_amount,transaction_commision,amount  from cashbackengine_transactions where clickref = '".$exit_click_id."' and program_id = '".$transaction['program_id']."' ");
					//	echo $objCallback;exit;
							if($objCallback)
							{ 
								echo 'up';
							}else{	

						//	echo 'testing gooe';exit;
							$GLOBALS['DB']->query('INSERT INTO cashbackengine_transactions set
							reference_id = "'.$transaction['reference_id'].'",
							fetch_from = "'.$transaction['network'].'",
							network_id = '.$transaction['network_id'].',
							clickref = "'.$transaction['clickref'].'",
							retailer_id = "'.$transaction['retailer_id'].'",
							program_id = "'.$transaction['program_id'].'",
							transaction_amount = "'.$transaction['ordervalue'].'",
							transaction_commision = "'.$transaction['commission'].'",
							amount = "'.$transaction['cashback'].'",
							extra_cashback_percentage = "'.$extra_cashback_percentage.'",
							this_time_cashback = "'.$transaction['this_time_cashback'].'",
							user_id = "'.$transaction['user_id'].'",
							payment_type = "'.$transaction['payment_type'].'",
							status = "'.$transaction['status'].'",
							created = NOW();
							'); 
							}
							$total ++;
					}
						 
				} 
			 
			}
		
			  
			  
		  }
		         }

return $total;
		}
	
	}

?>