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
$cantEst = mysql_fetch_array($consulta->cantRegistros("select count(*) from noticias where estado in (1,2)"));
$get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);

//Consulta el listado
$consulta = $consulta->traerNoticias($page_position, $item_per_page);

if (count($consulta) <= 0) {
    echo 'No existen noticias';
} else {

    echo "<br>
        <table id='tb_grilla' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>TÍTULO NOTICIA</th>
						<th>FECHA NOTICIA</th>
						<th>DESCRIPCIÓN NOTICIA</th>
                                                <th>LINK</th>
						<th>ESTADO</th>
                                                <th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    while ($row = mysql_fetch_array($consulta)) {
        $ie_noticia = $row[0];
        $titulo_noticia = $row[1];
        $fecha_noticia = $row[2];
        $descripcion_noticia = $row[3];
        $link_noticia = $row[4];
        if ($link_noticia == '')
            $link = 'NO';
        else
            $link = 'SI';
        $estado_noticia = $row[5];


        echo "<tr>"
        . "<td>$titulo_noticia</td>"
        . "<td>$fecha_noticia</td>"
        . "<td>$descripcion_noticia</td>"
        . "<td>$link</td>"
        . "<td>$estado_noticia</td>"
        . "<td> <button title='Editar Noticia' " . $_SESSION['opc_ed'] . " id='boton_editar" . $ie_noticia . "' onclick='activarpopupeditar(" . $ie_noticia . ")'></button> </td>"
        . "<td> <button title='Eliminar Noticia' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $ie_noticia . "' onclick='activarpopupeliminar(" . $ie_noticia . ")'></button> </td>"
        . "<td> <input type='hidden' id='input_" . $ie_noticia . "' value='" . $ie_noticia . "|" . $titulo_noticia . "|" . $fecha_noticia . "|" . $descripcion_noticia . "|" . $link_noticia . "|" . $link . "'></input> </td>"
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
