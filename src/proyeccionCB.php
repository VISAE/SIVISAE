<?php
session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();


$periodo = $_POST['periodo'];
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : 'T';
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : 'T';
//$pagina = $_POST["page"];
$auditor = $_POST['auditores'];
$registros;
$buscar;
$seleccionados = array();
if (isset($_POST["registros"])) {
    $registros = $_POST["registros"];
} else {
    $registros = 10;
}

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
$cantH;
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $cantH = mysql_fetch_array($consulta->filtrarCantHallazgos($auditor, $_POST["buscar"], $periodo, $escuela, $programa));
} else {
    $cantH = mysql_fetch_array($consulta->cantHallazgos2($auditor, $periodo, $escuela, $programa));
}
$get_total_rows = $cantH[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);

if (isset($_POST['selec']) && $_POST['selec'] != '') {
    //echo "select: ".$_POST['selec'];
    // echo "sel: " . implode(", ", $_POST['selec']);
    $seleccionados = split(",", $_POST['selec']);
//    echo implode(", ", $seleccionados);
}

$hallazgos;
//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
//echo "per: $periodo, cead: $cead, zona: $zona, esc: $escuela, prog: $programa, bus:". $_POST['buscar'];
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    //echo '1';
    $hallazgos = $consulta->filtrarHallazgos($auditor, $page_position, $item_per_page, $_POST["buscar"], $periodo, $escuela, $programa);
} else {
    //echo '2';
    $hallazgos = $consulta->filtrarHallazgos2($auditor, $page_position, $item_per_page, $periodo, $escuela, $programa);
}
if (count($hallazgos) <= 0) {
    echo 'Este auditor no ha realizado hallazgos.';
} else {

    echo "<br>
        <table id='tb_hallazgos' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
                                                <th>DOCUMENTO ESTUDIANTE</th>                                                
                                                <th>ESTUDIANTE</th>
                                                <th>ESTADO</th>
                                                <th>FECHA CREACIÓN</th>
                                                <th>NOMBRE AUDITOR</th>
                                                <th>CURSO</th>
                                                <th>TUTOR</th>
						<th>PROGRAMA</th>
                                                <th>ESCUELA</th>
                                                <th>FORMATO OBSERVACIONES</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    
    $completo = "class='completo'>";
    $notiene = "class='no-tiene'>";
    
    while ($row = mysql_fetch_array($hallazgos)) {
        
        $estado = $row[9] == 'Abierto' ? $completo . $row[2] : $notiene . $row[2];
        
        echo "<tr>"
        . "<td>$row[17]</td>"
        . "<td>$row[5]</td>"
        . "<td $estado </td>"
        . "<td>$row[3]</td>"
        . "<td>$row[5]</td>"
        . "<td>$row[10]</td>"
        . "<td>$row[12]</td>"
        . "<td>$row[15]</td>"
        . "<td>$row[16]</td>"
        . "<td><a href='http://192.168.4.25/sivisae/modelos/observacion_academica_12062015.pdf' target='_blank'><img width='30' height='30' src='http://192.168.4.25/sivisae/template/imagenes/opciones/observacion.png'/> </a></td>"
        . "<td> <input type='hidden' id='input_" . $row[0] . "' value='" . $row[0] . "'></input> </td>"
        . "</tr>";
    }

    echo "     </tbody>
                    </table>";

    echo '<div align="center"><br><br>';
    /* We call the pagination function here to generate Pagination link for us. 
      As you can see I have passed several parameters to the function. */
    echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
    echo '</div>';
}
$consulta->destruir();
