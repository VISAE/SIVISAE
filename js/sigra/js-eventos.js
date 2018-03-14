/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */

///Popup - inicio
(function ($) {
    $(function () {
        // Crear
        $('#boton_crear').bind('click', function (e) {
            e.preventDefault();
            var form = document.form_crear;
            limpiaForm(form);
            $('#result').html('');
            $('#popup_crear').bPopup();
            $('#btnCrear').show();
            $('#otro-proyecto').html('');
            $('.carg_banner_eve, .carg_doc_eve').hide();
//            $("#form_crear").find("select").chosen('destroy');
            $("#organizador, #poblacion, #proyecto, #tp_asist").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
            $("#organizador, #poblacion, #proyecto, #tp_asist").val("").trigger("chosen:updated");
        });
    });

})(jQuery);

function activarpopupeditar(id) {
    //Editar

    $('#boton_editar' + id).bind('click', function (e) {
        e.preventDefault();
        var form = document.editar_usuario;
        limpiaForm(form);
        $('#popup_editar').bPopup();
        $('#btn_submit_e').show();
        $('#otro-proyecto_e').html('');
        $("#organizador_e, #poblacion_e, #proyecto_e, #tp_asist_e").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
//                        $("#linea_e, #cobertura_e, #eje_e").css({"position":"fixed", "z-index":"99", "_position":"absolute"});
//                        $("#linea_e, #cobertura_e, #eje_e").css({"position":"fixed", "z-index":"99", "_position":"absolute"});
//                        startLoad("frmE");
        cargar_popup_editar(id);
    });
}

function cargar_popup_editar(id) {
//    var str = $('#input_' + id).val();
//                    alert(str);
//    var ids = str.split("|");
    //Se llenan los campos segun el formulario
    $.ajax({
        type: 'POST',
        url: 'src/eventosCB.php',
        data: "accion=traer_evento&even_id=" + id,
        dataType: "JSON",
        beforeSend: function () {
            startLoad("frmE");
        },
        success: function (data) {
            $("#even_id_e").val(data.id);
            $("#nombre_e").val(data.nombre);
            $("#fecha_ini_e").val(data.fecha_ini);
            $("#fecha_fin_e").val(data.fecha_fin);
            $("#lugar_e").val(data.lugar);
            $("#cupos_e").val(data.cupos);
            $("#organizador_e").val(data.organizador).trigger("chosen:updated");
            $("#poblacion_e").val(data.poblacion).trigger("chosen:updated");
            $("#tp_asist_e").val(data.tp_asist).trigger("chosen:updated");
            $("#proyecto_e").val(data.proyecto).trigger("chosen:updated");
            $('#result_e').html('');
            traerArchivos(data.id,'banner_eve_e');
            traerArchivos(data.id,'doc_eve_e');
            
//                            $('.chzn').val(data.cober.split("|")).trigger("chosen:updated");
        },complete: function(data){
            stopLoad("frmE");
        }
    });
}

function activarpopupeliminar(id, perfil) {
    //Eliminar
    $('#boton_eliminar' + id).bind('click', function (e) {
        e.preventDefault();
        document.getElementById("proy_id_el").value = id;
        document.getElementById("proy_id_el_p").value = perfil;
        $('#result_el').html('');
        $('#popup_eliminar').bPopup();
    });
}
///Popup - fin


///logica - inicio
function listaGrilla() {
    var form = document.form_eventos;
    var dataString = $(form).serialize();
    $.ajax({
        type: 'POST',
        url: 'src/eventosCB.php',
        data: "accion=listado" + dataString,
        beforeSend: function () {
            startLoad("carg");
        },
        success: function (data) {
            $('#list_grilla').html(data);
            stopLoad("carg");
            $("#list_grilla").on("click", ".pagination a", function (e) {
                startLoad("carg");
                e.preventDefault();
                var page = $(this).attr("data-page"); //get page number from link
                $("#list_grilla").load("src/eventosCB.php", {
                    "accion": "listado",
                    "page": page
                },
                        function () { //get content from PHP page
                            stopLoad("carg");
                        });
            });
        }

    });
    return false;
}

///Crear
function submitFormCrear() {
    $('#btn_submit').attr("disabled", true);
    var nom = $('#nombre').val();
    var form = document.form_crear;
    var dataString = $(form).serialize();
    var foo = [];
    if ($('.chzn').length) {
        $('.chzn :selected').each(function (i, selected) {
            foo[i] = $(selected).val();
        });
    }
    var alertas = validarProyecto('');
    if (alertas.length > 0) {
        var alerta = alertas.join('<br>');
        $('#result').html("<label style='color: #EC2121'>" + alerta + "</label>");
        return false;
    } else {
        $.ajax({
            type: 'POST',
            url: 'src/proyectosCB.php',
            data: "accion=crear_proy&" + dataString + "&chzn=" + foo.join("|"),
            dataType: "JSON",
            beforeSend: function () {
                startLoad("frmC");
                $('#btnCrear').hide();
            },
            success: function (data) {
                $('#btn_submit').attr("disabled", false);
                stopLoad("frmC");
                //                            alert(data);
                if (data.cod === "uno") {
                    $('#result').html("<label style='color: #004669'>Se creo el Proyecto " + nom + " correctamente.</label>");
                    //Se recarga la grilla
                    limpiaForm(form);
                    //                                setTimeout("CerrarPopup(1)", 3000);
                    setTimeout("CerrarPopup(1)", 3000);
                } else {
                    $('#result').html("<label style='color: #EC2121'>El Proyecto " + nom + " ya existe.</label>");
                    $('#btnCrear').show();
                }
            }
        });
        return false;
    }
}

///Editar
function submitFormEditar() {
    $('#btn_submit_e').attr("disabled", true);
    var nom = $('#nombre_e').val();
    startLoad("frmE");
        var form = document.form_editar;
        var dataString = $(form).serialize();
        $.ajax({
            type: 'POST',
            url: 'src/eventosCB.php',
            data: "accion=update_evento&" + dataString,
            beforeSend: function () {
                $('#btn_submit_e').hide();
            },
            success: function (data) {
                $('#btn_submit_e').attr("disabled", false);
                stopLoad("frmE");
                $('#result_e').html(data);
                //Se recarga la grilla
var validator = $( "#form_editar" ).validate();
                        validator.resetForm();
                limpiaForm(form);
                //                            listaGrilla();
                setTimeout("CerrarPopup(2)", 3000);
            }
        });
    return false;
}

///Eliminar
function submitFormEliminar() {
    $('#btn_submit_el').attr("disabled", true);
    $('#btn_submit_el').hide();
//                    $("#spinner_el").show();
    var form = document.form_eliminar;
    var dataString = $(form).serialize();
    $.ajax({
        type: 'POST',
        url: 'src/proyectosCB.php',
        data: "accion=elim_proy&" + dataString,
        beforeSend: function () {
            startLoad("frmEl");
        },
        success: function (data) {
            stopLoad("frmEl");
//                            $('#btn_submit_el').attr("disabled", false);
            $('#result_el').html(data);
            //Se recarga la grilla

            setTimeout("CerrarPopup(3)", 3000);
        }
    });
    return false;
}

function CerrarPopup(popup) {
    if (popup == 1)
    {
        var form = document.form_crear;
        limpiaForm(document.form_crear);
        $('#popup_crear').bPopup().close();
        listaGrilla();
    }
    if (popup == 2)
    {
        $('#popup_editar').bPopup().close();
        listaGrilla();
    }
    if (popup == 3)
    {
        $('#popup_eliminar').bPopup().close();
        listaGrilla();
    }
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
function c_organizador(tp) {
    if ($('#organizador' + tp).val() === 'o') {
        var dataString = {
            "accion": "organizador",
            "accion2": "campos",
            "tp": tp
        };
        $.ajax({
            type: 'POST',
            url: 'src/eventosCB.php',
            data: dataString,
            success: function (data) {
                $('#otro-organizador' + tp).html(data);
            }
        });
    } else {
        $('#otro-organizador' + tp).html("");
    }
}
function n_organizador(tp) {
    var dataString = {
        "accion": "organizador",
        "accion2": "crear",
        "tp": tp
    };
//    alert(dataString + " -- " + $('#f_org').find("input:text").serialize());
    $.ajax({
        type: 'POST',
        url: 'src/eventosCB.php',
        data: "accion=organizador&accion2=crear&tp=" + tp + "&" + $('#f_org').find("input:text").serialize(),
        dataType: "JSON",
        success: function (data) {
            if (data.id !== '0') {
                $(".td-org").html(data.html1);
                $("#organizador").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                $(".td-org_e").html(data.html2);
                $("#organizador_e").chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
                $('#organizador' + tp).val(data.id).trigger("chosen:updated");
            } else {
                $('#otro-organizador' + tp).html("<label style='color: #EC2121'>Organizador existente, por favor validar.</label>");
            }
        }
    });
//    return false;
}
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
                color: '#004669', // #rgb or #rrggbb or array of colors 
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
                color: '#004669', // #rgb or #rrggbb or array of colors 
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
///logica - fin

$(function () {
    $("#fecha_ini").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        minDate: 0,
        dateFormat: "yy-mm-dd",
        onClose: function (selectedDate) {
            $("#fecha_fin").datepicker("option", "minDate", selectedDate);
        },
        yearRange: "-0:+10"
    });
    $("#fecha_fin").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        minDate: 0,
        dateFormat: "yy-mm-dd",
        onClose: function (selectedDate) {
            $("#fecha_ini").datepicker("option", "maxDate", selectedDate);
        },
        yearRange: "-0:+10"
    });
    $("#fecha_ini_e").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        minDate: 0,
        dateFormat: "yy-mm-dd",
        onClose: function (selectedDate) {
            $("#fecha_fin_e").datepicker("option", "minDate", selectedDate);
        },
        yearRange: "-0:+10"
    });
    $("#fecha_fin_e").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        minDate: 0,
        dateFormat: "yy-mm-dd",
        onClose: function (selectedDate) {
            $("#fecha_ini_e").datepicker("option", "maxDate", selectedDate);
        },
        yearRange: "-0:+10"
    });
});

function infAd(tp){
    var dataString = {
            "accion": "proyecto",
            "accion2": "traer",
            "proyecto": $('#proyecto'+tp).val()
        };
        $.ajax({
            type: 'POST',
            url: 'src/eventosCB.php',
            data: dataString,
            success: function (data) {
                $('#otro-proyecto' + tp).html(data);
            }
        });
}

//    $('#form_crear').validate({ ignore: ":hidden:not(select)" });
//});
//
//$(function () {
$(document).ready(function () {

    $('#form_crear').validate({
//                rules: {
//                    nombre: "required",
//                    fecha_ini: "required",
//                    fecha_fin: "required",
//                    lugar: "required",
//                    organizador: "required",
//                    poblacion: "required",
//                    cupos: "required",
//                    tp_asist: "required",
//                    banner_eve: "required",
//                    doc_eve: "required",
//                    proyecto: "required"
//                },
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
            fecha_ini: " *",
            fecha_fin: " *",
            lugar: " *",
            organizador: " *",
            poblacion: " *",
            cupos: " *",
            tp_asist: " *",
            banner_eve: " *",
            doc_eve: " *",
            proyecto: " *"
        },
        ignore: ":hidden:not(select)",
        submitHandler: function () {
            startLoad("frmC");
            var nom = $('#nombre').val();

//                        $.post('ajax.php', 
//                        $('#form_crear').serialize() , 
//                        function(data){
//                            alert(data.msg);
//                        }, "json");
            $.ajax({
                type: 'POST',
                url: 'src/eventosCB.php',
                data: "accion=crear_evento&" + $('#form_crear').serialize() ,
                dataType: "JSON",
                beforeSend: function () {
//                    startLoad("frmC");
                    $('#btnCrear').hide();
                },
                success: function (data) {
                    $('#btn_submit').attr("disabled", false);
                    stopLoad("frmC");
//                    alert(data);
                    if (data.cod === "uno") {
                        $('#result').html("<label style='color: #004669'>Se creo el Evento " + nom + " correctamente.</label>");
                        //Se recarga la grilla
                        limpiaForm($('#form_crear'));
                        var validator = $( "#form_crear" ).validate();
                        validator.resetForm();
                        //                                setTimeout("CerrarPopup(1)", 3000);
                        setTimeout("CerrarPopup(1)", 3000);
                    } else {
                        $('#result').html("<label style='color: #EC2121'>El Proyecto " + nom + " ya existe.</label>");
                        $('#btnCrear').show();
                    }
                }
            });
        }
    });
    $('#form_editar').validate({
                rules: {
//                    nombre: "required",
//                    fecha_ini: "required",
//                    fecha_fin: "required",
//                    lugar: "required",
//                    organizador: "required",
//                    poblacion: "required",
//                    cupos: "required",
//                    tp_asist: "required",
                    banner_eve_e: {
                        required :  function(element) {
                                if($('#hid-banner_eve_e').length){ //$("#contactform_email").is(":checked");
                                    return false;
                                }else {return true;}
                            }
                        },
                    doc_eve_e: {
                        required :  function(element) {
                                if($('#hid-doc_eve_e').length){ //$("#contactform_email").is(":checked");
                                    return false;
                                }else {return true;}
                            }
                        }
//                    proyecto: "required"
                },
        errorPlacement: function (error, element) {
            // Append error within linked label
            $(element)
                    .closest("form")
                    .find("label[for='" + element.attr("id") + "']")
                    .append(error);
        },
        errorElement: "span",
        errorContainer: $('#errorContainer_e'),
        messages: {
            nombre_e: " *",
            fecha_ini_e: " *",
            fecha_fin_e: " *",
            lugar_e: " *",
            organizador_e: " *",
            poblacion_e: " *",
            cupos_e: " *",
            tp_asist_e: " *",
            banner_eve_e: " *",
            doc_eve_e: " *",
            proyecto_e: " *"
        },
        ignore: ":hidden:not(select)",
        submitHandler: function () {
            submitFormEditar();
//            $.ajax({
//                
//            });
            return false;
        }
    });
});