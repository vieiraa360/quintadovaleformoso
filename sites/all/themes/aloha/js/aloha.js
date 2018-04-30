(function ($) {

Drupal.Aloha = Drupal.Aloha || {};

Drupal.behaviors.actionAloha = {
  attach: function (context) {
    $('.btn-btt').smoothScroll({speed: 600});

    Drupal.Aloha.setInputPlaceHolder('search_block_form', 'Keywords', '#search-block-form');
    Drupal.Aloha.setInputPlaceHolder('mail', 'example@weebpal.com', '.block-simplenews');

    if (jQuery('#view-carousel-generic .carousel-indicators li').length > 2) {
      Drupal.Aloha.AddNavImageForSlideshow('left', 2);
      $("#view-carousel-generic").on("slide.bs.carousel", function(event) {
        var current = jQuery('#view-carousel-generic .carousel-indicators li.active').data('slide-to');
        Drupal.Aloha.AddNavImageForSlideshow(event.direction, current);
      });
    }
    Drupal.Aloha.searchBlock();
    if ($(window).width() <= 991) {
        $('#main-menu-inner .region-main-menu').accordionMenu();
      }
      $(window).resize(function(){
        if ($(window).width() <= 991) {
          $('#main-menu-inner .region-main-menu').accordionMenu();
        }
      });


      $("#menu-toggle, .btn-close").click(function(e) {

        var wrapper = $("#page");
        if (!wrapper.hasClass('toggled')) {
          wrapper.addClass('toggled');
          if (wrapper.find('.overlay').length == 0) {
            var overlay = $('<div class="overlay"></div>');
            overlay.prependTo(wrapper);
            overlay.click(function() {
              $('#menu-toggle').trigger('click');
            });
            $('body').css('overflow', 'hidden');
          }

        }
        else {
          var overlay = wrapper.find('.overlay');
          wrapper.css('width', '');
          wrapper.removeClass('toggled');

          if (overlay.length > 0) {
            overlay.remove();
            $('body').css('overflow', '');
          }
        }
        e.preventDefault();
        return false;
      });
  }
};

Drupal.Aloha.AddNavImageForSlideshow = function(direction, current) {
  var total   = jQuery('#view-carousel-generic .carousel-indicators li').length;

  var slideLeft, slideRight;
  if (direction == 'left') {
    current += 1;
    slideLeft  = current - 1;
    slideRight = (current + 1) % total;
  }
  else if (direction == 'right') {
    current += total - 1;
    slideLeft = (current - 1) % total;
    slideRight = (current + 1) % total;
  }

  var rows  = jQuery('#view-carousel-generic .carousel-inner .views-row');
  var left  = rows.eq(slideLeft).find('.slideshow-thumb');
  var right = rows.eq(slideRight).find('.slideshow-thumb');

  var btnNext = jQuery('#view-carousel-generic .right.carousel-control');
  var btnPrev = jQuery('#view-carousel-generic .left.carousel-control');

  btnNext.find('.slideshow-thumb').remove();
  btnPrev.find('.slideshow-thumb').remove();

  btnNext.append(right.clone());
  btnPrev.append(left.clone());
}

Drupal.Aloha.searchBlock = function () {
  if ($('#search-block-form').length) {
    $('#search-block-form').once('load').prepend('<span class="search-icon"><i class="fa fa-search"></i></span>');
    $('#search-block-form .search-icon').once('load').on('click', function(event){
      $('#search-block-form').addClass('active');
      $('#search-block-form input[name="search_block_form"]').trigger('focus');
      event.preventDefault();
      event.stopPropagation();
    });
    $('body').once('load').on('click', function(){
      if ($('#search-block-form').hasClass('active')) {
        $('#search-block-form').removeClass('active');
      }
    });
  }
}

Drupal.Aloha.setInputPlaceHolder = function (name, text, selector) {
  selector = selector == undefined ? '' : selector + ' ';

  if ($.support.placeholder) {
    $(selector + 'input[name="' + name + '"]').attr('placeholder', Drupal.t(text));
  }
  else {
    $(selector + 'input[name="' + name + '"]').val(Drupal.t(text));
    $(selector + 'input[name="' + name + '"]').focus(function(){
      if(this.value == Drupal.t(text)) {
        this.value='';
      }
    }).blur(function(){
      if(this.value == '') {
        this.value=Drupal.t(text);
      }
    });
  }
}

jQuery.support.placeholder = (function(){
    var i = document.createElement('input');
    return 'placeholder' in i;
})();

})(jQuery);
