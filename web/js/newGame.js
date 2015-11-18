$.datetimepicker.setLocale('es');

$(function () {

    $("#new_game_fin").each(function () {
        $(this).attr("readonly", "readonly");
    });
    $('#new_game_fin_date').datetimepicker({
        format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        mask: '9999/19/39',
        minDate: 'Date.now()'
    });
    $('#new_game_fin_time').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step: 5,
        mask: '29:59'
    });

    $('#new_game_fin_time').addClass("form-control text-center");
    $('#new_game_fin_date').addClass("form-control text-center");
});