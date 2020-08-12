<?php
	session_start();
	
	$captcha_num = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
	$captcha_num = substr(str_shuffle($captcha_num), 0, 6);
	
	$_SESSION['captcha'] = $captcha_num;
	
	$captcha = imagecreatefromjpeg("assets/images/captcha.jpg");
	$color = imagecolorallocate($captcha, 30, 80, 80);
	$font = realpath('code.otf');
	imagettftext($captcha, 30, 10, rand(60, 60), rand(70, 60), $color, $font, $captcha_num );
	imagepng($captcha);
    imagedestroy($captcha);
?>