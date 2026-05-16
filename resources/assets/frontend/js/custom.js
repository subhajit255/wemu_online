

// Stellar Nav
jQuery(document).ready(function($) {
    jQuery('.stellarnav').stellarNav({
        theme: 'light',
        breakpoint: 991,
        position: 'right',
        // phoneBtn: '18009997788',
        // locationBtn: 'https://www.google.com/maps'
    });
});

// Sticky Header Script
// var header = document.querySelector('.header');
// onScroll = () => {
//   var scrolledPage = Math.round(window.pageYOffset);
//   if(scrolledPage > 60) {
//     header.classList.add('sticky');
//   } else {
//     header.classList.remove('sticky');
//   }
// }
// document.addEventListener('scroll', onScroll); 

//
window.addEventListener('load', function() {
    const boxes = document.querySelectorAll('#equalfeatureBox');
    let maxHeight = 0;

    // Reset heights
    boxes.forEach(box => {
        box.style.height = 'auto'; // Reset height to auto to get natural height
    });

    // Find the maximum height
    boxes.forEach(box => {
        maxHeight = Math.max(maxHeight, box.offsetHeight);
    });

    // Set all boxes to the maximum height
    boxes.forEach(box => {
        box.style.height = `${maxHeight}px`;
    });
});



window.addEventListener('load', function() {
    const boxes = document.querySelectorAll('.howItWorkBox');
    let maxHeight = 0;

    // Reset heights
    boxes.forEach(box => {
        box.style.height = 'auto'; // Reset height to auto to get natural height
    });

    // Find the maximum height
    boxes.forEach(box => {
        maxHeight = Math.max(maxHeight, box.offsetHeight);
    });

    // Set all boxes to the maximum height
    boxes.forEach(box => {
        box.style.height = `${maxHeight}px`;
    });
});







// AOS Js
// AOS.init({
//     easing: 'ease-in-out-sine'
//   });

// Go to Top
$(function () {
// Scroll Event
$(window).on("scroll", function () {
    var scrolled = $(window).scrollTop();
    if (scrolled > 600) $(".go-top").addClass("active");
    if (scrolled < 600) $(".go-top").removeClass("active");
});
// Click Event
$(".go-top").on("click", function () {
    $("html, body").animate({ scrollTop: "0" }, 500);
});
});






