<?php
session_start();

include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

$est_id = $_POST['est_id'];
$periodo = $_POST['periodo'];
$mat_id = $_POST['mat_id'];
$aud_est_id = $_POST['aud_est_id'];
$sintilde = explode(',', SIN_TILDES);
$tildes   = explode(',', TILDES);
$detalle = mysql_fetch_array($consulta->detalleEstMateria($est_id, $periodo, $mat_id));
$mat_consec = $detalle[0];
$desc = ucwords(preg_replace($sintilde, $tildes, $detalle[1]));
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
$ruta_link = RUTA_PPAL."evidencias/" . $carpeta;

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
		stepsOrientation: 'vertical', 
		onStepChanging: function (event, currentIndex, newIndex) { 
			var alertas = validarCampos(currentIndex+1);
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
			var alertas = validarCampos(currentIndex+1);
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
	traerArchivos('n','$carpeta', 'e','no');
	traerArchivos('n','$carpeta', 't','no');
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
    var s = 0;
        $('.es').on('ifChecked', function(event) {
            s++; 
            $('.msj_e').show('slow'); 
        })
    .on('ifUnchecked', function() {
        if(s>0){
            s--;
            if(s==0){
                $('.msj_e').hide('slow');
            }
        }else{
            $('.msj_e').hide('slow');
        }
    });
    var t = 0;
        $('.tu').on('ifChecked', function(event) {
            t++; 
            $('.msj_t').show('slow'); 
        })
    .on('ifUnchecked', function() {
        if(t>0){
            t--;
            if(t==0){
                $('.msj_t').hide('slow');
            }
        }else{
            $('.msj_t').hide('slow');
        }
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


/*INFORMACION ESTUDIANTE*/
    echo "
    <input type='hidden' id='est_id' name='est_id' value='$est_id'>
    <input type='hidden' id='periodo' name='periodo' value='$periodo'>
    <input type='hidden' id='est_mat_id' name='est_mat_id' value='$est_mat_id'>
    <h2>Información del Estudiante</h2>
            <section>
            <table id='inf_e-med-0' class='tcar' style='width:630px'>
       
            </table>
                <div id='init'></div>
                <div>
                    <table class='tcar' style='width:630px'>
                        <tr class='bordes'>
                            <th class='tcar-qa4j' colspan='4'>Participación del estudiante en la realización de las actividades académicas del curso</th>
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
                                <label><input type='radio' class='es' value='no' name='web_c' id='web_c2'>No</label>
                                <label><input type='radio'  value='ne' name='web_c' id='web_c3'>N/E*</label>
                            </td>
                            <td class='tcar-xitf'>
                                <label><input type='radio'  value='si' name='chat' id='chat'>Sí</label>
                                <label><input type='radio' class='es' value='no' name='chat' id='chat'>No</label>
                                <label><input type='radio'  value='ne' name='chat' id='chat'>N/E*</label>
                            </td>
                            <td class='tcar-xitf'>
                                <label><input type='radio'  value='si' name='msj' id='msj'>Sí</label>
                                <label><input type='radio' class='es' value='no' name='msj' id='msj'>No</label>
                                <label><input type='radio'  value='ne' name='msj' id='msj'>N/E*</label>
                            </td>
                            <td class='tcar-xitf'>
                                <label><input type='radio'  value='si' name='foro' id='foro'>Sí</label>
                                <label><input type='radio' class='es' value='no' name='foro' id='foro'>No</label>
                                <label><input type='radio'  value='ne' name='foro' id='foro'>N/E*</label>
                            </td>
                        </tr>
                        <tr class='bordes'>
                            <td class='tcar-xitf'>*No se evidencia</td>
                        </tr>
                    </table>
                </div>
                <br>
                <div>
                    <table class='tcar' style='width:630px'>
                        <tr class='bordes'>
                            <th class='tcar-qa4j' colspan='4'>¿Presentará el Estudiante alguna evaluación en segunda instancia?</th>
                        </tr>
                        <tr class='bordes'>
                            <td colspan='4' class='tcar-xitf'>
                                <label><input type='radio'  value='si' name='acom' id='acom'>Sí</label>
                                <label><input type='radio'  value='no' name='acom' id='acom'>No</label>
                            </td>
                        </tr>
                        <tr class='acom bordes' style='display:none'>
                            <th class='tcar-qa4j' colspan='4'>Indique cuál de estas actividades tiene que presentar</th>
                        </tr>
                        <tr class='acom bordes' style='display:none'>
                            <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='h' name='seg_eva[]' id='seg_eva'>Habilitación</label></td>
                            <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='s' name='seg_eva[]' id='seg_eva'>Supletorio</label></td>
                            <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='v' name='seg_eva[]' id='seg_eva'>Validación</label></td>
                            <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval' value='r' name='seg_eva[]' id='seg_eva'>Recalificación</label></td>
                            <input type='hidden' id='hid_seg_eval' name='hid_seg_eval' />
                        </tr>
                    </table>
                </div>
                <br>
                <div class='div_acc' >
                    <table class='tcar' style='width:630px'>
                        <tr class='bordes'>
                            <th class='tcar-qa4j' colspan='2'>Acciones sobre el estudiante</th>
                        </tr>
                        <tr class='bordes'>
                            <th class='tcar-qa4j'>Preventivas</th>
                            <th class='tcar-qa4j'>Correctivas</th>
                        </tr>
                        <tr class='bordes msj_e' style='text-align:center; display:none;'>
                            <td colspan='2'><label style='font-family:Tahoma !important;;color:#EF0000;'>Seleccione al menos una acción</label></td>
                        </tr>
                        <tr class='bordes'>
                            <td class='tcar-xitf' style='vertical-align: top; '>
                                <select id='accion_preven_e'  name='acciones_preven_e' data-placeholder='Seleccione una acción' style='width:250px;' size='4' >
                                    <option value=''></option>";
                                    $list_acciones_preven_e = $consulta->traerAcciones("preven_e");
                                    while ($row1 = mysql_fetch_array($list_acciones_preven_e)) {
                                        $acc_id = $row1[0];
                                        $acc_nombre = ucfirst(preg_replace($sintilde, $tildes, $row1[1]));
                                        echo "<option value='$acc_id'>";
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
               <!-- <br>
                <div>
                    <table class='tcar' style='width:630px'>
                        <tr>
                            <td>
                                <table class='tcar' style='width:230px'>
                                    <tr class='bordes'>
                                        <th class='tcar-qa4j' colspan='2'>¿El Estudiante atendió las acciones estipuladas por el Auditor?</th>
                                    </tr>
                                    <tr class='bordes'>
                                        <td class='tcar-xitf'>
                                            <label><input type='radio'  value='si' name='accion1' id='accion'>Sí</label>
                                            <label><input type='radio'  value='no' name='accion1' id='accion'>No</label>
                                            <label><input type='radio'  value='na' name='accion1' id='accion'>N/A**</label>
                                        </td>
                                    </tr>
                                    <tr class='bordes'>
                                        <td class='tcar-xitf'>**No aplica</td>
                                    </tr>
                                </table>
                            </td>
                            <td class='obs'>
                                <table class='tcar' style='width:230px'>
                                    <tr class='bordes'>
                                        <th class='tcar-qa4j' colspan='2'>Escriba su observación al respecto</td>
                                    <tr class='bordes'>
                                        <td class='tcar-xitf'>
                                            <div id='divContenedor' class='divContenedor'>
                                                <div id='divContador' class='divContador'>200</div>
                                                <div id='divCajaProgreso' class='divCajaProgreso'>
                                                    <div id='divProgreso' class='divProgreso'></div>
                                                </div>
                                                <textarea id='observacion' disabled='disabled' class='observacion' name='observacion' maxlength='200'></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div> -->
                <br>
                <div align='center'>
                    <table style='width:630px'>
                        <tr>
                            <td>
                                <table class='tcar' style='width:630px; text-align:center;'>
                                    <tr class='bordes'>
                                        <th class='tcar-qa4j'>
                                            Evidencias
                                        </th>
                                    </tr>
                                    <tr class='bordes'>
                                        <td>       
                                            <table align='center'>
                                                <tr>
                                                    <td style='text-align:center;'>Archivo</td>
                                                    <td style='text-align:center;'><input type='file'  multiple='multiple' id='archivos'></td><!-- Este es nuestro campo input File-->
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td style='text-align:center;'><button id='enviar_arch' onclick='return subirArchivos(\"$est_mat_id\", \"e\",\"no\")'>Enviar Archivos</button></td>
                                                </tr>    
                                            </table>
                                            <div class='mensaje_e'></div>
                                        </td>
                                    </tr>
                                    <tr class='bordes carg-arch_e' style='display:none'>
                                        <th class='tcar-qa4j'>
                                            Archivos cargados
                                        </th>
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
                <br>
                <div align='center'>
                    <table class='tcar' >
                        <tr class='bordes'>
                            <th class='tcar-qa4j' style='width:330px'>¿Se registran PQR por parte del Estudiante al E-mediador?</th>

                            <td class='tcar-xitf'>
                                <label><input type='radio'  value='si' name='pqr' id='pqr'>Sí</label>
                                <label><input type='radio'  value='no' name='pqr' id='pqr'>No</label>
                            </td>
                        </tr>
                    </table>
                </div> 
                <br><br> 
            </section>";
 /*FIN INFORMACION ESTUDIANTE*/

        /*INFORMACION E-MEDIADOR*/
        echo "
    <h2>Información E-Mediador</h2>
    <section>
        <table id='inf_e-med-1' class='tcar' style='width:630px'>
        </table>
        <br>
        <table class='tcar' style='width:630px'>
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
            <table class='tcar' style='width:630px'>
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
                        <label><input type='radio'  value='si' name='web_c_t' id='web_c_t'>Sí</label>
                        <label><input type='radio' class='tu' value='no' name='web_c_t' id='web_c_t'>No</label>
                        <label><input type='radio'  value='ne' name='web_c_t' id='web_c_t'>N/E*</label>
                    </td>
                    <td class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='chat_t' id='chat_t'>Sí</label>
                        <label><input type='radio' class='tu' value='no' name='chat_t' id='chat_t'>No</label>
                        <label><input type='radio'  value='ne' name='chat_t' id='chat_t'>N/E*</label>
                    </td>
                    <td class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='msj_t' id='msj_t'>Sí</label>
                        <label><input type='radio' class='tu' value='no' name='msj_t' id='msj_t'>No</label>
                        <label><input type='radio'  value='ne' name='msj_t' id='msj_t'>N/E*</label>
                    </td>
                    <td class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='foro_t' id='foro_t'>Sí</label>
                        <label><input type='radio' class='tu' value='no' name='foro_t' id='foro_t'>No</label>
                        <label><input type='radio'  value='ne' name='foro_t' id='foro_t'>N/E*</label>
                    </td>
                </tr>
                <tr class='bordes'>
                    <td class='tcar-xitf'>*No se evidencia</td>
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table class='tcar' style='width:630px'>
                <tr class='bordes'>
                    <th class='tcar-qa4j' colspan='4'>El e-mediador hizo acompañamiento de evaluación en segunda instancia al estudiante</th>
                </tr>
                <tr class='bordes'>
                    <td colspan='4' class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='acom_t' id='acom_t'>Sí</label>
                        <label><input type='radio'  value='no' name='acom_t' id='acom_t'>No</label>
                    </td>
                </tr>
                <tr class='acom_t bordes' style='display:none'>
                    <th class='tcar-qa4j' colspan='4'>Indique cuál de estas actividades el e-Mediador evaluó </th>
                </tr>
                <tr class='acom_t bordes' style='display:none'>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='h' name='seg_eva_t[]' id='seg_eva_t'>Habilitación</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='s' name='seg_eva_t[]' id='seg_eva_t'>Supletorio</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='v' name='seg_eva_t[]' id='seg_eva_t'>Validación</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='seg_eval_t' value='r' name='seg_eva_t[]' id='seg_eva_t'>Recalificación</label></td>
                    <input type='hidden' id='hid_seg_eval_t' name='hid_seg_eval_t' />
                </tr>
            </table>
        </div>
        <br>
        <div id='div_acc_t' >
            <table class='tcar' style='width:630px'>
                <tr class='bordes'>
                    <th class='tcar-qa4j' colspan='2'>Acciones sobre el E-Mediador</th>
                </tr>
                <tr class='bordes'>
                    <th class='tcar-qa4j'>Preventivas</th>
                    <th class='tcar-qa4j'>Correctivas</th>
                </tr>
                <tr class='bordes msj_t' style='text-align:center; display:none;'>
                            <td colspan='2'><label style='font-family:Tahoma !important;;color:#EF0000;'>Seleccione al menos una acción</label></td>
                </tr>
                <tr class='bordes'>
                    <td class='tcar-xitf' style='vertical-align: top; '>
                        <select id='accion_preven_t'  name='acciones_preven_t' data-placeholder='Seleccione una acción' style='width:250px;' size='4' >
                            <option value=''></option>";
                            $list_acciones_preven_e = $consulta->traerAcciones("preven_t");
                            while ($row1 = mysql_fetch_array($list_acciones_preven_e)) {
                                $acc_id = $row1[0];
                                $acc_nombre = ucfirst(preg_replace($sintilde, $tildes, $row1[1]));
                                echo "<option value='$acc_id'>";
                                echo $acc_nombre;
                                echo "</option>";
                            }
                echo "  </select>
                        <br><br>
                        <div id='div-preven_t' style='min-height: 140px'>
                            <input type='hidden' id='preven_t' name='preven_t' />
                        </div>
                    </td>
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
        <!-- <br>
        <div>
            <table class='tcar' style='width:630px'>
                <tr>
                    <td>
                        <table class='tcar' style='width:230px'>
                            <tr class='bordes'>
                                <th class='tcar-qa4j' colspan='2'>¿El E-Mediador atendió las acciones estipuladas por el Auditor?</th>
                            </tr>
                            <tr class='bordes'>
                                <td class='tcar-xitf'>
                                    <label><input type='radio'  value='si' name='accion_t' id='accion_t'>Sí</label>
                                    <label><input type='radio'  value='no' name='accion_t' id='accion_t'>No</label>
                                    <label><input type='radio'  value='na' name='accion_t' id='accion_t2'>N/A**</label>
                                </td>
                            </tr>
                            <tr class='bordes'>
                                <td class='tcar-xitf'>**No aplica</td>
                            </tr>
                        </table>
                    </td>
                    <td class='obs_t'>
                        <table class='tcar' style='width:230px'>
                            <tr class='bordes'>
                                <th class='tcar-qa4j' colspan='2'>Escriba su observación al respecto</td>
                            <tr class='bordes'>
                                <td class='tcar-xitf'>
                                    <div id='divContenedor_t' class='divContenedor'>
                                        <div id='divContador_t' class='divContador'>200</div>
                                        <div id='divCajaProgreso_t' class='divCajaProgreso'>
                                            <div id='divProgreso_t' class='divProgreso'></div>
                                        </div>
                                        <textarea id='observacion_t' disabled='disabled' class='observacion' name='observacion_t' maxlength='200'></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div> -->
        <br>
        <div>
            <table class='tcar' style='width:630px'>
                <tr class='bordes'>
                    <th class='tcar-qa4j' colspan='4'>Registro de inquietud académica realizada por el Estudiante al E-Mediador</th>
                </tr>
                <tr class='bordes'>
                    <td colspan='4' class='tcar-xitf'>
                        <label><input type='radio'  value='si' name='aten_t' id='aten_t'>Sí</label>
                        <label><input type='radio'  value='no' name='aten_t' id='aten_t'>No</label>
                    </td>
                </tr>
                <tr class='aten bordes' style='display:none'>
                    <th class='tcar-qa4j' colspan='4'>Tipo de respuesta otorgada por el E-Mediador al Estudiante</th>
                </tr>
                <tr class='aten bordes' style='display:none'>
                    <td class='tcar-xitf'><label><input type='checkbox' class='atencion_t' value='o' name='atencion_t[]' id='atencion_t'>Oportuna</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='atencion_t' value='a' name='atencion_t[]' id='atencion_t'>Amable</label></td>
                    <td class='tcar-xitf'><label><input type='checkbox' class='atencion_t' value='c' name='atencion_t[]' id='atencion_t'>Clara</label></td>
                    <input type='hidden' id='hid_atencion_t' name='hid_atencion_t' />
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table style='width:430px' class='tcar'>
                <tr class='bordes'>
                    <th class='tcar-qa4j'>Evidencias</th>
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
                                <td><button id='enviar_arch_t' onclick='return subirArchivos(\"$est_mat_id\", \"t\",\"no\")'>Enviar Archivos</button></td>
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
/*FIN INFORMACION E-MEDIADOR*/
echo "
</div>
        ";

$consulta->destruir();
?>