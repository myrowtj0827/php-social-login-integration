<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2017 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");
	require_once("./inc/ce.inc.php");

	$today = date("Y-m-d");
	$yesterday = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

	$clicks_today = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date(added)='$today'"));
	$clicks_today = $clicks_today['total'];

	$clicks_yesterday = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date(added)='$yesterday'"));
	$clicks_yesterday = $clicks_yesterday['total'];

	$clicks_7days = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date_sub(curdate(), interval 7 day) <= added"));
	$clicks_7days = $clicks_7days['total'];

	$clicks_30days = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date_sub(curdate(), interval 30 day) <= added"));
	$clicks_30days = $clicks_30days['total'];

	$users_yesterday = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date(created)='$yesterday'"));
	$users_yesterday = $users_yesterday['total'];

	$users_today = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date(created)='$today'"));
	$users_today = $users_today['total'];

	$users_7days = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date_sub(curdate(), interval 7 day) <= created"));
	$users_7days = $users_7days['total'];

	$users_30days = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date_sub(curdate(), interval 30 day) <= created"));
	$users_30days = $users_30days['total'];

	$all_users = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users"));
	$all_users = $all_users['total'];

	$all_retailers = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_retailers"));
	$all_retailers = $all_retailers['total'];

	$all_coupons = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons"));
	$all_coupons = $all_coupons['total'];

	$all_reviews = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reviews"));
	$all_reviews = $all_reviews['total'];

	$retailers_today = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_retailers WHERE date(added)='$today'"));
	$retailers_today = $retailers_today['total'];

	$coupons_today = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE date(added)='$today'"));
	$coupons_today = $coupons_today['total'];

	$reviews_today = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reviews WHERE date(added)='$today'"));
	$reviews_today = $reviews_today['total'];

	$title = "Admin Dashboard";
	require_once ("inc/header.inc.php");

?>
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<h2>Admin Dashboard</h2>

	<?php if (file_exists("../install.php")) { ?>
		<div class="alert alert-danger alert-dismissible">You must now delete "install.php" from your server. Failing to delete these files is a serious security risk!</div>
	<?php } ?>

	 <table id="Cashback_Engine_info" align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
	 <tr>
		<td class="col-md-6">

			<table id="Cashback_Engine_info" align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2"><font color="#84C315"><b>Cashback</b></font><font color="#5392D5"><b>Engine</b></font> version:</td>
				<td align="right" valign="middle"><?php echo $cashbackengine_version; ?></td>
			</tr>			
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">License Key:</td>
				<td align="right" valign="middle"><?php echo GetSetting('license'); ?></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Last Login:</td>
				<td align="right" valign="middle"><?php $last_login = strtotime(GetSetting('last_admin_login')); echo date("d M Y h:i A", $last_login); ?></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Server Time:</td>
				<td align="right" valign="middle"><?php echo date('d M Y h:i A'); ?></td>
			</tr>	
			<tr>
				<td colspan="2"><div class="sline"></div></td>
			</tr>
			</table>

		</td>
		<td class="col-md-3">

			<table id="CashbackEngine_stats2" align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Clicks Today:</td>
				<td width="20%" align="right" valign="middle" class="stat_s"><a href="clicks.php?date=today"><span style="color: #2F97EB"><?php echo ($clicks_today > 0) ? "+".$clicks_today : "0"; ?></span></a></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Clicks Yesterday:</td>
				<td align="right" valign="middle" class="stat_s"><a href="clicks.php?date=yesterday"><?php echo $clicks_yesterday; ?></a></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Last 7 Days Clicks:</td>
				<td align="right" valign="middle" class="stat_s"><a href="clicks.php?date=7days"><?php echo $clicks_7days; ?></a></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Last 30 Days Clicks:</td>
				<td align="right" valign="middle" class="stat_s"><a href="clicks.php?date=30days"><?php echo $clicks_30days; ?></a></td>
			</tr>
			<tr>
				<td colspan="2"><div class="sline"></div></td>
			</tr>
			</table>

		</td>
		<td class="col-md-3">

			<table id="CashbackEngine_stats3" align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Users Today:</td>
				<td width="20%" align="right" valign="middle" class="stat_s"><a href="users.php"><span style="color: #2F97EB"><?php echo ($users_today > 0) ? "+".$users_today : "0"; ?></span></a></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Users Yesterday:</td>
				<td align="right" valign="middle" class="stat_s"><?php echo $users_yesterday; ?></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Last 7 Days Users:</td>
				<td align="right" valign="middle" class="stat_s"><?php echo $users_7days; ?></td>
			</tr>
			<tr class="wrow">
				<td align="left" valign="middle" class="tb2">Last 30 Days Users:</td>
				<td align="right" valign="middle" class="stat_s"><?php echo $users_30days; ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="sline"></div></td>
			</tr>
			</table>

		</td>
	 </tr>
	 </table>
	

	<h3>All Time Stats</h3>
	
	<table style="background: #F9F9F9;" align="center" width="100%" border="0" cellpadding="5" cellspacing="5">
	<tr height="60">
		<td width="18%" align="center" valign="top">
			<span class="stats_total"><?php echo $all_users; ?></span><br/>
			<?php echo ($all_users == 1) ? "member" : "members"; ?>
			<?php if ($users_today > 0) { ?><p><span class="todays_total">+<?php echo $users_today; ?> today</span></p><?php } ?>
		</td>
		<td width="18%" align="center" valign="top">
			<span class="stats_total"><?php echo $all_retailers; ?></span><br/>
			<?php echo ($all_retailers == 1) ? "store" : "stores"; ?>
			<?php if ($retailers_today > 0) { ?><p><span class="todays_total">+<?php echo $retailers_today; ?> today</span></p><?php } ?>
		</td>
		<td width="18%" align="center" valign="top">
			<span class="stats_total"><?php echo $all_coupons; ?></span><br/>
			<?php echo ($all_coupons == 1) ? "coupon" : "coupons"; ?>
			<?php if ($coupons_today > 0) { ?><p><span class="todays_total">+<?php echo $coupons_today; ?> today</span></p><?php } ?>
		</td>
		<td width="18%" align="center" valign="top">
			<span class="stats_total"><?php echo $all_reviews; ?></span><br/>
			<?php echo ($all_reviews == 1) ? "review" : "reviews"; ?>
			<?php if ($reviews_today > 0) { ?><p><span class="todays_total">+<?php echo $reviews_today; ?> today</span></p><?php } ?>
		</td>
		<td width="28%" align="center" valign="top">
			<span class="stats_total" style="color: #8CD706;"><?php echo GetCashbackTotal(); ?></span><br/> cashback
		</td>
	 </tr>
	 </table>

	 <br/>

	<?php
			if (isset($_GET['stats_period']) && is_numeric($_GET['stats_period']) && $_GET['stats_period'] > 0)
				$stats_period = (int)$_GET['stats_period'];
			else
				$stats_period = 30;
	?>

	<h3 style="float: left">Stores Visits Stats</h3>
	<div style="float: right; padding-top: 20px">
	<form id="form5" name="form5" method="get" action="">
	Period: <select name="stats_period" onChange="document.form5.submit()" class="selectpicker">
		<option value="7" <?php if (@$stats_period == 7) echo "selected='selected'"; ?>>last 7 days</option>
		<option value="30" <?php if (@$stats_period == 30) echo "selected='selected'"; ?>>last 30 days</option>
		<option value="90" <?php if (@$stats_period == 90) echo "selected='selected'"; ?>>last 90 days</option>
		<option value="180" <?php if (@$stats_period == 180) echo "selected='selected'"; ?>>last 180 days</option>
	</select>
	</form>
	</div>
	<div style="clear: both"></div>
	<div id="statstchart" style="height: 270px;"></div>
	<br/>
	<script src="js/raphael-min.js" language="javascript"></script>
	<script src="js/morris.min.js" language="javascript"></script>

	<?php
			for ($i=0; $i<=$stats_period; $i++)
			{
				$d = date("d M", strtotime('-'. $i .' days'));
				$days[$d] = 0;			
			}

			$chart_data = array_reverse($days);

			$chart_result = smart_mysql_query("SELECT DATE_FORMAT(added, '%d %b') as click_date, COUNT(*) as clicks FROM cashbackengine_clickhistory WHERE added > added - ".$stats_period." GROUP BY YEAR(added), MONTH(added), DAY(added)");
			
			if (mysqli_num_rows($chart_result) > 0)
			{
				while ($chart_row = mysqli_fetch_array($chart_result))
				{
					if (array_key_exists($chart_row['click_date'], $chart_data))
						$chart_data[$chart_row['click_date']] = $chart_row['clicks'];
				}
			}
	?>

	<script language="javascript" type="text/javascript">
	new Morris.Bar({
	  element: 'statstchart',
	  data: [ <?php foreach ($chart_data as $k => $v) { echo "{ click_date: '$k', value: $v },"; } ?> ],
	  //{ y: '2017', a: 100, b: 90 },
	  xkey: 'click_date',
	  ykeys: ['value'],
	  labels: ['Stores Visits'],
	  barColors: ['#799cd8'],
	  barRatio: 0.4,
	  hideHover: 'auto'
	});
	</script>


	<?php
			if (isset($_GET['stats_period2']) && is_numeric($_GET['stats_period2']) && $_GET['stats_period2'] > 0)
				$stats_period2 = (int)$_GET['stats_period2'];
			else
				$stats_period2 = 22222;
	?>
	<h3 style="float: left"><?php echo (@$_GET['show_for'] == "signups") ? "Sign Ups" : "Cashback"; ?> Stats</h3>
	<div style="float: right; padding-top: 20px">
	<form id="form6" name="form6" method="get" action="#cstats">
	Show for: <select name="show_for" onChange="document.form6.submit()" class="selectpicker">
		<option value="cashback" <?php if (@$_GET['show_for'] == "cashback") echo "selected='selected'"; ?>>cashback</option>
		<option value="signups" <?php if (@$_GET['show_for'] == "signups") echo "selected='selected'"; ?>>sign ups</option>
	</select>
	&nbsp;&nbsp;
	Period: <select name="stats_period2" onChange="document.form6.submit()" class="selectpicker">
		<option value="7" <?php if (@$stats_period2 == 7) echo "selected='selected'"; ?>>last 7 days</option>
		<option value="30" <?php if (@$stats_period2 == 30) echo "selected='selected'"; ?>>last 30 days</option>
		<option value="90" <?php if (@$stats_period2 == 90) echo "selected='selected'"; ?>>last 90 days</option>
		<option value="180" <?php if (@$stats_period2 == 180) echo "selected='selected'"; ?>>last 180 days</option>
		<option value="22222" <?php if (@$stats_period2 == 22222) echo "selected='selected'"; ?>>past months</option>
		<option value="11111" <?php if (@$stats_period2 == 11111) echo "selected='selected'"; ?>>past years</option>
	</select>
	</form>
	</div>
	<div style="clear: both"></div>
	<div id="statstchart2" style="height: 270px;"></div>
	<a name="cstats"></a>
	<br/>
	<?php
			unset($chart_data2);

			if ($stats_period2 == 11111)
			{
				for ($i=0; $i<=7; $i++)
				{
					$d = date("Y", strtotime('-'. $i .' year'));
					$years[$d] = 0;			
				}

				$eee = "YEAR(created)";
				$www = "%Y";
				$vvv = "";

				$chart_data2 = $years;
			}
			elseif ($stats_period2 == 22222)
			{
				for ($i=0; $i<=12; $i++)
				{
					$d = date("M Y", strtotime('-'. $i .' month'));
					$months[$d] = 0;			
				}

				$eee = "YEAR(created), MONTH(created)";
				$www = "%b %Y";
				$vvv = "";

				$chart_data2 = array_reverse($months);
			}
			else
			{
				for ($i=0; $i<=$stats_period2; $i++)
				{
					$d = date("d M", strtotime('-'. $i .' days'));
					$days[$d] = 0;			
				}
				
				$eee = "YEAR(created), MONTH(created), DAY(created)";
				$www = "%d %b";
				$vvv = "AND created > created - ".$stats_period2."";

				$chart_data2 = array_reverse($days);
			}

			if (isset($_GET['show_for']) && $_GET['show_for'] == "signups")
				$chart_result2 = smart_mysql_query("SELECT DATE_FORMAT(created, '".$www."') as stats_date, COUNT(*) as stats_amount FROM cashbackengine_users WHERE 1=1 ".$vvv." GROUP BY ".$eee);
			else
				$chart_result2 = smart_mysql_query("SELECT DATE_FORMAT(created, '".$www."') as stats_date, SUM(amount) as stats_amount FROM cashbackengine_transactions WHERE payment_type='cashback' AND status='confirmed' ".$vvv." GROUP BY ".$eee);
			
			if (mysqli_num_rows($chart_result2) > 0)
			{
				while ($chart_row2 = mysqli_fetch_array($chart_result2))
				{
					if (array_key_exists($chart_row2['stats_date'], $chart_data2))
						$chart_data2[$chart_row2['stats_date']] = $chart_row2['stats_amount'];
				}
			}
	?>

	<script language="javascript" type="text/javascript">
	new Morris.Bar({
	  element: 'statstchart2',
	  data: [ <?php foreach ($chart_data2 as $k => $v) { echo "{ statsdate: '$k', value: $v },"; } ?> ],
	  xkey: 'statsdate',
	  ykeys: ['value'],
	<?php if (isset($_GET['show_for']) && $_GET['show_for'] == "signups") { ?>
	  labels: ['Sign Ups'],
	  barColors: ['#999999'],
	<?php }else{ ?>
	  labels: ['Cashback <?php echo SITE_CURRENCY; ?>'],
	  barColors: ['#8ec120'],
	<?php } ?>
	  barRatio: 0.4,
	  hideHover: 'auto'
	});
	</script>

	<br/>
	<table style="background: #F9F9F9;" align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
	<tr>
		<td width="33%" align="center" valign="top">
			<h3 class="text-center">Most Visited Stores</h3>
			<?php

					$tops_query = "SELECT * FROM cashbackengine_retailers WHERE visits>0 ORDER BY visits DESC LIMIT 10";
					$tops_result = smart_mysql_query($tops_query);
					$tops_total = mysqli_num_rows($tops_result);

					if ($tops_total > 0)
					{
			?>
					<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
					<?php while ($tops_row = mysqli_fetch_array($tops_result)) { ?>
					<tr>
						<td width="75%" align="left" valign="middle"><a style="color: #777" href="retailer_details.php?id=<?php echo $tops_row['retailer_id']; ?>"><?php echo $tops_row['title']; ?></a></td>
						<td width="5%" align="left" valign="middle">&nbsp;</td>
						<td width="20%" align="center" valign="middle" class="stat_s" style="background: #EEE; color: #333"><?php echo number_format($tops_row['visits']); ?></td>
					</tr>
					<?php } ?>
					</table>
			<?php }else{ ?>
				<p class="text-center" style="color: #999">- no data at this time - </p>
			<?php } ?>
		</td>		
		<td width="33%" align="center" valign="top">
			<h3 class="text-center">Sign Up Sources</h3>
			<?php
					$tops_query = "SELECT COUNT(*) as total_users, auth_provider FROM cashbackengine_users WHERE auth_provider!='' GROUP BY auth_provider ORDER BY total_users DESC LIMIT 10";
					$tops_result = smart_mysql_query($tops_query);
					$tops_total = mysqli_num_rows($tops_result);

					$tops_query = "SELECT COUNT(*) as total_users, reg_source FROM cashbackengine_users WHERE reg_source!='' GROUP BY reg_source ORDER BY total_users DESC LIMIT 10";
					$tops_result = smart_mysql_query($tops_query);
					$tops_total = mysqli_num_rows($tops_result);

					if ($tops_total == 0 && $tops_total == 0) echo "<p align='center' style='color: #999'>- no data at this time - </p>";

					if ($tops_total > 0)
					{
			?>
					<p class="text-center"><b>How did you hear about us</b></p>
					<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
					<?php while ($tops_row = mysqli_fetch_array($tops_result)) { ?>
					<tr>
						<td width="65%" align="left" valign="middle"><?php echo $tops_row['reg_source']; ?></td>
						<td width="5%" align="left" valign="middle">&nbsp;</td>
						<td width="20%" align="center" valign="middle" class="stat_s"><?php echo number_format($tops_row['total_users']); ?></td>
						<td width="10%" align="center" valign="middle"><?php echo ($tops_row['total_users'] == 1) ? "user" : "users"; ?></td>
					</tr>
					<?php } ?>
					</table>
			<?php } ?>
			<?php
					if ($tops_total > 0)
					{
			?>
					<p class="text-center"><b>Other Signups</b></p>
					<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
					<?php while ($tops_row = mysqli_fetch_array($tops_result)) { ?>
					<tr>
						<td width="65%" align="left" valign="middle"><?php echo $tops_row['auth_provider']; ?></td>
						<td width="5%" align="left" valign="middle">&nbsp;</td>
						<td width="20%" align="center" valign="middle" class="stat_s"><?php echo number_format($tops_row['total_users']); ?></td>
						<td width="10%" align="center" valign="middle"><?php echo ($tops_row['total_users'] == 1) ? "user" : "users"; ?></td>
					</tr>
					<?php } ?>
					</table>
			<?php } ?>
		</td>
		<td width="33%" align="center" valign="top">
			<h3 class="text-center">Top Referrers</h3>
			<?php

					$topr_query = "SELECT *, COUNT(*) AS total_referred FROM cashbackengine_users WHERE ref_id>0 GROUP BY ref_id ORDER BY total_referred DESC LIMIT 10";
					$topr_result = smart_mysql_query($topr_query);
					$topr_total = mysqli_num_rows($topr_result);

					if ($topr_total > 0)
					{
			?>
					<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
					<?php while ($topr_row = mysqli_fetch_array($topr_result)) { ?>
					<tr>
						<td width="75%" align="left" valign="middle"><a style="color: #777" class="user" href="user_details.php?id=<?php echo $topr_row['ref_id']; ?>"><?php echo Getusername($topr_row['ref_id']); ?></a></td>
						<td width="5%" align="left" valign="middle">&nbsp;</td>
						<td width="20%" align="center" valign="middle" class="stat_s"><?php echo number_format($topr_row['total_referred']); ?></td>
					</tr>
					<?php } ?>
					</table>
			<?php }else{ ?>
				<p align="center" style="color: #999">- no data at this time - </p>
			<?php } ?>
		</td>
	</tr>
	</table>


<?php require_once ("inc/footer.inc.php"); ?>