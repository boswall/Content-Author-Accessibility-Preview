(function( $ ) {

    "use strict";

    $(document).ready( function(){
      var allPagesCheckbox = $('#caa11yp_options_allpages');
      allPagesCheckbox.on('check', function() {
        $('input.depends-allpages').attr('disabled', allPagesCheckbox.is(':checked'));
      }).on('change', function() {
        allPagesCheckbox.trigger('check');
      }).trigger('check');
      $('input.caa11yp_options_user_roles').change(function(){
        if ($('input.caa11yp_options_user_roles:checked').length == 0) {
          $('#setting-error-caa11yp_user_roles').show();
          $('input#submit').attr('disabled', true);
        } else {
          $('#setting-error-caa11yp_user_roles').hide();
          $('input#submit').attr('disabled', false);
        }
      });
      $('input.caa11yp_options_tests').change(function(){
        if ($('input.caa11yp_options_tests:checked').length == 0) {
          $('#setting-error-caa11yp_tests').show();
          $('input#submit').attr('disabled', true);
        } else {
          $('#setting-error-caa11yp_tests').hide();
          $('input#submit').attr('disabled', false);
        }
      });
    });

})(jQuery);
