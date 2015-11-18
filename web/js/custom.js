$(document).ready(function() {
    $('.parent').click(function() {
        $('.sub-nav').toggleClass('visible');
    });

    $('[data-toggle="tooltip"]').tooltip();
    $('.dropdown-toggle').dropdown();

    var body = document.body, html = document.documentElement;
    var height = Math.max( body.scrollHeight, body.offsetHeight,
        html.clientHeight, html.scrollHeight, html.offsetHeight );

    $("#column-full").css("height", height - 150);

    if ($(window).width() <= 768){
        //document.getElementById('table-mobile-full').removeAttribute('class');
        document.getElementById('table-mobile-full').addClass('container-fluid)');
    }
});

$(".navbar-toggle").click(function () {
    $content = $(".navbar-main-collapse");
    //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
    $content.slideToggle(500);
});