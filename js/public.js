(function( $ ) {
    'use strict';

    $(function() {
        // Slider.
        if ($('input.bxcft-slider') !== undefined) {
            $('input.bxcft-slider').on('input', function() {
                $('#output-' + $(this).attr('id')).html($(this).val());
            });
        }

        // Birthdate.
        if ($('div.field_birthdate') !== undefined) {
            var $year = $('select.bxcft-birthdate-year');

            // Check when page is loaded.
            $year.each(function(k) {
                var field_id = ($(this).attr('id')).replace('_year', ''),
                    $thisMonth = $('select#' + field_id + '_month'),
                    $thisDay = $('select#' + field_id + '_day');

                verifyDaysOfMonth($thisDay, $thisMonth, $(this));
            });

            // Year changed.
            $year.on('change', function(e) {
                var field_id = ($(e.target).attr('id')).replace('_year', ''),
                    $monthThisYear = $('select#' + field_id + '_month'),
                    $dayThisYear = $('select#' + field_id + '_day');

                verifyDaysOfMonth($dayThisYear, $monthThisYear, $(this));
            });

            // Month changed.
            var $month = $('select.bxcft-birthdate-month');
            $month.on('change', function(e) {
                var field_id = ($(e.target).attr('id')).replace('_month', ''),
                    $yearThisMonth = $('select#' + field_id + '_year'),
                    $dayThisMonth = $('select#' + field_id + '_day');

                verifyDaysOfMonth($dayThisMonth, $(this), $yearThisMonth);
            });
        }
    });

    /**
     * This function checks if select of day have correct number of days.
     * @param  jquery selector selectDay   The selectbox of day
     * @param  jquery selector selectMonth The selectbox of month
     * @param  jquery selector selectYear  The selectbox of year
     * @return void
     */
    function verifyDaysOfMonth(selectDay, selectMonth, selectYear) {
        var selectedMonth = selectMonth[0].selectedIndex,
            selectedYear = selectYear.val(),
            current_MaxDaysOfMonth = selectDay.children(':last').val(),
            correct_MaxDaysOfMonth = (new Date(selectedYear, selectedMonth, 0)).getDate();

        // Too much days.
        if (current_MaxDaysOfMonth > correct_MaxDaysOfMonth) {
            while (current_MaxDaysOfMonth > correct_MaxDaysOfMonth) {
                selectDay.children(":last").remove();
                current_MaxDaysOfMonth--;
            }
        // Missing days.
        } else if (current_MaxDaysOfMonth < correct_MaxDaysOfMonth) {
            while (current_MaxDaysOfMonth < correct_MaxDaysOfMonth) {
                var newDay = parseInt(current_MaxDaysOfMonth) + 1;
                $("<option></option>").attr('value', newDay).text(newDay).appendTo(selectDay);
                current_MaxDaysOfMonth++;
            }
        }
    }

})( jQuery );