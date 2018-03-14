<?php

/**
 * Description of auditoriaCB
 *
 * @author Ing. Andres Mendez
 */
session_start();
include_once '../config/sivisae_class.php';
include_once './generarObservacion.php';
$consulta = new sivisae_consultas();

$todayh = getdate(); //monday week begin reconvert
$d = $todayh['mday'];
$m = $todayh['mon'];
$y = $todayh['year'];
$opc = array('no' => '0', 'si' => '1', 'ne' => '2', 'na' => '3');
$est_id = $_POST['est_id'];
$periodo = $_POST['periodo'];
$auditor_estudiante_id = $_POST['aud_est_id'];
$auditor_id = $_POST['audi_id'];
$estudiante_materia_id = $_POST['est_mat_id'];
$seg_id = $_POST['seg_id'] !== 'n' ? $_POST['seg_id'] : 'n';
$pqr = $opc[$_POST['pqr']];
$web_c = $opc[$_POST['web_c']];
$foro = $opc[$_POST['foro']];
$msj = $opc[$_POST['msj']];
$chat = $opc[$_POST['chat']];
$seg_eva = $_POST['seg_eva'];
$preventivas = isset($_POST['preventivas']) ? $_POST['preventivas'] : '';
$correctivas = isset($_POST['correctivas']) ? $_POST['correctivas'] : '';
$accion_e = isset($_POST['accion_e']) ? $opc[$_POST['accion_e']] : '';
$observacion = isset($_POST['observacion']) ? $_POST['observacion'] : '';
$web_c_t = $opc[$_POST['web_c_t']];
$foro_t = $opc[$_POST['foro_t']];
$msj_t = $opc[$_POST['msj_t']];
$chat_t = $opc[$_POST['chat_t']];
$atencion_t = isset($_POST['atencion_t']) ? $_POST['atencion_t'] : 'no';
$seg_eva_t = $_POST['seg_eva_t'];
$preventivas_t = isset($_POST['preventivas_t']) ? $_POST['preventivas_t'] : '';
$correctivas_t = isset($_POST['correctivas_t']) ? $_POST['correctivas_t'] : '';
$h_acomp = $_POST['horas_acom_t'];
$accion_t = isset($_POST['accion_t']) ? $opc[$_POST['accion_t']] : '';
$observacion_t = isset($_POST['observacion_t']) ? $_POST['observacion_t'] : '';
$fecha_seg = isset($_POST['fecha_seg']) ? $_POST['fecha_seg'] : '';
$obs_general = $_POST['obs_gen'];
$gener = $_POST['gener'];
$seg_aud;
$acc_cerrar;
if (isset($_POST['seg_aud'])) {
    $seg_aud = $_POST['seg_aud'];
    $acc_cerrar = isset($_POST["hid_acc_seg"]) && $_POST["hid_acc_seg"] !== '' ? $_POST["hid_acc_seg"] : 'n';
} else {
    $seg_aud = 'n';
}

$seguimiento_id;
$enc_seg_id;
if ($seg_id === 'n') {
    $seguimiento_id = $consulta->traerSeguimiento($est_id, $periodo, $auditor_estudiante_id, 'n');
    $enc_seg_id = base64_encode($seguimiento_id);
    if ($seguimiento_id > 0) {
        $consulta->destruir();
    }
    $consulta = new sivisae_consultas();
} else {
    $seguimiento_id = $seg_id;
    $enc_seg_id = base64_encode($seg_id);
}
$seguimiento_aud;
if ($seg_aud === 'n') {
    $seguimiento_aud = $consulta->crearSeguimiento($seguimiento_id, $auditor_estudiante_id, $estudiante_materia_id, $web_c, $chat, $msj, $foro, $seg_eva, $accion_e, $pqr, $h_acomp, $web_c_t, $chat_t, $msj_t, $foro_t, $seg_eva_t, $atencion_t, $accion_t);
} else {
    $seguimiento_aud = $seg_aud;
    if ($acc_cerrar !== 'n') {
        $consulta->cerrarAcciones(explode(",", $acc_cerrar));
    }
    $consulta->updateSeguimientoAuditor($seguimiento_aud, $web_c, $chat, $msj, $foro, $seg_eva, $accion_e, $pqr, $h_acomp, $web_c_t, $chat_t, $msj_t, $foro_t, $seg_eva_t, $atencion_t, $accion_t);
}


if ($seguimiento_aud > 0) {

    if (isset($_POST['preventivas']) && $preventivas !== '') {
        $consulta->crearAccionSeguimiento($seguimiento_aud, explode(",", $preventivas));
    }
    if (isset($_POST['preventivas_t']) && $preventivas_t !== '') {
        $consulta->crearAccionSeguimiento($seguimiento_aud, explode(",", $preventivas_t));
    }
    if (isset($_POST['correctivas']) && $correctivas !== '') {
        $consulta->crearAccionSeguimiento($seguimiento_aud, explode(",", $correctivas));
    }
    if (isset($_POST['correctivas_t']) && $correctivas_t !== '') {
        $consulta->crearAccionSeguimiento($seguimiento_aud, explode(",", $correctivas_t));
    }
    if (isset($_POST['observacion']) && $observacion !== '') {
        $consulta->crearObservacion($seguimiento_aud, $observacion, 'e');
    }
    if (isset($_POST['observacion_t']) && $observacion_t !== '') {
        $consulta->crearObservacion($seguimiento_aud, $observacion_t, 't');
    }
    if (isset($_POST['obs_gen']) && $obs_general !== 'n') {
        $consulta->crearObservacion($seguimiento_aud, $obs_general, 'g');
    }
    $consulta->updateSeguimiento($seguimiento_id, $auditor_estudiante_id, $est_id, $periodo);

    $enc_id = base64_encode($est_id);
    $enc_per = base64_encode($periodo);
//    echo URL_PAGES . "sivisae_instrumento.php?st=$enc_id&pa=$enc_per&sg=$enc_seg_id";
    $serv = $_SERVER['DOCUMENT_ROOT'] . '/sivisae/evidencias/';
    $temporal = $serv . "tod/$periodo/";
    $ruta;
    if ($seg_aud !== 'n') {
        if (strtotime($fecha_seg) > strtotime("2015-10-07")) {
            $ruta = $serv . "$periodo/";
        } else {
            $ruta = $serv . "corte_1/corte_1" . SEPARADOR;
        }
    } else {
        $ruta = $serv . "$periodo/";
    }
    $busc_arch = $auditor_id . SEPARADOR . $estudiante_materia_id . SEPARADOR;
//    $arch = stristr($archivo, $busc_arch);


    if (file_exists($temporal)) {
        if (!file_exists($ruta)) {
            mkdir($ruta, 0777, TRUE);
        }
//        $consulta->mover_recursivo($temporal, $ruta);
//        $consulta->borrarDirectorio($temporal . "/$estudiante_materia_id");
    }
}
$oa_est = "n";
$oa_tut = "n";
//if ((isset($_POST['gener']) && $gener !== '')) {
//    $consulta->crearGeneralidad($seguimiento_id, explode(",", $gener));
//}
$consulta->destruir();
if ((isset($_POST['preventivas']) && $preventivas !== '') || (isset($_POST['correctivas']) && $correctivas !== '')) {
    $oa_est = crearObsrAcaPDF($seguimiento_id, 'e');
}
if ((isset($_POST['preventivas_t']) && $preventivas_t !== '') || (isset($_POST['correctivas_t']) && $correctivas_t !== '')) {
    $oa_tut = crearObsrAcaPDF($seguimiento_aud, 't');
}
if ($aux = opendir($temporal)) {
    while (($archivo = readdir($aux)) !== false) {
        if ($archivo != "." && $archivo != "..") {
//                    echo $busc_arch." -- ARCH:".$archivo."<br/>";
//                    echo stristr($archivo, $busc_arch);
            if (stristr($archivo, $busc_arch) !== false) {
                $newArch = explode(SEPARADOR, $archivo);
                if (rename($temporal . $archivo, $ruta . $seguimiento_id . SEPARADOR . $estudiante_materia_id . SEPARADOR . $newArch[2] . SEPARADOR . $newArch[3]))
                    ;
//                                echo "RENAME";                        
            }
        }
    }
}
closedir($aux);
$arr = array("o_est" => $oa_est, "o_tut" => $oa_tut, "url" => URL_PAGES . "sivisae_instrumento.php?st=$enc_id&pa=$enc_per&sg=$enc_seg_id", "segto" => $seguimiento_id);
echo json_encode($arr);
//echo "$oa_est*$oa_tut*" . URL_PAGES . "sivisae_instrumento.php?st=$enc_id&pa=$enc_per&sg=$enc_seg_id";

