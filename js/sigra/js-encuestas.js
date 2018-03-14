/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */
function listaGrilla() {
    var form = document.form_eventos;
    var dataString = $(form).serialize();
    $.ajax({
        type: 'POST',
        url: 'src/encuestasCB.php',
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
                $("#list_grilla").load("src/encuestasCB.php", {
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
    if(loader){
        loader.stop();
    }
}

function limpiaForm(miForm) {
    // recorremos todos los campos que tiene el formulario

    $(':input', miForm).each(function () {
        var type = this.type;
        var tag = this.tagName.toLowerCase();
        //limpiamos los valores de los campos…
        if (type === 'text' || type === 'password' || tag === 'textarea' || type === 'number' || type === 'file' || type === 'hidden') {
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

$(document).ready(function () {
    $(".chosen-select").chosen({no_results_text: "No se encontraron Coincidencias!", width: "95%"});
    $('#frm_enc').validate({// initialize plugin
        rules: {
            nombre: {
                required: true,
                minlength: 4
            },
            desc_enc: {
                required: false
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
            nombre: {
                required: " *",
                minlength: "El Nombre debe contener al menos 4 caracteres"
            }
        },
        ignore: ":hidden:not(select)"
    });

    $('#cancelar').click(function () {
        $('#documento').removeAttr("disabled");
        limpiaForm(document.form_inscripcion);
        return false;
    });
    $('#guardar').click(function () {
        if ($("#frm_enc").valid()) {
            var dataS = $("#frm_enc").serialize();
            $.ajax({
                type: 'POST',
                url: 'src/encuestasCB.php',
                data: "accion=nueva_enc&" + dataS,
                dataType: "JSON",
                beforeSend: function () {
                    startLoad("carga");
                    $('#guardar').attr("disabled", "disabled");
                    $('#guardar').hide();
                    $('#cancelar').hide();
                },
                success: function (data) {
//                    alert(data['data']['encuesta_id']);
                    if (data['data']['encuesta_id'] !== 'n') {
                        $(".lbl_nombre").text("Encuesta: " + data['data']['nombre'].toUpperCase());
                        $("#enc_id").val(data['data']['encuesta_id']);
                        $("#nombre").attr("readonly", "readonly");
                        $("#desc_enc").attr("readonly", "readonly");
                        $('#act-mod').removeAttr("disabled");
                        $('#act-preg').removeAttr("disabled");
                        $('#act-resp').removeAttr("disabled");
                    } else {
                        swal({
                            title: '¡La Encuesta ya existe!',
                            text: 'Por favor asigne otro Nombre',
                            type: 'error',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        $('#guardar').removeAttr("disabled");
                        $('#guardar').show();
                        $('#cancelar').show();
                    }
                }, complete: function (data) {
//                $('#frm_ins').show("slideDown");
                    stopLoad("carga");
                }
            });
        }
    });

    $("#act-mod").on("click", function () {
        if ($("#enc_id").val() !== '') {
//            alert("SIIII");
            traerModulos();
            $(this).attr('checked', true);
        } else {
//            alert("NOOOOO");
            $(this).attr('checked', false);
        }
    });
    $("#act-preg").on("click", function () {
        if ($("#enc_id").val() !== '') {
//            alert($("#enc_id").val());
            $.ajax({
                type: 'POST',
                url: 'src/encuestasCB.php',
                data: "accion=carg_mod&enc_id=" + $("#enc_id").val(),
                beforeSend: function () {
                    $("#modulo").append("<option value=''>Cargando...</option>").trigger("chosen:updated");
                },
                success: function (data) {
//                    alert(data);
                    $("#modulo").empty().trigger("chosen:updated");
                    $("#modulo").append(data).trigger("chosen:updated");
                }
            });
            $(this).attr('checked', true);
        } else {
            $(this).attr('checked', false);
        }
    });
    $("#act-resp").on("click", function () {
        if ($("#enc_id").val() !== '') {
//            alert($("#enc_id").val());
            $.ajax({
                type: 'POST',
                url: 'src/encuestasCB.php',
                data: "accion=carg_preg&enc_id=" + $("#enc_id").val(),
                beforeSend: function () {
                    $("#pregunta").append("<option value=''>Cargando...</option>").trigger("chosen:updated");
                },
                success: function (data) {
//                    alert(data);
                    $("#pregunta").empty().trigger("chosen:updated");
                    $("#pregunta").append(data).trigger("chosen:updated");
                }
            });
            $(this).attr('checked', true);
        } else {
            $(this).attr('checked', false);
        }
    });

    $('#add_mod').click(function () {
        agregarModulo();
        return false;
    });
    $('#rem_mod').click(function () {
        var tr = $("#modulos tr").length;
        if (tr > 2) {
            $("#modulos tr:last").remove();
        }
        return false;
    });
    $('#add_resp').click(function () {
        agregarRespuesta();
        return false;
    });
    $('#rem_resp').click(function () {
        var tr = $("#respuestas tr").length;
        if (tr > 2) {
            startLoad("carga_preg");
//            alert($("#respuestas tr:last").find(".hid_resp").val());
            var id = $("#respuestas tr:last").find(".hid_resp").val();
            $.ajax({
                type:"POST",url: "src/encuestasCB.php",data:"accion=borrar_resp&resp_id="+id,
                success: function(d){
                    alert(d);
                    $("#respuestas tr:last").remove();  
                    stopLoad("carga_preg");
                }
            });
        }
        return false;
    });

    $("#frm_mod").validate({errorPlacement: function (error, element) {
            // Append error within linked label
            $(element)
                    .closest("form")
                    .find("label[for='" + element.attr("id") + "']")
                    .append(error);
        },
        errorElement: "span",
        errorContainer: $('#errorContainer_mod')});
    $('#guardar_mod').click(function () {
        if ($("#frm_mod").valid()) {
            var dataS = $("#frm_mod").serialize();
            var tr = $("#modulos tr").length;
            var arr = $(".nume").map(function () {
                return $(this).val();
            }).toArray();
            var allHaveSameValue = $.unique(arr).length;
//            alert("accion=modulos&filas=" + tr + "&" + dataS + "&enc_id=" + $("#enc_id").val());
            if (allHaveSameValue === (tr - 1)) { 
                $.ajax({
                    type: 'POST',
                    url: 'src/encuestasCB.php',
                    data: "accion=modulos&filas=" + tr + "&" + dataS + "&enc_id=" + $("#enc_id").val(),
                    dataType: "JSON",
                    beforeSend: function () {
                        startLoad("carga_mod");
                        $('#btns').hide();
                    },
                    success: function (data) {
//                        alert(data);
                        if (data['data'] !== "n") {
                            swal({
                                title: '¡Los siguientes Módulos se repiten!',
                                html: data['data'],
                                type: 'error',
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });
                            $('#btns').show();
                        } else {
                            limpiaForm(document.frm_mod);
                            for(var i=0;i<tr-2;i++){
                                $("#modulos tr:last").remove();
                            }
                            $('#btns').show();
                            traerModulos();
//                            $('#act-preg').removeAttr("disabled");
                            //ocultar div y continuar con las preguntas
                            //generar lista de modulos creados para las preguntas
                        }
                    }, complete: function (data) {
                        stopLoad("carga_mod");
                    }
                });
            } else {
                swal({
                    title: '¡Uno o mas ordenes de los Módulos se repiten!',
                    text: 'Por favor Validar',
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
            }
        }
    });
    
    $("#modulo").on("change", function() {
        limpiaForm("#f_pregs");
        $("#f_pregs").hide("slideUp");
        if($(this).val()!==""){
            $("#f_pregs").show("slideDown");
            traerPreguntas();
        }else {
            $("#f_pregs").hide("slideUp");            
        }
    });
    
    
    $("#limpiar").click(function(){
//        alert("si");
        $("#editar_preg").slideUp(0);
        $("#guardar_preg").slideDown(0);
        stopLoad("loading_pr-"+$("#hid_preg_id").val());
        borrarArchivoPreg("n");
        limpiaForm("#f_pregs");
        $("#carg-img_preg").slideUp(300);
        $("#carg-img_preg").html("");
        $('#imagen_preg').slideDown(300);
        $("#est_preg").hide();
    });
    $("#limpiar_resp").click(function(){
//        alert("si");
        limpiaForm("#f_resp");
        $("#est_resp").hide();
    });
    $('#frm_preg').validate({// initialize plugin
        rules: {
            enunciado: {
                required: true,
                minlength: 15
            },
            tp_preg: {
                required: true
            },
            desc_preg: {
                required: true,
                minlength: 20
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
        errorContainer: $('#errorContainer_preg'),
        messages: {
            enunciado: {
                required: " *",
                minlength: "El Enunciado debe contener al menos 15 caracteres"
            },
            desc_preg: {
                required: " *",
                minlength: "El Enunciado debe contener al menos 20 caracteres"
            },
            orden_preg: {
                required: " *"
            },
            tp_preg: {
                required: " *"
            }
        },
        ignore: ":hidden:not(select)"
    });
    $('#guardar_preg').click(function () {
        if ($("#frm_preg").valid()) {
            var data = $("#frm_preg").serialize();
            var h_ord = $("#hid_orden_preg").val();
            var m = $("#modulo").val();
            alert("accion=crear_pregunta&" + data + "&hid_orden=" + h_ord);
//            var url_img = $("#url_imagen").val();
            $.ajax({
                type: 'POST',
                url: 'src/encuestasCB.php',
                data: "accion=crear_pregunta&" + data + "&hid_orden=" + h_ord,
                dataType: "JSON", 
                beforeSend: function () {
                    startLoad("carga_preg");
                },
                success: function (data) {
                    if(data['data']!=='n'){
                        swal({
                            title: '¡Pregunta creada!',
                            text: '',
                            type: 'success',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        traerPreguntas();
                        limpiaForm(document.frm_preg);
                        $("#limpiar").trigger("click");
                        $("#modulo").val(m).trigger("chosen:updated");
                    }else {
                        swal({
                            title: '¡Ya existe una pregunta en ese Orden!',
                            text: 'Por favor Validar',
                            type: 'error',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                }, complete: function (data) {
                    stopLoad("carga_preg");
                }
            });
        }
    });
    $('#editar_preg').click(function () {
        if ($("#frm_preg").valid()) {
            var data = $("#frm_preg").serialize();
            var h_ord = $("#hid_orden_preg").val();
            var m = $("#modulo").val();
            var chk = 2;
            if($("#estado_preg").is(":checked")){
                chk = 1;
            }
            var id = $("#hid_preg_id").val();
            var md = $("#hid_orden_preg").val().split(",");
            var or = md.indexOf($("#ord_preg-"+id).text());
            if(or >= 0){
                md.splice(or,1);
            }
            or = md.indexOf($("#orden_preg-"+id).val());
            if(or >= 0){
                swal({
                    title: '¡Ya existe una Pregunta en ese Orden!',
                    text: 'Por favor Validar',
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
            }else {
                $.ajax({
                    type: 'POST',
                    url: 'src/encuestasCB.php',
                    data: "accion=editar_preg&accion2=guardar&" + data + "&hid_orden=" + h_ord+"&estado="+chk,
                    dataType: "JSON", 
                    beforeSend: function () {
                        startLoad("carga_preg");
                    },
                    success: function (data) {
//                        alert(data['data']);
                        if(data['data']!=='n'){
                            swal({
                                title: '¡Actualización guardada!',
                                text: '',
                                type: 'success',
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });
                            traerPreguntas();
                            $("#carg-img_preg").slideUp(300);
                            $("#carg-img_preg").html("");
                            $('#imagen_preg').slideDown(300);
                            limpiaForm(document.frm_preg);
                            $("#modulo").val(m).trigger("chosen:updated");
                            stopLoad("loading_pr-"+id);
                            $("#est_preg").hide();
                        }else {
                            swal({
                                title: '¡Ya existe una pregunta en ese Orden!',
                                text: 'Por favor Validar',
                                type: 'error',
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    }, complete: function (data) {
                        stopLoad("carga_preg");
                    }
                });
            }
        }
    });
    //-----------Respuestas------------------//
    $("#pregunta").on("change", function() {
        limpiaForm("#f_resp");
        $("#f_resp").hide("slideUp");
        if($(this).val()!==""){
            traerRespuestas();
//            $("#f_resp").slideDown(300);
        }else {
            $("#f_resp").slideUp(300);
        }
    });
    $('#frm_resp').validate({// initialize plugin
        rules: {
            enunciado_resp: {
                required: true,
                minlength: 15
            },
            desc_preg: {
                required: true,
                minlength: 20
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
        errorContainer: $('#errorContainer_preg'),
        messages: {
            enunciado: {
                required: " *",
                minlength: "El Enunciado debe contener al menos 15 caracteres"
            },
            desc_preg: {
                required: " *",
                minlength: "El Enunciado debe contener al menos 20 caracteres"
            },
            orden_preg: {
                required: " *"
            },
            tp_preg: {
                required: " *"
            }
        },
        ignore: ":hidden:not(select)"
    });
    $('#guardar_resp').click(function () {
        if ($("#frm_resp").valid()) {
            var dataS = $("#frm_resp").serialize();
            var tr = $("#respuestas tr").length;
            var arr = $(".nume2").map(function () {
                return $(this).val();
            }).toArray();
            alert("accion=respuestas&filas=" + tr + "&" + dataS + "&preg_id=" + $("#pregunta").val());
            var allHaveSameValue = $.unique(arr).length;
//            alert("accion=modulos&filas=" + tr + "&" + dataS + "&enc_id=" + $("#enc_id").val());
            if (allHaveSameValue === (tr - 1)) { 
                $.ajax({
                    type: 'POST',
                    url: 'src/encuestasCB.php',
                    data: "accion=respuestas&filas=" + tr + "&" + dataS + "&preg_id=" + $("#pregunta").val(),
//                    dataType: "JSON",
                    beforeSend: function () {
                        startLoad("carga_resp");
//                        $('#btns_resp').hide();
                    },
                    success: function (data) {
//                        alert(data);
//                            limpiaForm(document.frm_resp);
//                            for(var i=0;i<tr-2;i++){
//                                $("#respuestas tr:last").remove();
//                            }
//                            $('#btns_resp').show();
                            traerRespuestas();
                    }, complete: function (data) {
//                        stopLoad("carga_resp");
                    }
                });
            } else {
                swal({
                    title: '¡Uno o mas ordenes de las Respuestas se repiten!',
                    text: 'Por favor Validar',
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
            }
        }
    });
});

function agregarModulo() {
    var $tr = $("#modulos").find("tbody tr:last").clone().appendTo("#modulos tbody").show();
    // obtener el atributo name para los inputs y selects
    $tr.find("input").val("");
//    $tr.find("input:number").val("");
    var n = "";
    $tr.find("input, label").attr("name", function () {
        //  separar el campo name y su numero en dos partes
        var parts = this.id.match(/(\D+)(\d+)$/);
        // crear un nombre nuevo para el nuevo campo incrementando el numero para los previos campos en 1
        return parts[1] + ++parts[2];
        // repetir los atributos ids
    }).attr("id", function () {
        var parts = this.id.match(/(\D+)(\d+)$/);
        return parts[1] + ++parts[2];
    }).html("");
    // añadir la nueva fila a la tabla
//    $('#fecha_g' + n).val($('#fecha_grado' + n).val());
    $("#modulos").find("tbody tr:last").after($tr);
}


function traerModulos() {
    $.ajax({
        type: 'POST',
        url: 'src/encuestasCB.php',
        data: "accion=traer_modulos&enc_id=" + $("#enc_id").val(),
        beforeSend: function () {
            startLoad("carg_lst_mods");
        },
        success: function (data) {
            if(data!==''){
                $("#lst_mods").html(data);
//                $(".chosen-select").chosen({no_results_text: "No se encontraron Coincidencias!", width: "95%"});
                if($("#tb_pre").length>0){
                    $('#act-preg').removeAttr("disabled");
                }
            }
        }, complete: function (data) {
            stopLoad("carg_lst_mods");
        }
    });
}
function traerPreguntas() {
    $.ajax({
        type: 'POST',
        url: 'src/encuestasCB.php',
        data: "accion=traer_preguntas&mod_id=" + $("#modulo").val(),
        beforeSend: function () {
            startLoad("carg_lst_pregs");
        },
        success: function (data) {
            if(data!=='n'){
                $("#lst_pregs").html(data);
//                $("#lst_mods").show();
            }
        }, complete: function (data) {
            stopLoad("carg_lst_pregs");
        }        
    });
}

function editarMod(id){
    startLoad("loading-"+id);
    $("#ed_mod-"+id).slideUp(300);
    $("#g_mod-"+id).slideDown(300);
    $(".lblm-"+id).slideUp(300);
    $(".edt-"+id).slideDown(300);
    stopLoad("loading-"+id);
}

function guardaEMod(id){
    
    var chk = 2;
    if($("#est-"+id).is(":checked")){
        chk = 1;
    }
    var dataS = "nombre_mod=" + $("#nombre_mod-"+id).val() + "&desc_mod=" + $("#desc_mod-"+id).val() + "&orden_mod=" + $("#orden_mod-"+id).val() +
        "&estado=" + chk;
    var md = $("#hid_orden_mod").val().split(",");
    var or = md.indexOf($("#ord_mod-"+id).text());
    if(or >= 0){
        md.splice(or,1);
    }
    or = md.indexOf($("#orden_mod-"+id).val());
    if(or >= 0){
        swal({
            title: '¡Ya existe un Módulo en ese Orden!',
            text: 'Por favor Validar',
            type: 'error',
            confirmButtonColor: '#004669',
            confirmButtonText: 'Aceptar'
        });
    }else {
        $.ajax({
            type: 'POST',
            url: 'src/encuestasCB.php',
            data: "accion=editar_mod&accion2=guardar&mod_id=" + id+"&"+dataS,
            beforeSend: function () {
                startLoad("loading-"+id);
            },
            success: function (data) {
                alert(data);
            }, complete: function (data) {
                traerModulos();
                stopLoad("loading-"+id);
            }        
        });
    }
}

function editarPregunta(id){
    $.ajax({
            type: 'POST',
            url: 'src/encuestasCB.php',
            data: "accion=editar_preg&accion2=traer&preg_id=" + id,
            dataType: "JSON",
            beforeSend: function () {
                startLoad("loading_pr-"+id);
                $("#guardar_preg").slideUp(0);
                $("#editar_preg").slideDown(0);
            },
            success: function (data) {
//                alert(data['data']['pregunta_id']);
                $("#hid_preg_id").val(data['data']['pregunta_id']);
                $("#enunciado").val(data['data']['enunciado']);
                $("#tp_preg").val(data['data']['tipo_preg']).trigger("chosen:updated");
                $("#desc_preg").val(data['data']['descripcion']);
                $("#orden_preg").val(data['data']['orden']);
                $("#referencia").val(data['data']['referencia']);
                $("#hipervinculo").val(data['data']['hipervinculo']);
                $("#est_preg").slideDown(300);
                if(data['data']['url_imagen']!==""){
                    archivosPreg(data['data']['pregunta_id']);
                }
                if(data['data']['estado_id']!=='1'){
                    $("#estado_preg")[0].checked = false;
                }else {
                    $("#estado_preg")[0].checked = true;
                }
            }, complete: function (data) {
                var topPos = $("#nom_enc").position().top;
                $("#sec_preg").animate({scrollTop : topPos});
            }        
        });
}

function traerRespuestas() {
    $.ajax({
        type: 'POST',
        url: 'src/encuestasCB.php',
        data: "accion=traer_respuestas&preg_id=" + $("#pregunta").val(),
        beforeSend: function () {
            startLoad("carga_resp");
        },
        success: function (data) {
            if(data!=='n'){
                $("#tb_rtas").html("");
                $("#tb_rtas").html(data);
//                $("#lst_mods").show();
            }
        }, complete: function (data) {
            stopLoad("carga_resp");
            $("#f_resp").slideDown(300);
//            $("#carg_lst_resp").html("");
        }        
    });
}

//function editarRespuesta(id){
//    $.ajax({
//            type: 'POST',
//            url: 'src/encuestasCB.php',
//            data: "accion=editar_resp&accion2=traer&respuesta_id=" + id,
//            dataType: "JSON",
//            beforeSend: function () {
//                startLoad("loading_pr-"+id);
//                $("#guardar_resp").slideUp(0);
//                $("#editar_resp").slideDown(0);
//            },
//            success: function (data) {
////                alert(data['data']['pregunta_id']);
//                $("#hid_resp_id").val(data['data']['respuesta_id']);
//                $("#enunciado_resp").val(data['data']['enunciado']);
//                $("#valor").val(data['data']['valor']).trigger("chosen:updated");
//                $("#desc_resp").val(data['data']['descripcion']);
//                $("#orden_resp").val(data['data']['orden']);
//                $("#est_resp").slideDown(300);
//                if(data['data']['estado_id']!=='1'){
//                    $("#estado_resp")[0].checked = false;
//                }else {
//                    $("#estado_resp")[0].checked = true;
//                }
//            }, complete: function (data) {
//                var topPos = $("#nom_enc").position().top;
//                $("#sec_preg").animate({scrollTop : topPos});
//            }        
//        });
//}

function agregarRespuesta() {
    var $tr = $("#respuestas").find("tbody tr:last").clone().appendTo("#respuestas tbody").show();
    // obtener el atributo name para los inputs y selects
    $tr.find("input,textarea").val("");
//    $tr.find("input:checkbox").attr("checked", true);
    var n = "";
    $tr.find("input, textarea, label").attr("name", function () {
        //  separar el campo name y su numero en dos partes
        var parts = this.id.match(/(\D+)(\d+)$/);
        // crear un nombre nuevo para el nuevo campo incrementando el numero para los previos campos en 1
        return parts[1] + ++parts[2];
        // repetir los atributos ids
    }).attr("id", function () {
        var parts = this.id.match(/(\D+)(\d+)$/);
        n = ++parts[2];
        return parts[1] + n;
    });
//    $tr.find("label").attr({
//        htmlFor: function () {
//            var parts = this.id.match(/(\D+)(\d+)$/);
//            return parts[1] + ++parts[2];
//        }
//    });
    // añadir la nueva fila a la tabla
    $("#respuestas").find("tbody tr:last").after($tr);
        document.getElementById("lbl_est_resp"+n).htmlFor = "estado_resp"+n;
//        $("#estado_resp"+n).attr("checked", true);
//        $("#lbl_est_resp"+n).removeClass("before");
//        $("#lbl_est_resp"+n).addClass("after");
}

