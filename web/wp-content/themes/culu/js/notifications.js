


/**
 * Notification functionality.


(function ($, root, undefined) {

	$(function () {

		if($('.easy-notification-bar-container').is(':visible')) {

			$(window).resize(function(){location.reload();});

			var heightNotificationBanner = parseInt($(".easy-notification-bar").css("padding-top")) + parseInt($(".easy-notification-bar").css("padding-bottom")) + parseInt($(".easy-notification-bar-button a").height()) + parseInt($(".easy-notification-bar-button a").css("padding-top")) + parseInt($(".easy-notification-bar-button a").css("padding-bottom")) + parseInt($(".easy-notification-bar-button").css("padding-top")) + parseInt($(".easy-notification-bar-button").css("padding-bottom"));


			var branding = $('header.branding');
			brandingHeight = parseInt(branding.css("margin-top"))+ parseInt(branding.css("margin-bottom")) + parseInt($('header.branding .logo-cul').css("padding-top")) + parseInt($('header.branding .logo-cul img').height());


			if (window.matchMedia("(min-width: 640px)").matches) {

				var positionHeroBackground = heightNotificationBanner + 150;
				$('.bg-header').css("top", positionHeroBackground + "px");

			} else {
				
				var positionMenuMobile = heightNotificationBanner + brandingHeight;
				$('.main-navigation').css("top", positionMenuMobile +  "px");

				var positionMenuIcon = positionMenuMobile - 198;
				$('.menu-toggle').css("top", positionMenuIcon + "px");

				var positionHeroBackground = heightNotificationBanner + 130;
				$('.bg-header').css("top", positionHeroBackground + "px");
	
			}

		}

	});

})(jQuery, this);

 */