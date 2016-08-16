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

                verifyMonthsOfYear($thisMonth, $(this));
                verifyDaysOfMonth($thisDay, $thisMonth, $(this));
            });

            // Year changed.
            $year.on('change', function(e) {
                var field_id = ($(e.target).attr('id')).replace('_year', ''),
                    $monthThisYear = $('select#' + field_id + '_month'),
                    $dayThisYear = $('select#' + field_id + '_day');

                verifyMonthsOfYear($monthThisYear, $(this));
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

    function verifyMonthsOfYear(selectMonth, selectYear) {
        var yesterday = new Date(),
            monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ],
            selectedMonth = selectMonth[0].selectedIndex,
            selectedYear = selectYear.val(),
            current_MaxMonthsOfYear = selectMonth.find('option').length-1,
            correct_MaxMonthsOfYear = 12;

        yesterday.setDate(yesterday.getDate()-1);

        if (parseInt(selectedYear) === parseInt(yesterday.getFullYear())) {
            correct_MaxMonthsOfYear = parseInt(yesterday.getMonth()) + 1;
        }

        // Too much months.
        if (current_MaxMonthsOfYear > correct_MaxMonthsOfYear) {
            while (current_MaxMonthsOfYear > correct_MaxMonthsOfYear) {
                selectMonth.children(":last").remove();
                current_MaxMonthsOfYear--;
            }
        // Missing months.
        } else if (current_MaxMonthsOfYear < correct_MaxMonthsOfYear) {
            while (current_MaxMonthsOfYear < correct_MaxMonthsOfYear) {
                var newMonth = parseInt(current_MaxMonthsOfYear) + 1;
                $("<option></option>")
                .attr('value', monthNames[newMonth-1])
                .text(bxcft_months[newMonth-1]).appendTo(selectMonth);
                current_MaxMonthsOfYear++;
            }
        }
    }

    /**
     * This function checks if select of day have correct number of days.
     * @param  jquery selector selectDay   The selectbox of day
     * @param  jquery selector selectMonth The selectbox of month
     * @param  jquery selector selectYear  The selectbox of year
     * @return void
     */
    function verifyDaysOfMonth(selectDay, selectMonth, selectYear) {
        var yesterday = new Date(),
            selectedMonth = selectMonth[0].selectedIndex,
            selectedYear = selectYear.val(),
            current_MaxDaysOfMonth = selectDay.children(':last').val(),
            correct_MaxDaysOfMonth = (new Date(selectedYear, selectedMonth, 0)).getDate();

        yesterday.setDate(yesterday.getDate()-1);

        if (parseInt(selectedYear) === parseInt(yesterday.getFullYear())
            && parseInt(selectedMonth) === parseInt(yesterday.getMonth())+1) {
            correct_MaxDaysOfMonth = yesterday.getDate();
        }

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