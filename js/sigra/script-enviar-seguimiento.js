function startLoad() {
    $("#spinner").introLoader({
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
            length: 20, // The length of each line 
            width: 10, // The line thickness 
            radius: 30, // The radius of the inner circle 
            corners: 1, // Corner roundness (0..1) 
            color: '#004669', // #rgb or #rrggbb or array of colors 
        }
    });
}
function stopLoad() {
    var loader = $('#spinner').data('introLoader');
    loader.stop();
}

function agregar(acc, data) {
    var caja = $('#' + acc).val();
    var tx;
    if (caja.length >= 1) {
        tx = caja + ',' + data;
    } else {
        tx = data;
    }
    $('#' + acc).val(tx);
}

function borrar(acc, data) {
    var caja = $('#' + acc).val();
    $('#' + acc).val('');
    var arr = caja.split(',');
    var es = $.inArray(data, arr);
    if (es >= 0) {
        arr.splice(es, 1)
    }
    $('#' + acc).val(arr.join(','));
}

function quitar(val) {
    $('#btn_' + val).remove();
    var res = val.split('-');
    borrar(res[0], res[1]);
    return false;
}

function accCerrar(val) {
    $('#btn_' + val).remove();
    var res = val.split('-');
    agregar('hid_acc_seg', res[1]);
    return false;
}

function guardar() {
    startLoad();
    var data = new FormData();
    data.append('est_id', $('#est_id').val());
    data.append('seg_id', $('#seg_id').val());
    data.append('periodo', $('#periodo').val());
    data.append('aud_est_id', $('#aud_est_id').val());
    if ($('#seg_aud').length) {
        data.append('seg_aud', $('#seg_aud').val());
        data.append('hid_acc_seg', $('#hid_acc_seg').val());
    }
    data.append('est_mat_id', $('#est_mat_id').val());
    if ($(".checked input[name='accion1']").length > 0 && $(".checked input[name='accion1']").val() !== undefined) {
        if ($('#observacion').val() !== null && $('#observacion').val() !== '') {
            data.append('observacion', $('#observacion').val());
        }
    }
    data.append('pqr', $(".checked input[name='pqr']").val());
    data.append('web_c', $(".checked input[name='web_c']").val());
    data.append('foro', $(".checked input[name='foro']").val());
    data.append('msj', $(".checked input[name='msj']").val());
    data.append('chat', $(".checked input[name='chat']").val());
    if ($(".checked input[name='acom']").val() === "si") {
        data.append('seg_eva', $('#hid_seg_eval').val());
    } else {
        data.append('seg_eva', 'No');
    }
    if ($('#preven_e').val() !== null && $('#preven_e').val() !== '') {
        data.append('preventivas', $('#preven_e').val());
    }
    if ($('#correc_e').val() !== null && $('#correc_e').val() !== '') {
        data.append('correctivas', $('#correc_e').val());
    }
    if ($(".checked input[name='accion_t']").length > 0 && $(".checked input[name='accion_t']").val() !== undefined) {
        if ($('#observacion_t').val() !== null && $('#observacion_t').val() !== '') {
            data.append('observacion_t', $('#observacion_t').val());
        }
    }
    data.append('pqr_t', $(".checked input[name='pqr_t']").val());
    data.append('web_c_t', $(".checked input[name='web_c_t']").val());
    data.append('foro_t', $(".checked input[name='foro_t']").val());
    data.append('msj_t', $(".checked input[name='msj_t']").val());
    data.append('chat_t', $(".checked input[name='chat_t']").val());
    if ($(".checked input[name='aten_t']").val() === "si") {
        data.append('atencion_t', $('#hid_atencion_t').val());
    } else {
        data.append('atencion_t', 'No');
    }
    if ($(".checked input[name='acom_t']").val() === "si") {
        data.append('seg_eva_t', $('#hid_seg_eval_t').val());
    } else {
        data.append('seg_eva_t', 'No');
    }
    if ($('#preven_t').val() !== null && $('#preven_t').val() !== '') {
        data.append('preventivas_t', $('#preven_t').val());
    }
    if ($('#correc_t').val() !== null && $('#correc_t').val() !== '') {
        data.append('correctivas_t', $('#correc_t').val());
    }
    data.append('horas_acom_t', $('#horas_acom_t').val());
    if ($('#obs_gen').val() !== null && $('#obs_gen').val() !== '') {
        data.append('obs_gen', $('#obs_gen').val());
    }else {
        data.append('obs_gen', 'n');
    }
    $.ajax({
        type: 'POST',
        url: 'src/auditoriaCB.php',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
        },
        success: function (data) {
            stopLoad();
            var res = data.split('*');
            var txt = "";
            if(res[0] !== 'n'){
                txt = txt + "Descargue la observación académica generada para el Estudiante \n\
                                <a target='_blank' href='" + res[0] + "' id='pdf' class='botones'>AQUI</a><br>";
            }
            if(res[1] !== 'n'){
                txt = txt + "Descargue la observación académica generada para el E-Mediador \n\
                                <a target='_blank' href='" + res[1] + "' id='pdf' class='botones'>AQUI</a>";
            }
            swal({
                    title: '¡Seguimiento guardado!',
                    text: txt,
                    type: 'success',
                    html: true,
                    confirmButtonColor: '#004669',
    //                closeOnConfirm: false,
                    confirmButtonText: 'Aceptar'
                },
                function () {
                    window.location.href = res[2];
            });
        }
    });
    return false;
}

function validarCampos(index) {
    var alerta = new Array();
    if (index === 0) {
        var part = 0;
        if ($(".checked input[name='web_c']").val() === undefined) {
            part++;
        }
        if ($(".checked input[name='foro']").val() === undefined) {
            part++;
        }
        if ($(".checked input[name='msj']").val() === undefined) {
            part++;
        }
        if ($(".checked input[name='chat']").val() === undefined) {
            part++;
        }
        if (part > 0) {
            alerta.push('Complete la participación del Estudiante en el curso');
        }

        if ($(".checked input[name='acom']").val() === "si") {
            if ($('#hid_seg_eval').val() === '') {
                alerta.push('Seleccione que actividad realizó el estudiante en segunda instancia');
            }
        }
        if ($(".checked input[name='acom']").val() === undefined) {
            alerta.push('Seleccione sí el estudiante presento evaluación en segunda instancia');
        }

        if ($("input[name='accion1']").length > 0 && $(".checked input[name='accion1']").val() === undefined) {
            alerta.push('Seleccione sí el Estudiante atendió las acciones');
        }
        
        if ($(".checked input[name='accion1']").val() === "no") {
            if ($('#observacion').val() === '') {
                alerta.push('Debe diligenciar la observación');
            }
        }
        
        if ($(".checked input[name='pqr']").val() === undefined) {
            alerta.push('Seleccione sí el Estudiante se presento Petición, Queja o Reclamo');
        }

    }
    if (index === 1) {
        if ($('#horas_acom_t').val() === null || $('#horas_acom_t').val() === '') {
            alerta.push('Debe digitar la cantidad de horas de acompañamiento del E-Mediador');
        }
        var part = 0;
        if ($(".checked input[name='web_c_t']").val() === undefined) {
            part++;
        }
        if ($(".checked input[name='foro_t']").val() === undefined) {
            part++;
        }
        if ($(".checked input[name='msj_t']").val() === undefined) {
            part++;
        }
        if ($(".checked input[name='chat']").val() === undefined) {
            part++;
        }
        if (part > 0) {
            alerta.push('Complete la participarión del E-Mediador en el cusro');
        }

        if ($(".checked input[name='acom_t']").val() === undefined) {
            alerta.push('Debe seleccionar sí el E-Mediador evaluó alguna actividad en segunda instancia');
        }
        if ($(".checked input[name='acom_t']").val() === "si") {
            if ($('#hid_seg_eval_t').val() === '') {
                alerta.push('Debe seleccionar que actividad evaluó el E-Mediador en segunda instancia');
            }
        }
        
        if ($("input[name='accion_t']").length > 0 && $(".checked input[name='accion_t']").val() === undefined) {
            alerta.push('Seleccione sí el E-Mediador atendió las acciones');
        }
        
        if ($(".checked input[name='accion_t']").val() === "no") {
            if ($('#observacion_t').val() === '') {
                alerta.push('Debe diligenciar la observación');
            }
        }

        if ($(".checked input[name='aten_t']").val() === "si") {
            if ($('#hid_atencion_t').val() === null || $('#hid_atencion_t').val() === '') {
                alerta.push('Complete la atención del E-Mediador al Estudiante');
            }
        }
        if ($(".checked input[name='aten_t']").val() === undefined) {
            alerta.push('Seleccione sí hubo atención del E-Mediador al Estudiante');
        }

    }
    return alerta;
}

function crearObsPDF(seguimiento_id, tipo) {
    var data = new FormData();
    var txt = "";
    if (tipo === 'e') {
        txt = "Descargue la observación académica generada para el Estudiante <br>";
    } else {
        txt = "Descargue la observación académica generada para el E-Mediador <br>";
    }
    data.append('seg_aud', seguimiento_id);
    data.append('tipo', tipo);
    $.ajax({
        type: 'POST',
        url: 'src/generarObservacion.php',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#spinner').show();
        },
        success: function (data) {
//            return data;
            $('#spinner').hide();
            swal({
                title: '¡Descargue su documento!',
                text: txt + "<a target='_blank' href='" + data + "' id='pdf' class='botones'>AQUI</a>",
                type: 'success',
                html: true,
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            //alert(data); //.hide();
        }
    });
    return false;
}
