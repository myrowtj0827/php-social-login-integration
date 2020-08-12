
	<li><a href="<?php echo SITE_URL; ?>myaccount.php"><?php echo CBE1_ACCOUNT_HOME; ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>myfavorites.php"><?php echo CBE1_ACCOUNT_FAVORITES; ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>myclicks.php"><?php echo CBE1_ACCOUNT_CLICKS; ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>mybalance.php"><?php echo CBE1_ACCOUNT_BALANCE; ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>invite.php"><?php echo CBE1_ACCOUNT_INVITE; ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>withdraw.php"><?php echo CBE1_ACCOUNT_WITHDRAW; ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>myreviews.php"><?php echo CBE1_ACCOUNT_REVIEWS; ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>mysupport.php"><?php echo CBE1_ACCOUNT_SUPPORT; ?> <?php if (GetMemberMessagesTotal() > 0) { ?> <span class="badge"><?php echo GetMemberMessagesTotal(); ?></span><?php } ?></a></li>
	<li><a href="<?php echo SITE_URL; ?>myprofile.php"><?php echo CBE1_ACCOUNT_PROFILE; ?></a></li>
	<li class="divider"></li>
	<li><a href="<?php echo SITE_URL; ?>logout.php"><?php echo CBE1_ACCOUNT_LOGOUT; ?></a></li>
					