(function ($, Drupal) {
  Drupal.behaviors.objects_map_blocks = {
    attach: function (context, settings) {
      $('.uag-block-objects-map', context).once('objects_map_block').each(function () {
        $('.map', this).vectorMap({
          map: 'ukraine_ua',
          backgroundColor: 'white',
          borderColor: '#ffffff',
          borderOpacity: 0.60,
          borderWidth: 2,
          color: '#4992d0',
          hoverColor: '#63ace0',
          selectedColor: '#98d0f3',
          onLabelShow: function(event, label, code)
          {
            label.html(settings.uag_objects_map_block.render_regions[code]);
          },
        });
      });
    }
  }
})(jQuery, Drupal);
