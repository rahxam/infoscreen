/*!
 * Start Bootstrap - Grayscale Bootstrap Theme (http://startbootstrap.com)
 * Code licensed under the Apache License v2.0.
 * For details, see http://www.apache.org/licenses/LICENSE-2.0.
 */

// jQuery to collapse the navbar on scroll
$(window).scroll(function() {
    if ($(".navbar").offset().top > 50) {
        $(".navbar-fixed-top").addClass("top-nav-collapse");
    } else {
        $(".navbar-fixed-top").removeClass("top-nav-collapse");
    }
});

// jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});

// Closes the Responsive Menu on Menu Item Click
$('.navbar-collapse ul li a').click(function() {
    $('.navbar-toggle:visible').click();
});

$(document).ready(function() {


    $('.article-heading').on('scrollSpy:enter', function() {
        console.log("enter article");
        var article = $(this).parent();
        if($(article).outerHeight() > window.innerHeight) {
            console.log("start scroll");

            setTimeout(function(){ 

                $('html, body').stop().animate({
                scrollTop: $(article).find('span.article-bottom').offset().top - window.innerHeight
            }, (($(article).outerHeight() - window.innerHeight) / 1050) * 90000, 'linear');
            }, 25000);
        }
    });

    $('.article').find('span.article-bottom').on('scrollSpy:enter', function() {
        console.log("enter bottom");
        var article = $(this);
        setTimeout(function(){ 
            showNext();
        }, 35000);
    });

    $('.article').find('span.article-bottom').scrollSpy();

    $('.article-heading').scrollSpy();
});