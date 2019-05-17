(function( $ ) {

    "use strict";

    $(document).ready( function(){
      var allPagesCheckbox = $('#caa11yp_options_allpages');
      allPagesCheckbox.on('check', function() {
        $('input.depends-allpages').attr('disabled', allPagesCheckbox.is(':checked'));
      }).on('change', function() {
        allPagesCheckbox.trigger('check');
      }).trigger('check');
    });

})(jQuery);
