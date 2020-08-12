<?php 
header('Content-type: text/html');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
chdir(dirname(__FILE__));
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
//ini_set("error_reporting", E_ALL & ~E_DEPRECATED); 
ini_set('display_errors', 1);

ini_set('max_execution_time', 300); 

//echo phpinfo();exit;
 
/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
include 'conf.php';
include_once dirname(__FILE__).'/classes/tradedoubler.class.php';
include_once dirname(__FILE__).'/classes/zanox.class.php';

include_once dirname(__FILE__).'/classes/network.class.php';
include_once dirname(__FILE__).'/classes/aw_affiliate_client.php';
include_once dirname(__FILE__).'/classes/awin.class.php';

include_once dirname(__FILE__).'/classes/shareasale.class.php';

include_once dirname(__FILE__).'/classes/netbooster.class.php';
include_once dirname(__FILE__).'/classes/adraction.class.php';
include_once dirname(__FILE__).'/classes/tradetracker.class.php';
include_once dirname(__FILE__).'/classes/adservice.class.php';
include_once dirname(__FILE__).'/classes/adrecord.class.php';
include_once dirname(__FILE__).'/classes/double.class.php';

include_once dirname(__FILE__).'/classes/bmillions.class.php';
include_once dirname(__FILE__).'/classes/admitad.class.php';


////world lottery affiliate Empire get cake/////
include_once dirname(__FILE__).'/classes/getcake.class.php';

include_once dirname(__FILE__).'/classes/db.class.php';


$newline = '<br>
';
echo $from_date = date("d-m-Y", time()-86400*(34*1));
$to_date = date("Y-m-d", time());


$to_date_adservice = date("Y-m-d", time()+1*86400);

if(isset($_GET['from_date']) && $_GET['from_date'] != '')
	$from_date = $_GET['from_date'];
if(isset($_GET['to_date']) && $_GET['to_date'] != '')
	$to_date = $_GET['to_date'];
	
echo $newline.'Importing Commission Apis.'.$newline;
importCashback($from_date, $to_date);

function importCashback($from_date, $to_date)
{	
	include 'conf.php';
	global $newline;
	$to_date_adservice = date("Y-m-d", time()+1*86400);
	//echo $to_date;exit;
	$arrNetworkObjects = array(	
		//  'Adrecord' 	    		=> new Adrecord('' , '2017-01-06', $to_date),	 
		// 'TradeDoubler' 			    => new TradeDoubler($TradeDoubler_key, '2016-10-01', $to_date),
	 // 	'NetBooster' 			    => new NetBooster($NetBooster_key, $from_date, $to_date),
	 //    'Adtraction' 			    => new Adtraction($Adtraction_token, '2016-10-01', $to_date),
		// 'TradeTracker' 			    => new TradeTracker($tradetracker_xmlKey, $tradetracker_xmlPass, $tradetracker_siteId, '2016-10-01', $to_date),
	 //    'Zanox' 			        => new Zanox($zanox_connectId, $zanox_secret_key, $startDate = '2016-10-01'),
	 //     'aw_affiliate_client' 	    => new aw_affiliate_client('2016-10-01', $to_date),
  //       'Adservice' 	    		=> new Adservice($adService_PID, $adService_LoginToken, '2016-10-13', $to_date),

///////////All Networks Valid Code///////////////////

	 //   'Adrecord' 	    		=> new Adrecord('' , $from_date, $to_date),	 
		 //      'aw_affiliate_client' 	    => new aw_affiliate_client($from_date, $to_date),
		 //        'Double' 	    			=> new Double($Double_token, $from_date, $to_date),
        
  //      'Getcake' 			        => new Getcake($getcake_uniquekey, $from_date, $to_date),


		
/// TradeDoubler should always start from 23 Janaury 2018 like 2018-01-23

		  // 'TradeDoubler' 			    => new TradeDoubler($TradeDoubler_key, $from_date, $to_date),
	   //    'NetBooster' 			    => new NetBooster($NetBooster_key, $from_date, $to_date),
	   //    'Adtraction' 			    => new Adtraction($Adtraction_token, $from_date, $to_date),
		  // 'TradeTracker' 			    => new TradeTracker($tradetracker_xmlKey, $tradetracker_xmlPass, $tradetracker_siteId, $from_date, $to_date),
	   //    'Adservice' 	    		=> new Adservice($adService_PID, $adService_LoginToken, $from_date, $to_date_adservice),
	   //    'Awin' 			      		    => new Awin($Awin_key, $from_date, $to_date),
		  // 'Bmillions' 			        => new BMILLIONS('', $from_date, $to_date),
		

		  'Admitad' 			        => new Admitad($from_date, $to_date),
		


		 // 'Zanox' 			        => new Zanox($zanox_connectId, $zanox_secret_key, $from_date),

		  


		


	);
	 
	
	foreach($arrNetworkObjects as $name => $oNW)
	{
		echo $newline.$newline.'Running: '.$name.' from: '.$from_date.' to: '.$to_date;
		
		if(!$oNW->loadFeedXML())
		{
			echo $newline. 'No new records found.'.$newline;	
			continue;
		}
		
		$retTransactions = $oNW->retResultsArray();
		
		 echo "<pre>";
		 print_r($retTransactions);
		 echo "</pre>";	
		
	//	 exit;
		 //exit;
		//continue;
		$total_insertions = @InsertData::enterData($retTransactions);
		echo $newline. $total_insertions.' records inserted/updated.'.$newline;
	}
	
	echo $newline. $from_date.' - '. $to_date.' Import finished.';
}
	
?>