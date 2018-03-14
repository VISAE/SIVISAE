/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */
$(document).ready(function () {
//    $('#btn_submit').attr("disabled", true);
//    $('#btnCrear').attr("disabled", true);
//    $('#btn_submit').addClass("disable");
//    $('#btnCrear').addClass("disable");
});

function validarProyecto(tp){
    var alerta = new Array();
    
    if ($('#nombre'+tp).val() === '') {
        alerta.push('* Falta el Nombre del Proyecto');
        $('#nombre'+tp).append('</br><label id="nombre'+tp+'_alert" style="color: #EC2121">* Falta el Nombre del Proyecto</label>');
    }
    if ($('#eje'+tp).val() === '') {
        alerta.push('* Seleccione el Eje del Proyecto');
    }
    if ($('#linea'+tp).val() === '') {
        alerta.push('* Seleccione la Línea de Acción del Proyecto');
    }else {
        if ($('#linea'+tp).val() === 'o' && $('#desc_linea'+tp).val() === '') {
            alerta.push('* Falta el nombre para la nueva Línea de Acción del Proyecto');
        }
    }
    if ($('#cobertura'+tp).val() === '') {
        alerta.push('* Seleccione la Cobertura que tendrá el Proyecto');
    }else {
        if ($('#cobertura'+tp).val() === 'o') {
            alerta.push('* Falta guardar la nueva Cobertura para el Proyecto');
        }
        if ($('.chzn').length && $('.chzn').val() === null){ 
            switch ($('#cobertura'+tp+' :selected').text()){
                case "Cead":
                    alerta.push('* Seleccione el(los) Centro(s) para el Proyecto');
                    break;
                case "Escuela":
                    alerta.push('* Seleccione la(s) Escuela(s) para el Proyecto');
                    break;
                case "Programa":
                    alerta.push('* Seleccione el(los) Programa(s) para el Proyecto');
                    break;
                case "Zona":
                    alerta.push('* Seleccione la(s) Zona(s) para el Proyecto');
                    break;
            }
        }
    }
    return alerta;
}

function validarProyecto(tp){
    var alerta = new Array();
    
    if ($('#nombre'+tp).val() === '') {
        alerta.push('* Falta el Nombre del Proyecto');
        $('#nombre'+tp).append('</br><label id="nombre'+tp+'_alert" style="color: #EC2121">* Falta el Nombre del Proyecto</label>');
    }
    if ($('#eje'+tp).val() === '') {
        alerta.push('* Seleccione el Eje del Proyecto');
    }
    if ($('#linea'+tp).val() === '') {
        alerta.push('* Seleccione la Línea de Acción del Proyecto');
    }else {
        if ($('#linea'+tp).val() === 'o' && $('#desc_linea'+tp).val() === '') {
            alerta.push('* Falta el nombre para la nueva Línea de Acción del Proyecto');
        }
    }
    if ($('#cobertura'+tp).val() === '') {
        alerta.push('* Seleccione la Cobertura que tendrá el Proyecto');
    }else {
        if ($('#cobertura'+tp).val() === 'o') {
            alerta.push('* Falta guardar la nueva Cobertura para el Proyecto');
        }
        if ($('.chzn').length && $('.chzn').val() === null){ 
            switch ($('#cobertura'+tp+' :selected').text()){
                case "Cead":
                    alerta.push('* Seleccione el(los) Centro(s) para el Proyecto');
                    break;
                case "Escuela":
                    alerta.push('* Seleccione la(s) Escuela(s) para el Proyecto');
                    break;
                case "Programa":
                    alerta.push('* Seleccione el(los) Programa(s) para el Proyecto');
                    break;
                case "Zona":
                    alerta.push('* Seleccione la(s) Zona(s) para el Proyecto');
                    break;
            }
        }
    }
    return alerta;
}