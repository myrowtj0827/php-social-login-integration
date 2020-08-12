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

	if (MEMBERS_SUBMIT_COUPONS == 1 && !isLoggedIn())
	{
		header ("Location: login.php?login");
		exit();
	}

	if (SUBMIT_COUPONS != 1)
	{
		header ("Location: index.php");
		exit();
	}


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		unset($errs);
		$errs = array();

		$coupon_type	= mysqli_real_escape_string($conn, getPostParameter('coupon_type'));
		$coupon_title	= mysqli_real_escape_string($conn, getPostParameter('coupon_title'));
		$retailer_id	= (int)getPostParameter('store');
		$code			= mysqli_real_escape_string($conn, getPostParameter('code'));
		$link			= mysqli_real_escape_string($conn, getPostParameter('link'));
		$date_mm		= mysqli_real_escape_string($conn, getPostParameter('date_mm'));
		$date_dd		= mysqli_real_escape_string($conn, getPostParameter('date_dd'));
		$date_yy		= mysqli_real_escape_string($conn, getPostParameter('date_yy'));
		$description	= mysqli_real_escape_string($conn, nl2br(getPostParameter('description')));
		$captcha		= mysqli_real_escape_string($conn, getPostParameter('captcha'));
		$ip				= mysqli_real_escape_string($conn, getenv("REMOTE_ADDR"));
		if (isLoggedIn()) $author_id = (int)$userid; else $author_id = "11111111";


		if (!($coupon_type && $coupon_title && $retailer_id))
		{
			$errs[] = CBE1_SCOUPON_ERR1;
		}
		else
		{
			if ($date_mm && $date_dd && $date_yy)
			{
				$end_date = $date_yy."-".$date_mm."-".$date_dd;
	
				if (strtotime($end_date) < strtotime("now"))
				{
					$errs[] = CBE1_SCOUPON_ERR3;
				}
				else
				{
					$end_date .= " 00:00:00";
				}
			}

			if ($coupon_title == "coupon" && $code == "")
			{
				$errs[] = CBE1_SIGNUP_ERR4;
			}
			elseif ($coupon_title == "printable" && $link == "")
			{
				$errs[] = CBE1_SIGNUP_ERR5;
			}

			if (!isLoggedIn())
			{
				if (!$captcha || empty($_SESSION['captcha']) || strcasecmp($_SESSION['captcha'], $captcha) != 0)
				{
					$errs[] = CBE1_SIGNUP_ERR3;
				}
			}

			if ($code != "") $where = "AND code='$code'"; elseif($link != "") $where = "AND link='$link'";
			if ($where != "")
			{
				$check_query = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' $where LIMIT 1");
				if (mysqli_num_rows($check_query) != 0)
				{
					$errs[] = CBE1_SCOUPON_ERR2;
				}
			}
		}

		if (count($errs) == 0)
		{
			$query = "INSERT INTO cashbackengine_coupons SET coupon_type='$coupon_type', title='$coupon_title', retailer_id='$retailer_id', user_id='$author_id', code='$code', link='$link', start_date='', end_date='$end_date', description='$description', viewed='0', status='inactive', added=NOW()";
			$result = smart_mysql_query($query);

			// send email notification //
			if (NEW_COUPON_ALERT == 1)
			{
				SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT1, CBE1_EMAIL_ALERT1_MSG);
			}
			/////////////////////////////
		
			header("Location: submit_coupon.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}


	if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
	{
		$retailer_id = (int)$_REQUEST['id'];

		$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1"; 
		$result = smart_mysql_query($query);
		$total = mysqli_num_rows($result);
		if ($total > 0)
		{
			$row = mysqli_fetch_array($result);
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_SCOUPON_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><img src="<?php echo SITE_URL; ?>images/coupon.png" align="absmiddle" /> <?php echo CBE1_SCOUPON_TITLE; ?></h1>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div class="alert alert-success"><?php echo CBE1_SCOUPON_SENT; ?></div>
	<?php } ?>

	<?php if (!(isset($_GET['msg']) && $_GET['msg'] == 1)) { ?>		

<div class="container">
<div class="row">
	<div class="col-md-6 col-md-offset-3">
		
		<p align="center"><?php echo CBE1_SCOUPON_TEXT; ?></p>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="alert alert-danger"><?php echo $allerrors; ?></div>
		<?php } ?>

		
		<form action="" method="post">
		<div class="form-group">
		   <label><?php echo CBE1_SCOUPON_STORE; ?>:</label>
		   <?php if ($total > 0) { ?>
				<b><?php echo $row['title']; ?></b>
		   <?php }else{ ?>
				<select name="store" id="store" class="form-control">
				<option value=""><?php echo CBE1_SCOUPON_STORE_SELECT; ?></option>
				<?php
					$select_allstores = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title ASC");
					while ($srow_allstores = mysqli_fetch_array($select_allstores))
					{
						if ($retailer_id == $srow_allstores['retailer_id']) $dsel = "selected='selected'"; else $dsel = "";
						echo "<option value=\"".$srow_allstores['retailer_id']."\" $dsel>".$srow_allstores['title']."</option>";
					}
				?>
				</select>
			<?php } ?>
		</div>
		<div class="form-group">
		   <label><?php echo CBE1_SCOUPON_TYPE; ?>:</label>
				<select name="coupon_type" id="coupon_type" class="form-control" onchange="hiddenDiv('coupon_type')">
					<option value="coupon" <?php if ($coupon_type == "coupon") echo "selected='selected'"; ?>><?php echo CBE1_SCOUPON_TYPE1; ?></option>
					<option value="printable" <?php if ($coupon_type == "printable") echo "selected='selected'"; ?>><?php echo CBE1_SCOUPON_TYPE2; ?></option>
					<option value="discount" <?php if ($coupon_type == "discount") echo "selected='selected'"; ?>><?php echo CBE1_SCOUPON_TYPE3; ?></option>
				</select>
		</div>
		<div class="form-group">
		   <label><?php echo CBE1_SCOUPON_NAME; ?>:</label>
		   <input type="text" name="coupon_title" id="coupon_title" value="<?php echo getPostParameter('coupon_title'); ?>" class="form-control" required="required">
		</div>
		<div id="coupon_code" <?php if ($coupon_type != "coupon") { ?>style="display: none;"<?php } ?>>
		   <label><?php echo CBE1_SCOUPON_CODE; ?>:</label>
		   <td valign="top"><input type="text" name="code" id="code" value="<?php echo getPostParameter('code'); ?>" class="form-control">
		</div>
		<div id="coupon_link" <?php if ($coupon_type != "printable") { ?>style="display: none;"<?php } ?>>
		   <label><?php echo CBE1_SCOUPON_LINK; ?>:</label>
		   <input type="text" name="link" id="link" placeholder="http://mycouponlink.com" value="<?php echo getPostParameter('link'); ?>" class="form-control">
		</div>
		<div class="form-group">
		   <label><?php echo CBE1_SCOUPON_EXPIRY; ?> (<?php echo CBE1_FORMS_OPTIONAL; ?>):</label>
			   	<div class="row">
			   		<div class="col-xs-4">
				<input type="text" name="date_mm" id="date_mm" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_MM; ?>" class="form-control" value="<?php echo getPostParameter('date_mm'); ?>" maxlength="2" size="2" /></div>
				<div class="col-xs-4">
				<input type="text" name="date_dd" id="date_dd" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_DD; ?>" class="form-control" value="<?php echo getPostParameter('date_dd'); ?>" maxlength="2" size="2" /></div>
				<div class="col-xs-4">
				<input type="text" name="date_yy" id="date_yy" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_YYYY; ?>" class="form-control" value="<?php echo getPostParameter('date_yy'); ?>" maxlength="4" size="4" /></div>
			   	</div>
		</div>
		<div class="form-group">
			<textarea name="description" cols="55" rows="5" class="form-control" placeholder="<?php echo CBE1_SCOUPON_DESCRIPTION; ?>"><?php echo getPostParameter('description'); ?></textarea>
		</div>
		<?php if (!isLoggedIn()) { ?>
		<div class="form-group">
			<label><?php echo CBE1_SIGNUP_SCODE; ?>:</label>
				<div class="row">
				<div class="col-xs-4">
				<input type="text" id="captcha" class="form-control" name="captcha" value="" required="required">
				</div>
				<div class="col-xs-8">
				<img src="<?php echo SITE_URL; ?>captcha.php?rand=<?php echo rand(); ?>" id="captchaimg" align="absmiddle" /> <a href="javascript: refreshCaptcha();" style="color: #777" title="<?php echo CBE1_SIGNUP_RIMG; ?>"><img src="<?php echo SITE_URL; ?>images/icon_refresh.png" align="absmiddle" alt="<?php echo CBE1_SIGNUP_RIMG; ?>" /></a>
				</div>
				</div>
		 </div>
		 <?php } ?>
			<?php if ($row['retailer_id'] > 0) { ?><input type="hidden" name="store" value="<?php echo (int)$row['retailer_id']; ?>" /><?php } ?>
			<?php if ($row['retailer_id'] > 0) { ?><input type="hidden" name="id" value="<?php echo (int)$row['retailer_id']; ?>" /><?php } ?>
			<input type="hidden" name="action" value="add" />
			<input type="submit" class="submit" value="<?php echo CBE1_SUBMIT_BUTTON; ?>" />
			<input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onclick="history.go(-1);return false;" />
		 </form>

			<script language="javascript" type="text/javascript">
				function hiddenDiv(id){
					if (document.getElementById(id).value == "printable"){
						document.getElementById("coupon_link").style.display = "";
						document.getElementById("coupon_code").style.display = "none";
					}else if(document.getElementById(id).value == "discount"){
						document.getElementById("coupon_code").style.display = "none";
						document.getElementById("coupon_link").style.display = "none";
					}else{
						document.getElementById("coupon_code").style.display = "";
						document.getElementById("coupon_link").style.display = "none";
					}
				}
			</script>

			<script language="javascript" type="text/javascript">
				function refreshCaptcha()
				{
					var img = document.images['captchaimg'];
					img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
				}
			</script>

	</div>
</div>
</div>

	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>