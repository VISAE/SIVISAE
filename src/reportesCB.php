<?php

/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */
session_start();
include_once '../config/sigra_class.php';
include './paginador.php';
include_once './crear_reporteCB.php';
$consulta = new sigra_consultas();
$accion = $_POST['accion'];
if ($accion === "listado_eventos_asistentes") {
    $consulta = new sigra_consultas();
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $proyecto = isset($_POST['proyecto']) && $_POST['proyecto'] != '' ? implode("', '", $_POST['proyecto']) : 'T';
    $evento = isset($_POST['evento']) && $_POST['evento'] != '' ? implode("', '", $_POST['evento']) : 'T';
    $linea1 = isset($_POST['linea']) && $_POST['linea'] != '' ? implode("', '", $_POST['linea']) : 'T';
    $cobertura1 = isset($_POST['cobertura']) && $_POST['cobertura'] != '' ? implode("', '", $_POST['cobertura']) : 'T';
    if (isset($_POST["registros"])) {
        $registros = $_POST["registros"];
    } else {
        $registros = 50;
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
    
    $cantGra;
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $cantGra = mysqli_fetch_array($consulta->cantReporteAsistentes($_POST["buscar"], $evento, $proyecto, $linea1, $cobertura1));
    } else {
        $cantGra = mysqli_fetch_array($consulta->cantReporteAsistentes('', $evento, $proyecto, $linea1, $cobertura1));
//        $cantGra = mysqli_fetch_array($consulta->cantGraduados('', 'T', 'T','T', 'T'));
    }
    $get_total_rows = $cantGra['cant'];
    //Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

    $graduados;
//Consulta que alimenta la tabla de graduados dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $graduados = $consulta->reporteAsistentes($page_position, $item_per_page, $_POST["buscar"], $evento, $proyecto, $linea1, $cobertura1);
    } else {
        $graduados = $consulta->reporteAsistentes($page_position, $item_per_page, '', $evento, $proyecto, $linea1, $cobertura1);
//        $graduados = $consulta->listaGraduados($page_position, $item_per_page, '', 'T', 'T','T', 'T');
    }
    $html = "";
    if ($get_total_rows <= 0) {
        $html = 'No se encontraron Registros que cumplan las condiciones de busqueda';
    } else {
        $html = "
            <a onclick='return crearReporte(\"descargar_eventos_asistentes\")'  id='excel-asignados' class='botones'>Descargue este listado</a>
<br>
        <table id='tb_graduados' class='tg' style='table-layout: fixed; width:100%'>
            <colgroup>
                <col style='width: 10%'>
                <col style='width: 20%'>
                <col style='width: 20%'>
                <col style='width: 20%'>
                <col style='width: 15%'>
                <col style='width: 15%'>
            </colgroup>
				<thead>
					<tr>
                                                <th>DOCUMENTO</th>
						<th>NOMBRE</th>
						<th>PROYECTO</th>
						<th>EVENTO</th>
						<th>FECHA INSCRIPCIÓN</th>
						<th>FECHA ASISTENCIA</th>
					</tr>
				</thead>
                        <tbody>
                    ";
        
        while ($row = mysqli_fetch_array($graduados)) {
//            $id = $row[0];
            $documento = ucfirst($row[0]);
            $nombre = ucwords($row[1]);
            $proy = ucwords($row[2]);
            $even = $row[3];
            $fecha_ins = $row[4];
            $fecha_asis = $row[5];
            $html .=  "<tr>"
            . "<input type='hidden' value='$id' id='estid-$id' class='id'/>
                    <td align='center'>$documento</td>
                    <td>$nombre</td>
                    <td align='center'>$proy</td> 
                    <td align='center'>$even</td> 
                    <td align='center'>$fecha_ins</td> 
                    <td align='center'>$fecha_asis</td> 
                    </tr> ";
        }
        $html .= "     </tbody>
                    </table>";

        $mostrar = $get_total_rows>$registros ? $registros : $get_total_rows;
        $html .= '<div align="center"><br><br>'
        . '<table><tr><td>Mostrando ' . $mostrar . ' registros de ' . $get_total_rows . ' encontrados </td>'
        . '<td>';
        $html .= paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        $pagina = $total_pages>1 ? "p&aacute;ginas" : "p&aacute;gina" ;
        $html .= '</td>'
        . '<td> en ' . $total_pages . ' ' . $pagina .'.</td></tr></table></div>'
        . '<div id="oculto">'
        . '<input type="hidden" id="est_hid" name="est_hid[]"/>'
        . '</div>';
        
    }
    $consulta->destruir();
    $consulta->destruir2();    
    echo $html;
}

if($accion === "descargar_eventos_asistentes"){
    $proyecto = isset($_POST['proyecto']) && $_POST['proyecto'] != '' ? implode("', '", $_POST['proyecto']) : 'T';
    $evento = isset($_POST['evento']) && $_POST['evento'] != '' ? implode("', '", $_POST['evento']) : 'T';
    $linea1 = isset($_POST['linea']) && $_POST['linea'] != '' ? implode("', '", $_POST['linea']) : 'T';
    $cobertura1 = isset($_POST['cobertura']) && $_POST['cobertura'] != '' ? implode("', '", $_POST['cobertura']) : 'T';
    
    $asignar = array();
    //Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $temp = $consulta->reporteAsistentes('', '', $_POST["buscar"], $evento, $proyecto, $linea1, $cobertura1);
        while ($fila = mysqli_fetch_array($temp)) {
            $asignar[] = $fila;
        }
    } else {
        $temp = $consulta->reporteAsistentes('', '', '', $evento, $proyecto, $linea1, $cobertura1);
        while ($fila = mysqli_fetch_array($temp)) {
            $asignar[] = $fila;
        }
    }

    $titulo = "Reporte de Asistentes a Eventos";
    $columnas = array("Documento", "Nombre", "Proyecto", "Evento", "Fecha Inscripción", "Fecha Asistencia");
    $nombre_arch = "Asistentes a Eventos";
    $desc = "Reporte que contiene un listado de Personas que asistieron a eventos.";
    $ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);

    echo $ruta;
}
if ($accion === "listado_eventos_registrados") {
    $consulta = new sigra_consultas();
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $proyecto = isset($_POST['proyecto']) && $_POST['proyecto'] != '' ? implode("', '", $_POST['proyecto']) : 'T';
    $evento = isset($_POST['evento']) && $_POST['evento'] != '' ? implode("', '", $_POST['evento']) : 'T';
    $linea1 = isset($_POST['linea']) && $_POST['linea'] != '' ? implode("', '", $_POST['linea']) : 'T';
    $cobertura1 = isset($_POST['cobertura']) && $_POST['cobertura'] != '' ? implode("', '", $_POST['cobertura']) : 'T';
    if (isset($_POST["registros"])) {
        $registros = $_POST["registros"];
    } else {
        $registros = 50;
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
    
    $cantGra;
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $cantGra = mysqli_fetch_array($consulta->cantReporteRegistrados($_POST["buscar"], $evento, $proyecto, $linea1, $cobertura1));
    } else {
        $cantGra = mysqli_fetch_array($consulta->cantReporteRegistrados('', $evento, $proyecto, $linea1, $cobertura1));
//        $cantGra = mysqli_fetch_array($consulta->cantGraduados('', 'T', 'T','T', 'T'));
    }
    $get_total_rows = $cantGra['cant'];
    //Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

    $graduados;
//Consulta que alimenta la tabla de graduados dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $graduados = $consulta->reporteRegistrados($page_position, $item_per_page, $_POST["buscar"], $evento, $proyecto, $linea1, $cobertura1);
    } else {
        $graduados = $consulta->reporteRegistrados($page_position, $item_per_page, '', $evento, $proyecto, $linea1, $cobertura1);
//        $graduados = $consulta->listaGraduados($page_position, $item_per_page, '', 'T', 'T','T', 'T');
    }
    $html = "";
    if ($get_total_rows <= 0) {
        $html = 'No se encontraron Registros que cumplan las condiciones de busqueda';
    } else {
        $html = "
            <a onclick='return crearReporte(\"descargar_eventos_registrados\")'  id='excel-asignados' class='botones'>Descargue este listado</a>
<br>
        <table id='tb_graduados' class='tg' style='table-layout: fixed; width:100%'>
            <colgroup>
                <col style='width: 10%'>
                <col style='width: 20%'>
                <col style='width: 20%'>
                <col style='width: 20%'>
                <col style='width: 15%'>
                <col style='width: 15%'>
            </colgroup>
				<thead>
					<tr>
                                                <th>DOCUMENTO</th>
						<th>NOMBRE</th>
						<th>PROYECTO</th>
						<th>EVENTO</th>
						<th>FECHA INSCRIPCIÓN</th>
						<th>FECHA ASISTENCIA</th>
					</tr>
				</thead>
                        <tbody>
                    ";
        
        while ($row = mysqli_fetch_array($graduados)) {
//            $id = $row[0];
            $documento = ucfirst($row[0]);
            $nombre = ucwords($row[1]);
            $proy = ucwords($row[2]);
            $even = $row[3];
            $fecha_ins = $row[4];
            $fecha_asis = $row[5];
            $html .=  "<tr>"
            . "<input type='hidden' value='$id' id='estid-$id' class='id'/>
                    <td align='center'>$documento</td>
                    <td>$nombre</td>
                    <td align='center'>$proy</td> 
                    <td align='center'>$even</td> 
                    <td align='center'>$fecha_ins</td> 
                    <td align='center'>$fecha_asis</td> 
                    </tr> ";
        }
        $html .= "     </tbody>
                    </table>";

        $mostrar = $get_total_rows>$registros ? $registros : $get_total_rows;
        $html .= '<div align="center"><br><br>'
        . '<table><tr><td>Mostrando ' . $mostrar . ' registros de ' . $get_total_rows . ' encontrados </td>'
        . '<td>';
        $html .= paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        $pagina = $total_pages>1 ? "p&aacute;ginas" : "p&aacute;gina" ;
        $html .= '</td>'
        . '<td> en ' . $total_pages . ' ' . $pagina .'.</td></tr></table></div>'
        . '<div id="oculto">'
        . '<input type="hidden" id="est_hid" name="est_hid[]"/>'
        . '</div>';
        
    }
    $consulta->destruir();
    $consulta->destruir2();    
    echo $html;
}

if($accion === "descargar_eventos_registrados"){
    $proyecto = isset($_POST['proyecto']) && $_POST['proyecto'] != '' ? implode("', '", $_POST['proyecto']) : 'T';
    $evento = isset($_POST['evento']) && $_POST['evento'] != '' ? implode("', '", $_POST['evento']) : 'T';
    $linea1 = isset($_POST['linea']) && $_POST['linea'] != '' ? implode("', '", $_POST['linea']) : 'T';
    $cobertura1 = isset($_POST['cobertura']) && $_POST['cobertura'] != '' ? implode("', '", $_POST['cobertura']) : 'T';
    
    $asignar = array();
    //Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $temp = $consulta->reporteRegistrados('', '', $_POST["buscar"], $evento, $proyecto, $linea1, $cobertura1);
        while ($fila = mysqli_fetch_array($temp)) {
            $asignar[] = $fila;
        }
    } else {
        $temp = $consulta->reporteRegistrados('', '', '', $evento, $proyecto, $linea1, $cobertura1);
        while ($fila = mysqli_fetch_array($temp)) {
            $asignar[] = $fila;
        }
    }

    $titulo = "Reporte de Registrados a Eventos";
    $columnas = array("Documento", "Nombre", "Proyecto", "Evento", "Fecha Inscripción", "Fecha Asistencia");
    $nombre_arch = "Registrados a Eventos";
    $desc = "Reporte que contiene un listado de Personas que se registraron a eventos.";
    $ruta = generarReporte($titulo, $columnas, $asignar, $nombre_arch, $desc);

    echo $ruta;
}