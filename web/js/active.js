(function ($) {
    "use strict";

    jQuery(document).ready(function () {

        //  Slicl nav.js For Responsive Menu

        $('.mainmenu').slicknav({
            prependTo: '.menu_col',
            label: ''
        });

        // this code is for isotope 
        var light_project = $(".light-project-categories li");
        light_project.on('click', function () {

            light_project.removeClass("active");
            $(this).addClass("active");

            var selector = $(this).attr("data-filter");
            $(".all-projects-isotope").isotope({
                filter: selector,
                animationOptions: {
                    duration: 750,
                    easing: "linear",
                    queue: false,
                }
            });

            $(".load-more-projects-wrap").hide();
        });


        $("#light-all-projects-filter").on('click', function () {
            $(".load-more-projects-wrap").show();
        });




    });


    // makes sure the whole site is loaded


    // site preloader -- also uncomment the div in the header and the css style for #preloader
    $(window).on('load', function () {
        $('.loader').fadeOut('slow', function () {
            $(this).remove();
        });

        jQuery(".preloader-wrap").fadeOut(1000, function () {
            $(this).remove();
        });




    });





})(jQuery);