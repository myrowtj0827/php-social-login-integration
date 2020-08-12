<!DOCTYPE html>
<html lang="en-us">
<head>
	<title><?php echo $PAGE_TITLE." | ".SITE_TITLE; ?></title>
	<?php if ($PAGE_DESCRIPTION != "") { ?><meta name="description" content="<?php echo $PAGE_DESCRIPTION; ?>" /><?php } ?>
	<?php if ($PAGE_KEYWORDS != "") { ?><meta name="keywords" content="<?php echo $PAGE_KEYWORDS; ?>" /><?php } ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="author" content="CashbackEngine.net" />
	<meta name="robots" content="index, follow" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:400,700" rel="stylesheet" type="text/css" />
    <link href="<?php echo SITE_URL; ?>css/bootstrap.min.css" rel="stylesheet" />
	<!--[if lt IE 9]>
    <script src="<?php echo SITE_URL; ?>js/html5shiv.js"></script>
    <script src="<?php echo SITE_URL; ?>js/respond.min.js"></script>
    <![endif]-->   
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>css/stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>css/style.css" />
	<link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" />
	<link rel="icon" type="image/ico" href="<?php echo SITE_URL; ?>favicon.ico" />
	<meta property="og:title" content="<?php echo $PAGE_TITLE; ?>" />
	<meta property="og:url" content="<?php echo SITE_URL; ?>" />
	<meta property="og:description" content="<?php echo $PAGE_DESCRIPTION; ?>" />
	<meta property="og:image" content="<?php echo SITE_URL; ?>images/logo.png" />
	<?php echo GOOGLE_ANALYTICS; ?>
</head>
<body>
<a href="#" class="scrollup">Top</a>
		
<div id="wrapper">
<div class="header">
  <header id="header" class="header-in">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="logo"><a href="<?php echo SITE_URL; ?>"><img src="<?php echo SITE_URL; ?>images/logo.png" alt="<?php echo SITE_TITLE; ?>"></a></div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="row">
         
            <div class="header-top">
				<?php if (MULTILINGUAL == 1 && count($languages) > 0) { ?>
					<div class="top1" id="languages">
					<?php foreach ($languages as $language_code => $language) { ?>
						<a href="<?php echo SITE_URL; ?>?lang=<?php echo $language; ?>"><img src="<?php echo SITE_URL; ?>images/flags/<?php echo $language_code; ?>.png" alt="<?php echo $language; ?>" border="0" /></a> &nbsp;
					<?php } ?>
					</div>
				<?php } ?>
             <div class="top2">
	             
			<div class="search-filed">
            <form name="searchfrm" id="searchfrm" action="<?php echo SITE_URL; ?>search.php" method="get" autocomplete="off">
            <div id="custom-search-input">
                <div class="input-group col-md-12">
                    <input type="text" onkeypress="ajaxsearch(this.value)" id="searchtext" name="searchtext" class="form-control input-lg" value="<?php echo @$stext; ?>" placeholder="<?php echo CBE_SEARCH_MSG; ?>" />
                    <span class="input-group-btn">
                        <button class="btn btn-info btn-lg" type="submit">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            <input type="hidden" name="action" value="search" />
            </form>
            <div class="searchhere"></div>
			</div>    

	             
             <div class="login-reg">
	             
			 <?php if (isLoggedIn()) { ?>
					<?php echo CBE_WELCOME; ?>, <span class="member"><b><?php echo $_SESSION['FirstName']; ?></b></span> | <?php echo CBE_BALANCE; ?>: <span class="mbalance"><?php echo GetUserBalance($_SESSION['userid']); ?></span> | <?php echo CBE_REFERRALS; ?>: <a href="<?php echo SITE_URL; ?>invite.php" style="color: #000"><span class="referrals"><?php echo GetReferralsTotal($_SESSION['userid']); ?></span></a><!-- | <a class="logout" href="<?php echo SITE_URL; ?>logout.php"><?php echo CBE_LOGOUT; ?></a>-->
				
					<div class="dropdown" id="account_nav">
					  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><img src="<?php echo SITE_URL; ?>images/icon_user.png"> <?php echo CBE1_BOX_ACCOUNT; ?> <?php if (GetMemberMessagesTotal() > 0) { ?> <span class="badge"><?php echo GetMemberMessagesTotal(); ?></span><?php } ?> <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					    <?php require_once("inc/usermenu.inc.php"); ?>
					  </ul>
					</div>
									
			<?php }else{ ?>
				<a class="signup" href="<?php echo SITE_URL; ?>signup.php"><?php echo CBE_SIGNUP; ?></a> <a class="login" href="<?php echo SITE_URL; ?>login.php"><?php echo CBE_LOGIN; ?></a>
			<?php } ?>	             
	             
             </div>

             
             </div>
            </div>
              
            </div>
           
          </div>
        </div>
      </div>
  </header>		

<div class="menu-ctnr">
<div class="container">
<div class="row">

  <nav class="navbar navbar-default" style="background-color: transparent; border: none;">
    <div class="navbar-header">
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"></a>
    </div>
    <div class="collapse navbar-collapse js-navbar-collapse">
      <ul class="nav navbar-nav">
 			<li><a href="<?php echo SITE_URL; ?>" class="home"><?php echo CBE_MENU_HOME; ?></a></li>
			<li><a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE_MENU_STORES; ?></a></li>
			<li><a href="<?php echo SITE_URL; ?>coupons.php"><?php echo CBE_MENU_COUPONS; ?></a></li>
			<li><a href="<?php echo SITE_URL; ?>featured.php"><?php echo CBE_MENU_FEATURED; ?></a></li>
			<!--<li><a href="<?php echo SITE_URL; ?>myaccount.php" rel="nofollow"><?php echo CBE_MENU_ACCOUNT; ?></a></li>-->
			<li><a href="<?php echo SITE_URL; ?>myfavorites.php" rel="nofollow"><?php echo CBE_MENU_FAVORITES; ?></a></li>
			<li><a href="<?php echo SITE_URL; ?>howitworks.php"><?php echo CBE_MENU_HOW; ?></a></li>
			<li><a href="<?php echo SITE_URL; ?>help.php"><?php echo CBE_MENU_HELP; ?></a></li>
			<?php echo ShowTopPages(); ?>
      </ul>
    </div>
  </nav>
  
</div>
</div>
</div>

</div>


<div id="body-ctnr">
<div class="container">
<div class="row">		

