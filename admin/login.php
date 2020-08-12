<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
  * Copyright (c) 2010-2017 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	$admin_panel = 1;

	session_start();
	require_once("../inc/config.inc.php");


	if (isset($_POST['action']) && $_POST['action'] == "login")
	{
		$username	= mysqli_real_escape_string($conn, getPostParameter('username'));
		$pwd		= mysqli_real_escape_string($conn, getPostParameter('password'));
		$iword		= substr(GetSetting('iword'), 0, -3);
		$ip			= mysqli_real_escape_string($conn, getenv("REMOTE_ADDR"));

		if (!($username && $pwd))
		{
			$errormsg = "Please enter username and password";
		}
		else
		{
			$sql = "SELECT * FROM cashbackengine_settings WHERE setting_key='word' AND setting_value='".md5(sha1($pwd.$iword))."' LIMIT 1";
			$result = smart_mysql_query($sql);

			if ((mysqli_num_rows($result) != 0) && ($username == 'admin'))
			{
				$row = mysqli_fetch_array($result);

				smart_mysql_query("UPDATE cashbackengine_settings SET setting_value=NOW() WHERE setting_key='last_admin_login' LIMIT 1");

				if (!session_id()) session_start();
				$_SESSION['adm']['id'] = $row['setting_id'];
		
				header("Location: index.php");
				exit();
			}
			else
			{
				header("Location: login.php?msg=1");
				exit();
			}
		}
	}

?>
<!DOCTYPE html>
<html lang="en-us">
<head>
	<title>Log in | CashbackEngine Admin Panel</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/login.css" />
	<link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" />
	<link rel="icon" type="image/ico" href="<?php echo SITE_URL; ?>favicon.ico" />
</head>
<body>

<table align="center" cellpadding="5" cellspacing="0" border="0" align="center">
<tr>
	<td height="170" valign="bottom" align="center">
		<a target="_blank" href="http://www.cashbackengine.net"><img src="images/cashbackengine_logo.gif" alt="CashbackEngine" title="CashbackEngine" border="0" /></a><br/>
	</td>
</tr>
</table>

<table width="350" align="center" cellpadding="5" cellspacing="0" border="0" align="center">
<tr>
	<td valign="top" align="left">
      
       <h2 style="margin-bottom: 3px;">Admin Panel</h2>

		<?php if (isset($errormsg) || isset($_GET['msg'])) { ?>
			<table width="100%" style="border: 1px #F3C5D4 dotted;" bgcolor="#EF0303" align="center" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td align="center" valign="middle">
					<span style="color: #FFFFFF">
						<?php if (isset($errormsg) && $errormsg != "") {  echo $errormsg; } ?>
						<?php if ($_GET['msg'] == 1) { echo "Wrong username or password"; } ?>
					</span>
				</td>
			</tr>
			</table>
		<?php } ?>

		<form action="login.php" method="post">
        <table bgcolor="#FFFFFF" width="100%" class="login_box" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr>
            <td width="80" align="right" valign="middle">Username:</td>
            <td valign="top"><input type="text" class="textbox" name="username" value="" size="27" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle">Password:</td>
            <td valign="top"><input type="password" class="textbox" name="password" value="" size="27" /></td>
          </tr>
          <tr>
			<td align="center" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
		  		<input type="hidden" name="action" value="login" />
				<input type="submit" class="submit" name="login" id="login" value="Log in" />
			</td>
          </tr>
        </table>
      </form>

	</td>
</tr>
</table>
</body>
</html>