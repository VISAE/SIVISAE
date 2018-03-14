var limite = 200;

$(document).ready(function () {
    $("#observacion").keyup(function (e) {
        var box = $(this).val();
        var value = (box.length * 100) / limite;
        var resta = limite - box.length;
        if (box.length <= limite) {
            $('#divContador').html(resta);
            $('#divProgreso').animate({
                "width": value + '%'
            }, 1);
            if (value < 50) {
                $('#divProgreso').removeClass();
                $('#divProgreso').addClass('verde');
            }
            else if (value < 85) { // si no se llegó al 85% que sea amarilla
                $('#divProgreso').removeClass();
                $('#divProgreso').addClass('amarillo');
            }
            else { // si se superó el 85% que sea roja
                $('#divProgreso').removeClass();
                $('#divProgreso').addClass('rojo');
            }
            ;
        }
        else {
            e.preventDefault();
        }
    });
});