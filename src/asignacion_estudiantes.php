<?php

include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();



//$pagina = $_POST["page"];
$auditor;
if (isset($_POST["auditor"])) {
    $auditor = $_POST['auditor'];
}else {
    $auditor = 'T';
}

//echo $pagina;
if (isset($_POST["page"])) {
    $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('N'. chr(250) .'mero de p'. chr(225) .'gina incorrecto!');
    } //incase of invalid page number
} else {
    $page_number = 1; //Si no hay numero de pagina coloca 1 por defecto
}
//Cantidad de items a mostrar
$item_per_page = 40000;
//Obtiene la cantidad total de registros desde BD para crear la paginacion
$cantEst = mysql_fetch_array($consulta->cantEstudiantesAsignados($auditor));
$get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);

//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
$estudiantes = $consulta->estudiantesAsignados($auditor, $page_position, $item_per_page);

if (count($estudiantes) <= 0) {
    echo 'Este auditor no tiene estudiantes asignados';
} else {

    echo "<br>
        <table id='tb_estudiantes' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>CEDULA</th>
						<th>NOMBRE</th>
						<th>CEAD</th>
						<th>PROGRAMA</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    while ($row = mysqli_fetch_array($estudiantes)) {
        $cedula = ucfirst(strtolower($row[0]));
        $nombre = ucwords(strtolower($row[1]));
        $cead = ucwords(strtolower($row[2]));
        $cod_prog = (strtolower($row[3]));
        $prog = ucwords(strtolower($row[4]));
        echo "<tr>"
        . "<td stylie='width:80px'>$cedula</td>"
        . "<td nowrap='nowrap'>$nombre</td>"
        . "<td nowrap='nowrap'>$cead</td>"
        . "<td nowrap='nowrap'>$cod_prog - $prog</td>"
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