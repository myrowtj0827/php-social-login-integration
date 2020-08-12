
</div>
</div>
</div>


    <?php if (SHOW_SITE_STATS == 1) { ?>
       	<div class="container">
	   	<div class="row">
			<h3><?php echo CBE1_BOX_STATS; ?></h3>
				<div class="col-md-3 text-center">
					<?php echo CBE1_BOX_STATS_TITLE1; ?><br/>
					<span><?php echo GetStoresTotal(); ?></span>
				</div>
				<div class="col-md-3 text-center">
					<?php echo CBE1_BOX_STATS_TITLE2; ?><br/>
					<span><?php echo GetCouponsTotal(); ?></span>
				</div>
				<div class="col-md-3 text-center">
					<?php echo CBE1_BOX_STATS_TITLE3; ?><br/>
					<span><?php echo GetUsersTotal(); ?></span>
				</div>
				<div class="col-md-3 text-center">
					<?php echo CBE1_BOX_STATS_TITLE4; ?>
					<span class="allcashback"><?php echo GetCashbackTotal(); ?></span>
				</div>
		</div>
       	</div>
	<?php } ?>
		
		
	<footer id="footer">
	<div class="container">
	<div class="row">

		<div class="col-md-3" id="social">
			<h3 class="hidden-xs"><?php echo CBE1_BOX_FOLLOW; ?></h3>
			<?php if (FACEBOOK_PAGE != "") { ?><a href="<?php echo FACEBOOK_PAGE; ?>" class="facebook_icon" target="_blank" rel="nofollow"></a><?php } ?>
			<?php if (TWITTER_PAGE != "") { ?><a href="<?php echo TWITTER_PAGE; ?>" class="twitter_icon" target="_blank" rel="nofollow"></a><?php } ?>
			<a href="<?php echo SITE_URL; ?>rss.php" class="rss_icon"></a>
		</div>

		<div class="col-md-6 copyright">
			<p>
				<?php echo ShowFooterPages(); ?>
				<a href="<?php echo SITE_URL; ?>aboutus.php"><?php echo CBE1_FMENU_ABOUT; ?></a> | 
				<a href="<?php echo SITE_URL; ?>news.php"><?php echo CBE1_FMENU_NEWS; ?></a> |
				<a href="<?php echo SITE_URL; ?>terms.php"><?php echo CBE1_FMENU_TERMS; ?></a> |
				<a href="<?php echo SITE_URL; ?>privacy.php"><?php echo CBE1_FMENU_PRIVACY; ?></a> |
				<a href="<?php echo SITE_URL; ?>contact.php"><?php echo CBE1_FMENU_CONTACT; ?></a>
			</p>
			<p>&copy; 2020 <?php echo SITE_TITLE; ?>. <?php echo CBE1_FMENU_RIGHTS; ?>.</p>
		</div>

		<!-- Do not remove this copyright notice! -->
		<div class="powered-by-cashbackengine"><a href="https://www.halamiles.com" title="zoonily cashback" target="_blank"><span style="color: #FFF">Halamiles.com</span><span style="color: #FFF"></span></a><div>
		<!-- Do not remove this copyright notice! -->	 
		</div>
	
	</div>
	</div>
	</footer>
	
</div>

	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_URL; ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/autocomplete.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jsCarousel.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/clipboard.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/cashbackengine.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/easySlider1.7.js"></script>
	
	<?php if (isset($ADDTHIS_SHARE) && $ADDTHIS_SHARE == 1) { ?>
		<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#username=<?php echo ADDTHIS_ID; ?>"></script>
	<?php } ?>
	
	<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
		<script type="text/javascript" src="https://connect.facebook.net/en_US/all.js#appId=<?php echo FACEBOOK_APPID; ?>&amp;xfbml=1"></script>
	<?php } ?>	

</body>
</html>