<?php

/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */
include_once './generarObservacion.php';
include_once '../config/sivisae_class.php';

if (isset($_POST['acc'])) {
    $segt = $_POST['seg_aud'];
    $tp = $_POST['tp'];
    
    echo crearObsrAcaPDF($segt, $tp);
//    $consulta = new sivisae_consultas();
//    $obs = $consulta->observacionesAcadEst($segt);
//    
//    foreach ($obs as $key => $value) {
//        echo "titulo: $key \n";
////        echo "value: ".$value[1]." \n";
//        if(count($value)>1){
//            echo "descrp: ".$value[0]." \n";
//            for($i=1;count($value)>$i;$i++){
//                echo "curso: ".$value[$i]." \n";
//            }
//        }
//    }
}