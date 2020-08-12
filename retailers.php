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

	$cc = 0;

	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0 && in_array($_GET['show'], $results_on_page))
	{
		$results_per_page = (int)$_GET['show'];
		if (!(isset($_GET['go']) && $_GET['go'] == 1)) $page = 1;
	}
	else
	{
		$results_per_page = RESULTS_PER_PAGE;
	}


	if (isset($_GET['view']) && $_GET['view'] != "")
	{
		switch ($_GET['view'])
		{
			case "full":	$STORES_LIST_STYLE = 1; break;
			case "list":	$STORES_LIST_STYLE = 2; break;
			default:		$STORES_LIST_STYLE = STORES_LIST_STYLE; break;
		}

		$_SESSION['view'] = $STORES_LIST_STYLE;
	}

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

	if (isset($_GET['cat']) && is_numeric($_GET['cat']) && $_GET['cat'] > 0)
	{
		$cat_id = (int)$_GET['cat'];

		$cat_query = "SELECT * FROM cashbackengine_categories WHERE category_id='$cat_id' LIMIT 1";
		$cat_result = smart_mysql_query($cat_query);
		if (mysqli_num_rows($cat_result) > 0)
		{
			$cat_row = mysqli_fetch_array($cat_result);
			$totitle = $cat_row['name'];
		}
		else
		{
			// if category not found //
			$not_found = 1;
			$totitle = CBE1_STORES_CNO;

			header ("Location: retailers.php");
			exit();
		}
		
		unset($retailers_per_category);
		$retailers_per_category = array();
		$retailers_per_category[] = "111111111111111111111";

		$sql_retailers_per_category = smart_mysql_query("SELECT retailer_id FROM cashbackengine_retailer_to_category WHERE category_id='$cat_id'");
		while ($row_retailers_per_category = mysqli_fetch_array($sql_retailers_per_category))
		{
			$retailers_per_category[] = $row_retailers_per_category['retailer_id'];
		}

		$where .= "retailer_id IN (".implode(",",$retailers_per_category).") AND";
	}

	// country filter //
	if (isset($_GET['country']) && is_numeric($_GET['country']) && $_GET['country'] > 0)
	{
			$country_id = (int)$_GET['country'];
			
			$totitle = GetCountry($country_id);

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

	if (isset($_GET['letter']) && in_array($_GET['letter'], $alphabet))
	{
		$ltr = mysqli_real_escape_string($conn, getGetParameter('letter'));
		
		if ($ltr == "0-9")
		{
			$where .= " title REGEXP '^[0-9]' AND";
		}
		else
		{
			$ltr = substr($ltr, 0, 1);
			$where .= " UPPER(title) LIKE '$ltr%' AND";
		}

		$totitle = " $ltr";
	}

	$where .= " (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'";
	
	if ($rrorder == "cashback")
		$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY ABS(cashback) $rorder LIMIT $from, $results_per_page";
	else
		$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY featured DESC, $rrorder $rorder LIMIT $from, $results_per_page";
	
	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY title ASC");
	$total = mysqli_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysqli_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE	= $totitle." ".CBE1_STORES_STORES;
	
	if ($cat_id)
	{
		$PAGE_DESCRIPTION	= $cat_row['meta_description'];
		$PAGE_KEYWORDS		= $cat_row['meta_keywords'];
	}
	else
	{
		$PAGE_DESCRIPTION	= "";
		$PAGE_KEYWORDS		= "";
	}

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo $totitle." ".CBE1_STORES_STORES; ?></h1>

	<div class="breadcrumbs"><a href="<?php echo SITE_URL; ?>" class="home_link"><?php echo CBE1_BREADCRUMBS_HOME; ?></a> &#155; <a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_BREADCRUMBS_STORES; ?></a> <?php echo ($totitle != "") ? "&#155; ".$totitle : ""; ?></div>

	<?php if ($cat_row['description'] != "") { ?>
		<p class="category_description"><?php echo $cat_row['description']; ?></p>
	<?php } ?>
	
	<div class="row">
	<div class="col-sm-9">

	<div id="alphabet" class="hidden-xs">
		<ul>
			<li><a href="<?php echo SITE_URL; ?>retailers.php" <?php if (empty($ltr)) echo 'class="active"'; ?>><?php echo CBE1_STORES_ALL; ?></a></li>
			<?php

				$numLetters = count($alphabet);
				$i = 0;

				foreach ($alphabet as $letter)
				{
					$i++;
					if ($i == $numLetters) $lilast = ' class="last"'; else $lilast = '';
					if (isset($ltr) && $ltr == $letter) $liclass = ' class="active"'; else $liclass = '';
					echo "<li".$lilast."><a href=\"".SITE_URL."retailers.php?".$view_a."letter=$letter\" $liclass>$letter</a></li>";
				}
			?>
		</ul>
	</div>

	<?php

		if ($total > 0) {

	?>
		<?php if (!isLoggedIn()) { ?><div class="login_msg"><?php echo CBE1_STORES_LOGIN; ?></div><?php } ?>

		<?php
			// show random featured retailers //
			$fwhere = $where." AND featured='1'";
			$result_featured = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $fwhere ORDER BY RAND() LIMIT ".FEATURED_STORES_LIMIT);
			$total_fetaured = mysqli_num_rows($result_featured);

			if ($total_fetaured > 0) { 
		?>
			<div class="hidden-xs">
			<h3 class="featured_title"><?php echo $totitle." ".CBE1_STORES_FEATURED; ?></h3>
			<div id="scrollstores">
			<?php while ($row_featured = mysqli_fetch_array($result_featured)) { $cc++; ?>
			<div>
				<div class="imagebox"><a href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>"><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></a></div>
				<?php if ($row_featured['cashback'] != "") { ?><span class="thumbnail-text"><span class="cashback"><?php echo DisplayCashback($row_featured['cashback']); ?></span> <?php echo CBE1_CASHBACK2; ?></span><?php } ?>
			</div>
			<?php } ?>
			</div>
			</div>
			<div style="clear: both"></div>
		<?php } // end featured retailers ?>


		<div class="row browse_top">
		<div class="col-md-8">
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
					<?php if ($country_id) { ?><input type="hidden" name="country" value="<?php echo $country_id; ?>" /><?php } ?>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="view" value="<?php echo $view; ?>" />
					&nbsp;
					<span><?php echo CBE1_RESULTS; ?>:</span>
					<select name="show" id="show" class="form-control" onChange="document.form1.submit()">
						<option value="5" <?php if ($results_per_page == "5") echo "selected"; ?>>5</option>
						<option value="12" <?php if ($results_per_page == "12") echo "selected"; ?>>12</option>
						<option value="24" <?php if ($results_per_page == "24") echo "selected"; ?>>24</option>
						<option value="50" <?php if ($results_per_page == "50") echo "selected"; ?>>50</option>
						<option value="100" <?php if ($results_per_page == "100") echo "selected"; ?>>100</option>
						<option value="111111" <?php if ($results_per_page == "111111") echo "selected"; ?>><?php echo CBE1_RESULTS_ALL; ?></option>
					</select>
				</form>		
			</div>
		</div>
		<div class="col-md-4 text-right">
			<div class="results">
				<a href="?view=full"><img src="<?php echo SITE_URL; ?>images/list2.png" align="absmiddle" /></a>
				<a href="?view=list"><img src="<?php echo SITE_URL; ?>images/list.png" align="absmiddle" /></a>
				&nbsp;&nbsp;&nbsp;
				<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total; ?>
			</div>
		</div>
		</div>


		<?php if (@$_SESSION['view'] == 2) { ?>
		<div class="table-responsive">
		<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
      			<th width="50%" align="left"><?php echo CBE1_STORES_NAME; ?></a></th>
				<th width="20%" align="center"><?php echo CBE1_CASHBACK2; ?></th>
				<th width="10%" align="center"><?php echo CBE1_STORES_COUPONS; ?></th>
				<th width="20%"align="center"><?php echo CBE1_STORES_VISIT; ?></th>
			</tr>
			<?php while ($row = mysqli_fetch_array($result)) { $cc++; ?>				
				<tr class="rets_list <?php if ($row['featured'] == 1) echo "sfeatured"; ?>">
					<td align="left" nowrap="nowrap">
						<a class="fav" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>" title="<?php echo CBE1_ADD_FAVORITES; ?>"></a>
						<a class="retailer_title_s" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
					</td>
					<td align="center"><span class="cashback"><?php echo DisplayCashback($row['cashback']); ?></span></td>
					<td align="center">
					<?php
							$store_coupons_total = GetStoreCouponsTotal($row['retailer_id']);
							echo ($store_coupons_total > 0) ? "<span class='coupons'>".$store_coupons_total."</span>" : "";
					?>
					</td>
					<td align="center"><a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a></td>
				</tr>
			
			<?php } ?>
		</table>
		</div>
		
		<?php }else{ ?>
		
			<div class="row">
			<?php while ($row = mysqli_fetch_array($result)) { $cc++; ?>
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 text-center">
					<div class="item_store">
						 <a class="coupons pull-right" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons" title="<?php echo $row['title']; ?> <?php echo CBE1_COUPONS_TITLE; ?>"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a>
						<a class="stitle hidden-xs" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><h3><?php echo $row['title']; ?></h3></a>
						<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"><img src="<?php echo SITE_URL; ?>images/featured.png"></span><?php } ?>
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0"></div>
						</a>
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
			
			<?php } ?>


			<?php
					$params = "";
					if (isset($cat_id) && $cat_id > 0) { $params = "cat=$cat_id&"; }
					if (isset($ltr) && $ltr != "") { $params .= "letter=$ltr&"; }
					if (isset($country_id) && $country_id > 0) { $params .= "country=$country_id&"; }

					echo ShowPagination("retailers",$results_per_page,"retailers.php?".$params."column=$rrorder&order=$rorder&show=$results_per_page&go=1&","WHERE ".$where);
			?>
			<br/><br/>
			

	<?php }else{ ?>
		<br/><p align="center"><?php echo CBE1_STORES_NO; ?></p>
		<div class="sline"></div>
	<?php } ?>
	
	
			</div>
			<div class="col-sm-3">

				<?php
						$store_of_week = GetStoreofWeek();
						$sow_query = "SELECT * FROM cashbackengine_retailers WHERE (retailer_id='".(int)$store_of_week."' OR deal_of_week='1') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY RAND() LIMIT 1";
						$sow_result = smart_mysql_query($sow_query);
			
						if (mysqli_num_rows($sow_result) > 0)
						{
							$sow_row = mysqli_fetch_array($sow_result);
				?>
					<div class="box">
						<div class="top"><?php echo CBE1_BOX_SOW; ?></div>
						<div class="middle text-center">
		
								<a href="<?php echo GetRetailerLink($sow_row['retailer_id'], $sow_row['title']); ?>"><div class="imagebox"><img src="<?php if (!stristr($sow_row['image'], 'http')) echo SITE_URL."img/"; echo $sow_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" border="0" alt="<?php echo $sow_row['title']; ?>" title="<?php echo $sow_row['title']; ?>"></div></a>
								<?php if ($sow_row['old_cashback'] != "") { ?><span class="oldcash"><?php echo DisplayCashback($sow_row['old_cashback']); ?></span><?php } ?>
								<?php if ($sow_row['cashback'] != "") { ?><span class="ccash"><?php echo DisplayCashback($sow_row['cashback']); ?> <?php echo CBE1_CASHBACK2; ?></span><?php } ?>
		
						</div>
						<div class="bottom">&nbsp;</div>
					</div>
				<?php } ?>


		       <div class="box">
					<div class="top"><?php echo CBE1_LABEL_COUNTRY; ?></div>
					<div class="middle">
						
						<form name="rform2" id="rform2" method="get" action="<?php echo SITE_URL; ?>retailers.php">
						<select class="form-control" name="country" id="country" onChange="document.rform2.submit()">
						<option value=""><?php echo CBE1_LABEL_COUNTRY_SELECT; ?></option>
						<?php
							$sql_country = "SELECT * FROM cashbackengine_countries WHERE status='active' ORDER BY sort_order, name";
							$rs_country = smart_mysql_query($sql_country);
							$total_country = mysqli_num_rows($rs_country);
				
							if ($total_country > 0)
							{
								while ($row_country = mysqli_fetch_array($rs_country))
								{
									if ($_GET['country'] == $row_country['country_id'])
										echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
									else
										echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
								}
							}
						?>					
						</select>
						</form>
						
					</div>
					<div class="bottom">&nbsp;</div>
				</div>				


		       <div class="box">
					<div class="top"><?php echo CBE1_BOX_SBC; ?></div>
					<div class="middle">
						<ul id="categories">
							<li><a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_BOX_ALLSTORES; ?></a> <span class="badge"><?php echo GetStoresTotal(); ?></span></li>
							<?php ShowCategories(0); ?>
						</ul>
					</div>
					<div class="bottom">&nbsp;</div>
				</div>				
				

				<?php if (POPULAR_STORES_LIMIT > 0) { ?>
				<div class="box">
					<div class="top"><?php echo CBE1_BOX_POPULAR; ?></div>
					<div class="middle">
						<?php
		
							$tops_query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id!='".(int)$store_of_week."' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY visits DESC LIMIT ".POPULAR_STORES_LIMIT;
							$tops_result = smart_mysql_query($tops_query);
							$tops_total = mysqli_num_rows($tops_result);
		
							if ($tops_total > 0)
							{
						?>
							<ul id="popular_list">
							<?php while ($tops_row = mysqli_fetch_array($tops_result)) { ?>
								<li><a href="<?php echo GetRetailerLink($tops_row['retailer_id'], $tops_row['title']); ?>"><?php echo $tops_row['title']; ?></a></li>
							<?php } ?>
							</ul>
						<?php } ?>
					</div>
					<div class="bottom">&nbsp;</div>
				</div>
				<?php } ?>


				<?php if (NEW_STORES_LIMIT > 0) { ?>
				<div class="box">
					<div class="top"><?php echo CBE1_BOX_NEW; ?></div>
					<div class="middle">
						<?php
		
							$n_query = "SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY added DESC LIMIT ".NEW_STORES_LIMIT;
							$n_result = smart_mysql_query($n_query);
							$n_total = mysqli_num_rows($n_result);
		
							if ($n_total > 0)
							{
						?>
							<ul id="newest_list">
							<?php while ($n_row = mysqli_fetch_array($n_result)) { ?>
								<li>
									<a href="<?php echo GetRetailerLink($n_row['retailer_id'], $n_row['title']); ?>"><?php echo $n_row['title']; ?></a>
									<?php if ($n_row['cashback'] != "") { ?><br/><span class="newest_cashback"><?php echo DisplayCashback($n_row['cashback']); ?></span> <span class="cashback_label"><?php echo CBE1_CASHBACK2; ?></span><?php } ?>
								</li>
							<?php } ?>
							</ul>
							<div align="right"><a class="more" href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_BOX_NEW_MORE; ?></a></div>
						<?php } ?>
					</div>
					<div class="bottom">&nbsp;</div>
				</div>
				<?php } ?>
				
	
				<?php if (SHOW_FB_LIKEBOX == 1 && FACEBOOK_PAGE != "") { ?>
				<div class="box">
					<iframe src="//www.facebook.com/plugins/likebox.php?href=<?php echo urlencode(FACEBOOK_PAGE); ?>&amp;width=185&amp;height=200&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:185px; height:200px;" allowTransparency="true"></iframe>
				</div>
				<?php } ?>				

			</div>
	

<?php require_once ("inc/footer.inc.php"); ?>