<?php

/*
 * 
 *   @author Ing. Andres Mendez
 * 
 */
include_once '../config/sivisae_class.php';
$cons = new sivisae_consultas();
//if (isset($_POST['gener'])) {
if ($_POST['gener']<>'') {
    $gener = $_POST['gener'];
    $seguimiento = $_POST['seguimiento'];
//print_r(explode(",", $gener));
    $fin = array_diff(explode(",", $gener), $cons->getGeneralidades($seguimiento));
    $cerrar = array_diff($cons->getGeneralidades($seguimiento), explode(",", $gener));
    if (count($cerrar) > 0) {
        $cons->cerrarGeneralidades($seguimiento, implode(",", $cerrar));
    }
    $cons->crearGeneralidad($seguimiento, explode(",", $gener));
}