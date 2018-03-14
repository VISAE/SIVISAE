<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);

$periodo = $_POST['periodo'];
$zona = isset($_POST['zona']) && $_POST['zona'] != '' ? implode(", ", $_POST['zona']) : "T";
$cead = isset($_POST['cead']) && $_POST['cead'] != '' ? implode(", ", $_POST['cead']) : "T";
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : "T";
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : "T";

$fecha_ini = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];


//$pagina = $_POST["page"];
$auditor = isset($_POST['auditor']) && $_POST['auditor'] != '' ? $_POST['auditor'] : 'T';

$registros;
$buscar;

$seleccionados = array();
if (isset($_POST["registros"])) {
    $registros = $_POST["registros"];
} else {
    $registros = 10;
}

//$auditor = 'T';
//echo $pagina;
if (isset($_POST["page"])) {
    $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('N' . chr(250) . 'mero de p' . chr(225) . 'gina incorrecto!');
    } //incase of invalid page number
} else {
    $page_number = 1; //Si no hay numero de pagina coloca 1 por defecto
}
//Cantidad de items a mostrar
$item_per_page = $registros;




//Obtiene la cantidad total de registros desde BD para crear la paginacion
$cantAud;
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $cantAud = mysql_fetch_array($consulta->cantReporteSeguimientoAud($_POST["buscar"], $auditor, $periodo, $cead, $zona, $escuela, $programa, $fecha_ini, $fecha_fin));
} else {
    $cantAud = mysql_fetch_array($consulta->cantReporteSeguimientoAud('n', $auditor, $periodo, $cead, $zona, $escuela, $programa, $fecha_ini, $fecha_fin));
}
$get_total_rows = $cantAud[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);

$auditores;
//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
//echo "per: $periodo, cead: $cead, zona: $zona, esc: $escuela, prog: $programa, bus:". $_POST['buscar'];
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    //echo '1';
    $asignacion = $consulta->reporteSeguimientoAud('pag', $_POST["buscar"], $auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa, $fecha_ini, $fecha_fin);
    $corteSeguimiento = $consulta->reporteCorteSeguimientoAud('pag', $_POST["buscar"], $auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa, $fecha_ini, $fecha_fin);
} else {
    //echo '2';
    $asignacion = $consulta->reporteSeguimientoAud('pag', 'n', $auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa, $fecha_ini, $fecha_fin);
    $corteSeguimiento = $consulta->reporteCorteSeguimientoAud('pag', 'n', $auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa, $fecha_ini, $fecha_fin);
}

while ($fila = mysql_fetch_array($corteSeguimiento)) {
    $res[] = array(
        'nombre' => $fila['nombre'],
        'centro' => $fila['centro'],
        'zona' => $fila['zona'],
        'asignados' => $fila['asignados'],
        'seguimientos' => $fila['seguimientos'],
        'estudiantes_con_seguimiento' => $fila['estudiantes_con_seguimiento'],
        'estudiantes_sin_seguimiento' => $fila['estudiantes_sin_seguimiento'],
        'auditor_auditor_id' => $fila['auditor_auditor_id']
    );
}




if (count($asignacion) <= 0) {
    echo 'Este auditor no tiene estudiantes asignados';
} else {
    
    echo '<br><br><font color="red" size="2"> Tenga en cuenta que el sistema toma por defecto la asignación general del auditor cuando el rango de fechas seleccionadas no corresponde al cronograma de seguimiento estipulado. Esto se realiza con el fin de hacer el cálculo solicitado. </font></br></br>';

    echo "
    
        <table id='tb_estudiantes' class='tg' style='table-layout: fixed; width=100%'>
				<thead>
					<tr>
                                                <th bgcolor='#FF0000' rowspan='2'>AUDITOR</th>
						<th rowspan='2'>ASIGNADOS</th>
                                                <th colspan='4'>PERIODO</th>
                                                <th colspan='3'>CORTE</th>
					</tr>
                                        <tr>
                                                <th>SEGUIMIENTOS</th>
                                                <th>ESTUDIANTES CON<br>SEGUIMIENTO</th>
						<th>ESTUDIANTES SIN<br>SEGUIMIENTO</th>
						<th>PORCENTAJE<br>CUMPLIMIENTO</th>
                                                <th>ESTUDIANTES CON<br>SEGUIMIENTO</th>
						<th>ESTUDIANTES SIN<br>SEGUIMIENTO</th>
						<th>PORCENTAJE<br>CUMPLIMIENTO</th>
                                        </tr>
				</thead>
                        <tbody>
                    ";
    while ($row = mysql_fetch_array($asignacion)) {
        $nombre = ucwords(preg_replace($sintilde, $tildes, $row[0]));
        $cead = ucwords(preg_replace($sintilde, $tildes, $row[1]));
        $zona = ucwords(preg_replace($sintilde, $tildes, $row[2]));
        $asignados = ucwords(preg_replace($sintilde, $tildes, $row[3]));
        $seguimientos = ucwords(preg_replace($sintilde, $tildes, $row[4]));
        $con_seguimiento = ucwords(preg_replace($sintilde, $tildes, $row[5]));
        $sin_seguimiento = ucwords(preg_replace($sintilde, $tildes, $row[6]));
        $id_auditor = ucwords(preg_replace($sintilde, $tildes, $row[7]));

        //Se consulta el % de cumplimiento establecido para el periodo
        $cumpSeg = $consulta->cantidadSeguimientoSemanal($periodo, $fecha_ini, $fecha_fin, $asignados);

        if ($cumpSeg === '0') {
            $cumpSeg = $asignados;
        }

        $estudiante_con_seguimientosCorte = 0;

        if (isset($res)) {
            foreach ($res as $resultadoCorte) {
                $idC = $resultadoCorte["auditor_auditor_id"];
                if ($idC === $id_auditor) {
                    $estudiante_con_seguimientosCorte = $resultadoCorte["estudiantes_con_seguimiento"];
                    break;
                }
            }
        }


        $estudiante_sin_seguimientosCorte = $asignados - $estudiante_con_seguimientosCorte;
        $porcentaje_periodo = round(($con_seguimiento * 100) / $asignados, 0);
        $porcentaje_corte = round(($estudiante_con_seguimientosCorte * 100) / $cumpSeg, 0);

        if ($porcentaje_periodo <= 40) {
            $colorC = '#FF6767';
        }

        if ($porcentaje_periodo >= 41 && $porcentaje_periodo <= 60) {
            $colorC = '#FCA205';
        }

        if ($porcentaje_periodo >= 61 && $porcentaje_periodo <= 90) {
            $colorC = '#FFE779';
        }

        if ($porcentaje_periodo >= 91) {
            $colorC = '#41EE41';
        }

        if ($porcentaje_corte <= 40) {
            $colorI = '#FF6767';
        }
        if ($porcentaje_corte >= 41 && $porcentaje_corte <= 60) {
            $colorI = '#FCA205';
        }

        if ($porcentaje_corte >= 61 && $porcentaje_corte <= 90) {
            $colorI = '#FFE779';
        }

        if ($porcentaje_corte >= 91) {
            $colorI = '#41EE41';
        }



        echo "<tr>"
        . "<td>$nombre<br><b>$cead</b><br><b>$zona</b></td>"
        . "<td>$asignados</td>"
        . "<td>$seguimientos</td>"
        . "<td>$con_seguimiento</td>"
        . "<td>$sin_seguimiento</td>"
        . "<td style='background-color:$colorC'>$porcentaje_periodo %</td>"
        . "<td>$estudiante_con_seguimientosCorte</td>"
        . "<td>$estudiante_sin_seguimientosCorte</td>"
        . "<td style='background-color:$colorI'>$cumpSeg - $porcentaje_corte %</td>"
        . "</tr>";
    }
    echo "     </tbody>
                    </table>";

    echo '<div align="center"><br><br>'
    . '<table><tr><td>Mostrando ' . $registros . ' registros de ' . $get_total_rows . ' encontrados.</td>'
    . '<td>';
    echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
    echo '</td>'
    . '<td> de ' . $total_pages . ' p&aacute;ginas.</td></tr></table></div>'
    . '<div id="oculto">'
    . '<input type="hidden" id="est_hid" name="est_hid[]"/>'
    . '</div>';
}
$consulta->destruir();
?>