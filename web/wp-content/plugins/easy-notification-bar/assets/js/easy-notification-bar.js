( function() {

	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
		noticeInit();
		noticeClose();
	} );

	function noticeInit() {
		let notice = document.querySelector( '.easy-notification-bar' );
		if ( notice && ! isHidden() ) {
			notice.classList.remove( 'easy-notification-bar--hidden' );
		}
	}

	function noticeClose() {

		document.addEventListener( 'click', (e) => {
			let targetElement = e.target || e.srcElement;
			let toggle = targetElement.closest( '.easy-notification-bar__close' );

			if ( ! toggle ) {
				return;
			}

			e.preventDefault();

			let notice = document.querySelector( '.easy-notification-bar' );

			notice.classList.add( 'easy-notification-bar--hidden' );

			if ( 'undefined' !== typeof localStorage ) {
				localStorage.setItem( 'easy_notification_bar_is_hidden', 'yes' );
			}

		} );

	}

	function isHidden() {
		if ( 'undefined' !== typeof localStorage && 'yes' === localStorage.getItem( 'easy_notification_bar_is_hidden' ) ) {
			return true;
		} else {
			return false;
		}

	}

} )();