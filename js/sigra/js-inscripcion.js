/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */
function startLoad(div) {
    $('#' + div).show();
    if (div !== "carg") {
        $("#" + div).introLoader({
            animation: {
                name: 'simpleLoader',
                options: {
                    stop: false,
                    fixed: false,
                    exitFx: 'fadeOut',
                    ease: "linear",
                    style: 'light',
                    customGifBgColor: '#E8E8E8'
                }
            },
            spinJs: {
                lines: 13, // The number of lines to draw 
                length: 10, // The length of each line 
                width: 5, // The line thickness 
                radius: 10, // The radius of the inner circle 
                corners: 1, // Corner roundness (0..1) 
                color: '#004669' // #rgb or #rrggbb or array of colors 
            }
        });
    } else {
        $("#" + div).introLoader({
            animation: {
                name: 'simpleLoader',
                options: {
                    stop: false,
                    fixed: false,
                    exitFx: 'fadeOut',
                    ease: "linear",
                    style: 'light'
                }
            },
            spinJs: {
                lines: 13, // The number of lines to draw 
                length: 30, // The length of each line 
                width: 10, // The line thickness 
                radius: 30, // The radius of the inner circle 
                corners: 1, // Corner roundness (0..1) 
                color: '#004669' // #rgb or #rrggbb or array of colors 
            }
        });
    }
}
function stopLoad(div) {
//                    $('#list_graduados').show();
    $('#' + div).hide();
    var loader = $('#' + div).data('introLoader');
    loader.stop();
}

function limpiaForm(miForm) {
    // recorremos todos los campos que tiene el formulario
    
    $(':input', miForm).each(function () {
        var type = this.type;
        var tag = this.tagName.toLowerCase();
        //limpiamos los valores de los campos…
        if (type === 'text' || type === 'password' || tag === 'textarea' || type === 'number' || type==='file'){
            this.value = "";
            $(this).removeClass("error");
        }
        // excepto de los checkboxes y radios, le quitamos el checked
        // pero su valor no debe ser cambiado
        else if (type === 'checkbox' || type === 'radio')
            this.checked = false;
        // los selects le ponesmos el indice a -
        else if (tag === 'select')
        {
            this.selectedIndex = 0;
            $(this).val('').trigger("chosen:updated");
//            $(this).chosen('destroy');
//            $(this).chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
//            $(this).prop('selectedIndex', 0);
        }
    });
}

function buscarParticipante() {
    var doc = $('#documento').val();
    if (doc !== '') {
        $('#buscarParticipante').attr("disabled", "disabled");
        $('#buscarParticipante').addClass("disable");

        $.ajax({
            type: 'POST',
            url: 'src/inscripcionCB.php',
            data: "accion=buscar_participante&ced=" + doc,
            dataType: "JSON",
            beforeSend: function () {
//            startLoad("frm");
                $('#documento').attr("disabled", "disabled");
            },
            success: function (data) {
//                alert(data[0]['participante_id']);
                if (data[0]['participante_id'] !== 'n') {
                    $("#nombre").val(data[0]['nombre']);
                    $("#estamento").val(data[0]['estamento']).trigger("chosen:updated");
                    $("#cel").val(data[0]['celular']);
                    $("#tel").val(data[0]['telefono']);
                    $("#mail").val(data[0]['email']);
                }
//            $("#nombre_e").val(data.nombre);
//            $("#fecha_ini_e").val(data.fecha_ini);
//            $("#fecha_fin_e").val(data.fecha_fin);
//            $("#lugar_e").val(data.lugar);
//            $("#cupos_e").val(data.cupos);
//            $("#organizador_e").val(data.organizador).trigger("chosen:updated");
//            $("#poblacion_e").val(data.poblacion).trigger("chosen:updated");
//            $("#tp_asist_e").val(data.tp_asist).trigger("chosen:updated");
//            $("#proyecto_e").val(data.proyecto).trigger("chosen:updated");
//            $('#result_e').html('');
//            traerArchivos(data.id,'banner_eve_e');
//            traerArchivos(data.id,'doc_eve_e');

//                            $('.chzn').val(data.cober.split("|")).trigger("chosen:updated");
            }, complete: function (data) {
                $('#buscarParticipante').removeAttr("disabled");
                $('#buscarParticipante').removeClass("disable");
                $('#frm_ins').show("slideDown");
//            stopLoad("frmE");
            }
        });
        return false;
    } else {
        swal({
            title: '¡Digite número de Documento!',
            text: '',
            type: 'error',
            confirmButtonColor: '#004669',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }
}

$(document).ready(function () {

    $('#form_inscripcion').validate({// initialize plugin
        rules: {
            tel: {
                required: false
            },
            mail: {
                required: true,
                email: true
            }
        },
        errorPlacement: function (error, element) {
            // Append error within linked label
            $(element)
                    .closest("form")
                    .find("label[for='" + element.attr("id") + "']")
                    .append(error);
        },
        errorElement: "span",
        errorContainer: $('#errorContainer'),
        messages: {
            nombre: " *",
            estamento: " *",
            cel: " *",
            mail: " *"
        },
        ignore: ":hidden:not(select)"
    });

    $('#cancelar').click(function () {
        $('#documento').removeAttr("disabled");
        $('#frm_ins').slideUp("500");
        limpiaForm(document.form_inscripcion);
        return false;
    });
    $('#guardar').click(function () {
        if($("#form_inscripcion").valid()){
            var doc = $('#documento').val();
            var dataS = $("#form_inscripcion").serialize();
            var ev = $("#evento option:selected").text();
            $.ajax({
            type: 'POST',
            url: 'src/inscripcionCB.php',
            data: "accion=guardar_participante&doc=" + doc+"&"+dataS+"&evento_nom="+ev,
//            dataType: "JSON",
            beforeSend: function () {
            startLoad("carga");
                $('#documento').attr("disabled", "disabled");
            },
            success: function (data) {
//                alert(data);
                $("#qr").html(data);
            }, complete: function (data) {
                stopLoad("carga");
                setTimeout("document.location.reload()",4000);
            }
        });
        }else {
            alert("else");
        }
    });

});

function infAd(){
    var dataString = {
            "accion": "evento",
            "accion2": "traer",
            "evento": $('#evento').val()
        };
        $.ajax({
            type: 'POST',
            url: 'src/inscripcionCB.php',
            data: dataString,
            beforeSend: function () {
                $('#otro-evento').slideUp(500);
                $('#otro-evento').html("");
            },
            success: function (data) {
                $('#otro-evento').html(data);
            },
            complete: function (jqXHR, textStatus) {
                $('#otro-evento').slideDown(500);                
            }
        });
}