(function ($, Drupal) {
  Drupal.behaviors.test = {
    attach: function (context, settings) {
      $('.test', context).once('test').each(function () {
        // Apply the myCustomBehaviour effect to the elements only once.
      });
    }
  };
})(jQuery, Drupal);
