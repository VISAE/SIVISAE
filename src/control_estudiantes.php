<?php

include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();


$periodo = $_POST['periodo'];
$zona = isset($_POST['zona']) && $_POST['zona'] != '' ? implode(", ", $_POST['zona']) : "T";
$cead = isset($_POST['cead']) && $_POST['cead'] != '' ? implode(", ", $_POST['cead']) : "T";
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : "T";
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : "T";
//$pagina = $_POST["page"];
$auditor;
$registros;
$buscar;
$seleccionados = array();
if (isset($_POST["registros"])) {
    $registros = $_POST["registros"];
} else {    
    $registros = 10;
}

$auditor = 'T';
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
$cantEst;
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $cantEst = mysql_fetch_array($consulta->filtroCantEstudiantesNoAsignados($auditor, $_POST["buscar"], $periodo, $cead, $zona, $escuela, $programa));
} else {
    $cantEst = mysql_fetch_array($consulta->cantEstudiantesNoAsignados($auditor, $periodo, $cead, $zona, $escuela, $programa));
}
$get_total_rows = $cantEst[0];

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

$estudiantes;
//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
//echo "per: $periodo, cead: $cead, zona: $zona, esc: $escuela, prog: $programa, bus:". $_POST['buscar'];
if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    //echo '1';
    $estudiantes = $consulta->filtrarEstudiantesNoAsignados($auditor, $page_position, $item_per_page, $_POST["buscar"], $periodo, $cead, $zona, $escuela, $programa);
} else {
    //echo '2';
    $estudiantes = $consulta->estudiantesNoAsignados($auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa);
}
if (count($estudiantes) <= 0) {
    echo 'Este auditor no tiene estudiantes asignados';
} else {

    echo "
        <script type='text/javascript' charset='utf-8'>
    $(document).ready(function() {
       $('.all, .est').iCheck({
    checkboxClass: 'icheckbox_polaris',
    radioClass: 'iradio_polaris',
    increaseArea: '-10%' // optional
  });
	    $('#selectAll')
		.on('ifChecked', function(event) {
			$('.est').iCheck('check');
		})
		.on('ifUnchecked', function() {
			$('.est').iCheck('uncheck');
		});
            
            $('.est').on('ifChecked', function(event) {
			agregar($(this).val());
		})
		.on('ifUnchecked', function() {
			quitar($(this).val());
		});
            });
</script>     
  
<br>
        <table id='tb_estudiantes' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
                                                <th><input type='checkbox' class='all' id='selectAll'></th>
						<th>CEDULA</th>
						<th>NOMBRE</th>
						<th>CEAD</th>
						<th>PROGRAMA</th>
						<th>ESCUELA</th>
						<th>PERIODO</th>
					</tr>
				</thead>
                        <tbody>
                    ";

    if (isset($_POST['selec_est'])) {
        //echo "select: ".$_POST['selec'];
        // echo "sel: ".implode(", ", $_POST['selec_est']);
        $seleccionados = $_POST['selec_est'];
//    echo implode(", ", $seleccionados);
    }
    while ($row = mysql_fetch_array($estudiantes)) {
        $id = $row[0];
        $cedula = ucfirst(strtolower($row[1]));
        $nombre = ucwords(strtolower($row[2]));
        $cead = ucwords(strtolower($row[3]));
        $cod_prog = (strtolower($row[4]));
        $prog = ucwords(strtolower($row[5]));
        $escuela = ucwords(strtolower($row[6]));
        $periodo = $row[7];
        $check = "";
        if (in_array($id, $seleccionados)) {
            $check = "checked";
        }
        echo "<tr>"
        . "<td><input type='checkbox' $check class='est' value='$id' name='estudiante[]' id='estudiante'></td>"
        . "<td style='width:60px' class='tg-0ord'><label>$cedula</label></td>"
        . "<td style='width:200px'>$nombre</td>"
        . "<td style='width:250px' >$cead</td>"
        . "<td style='width:290px' >$cod_prog - $prog</td>"
        . "<td style='width:200px' >$escuela</td>"
        . "<td style='width:70px' >$periodo</td>"
        . "</tr>";
    }
    echo "     </tbody>
                    </table>";

    echo '<div align="center"><br><br>'
    . '<table><tr><td>Mostrando ' . $registros . ' registros de ' . $get_total_rows . ' encontrados.</td>'
    . '<td>';
    /* We call the pagination function here to generate Pagination link for us. 
      As you can see I have passed several parameters to the function. */
    echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
    echo '</td>'
    . '<td> de ' . $total_pages . ' p&aacute;ginas.</td></tr></table></div>'
    . '<div id="oculto">'
    . '<input type="hidden" id="est_hid" name="est_hid[]"/>'
    . '</div>';
}
$consulta->destruir();