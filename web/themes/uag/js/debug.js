(function ($, Drupal) {
  Drupal.behaviors.uag_debug_main = {
    attach: function (context, settings) {
      $('body', context).once('uag_debug_main').each(function () {
        const body = $(this);
        body.append('<div id="uag_debug_main"><input type="checkbox"></div>');

        const toggle = $('#uag_debug_main input[type="checkbox"]', context);
        toggle.on('click', function (ev) {
          if (ev.currentTarget.checked === true) {
            body.addClass('uag-debug');
          } else {
            body.removeClass('uag-debug');
          }
        })
      });
    }
  };
})(jQuery, Drupal);
