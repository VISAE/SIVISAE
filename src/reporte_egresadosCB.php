<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);

$cohorte = $_POST['periodo'];
$zona = isset($_POST['zona']) && $_POST['zona'] != '' ? implode(", ", $_POST['zona']) : "T";
$cead = isset($_POST['cead']) && $_POST['cead'] != '' ? implode(", ", $_POST['cead']) : "T";
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : "T";
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : "T";
//$pagina = $_POST["page"];
//$auditor = isset($_POST['auditor']) && $_POST['auditor'] != '' ? $_POST['auditor'] : 'T';

$registros;
$buscar;
$seleccionados = array();
if (isset($_POST["registros"])) {
    $registros = $_POST["registros"];
} else {
    $registros = 10;
}

$momentos = $_POST["momentos"];


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
    $cantAud = $consulta->cantReporteEgresados($_POST["buscar"], $cohorte, $zona, $cead, $escuela, $programa, $momentos);
} else {
    $cantAud = $consulta->cantReporteEgresados('n', $cohorte, $zona, $cead, $escuela, $programa, $momentos);
}
$get_total_rows = $cantAud;

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);


if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $egresados_R = $consulta->ReporteEgresados($_POST["buscar"], $cohorte, $zona, $cead, $escuela, $programa, $momentos, $page_position, $item_per_page);
} else {
    $egresados_R = $consulta->ReporteEgresados('n', $cohorte, $zona, $cead, $escuela, $programa, $momentos, $page_position, $item_per_page);
}

if (count($egresados_R) <= 0) {
    echo 'No existen resultados para esta consulta';
} else {

    echo "<br>
        
        <a href='#' onclick='return crearReporte(1)'  id='excel-asignados' class='botones'>Descargue listado general</a>
        <a href='#' onclick='return crearReporte(2)'  id='excel-asignados' class='botones'>Descargue listado para comunicaciones </a>
        <a href='#' onclick='return crearReporte(3)'  id='excel-asignados' class='botones'>Descargue listado para Momento Cero </a>";
        
        

    echo "
    <br><br>
        <div style='overflow-y: hidden; overflow-x:scroll;'>
        <table id='tb_estudiantes' class='tg' style='table-layout: fixed; width=300px'>
				<thead>
                                        <tr>
                                                <th>DOCUMENTO</th>
						<th>NOMBRE</th>
						<th>AREA GEOGRÁFICA</th>
                                                <th>GÉNERO</th>
                                                <th>DIRECCIÓN</th>
                                                <th>EMAIL</th>
                                                <th>TELÉFONO</th>
                                                <th>CIUDAD RESIDENCIA</th>
                                                <th>PROGRAMA</th>
                                                <th>ESCUELA</th>
                                                <th>CENTRO</th>
                                                <th>ZONA</th>
                                                <th>MES</th> 
                                                <th>AÑO</th>                                                
                                                <th>NIVEL</th>
                                                <th>SITUACIÓN LABORAL</th>
                                                <th>NOMBRE EMPRESA</th>
                                                <th>DIRECCIÓN EMPRESA</th>
                                                <th>TELÉFONO EMPRESA</th>
                                                <th>ANTIGUEDAD</th>
                                                <th>TIEMPO DESEMPLEADO</th>
                                                <th>CARGO</th>
                                                <th>SECTOR</th>
                                                <th>RELACIÓN PROGRAMA-TRABAJO</th>
                                                <th>ESTADO</th>
                                                <th>MOMENTO</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    while ($row = mysql_fetch_array($egresados_R)) {
        echo "<tr>"
        . "<td>$row[0]</td>"
        . "<td>$row[3]</td>"
        . "<td>$row[4]</td>"
        . "<td>$row[5]</td>"
        . "<td>$row[6]</td>"
        . "<td>$row[7]</td>"
        . "<td>$row[8]</td>"
        . "<td>$row[9]</td>"
        . "<td>$row[10]</td>"
        . "<td>$row[11]</td>"
        . "<td>$row[12]</td>"
        . "<td>$row[13]</td>"
        . "<td>$row[14]</td>"
        . "<td>$row[15]</td>"
        . "<td>$row[16]</td>"
        . "<td>$row[17]</td>"
        . "<td>$row[19]</td>"
        . "<td>$row[20]</td>"
        . "<td>$row[21]</td>"
        . "<td>$row[22]</td>"
        . "<td>$row[23]</td>"
        . "<td>$row[24]</td>"
        . "<td>$row[25]</td>"
        . "<td>$row[26]</td>"
        . "<td>$row[27]</td>"
        . "<td>$row[28]</td>";
    }

    echo "     </tbody>
                    </table></div>";

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
