<?php

/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */

session_start();

include_once '../config/sigra_class.php';
include './paginador.php';
include_once './mail_config.php';
$consulta = new sigra_consultas();
$accion = $_POST["accion"];
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);
            
if ($accion==="buscar_participante") {
    $documento = $_POST["ced"];
    $consulta = new sigra_consultas();
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
    $consulta = new sigra_consultas();
    $documento = $_POST["doc"];
    $nombre = $_POST["nombre"];
    $estamento = $_POST["estamento"];
    $cel = $_POST["cel"];
    $tel = $_POST["tel"];
    $mail = $_POST["mail"];
    $evento_id = $_POST["evento"];
    $evento = $_POST["evento_nom"];
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

if($accion==="evento"){
    if($_POST['accion2']==="traer"){
        $even_id = $_POST['evento'];
        $infEvento = mysqli_fetch_array($consulta->traerEvento($even_id));
        $url = $infEvento['url_banner'];
        $nombre = ucwords($infEvento['nombre']);
        $html = '
            <table id="inf_proy" >
                    <tr>
                        <td align="right"><label>Organizador:</label></td>
                        <td><label style="color: #004669">&nbsp;&nbsp;'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($infEvento[6]))).'</label></td>
                    </tr>
                    <tr>
                        <td align="right"><label>Asistencia:</label></td>
                        <td><label style="color: #004669">&nbsp;&nbsp;'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($infEvento[14]))).'</label></td>
                    </tr>
                    <tr>
                        <td align="right"><label>Proyecto:</label></td>
                        <td><label style="color: #004669">&nbsp;&nbsp;'.ucwords(preg_replace($sintilde, $tildes, utf8_decode($infEvento[13]))).'</label></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" ><a href="'.RUTA_PPAL.'eventos/'.date("Y").'/'.$url.'" target="blank">
                            <img src="'.RUTA_PPAL.'eventos/'.date("Y").'/'.$url.'" alt="'.$nombre.'" width="200px" height="150px"/></a>
                        </td>
                    </tr>
                </table>';
        echo "$html";
    }
}
