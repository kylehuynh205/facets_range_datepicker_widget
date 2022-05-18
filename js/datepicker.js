/**
 * @file
 * Provides the Range datepicker functionality.
 */

(function ($) {

    'use strict';

    Drupal.facets = Drupal.facets || {};

    Drupal.behaviors.facet_datepicker = {
        attach: function (context, settings) {
            //$('.facets-widget-datepicker input[data-type=datepicker-min]', context).on('change', autoSubmit);
            //$('.facets-widget-range_datepicker input[data-type=datepicker-min]', context).on('change', autoRangeSubmit);
            //$('.facets-widget-range_datepicker input[data-type=datepicker-max]', context).on('change', autoRangeSubmit);
            $('.facets-widget-range_datepicker button').click(function () {
                refineSubmit();
            });

            function refineSubmit() {
                console.log("Submit Refining for Date range");

                const $this = $(this);
                const parentDiv = $('.facets-widget-range_datepicker');
                const facetId = parentDiv.find('ul').attr('data-drupal-facet-id');
                // Get url from target facet.
                console.log(facetId);
                const facetUrl =  window.location.href + window.location.search +  "&f[0]=datepicker_date:(min:__range_datepicker_min__,max:__range_datepicker_max__)"; //settings.facets.datepicker[facetId].url;
                console.log(facetUrl);
                let min = parentDiv.find('input[data-type=datepicker-min]').val();
                let max = parentDiv.find('input[data-type=datepicker-max]').val();

                console.log(min);
                console.log(max);

                let redirectUrl = window.location.href;
                if (min > 0 && max > 0) {
                    redirectUrl = facetUrl.replace('__range_datepicker_min__', min).replace('__range_datepicker_max__', max);
                } else if (min > 0 && !max) {
                    redirectUrl = facetUrl.replace('__range_datepicker_min__', min).replace('__range_datepicker_max__', '');
                } else if (!min && max > 0) {
                    redirectUrl = facetUrl.replace('__range_datepicker_min__', '').replace('__range_datepicker_max__', max);
                }


                console.log(redirectUrl);
                window.location.href = redirectUrl;
            }

            function autoSubmit() {
                const $this = $(this);
                const facetId = $this.parents('.facets-widget-datepicker').find('ul').attr('data-drupal-facet-id');
                const facetUrl = settings.facets.datepicker[facetId].url;
                let datePickerValue = toTimestamp($this.parents('.facets-widget-datepicker').find('input[data-type=datepicker-min]').val());
                let redirectUrl = window.location.href;
                if (datePickerValue) {
                    redirectUrl = facetUrl.replace('__datepicker_min__', datePickerValue);
                }
                window.location.href = redirectUrl;
            }

            function autoRangeSubmit() {
                const $this = $(this);
                const parentDiv = $this.parents('.facets-widget-range_datepicker');
                const facetId = parentDiv.find('ul').attr('data-drupal-facet-id');
                // Get url from target facet.
                const facetUrl = settings.facets.datepicker[facetId].url;
                let min = toTimestamp(parentDiv.find('input[data-type=datepicker-min]').val());
                let max = toTimestamp(parentDiv.find('input[data-type=datepicker-max]').val());
                let redirectUrl = window.location.href;
                if (min > 0 && max > 0) {
                    redirectUrl = facetUrl.replace('__range_datepicker_min__', min).replace('__range_datepicker_max__', max);
                } else if (min > 0 && !max) {
                    redirectUrl = facetUrl.replace('__range_datepicker_min__', min).replace('__range_datepicker_max__', '');
                } else if (!min && max > 0) {
                    redirectUrl = facetUrl.replace('__range_datepicker_min__', '').replace('__range_datepicker_max__', max);
                }
                window.location.href = redirectUrl;
            }

            function toTimestamp(strDate) {
                let datum = Date.parse(strDate);
                return datum / 1000;
            }
        }
    };

})(jQuery);
