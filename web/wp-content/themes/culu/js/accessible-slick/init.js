/**
 * Featured news slider
 * Accessible slick slider
 * https://github.com/Accessible360/accessible-slick
 * Samples: https://codepen.io/collection/nwRGZk
 */

(function ($, root, undefined) {

  $(function () {

    // Position dynamically navigation dot under featured news image

    var $featuredNewsPhoto = $('.fearuted-news-photo');
    $featuredNewsPhoto.load(function () {
      $('.slick-dots').css('top', parseInt($featuredNewsPhoto.height()) + 20 + 'px');
    });

    $(window).resize(function () {
      $featuredNewsPhoto = $('.fearuted-news-photo');
      $('.slick-dots').css('top', parseInt($featuredNewsPhoto.height()) + 20 + 'px');
    });

    // Accessible Slick setup

    $(document).ready(function () {

      $('.hero-slider').slick({
        autoplay: false,
        autoplaySpeed: 5000,
        dots: true,
        arrows: true,
        arrowsPlacement: 'beforeSlides',
        prevArrow: '<button type="button" class="custom-prev-button">'
          + ' <i class="fas fa-chevron-left" aria-hidden="true"></i>'
          + '  <span class="sr-only">Previous slide</span>'
          + '</button>',
        nextArrow: '<button type="button" class="custom-next-button">'
          + ' <i class="fas fa-chevron-right" aria-hidden="true"></i>'
          + '  <span class="sr-only">Next slide</span>'
          + '</button>',
        pauseIcon: '<span class="la la-pause" aria-hidden="true"></span>',
        playIcon: '<span class="la la-play" aria-hidden="true"></span>'
      });
    });

  });
})(jQuery, this);
