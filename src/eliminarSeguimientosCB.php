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
$item_per_page = 25;



//Obtiene la cantidad total de registros desde BD para crear la paginacion
$cantEst = mysql_fetch_array($consulta->cantRegistros("SELECT COUNT(*) FROM SIVISAE.`eliminacion_seguimientos` WHERE `estado_id`=1"));
$get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);

//Consulta el listado
$consulta = $consulta->traerSolicitudes($page_position, $item_per_page);

if (count($consulta) <= 0) {
    echo 'No existen noticias';
} else {
    echo "<br>
        <table id='tb_grilla' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
                                                <th>ID</th>
                                                <th>AUDITOR</th>
                                                <th>OBSERVACIÓN</th>
                                                <th>RADICACIÓN</th>
                                                <th>ESTUDIANTE</th>
                                                <th>PERIODO</th>
                                                <th>DETALLE</th>
                                                <th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    $cont = 0;
    while ($row = mysql_fetch_array($consulta)) {
        $eliminacion_id = $row[0];
        $observacion = $row[1];
        $seguimiento_id=base64_encode($row[2]);
        $fecha_radicacion = $row[3];
        $estado = $row[4];
        $estudiante_id=base64_encode($row[5]);
        $estudiante_nombre = $row[6];
        $periodo_id=base64_encode($row[7]);
        $periodo_academico = $row[8];
        $auditor = $row[9];

        
        
        echo "<tr>"
        . "<td>$eliminacion_id</td>"
        . "<td>$auditor</td>"
        . "<td>$observacion</td>"
        . "<td>$fecha_radicacion</td>"
        . "<td>$estudiante_nombre</td>"
        . "<td>$periodo_academico</td>"
        . "<td><a target='_blank' href='" . URL_PAGES . "sivisae_soliciitud_eliminar_seguimiento.php?st=$estudiante_id&pa=$periodo_id&sg=$seguimiento_id' class='botones_ver_seg'>Ver</a><br><br></td>"
        . "<td> <button title='Eliminar Seguimiento' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $eliminacion_id . "' onclick='activarpopupeliminar(" . $eliminacion_id . ")'></button> </td>"
        . "<td> <input type='hidden' id='input_" . $eliminacion_id . "' value='" . $eliminacion_id . "'></input> </td>"
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
