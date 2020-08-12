<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
  * Copyright (c) 2010-2017 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	if (file_exists("./install.php"))
	{
		header ("Location: install.php");
		exit();
	}

	session_start();
	require_once("inc/config.inc.php");

	// save referral id //////////////////////////////////////////////
	if (isset($_GET['ref']) && is_numeric($_GET['ref']))
	{
		$ref_id = (int)$_GET['ref'];
		setReferral($ref_id);

		// count ref link clicks
		if (!isLoggedIn())
		{
			smart_mysql_query("UPDATE cashbackengine_users SET ref_clicks=ref_clicks+1 WHERE user_id='$ref_id' LIMIT 1");
		}

		header("Location: index.php");
		exit();
	}

	// set language ///////////////////////////////////////////////////
	if (isset($_GET['lang']) && $_GET['lang'] != "")
	{
		$site_lang	= strtolower(getGetParameter('lang'));
		$site_lang	= preg_replace("/[^0-9a-zA-Z]/", " ", $site_lang);
		$site_lang	= substr(trim($site_lang), 0, 30);
		
		if ($site_lang != "")
		{
			setcookie("site_lang", $site_lang, time()+3600*24*365, '/');
		}

		header("Location: index.php");
		exit();
	}

	$content = GetContent('home');

	///////////////  Page config  ///////////////
	$PAGE_TITLE			= SITE_HOME_TITLE;
	$PAGE_DESCRIPTION	= $content['meta_description'];
	$PAGE_KEYWORDS		= $content['meta_keywords'];

	require_once("inc/header.inc.php");

?>

</div>

		<?php

			if (FEATURED_STORES_LIMIT > 0)
			{
				// show featured retailers //
				$result_featured = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY RAND() LIMIT ".FEATURED_STORES_LIMIT); //12
				$total_featured = mysqli_num_rows($result_featured);

				if ($total_featured > 0) { 
		?>

			<div class="heading">
			<div class="container">
			<div class="row"><h1><?php echo CBE1_HOME_FEATURED_STORES; ?></h1></div>
			</div>
			</div>
				
			<div class="brands-section">
			<div class="container">
			<div class="row">

			<?php while ($row_featured = mysqli_fetch_array($result_featured)) { ?>
				<div class="brands-ctnr"><a href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>"><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" border="0"></a><h4 class="hidden-xs"><?php echo $row_featured['title']; ?></h4>
				<?php if ($row_featured['cashback'] != "") { ?>
					<span class="cashback hidden-xs"><span class="value"><?php echo DisplayCashback($row_featured['cashback']); ?> <?php echo CBE1_CASHBACK; ?></span></span>
				<?php } ?>
			</div>
			<?php } ?>
			</div>
			</div>
			</div>
		<?php
				}
			} // end featured retailers 
		?>
 
 
 		<?php
			if (TODAYS_COUPONS_LIMIT > 0)
			{
				// show today's top coupons //
				$result_todays_coupons = smart_mysql_query("SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND DATE(c.last_visit)=DATE(NOW()) AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY visits_today DESC LIMIT ".TODAYS_COUPONS_LIMIT);
				$total_todays_coupons = mysqli_num_rows($result_todays_coupons);

				if ($total_todays_coupons > 0) { 
		?>
		
			  <div class="heading">
			  <div class="container">
			  <div class="row"><h1><?php echo CBE1_HOME_TOP_COUPONS; ?></h1></div>
			  </div>
			  </div>
			
			<div class="product-cntr">
			<div class="container">
			  <div class="row">
				  
			<?php while ($row_todays_coupons = mysqli_fetch_array($result_todays_coupons)) { ?>	  
			<div class="col-md-3">
			<div class="row">

			<div class="copun-ctnr"><a href="<?php echo GetRetailerLink($row_todays_coupons['retailer_id'], $row_todays_coupons['title']); ?>"><img src="<?php if (!stristr($row_todays_coupons['image'], 'http')) echo SITE_URL."img/"; echo $row_todays_coupons['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_todays_coupons['title']; ?>" title="<?php echo $row_todays_coupons['title']; ?>" border="0" /></a>
			 <h3><?php echo $row_todays_coupons['coupon_title']; ?></h3>
			<?php if ($row_todays_coupons['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($row_todays_coupons['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 0); ?>&nbsp;</div><?php } ?>
			 <h4><?php echo ($row_todays_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></h4><br>
			  </div>
			</div>
			<?php } ?>
			</div>
			</div>
			</div>
		<?php
				}
			} // end today's top coupons
		?>
 

		<?php

			if (HOMEPAGE_REVIEWS_LIMIT > 0)
			{
				// Show recent reviews //
				$reviews_query = "SELECT r.*, DATE_FORMAT(r.added, '".DATE_FORMAT."') AS review_date, u.user_id, u.username, u.fname, u.lname FROM cashbackengine_reviews r LEFT JOIN cashbackengine_users u ON r.user_id=u.user_id WHERE r.status='active' ORDER BY r.added DESC LIMIT ".HOMEPAGE_REVIEWS_LIMIT;
				$reviews_result = smart_mysql_query($reviews_query);
				$reviews_total = mysqli_num_rows($reviews_result);

				if ($reviews_total > 0) {
		?>
			  <div class="heading">
			  <div class="container">
			  <div class="row"><h1><span class="glyphicon glyphicon-user"></span> <?php echo CBE1_HOME_RECENT_REVIEWS; ?></h1></div>
			  </div>
			  </div>
			
			<div class="product-cntr">
			<div class="container">
			  <div class="row">			  		
			<?php while ($reviews_row = mysqli_fetch_array($reviews_result)) { ?>
            <div class="col-md-4">
	            <div class="user_review" style="border: 1px solid #EEE; border-radius: 7px; padding: 10px">
	                <span class="review-author"><?php echo $reviews_row['fname']." ".substr($reviews_row['lname'], 0, 1)."."; ?></span>
					<span class="review-date"><?php echo $reviews_row['review_date']; ?></span><br/><br/>
					<b><a href="<?php echo GetRetailerLink($reviews_row['retailer_id'], GetStoreName($reviews_row['retailer_id'])); ?>"><?php echo GetStoreName($reviews_row['retailer_id']); ?></a></b><br/>
					<img src="<?php echo SITE_URL; ?>images/icons/rating-<?php echo $reviews_row['rating']; ?>.png" />&nbsp;
					<span class="review-title"><?php echo $reviews_row['review_title']; ?></span><br/>
					<div class="review-text"><?php echo $reviews_row['review']; ?></div>
                </div>
            </div>
			<?php } ?>
			</div>
			</div>
			</div>
		<?php
				}
			}
		?>

<br/>
</div>
</div>


<?php require_once("inc/footer.inc.php"); ?>