/* (c) 2010-2017 CashbackEngine */

//--------(autocomplete)-----------------------------------------------------------------------------------------------

function formatItem(row) {
		return '<div style=position:relative;float:left>' + row[0] + '</div>';
}

function seacrhSubmit() {
	document.forms['searchfrm'].submit();
}

function facebook_login() {
	FB.login(function(response) {
		window.location = '../fblogin.php';
		if (response.session) {
			// Log.info('User is logged in');
		} else {
			// Log.info('User is logged out');
		}
	}, {scope: 'public_profile,email'});
}

///---------(autocomplete)---------------------------------------------------------------------------------------------

$(document).ready(function () {
	$('#searchfrm').submit(function() {
		if ($.trim($("#searchtext").val()) === "") {
			return false;
		}
	});
	$('html').bind('click', function() {
		 $('.searchhere').fadeOut();
	 });
	$('html:not(.list)').trigger('click'); 
});

function ajaxsearch(what) {
	 $.ajax({
			type: "GET",
			url: "../autocomplete.php",
			data: {q:what},
			cache: false,
			success: function(data)
			{
				if (what == '' || data.length <= 1)
				{
					 $('.searchhere').fadeOut();
				}
				else
				{
					 $('.searchhere').fadeIn();
					 $('.searchhere').html(data);
				}
			}
	 });			
}

///---------(tooltip)--------------------------------------------------------------------------------------------------

$(document).ready(function() {
	    $(".cashbackengine_tooltip").hover(
	        function() { $(this).contents("span:last-child").css({ display: "block" }); },
	        function() { $(this).contents("span:last-child").css({ display: "none" }); }
	    );
	    $(".cashbackengine_tooltip").mousemove(function(e) {
	        var mousex = e.pageX + 10;
	        var mousey = e.pageY + 10;
	        $(this).contents("span:last-child").css({  top: mousey, left: mousex });
	    });
});

///---------(slider)-------------------------------------------------------------------------------------------------------

$(document).ready(function(){	
	$("#slider").easySlider({
		auto: true, 
		continuous: true,
		numeric: true
	});
});	

///---------(top)-------------------------------------------------------------------------------------------------------

 $(document).ready(function() {
	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('.scrollup').fadeIn();
		} else {
			$('.scrollup').fadeOut();
		}
		});
 
		$('.scrollup').click(function() {
			$("html, body").animate({ scrollTop: 0 }, 600);
			return false;
		});
});

///---------(tabs)------------------------------------------------------------------------------------------------------

$(document).ready(function(){

	$(".tab_content").hide(); // Hide all content
	$("#tabs li:first").addClass("active").show(); // Activate first tab
	$(".tab_content:first").show(); // Show first tab content

	$("#tabs li").click(function() {
		//	First remove class "active" from currently active tab
		$("#tabs li").removeClass('active');

		//	Now add class "active" to the selected/clicked tab
		$(this).addClass("active");

		//	Hide all tab content
		$(".tab_content").hide();

		//	Here we get the href value of the selected tab
		var selected_tab = $(this).find("a").attr("href");

		//	Show the selected tab content
		$(selected_tab).fadeIn();
		return false;
	});
});


$(document).ready(function(){
    $('.cashbackengine_tooltip').tooltip();
});

///---------(scroll)--------------------------------------------------------------------------------------------------------

$(document).ready(function() {
	$('#scrollstores').jsCarousel({ autoscroll: true, circular: true, masked: false, itemstodisplay: 6, orientation: 'h' });
});

$(document).ready(function() {
	$("#next-button").click(function () {
	  $("#hide-text-block").toggle("slow");
	  $("#next-button").hide();
	  $("#prev-button").show();
	});
	$("#prev-button").click(function () {
	 $("#hide-text-block").hide();
	 $("#prev-button").hide();
	 $("#next-button").show();
	});
});


(function( $ ) {

    //Function to animate slider captions 
	function doAnimations( elems ) {
		//Cache the animationend event in a variable
		var animEndEv = 'webkitAnimationEnd animationend';
		
		elems.each(function () {
			var $this = $(this),
				$animationType = $this.data('animation');
			$this.addClass($animationType).one(animEndEv, function () {
				$this.removeClass($animationType);
			});
		});
	}
	
	//Variables on page load 
	var $myCarousel = $('#carousel-example-generic'),
		$firstAnimatingElems = $myCarousel.find('.item:first').find("[data-animation ^= 'animated']");
		
	//Initialize carousel 
	$myCarousel.carousel();
	
	//Animate captions in first slide on page load 
	doAnimations($firstAnimatingElems);
	
	//Pause carousel  
	$myCarousel.carousel('pause');
	
	
	//Other slides to be animated on carousel slide event 
	$myCarousel.on('slide.bs.carousel', function (e) {
		var $animatingElems = $(e.relatedTarget).find("[data-animation ^= 'animated']");
		doAnimations($animatingElems);
	});  
    $('#carousel-example-generic').carousel({
        interval:3000,
        pause: "false"
    });
	
})(jQuery);	

