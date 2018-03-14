<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);


//Se obtienen las variables

$zona = isset($_POST['zona']) && $_POST['zona'] != '' ? implode(", ", $_POST['zona']) : "T";
$cead = isset($_POST['cead']) && $_POST['cead'] != '' ? implode(", ", $_POST['cead']) : "T";
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : "T";
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : "T";
$auditor = isset($_POST['auditor']) && $_POST['auditor'] != '' ? $_POST['auditor'] : 'T';
$f_inicial = isset($_POST['fecha_inicio']) && $_POST['fecha_inicio'] != '' ? $_POST['fecha_inicio'] : 'T';
$f_final = isset($_POST['fecha_fin']) && $_POST['fecha_fin'] != '' ? $_POST['fecha_fin'] : 'T';
$buscar;
$seleccionados = array();

//Base general

$atenciones = $consulta->ReporteAtenciones($auditor, 1, $_SESSION["perfilid"], $_SESSION["modulo"]);
$cont_a = 0;

while ($rowC = mysql_fetch_array($atenciones)) {
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $datosC = $consulta->ConteoDatosReporteAtenciones($rowC[0], $_POST["buscar"], $zona, $cead, $escuela, $programa, $f_inicial, $f_final);
    } else {
        $datosC = $consulta->ConteoDatosReporteAtenciones($rowC[0], 'n', $zona, $cead, $escuela, $programa, $f_inicial, $f_final);
    }
    while ($rowDataC = mysql_fetch_array($datosC)) {
        $cont_a++;
    }
}

$atenciones2 = $consulta->ReporteAtenciones($auditor, 2, $_SESSION["perfilid"], $_SESSION["modulo"]);

// lista de categorias
$catList = array();
$categorias = $consulta->categoriasAtencion($_SESSION["modulo"]);
while ($fila = mysql_fetch_array($categorias)) {
    $catList[] = $fila;
}

$result = "";

$result.= "
    <br><br>
    <div style='overflow-y: hidden; overflow-x:scroll;'>
    
        <table id='tb_estudiantes' class='tg' style='table-layout: fixed; width=300px;'>
				<thead>
                                        <tr>
                                                <th>DOCUMENTO ESTUDIANTE</th>
						<th>NOMBRE</th>
						<th>PROGRAMA</th>
                                                <th>ESCUELA</th>
                                                <th>CENTRO</th>
                                                <th>ZONA</th>
                                                <th>TELÉFONO</th>
                                                <th>CORREO</th>
                                                <th>CATEGORIA</th>                                                   
                                                <th>TIPO</th>
                                                <th>FECHA ATENCIÓN</th>";
if ($_SESSION["perfilid"] != 9 && $_SESSION["perfilid"] != 8) {
    $result.= "<th>CONSEJERO</th>";
} else {
    $result.= "<th>MONITOR</th>";
}
$result.= "
					</tr>
				</thead>
                        <tbody>
                    ";
$cont = 0;
$catAtendidas = "";
while ($row = mysql_fetch_array($atenciones2)) {
    $id = $row[0];
    //Se muestra reporte de los atendidos
    $datos = $consulta->DatosReporteAtenciones($id, $_POST["buscar"], $zona, $cead, $escuela, $programa, $f_inicial, $f_final);
    while ($rowData = mysql_fetch_array($datos)) {

        if ($cont < 100) {
            //Se hace el conteo de categorias 
            $i = 0;

            foreach (split(",", $rowData[8]) as $categoria) {
                $catAtendidas.=$categoria . '|';
            }

            if ($rowData[16] == "4") {
                $result.= "<tr>"
                        . "<td>$rowData[0]</td>"
                        . "<td colspan='3'>Registro migrado formulario Drive</td>"
                        . "<td>" . $rowData["cead"] . "</td>"
                        . "<td>$rowData[5]</td>"
                        . "<td colspan='2'>Registro migrado formulario Drive</td>"
                        . "<td>$rowData[8]</td>"
                        . "<td>$rowData[9]</td>"
                        . "<td>$rowData[10]</td>"
                        . "<td>$rowData[15]</td>"
                        . "</tr>";
            } else {
                $result.= "<tr>"
                        . "<td>$rowData[0]</td>"
                        . "<td>" . $rowData["nombre_estudiante"] . "</td>"
                        . "<td>" . $rowData["programa"] . "</td>"
                        . "<td>" . $rowData["escuela"] . "</td>"
                        . "<td>$rowData[4]</td>"
                        . "<td>$rowData[5]</td>"
                        . "<td>$rowData[6]</td>"
                        . "<td>$rowData[7]</td>"
                        . "<td>$rowData[8]</td>"
                        . "<td>$rowData[9]</td>"
                        . "<td>$rowData[10]</td>"
                        . "<td>$rowData[15]</td>"
                        . "</tr>";
            }
        }
        $cont++;
    }
}


$result.= "     </tbody>
                    </table></div>";

$i = 0;
//echo $catAtendidas;
foreach ($catList as $fila) {
    foreach (explode("|", $catAtendidas) as $catego) {
        //echo ' fila ' . $fila[1] . ' catego: ' . $catego . ' <br>';
        if (strcmp($fila[1], $catego) === 0) {
            $catList[$i][2] = $catList[$i][2] + 1;
        }
    }
    $i++;
}

if ($cont == 0) {
    echo 'No se encontraron registros';
} else {
    echo "
        <table id='tb_categorias' class='tg' style='table-layout: fixed; width=300px'>
				<thead>
                                        <tr>
                                                <th>CATEGORIA</th>
						<th>TOTAL</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    $i = 0;
    $j = 0;
    for ($i = 0; $i < count($catList); $i++) {
        for ($j = 0; $j < count($catList); $j++) {
            if ($catList[$i][2] > $catList[$j][2]) {
                $tempA = $catList[$i][1];
                $tempB = $catList[$i][2];
                $catList[$i][1] = $catList[$j][1];
                $catList[$i][2] = $catList[$j][2];
                $catList[$j][1] = $tempA;
                $catList[$j][2] = $tempB;
            }
        }
    }

    foreach ($catList as $fila) {
        if ($fila[2] > 0) {
            echo "<tr>"
            . "<td>$fila[1]</td>"
            . "<td>$fila[2]</td>"
            . "</tr>";
        }
    }
    echo "     </tbody>
                    </table>";
    echo "<br><br>";

    echo " <table>"
    . " <tr>"
    . "<td>"
    . "<a href='#' onclick='return crearReporte()'  id='excel-atenciones' class='botones'>Descargue listado detallado</a>"
    . "</td>"
    . "</tr>"
    . "</table>";

    echo '<div align="center"><br><br>'
    . '<table><tr><td>Mostrando 100 primeros registros de ' . $cont . ' encontrados.</td>'
    . '</tr></table></div>';

    echo $result;
}
$consulta->destruir();
