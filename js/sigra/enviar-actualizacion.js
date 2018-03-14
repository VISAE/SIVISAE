/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */

function traer() {
//    $('#buscar').attr("disabled", "disabled");
//    $('#buscar').addClass("disable");
    if ($('#actualizacion').is(":visible")) {
//        alert('IF');
        frm();
//        buscar();
    } else {
//        alert('ELSE');
        frm();
//        buscar();
    }
}

function validar() {
    var t = 0;
    if ($('#nombre').val() === '') {
        t++;
    }
    if ($('#apellido').val() === '') {
        t++;
    }
    if ($('#nombre').val() === '') {
        t++;
    }
    if ($('#nombre').val() === '') {
        t++;
    }
    if ($('#nombre').val() === '') {
        t++;
    }
}

function buscar() {

    if ($('#documento_b').val() !== '') {
        var doc = $('#documento_b').val();
        var cver = $('#cverficacion_b').val();
        var inter = $('#interno').val();

//        $('.content, .clearfix').height(600);
        $.ajax({
            type: 'POST',
            url: 'src/actualizacion_datosCB.php',
            data: "accion=buscar&documento=" + doc + "&codigo=" + cver + "&inter=" + inter,
            dataType: 'JSON',
            success: function (data) {
//                alert(data.graduado);
                if (data !== "no" && data !== "na") {
                    swal({
                        title: 'Se han encontrado datos del Graduado',
                        text: 'A continuación la información será precargada.',
                        confirmButtonColor: '#004669',
                        confirmButtonText: 'Aceptar',
                        type: 'success',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    var grad_id = data.graduadoid;
                    $('#nombre').val(data.nombre);
//                    $('#graduado_id').val(""+grad_id);
                    $("input[id=graduado_id]").val(grad_id);
                    $('#apellido').val(data.apellido);
                    $('#tipo_doc').val(data.tipo_doc).trigger('chosen:updated');
                    $('#documento').val(data.documento);
                    $("#sexo_" + data.sexo).iCheck('check');
                    $('#est_civil').val(data.estado_civil).trigger('chosen:updated');
                    $('#fecha_nac').val(data.fecha_nac);
                    $('#pais_nac').val(data.pais_nac).trigger("chosen:updated");
                    $('#pais_res').val(data.pais_residencia).trigger("chosen:updated");
                    $('#direccion').val(data.direccion_residencia);
                    $('#tel_res').val(data.telefono_residencia);
                    $('#tel_cel').val(data.telefono_celular);
                    $('#email').val(data.email);
                    $('#email2').val(data.email_2);
                    $('#estrato').val(data.estrato).trigger('chosen:updated');
                    $('#parentezco').val(data.parentezco).trigger('chosen:updated');
                    $('#nombre_fam').val(data.nombre_fam);
                    $('#tel_res_fam').val(data.tel_fam);
                    $('#tel_cel_fam').val(data.cel_fam);
                    $('#email_fam').val(data.email_fam);
                    $('#sit_lab').val(data.situacion).trigger('chosen:updated');
                    if (data.situacion === 'Empleado' || data.situacion === 'Comerciante' || data.situacion === 'Independiente' || data.situacion === 'Empresario') {
                        $('.inf_lab').show('slideDown');
                        $('#actualizacion-p-3').height(400);
                        $('#cargo').val(data.cargo);
                        $('#empresa').val(data.nombre_empresa);
                        $('#relacion').val(data.relacion_unad).trigger('chosen:updated');
                        $('#tel_of').val(data.telefono_of);
                        $('#ciiu').val(data.ciiu).trigger("chosen:updated");
                        $('#email_lab').val(data.email_lab);
                    } else {
                        if (data.situacion === "Otra actividad (¿cuál?)") {
                            $(".inf_cual").show("slideDown");
                            $('#cual').val(data.nombre_empresa);
                        } else {
                            $(".inf_lab").hide("slideUp");
                            //            $("#actualizacion-p-3").height(450);
                        }
                    }
                    if (grad_id === 'x') {
                        if (data.ciudad_nac !== '') {
                            $('#lbl_ciudad_nac').html("Ciudad de nacimiento<br/><font style='color:#C80808; font-weight: normal;'>" + data.ciudad_nac + "</font>");
                        }
                        if (data.pais_residencia !== '') {
                            $('#lbl_pais_res').html("País de residencia<br/><font style='color:#C80808; font-weight: normal;'>" + data.pais_residencia + "</font>");
                        }
                        if (data.ciudad_residencia !== '') {
                            $('#lbl_ciudad_res').html("Ciudad de residencia<br/><font style='color:#C80808; font-weight: normal;'>" + data.ciudad_residencia + "</font>");
                        }
                    } else {
                        var nac = data.cod_ciudad_nac;
                        var res = data.cod_ciudad_res;
                        if ($('#pais_nac').val() !== "") {
                            $.ajax({
                                type: "POST",
                                url: "src/actualizacion_datosCB.php",
                                data: "accion=ciudades&pais=" + $('#pais_nac').val(),
                                beforeSend: function () {
                                    $("#city_nac").html("<option value=''>Cargando...</option>").trigger("chosen:updated");
                                },
                                success: function (data) {
                                    $("#city_nac").html(data).trigger("chosen:updated");
                                }
                            });
                        }
                        if ($('#pais_res').val() !== "") {
                            $.ajax({
                                type: "POST",
                                url: "src/actualizacion_datosCB.php",
                                data: "accion=ciudades&pais=" + $('#pais_res').val(),
                                beforeSend: function () {
                                    $("#city_res").html("<option value=''>Cargando...</option>").trigger("chosen:updated");
                                },
                                success: function (data) {
                                    $("#city_res").html(data).trigger("chosen:updated");
                                },
                                complete: function (data) {
                                    $('#city_nac').val(nac).trigger("chosen:updated");
                                    $('#city_res').val(res).trigger("chosen:updated");
                                }
                            });
                        }
//                        $('#city_nac').val($('#hid_ciudad_nac').val()).trigger("chosen:updated");  
//                        $('#city_res').val($('#hid_ciudad_res').val()).trigger("chosen:updated");  
                    }
                } else {
                    if (data === "na") {
                        swal({
                            title: 'Preguntas de seguridad',
                            text: 'Ud. no ha logrado superar las preguntas de seguridad, por favor verifiquelas e intente nuevamente.',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar',
                            type: 'error'
                        }, function () {
                            location.reload();
                        });
                    } else {

                        swal({
                            title: 'No Se han encontrado datos del Graduado',
                            text: 'Por favor verifique el número de documento y la información ingresada.',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar',
                            type: 'error'
                        }, function () {
                            location.reload();
                        });
                    }

                }
                $('#fecha_nac,#fecha_grado').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }
        });
    } else {
        swal({
            title: 'Digite el numero de Documento!',
            text: '',
            type: 'error',
            confirmButtonColor: '#004669',
            confirmButtonText: 'Aceptar'
        });
    }
}

function actualizarGraduado() {
    var dataString = $('#frm_actualizar').serialize();
    if ($("#privacy").length > 0) {
        swal({
            title: 'LEY DE PROTECCIÓN DE DATOS PERSONALES (LEY 1581 DE 2012).',
            width: 850,
            html: '<div align="justify"><br/>La protección y el manejo de la información personal de sus graduados es muy importante para la UNAD. \n\
                    Por esta razón, la Universidad ha diseñado políticas y procedimientos que, en conjunto con la siguiente autorización, \n\
                    permiten hacer uso de sus datos personales conforme a la ley.<br/><br/>\n\
                    Lo invitamos a conocer la POLÍTICA DE PROTECCIÓN DE DATOS PERSONALES de la Universidad Nacional Abierta y Distancia - UNAD, \n\
                    haciendo clic <a href="http://egresados.unad.edu.co/files/Politica_Privacidad_RedeUNAD.pdf" target="_blank"/><strong>Aquí</strong></a>.<br/><br/>AUTORIZACIÓN AL TRATAMIENTO DE DATOS PERSONALES<br/><br/>De conformidad con las políticas de tratamiento de datos personales, autorizo la Universidad Nacional Abierta y a Distancia - UNAD para tratar mi información personal, con el fin de estructurar ofertas académicas y remitir información sobre los productos y servicios la institución.' + '<br/><br/>Esta autorización estará vigente a partir de la fecha, y se podrá revocar a través de este formulario cada vez que usted desee actualizar sus datos.</div>',
            showCancelButton: true,
            confirmButtonColor: '#004669',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            confirmButtonClass: 'confirm-class',
            cancelButtonClass: 'cancel-class',
            closeOnConfirm: false,
            closeOnCancel: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        },
        function (isConfirm) {
            if (isConfirm) {
                $("#privacy").val("si");
                $.ajax({
                    type: 'POST',
                    url: 'src/actualizacion_datosCB.php',
                    data: "accion=guardar&" + dataString + "&privacy=si",
                    success: function (data) {
                        //                alert(data);
                        swal({
                            title: 'Graduado Actualizado con éxito!',
                            text: '',
                            type: 'success',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        location.reload();
                    }
                });
                //alert(dataString);
            } else {
                $("#privacy").val("no");
                $.ajax({
                    type: 'POST',
                    url: 'src/actualizacion_datosCB.php',
                    data: "accion=guardar&" + dataString + "&privacy=no",
                    success: function (data) {
                        alert(data);
                        swal({
                            title: 'Graduado Actualizado con éxito!',
                            text: '',
                            type: 'success',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        location.reload();
                    }
                });
                //alert(dataString);
            }
        });
    } else {
        $.ajax({
            type: 'POST',
            url: 'src/actualizacion_datosCB.php',
            data: "accion=guardar&" + dataString,
            success: function (data) {
                alert(data);
                swal({
                    title: 'Graduado Actualizado con éxito!',
                    text: '',
                    type: 'success',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
                location.reload();
            }
        });
    }
}

function frm() {
    if ($('#documento_b').val() !== '') {

        if ($('#cverficacion_b').val() !== '') {
            $('#buscar').attr("disabled", "disabled");
            $('#buscar').addClass("disable");
            var doc = $('#documento_b').val();
            $.ajax({
                type: 'POST',
                url: 'src/actualizacion_datosCB.php',
                data: "accion=frm",
                dataType: 'HTML',
                success: function (data) {
                    $('#frm_actualizacion').empty();
                    $('#frm_actualizacion').append(data);
                    buscar();
                    $("#inf-gen").find('input').width(300);
                    $("#escuela").width(300);
                    $('#buscar').removeAttr("disabled");
                    $('#buscar').removeClass("disable");
                }
            });
        } else {
            swal({
                title: 'Falta información',
                text: 'Debe ingresar el código de verificación.',
				html:true,
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
        }


    } else
    {
        swal({
            title: 'Falta información',
            text: 'Debe ingresar el número de documento.',
            type: 'error',
            confirmButtonColor: '#004669',
            confirmButtonText: 'Aceptar'
        });
    }
}

function validarFechaNac(fechaN) {
    var fecha = new Date(fechaN);
    var hoy = new Date(); //30 de noviembre de 2014
    var anio = hoy.getFullYear();
    var mes = hoy.getMonth();
    var dia = hoy.getDay();
    var nvaHoy = new Date((anio - 15) + '-' + mes + '-' + dia);
    if (fecha.getTime() >= nvaHoy.getTime()) {
        return true;
    } else
    {
        return false;
    }
}

function validarCampos(index) {
    var alerta = new Array();
    if (index === 0) {
        var part = 0;
        if ($(".checked input[name='sexo']").val() === undefined) {
            alerta.push('* Seleccione el Sexo del graduado');
        }
        if ($('#nombre').val() === '') {
            alerta.push('* Falta el(los) Nombre(s) del Graduado');
        }
        if ($('#apellido').val() === '') {
            alerta.push('* Falta el(los) Apellido(s) del Graduado');
        }
        if ($('#tipo_doc').val() === '' || $('#tipo_doc').val() === null) {
            alerta.push('* Seleccione el Tipo de Documento');
        }
        if ($('#documento').val() === '') {
            alerta.push('* Falta el número de Documento del Graduado');
        }
        if ($('#est_civil').val() === '' || $('#est_civil').val() === null) {
            alerta.push('* Seleccione el Estado Civil del Graduado');
        }
        if ($('#pais_nac').val() === '' || $('#pais_nac').val() === null) {
            alerta.push('* Seleccione el País de Nacimiento del Graduado');
        } else {
            if ($('#city_nac').val() === '' || $('#city_nac').val() === null) {
                alerta.push('* Seleccione la Ciudad de Nacimiento del Graduado');
            }
        }
        if ($('#fecha_nac').val() === '') {
            alerta.push('* Seleccione la Fecha de Nacimiento del Graduado');
        } else
        {
            if (validarFechaNac($('#fecha_nac').val())) {
                alerta.push('* La Fecha de Nacimiento Nngresada no es Válida.');
            }
        }

        if ($('#pais_res').val() === '' || $('#pais_res').val() === null) {
            alerta.push('* Seleccione el País de Residencia del Graduado');
        } else {
            if ($('#city_res').val() === '' || $('#city_res').val() === null) {
                alerta.push('* Seleccione la Ciudad de Residencia del Graduado');
            }
        }
        if ($('#direccion').val() === '') {
            alerta.push('* Falta la Dirección de Residencia del Graduado');
        }
        if ($('#estrato').val() === '' || $('#estrato').val() === null) {
            alerta.push('* Seleccione el Estrato Socio-Económico del Graduado');
        }
        if ($('#tel_res').val() === '') {
            alerta.push('* Falta el número Teléfono de Residencia del Graduado');
        }
        if ($('#tel_cel').val() === '') {
            alerta.push('* Falta el número Celular del Graduado');
        }
        if ($('#email').val() === '') {
            alerta.push('* Falta el Correo Electrónico principal del Graduado');
        }
    }
    if (index === 1) {
//        if ($('#cod_programa1').length === 0 || $('#cod_programa1').val() === '') {
//            alerta.push('Debe seleccionar al menos un Programa para el Graduado');
//        }
//        if ($('#cod_cead1').length === 0 || $('#cod_cead1').val() === '') {
//            alerta.push('Debe seleccionar el CEAD al que pertenece el Graduado');
//        }
//        if ($('#niv_aca1').length === 0 || $('#niv_aca1').val() === '') {
//            alerta.push('Debe seleccionar el Nivel Académico del programa del Graduado');
//        }
    }

    if (index === 2) {
        if ($('#nombre_fam').val() === '') {
            alerta.push('* Falta el Nombre Completo de la persona de contacto');
        }
        if ($('#parentezco') === '') {
            alerta.push('Seleccione el parentezco con la persona de contacto');
        }
        if ($('#tel_res_fam').val() === '') {
            alerta.push('* Ingrese número de Teléfono Residencia de la persona de contacto');
        }
        if ($('#tel_cel_fam').val() === '') {
            alerta.push('* Ingrese número de Celular de la persona de contacto');
        }
    }

    if (index === 3) {
        if ($('#sit_lab') === '') {
            alerta.push('Seleccione la Situación Laboral del Graduado');
        } else {
            if ($('#sit_lab').val() === "Empleado" || $('#sit_lab').val() === "Empresario" || $('#sit_lab').val() === "Comerciante" || $('#sit_lab').val() === "Independiente") {
                if ($('#empresa').val() === '') {
                    alerta.push('* Falta el Nombre de la Empresa del Graduado');
                }
                if ($('#cargo').val() === '') {
                    alerta.push('* Falta el Cargo del Graduado');
                }
                if ($('#ciiu').val() === '') {
                    alerta.push('* Seleccione la Actividad Económica del Graduado');
                }
                if ($('#relacion').val() === '') {
                    alerta.push('* Seleccione si tiene Relación con el Programa estudiado por el Graduado');
                }
            }
        }
    }
    return alerta;
}

$(document).ready(function () {
    $('#documento_b, #tel_res, #tel_cel, #tel_res_fam, #tel_cel_fam, #tel_of').keydown(function (event) {
        if (event.shiftKey) {
            event.preventDefault();
        }
        if (event.keyCode === 46 || event.keyCode === 8) {
        } else {
            if (event.keyCode < 95) {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            } else {
                if (event.keyCode < 96 || event.keyCode > 105) {
                    event.preventDefault();
                }
            }
        }
    });
});