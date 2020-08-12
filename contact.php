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

	$content = GetContent('contact');


	if (isset($_POST['action']) && $_POST['action'] == 'contact')
	{
		unset($errs);
		$errs = array();

		$fname		= getPostParameter('fname');
		$email		= getPostParameter('email');
		$subject	= trim(getPostParameter('subject'));
		$umessage	= nl2br(getPostParameter('umessage'));

		if (!($fname && $email && $subject && $umessage))
		{
			$errs[] = CBE1_CONTACT_ERR1;
		}
		else
		{
			if (isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				$errs[] = CBE1_CONTACT_ERR2;
			}
		}

		if (count($errs) == 0)
		{
			$from = 'From: '.$fname.' <'.$email.'>';
			SendEmail(SITE_MAIL, $subject, $umessage, $noreply_mail = 1, $from);
				
			header("Location: contact.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE			= $content['title'];
	$PAGE_DESCRIPTION	= $content['meta_description'];
	$PAGE_KEYWORDS		= $content['meta_keywords'];

	require_once ("inc/header.inc.php");
	
?>

		<h1><?php echo $content['title']; ?></h1>

		<div class="row">
			<div class="col-md-6">
			<br>

			<!--<h3><?php echo CBE1_CONTACT_TITLE; ?></h3>-->

			<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
				<div class="alert alert-success"><?php echo CBE1_CONTACT_SENT; ?></div>
			<?php }?>
	
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="alert alert-danger"><?php echo $allerrors; ?></div>
			<?php } ?>	
	
		  <form action="" method="post">
		  <div class="form-group">
			  <label><?php echo CBE1_CONTACT_NAME; ?></label>
			  <input name="fname" class="form-control" type="text" value="<?php echo getPostParameter('fname'); ?>" required="required">
			</div>
			<div class="form-group">
			  <label><?php echo CBE1_CONTACT_EMAIL; ?></label>
			  <input name="email" class="form-control" type="text" value="<?php echo getPostParameter('email'); ?>" required="required">
			</div>
			<div class="form-group">
			  <label><?php echo CBE1_CONTACT_SUBJECT; ?></label>
			  <input name="subject" class="form-control" type="text" value="<?php echo getPostParameter('subject'); ?>" required="required">
			</div>
			<div class="form-group">
			  <label><?php echo CBE1_CONTACT_MESSAGE; ?></label>
			  <textarea cols="50" rows="8" class="form-control" required="required" name="umessage"><?php echo getPostParameter('umessage'); ?></textarea>
			</div>
			<input type="hidden" name="action" id="action" value="contact" />
			<input type="submit" class="submit" name="Submit" value="<?php echo CBE1_CONTACT_BUTTON; ?>" />
		  </form>
	  
	  </div>
	  <div class="col-md-6">
		  <p><?php echo $content['text']; ?></p>
	  </div>
	  </div>
	
<?php require_once ("inc/footer.inc.php"); ?>