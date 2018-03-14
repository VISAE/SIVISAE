<?php

session_start();
include_once '../config/sivisae_class.php';
// A list of permitted file extensions
$allowed = array('docx', 'rtf', 'doc', 'pdf');

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if (isset($_POST['persona'])) {
        $persona = $_POST['persona'];
    }

    $serv = $_SERVER['DOCUMENT_ROOT'] . '/sivisae/evidencias/';

    if ($accion === "agr") {
        $estudiante_materia_id = $_POST['est_mat_id'];
        $periodo = $_POST['periodo'];
        $auditor_id = $_POST['auditor'];
//$ruta = './Files/'; //Decalaramos una variable con la ruta en donde almacenaremos los archivos
        $max_tam = 1500000;
//    $renom = "$auditor_id|" . $estudiante_materia_id . "|" . $persona . "|";
        $renom = $auditor_id . SEPARADOR . $estudiante_materia_id . SEPARADOR . $persona . SEPARADOR;
        $ruta = $serv . "tod/$periodo/";
        $mensage = ''; //Declaramos una variable mensaje quue almacenara el resultado de las operaciones.

        foreach ($_FILES as $key) { //Iteramos el arreglo de archivos
            $tamano = $key['size'];
            $NombreOriginal = $key['name'];
            if ($tamano < $max_tam) {
                $partes_nombre = explode('.', $NombreOriginal);
                $extension = end($partes_nombre);
                if (in_array(strtolower($extension), $allowed)) { //Se valida si el tipo de archivo esta dentro de los permitidos
                    if ($key['error'] == UPLOAD_ERR_OK) {//Si el archivo se paso correctamente Ccontinuamos 
//            $NombreOriginal = $key['name']; //Obtenemos el nombre original del archivo
                        if (!file_exists($ruta)) {
                            mkdir($ruta, 0777, TRUE); //se crea la ruta para guardar el archivo
                        }
                        $temporal = $key['tmp_name']; //Obtenemos la ruta Original del archivo
                        $Destino = $ruta . $NombreOriginal; //Creamos una ruta de destino con la variable ruta y el nombre original del archivo	
                        move_uploaded_file($temporal, $Destino); //Movemos el archivo temporal a la ruta especificada	
                        $search = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Ñ', 'à', 'è', 'ì', 'ò', 'ù', 'Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù');
                        $replace = array('a', 'e', 'i', 'o', 'u', 'n', 'n', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u');
                        $nuevo_nombre = strtolower(str_replace($search, $replace, $NombreOriginal));
                        rename($Destino, $ruta . $renom . $nuevo_nombre);
                    }
                    if ($key['error'] == '') { //Si no existio ningun error, retornamos un mensaje por cada archivo subido
                        $mensage .= '-> Archivo <b>' . $NombreOriginal . '</b> Subido correctamente. <br>';
                    }
                    if ($key['error'] != '' && $key['error'] == '1') {//Si existio algún error retornamos un el error por cada archivo.
                        $mensage .= '-> No se pudo subir el archivo <b>' . $NombreOriginal . '</b> excede el tamaño (Max. 1.5 Mb)';
                    }
                } else {
                    $mensage .= '-> Archivo <b>' . $NombreOriginal . '</b> No fue cargado porque su formato no es valido. <br>';
                }
            } else {
                $mensage .= '-> No se pudo subir el archivo <b>' . $NombreOriginal . '</b> excede el tamaño (Max 1.5 Mb)';
            }
        }
        echo $mensage; // Regresamos los mensajes generados al cliente
    }
    if ($accion === "borrar") {
        $ruta_arch = $_POST['ruta_arch'];
        unlink($ruta_arch);
        echo "borrar: " . $ruta_arch;
    }
    if ($accion === "traer") {
//    $ruta_arch = $_POST['ruta_arch'] . $persona . '/';
        $periodo = $_POST['periodo'];
        $persona = $_POST['persona'];
        $auditor = $_POST['auditor'];
        $fecha_seg = $_POST['fecha_seg'];
        $est_mat_id = $_POST['est_mat_id'];
        $seguimiento = $_POST['seguimiento'];
//    $ruta = $serv . $ruta_arch;
        $ruta = $serv;
        $busc_arch = "";
        $ruta_link = RUTA_PPAL . "evidencias/";
        if ($seguimiento !== 'n') {
            if (strtotime($fecha_seg) > strtotime("2015-10-07")) {
                $ruta_link .= "$periodo/";
                $ruta .= "$periodo/";
                $busc_arch = $seguimiento . SEPARADOR . $est_mat_id . SEPARADOR . $persona . SEPARADOR;
            } else {
                $ruta_link .= "corte_1/";
                $ruta.="corte_1/";
                $busc_arch = "corte_1" . SEPARADOR . $seguimiento . SEPARADOR . $est_mat_id . SEPARADOR . $persona . SEPARADOR;
            }
        } else {
            $ruta_link .= "tod/$periodo/";
            $ruta .= "tod/$periodo/";
            $busc_arch = $auditor . SEPARADOR . $est_mat_id . SEPARADOR . $persona . SEPARADOR;
        }
        if (is_dir($ruta)) {
            if ($aux = opendir($ruta)) {
                while (($archivo = readdir($aux)) !== false) {
                    if ($archivo != "." && $archivo != "..") {
//                    echo $busc_arch." -- ARCH:".$archivo."<br/>";
//                    echo stristr($archivo, $busc_arch);
                        if (stristr($archivo, $busc_arch) !== false) {
                            echo "<a href='" . $ruta_link . $archivo . "'>$archivo</a>&nbsp<a href='#' onclick='return borrarArchivo(\"" . $ruta . $archivo . "\", \"$seguimiento\", \"$est_mat_id\", \"" . $persona . "\", \"$fecha_seg\")' class='equis'>X</a><br />";
                        }
                    }
                }
            }
            closedir($aux);
        }
    }
    if ($accion === "pasar") {
        $persona = "e";
        $auditor = "93";
        $ruta_arch = $_POST['ruta_arch'];
        $est_mat_id = "196508";
        $seguimiento = "10001";
        $busc_arch = $auditor . SEPARADOR . $est_mat_id . SEPARADOR;
        $serv = $_SERVER['DOCUMENT_ROOT'] . '/sivisae/evidencias/';
        $temporal = $serv . "tod/5/";
        $ruta = $serv . "5/";
//    $arch = stristr($archivo, $busc_arch);
        if ($aux = opendir($temporal)) {
            while (($archivo = readdir($aux)) !== false) {
                if ($archivo != "." && $archivo != "..") {
//                    echo $busc_arch." -- ARCH:".$archivo."<br/>";
//                    echo stristr($archivo, $busc_arch);
                    if (stristr($archivo, $busc_arch) !== false) {
                        $newArch = explode(SEPARADOR, $archivo);
                        if (rename($temporal . $archivo, $ruta . $seguimiento . SEPARADOR . $est_mat_id . SEPARADOR . $newArch[2] . SEPARADOR . $newArch[3]))
                            ;
//                                echo "RENAME";                        
                    }
                }
            }
        }
        closedir($aux);

//    $ruta_arch = $_POST['ruta_arch'];
//    unlink($ruta_arch);
//    echo "borrar: " . $ruta_arch;
    }
}
?>
