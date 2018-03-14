<?php

session_start();
include_once '../config/sivisae_class.php';
$allowed = array('png', 'jpeg', 'jpg', 'gif');

$ruta = $_SERVER['DOCUMENT_ROOT'] . '/sivisae/noticias_imagenes/';
$max_tam = 1500000;
$mensage = ''; //Declaramos una variable mensaje quue almacenara el resultado de las operaciones.
$idNot = $_POST['idN'];

foreach ($_FILES as $key) {
    $tamano = $key['size'];
    $NombreOriginal = $idNot . $key['name'];
    if ($tamano < $max_tam) {
        $partes_nombre = explode('.', $NombreOriginal);
        $extension = end($partes_nombre);
        if ($key['error'] == UPLOAD_ERR_OK) {//Si el archivo se paso correctamente Ccontinuamos 
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, TRUE); //se crea la ruta para guardar el archivo
            }
            $temporal = $key['tmp_name']; //Obtenemos la ruta Original del archivo
            $Destino = $ruta . $NombreOriginal; //Creamos una ruta de destino con la variable ruta y el nombre original del archivo	
            move_uploaded_file($temporal, $Destino); //Movemos el archivo temporal a la ruta especificada	
            $search = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Ñ', 'à', 'è', 'ì', 'ò', 'ù', 'Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù');
            $replace = array('a', 'e', 'i', 'o', 'u', 'n', 'n', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u');
            $nuevo_nombre = strtolower(str_replace($search, $replace, $NombreOriginal));
            rename($Destino, $ruta . $nuevo_nombre);
            //Se actualiza imagen en la noticia -- traer id noticia dentro del archivo
            $insert = new sivisae_consultas();
            $insert->guardarImagenNoticia($idNot, $nuevo_nombre);
        }
        if ($key['error'] == '') { //Si no existio ningun error, retornamos un mensaje por cada archivo subido
            $mensage .= '-> Archivo <b>' . $NombreOriginal . '</b> Subido correctamente. <br>';
        }
        if ($key['error'] != '' && $key['error'] == '1') {//Si existio algún error retornamos un el error por cada archivo.
            $mensage .= '-> No se pudo subir el archivo <b>' . $NombreOriginal . '</b> excede el tamaño (Max. 1.5 Mb)';
        }
    } else {
        $mensage .= '-> No se pudo subir el archivo <b>' . $NombreOriginal . '</b> excede el tamaño (Max 1.5 Mb)';
    }
}
echo $mensage; // Regresamos los mensajes generados al cliente 
$insert->destruir();
?>
