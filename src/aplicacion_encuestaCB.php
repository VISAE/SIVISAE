<?php

/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */
include_once '../config/sigra_class.php';
include './paginador.php';
include_once './mail_config.php';
$consulta = new sigra_consultas();
$accion = $_POST["accion"];
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);

if ($accion==="buscar_participante") {
    $documento = $_POST["ced"];
    $data = $consulta->buscarParticipante($documento);
    $retorno = array();
    if(mysqli_num_rows($data)>0){
        $participante = mysqli_fetch_array($data);
        $retorno[] = $participante;
    }else {
        $retorno[] = array("participante_id"=>"n");
    }
    $consulta->destruir();
    $consulta->destruir2();
    echo json_encode($retorno);
}

if($accion === "guardar_participante"){
    $documento = $_POST["doc"];
    $nombre = $_POST["nombre"];
    $estamento = $_POST["estamento"];
    $cel = $_POST["cel"];
    $tel = $_POST["tel"];
    $mail = $_POST["mail"];
    $encuesta_id = $_POST["encuesta"];
    $part = $consulta->gestParticipante($documento, $nombre, $tel, $cel, $mail, $estamento);
    if($part>0){
        $token = $consulta->validarToken();
        $mail = new mail_config();
//        echo $env;
        $l = $consulta->inscribir($part, $evento_id, $token);
        if($l){
            $env = $mail->enviarInvEvento("Código de confirmación del evento ".$evento, $token, "cristian.patino@unad.edu.co", $nombre, $evento, $documento);
            echo "<label style='color: #004669'>Su registro ha sido satisfactorio.</label>";
        }else {
            echo "<label style='color: #EC2121'>Usted ya esta inscrito en este evento.$l</label>";
        }
    }

//    $consulta->destruir();
//    $consulta->destruir2();
//    echo $evento;
}

if ($accion === "empezar") {
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $encuesta = base64_decode($_POST['encuesta']);
    $modulos = $consulta->getModulos($encuesta);
    
    $html = '
        <script src="js/sigra/tools-encuestas.js"></script>
        <script>
                      $(function() {
                        $( ".fecha" ).datepicker({
                          showOtherMonths: true,
                          selectOtherMonths: true,
                          changeMonth: true,
                          changeYear: true,
                          dateFormat: "yy-mm-dd",
                          yearRange: "-80:+0"
                        });
                      });
                </script>
                <div align="center" style="background-color: #004669">
                        <h2 id="p_fieldset_autenticacion_2">
                            ENCUESTA 
                        </h2>
                    </div>
                    <br/>
                    <form id="frm_actualizar">
                            <div id="encuesta" class="encuesta">';
    while ($mod = mysqli_fetch_array($modulos)) {
        $mod_id = $mod['modulo_encuesta_id'];
        $nombre = $mod['nombre'];
               $html .= '     <h3>'.$nombre.'</h3>
                                <section >
                                    <table style="width: 90%;" id="">';
               $preguntas = $consulta->traerPreguntas($mod_id);
               while ($preg = mysqli_fetch_array($preguntas)) {
                   $preg_id = $preg['pregunta_id'];
                   $enunciado = $preg['enunciado'];
                   $tp_preg = $preg['tipo_preg'];
                   $html .= '<tr>
                            <td>
                                '.$enunciado.'
                           </td>
                           </tr>';          
                   $respuestas = $consulta->traerRespuestas($preg_id);
                   $type = $tp_preg === "UNICA" ? "radio" : "checkbox";
                   while ($resp = mysqli_fetch_array($respuestas)) {
                       $resp_id = $resp['respuesta_id'];
                       $enunc_r = $resp['enunciado'];
                       $html .= '<tr>
                                <td><input type="'.$type.'" />&nbsp;&nbsp;&nbsp;&nbsp;'.$enunc_r.'</td>
                               </tr>';                   
                   }
               }
               $html .= ' </table>
                                </section>';
    }                                
                    $html .= '</div>
                            </form>';
    $consulta->destruir();
    $consulta->destruir2();
    echo $html;
}