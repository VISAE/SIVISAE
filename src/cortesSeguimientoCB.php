<?php
session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();

if (isset($_POST["page"])) {
    $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('N' . chr(250) . 'mero de p' . chr(225) . 'gina incorrecto!');
    } //incase of invalid page number
} else {
    $page_number = 1; //Si no hay numero de pagina coloca 1 por defecto
}
//Cantidad de items a mostrar
$item_per_page = 10;

//Obtiene la cantidad total de registros desde BD para crear la paginacion
$cantEst = mysql_fetch_array($consulta->cantRegistros("select count(*) from corte_seguimiento"));
$get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);

//Consulta el listado
$consulta = $consulta->traerCortesSeguimiento($page_position, $item_per_page);

if (count($consulta) <= 0) {
    echo 'No existen cortes';
} else {

    echo "<br>
        <table id='tb_grilla' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>PERIODO</th>
						<th>NO. SEMANAS</th>
						<th>FECHA INICIO</th>
                                                <th>FECHA FIN</th>
						<th>NO. SEGUIMIENTOS X PERIODO</th>
                                                <th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    while ($row = mysql_fetch_array($consulta)) {
        $corte_id = $row[0];
        $periodo_id = $row[1];
        $nombre_periodo = $row[2];
        $numero_semanas = $row[3];
        $fecha_inicio = $row[4];
        $fecha_fin = $row[5];
        $iteraciones = $row[6];

        echo "<tr>"
        . "<td>$nombre_periodo</td>"
        . "<td>$numero_semanas</td>"
        . "<td>$fecha_inicio</td>"
        . "<td>$fecha_fin</td>"
        . "<td>$iteraciones</td>"
        . "<td> <button title='Editar Corte' " . $_SESSION['opc_ed'] . " id='boton_editar" . $corte_id . "' onclick='activarpopupeditar(" . $corte_id . ")'></button> </td>"
        . "<td> <input type='hidden' id='input_" . $corte_id . "' value='" . $corte_id . "|" . $periodo_id . "|" . $nombre_periodo . "|" . $numero_semanas . "|" . $fecha_inicio . "|" . $fecha_fin . "|" . $iteraciones . "'></input> </td>"
        . "</tr>";
    }

    echo "</tbody> </table>";
    echo '<div align="center"><br><br>';
    /* We call the pagination function here to generate Pagination link for us. 
      As you can see I have passed several parameters to the function. */
    echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
    echo '</div>';
}