jQuery(window).load(function () {
    jQuery('.flexslider').flexslider({
        animation: "slide",
        smoothHeight: true, /* for adjusting height for small images */
        animationLoop: false,
        start: function (slider) {
            jQuery('body').removeClass('loading');
        }
    });

    jQuery("#multiple").select2({
        width: '100%'
    });

    // jQuery(document).find(".invite-user .modal_close").click(function () {
    //     jQuery(".invite-user").attr("style", "");
    //     jQuery(".reveal-modal-bg").css("display", "none");
    //     jQuery(".invite-user").removeClass("open");
    // });

    jQuery(document).on('click',".modal_close",function () {
      
       var parent = jQuery(this).parent('div');
       jQuery(parent).attr("style", "");
       jQuery(".reveal-modal-bg").css("display", "none");
       jQuery(parent).removeClass("open");
   });
});

jQuery(document).ready(function () {

    jQuery('#start-date').datepicker({
        format: 'mm-dd-yyyy'
    });
    jQuery('#end-date').datepicker({
        format: 'mm-dd-yyyy'
    });
    jQuery('#event-time').timepicker({
        showSeconds: true
    });
    jQuery('#birth-date').datepicker({
       format: 'mm-dd-yyyy'
    });
    jQuery("[class='make-switch']").bootstrapSwitch();

    var todayDate = moment().startOf('day');
    var YM = todayDate.format('YYYY-MM');
    var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
    var TODAY = todayDate.format('YYYY-MM-DD');
    var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

    jQuery('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listWeek'
        },
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        navLinks: true,
        events: [
            {
                title: 'All Day Event',
                start: YM + '-01'
            },
            {
                title: 'Long Event',
                start: YM + '-07',
                end: YM + '-10'
            },
            {
                id: 999,
                title: 'Repeating Event',
                start: YM + '-09T16:00:00'
            },
            {
                id: 999,
                title: 'Repeating Event',
                start: YM + '-16T16:00:00'
            },
            {
                title: 'Conference',
                start: YESTERDAY,
                end: TOMORROW
            },
            {
                title: 'Meeting',
                start: TODAY + 'T10:30:00',
                end: TODAY + 'T12:30:00'
            },
            {
                title: 'Lunch',
                start: TODAY + 'T12:00:00'
            },
            {
                title: 'Meeting',
                start: TODAY + 'T14:30:00'
            },
            {
                title: 'Happy Hour',
                start: TODAY + 'T17:30:00'
            },
            {
                title: 'Dinner',
                start: TODAY + 'T20:00:00'
            },
            {
                title: 'Birthday Party',
                start: TOMORROW + 'T07:00:00'
            },
            {
                title: 'Click for Google',
                url: 'http://google.com/',
                start: YM + '-28'
            }
        ]
    });
});


jQuery(window).load(function () {
    /* Code start for map reload and center it on category page after tabs changing */
    jQuery(function () {
        jQuery(document).on('click', "ul.view_mode li #locations_map", function () {
            google.maps.event.trigger(map, 'resize');
            map.fitBounds(bounds);
            var center = bounds.getCenter();
            map.setCenter(center);
        });
    });
    /* Code end */

    if (jQuery('#locations_map').hasClass('active'))
    {
        jQuery('.tev_sorting_option').css('display', 'none');
        jQuery('#directory_sort_order_alphabetical').css('display', 'none');
    } else
    {
        jQuery('.tev_sorting_option').css('display', '');
        jQuery('#directory_sort_order_alphabetical').css('display', '');
    }
    jQuery('.viewsbox a.listview').click(function (e) {
        jQuery('.tev_sorting_option').css('display', '');
        jQuery('#directory_sort_order_alphabetical').css('display', '');
    });
    jQuery('.viewsbox a.gridview').click(function (e) {
        jQuery('.tev_sorting_option').css('display', '');
        jQuery('#directory_sort_order_alphabetical').css('display', '');
    });
    jQuery('.viewsbox a#locations_map').click(function (e) {
        jQuery('.tev_sorting_option').css('display', 'none');
        jQuery('#directory_sort_order_alphabetical').css('display', 'none');
    });
});

function invite_form() {
    jQuery(".invite-user #invite-popup").show();
}