$(document).ready(function() {
    $("#flip").click(function() {
        $("#panel-inner").slideToggle("slow");
        $("#flip").toggleClass('flip img');
    });
});