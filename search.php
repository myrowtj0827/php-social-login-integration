<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
  * Copyright (c) 2010-2017 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");


	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0 && in_array($_GET['show'], $results_on_page))
	{
		$results_per_page = (int)$_GET['show'];
		if (!(isset($_GET['go']) && $_GET['go'] == 1))$page = 1;
	}
	else
	{
		$results_per_page = RESULTS_PER_PAGE;
	}

	$cc = 0;

	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "title": $rrorder = "title"; break;
				case "added": $rrorder = "added"; break;
				case "visits": $rrorder = "visits"; break;
				case "cashback": $rrorder = "cashback"; break;
				default: $rrorder = "title"; break;
			}
		}
		else
		{
			$rrorder = "title";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "asc"; break;
			}
		}
		else
		{
			$rorder = "asc";
		}
	//////////////////////////////////////////////////

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$where = "";

	if (isset($_GET['action']) && $_GET['action'] == "search")
	{
		$stext = mysqli_real_escape_string($conn, getGetParameter('searchtext'));
		$stext = substr(trim($stext), 0, 100);

		// country filter //
		if (isset($_GET['country']) && is_numeric($_GET['country']) && $_GET['country'] > 0)
		{
			$country_id = (int)$_GET['country'];

			unset($retailers_per_country);
			$retailers_per_country = array();
			$retailers_per_country[] = "111111111111111111111";

			$sql_retailers_per_country = smart_mysql_query("SELECT retailer_id FROM cashbackengine_retailer_to_country WHERE country_id='$country_id'");
			while ($row_retailers_per_country = mysqli_fetch_array($sql_retailers_per_country))
			{
				$retailers_per_country[] = $row_retailers_per_country['retailer_id'];
			}

			$where .= "retailer_id IN (".implode(",",$retailers_per_country).") AND";
		}

		$where .= " (title LIKE '%".$stext."%' OR description LIKE '%".$stext."%' OR website LIKE '%".$stext."%' OR tags LIKE '%".$stext."%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'";

		if ($rrorder == "cashback")
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY ABS(cashback) $rorder LIMIT $from, $results_per_page";
		else
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY featured DESC, $rrorder $rorder LIMIT $from, $results_per_page";

		$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY title ASC");
		$total = mysqli_num_rows($total_result);

		$result = smart_mysql_query($query);
		$total_on_page = mysqli_num_rows($result);
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_SEARCH_TITLE." ".$stext;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_SEARCH_TITLE; ?> '<?php echo $stext; ?>'</h1>

	<div class="search_box form-inline">
	<form action="" method="get">
		<b><?php echo CBE1_SEARCH_TITLE2; ?></b>: 
		<input type="text" name="searchtext" class="form-control" value="<?php echo $stext; ?>" size="40">
		<select name="country" class="form-control" id="country">
		<option value=""><?php echo CBE1_LABEL_COUNTRY_SELECT; ?></option>
		<?php
			$sql_country = "SELECT * FROM cashbackengine_countries WHERE status='active' ORDER BY sort_order, name";
			$rs_country = smart_mysql_query($sql_country);
			$total_country = mysqli_num_rows($rs_country);

			if ($total_country > 0)
			{
				while ($row_country = mysqli_fetch_array($rs_country))
				{
					if ($country_id == $row_country['country_id'])
						echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
					else
						echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
				}
			}
		?>
		</select>
		<input type="hidden" name="action" value="search" />
		<input type="submit" class="submit" value="<?php echo CBE1_SEARCH_BUTTON; ?>" />
	</form>
	</div>

	<?php

		if ($total > 0) {
	?>

	<?php if (!isLoggedIn()) { ?><div class="login_msg"><?php echo CBE1_STORES_LOGIN; ?></div><?php } ?>
	

		<div class="container">
		<div class="row browse_top">
		<div class="col-md-6">
			<div class="sortby">
				<form action="" id="form1" name="form1" method="get" class="form-inline">
					<span><?php echo CBE1_SORT_BY; ?>:</span>
					<select name="column" id="column" class="form-control" onChange="document.form1.submit()">
						<option value="title" <?php if ($_GET['column'] == "title") echo "selected"; ?>><?php echo CBE1_SORT_NAME; ?></option>
						<option value="visits" <?php if ($_GET['column'] == "visits") echo "selected"; ?>><?php echo CBE1_SORT_POPULARITY; ?></option>
						<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>><?php echo CBE1_SORT_DATE; ?></option>
						<option value="cashback" <?php if ($_GET['column'] == "cashback") echo "selected"; ?>><?php echo CBE1_SORT_CASHBACK; ?></option>
					</select>
					<select name="order" id="order" class="form-control" onChange="document.form1.submit()">
						<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
						<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
					</select>
					<?php if ($cat_id) { ?><input type="hidden" name="cat" value="<?php echo $cat_id; ?>" /><?php } ?>
					<?php if ($ltr) { ?><input type="hidden" name="letter" value="<?php echo $ltr; ?>" /><?php } ?>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="view" value="<?php echo $view; ?>" />
					<span><?php echo CBE1_RESULTS; ?>:</span>
					<select name="show" id="show" class="form-control" onChange="document.form1.submit()">
						<option value="5" <?php if ($results_per_page == "5") echo "selected"; ?>>5</option>
						<option value="10" <?php if ($results_per_page == "10") echo "selected"; ?>>10</option>
						<option value="25" <?php if ($results_per_page == "25") echo "selected"; ?>>25</option>
						<option value="50" <?php if ($results_per_page == "50") echo "selected"; ?>>50</option>
						<option value="100" <?php if ($results_per_page == "100") echo "selected"; ?>>100</option>
						<option value="111111" <?php if ($results_per_page == "111111") echo "selected"; ?>><?php echo CBE1_RESULTS_ALL; ?></option>
					</select>
				<input type="hidden" name="searchtext" value="<?php echo $stext; ?>" />
				<input type="hidden" name="page" value="<?php echo $page; ?>" />
				<input type="hidden" name="action" value="search" />					
				</div>
				</form>		
			</div>	
		<div class="col-md-6">
			<div class="results text-right">
				<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total; ?>
			</div>
		</div>
		</div>
		</div>
	

			<div class="row">
			<?php while ($row = mysqli_fetch_array($result)) { $cc++; ?>
				<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 text-center">
					<div class="item_store">
						 <a class="coupons pull-right" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons" title="<?php echo $row['title']; ?> <?php echo CBE1_COUPONS_TITLE; ?>"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a>
						<div style="clear: both"></div>
						<a class="stitle hidden-xs" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><h3 style="margin:0;"><?php echo $row['title']; ?></h3></a>
						<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"><img src="<?php echo SITE_URL; ?>images/featured.png"></span><?php } ?>
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0"></div>
						</a>
						<!--<br/><?php //echo GetStoreRating($row['retailer_id'], $show_start = 1); ?><br/>-->
						<?php if ($row['cashback'] != "") { ?>
							<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
							<span class="cashback"><span class="value"><?php echo DisplayCashback($row['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?></span><br/>
						<?php } ?>
						<?php
							$share_title = urlencode($row['title']." ".CBE1_STORE_EARN." ".DisplayCashback($row['cashback'])." ".CBE1_CASHBACK2);
							if (isLoggedIn()) $share_add .= "&ref=".(int)$_SESSION['userid'];
							$share_link = urlencode(GetRetailerLink($row['retailer_id'], $row['title']).$share_add);
						?>
						<a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $share_link; ?>&t=<?php echo $share_title; ?>" target="_blank" class="cashbackengine_tooltip" title="<?php echo CBE1_SHARE_FACEBOOK; ?>" rel="nofollow"><img src="<?php echo SITE_URL; ?>images/icon_facebook.png"></a>&nbsp;
						<a href="https://twitter.com/intent/tweet?text=<?php echo $share_title; ?>&url=<?php echo $share_link; ?>&via=<?php echo SITE_TITLE; ?>" target="_blank" class="cashbackengine_tooltip" title="<?php echo CBE1_SHARE_TWITTER; ?>" rel="nofollow"><img src="<?php echo SITE_URL; ?>images/icon_twitter.png"></a>&nbsp;
						<a class="favorites cashbackengine_tooltip" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>" title="<?php echo CBE1_ADD_FAVORITES; ?>">&nbsp;</a>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a>
					</div>
				</div>
			<?php } ?>
			</div>

			<?php
					$params = "";
					if (isset($_GET['order'])) { $params .= "column=$rrorder&order=$rorder&"; }
					if (isset($_GET['show'])) { $params .= "show=$results_per_page&go=1&"; }
			
					echo ShowPagination("retailers",$results_per_page,"search.php?action=search&searchtext=$stext&".$params, "WHERE $where");
			?>

	<?php }else{ ?>
		
		<div class="alert alert-info text-center"><?php echo CBE1_SEARCH_NO; ?></div>
		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a></p>

	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>