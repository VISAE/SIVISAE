<?php

session_start();
include_once './excel/PHPExcel.php';
include_once '../config/sivisae_class.php';
include_once './crear_reporteCB.php';
if (!isset($_FILES['archivo'])) {
    echo 'Ha habido un error, tienes que elegir un archivo';
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
                $aux = "";
                $cont = 0;
                foreach ($fila as $celda) {
                    if ($celda != "") {
                        $cont++;
                        if ($cont == 1) {
                            $aux.=$celda;
                        } else {
                            $aux.=";" . $celda;
                        }
                    }
                }
                $estudiantes[] = $aux;
            }


            $result = $consulta->cargarAsignarEstudiantesConsejeria(array_slice($estudiantes, 1), $_SESSION['usuarioid']);
            echo "
     
<table><tr>";
            if ($result[0] !== '1') {
                $asig = array();
                while ($fila = mysql_fetch_array($result[0])) {
                    $asig[] = $fila;
                }
                $titulo = "Reporte de estudiantes ya asignados";
                $columnas = array("Cedula Estudiante", "Nombre Estudiante", "Cedula Consejero", "Nombre Consejero");
                $nombre_arch = "Estudiantes ya asignados";
                $desc = "Reporte que contiene un listado de estudiantes que ya habían sido asignados";
                $ruta = generarReporte($titulo, $columnas, $asig, $nombre_arch, $desc);

                echo "<td >Algunos estudiantes ya estaban asignados: &nbsp&nbsp";
                echo mysql_num_rows($result[0]) . "<br>";
                echo "<br>Descargue el reporte <a href='" . RUTA_PPAL . "$ruta'>Aquí</a>";
                echo "<br><div id='asignados' class='tg' style='display:none'><table>"
                . "<colgroup>
                        <col style='width: 300px'>
                        <col style='width: 300px'>
                    </colgroup>
                        <tr>
                            <th class='tg-r31a'>Estudiante</th>
                            <th class='tg-r31a' >Consejero</th>
                        </tr>";
//                while ($row = mysql_fetch_array($result[0])) {
                foreach ($asig as $row) {
                    echo "<tr><td class='tg-7ser'>$row[0]"
                    . "<br>" . ucwords($row[1]) . "</td>"
                    . "<td class='tg-7ser'>$row[2]"
                    . "<br>" . ucwords($row[3]) . "</td>"
                    . "</tr>";
                }
                echo "</table></div></td>";
            }

            if ($result[1] !== '0') {
                $asignar = array();
                while ($fila = mysql_fetch_array($result[1])) {
                    $asignar[] = $fila;
                }
                $titulo = "Reporte de estudiantes asignados";
                $columnas = array("Cedula Estudiante", "Nombre Estudiante", "Cedula Consejero", "Nombre Consejero");
                $nombre_arch = "Estudiantes recien Asignados";
                $desc = "Reporte que contiene un listado de estudiantes que han sido asignados en este cargue";
                $ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);


                echo "<td >Estudiantes asignados en este cargue: &nbsp&nbsp";
                echo mysql_num_rows($result[1]);
                echo "<br>Descargue el reporte <a href='" . RUTA_PPAL . "$ruta'>Aquí</a>";
                echo "<br><div id='recien' class='tg' style='display:none'><table>"
                . "<colgroup>
                        <col style='width: 300px'>
                        <col style='width: 300px'>
                    </colgroup>
                        <tr>
                            <th class='tg-r31a'>Estudiante</th>
                            <th class='tg-r31a' >Consejero</th>
                        </tr>";
//                while ($row = mysql_fetch_array($result[0])) {
                foreach ($asignar as $row) {
                    echo "<tr><td class='tg-7ser'>$row[0]"
                    . "<br>" . ucwords($row[1]) . "</td>"
                    . "<td class='tg-7ser'>$row[2]"
                    . "<br>" . ucwords($row[3]) . "</td>"
                    . "</tr>";
                }
                echo "</table></div></td>";
            } else {
                echo "<td>No se asignaron los estudiantes, por vafor compruebe que <br>si pertenezcan a el periodo escrito en el archivo</td>";
            }
        }
        echo "</tr></table>";
    } else {
        echo 'Archivo inválido ';
    }
//    }
}