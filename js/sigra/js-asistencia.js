/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */

function buscarParticipante() {
    var doc = $('#documento').val();
    if (doc !== '') {
        $.ajax({
            type: 'POST',
            url: 'src/asistenciaCB.php',
            data: "accion=buscar_participante&ced=" + doc,
            beforeSend: function () {
            startLoad("carg");
            },
            success: function (data) {
                $("#data").html(data);
            }, complete: function (data) {
                stopLoad("carg");
            }
        });
        return false;
    } else {
        swal({
            title: '¡Digite número de Documento ó un Código de Confirmación!',
            text: '',
            type: 'error',
            confirmButtonColor: '#004669',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }
}

function confirmar(id){
    var confir = $("input[name='tp_conf-"+id+"']:checked").val();
    $.ajax({
        type: 'POST',
        url: 'src/asistenciaCB.php',
        data: "accion=confirmar_asistencia&tp_conf=" + confir + "&insc=" + id,
        beforeSend: function () {
            startLoad("btn");
            $("#confirmar-"+id).slideUp(300);
        },
        success: function (data) {
//            $("#data").html(data);
        }, complete: function (data) {
            stopLoad("btn");
            buscarParticipante();
//            $("#res").addClass("listo");
//            $("#res").html("Confirmado");
        }
    });
    return false;
}