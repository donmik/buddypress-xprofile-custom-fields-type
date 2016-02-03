(function( $ ) {
    'use strict';

    $(function() {
        $('input.bxcft-slider').on('input', function() {
            $('#output-' + $(this).attr('id')).html($(this).val());
        });
    });

})( jQuery );