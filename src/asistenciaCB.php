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
    $valor = $_POST['ced'];
    $evts = $consulta->buscarAsistente($valor);
    $html = "";
    $con = "";
    if (is_numeric($valor)) {
        $con = "N";
    }else {
        $con = "S";
    }
    if(mysqli_num_rows($evts)>0){
        $html .= '<br/><br/>
            <script>
                $(document).ready(function(){
                    $("input").iCheck({
                        checkboxClass: "icheckbox_polaris",
                        radioClass: "iradio_polaris",
                        increaseArea: "-20%" // optional
                    });
                });
            </script>
            <table id="inf_evt" class="tg" style="table-layout: fixed; width:100%">
                <thead>
                    <tr class="bo">
                        <th class="tcar-qa4j">NOMBRE</th>
                        <th class="tcar-qa4j">EVENTO</th>
                        <th class="tcar-qa4j">ESTAMENTO</th>
                        <th class="tcar-qa4j">ASISTENCIA</th>
                        <th class="tcar-qa4j">CÓDIGO<br/>CONFIRMACIÓN</th>
                        <th class="tcar-qa4j">USUARIO CONFIRMÓ</th>
                        <th class="tcar-qa4j"></th>
                    </tr>
                </thead>
                <tbody>';
        while ($row1 = mysqli_fetch_array($evts)) {
            $inscr_id = $row1['inscripcion_id'];
            $part = $row1['participante'];
            $estamento = $row1['estamento'];
            $evt = ucwords($row1['evento']);
            $as = $consulta->traerAsistencia($inscr_id);
            $asist = "No";
            $usr = "";
            $conf = "";
            if(mysqli_num_rows($as)>0){
                $asistencia = mysqli_fetch_array($as);
                $asist = $asistencia['asistencia'];
                $usr = $asistencia['usuario'];
                $conf = $asistencia['tp_conf'];
            }
            $cheS = "checked";
            $cheN = "checked";
            $date = date("Y-m-d");
            $boton = "";
            if($asist!=='No' && $asist === $date){
                $boton = "Confirmado";
            }else {
                $usr = "";
                $asist = "No";
                $boton = '<a id="confirmar-'.$inscr_id.'" class="tipo botones" onclick="confirmar(\''.$inscr_id.'\')">Confirmar</a>';
                $conf = $con;
            }
            if($conf==='S'){
                $cheN = "";
            }else {
                $cheS = "";
            }
            $html .= '<tr >
                    <td >'.$part.'</td>
                    <td >'.$evt.'</td>
                    <td align="center">'.$estamento.'</td>
                    <td align="center">'.$asist.'</td>
                    <td nowrap="nowrap" align="center">
                        <label><input type="radio" name="tp_conf-'.$inscr_id.'" id="tp_conf-S" value="S" '.$cheS.' disabled> Si</label>
                        <label><input type="radio" name="tp_conf-'.$inscr_id.'" id="tp_conf-N" value="N" '.$cheN.' disabled> No</label>
                    </td>
                    <td class="tcar-xitf">'.$usr.'</td>
                    <td align="center" style="vertical-align: central;" nowrap ">
                        <div id="res" >'.$boton.'</div>
                        <div id="btn"></div>
                    </td>
                    </tr>
                    ';
        }
        $html .= '</tbody>'
                . '</table>';
        echo $html;
    }
    
}

if($accion==="confirmar_asistencia"){
    $insc = $_POST['insc'];
    $confir = $_POST['tp_conf'];
    $ins = $consulta->confirmarAsistencia($insc, $confir, $_SESSION['usuarioid']);
}