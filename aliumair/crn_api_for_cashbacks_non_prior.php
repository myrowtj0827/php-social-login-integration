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


include_once dirname(__FILE__).'/classes/network.class.php';
include_once dirname(__FILE__).'/classes/awin.class.php';
include_once dirname(__FILE__).'/classes/cj.class.php';
include_once dirname(__FILE__).'/classes/db.class.php';
include_once dirname(__FILE__).'/classes/arabclicks.class.php';
include_once dirname(__FILE__).'/classes/vcommission.class.php';
include_once dirname(__FILE__).'/classes/hareerdeals.class.php';


$newline = '<br>
';
echo $from_date = date("Y-m-d", time()-20*86400);
$to_date = date("Y-m-d", time());

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
	//echo $to_date;exit;
	$arrNetworkObjects = array(	
		'CJ35' 					    => new CJ($cj_developerKey, $cj_websiteId, $from_date, $to_date),
		  'Awin' 			      		    => new Awin($Awin_key, $from_date, $to_date),
		  'ArabClicks' 			        => new ArabClicks('', $from_date, $to_date),
		   'Vcommission' 			        => new Vcommission('', $from_date, $to_date),
		    'Hareerdeals' 			        => new Hareerdeals('', $from_date, $to_date),
	);
	 
	
	foreach($arrNetworkObjects as $name => $oNW)
	{
		//echo $newline.$newline.'Running: '.$name.' from: '.$from_date.' to: '.$to_date;
		
		if(!$oNW->loadFeedXML())
		{
			echo $newline. 'No new records found.'.$newline;	
			continue;
		}
		
		$retTransactions = $oNW->retResultsArray();
		// exit;
		//  echo "<pre>";
		//  print_r($retTransactions);
		//  echo "</pre>";	
		
		//  exit;
		 //exit;
		//continue;
		$total_insertions = @InsertData::enterData($retTransactions);
		echo $newline. $total_insertions.' records inserted/updated.'.$newline;
	}
	
	echo $newline. $from_date.' - '. $to_date.' Import finished.';
}
	
?>