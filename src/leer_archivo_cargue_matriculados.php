<?php

session_start();
include_once './excel/PHPExcel.php';
include_once '../config/sivisae_class.php';
//include_once './crear_reporteCB.php';
//set_time_limit(300);
if (!isset($_FILES['archivo'])) {
    echo 'Ha habido un error, tiene que elegir un archivo';
} else {
    $nombre = $_FILES['archivo']['name'];
    $nombre_tmp = $_FILES['archivo']['tmp_name'];
    $tipo = $_FILES['archivo']['type'];
    $tamano = $_FILES['archivo']['size'];

    $ext_permitidas = array('xls', 'xlsx', 'csv');
    $partes_nombre = explode('.', $nombre);
    $extension = end($partes_nombre);
    $ext_correcta = in_array($extension, $ext_permitidas);

    $tipo_correcto = preg_match('/^excel\/(xls|xlsx|csv)$/', $tipo);

    $limite = 500 * 1024;
    $consulta = new sivisae_consultas();
    $estudiantes = array();

    if ($ext_correcta) {
        if ($_FILES['archivo']['error'] > 0) {
            echo 'Error: ' . $_FILES['archivo']['error'] . '<br/>';
        } else {
            move_uploaded_file($nombre_tmp, "../tmp/" . $nombre);

            $phpExcel = PHPExcel_IOFactory::load("../tmp/" . $nombre);
            $hoja = $phpExcel->getActiveSheet()->toArray(true, true, true);

            foreach ($hoja as $indice => $fila) {
                if($indice == 5) { // captura periodo
                    $periodo = $consulta->consultaPeriodo($fila[0]);
                    if ($row = mysqli_fetch_array($periodo))
                        $periodo = $row[0];
                }
                if($indice == 6) //captura encabezados
                    $encabezados = $fila;
                if($indice > 6) { // Inicio informacion estudiantes
                    $estudiante = array_combine($encabezados, $fila);
                    $centro = preg_replace("/\(.*?\)/", "", $estudiante["Centro"]);
                    $idCentro = $consulta->consultaCentro($centro);
                    $idCentro = mysqli_fetch_array($idCentro)[0];
                    $idPrograma = $consulta->consultaPrograma($estudiante["Programa"]);
                    $idPrograma = mysqli_fetch_array($idPrograma)[0];
                    $estrato = $consulta->consultaEstrato($estudiante["Estrato"]);
                    $estrato = mysqli_fetch_array($estrato)[0];
                    $etnia = $consulta->consultaEtnia($estudiante["Etnia"]);
                    $etnia = mysqli_fetch_array($etnia)[0];
                    $discapacidad = $consulta->consultaDiscapacidad($estudiante["Discapacidad"]);
                    $discapacidad = mysqli_fetch_array($discapacidad)[0];
                    $tipo = ($estudiante["Tipo"] == "NUEVO")?"H":"G";
                    $genero = ($estudiante["Genero"] == "MASCULINO")?"M":"F";
                    $usuario = explode("@", $estudiante["Email Institucional"])[0];

                    $datosEstudiante = $consulta->consultaEstudiante($estudiante["Código"]);
                    if ($row = mysqli_fetch_array($datosEstudiante)) { // si encuentra datos del estudiante
                        /*    $datosMatricula = $consulta->consultaMatricula($row[0], $periodo, $idPrograma);
                            if(!($row_1 = mysqli_fetch_array($datosMatricula))) { // si no existen datos de matrícula
                                //$idMatricula = $consulta->agregaMatricula($row[0], $periodo, $idPrograma, $tipo, 1);
                            }
                            $estudiantes[] = "Antiguo: ". $row[0] ." matricula: ".$row_1[0];
                            $estudiantes[] = "Antiguo: ". $row[0] ." matricula: ".$idMatricula;
                        } else {
                            $idEstudiante = $consulta->agregaEstudiante($estudiante, $idCentro, $genero, $usuario, $estrato, $etnia, $discapacidad);
                            $idMatricula = $consulta->agregaMatricula($row[0], $periodo, $idPrograma, $tipo, 1);

                            $estudiantes[] = "Nuevo: ". $row[0] ." matricula: ".$idMatricula;
                            $estudiantes[] = $row[0];*/
                    }
                    $estudiantes[] = $idCentro.' '.$idPrograma.' '.$estrato.' '.$etnia.' '.$discapacidad.' '.$tipo.' '.$genero.' '.$usuario;
                }
            }

//            echo array_search('Discapacidad',$estudiantes[0]);
            echo json_encode($estudiantes);
        }
    } else {
        echo 'Archivo inválido ';
    }
}