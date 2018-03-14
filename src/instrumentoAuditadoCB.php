<?php

session_start();

include_once '../config/sivisae_class.php';
include_once './Seguimiento_auditor_estudianteDao.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);
$est_id = $_POST['est_id'];
$periodo = $_POST['periodo'];
$mat_id = $_POST['mat_id'];
$aud_est_id = $_POST['aud_est_id'];
$segto_id = $_POST['segto_id'];
$opc = array('no' => '0', 'si' => '1', 'ne' => '2', 'na' => '3');
$segDAO = new Seguimiento_auditor_estudianteDao();
$seguimiento = $segDAO->getObject($segto_id);
/* ---Inicio Estudiante--- */
$displ = "style='display:none'";
$display_seg_ins = "style='display:none'";
$seg_eva = "";
if ($seguimiento->evaluacion_seg_instancia !== 'No') {
    $seg_ins = explode(",", $seguimiento->evaluacion_seg_instancia);
    $display_seg_ins = "";
    $seg_eva.= "$('#hid_seg_eval').val('$seguimiento->evaluacion_seg_instancia');"
            . "$(\"input[id='acom1']\").iCheck('check');";
    foreach ($seg_ins as $eva) {
        $seg_eva .= "$(\"input[id='seg_eva" . $eva . "']\").iCheck('check');";
    }
} else {
    $seg_eva.= "$(\"input[id='acom0']\").iCheck('check');";
}
$show_obs = "$(\"input[id='accion3']\").iCheck('check');";
$show_obs.= "$('#obs_ant_e').html('" . preg_replace($sintilde, $tildes, $seguimiento->observacion) . "');";

/* ---Fin Estudiante--- */
/* ---Inicio E-Mediador--- */
$horas_acom = "";
if ($seguimiento->horas_acompanamiento !== '0') {
    $horas_acom = "$('#horas_acom_t').val('$seguimiento->horas_acompanamiento');";
} else {
    $horas_acom = "$('#horas_acom_t').val('$seguimiento->horas_acompanamiento');";
    $horas_acom .= "$(\"input[id='check_horas_acom_t']\").iCheck('check');";
    $horas_acom .= "$('#horas_acom_t').prop('disabled','disabled');";
}
$display_seg_ins_t = "style='display:none'";
$seg_eva_t = "";
if ($seguimiento->evaluacion_seg_inst_tutor !== 'No') {
    $seg_ins_t = explode(",", $seguimiento->evaluacion_seg_inst_tutor);
    $display_seg_ins_t = "";
    $seg_eva_t.= "$('#hid_seg_eval_t').val('$seguimiento->evaluacion_seg_inst_tutor');"
            . "$(\"input[id='acom_t1']\").iCheck('check');";
    foreach ($seg_ins_t as $eva_t) {
        $seg_eva_t .= "$(\"input[id='seg_eva_t" . $eva_t . "']\").iCheck('check');";
    }
} else {
    $seg_eva_t.= "$(\"input[id='acom_t0']\").iCheck('check');";
}
$show_obs_t = "$(\"input[id='accion_t3']\").iCheck('check');";
$show_obs_t.= "$('#obs_ant_t').html('" . preg_replace($sintilde, $tildes, $seguimiento->observacion_accion_tutor) . "');";

$display_atencion = "style='display:none'";
$opc_atencion = "";
if ($seguimiento->respuesta_tutor !== 'No') {
    $resp = explode(",", $seguimiento->respuesta_tutor);
    $display_atencion = "";
    $opc_atencion = "$(\"input[id='aten_t1']\").iCheck('check');";
    $opc_atencion .= "$('#hid_atencion_t').val('$seguimiento->respuesta_tutor');";
    foreach ($resp as $op) {
        $opc_atencion .= "$(\"input[id='atencion_t" . $op . "']\").iCheck('check');";
    }
} else {
    $opc_atencion = "$(\"input[id='aten_t0']\").iCheck('check');";
}
/* ---Fin E-Mediador--- */
/* ---Inicio Acciones--- */
$get_acciones = $consulta->accionesXSeguimiento($seguimiento->seguimiento_aduditor_estudiante_id);
$acciones = array();
$botones = "";
$hid_acciones = "";
if (mysql_num_rows($get_acciones) >= 1) {
    while ($row = mysql_fetch_array($get_acciones)) {
        $acc_seg = $row[0];
        $acc_id = $row[1];
        $tit = $row[2];
        $tipo = $row[3];
        if ($acc_id !== 'c') {
            $botones .= "$('#div-$tipo').append('<a href=\"#\" class=\"cerrar pend\"  id=\"btn_acc_seg-$acc_seg\" onclick=\"return accCerrar(\'acc_seg-$acc_seg\')\">$tit</a>');";
        }
    }
}
/* ---Fin Acciones--- */
$detalle = mysql_fetch_array($consulta->detalleEstMateria($est_id, $periodo, $mat_id));
$mat_consec = $detalle[0];
$desc = str_replace("cion", "ción", ucwords($detalle[1]));
$t_id = $detalle[2];
$t_ced = $detalle[3];
$t_nombre = ucwords($detalle[4]);
$t_correo = $detalle[5];
$t_skype = $detalle[6];
$t_tel = $detalle[7];
$est_mat_id = $detalle[8];

$carpeta = $est_mat_id . "";
$serv = $_SERVER['DOCUMENT_ROOT'] . '/sivisae/evidencias/';
$ruta = $serv . $carpeta;
$ruta_link = RUTA_PPAL . "evidencias/" . $carpeta;


echo "            
<script type='text/javascript' language='javascript'>

    $(function (){
	$('#wizard').steps({
                headerTag: 'h2',
                labels: {
                    finish: 'Guardar',
                    loading: 'Cargando...'
                },
		bodyTag: 'section',
		transitionEffect: 'slideLeft',
		// stepsOrientation: 'vertical',
		onStepChanging: function (event, currentIndex, newIndex) { 
			var alertas = validarCampos(currentIndex);
			var alerta = alertas.join('<br>');
			if(alertas.length>0){
				swal({
					title: 'Falta información!',
					text: 'Tiene campos vacíos: <br>'+alerta,
					html: true,
					type: 'error',
					confirmButtonColor: '#004669',
					confirmButtonText: 'Aceptar'
				});        
				return false;
			}else {
				document.getElementById('inf_e-med-'+currentIndex).scrollIntoView(true);
				$('#horas_acom_t').focus();
				return true; 
			}
		},
		onFinishing: function (event, currentIndex) { 
			var alertas = validarCampos(currentIndex);
			var alerta = alertas.join('<br>');
			if(alertas.length>0){
				swal({
					title: 'Falta información!',
					text: 'Tiene campos vacíos: <br>'+alerta,
					html: true,
					type: 'error',
					confirmButtonColor: '#004669',
					confirmButtonText: 'Aceptar'
				});        
				return false;
			}else {                            
                            swal({
                                    title: 'Comentario Final del Auditor',
                                    text: 'Opcional:',
                                    type: 'input',
                                    showCancelButton: true,
                                    closeOnConfirm: false,
                                    animation: 'slide-from-top',
                                    confirmButtonColor: '#004669',
                                    inputPlaceholder: ''
                                },
                                function(inputValue){
                                    if (inputValue === false){ return false; }
                                    else { 
                                        if (inputValue !== '') {
                                            $('#obs_gen').val(inputValue);
                                        }          
                                        guardar(); 
                                    }
                                });
                            return true; 
			}
		}
	});
	traerArchivos('$seguimiento->seguimiento_id', '$carpeta', 'e','$seguimiento->fecha_seguimiento');
	traerArchivos('$seguimiento->seguimiento_id', '$carpeta', 't','$seguimiento->fecha_seguimiento');
    });

    $(document).ready(function(){ 
        $('#horas_acom_t').keydown(function (event) {
            if (event.shiftKey){
                event.preventDefault();
            }
            if (event.keyCode === 46 || event.keyCode === 8){
            }else {
                if (event.keyCode < 95) {
                    if (event.keyCode < 48 || event.keyCode > 57) {
                        event.preventDefault();
                    }
                }else {
                    if (event.keyCode < 96 || event.keyCode > 105) {
                        event.preventDefault();
                    }
                }
            }
    });

    $('input').iCheck({
        checkboxClass: 'icheckbox_polaris',
        radioClass: 'iradio_polaris',
        increaseArea: '-20%' // optional
    });

    $(document).ready(function(){ 
        
        $(\"input[id='web_c" . $seguimiento->web_conference_est . "']\").iCheck('check');
        $(\"input[id='chat" . $seguimiento->chat_est . "']\").iCheck('check');
        $(\"input[id='msj" . $seguimiento->mensajeria_interna_est . "']\").iCheck('check');
        $(\"input[id='foro" . $seguimiento->foro_est . "']\").iCheck('check'); " .
 $seg_eva . $show_obs .
 "   $(\"input[id='pqr" . $seguimiento->pqr_estudiante . "']\").iCheck('check');" .
 $horas_acom .
 "
            $(\"input[id='web_c_t" . $seguimiento->web_conference_tutor . "']\").iCheck('check');
        $(\"input[id='chat_t" . $seguimiento->chat_tutor . "']\").iCheck('check');
        $(\"input[id='msj_t" . $seguimiento->mensajeria_interna_tutor . "']\").iCheck('check');
        $(\"input[id='foro_t" . $seguimiento->foro_tutor . "']\").iCheck('check');" .
 $seg_eva_t . $show_obs_t . $opc_atencion . $botones .
 "$('.cerrar').each(function() {
                    $(this).qtip({         
                        content: {
                            text: '<label style=\"font-size: 16px;\">Haga clic para CERRAR la acción</label>'}, 
                        style: { classes: 'qtip-red' },
                        position: {
                            my: 'bottom center',  
                            at: 'top center' },
                        show: {delay: 500},
                        hide: {delay: 1000}
                    });
                });
            
            $('#observacion, #observacion_t').keyup(function (e) {
            var t = '';
            if($(this).attr('id')==='observacion_t'){
                t = '_t';
            }
            var limite = 200;
                    var box = $(this).val();
                    var value = (box.length * 100) / limite;
                    var resta = limite - box.length;
                    if (box.length <= limite) {
                        if (resta == 0) {
                            $('#divContador'+t).html('No puede escribir mas!!!');
                        }else{
                            $('#divContador'+t).html(resta);
                        }
                        $('#divProgreso'+t).animate({'width': value + '%'}, 1);
                        if (value < 50) {
                            $('#divProgreso'+t).removeClass();
                            $('#divProgreso'+t).addClass('verde');
                        }else 
                            if (value < 85) { // si no se llegó al 85% que sea amarilla
                                $('#divProgreso'+t).removeClass();
                                $('#divProgreso'+t).addClass('amarillo');
                            }else { // si se superó el 85% que sea roja
                                $('#divProgreso'+t).removeClass();
                                $('#divProgreso'+t).addClass('rojo');
                            };
                    }else {
                            e.preventDefault();
                    }
            });

            $('#ver_obs_e').on( 'click', function(e) {	 
	        $('#obs_ant_e').toggle();
                if($('#ver_obs_e').text()==='Ver anteriores'){
                    $('#ver_obs_e').text('Ocultar');
                }
                if($('#ver_obs_e').text()==='Ocultar'){
                    $('#ver_obs_e').text('Ver anteriores');
                }
                e.preventDefault();
	    });

            $('#ver_obs_t').on( 'click', function(e) {	 
	        $('#obs_ant_t').toggle();
                if($('#ver_obs_t').text()==='Ver anteriores'){
                    $('#ver_obs_t').text('Ocultar');
                }
                if($('#ver_obs_t').text()==='Ocultar'){
                    $('#ver_obs_t').text('Ver anteriores');
                }
                e.preventDefault();
	    });
    });


    $(\"input[name='accion1']\").on('ifClicked', function (event) {
        if(this.value==='si'||this.value==='na'){
            $('#observacion').prop('disabled','disabled');
            $('#observacion').val('');
        }
        if(this.value==='no'){
            $('#observacion').prop('disabled','');
            $('#divProgreso').removeClass();
            $('#divContador').html('200');
            $('#observacion').val('');
        }
    });

    $(\"input[name='accion_t']\").on('ifClicked', function (event) {
        if(this.value==='si'||this.value==='na'){
            $('#observacion_t').prop('disabled','disabled');
            $('#observacion_t').val('');
        }
        if(this.value==='no'){
            $('#observacion_t').prop('disabled','');
            $('#divProgreso_t').removeClass();
            $('#divContador_t').html('200');
            $('#observacion_t').val('');
        }
    });

    $(\"input[name='acom']\").on('ifClicked', function (event) {
        if(this.value==='no'){
            $('.acom').hide('slide');
        }
        if(this.value==='si'){
            $('.acom').show( 'slide' );;
        }
    });

    $(\"input[name='acom_t']\").on('ifClicked', function (event) {
        if(this.value==='no'){
            $('.acom_t').hide('slide');
        }
        if(this.value==='si'){
            $('.acom_t').show( 'slide' );;
        }
    });

    $(\"input[name='aten_t']\").on('ifClicked', function (event) {
        if(this.value==='no'){
            $('.aten').hide('slide');
        }
        if(this.value==='si'){
            $('.aten').show( 'slide' );;
        }
    });

    $('#accion_preven_e').on('change', function(evt, params) {
            if(this.value!==''){
                var acc_text = $('#accion_preven_e option:selected').text();
                var acc_id = this.value;
                var r = 'preven_e-'+acc_id;
                $('#div-preven_e').append('<a href=\"#\" class=\"notif botones\"  id=\"btn_preven_e-'+acc_id+'\" onclick=\"return quitar(\''+r+'\')\">'+acc_text+'</a>');
                agregar('preven_e', acc_id);
                $('.notif').each(function() {
                    $(this).qtip({         
                        content: {text: 'Hacer clic para borrar esta accion'}, 
                        style: { classes: 'qtip-blue' },
                        position: {
                            my: 'bottom center',  
                            at: 'top center' },
                        show: {delay: 1000},
                        hide: {delay: 3000}
                    });
                });
            }
        });

        $('#accion_correc_e').on('change', function(evt, params) {
            if(this.value!==''){
                var acc_text = $('#accion_correc_e option:selected').text();
                var acc_id = this.value;
                var r = 'correc_e-'+acc_id;
                $('#div-correc_e').append('<a href=\"#\" class=\"notif botones\"  id=\"btn_correc_e-'+acc_id+'\" onclick=\"return quitar(\''+r+'\')\">'+acc_text+'</a>');
                agregar('correc_e', acc_id);
                $('.notif').each(function() {
                    $(this).qtip({         
                        content: {text: 'Hacer clic para borrar esta accion'}, 
                        style: { classes: 'qtip-blue' },
                        position: {
                            my: 'bottom center',  
                            at: 'top center' },
                        show: {delay: 1000},
                        hide: {delay: 3000}
                    });
                });
            }
        });
        
        $('#accion_preven_t').on('change', function(evt, params) {
            if(this.value!==''){
                var acc_text = $('#accion_preven_t option:selected').text();
                var acc_id = this.value;
                var r = 'preven_t-'+acc_id;
                $('#div-preven_t').append('<a href=\"#\" class=\"notif botones\"  id=\"btn_preven_t-'+acc_id+'\" onclick=\"return quitar(\''+r+'\')\">'+acc_text+'</a>');
                agregar('preven_t', acc_id);
                $('.notif').each(function() {
                    $(this).qtip({         
                        content: {text: 'Hacer clic para borrar esta accion'}, 
                        style: { classes: 'qtip-blue' },
                        position: {
                            my: 'bottom center',  
                            at: 'top center' },
                        show: {delay: 1000},
                        hide: {delay: 3000}
                    });
                });
            }
        });

        $('#accion_correc_t').on('change', function(evt, params) {
            if(this.value!==''){
                var acc_text = $('#accion_correc_t option:selected').text();
                var acc_id = this.value;
                var r = 'correc_t-'+acc_id;
                $('#div-correc_t').append('<a href=\"#\" class=\"notif botones\"  id=\"btn_correc_t-'+acc_id+'\" onclick=\"return quitar(\''+r+'\')\">'+acc_text+'</a>');
                agregar('correc_t', acc_id);
                $('.notif').each(function() {
                    $(this).qtip({         
                        content: {text: 'Hacer clic para borrar esta accion'}, 
                        style: { classes: 'qtip-blue' },
                        position: {
                            my: 'bottom center',  
                            at: 'top center' },
                        show: {delay: 1000},
                        hide: {delay: 3000}
                    });
                });
            }
        });

    });

    $('#check_horas_acom_t').on('ifChecked', function(event) {
        $('#horas_acom_t').prop('disabled','disabled');
        $('#horas_acom_t').val('0');
    })
    .on('ifUnchecked', function() {
        $('#horas_acom_t').prop('disabled','');
        $('#horas_acom_t').val('');
    });

    $('.seg_eval').on('ifChecked', function(event) {
        agregar('hid_seg_eval', $(this).val());
    })
    .on('ifUnchecked', function() {
        borrar('hid_seg_eval', $(this).val());
    });

    $('.seg_eval_t').on('ifChecked', function(event) {
        agregar('hid_seg_eval_t', $(this).val());
    })
    .on('ifUnchecked', function() {
        borrar('hid_seg_eval_t', $(this).val());
    });

    $('.atencion_t').on('ifChecked', function(event) {
        agregar('hid_atencion_t', $(this).val());
    })
    .on('ifUnchecked', function() {
        borrar('hid_atencion_t', $(this).val());
    });
            
</script>

<table id='inf_e-med-0' name='inf_e-med' class='tcar' style='width:92%'>
                    <tr class='bordes'>
                        <th class='tcar-qa4j'>Curso:</th>
                        <th class='tcar-qa4j'>E-Mediador:</th>
                        <th class='tcar-qa4j'>Correo electrónico:</th>
                        <th class='tcar-qa4j'>Skype:</th>
                        <th class='tcar-qa4j'>Teléfono:</th>
                    </tr>
                    <tr class='bordes'>
                        <td class='tcar-xitf'>$mat_consec<br>$desc</td>
                        <td class='tcar-xitf'>$t_ced<br>$t_nombre</td>
                        <td class='tcar-xitf'>$t_correo</td>
                        <td class='tcar-xitf'>$t_skype</td>
                        <td class='tcar-xitf'>$t_tel</td>
                    </tr>
                </table>
                <br>

<div id='wizard' >";


$seguimiento->seguimiento_aduditor_estudiante_id;

//Se traen las observaciones
$get_obs_histo = $consulta->observacionesXSeguimientoHistorial($seguimiento->seguimiento_aduditor_estudiante_id);
//Se traen las acciones
$get_acciones_histo = $consulta->accionesXSeguimientoHistorial($seguimiento->seguimiento_aduditor_estudiante_id);



/* HISTORICO CURSO DEL ESTUDIANTE */
echo "<h2>Histórico</h2>
    <section>
    <table id='inf_e-med-0' class='tcar' style='width:100%'>
        <tr class='bordes carg-arch_t' style='display:none'>
            <th class='tcar-qa4j' colspan='3'><strong>Observaciones del Auditor</strong></th>
        </tr>
        <tr class='bordes carg-arch_t' style='display:none'>
            <th class='tcar-qa4j'>Observación</th>
            <th class='tcar-qa4j'>Fecha</th>
            <th class='tcar-qa4j'>Tipo</th>
        </tr>
        ";

if (mysql_num_rows($get_obs_histo) >= 1) {
    while ($row = mysql_fetch_array($get_obs_histo)) {
        echo "  <tr class='bordes'>
                    <td class='tcar-xitf'>" . $row[0] . "</td>
                    <td class='tcar-xitf'>" . $row[1] . "</td>
                    <td class='tcar-xitf'>" . $row[2] . "</td>
                </tr>";
    }
} else {
    echo "  <tr class='bordes'>
                    <td colspan='3' class='tcar-xitf'>No hay observaciones</td>
                </tr>";
}

echo "
    </table>
    <br>
    <table id='inf_e-med-0' class='tcar' style='width:100%'>
         <tr class='bordes carg-arch_t' style='display:none'>
            <th class='tcar-qa4j' colspan='4'><strong>Acciones Emprendidas</strong></th>
        </tr>
        <tr class='bordes carg-arch_t' style='display:none'>
            <th class='tcar-qa4j'>Acción</th>
            <th class='tcar-qa4j'>Tipo</th>
            <th class='tcar-qa4j'>Fecha</th>
            <th class='tcar-qa4j'>Estado</th>
        </tr>";

if (mysql_num_rows($get_acciones_histo) >= 1) {
    while ($row = mysql_fetch_array($get_acciones_histo)) {
        echo "  <tr class='bordes'>
                    <td class='tcar-xitf'>" . $row[0] . "</td>
                    <td class='tcar-xitf'>" . $row[1] . "</td>
                    <td class='tcar-xitf'>" . $row[2] . "</td>
                    <td class='tcar-xitf'>" . $row[3] . "</td>
                </tr>";
    }
} else {
    echo "  <tr class='bordes'>
                    <td colspan='4' class='tcar-xitf'>No hay acciones emprendidas</td>
                </tr>";
}

echo "   </table>
    
    </section>";
/* HISTORICO CURSO DEL ESTUDIANTE */

/* INFORMACION ESTUDIANTE */
echo "
<input type='hidden' id='est_id' name='est_id' value='$est_id'>
<input type='hidden' id='periodo' name='periodo' value='$periodo'>
<input type='hidden' id='aud_est_id' name='aud_est_id' value='$aud_est_id'>  
<input type='hidden' id='est_mat_id' name='est_mat_id' value='$est_mat_id'>
<input type='hidden' id='hid_acc_seg' name='hid_acc_seg'>
<input type='hidden' id='seg_aud' name='seg_aud' value='$seguimiento->seguimiento_aduditor_estudiante_id'>
<h2>Información Estudiante</h2>
    <section>
        <table id='inf_e-med-1' class='tcar' style='width:100%'>
        </table>
        <div>
            <table class='tcar' style='width:100%'>
                <tr class='bordes'>
                    <th class='tcar-qa4j' colspan='4'>Participación del Estudiante en la realización de las actividades académicas del curso</th>
                </tr>
                <tr class='bordes'> 
                    <th class='tcar-qa4j' colspan='2'>Sincrónico</th>
                    <th class='tcar-qa4j' colspan='2'>Asincrónico</th>
                </tr>
                <tr class='bordes'>
                    <td class='tcar-xitf'>Web Conference</td>
                    <td class='tcar-xitf'>Chat</td>
                    <td class='tcar-xitf'>Mensajería Interna</td>
                    <td class='tcar-xitf'>Foro</td>
                </tr>
                <tr class='bordes'>
                    <td class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='web_c' id='web_c1'>Sí</label>
                        <label><input type='radio'  value='no' name='web_c' id='web_c0'>No</label>
                        <label><input type='radio'  value='ne' name='web_c' id='web_c2'>N/E*</label>
                    </td>
                    <td class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='chat' id='chat1'>Sí</label>
                        <label><input type='radio'  value='no' name='chat' id='chat0'>No</label>
                        <label><input type='radio'  value='ne' name='chat' id='chat2'>N/E*</label>
                    </td>
                    <td class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='msj' id='msj1'>Sí</label>
                        <label><input type='radio'  value='no' name='msj' id='msj0'>No</label>
                        <label><input type='radio'  value='ne' name='msj' id='msj2'>N/E*</label>
                    </td>
                    <td class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='foro' id='foro1'>Sí</label>
                        <label><input type='radio'  value='no' name='foro' id='foro0'>No</label>
                        <label><input type='radio'  value='ne' name='foro' id='foro2'>N/E*</label>
                    </td>
                </tr>
                <tr class='bordes'>
                    <td class='tcar-xitf'>*No se evidencia</td>
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table class='tcar' style='width:100%'>
                <tr class='bordes'>
                    <th class='tcar-qa4j' colspan='4'>¿Presentará el Estudiante alguna evaluación en segunda instancia?</th>
                </tr>
                <tr class='bordes'>
                    <td colspan='4' class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='acom' id='acom1'>Sí</label>
                        <label><input type='radio'  value='no' name='acom' id='acom0'>No</label>
                    </td>
                </tr>
                <tr class='acom bordes' $display_seg_ins>
                    <th class='tcar-qa4j' colspan='4'>Indique cuál de estas actividades tiene que presentar</th>
                </tr>
                <tr class='acom bordes' $display_seg_ins>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='h' name='seg_eva[]' id='seg_evah'>Habilitación</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='s' name='seg_eva[]' id='seg_evas'>Supletorio</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='v' name='seg_eva[]' id='seg_evav'>Validación</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='r' name='seg_eva[]' id='seg_evar'>Recalificación</label></td>
                    <input type='hidden' id='hid_seg_eval' name='hid_seg_eval'  />
                </tr>
            </table>
        </div>
        <br>
        <div class='div_acc' >
            <table class='tcar' style='width:100%'>
                <tr class='bordes'>
                    <th class='tcar-qa4j' colspan='2'>Acciones sobre el Estudiante</th>
                </tr>
                <tr class='bordes'>
                    <th class='tcar-qa4j'>Preventivas</th>
                    <th class='tcar-qa4j'>Correctivas</th>
                </tr>
                <tr class='bordes'>
                    <td class='tcar-xitf' style='vertical-align: top; '>
                        <select id='accion_preven_e'  name='acciones_preven_e' data-placeholder='Seleccione una acción' style='width:250px;' size='4' >
                            <option value=''></option>";
$list_acciones_preven_e = $consulta->traerAcciones("preven_e");
while ($row1 = mysql_fetch_array($list_acciones_preven_e)) {
    $acc_id = $row1[0];
    $acc_nombre = ucfirst(preg_replace($sintilde, $tildes, $row1[1]));
    echo "<option value='$acc_id' est='$row[2]'>";
    echo $acc_nombre;
    echo "</option>";
}
echo "  </select>
                        <br><br>
                        <div id='div-preven_e' style='min-height: 140px'>
                            <input type='hidden' id='preven_e' name='preven_e' />
                        </div>
                    <td class='tcar-xitf' style='vertical-align: top; '>
                        <select id='accion_correc_e'  name='acciones_correc_e' data-placeholder='Seleccione una acción' style='width:250px;' size='4' >
                            <option value=''></option>";
$list_acciones_correc_e = $consulta->traerAcciones("correc_e");
while ($row1 = mysql_fetch_array($list_acciones_correc_e)) {
    $acc_id = $row1[0];
    $acc_nombre = ucfirst(preg_replace($sintilde, $tildes, $row1[1]));
    echo "<option value='$acc_id'>";
    echo $acc_nombre;
    echo "</option>";
}
echo "  </select>
                        <br><br>
                        <div id='div-correc_e' style='height: 140px'>
                            <input type='hidden' id='correc_e' name='correc_e' />
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table class='tcar' style='width:100%'>
                <tr>
                    <td>
                        <table class='tcar' style='width:100%'>
                            <tr class='bordes'>
                                <th class='tcar-qa4j' colspan='2'>¿El Estudiante atendió las acciones estipuladas por el Auditor?</th>
                                <td class='tcar-xitf'>
                                    <label><input type='radio'  value='si' name='accion1' id='accion1'>Sí</label>
                                    <label><input type='radio'  value='no' name='accion1' id='accion0'>No</label>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                <td class='obs'>
                        <table class='tcar' style='width:100%'>
                            <tr class='bordes'>
                                <th class='tcar-qa4j' colspan='2'>Escriba su observación al respecto</td>
                            <tr class='bordes'>
                                <td class='tcar-xitf'>
                                    <div id='obs_ant_e' style='text-align:left; display:none'></div>
                                        <!--<a href='#' id='ver_obs_e' class='botones'>Ver anteriores</a>-->
                                        <div id='divContenedor' class='divContenedor'>
                                        <div id='divContador' class='divContador'>200</div>
                                        <div id='divCajaProgreso' class='divCajaProgreso'>
                                            <div id='divProgreso' class='divProgreso'></div>
                                        </div>
                                        <textarea id='observacion' class='observacion' name='observacion' maxlength='200'></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table style='width:100%'>
                <tr>
                    <td>
                        <table class='tcar' style='width:100%'>
                            <tr class='bordes'>
                                <th class='tcar-qa4j' colspan='2'>¿Se registran PQR por parte del Estudiante al E-mediador?</th>
                            </tr>
                            <tr class='bordes'>
                                <td class='tcar-xitf'>
                                    <label><input type='radio'  value='si' name='pqr' id='pqr1'>Sí</label>
                                    <label><input type='radio'  value='no' name='pqr' id='pqr0'>No</label>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table>
                <table style='width:100%'>
                <tr>
                    <td>
                        <table class='tcar' style='width:100%'>
                            <tr class='bordes'>
                                <th class='tcar-qa4j'>
                                    Evidencias
                                </th>
                            </tr>
                            <tr class='bordes'>
                                <td>       
                                    <table align='center'>
                                        <tr>
                                            <td>Archivo</td>
                                            <td><input type='file'  multiple='multiple' id='archivos'></td><!-- Este es nuestro campo input File-->
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td><button id='enviar_arch' onclick='return subirArchivos(\"$est_mat_id\", \"e\",\"$seguimiento->fecha_seguimiento\")'>Enviar Archivos</button></td>
                                        </tr>    
                                    </table>
                                    <div class='mensaje_e'></div>
                                </td>
                            </tr>
                            <tr class='bordes carg-arch_e' style='display:none'>
                                <th class='tcar-qa4j'>Archivos cargados</th>
                            </tr>
                            <tr class='bordes'> 
                                <td class='tcar-xitf'>
                                    <div id='arch_e' name='arch_e'></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    </tr>
            </table>
        </div>                     
        <br><br> 
    </section>";
/* FIN INFORMACION ESTUDIANTE */

/* INFORMACION E-MEDIADOR */
echo "
    <h2>Información E-Mediador</h2>
<section>
    <table id='inf_e-med-2' class='tcar' style='width:100%'>
    </table>
    <table class='tcar' style='width:100%'>
        <tr class='bordes'>
            <th class='tcar-qa4j' >Cantidad de horas establecidas según el tipo de vinculación:</th>
            <td>
                <input style='width: 180px;' min='1' max='99' id='horas_acom_t' name='horas_acom_t' type='number' maxlength='2' required='Por favor ingrese el número de horas.'/>
                <br><label><input type='checkbox'  value='ne' name='check_horas_acom_t' id='check_horas_acom_t'>N/E*</label>
            </td>
        </tr>
    </table>
    <br>
    <div>
        <table class='tcar' style='width:100%'>
            <tr class='bordes'>
                <th class='tcar-qa4j' colspan='4'>Cumplimiento del acompañamiento del E-Mediador al Estudiante</th>
            </tr>
            <tr class='bordes'> 
                <th class='tcar-qa4j' colspan='2'>Sincrónico</th>
                <th class='tcar-qa4j' colspan='2'>Asincrónico</th>
            </tr>
            <tr class='bordes'>
                <td class='tcar-xitf'>Web Conference</td>
                <td class='tcar-xitf'>Chat</td>
                <td class='tcar-xitf'>Mensajería Interna</td>
                <td class='tcar-xitf'>Foro</td>
            </tr>
            <tr class='bordes'>
                <td class='tcar-xitf'>
                    <label><input type='radio'  value='si' name='web_c_t' id='web_c_t1'>Sí</label>
                    <label><input type='radio'  value='no' name='web_c_t' id='web_c_t0'>No</label>
                    <label><input type='radio'  value='ne' name='web_c_t' id='web_c_t2'>N/E*</label>
                </td>
                <td class='tcar-xitf'>
                    <label><input type='radio'  value='si' name='chat_t' id='chat_t1'>Sí</label>
                    <label><input type='radio'  value='no' name='chat_t' id='chat_t0'>No</label>
                    <label><input type='radio'  value='ne' name='chat_t' id='chat_t2'>N/E*</label>
                </td>
                <td class='tcar-xitf'>
                    <label><input type='radio'  value='si' name='msj_t' id='msj_t1'>Sí</label>
                    <label><input type='radio'  value='no' name='msj_t' id='msj_t0'>No</label>
                    <label><input type='radio'  value='ne' name='msj_t' id='msj_t2'>N/E*</label>
                </td>
                <td class='tcar-xitf'>
                    <label><input type='radio'  value='si' name='foro_t' id='foro_t1'>Sí</label>
                    <label><input type='radio'  value='no' name='foro_t' id='foro_t0'>No</label>
                    <label><input type='radio'  value='ne' name='foro_t' id='foro_t2'>N/E*</label>
                </td>
            </tr>
            <tr class='bordes'>
                <td class='tcar-xitf'>*No se evidencia</td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table class='tcar' style='width:100%'>
            <tr class='bordes'>
                <th class='tcar-qa4j' colspan='4'>El E-Mediador hizo acompañamiento de evaluación en segunda instancia al estudiante</th>
            </tr>
            <tr class='bordes'>
                <td colspan='4' class='tcar-xitf'>
                    <label><input type='radio'  value='si' name='acom_t' id='acom_t1'>Sí</label>
                    <label><input type='radio'  value='no' name='acom_t' id='acom_t0'>No</label>
                </td>
            </tr>
            <tr class='acom_t bordes' style='display:none'>
                <th class='tcar-qa4j' colspan='4'>Indique cuál de estas actividades el E-Mediador evaluó </th>
            </tr>
            <tr class='acom_t bordes' $display_seg_ins_t>
                <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='h' name='seg_eva_t[]' id='seg_eva_th'>Habilitación</label></td>
                <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='s' name='seg_eva_t[]' id='seg_eva_ts'>Supletorio</label></td>
                <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='v' name='seg_eva_t[]' id='seg_eva_tv'>Validación</label></td>
                <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='r' name='seg_eva_t[]' id='seg_eva_tr'>Recalificación</label></td>
                <input type='hidden' id='hid_seg_eval_t' name='hid_seg_eval_t' />
            </tr>
        </table>
    </div>
    <br>
    <div id='div_acc_t' >
        <table class='tcar' style='width:100%'>
            <tr class='bordes'>
                <th class='tcar-qa4j' colspan='2'>Acciones sobre el E-Mediador</th>
            </tr>
            <tr class='bordes'>
                <th class='tcar-qa4j'>Preventivas</th>
                <th class='tcar-qa4j'>Correctivas</th>
            </tr>
            <tr class='bordes'>
                <td class='tcar-xitf' style='vertical-align: top; '>
                    <select id='accion_preven_t'  name='acciones_preven_t' data-placeholder='Seleccione una acción' style='width:250px;' size='4' >
                        <option value=''></option>";
$list_acciones_preven_e = $consulta->traerAcciones("preven_t");
while ($row1 = mysql_fetch_array($list_acciones_preven_e)) {
    $acc_id = $row1[0];
    $acc_nombre = ucfirst(preg_replace($sintilde, $tildes, $row1[1]));
    echo "<option value='$acc_id' est='$row[2]'>";
    echo $acc_nombre;
    echo "</option>";
}
echo "  </select>
                    <br><br>
                    <div id='div-preven_t' style='min-height: 140px'>
                        <input type='hidden' id='preven_t' name='preven_t' />
                    </div>
                <td class='tcar-xitf' style='vertical-align: top; '>
                    <select id='accion_correc_t'  name='acciones_correc_t' data-placeholder='Seleccione una acción' style='width:250px;' size='4' >
                        <option value=''></option>";
$list_acciones_correc_e = $consulta->traerAcciones("correc_t");
while ($row1 = mysql_fetch_array($list_acciones_correc_e)) {
    $acc_id = $row1[0];
    $acc_nombre = ucfirst(preg_replace($sintilde, $tildes, $row1[1]));
    echo "<option value='$acc_id'>";
    echo $acc_nombre;
    echo "</option>";
}
echo "  </select>
                    <br><br>
                    <div id='div-correc_t' style='height: 140px'>
                        <input type='hidden' id='correc_t' name='correc_t' />
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table class='tcar' style='width:100%'>
            <tr>
                <td>
                    <table class='tcar' style='width:100%'>
                        <tr class='bordes'>
                            <th class='tcar-qa4j' colspan='2'>¿El E-Mediador atendió las acciones estipuladas por el Auditor?</th>
                            <td class='tcar-xitf'>
                                <label><input type='radio'  value='si' name='accion_t' id='accion_t1'>Sí</label>
                                <label><input type='radio'  value='no' name='accion_t' id='accion_t0'>No</label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class='obs_t'>
                <table class='tcar' style='width:100%'>
                        <tr class='bordes'>
                            <th class='tcar-qa4j' colspan='2'>Escriba su observación al respecto</td>
                        <tr class='bordes'>
                            <td class='tcar-xitf'>
                                <div id='obs_ant_t' style='text-align:left; display:none'></div>
                                <!--<a href='#' id='ver_obs_e' class='botones'>Ver anteriores</a>-->
                                    <div id='divContenedor_t' class='divContenedor'>
                                    <div id='divContador_t' class='divContador'>200</div>
                                    <div id='divCajaProgreso_t' class='divCajaProgreso'>
                                        <div id='divProgreso_t' class='divProgreso'></div>
                                    </div>
                                    <textarea id='observacion_t' class='observacion' name='observacion_t' maxlength='200'></textarea>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table class='tcar' style='width:100%'>
            <tr class='bordes'>
                <th class='tcar-qa4j' colspan='4'>Registro de inquietud académica realizada por el Estudiante al E-Mediador</th>
                <td colspan='2' class='tcar-xitf'>
                    <label><input type='radio'  value='si' name='aten_t' id='aten_t1'>Sí</label>
                    <label><input type='radio'  value='no' name='aten_t' id='aten_t0'>No</label>
                </td>
            </tr>
            <tr class='aten bordes' $display_atencion>
                <th class='tcar-qa4j' colspan='6'>Tipo de respuesta otorgada por el E-Mediador al Estudiante</th>
            </tr>
            <tr class='aten bordes' $display_atencion>
                <td align='center' colspan='2' class='tcar-xitf'><label><input type='checkbox' class='atencion_t' value='o' name='atencion_t[]' id='atencion_to'>Oportuna</label></td>
                <td align='center colspan='2' class='tcar-xitf'><label><input type='checkbox' class='atencion_t' value='a' name='atencion_t[]' id='atencion_ta'>Amable</label></td>
                <td align='center colspan='2' class='tcar-xitf'><label><input type='checkbox' class='atencion_t' value='c' name='atencion_t[]' id='atencion_tc'>Clara</label></td>

                <input type='hidden' id='hid_atencion_t' name='hid_atencion_t' />
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table style='width:100%' class='tcar'>
            <tr class='bordes'>
                <th class='tcar-qa4j'>
                    Evidencias
                </th>
            </tr>
            <tr class='bordes'>
                <td>
                    <table align='center'>
                        <tr>
                            <td>Archivo</td>
                            <td><input type='file'  multiple='multiple' id='archivos_t'></td><!-- Este es nuestro campo input File-->
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><button id='enviar_arch_t' onclick='return subirArchivos(\"$est_mat_id\", \"t\",\"$seguimiento->fecha_seguimiento\")'>Enviar Archivos</button></td>
                        </tr>    
                    </table>
                    <div class='mensaje_t'></div>
                </td>
            </tr>
            <tr class='bordes carg-arch_t' style='display:none'>
                <th class='tcar-qa4j'>Archivos cargados</th>
            </tr>
            <tr class='bordes'> 
                <td class='tcar-xitf'>
                <div id='arch_t' name='arch_t'></div>
                </td>
            </tr>
        </table>
        <input type='hidden' name='obs_gen' id='obs_gen' />
    </div>                     
    <br><br> 
</section>";
/* FIN INFORMACION E-MEDIADOR */
echo "    
</div>
        ";

$consulta->destruir();
?>