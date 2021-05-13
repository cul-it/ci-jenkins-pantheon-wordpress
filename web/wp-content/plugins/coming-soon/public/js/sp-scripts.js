"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*! js-cookie v3.0.0-rc.0 | MIT */
!function (e, t) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = e || self, function () {
    var r = e.Cookies,
        n = e.Cookies = t();

    n.noConflict = function () {
      return e.Cookies = r, n;
    };
  }());
}(void 0, function () {
  "use strict";

  function e(e) {
    for (var t = 1; t < arguments.length; t++) {
      var r = arguments[t];

      for (var n in r) {
        e[n] = r[n];
      }
    }

    return e;
  }

  var t = {
    read: function read(e) {
      return e.replace(/%3B/g, ";");
    },
    write: function write(e) {
      return e.replace(/;/g, "%3B");
    }
  };
  return function r(n, i) {
    function o(r, o, u) {
      if ("undefined" != typeof document) {
        "number" == typeof (u = e({}, i, u)).expires && (u.expires = new Date(Date.now() + 864e5 * u.expires)), u.expires && (u.expires = u.expires.toUTCString()), r = t.write(r).replace(/=/g, "%3D"), o = n.write(String(o), r);
        var c = "";

        for (var f in u) {
          u[f] && (c += "; " + f, !0 !== u[f] && (c += "=" + u[f].split(";")[0]));
        }

        return document.cookie = r + "=" + o + c;
      }
    }

    return Object.create({
      set: o,
      get: function get(e) {
        if ("undefined" != typeof document && (!arguments.length || e)) {
          for (var r = document.cookie ? document.cookie.split("; ") : [], i = {}, o = 0; o < r.length; o++) {
            var u = r[o].split("="),
                c = u.slice(1).join("="),
                f = t.read(u[0]).replace(/%3D/g, "=");
            if (i[f] = n.read(c, f), e === f) break;
          }

          return e ? i[e] : i;
        }
      },
      remove: function remove(t, r) {
        o(t, "", e({}, r, {
          expires: -1
        }));
      },
      withAttributes: function withAttributes(t) {
        return r(this.converter, e({}, this.attributes, t));
      },
      withConverter: function withConverter(t) {
        return r(e({}, this.converter, t), this.attributes);
      }
    }, {
      attributes: {
        value: Object.freeze(i)
      },
      converter: {
        value: Object.freeze(n)
      }
    });
  }(t, {
    path: "/"
  });
});
var seedprodCookies = Cookies.noConflict(); // optin form

var sp_emplacementRecaptcha = [];
var sp_option_id = "";
jQuery("form[id^=sp-optin-form]").submit(function (e) {
  e.preventDefault();
  var form_id = jQuery(this).attr("id");
  var id = form_id.replace("sp-optin-form-", "");

  if (seeprod_enable_recaptcha === 1) {
    grecaptcha.execute(sp_emplacementRecaptcha[id]);
  } else {
    var token = "";
    sp_send_request(token, id);
  }
});

var sp_CaptchaCallback = function sp_CaptchaCallback() {
  jQuery("div[id^=recaptcha-]").each(function (index, el) {
    sp_option_id = el.id.replace("recaptcha-", "");
    sp_emplacementRecaptcha[sp_option_id] = grecaptcha.render(el, {
      sitekey: "6LdfOukUAAAAAMCOEFEZ9WOSKyoYrxJcgXsf66Xr",
      badge: "bottomright",
      type: "image",
      size: "invisible",
      callback: function callback(token) {
        sp_send_request(token, sp_option_id);
      }
    });
  });
};

function sp_send_request(token, id) {
  var data = jQuery("#sp-optin-form-" + id).serialize();
  var j1 = jQuery.ajax({
    url: seedprod_api_url + "subscribers",
    type: "post",
    dataType: "json",
    timeout: 5000,
    data: data
  }); // add ajax class

  jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').addClass('sp-ajax-striped sp-ajax-animated'); //var j2 = jQuery.ajax( "/" );

  var j2 = jQuery.ajax({
    url: sp_subscriber_callback_url,
    type: 'post',
    timeout: 30000,
    data: data
  });
  jQuery.when(j1, j2).done(function (a1, a2) {
    // take next action
    var action = jQuery("#sp-optin-form-" + id + " input[name^='seedprod_action']").val(); // show success message

    if (action == "1") {
      jQuery("#sp-optin-form-" + id).hide();
      jQuery("#sp-optin-success-" + id).show();
    } // redirect


    if (action === "2") {
      var redirect = jQuery("#sp-optin-form-" + id + " input[name^='redirect_url']").val();
      window.location.href = redirect;
    }

    jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').removeClass('sp-ajax-striped sp-ajax-animated'); // alert( "We got what we came for!" );
  }).fail(function (jqXHR, textStatus, errorThrown) {
    jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').removeClass('sp-ajax-striped sp-ajax-animated');

    if (seeprod_enable_recaptcha === 1) {
      grecaptcha.reset(sp_emplacementRecaptcha[id]);
    } // var response = JSON.parse(j1.responseText);
    // var errorString  = '';
    // jQuery.each( response.errors, function( key, value) {
    //     errorString +=  value ;
    // });
    // alert(errorString);
    // console.log(j1);
    // console.log(j2);

  });
  return;
} // countdown


var x = [];

function countdown(type, ts, id, action, redirect) {
  var now = new Date().getTime();

  if (type == 'vt') {
    ts = ts + now; //console.log(ts);

    var seedprod_enddate = seedprodCookies.get('seedprod_enddate_' + id);

    if (seedprod_enddate != undefined) {
      ts = seedprod_enddate;
      seedprodCookies.set('seedprod_enddate_' + id, ts, {
        expires: 360
      });
    }
  } // Update the count down every 1 second


  x[id] = setInterval(function () {
    var now = new Date().getTime();
    var distance = ts - now;
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));
    var minutes = Math.floor(distance % (1000 * 60 * 60) / (1000 * 60));
    var seconds = Math.floor(distance % (1000 * 60) / 1000);

    if (days == 0) {
      jQuery("#sp-cd-days-" + id).hide();
    } else {
      jQuery("#sp-cd-days-" + id + " .sp-cd-amount").html(pad(days, 2));
    }

    jQuery("#sp-cd-hours-" + id + " .sp-cd-amount").html(pad(hours, 2));
    jQuery("#sp-cd-minutes-" + id + " .sp-cd-amount").html(pad(minutes, 2));
    jQuery("#sp-cd-seconds-" + id + " .sp-cd-amount").html(pad(seconds, 2)); //   document.getElementById(id).innerHTML = days + "d " + pad(hours,2) + "h "
    //   + pad(minutes,2) + "m " + pad(seconds,2) + "s ";
    // If the count down is finished, write some text

    if (distance < 0) {
      clearInterval(x[id]); // show success message

      if (action == "1") {
        jQuery("#sp-countdown-" + id + " .sp-countdown-group").hide();
        jQuery("#sp-countdown-expired-" + id).show();
      } // redirect


      if (action == "2") {
        jQuery("#sp-countdown-" + id + " .sp-countdown-group").hide();
        window.location.href = redirect;
      }
    }
  }, 1000);
}

function pad(n, width, z) {
  z = z || "0";
  n = n + "";
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
} // remove any theme css


jQuery(document).ready(function ($) {
  $('link[href*="/wp-content/themes/"]').remove();
}); // Dynamic Text
// jQuery(document).ready(function ($) {
// 	var default_format = "{MM}/{dd}/{yyyy}";
// 	var html = $("#sp-page").html();
// 	var newTxt = html.split("[#");
// 	for (var i = 1; i < newTxt.length; i++) {
// 		var format = default_format;
// 		var tag = newTxt[i].split("]")[0];
// 		var parts = tag.split(":");
// 		if (parts.length > 1) {
// 			format = parts[1];
// 		} else {
// 			format = default_format;
// 		}
// 		var d = Date.create(parts[0]);
// 		var regex = "\\[#" + tag + "]";
// 		var re = new RegExp(regex, "g");
// 		$("#sp-page *").replaceText(re, d.format(format));
// 	}
// });

/*!-----------------------------------------------------------------------------
 * seedprod_bg_slideshow()
 * ----------------------------------------------------------------------------
 * Example:
 * seedprod_bg_slideshow('body', ['IMG_URL', 'IMG_URL', 'IMG_URL'], 3000);
 * --------------------------------------------------------------------------*/

function seedprod_bg_slideshow(selector, slides) {
  var delay = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 5000;
  var transition_timing = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'ease-in';
  var transition_duration = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 500;
  document.querySelector(selector).style.backgroundSize = "cover";
  document.querySelector(selector).style.backgroundRepeat = "no-repeat";
  document.querySelector(selector).style.backgroundPosition = "center center"; // Set transitions

  var transition = "all " + transition_duration + 'ms ' + transition_timing;
  document.querySelector(selector).style.WebkitTransition = transition;
  document.querySelector(selector).style.MozTransition = transition;
  document.querySelector(selector).style.MsTransition = transition;
  document.querySelector(selector).style.OTransition = transition;
  document.querySelector(selector).style.transition = transition;
  var currentSlideIndex = 0; // Load first slide

  document.querySelector(selector).style.backgroundImage = "url('" + slides[currentSlideIndex] + "')";
  currentSlideIndex++; // Load next slide every interval

  setInterval(function () {
    document.querySelector(selector).style.backgroundImage = "url('" + slides[currentSlideIndex] + "')";
    currentSlideIndex++; // Reset counter

    if (currentSlideIndex >= slides.length) {
      currentSlideIndex = 0;
    }
  }, delay); // Preload slideshow images

  var preloadImages = new Array();
  slides.forEach(function (val, i) {
    preloadImages[i] = new Image();
    preloadImages[i].src = val;
  });
}

jQuery('.sp-testimonial-nav button').click(function () {
  var currentId = '#' + jQuery(this).parents('.sp-testimonials-wrapper').attr('id');
  var currentButtonIndex = jQuery(currentId + ' .sp-testimonial-nav button').index(this);
  var currentIndex = 0;
  var testimonials = jQuery('.sp-testimonial-wrapper', jQuery(this).parents(currentId));
  jQuery(testimonials).each(function (index) {
    var o = jQuery(this).css('opacity');

    if (o == 1) {
      currentIndex = index;
    }
  });
  var buttonsLength = jQuery(currentId + ' .sp-testimonial-nav button').length - 1;
  var currentButtonIndexData = jQuery(currentId + ' .sp-testimonial-nav button').eq(currentButtonIndex).attr('data-index'); // check for previous button click

  if (currentButtonIndex == 0) {
    if (0 == currentIndex) {
      currentIndex = testimonials.length - 1;
    } else {
      currentIndex--;
    }
  } // check for next button click


  if (currentButtonIndex == buttonsLength) {
    if (testimonials.length - 1 == currentIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
  } // reset states


  testimonials.css({
    'opacity': 0,
    'height': '0',
    'position': 'absolute'
  });
  jQuery(currentId + ' .sp-testimonial-nav button[data-index]').css({
    'opacity': 0.25
  }); // select testimonial and button

  if (currentButtonIndexData !== undefined) {
    currentIndex = currentButtonIndexData;
    jQuery(testimonials).eq(currentIndex).css({
      'opacity': 1,
      'height': 'auto',
      'position': 'initial'
    });
    jQuery(currentId + ' .sp-testimonial-nav button').eq(currentButtonIndex).css({
      'opacity': 1
    });
  } else {
    jQuery(testimonials).eq(currentIndex).css({
      'opacity': 1,
      'height': 'auto',
      'position': 'initial'
    });
    jQuery(currentId + ' .sp-testimonial-nav button').eq(currentIndex + 1).css({
      'opacity': 1
    });
  }
});
var testimonial_timers = {};
jQuery(".sp-testimonials-wrapper").each(function (index) {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    testimonial_timers[currentId] = setInterval(function () {
      jQuery(currentId + ' .sp-testimonial-nav button:last-child').trigger('click');
    }, speed);
  }
});
jQuery(".sp-testimonials-wrapper").hover(function () {
  var id = '#' + jQuery(this).attr('id');
  clearInterval(testimonial_timers[id]);
});
jQuery(".sp-testimonials-wrapper").mouseleave(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    testimonial_timers[currentId] = setInterval(function () {
      jQuery(currentId + ' .sp-testimonial-nav button:last-child').trigger('click');
    }, speed);
  }
});