<?php
class Awin {
	private $startDate;
	private $endDate;
	private $Awin_key;
	private $xmlPass;
	private $siteId;
	private $oXML;
	private $retMessage;
	private $aResults;
	
	public function __construct($Awin_key, $startDate = '', $endDate = '')
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
		$this->Awin_key    	= $Awin_key;
		$this->Awin_key = $Awin_key;
	}

	
	public function loadFeedXML()
	{



 echo	  $feedURL = "https://api.awin.com/publishers/728469/transactions/?startDate=".$this->startDate ."T00%3A00%3A00&endDate=".$this->endDate ."T23%3A59%3A59&timezone=UTC&accessToken=".$this->Awin_key;

	   


		$this->oXML = json_decode(file_get_contents($feedURL)); 
        
	   // echo '<pre>';print_r($this->oXML);exit;
         
         $filename = '...';


 // function getXMLnode($object, $param) {
 //        foreach($object as $key => $value) {

 //        	print_r($value);exit();
 //        }
 //        if($ret) return (string) $ret;
 //        return false;
 //    }


 //    $result = getXMLnode($this->oXML, 'DirectTrackId');
 //    echo $result;
	//	exit;
		//$this->oXML = $this->oXML->matrix->rows->row;
		// echo '<pre>';
		// print_r($this->oXML);
		// die();
		
		if(sizeof($this->oXML) < 1)
			return false;
		else
			return true;

	}

	

	public function retResultsArray()
	{
		$arrStatus = array('P' => 'Pending', 'D' => 'Denied', 'A' => 'Approved');

		 
 


		   
      // echo "<pre>";
      // print_r($this->oXML);
       $i = 0;
		foreach($this->oXML as $oTransaction[$i]) 
		{ 
			// echo "<pre>";
			// print_r($oTransaction[$i]);
		//echo $oTransaction[$i]->saleAmount->amount;exit; 

 		   if($oTransaction[$i]->saleAmount->amount > 0)
		   {
            
		   		$statuss = 'pending';

		   	  
			$commission = floatval(preg_replace('~[^0-9.,]~', null, $oTransaction[$i]->commissionAmount->amount));
			$commission = round($commission, 2);
			$orderAmount = floatval(preg_replace('~[^0-9.,]~', null, $oTransaction[$i]->saleAmount->amount)); 
			$orderAmount = round($orderAmount, 2);


			if( (string)$oTransaction[$i]->clickRefs->clickRef !='' ){
				$click_id = (string)$oTransaction[$i]->clickRefs->clickRef;
			}else{
				$click_id = (string)$oTransaction[$i]->clickRefs->clickRef2;
			}
			
			$ID = trim((string) $oTransaction[$i]->id);
			if($ID == '')
				$ID = (string) $oTransaction[$i]->id;

			$user_id     = '';
			$retailer_id = '';
			$cashback    = 0;
			$titles      = '';
			$vat_value  = 1;
		//	echo $oTransaction[$i]->Title;
			$objCallback = $GLOBALS['DB']->queryUniqueObject("select * from cashbackengine_clickhistory where click_id = '".$click_id."'");
			if($objCallback)
			{ 
//print_r($objCallback);
             $user_id = $objCallback->user_id;
             $retailer_id = $objCallback->retailer_id;
			 $added       = $oTransaction[$i]->transactionDate;

                 $objCashback = $GLOBALS['DB']->queryUniqueObject("select cashback,title,network_id from cashbackengine_retailers where retailer_id = '".$retailer_id."'");
                 if($objCashback){
//print_r($objCashback);
                 	$cashback = $objCashback->cashback;
                 	$this_time_cashback = $objCashback->cashback;
                 	 
                 	$network_id= $objCashback->network_id;

                 	$event     = $oTransaction[$i]->transactionParts[0]->commissionGroupName;


               echo  	$get_amount     = $oTransaction[$i]->transactionParts[0]->amount;
               echo  	$get_comission  = $oTransaction[$i]->transactionParts[0]->commissionAmount;

                  
                 	//print_r($event);

               		// exit;
                 	
                 	$titles    = $objCashback->title;

                 	 
 

                 	 
                 }

			}
			//echo 33;
			$status = 'Pending';//(string)$oTransaction[$i]->status;
 			$arrStatus = array('Pending' => 'pending', 'Failed' => 'failed', 'Approved' => 'confirmed', 'Denied' => 'declined');

			$updated_status = $arrStatus[(string)$status];

            $p_time = date('Y-m-d' , strtotime($added));

            if((string)$oTransaction[$i]->commissionStatus == 'reject' || (string)$oTransaction[$i]->commissionStatus == 'failed' || (string)$oTransaction[$i]->commissionStatus == 'declined' || (string)$oTransaction[$i]->commissionStatus == 'deleted'){
            	$statuss = 'declined';
            	
            }elseif($oTransaction[$i]->paidToPublisher == 1){
            	$statuss = 'confirmed';
            }
   			
   			if((string)$oTransaction[$i]->commissionStatus == 'deleted'){continue;}
            
			
			  
			    $this->aResults[$i]['clickref'] = $click_id;
				$this->aResults[$i]['network'] = 'Awin';
				$this->aResults[$i]['network_id'] = $network_id;
				$this->aResults[$i]['details'] = $titles;
				$this->aResults[$i]['commission'] = $commission;
				$this->aResults[$i]['ordervalue'] = $orderAmount;
				$this->aResults[$i]['cwhen'] = substr((string)$added, 0, 19);
				$this->aResults[$i]['reference_id'] = 'AWN'.$ID;
				$this->aResults[$i]['eventName']  = $event;
				$this->aResults[$i]['user_id']  =  $user_id;
				$this->aResults[$i]['retailer_id']  = $retailer_id;
				$this->aResults[$i]['program_id']  = 'AWN'.$ID;
				$this->aResults[$i]['cashback']  = round($cashback ,2);
				$this->aResults[$i]['this_time_cashback']  = $this_time_cashback;
				$this->aResults[$i]['payment_type']  = 'Cashback '.$titles.' '. $p_time. ' (' . $user_id . ')';
				$this->aResults[$i]['status'] = $statuss;
				$this->aResults[$i]['network_currency'] = $oTransaction[$i]->saleAmount->currency;
				//$this->aResults[$i]['get_percentage'] = $get_percentage ;

				//$this->aResults[$i]['get_status'] = (string)$oTransaction[$i]->commissionStatus;

		//echo '<pre>';print_r($this->aResults);echo '</pre>';die();

     //                $objEmailTemplate = $GLOBALS['DB']->queryUniqueObject("select * from cashbackengine_email_templates where template_id = 7");
				 //    if ($objEmailTemplate){
					
					 
					 
					// $subject =  $objEmailTemplate->email_subject;
					// $html_body = str_replace('{store}', (string)$oTransaction[$i]->programName, $objEmailTemplate->email_message);
					// $text_body = str_replace('{cashback_amount}', $cashback, $html_body);

					 
					// $to = "a.umair55@gmail.com";
					
					
					// $headers = "From: " . 'a.umair55@gmail.com' . "\r\n";
					// $headers .= "Reply-To: ". 'a.umair55@gmail.com' . "\r\n";
					// $headers .= "CC: a.umair55@gmail.com\r\n";
					// $headers .= "MIME-Version: 1.0\r\n";
					// $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
					
					// mail($to,$subject,$text_body,$headers);
					// //exit;
					// // mail($contact_email,$subject,$text_body);
				 //  }
				$i++;
				 
		 }

                    
		}
		 echo '<pre>';print_r($this->aResults);echo '</pre>';

		 return $this->aResults;
		
	}
}

