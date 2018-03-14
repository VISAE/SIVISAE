/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */

$(document).ready(function () {
    $(".encuesta").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slideLeft",
        stepsOrientation: "vertical",
        //                        enableAllSteps: true,
        labels: {
            finish: "Guardar",
            loading: "Cargando..."
        },
        onStepChanging: function (event, currentIndex, newIndex) {
//            $('.wizard .content').css("height", '900px');
            document.getElementById("p_fieldset_autenticacion_2").scrollIntoView(true);
//            if (currentIndex === 0) {
//                $('.wizard .content').css("height", '1000px');
//            }
            
                return true;
        },
        onFinishing: function (event, currentIndex) {
            var alertas = validarCampos(currentIndex);
            if (alertas.length > 0) {
                var alerta = alertas.join('<br>');
                swal({
                    title: 'Falta información!',
                    html: 'Tiene campos vacíos: <br>' + alerta,
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            } else {
                actualizarGraduado();
            }
        }
    });
});