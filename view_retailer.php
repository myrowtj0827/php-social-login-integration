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

	$ADDTHIS_SHARE = 1;

	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$retailer_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}

	$query = "SELECT *, DATE_FORMAT(added, '".DATE_FORMAT."') AS date_added FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysqli_num_rows($result);

	if ($total > 0)
	{
		$row = mysqli_fetch_array($result);
		
		$retailer_id	= $row['retailer_id'];
		$cashback		= DisplayCashback($row['cashback']);
		$retailer_url	= GetRetailerLink($row['retailer_id'], $row['title']);
		if (isLoggedIn()) $retailer_url .= "&ref=".(int)$_SESSION['userid'];

		// save referral //
		if (!isLoggedIn() && isset($_GET['ref']) && is_numeric($_GET['ref']))
		{
			$ref_id = (int)$_GET['ref'];
			setReferral($ref_id);
		}

		if ($row['seo_title'] != "")
		{
			$ptitle	= $row['seo_title'];
		}
		else
		{
			if ($cashback != "") 
				$ptitle	= $row['title'].". ".CBE1_STORE_EARN." ".$cashback." ".CBE1_CASHBACK2;
			else
				$ptitle	= $row['title'];
		}		

		//// ADD REVIEW ///////////////////////////////////////////////////////////////////////
		if (isset($_POST['action']) && $_POST['action'] == "add_review" && isLoggedIn())
		{
			$userid			= (int)$_SESSION['userid'];
			$retailer_id	= (int)getPostParameter('retailer_id');
			$rating			= (int)getPostParameter('rating');
			$review_title	= mysqli_real_escape_string($conn, getPostParameter('review_title'));
			$review			= mysqli_real_escape_string($conn, nl2br(trim(getPostParameter('review'))));
			$review			= ucfirst(strtolower($review));

			unset($errs);
			$errs = array();

			if (!($userid && $retailer_id && $rating && $review_title && $review))
			{
				$errs[] = CBE1_REVIEW_ERR;
			}
			else
			{
				$number_lines = count(explode("<br />", $review));
				
				if (strlen($review) > MAX_REVIEW_LENGTH)
					$errs[] = str_replace("%length%",MAX_REVIEW_LENGTH,CBE1_REVIEW_ERR2);
				else if ($number_lines > 5)
					$errs[] = CBE1_REVIEW_ERR3;
				else if (stristr($review, 'http'))
					$errs[] = CBE1_REVIEW_ERR4;
			}

			if (count($errs) == 0)
			{
				$review = substr($review, 0, MAX_REVIEW_LENGTH);
				
				if (ONE_REVIEW == 1)
					$check_review = mysqli_num_rows(smart_mysql_query("SELECT * FROM cashbackengine_reviews WHERE retailer_id='$retailer_id' AND user_id='$userid'"));
				else
					$check_review = 0;

				if ($check_review == 0)
				{
					(REVIEWS_APPROVE == 1) ? $status = "pending" : $status = "active";
					$review_query = "INSERT INTO cashbackengine_reviews SET retailer_id='$retailer_id', rating='$rating', user_id='$userid', review_title='$review_title', review='$review', status='$status', added=NOW()";
					$review_result = smart_mysql_query($review_query);
					$review_added = 1;

					// send email notification //
					if (NEW_REVIEW_ALERT == 1) 
					{
						SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT2, CBE1_EMAIL_ALERT2_MSG);
					}
					/////////////////////////////
				}
				else
				{
					$errormsg = CBE1_REVIEW_ERR5;
				}

				unset($_POST['review']);
			}
			else
			{
				$errormsg = "";
				foreach ($errs as $errorname)
					$errormsg .= $errorname."<br/>";
			}
		}
		//////////////////////////////////////////////////////////////////////////////////////////
	}
	else
	{
		$ptitle = CBE1_STORE_NOT_FOUND;
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE			= $ptitle;
	$PAGE_DESCRIPTION	= $row['meta_description'];
	$PAGE_KEYWORDS		= $row['meta_keywords'];

	require_once ("inc/header.inc.php");

?>	

	<?php

		if ($total > 0) {

	?>
			<h1><?php echo $ptitle; ?></h1>

			<div class="breadcrumbs"><a href="<?php echo SITE_URL; ?>" class="home_link"><?php echo CBE1_BREADCRUMBS_HOME; ?></a> &#155; <a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_BREADCRUMBS_STORES; ?></a> &#155; <?php echo $row['title']; ?></div>

			<div class="row">
					<div class="col-md-3 text-center">
						<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank">
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"><img src="<?php echo SITE_URL; ?>images/featured.png"></span><?php } ?>
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" class="img-responsive"></div></a>
						<div style="clear: both"></div>
						<?php if ($cashback != "") { ?>
						<div class="visible-xs">
							<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
							<span class="bcashback"><?php echo $cashback; ?> <?php echo CBE1_CASHBACK; ?></span>
						</div>
						<?php } ?>
						<a class="coupons" href="#coupons"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?> <?php echo CBE1_STORE_COUPONS1; ?></a><br/>
						<a class="scroll2reviews" data-location="#reviews" href="#reviews" style="color: #707070; font-weight: bold;"><?php echo GetStoreReviewsTotal($row['retailer_id']); ?></a><br/>
						<?php echo GetStoreRating($row['retailer_id'], $show_start = 1); ?>
					</div>
					<div class="col-md-9">

						<?php if ($cashback != "") { ?>
						<div class="cashback_box hidden-xs" style="float:right">
							<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
							<span class="bcashback"><?php echo $cashback; ?></span> <?php echo CBE1_CASHBACK; ?>
						</div>
						<?php } ?>		
						
						<div class="info_box2">
							<a class="stitle" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo $row['title']; ?></a>
							<div class="retailer_description"><?php echo TruncateText(stripslashes($row['description']), STORES_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div>
							<?php echo GetStoreCountries($row['retailer_id']); ?>
							<?php if ($row['tags'] != "") { ?><p><span class="tags"><?php echo $row['tags']; ?></span></p><?php } ?>
							<?php if ($row['conditions'] != "") { ?>
								<br/><p><b><?php echo CBE1_CONDITIONS; ?></b><br/>
								<span class="conditions_desc"><?php echo $row['conditions']; ?></span>
								</p>
							<?php } ?>
						</div>
			
						<div style="clear: both"></div>
						<div class="info_links" style="width: 100%; margin-top: 10px">
							<a class="favorites" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_ADD_FAVORITES; ?></a>
							<a class="report" href="<?php echo SITE_URL; ?>report_retailer.php?id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_REPORT; ?></a>
							<?php if (SUBMIT_COUPONS == 1) { ?>
								<a class="submit_coupon" href="<?php echo SITE_URL; ?>submit_coupon.php?id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_STORE_COUPONS2; ?></a>
							<?php } ?>
							<br/><br/><br/>
							<p>
							<?php if (isset($_SESSION['userid'])) { ?>
								<a class="go2store_large" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE2; ?></a>
							<?php }else{ ?>
								<a class="go2store_large" href="#" data-toggle="modal" data-target="#loginModal"><?php echo CBE1_GO_TO_STORE2; ?></a>
							<?php } ?>
							</p>
							<br>
						</div>
					</div>
			</div>

			<div style="clear: both"></div>
			<div class="row" style="background: #F9F9F9">
					
				<?php if (SHOW_RETAILER_STATS == 1) { ?>
				<div class="col-xs-6 col-md-3">
					<div class="retailer_statistics">
						<center><h3><?php echo CBE1_STORE_STATS; ?></h3></center>
						<table width="100%" border="0">
							<tr><td><?php echo CBE1_STORE_COUPONS; ?>:</td><td><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></td></tr>
							<tr><td><?php echo CBE1_STORE_REVIEWS; ?>:</td><td><?php echo GetStoreReviewsTotal($row['retailer_id'], $all = 0, $word = 0); ?></td></tr>
							<tr><td><?php echo CBE1_STORE_FAVORITES; ?>:</td><td><?php echo GetFavoritesTotal($row['retailer_id']); ?></td></tr>
							<tr><td><?php echo CBE1_STORE_DATE; ?>:</td><td><?php echo $row['date_added']; ?></td></tr>
						</table>
					</div>
				</div>
				<?php } ?>
				<?php if (SHOW_CASHBACK_CALCULATOR == 1 && strstr($row['cashback'], '%')) { ?>
				<div class="col-xs-6 col-md-3">
					<h3 class="text-center"><?php echo CBE1_STORE_CCALCULATOR; ?></h3>
					<table align="center" width="100%" border="0" cellspacing="0" cellpadding="2">
					<tr>
						<td width="50%" align="center"><?php echo CBE1_STORE_SPEND; ?></td>
						<td width="50%" align="center"><?php echo CBE1_CASHBACK; ?></td>
					</tr>
					<tr>
						<td align="center"><span class="calc_spend"><?php echo DisplayMoney("100", 0, 1); ?></span></td>
						<td align="center"><span class="calc_cashback"><?php echo DisplayMoney(CalculatePercentage(100, $cashback),0,1); ?></span></td>
					</tr>
					<tr>
						<td align="center"><span class="calc_spend"><?php echo DisplayMoney("500", 0, 1); ?></span></td>
						<td align="center"><span class="calc_cashback"><?php echo DisplayMoney(CalculatePercentage(500, $cashback),0,1); ?></span></td>
					</tr>
					<tr>
						<td align="center"><span class="calc_spend"><?php echo DisplayMoney("1000", 0, 1); ?></span></td>
						<td align="center"><span class="calc_cashback"><?php echo DisplayMoney(CalculatePercentage(1000, $cashback),0,1); ?></span></td>
					</tr>
					</table>
				</div>
				<?php } ?>
				<div class="col-xs-12 col-md-6">

					<div class="share_box">
						<!-- AddThis Share Buttons -->
						<div class="addthis_toolbox" addthis:url="<?php echo $retailer_url; ?>" addthis:title="<?php echo $ptitle; ?>">
						<div class="addthis_toolbox addthis_default_style">
							<?php if (SHARE_ICONS_STYLE == 1) { ?>
								<a class="addthis_button_facebook_like" fb:like:layout="box_count"></a> 
								<a class="addthis_button_tweet" tw:count="vertical"></a> 
								<a class="addthis_button_google_plusone"></a>
							<?php }else{ ?>
								<a class="addthis_button_facebook_like"></a> 
								<a class="addthis_button_tweet"></a> 
								<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
							<?php } ?>
							<a title="<?php echo CBE1_STORE_MORE; ?>" href="http://addthis.com/bookmark.php?v=250" class="addthis_button_expanded at300m" style="color: #999;">&nbsp;</a>
						</div>
						</div>
					</div>
					<div class="share_boxx">
						<?php echo CBE1_STORE_SHARE; ?>:
						<input type="text" class="form-control" style="background: #FFF" READONLY onfocus="this.select();" onclick="this.focus();this.select();" value="<?php echo $retailer_url; ?>" /><br/>	
					</div>

				</div>
			</div>
			
			
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery.min.js"></script>


		<?php
				// start store coupons //
				$ee = 0;
				$query_coupons = "SELECT *, DATE_FORMAT(end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND (start_date<=NOW() AND (end_date='0000-00-00 00:00:00' OR end_date > NOW())) AND status='active' ORDER BY sort_order, added DESC";
				$result_coupons = smart_mysql_query($query_coupons);
				$total_coupons = mysqli_num_rows($result_coupons);

				if ($total_coupons > 0)
				{
		?>
			<a name="coupons"></a>
			<h3 class="store_coupons"><?php echo $row['title']; ?> <?php echo CBE1_STORE_COUPONS; ?></h3>
			<ul class="coupons-list">
				<?php while ($row_coupons = mysqli_fetch_array($result_coupons)) { $ee++; ?>
				<li class="coupon">
					<span class="scissors"></span>
					<a class="coupon_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><?php echo $row_coupons['title']; ?></a>
					<?php if ($row_coupons['exclusive'] == 1) { ?> <sup><span class="exclusive_s"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span></sup><?php } ?>
					<?php echo ($row_coupons['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$row_coupons['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
					<?php if ($row_coupons['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($row_coupons['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
					<a class="go2store" style="float: right" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><?php echo ($row_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					<?php if ($row_coupons['code'] != "") { ?>
						<span class="coupon_code" style="float: right"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $row_coupons['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span>
						<span class="coupon_note"><?php echo CBE1_COUPONS_MSG; ?></span><br/><br/>
					<?php } ?>
					<?php if ($row_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
						<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row_coupons['coupon_end_date']; ?></span> &nbsp; 
						<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row_coupons['time_left']); ?></span>
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
			<div style="clear: both"></div>
		<?php } // end store coupons // ?>

		<?php
				// start expired coupons //
				$ee = 0;
				$query_exp_coupons = "SELECT *, DATE_FORMAT(end_date, '".DATE_FORMAT."') AS coupon_end_date FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND end_date != '0000-00-00 00:00:00' AND end_date < NOW() AND status!='inactive' ORDER BY sort_order, added DESC";
				$result_exp_coupons = smart_mysql_query($query_exp_coupons);
				$total_exp_coupons = mysqli_num_rows($result_exp_coupons);

				if ($total_exp_coupons > 0)
				{
		?>
			<h3 class="store_exp_coupons"><?php echo CBE1_STORE_ECOUPONS; ?></h3>
			<ul class="coupons-list">
				<?php while ($row_exp_coupons = mysqli_fetch_array($result_exp_coupons)) { $ee++; ?>
				<li class="coupon" style="opacity: 0.5; filter: alpha(opacity=50);">
					<span class="scissors"></span>
					<b><?php echo $row_exp_coupons['title']; ?></b>
					<?php echo ($row_exp_coupons['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$row_exp_coupons['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
					<?php if ($row_exp_coupons['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($row_exp_coupons['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
					<?php if ($row_exp_coupons['code'] != "") { ?><span class="coupon_code" style="float: right; background: #EEE; border-color: #EEE"><?php echo $row_exp_coupons['code']; ?></span><?php } ?>
					<span class="expires"><?php echo CBE1_COUPONS_ENDED; ?>: <?php echo $row_exp_coupons['coupon_end_date']; ?></span>
				</li>
				<?php } ?>
			</ul>
			<div style="clear: both"></div>
		<?php } // end expired coupons // ?>

		<?php
				// store reviews //
				$results_per_page = REVIEWS_PER_PAGE;

				if (isset($_GET['cpage']) && is_numeric($_GET['cpage']) && $_GET['cpage'] > 0) { $page = (int)$_GET['cpage']; } else { $page = 1; }
				$from = ($page-1)*$results_per_page;

				$reviews_query = "SELECT r.*, DATE_FORMAT(r.added, '".DATE_FORMAT."') AS review_date, u.user_id, u.username, u.fname, u.lname FROM cashbackengine_reviews r LEFT JOIN cashbackengine_users u ON r.user_id=u.user_id WHERE r.retailer_id='$retailer_id' AND r.status='active' ORDER BY r.added DESC LIMIT $from, $results_per_page";
				$reviews_result = smart_mysql_query($reviews_query);
				$reviews_total = mysqli_num_rows(smart_mysql_query("SELECT * FROM cashbackengine_reviews WHERE retailer_id='$retailer_id' AND status='active'"));
		?>

		<div id="add_review_link"><a id="add-review" href="javascript:void(0);"><?php echo CBE1_REVIEW_TITLE; ?></a></div>
		<div id="reviews"></div>
		<h3 class="store_reviews"><?php echo $row['title']; ?> <?php echo CBE1_STORE_REVIEWS; ?> <?php echo ($reviews_total > 0) ? "($reviews_total)" : ""; ?></h3>

		<script type="text/javascript">
		$("#add-review").click(function () {
			$("#review-form").toggle("slow");
		});
		$('.scroll2reviews').click(function() {
			var location = jQuery(this).data('location');
			if (jQuery(location).length > 0) { jQuery('html, body').animate({scrollTop:jQuery(location).offset().top},1000); }
		});
		</script>


<div class="row">
	<div class="col-md-6">

		<div id="review-form" class="review-form" style="<?php if (!(isset($_POST['action']) && $_POST['action'] == "add_review")) { ?>display: none;<?php } ?>">
			<?php if (isset($errormsg) && $errormsg != "") { ?>
				<div class="alert alert-danger"><?php echo $errormsg; ?></div>
			<?php } ?>
			<?php if (REVIEWS_APPROVE == 1 && $review_added == 1) { ?>
				<div class="alert alert-success"><?php echo CBE1_REVIEW_SENT; ?></div>
			<?php }else{ ?>
				<?php if (isLoggedIn()) { ?>
					<form method="post" action="#reviews">
						<div class="form-group">
						<select name="rating" class="form-control">
							<option value=""><?php echo CBE1_REVIEW_RATING_SELECT; ?></option>
							<option value="5" <?php if ($rating == 5) echo "selected"; ?>>&#9733;&#9733;&#9733;&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING1; ?></option>
							<option value="4" <?php if ($rating == 4) echo "selected"; ?>>&#9733;&#9733;&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING2; ?></option>
							<option value="3" <?php if ($rating == 3) echo "selected"; ?>>&#9733;&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING3; ?></option>
							<option value="2" <?php if ($rating == 2) echo "selected"; ?>>&#9733;&#9733; - <?php echo CBE1_REVIEW_RATING4; ?></option>
							<option value="1" <?php if ($rating == 1) echo "selected"; ?>>&#9733; - <?php echo CBE1_REVIEW_RATING5; ?></option>					
						</select>
						</div>
						<div class="form-group">
						<label><?php echo CBE1_REVIEW_RTITLE; ?></label>
						<input type="text" name="review_title" id="review_title" value="<?php echo getPostParameter('review_title'); ?>" size="47" class="form-control" /></div>
						<div class="form-group">
						<label><?php echo CBE1_REVIEW_REVIEW; ?></label>
						<textarea id="review" name="review" cols="45" rows="7" class="form-control"><?php echo getPostParameter('review'); ?></textarea>
						</div>
						<input type="hidden" id="retailer_id" name="retailer_id" value="<?php echo $retailer_id; ?>" />
						<input type="hidden" name="action" value="add_review" />
						<button type="submit" class="btn btn-success"><?php echo CBE1_REVIEW_BUTTON; ?></button>
					</form>
				<?php }else{ ?>
					<b><?php echo CBE1_REVIEW_MSG; ?></b>
				<?php } ?>
			<?php } ?>
		</div>
		
	</div>
	</div>

		<div style="clear: both"></div>
		<?php if ($reviews_total > 0) { ?>

			<?php while ($reviews_row = mysqli_fetch_array($reviews_result)) { ?>
            <div id="review">
                <span class="review-author"><?php echo $reviews_row['fname']." ".substr($reviews_row['lname'], 0, 1)."."; ?></span>
				<span class="review-date"><?php echo $reviews_row['review_date']; ?></span><br/><br/>
				<img src="<?php echo SITE_URL; ?>images/icons/rating-<?php echo $reviews_row['rating']; ?>.png" />&nbsp;
				<span class="review-title"><?php echo $reviews_row['review_title']; ?></span><br/>
				<div class="review-text"><?php echo $reviews_row['review']; ?></div>
                <div style="clear: both"></div>
            </div>
			<?php } ?>
		
			<?php echo ShowPagination("reviews",REVIEWS_PER_PAGE,"?id=$retailer_id&","WHERE retailer_id='$retailer_id' AND status='active'"); ?>
		
		<?php }else{ ?>
				<?php echo CBE1_REVIEW_NO; ?>
		<?php } ?>


		<?php
			// start related retailers //
			$query_like = "SELECT * FROM cashbackengine_retailers WHERE retailer_id<>'$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY RAND() LIMIT 4";
			$result_like = smart_mysql_query($query_like);
			$total_like = mysqli_num_rows($result_like);

			if ($total_like > 0)
			{
		?>
			<div style="clear: both"></div>
			<br>
			<h3><?php echo CBE1_STORE_LIKE; ?></h3>
			<div class="sline"></div><br/>
			<div class="row">
				<?php while ($row_like = mysqli_fetch_array($result_like)) { ?>
					<div class="col-xs-6 col-md-3 text-center">
						<h3 class="hidden-xs"><?php echo $row_like['title']; ?></h3>
						<a href="<?php echo GetRetailerLink($row_like['retailer_id'], $row_like['title']); ?>"><img src="<?php if (!stristr($row_like['image'], 'http')) echo SITE_URL."img/"; echo $row_like['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_like['title']; ?>" title="<?php echo $row_like['title']; ?>" border="0" style="margin:5px;" class="imgs" /></a><br/>
						<?php if ($row_like['cashback'] != "") { ?><span class="cashback"><?php echo DisplayCashback($row_like['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?><?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } // end related retailers // ?>

	<?php }else{ ?>
		<h1><?php echo $ptitle; ?></h1>
		<p align="center"><?php echo CBE1_STORE_NOT_FOUND2; ?></p>
		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a></p>
	<?php } ?>		  	


<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h2 class="modal-title" id="myModalLabel"><?php echo CBE1_LOGIN_TITLE; ?></h2>
      </div>
      <div class="modal-body">
	      	<div class="text-right"><?php echo CBE1_LOGIN_NMEMBER; ?> <a href="<?php echo SITE_URL; ?>signup.php"><b><?php echo CBE_SIGNUP; ?></b></a></div>
	      	<div class="alert alert-info text-center"><?php echo CBE1_LOGIN_ERR4; ?></div>
			<div class="login_box" style="padding: 10px; border-radius: 10px;">
			<form action="<?php echo SITE_URL; ?>login.php" method="post">
			<div class="form-group">
				<label><?php echo CBE1_LOGIN_EMAIL2; ?></label>
				<input type="text" class="form-control input-lg" name="username" value="<?php echo getPostParameter('username'); ?>" size="25" required="required" />
			  </div>
			  <div class="form-group">
				<label><?php echo CBE1_LOGIN_PASSWORD; ?></label>
				<input type="password" class="form-control input-lg" name="password" value="" size="25" required="required" />
			  </div>
			  <div class="checkbox">
			  <label>
				<input type="checkbox" class="checkboxx" name="rememberme" id="rememberme" value="1" checked="checked" /> <?php echo CBE1_LOGIN_REMEMBER; ?>
			  </label>
			  </div>
			  <div class="form-group">
					<input type="hidden" name="action" value="login" />
					<input type="submit" class="btn btn-success btn-lg" name="login" id="login" value="<?php echo CBE1_LOGIN_BUTTON; ?>" />
			  </div>
			  <div class="form-group">
					<a href="<?php echo SITE_URL; ?>forgot.php"><?php echo CBE1_LOGIN_FORGOT; ?></a>
					<?php if (ACCOUNT_ACTIVATION == 1) { ?>
						<p><a href="<?php echo SITE_URL; ?>activation_email.php"><?php echo CBE1_LOGIN_AEMAIL; ?></a></p>
					<?php } ?>
			  </div>
		  	</form>
		  	</div>

			<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
				<div style="border-bottom: 1px solid #ECF0F1; margin-bottom: 15px;">
					<div style="font-weight: bold; background: #FFF; color: #CECECE; margin: 0 auto; top: 5px; text-align: center; width: 50px; position: relative;">or</div>
				</div>
				<p align="center"><a href="javascript: void(0);" onclick="facebook_login();" class="connect-f"><img src="<?php echo SITE_URL; ?>images/facebook_connect.png" /></a></p>
			<?php } ?>
		  	
      </div>
      <div class="text-center">
			<div class="sline"></div>
			<h3><?php echo CBE1_LOGIN_THX; ?></h3>
			<p><a class="btn btn-success" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_LOGIN_CONTINUE; ?></a></p>
      </div>
    </div>
  </div>
</div>	
	
	

<?php require_once ("inc/footer.inc.php"); ?>