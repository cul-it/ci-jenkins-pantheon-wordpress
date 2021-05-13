(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

var hours = {
  onLoad: function () {
    this.libcalInit();
    this.bindEventListeners();
    this.currentWeek = 0;
    this.currentMonth = 0;
    this.week_month_btn_visibility = false;
    this.monthly_view_visibility = false;
  },
  libcalInit: function () {
    if (typeof lid_weekly != 'undefined') {
      hours.week_month_btn_visibility = false;
      var libcal_lid = [];
      for (var i = 0; i < lid_weekly.length; ++i) {
          libcal_lid[i] = lid_weekly[i];
          var render = "#s-lc-whw-" + libcal_lid[i];
          // var week = new $.LibCalWeeklyGrid( $("#s-lc-whw" + libcal_lid[i]), { iid: 973, lid: libcal_lid[i],  weeks: 52, systemTime: false });
          var week = new $.LibCalWeeklyGrid( $(render), { iid: 973, lid: libcal_lid[i],  weeks: 52, systemTime: false });
      }
    }
    if(typeof lid_monthly != 'undefined'){
      hours.week_month_btn_visibility = false;
      hours.monthly_view_visibility = true;
      var library_id = lid_monthly.libcal_library_id;
      var render = "#s-lc-mhw-" + library_id;
      // var month = new $.LibCalHoursCal( $("#s_lc_mhw_973_" + libcal_library_id), { iid: 973, lid: libcal_library_id, months: 12, systemTime: false });
      var month = new $.LibCalHoursCal( $(render), { iid: 973, lid: library_id, months: 12, systemTime: false });
    }
    if (typeof lid_combined != 'undefined') {
      hours.week_month_btn_visibility = true;
      hours.monthly_view_visibility = false;
      var libcal_library_id = lid_combined.libcal_library_id;
      var render_week = "#s-lc-whw-" + libcal_library_id;
      var render_month = "#s-lc-mhw-" + libcal_library_id;
      var weeks = new $.LibCalWeeklyGrid( $(render_week), { iid: 973, lid: libcal_library_id,  weeks: 52, systemTime: false });
      var months = new $.LibCalHoursCal( $(render_month), { iid: 973, lid: libcal_library_id, months: 12, systemTime: false });
    }
  },
  bindEventListeners: function () {
    $('.js-hours-next').on('click', function () {
      hours.gotoView(view, 'next');
    });

    $('.js-hours-prev').on('click', function () {
      hours.gotoView(view, 'prev');
    });

    $('.js-hours-today').on('click', function () {
      if (view === 'week') {
        hours.gotoView(view, 0);
      } else if (view === 'month') {
        hours.gotoView(view, 0);
      }
    });
    // Hide week month buttons for either weekly or monthly view and show for combined
    if (hours.week_month_btn_visibility == false) {
      $('.hours-weekly__switch-view').hide();
    }
    // Hide monthly view on combined
    if (hours.monthly_view_visibility == false) {
      $('#libcal-monthly-hours').hide();
    }

    var view = 'week';

    $('.js-hours-week').on('click', function () {
      $('#libcal-weekly-hours').transition('show');
      $('#libcal-monthly-hours').transition('hide');
      $(this).addClass('active');
      $('.js-hours-month').removeClass('active');
      view = 'week';
      hours.gotoView(view, hours.currentWeek);
    });

    $('.js-hours-month').on('click', function () {
      $('#libcal-weekly-hours').transition('hide');
      $('#libcal-monthly-hours').transition('show');
      $(this).addClass('active');
      $('.js-hours-week').removeClass('active');
      view = 'month';
      hours.gotoView(view, hours.currentMonth);
    });
  },

  gotoView: function (view, currentView) {
    if (view === 'week') {
      var requestedWeek = null;
      switch (currentView) {
        case 'next':
          requestedWeek = hours.currentWeek + 1;
          break;
        case 'prev':
          requestedWeek = hours.currentWeek - 1;
          break;
        default:
          requestedWeek = currentView;
          break;
      }
      // Hide current & display requested week
      $('.table-responsive').eq(hours.currentWeek).transition('toggle');
      $('.table-responsive').eq(requestedWeek).transition('toggle');

      // Update tracking of current week accordingly
      hours.currentWeek = requestedWeek;
      // Enable/disable appropriate buttons
      hours.housekeeping(view, hours.currentWeek);
    }
    if (view === 'month') {
      var requestedMonth = null;
      switch (currentView) {
        case 'next':
          requestedMonth = hours.currentMonth + 1;
          break;
        case 'prev':
          requestedMonth = hours.currentMonth - 1;
          break;
        default:
          requestedMonth = currentView;
          break;
      }
      $('.s-lc-mhw-c').eq(hours.currentMonth).transition('toggle');
      $('.s-lc-mhw-c').eq(requestedMonth).transition('toggle');
      // Update tracking of current week accordingly
      hours.currentMonth = requestedMonth;
      // Enable/disable appropriate buttons
      hours.housekeeping(view, hours.currentMonth);
    }
  },

  housekeeping: function (view, week) {
    if (view === 'week') {
      // Disable prev & today btns if returning to first week
      if (week === 0) {
        $('.js-hours-prev, .js-hours-today').addClass('disabled');
        $('.js-hours-next').removeClass('disabled');
      // Disable next btn after 52 weeks (zero-based index: 51)
      } else if (week === 51) {
        $('.js-hours-prev, .js-hours-today').removeClass('disabled');
        $('.js-hours-next').addClass('disabled');
      } else {
        $('.js-hours-prev, .js-hours-today, .js-hours-next').removeClass('disabled');
      }
    }
    if (view === 'month') {
      // Disable prev & today btns if returning to first week
      if (week === 0) {
        $('.js-hours-prev, .js-hours-today').addClass('disabled');
        $('.js-hours-next').removeClass('disabled');
      // Disable next btn after 3 month (zero-based index: 2)
      } else if (week === 11) {
        $('.js-hours-prev, .js-hours-today').removeClass('disabled');
        $('.js-hours-next').addClass('disabled');
      } else {
        $('.js-hours-prev, .js-hours-today, .js-hours-next').removeClass('disabled');
      }
    }
  }
};

$(document).ready(function () {
  hours.onLoad();
});


})( jQuery );
