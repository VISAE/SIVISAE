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
    $cantAud = $consulta->cantReporteGestionAud($_POST["buscar"], $auditor, $cead, $zona);
} else {
    $cantAud = $consulta->cantReporteGestionAud('n', $auditor, $cead, $zona);
}
$get_total_rows = $cantAud;

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);


if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
    $auditores = $consulta->reporteGestionAudListado($_POST["buscar"], $auditor, $cead, $zona, $page_position, $item_per_page);
} else {
    $auditores = $consulta->reporteGestionAudListado('n', $auditor, $cead, $zona, $page_position, $item_per_page);
}

//1. Se muestra el listado de auditores



if (count($auditores) <= 0) {
    echo 'Este auditor no tiene estudiantes asignados';
} else {
    echo "
    <br><br>
        <table id='tb_estudiantes' class='tg' style='table-layout: fixed; width=300px'>
				<thead>
					<tr>
                                                <th>NOMBRE</th>
						<th>ASIGNADOS</th>
						<th>NUE</th>
                                                <th colspan='3'>CARACTERIZACIÓN<br>NUEVOS (C|I|F)</th>
                                                <th>%</th>
						<th>HOM</th>
                                                <th colspan='3'>CARACTERIZACIÓN<br>HOMOLOG (C|I|F)</th>
                                                <th>%</th>
						<th>ANT</th>
                                                <th colspan='3'>CARACTERIZACIÓN<br>ANT (C|I|F)</th>
						<th>%</th>
						<th colspan='2'>INDUCCIÓN<br>NUEVOS (S|N)</th>
                                                <th>%</th>
                                                <th colspan='2'>INDUCCIÓN<br>HOMOLOG (S|N)</th>
						<th>%</th>
					</tr>
				</thead>
                        <tbody>
                    ";


    $t_asignados = 0;
    $t_nuevos = 0;
    $t_n_completos = 0;
    $t_n_incompletos = 0;
    $t_n_faltantes = 0;
    $t_n_cumpl_car = 0;
    $t_homologados = 0;
    $t_h_completos = 0;
    $t_h_incompletos = 0;
    $t_h_faltantes = 0;
    $t_h_cumpl_car = 0;
    $t_a_completos = 0;
    $t_a_incompletos = 0;
    $t_a_faltantes = 0;
    $t_a_cumpl_car = 0;
    $t_n_induccion = 0;
    $t_n_falta_ind = 0;
    $t_n_cumpl_ind = 0;
    $t_h_induccion = 0;
    $t_h_falta_ind = 0;
    $t_h_cumpl_ind = 0;
    $t_n_cumpl_car = 0;
    $t_h_cumpl_car = 0;
    $t_a_cumpl_car = 0;
    $t_n_cumpl_ind = 0;
    $t_h_cumpl_ind = 0;


    while ($row = mysql_fetch_array($auditores)) {

        $id_auditor = ucwords(preg_replace($sintilde, $tildes, $row[0]));
        $cedula = ucwords(preg_replace($sintilde, $tildes, $row[1]));
        $nombre = ucwords(preg_replace($sintilde, $tildes, $row[2]));
        $cead = ucwords(preg_replace($sintilde, $tildes, $row[3]));
        $zona = ucwords(preg_replace($sintilde, $tildes, $row[4]));

        //Se consulta el numero de asignados (total, nuevos, antiguos y homologacion)
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudAsignados($id_auditor, $periodo, $programa, $escuela, 1));
        $asignados = $cantContador[0];
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudAsignados($id_auditor, $periodo, $programa, $escuela, 2));
        $nuevos = $cantContador[0];
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudAsignados($id_auditor, $periodo, $programa, $escuela, 3));
        $homologados = $cantContador[0];
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudAsignados($id_auditor, $periodo, $programa, $escuela, 4));
        $antiguos = $cantContador[0];

        //Se consulta de los nuevos cuantos han realizado caracterizacion
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudCaracterizacion($id_auditor, $periodo, $programa, $escuela, 2));
        $n_completos = $cantContador[0];
        $n_incompletos = $cantContador[1];
        $n_faltantes = $cantContador[2];
        if ($nuevos > 0) {
            $n_cumpl_car = round(($n_completos * 100) / $nuevos, 0);
        } else {
            $n_cumpl_car = "N/A";
        }

        //Se consulta de los homologados cuantos han realizado caracterizacion
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudCaracterizacion($id_auditor, $periodo, $programa, $escuela, 3));
        $h_completos = $cantContador[0];
        $h_incompletos = $cantContador[1];
        $h_faltantes = $cantContador[2];
        if ($homologados > 0) {
            $h_cumpl_car = round(($h_completos * 100) / $homologados, 0);
        } else {
            $h_cumpl_car = "N/A";
        }

        //Se consulta de los antiguos cuantos han realizado caracterizacion
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudCaracterizacion($id_auditor, $periodo, $programa, $escuela, 4));
        $a_completos = $cantContador[0];
        $a_incompletos = $cantContador[1];
        $a_faltantes = $cantContador[2];
        if ($antiguos > 0) {
            $a_cumpl_car = round(($a_completos * 100) / $antiguos, 0);
        } else {
            $a_cumpl_car = "N/A";
        }

        //Se consultan las inducciones de los nuevos
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudInducciones($id_auditor, $periodo, $programa, $escuela, 2));
        $n_induccion = $cantContador[1];
        if ($nuevos > 0) {
            $n_cumpl_ind = round(($n_induccion * 100) / $nuevos, 0);
            $n_falta_ind = $nuevos - $n_induccion;
        } else {
            $n_cumpl_ind = "N/A";
        }

        //Se consultan las inducciones de los homologados
        $cantContador = mysql_fetch_array($consulta->reporteGestionAudInducciones($id_auditor, $periodo, $programa, $escuela, 3));
        $h_induccion = $cantContador[1];
        if ($homologados > 0) {
            $h_cumpl_ind = round(($h_induccion * 100) / $homologados, 0);
            $h_falta_ind = $homologados - $h_induccion;
        } else {
            $h_cumpl_ind = "N/A";
            $h_falta_ind = "N/A";
        }

        echo "<tr>"
        . "<td>$nombre</br><strong>$cead</strong></br><strong>$zona</strong></td>"
        . "<td>$asignados</td>"
        . "<td style='background-color:#D8EAEE'>$nuevos</td>"
        . "<td>$n_completos</td>"
        . "<td>$n_incompletos</td>"
        . "<td><font color='red'><strong>$n_faltantes</strong></font></td>"
        . "<td style='background-color:" . $consulta->reporteGestionAudColor($n_cumpl_car) . "'>$n_cumpl_car %</td>"
        . "<td style='background-color:#D8EAEE'>$homologados</td>"
        . "<td>$h_completos</td>"
        . "<td>$h_incompletos</td>"
        . "<td><font color='red'><strong>$h_faltantes</strong></font></td>"
        . "<td style='background-color:" . $consulta->reporteGestionAudColor($h_cumpl_car) . "'>$h_cumpl_car %</td>"
        . "<td style='background-color:#D8EAEE'>$antiguos</td>"
        . "<td>$a_completos</td>"
        . "<td>$a_incompletos</td>"
        . "<td>$a_faltantes</td>"
        . "<td style='background-color:" . $consulta->reporteGestionAudColor($a_cumpl_car) . "'>$a_cumpl_car %</td>"
        . "<td>$n_induccion</td>"
        . "<td><font color='red'><strong>$n_falta_ind</strong></font></td>"
        . "<td style='background-color:" . $consulta->reporteGestionAudColor($n_cumpl_ind) . "'>$n_cumpl_ind %</td>"
        . "<td>$h_induccion</td>"
        . "<td><font color='red'><strong>$h_falta_ind</strong></font></td>"
        . "<td style='background-color:" . $consulta->reporteGestionAudColor($h_cumpl_ind) . "'>$h_cumpl_ind %</td>"
        . "</tr>";


        $t_asignados+=$asignados;
        $t_nuevos+=$nuevos;
        $t_n_completos+=$n_completos;
        $t_n_incompletos+=$n_incompletos;
        $t_n_faltantes+=$n_faltantes;
        $t_n_cumpl_car+=$n_cumpl_car;
        $t_homologados+=$homologados;
        $t_h_completos+=$h_completos;
        $t_h_incompletos+=$h_incompletos;
        $t_h_faltantes+=$h_faltantes;
        $t_h_cumpl_car+=$h_cumpl_car;
        $t_a_completos+=$a_completos;
        $t_a_incompletos+=$a_incompletos;
        $t_a_faltantes+=$a_faltantes;
        $t_a_cumpl_car+=$a_cumpl_car;
        $t_n_induccion+=$n_induccion;
        $t_n_falta_ind+=$n_falta_ind;
        $t_n_cumpl_ind+=$n_cumpl_ind;
        $t_h_induccion+=$h_induccion;
        $t_h_falta_ind+=$h_falta_ind;
        $t_h_cumpl_ind+=$h_cumpl_ind;
    }


    // Totales
    $t_n_cumpl_car = round(($t_n_cumpl_car / $get_total_rows), 0);
    $t_h_cumpl_car = round(($t_h_cumpl_car / $get_total_rows), 0);
    $t_a_cumpl_car = round(($t_a_cumpl_car / $get_total_rows), 0);
    $t_n_cumpl_ind = round(($t_n_cumpl_ind / $get_total_rows), 0);
    $t_h_cumpl_ind = round(($t_h_cumpl_ind / $get_total_rows), 0);



    echo "<tr>"
    . "<td><strong>TOTALES</strong></td>"
    . "<td>$t_asignados</td>"
    . "<td style='background-color:#D8EAEE'>$t_nuevos</td>"
    . "<td>$t_n_completos</td>"
    . "<td>$t_n_incompletos</td>"
    . "<td><font color='red'><strong>$t_n_faltantes</strong></font></td>"
    . "<td style='background-color:" . $consulta->reporteGestionAudColor($t_n_cumpl_car) . "'>$t_n_cumpl_car %</td>"
    . "<td style='background-color:#D8EAEE'>$t_homologados</td>"
    . "<td>$t_h_completos</td>"
    . "<td>$t_h_incompletos</td>"
    . "<td><font color='red'><strong>$t_h_faltantes</strong></font></td>"
    . "<td style='background-color:" . $consulta->reporteGestionAudColor($t_h_cumpl_car) . "'>$t_h_cumpl_car %</td>"
    . "<td style='background-color:#D8EAEE'>$antiguos</td>"
    . "<td>$t_a_completos</td>"
    . "<td>$t_a_incompletos</td>"
    . "<td>$t_a_faltantes</td>"
    . "<td style='background-color:" . $consulta->reporteGestionAudColor($t_a_cumpl_car) . "'>$t_a_cumpl_car %</td>"
    . "<td>$t_n_induccion</td>"
    . "<td><font color='red'><strong>$t_n_falta_ind</strong></font></td>"
    . "<td style='background-color:" . $consulta->reporteGestionAudColor($t_n_cumpl_ind) . "'>$t_n_cumpl_ind %</td>"
    . "<td>$t_h_induccion</td>"
    . "<td><font color='red'><strong>$t_h_falta_ind</strong></font></td>"
    . "<td style='background-color:" . $consulta->reporteGestionAudColor($t_h_cumpl_ind) . "'>$t_h_cumpl_ind %</td>"
    . "</tr>";

//Se trae las inducciones de solo los estudiantes nuevos
    //$auditoresInducciones = $consulta->reporteGestionAudInducciones($periodo);

    /*
      while ($row = mysql_fetch_array($auditores)) {
      $id_auditor = ucwords(preg_replace($sintilde, $tildes, $row[0]));

      $induccion = 0;
      //Se trae cumplimiento de induccion del auditor
      foreach ($auditoresInducciones as $inducciones) {
      if ($inducciones['id_auditor'] === $id_auditor) {
      $induccion = $inducciones['induccion'];
      }
      }
      $nombre = ucwords(preg_replace($sintilde, $tildes, $row[1]));
      $cead = ucwords(preg_replace($sintilde, $tildes, $row[2]));
      $zona = ucwords(preg_replace($sintilde, $tildes, $row[3]));
      $asignados = ucwords(preg_replace($sintilde, $tildes, $row[4]));
      $nuevos = ucwords(preg_replace($sintilde, $tildes, $row[5]));
      $homologados = ucwords(preg_replace($sintilde, $tildes, $row[6]));
      $antiguos = ucwords(preg_replace($sintilde, $tildes, $row[7]));
      $carac_comp = ucwords(preg_replace($sintilde, $tildes, $row[8]));
      $carac_inc = ucwords(preg_replace($sintilde, $tildes, $row[9]));
      $sin_carac = ucwords(preg_replace($sintilde, $tildes, $row[10]));
      $sin_induc = $asignados - $induccion;
      $porcentaje_caracterizacion = round(($carac_comp * 100) / $asignados, 0);
      $porcentaje_induccion = round(($induccion * 100) / $asignados, 0);

      if ($porcentaje_caracterizacion <= 40) {
      $colorC = '#FF6767';
      }

      if ($porcentaje_caracterizacion >= 41 && $porcentaje_caracterizacion <= 60) {
      $colorC = '#FCA205';
      }

      if ($porcentaje_caracterizacion >= 61 && $porcentaje_caracterizacion <= 90) {
      $colorC = '#FFE779';
      }

      if ($porcentaje_caracterizacion >= 91) {
      $colorC = '#41EE41';
      }

      if ($porcentaje_induccion <= 40) {
      $colorI = '#FF6767';
      }

      if ($porcentaje_induccion >= 41 && $porcentaje_induccion <= 60) {
      $colorI = '#FCA205';
      }

      if ($porcentaje_induccion >= 61 && $porcentaje_induccion <= 90) {
      $colorI = '#FFE779';
      }

      if ($porcentaje_induccion >= 91) {
      $colorI = '#41EE41';
      }

      $Tasignados += $asignados;
      $Tnuevos += $nuevos;
      $Thomologados += $homologados;
      $Tantiguos += $antiguos;
      $Tcarac_comp += $carac_comp;
      $Tcarac_inc += $carac_inc;
      $Tsin_carac += $sin_carac;
      $Tsin_induc += $asignados - $induccion;
      $Tinduccion+=$induccion;
      $TPCarac+= $porcentaje_caracterizacion;
      $TPInducc+= $porcentaje_induccion;


      echo "<tr>"
      . "<td>$nombre</td>"
      . "<td>$cead</td>"
      . "<td>$zona</td>"
      . "<td>$asignados</td>"
      . "<td>$nuevos</td>"
      . "<td>$homologados</td>"
      . "<td>$antiguos</td>"
      . "<td>$carac_comp</td>"
      . "<td>$carac_inc</td>"
      . "<td>$sin_carac</td>"
      . "<td style='background-color:$colorC'>$porcentaje_caracterizacion %</td>"
      . "<td>$induccion</td>"
      . "<td>$sin_induc</td>"
      . "<td style='background-color:$colorI'>$porcentaje_induccion %</td>"
      . "</tr>";
      }

      $TPCarac = round($TPCarac / $get_total_rows, 0);
      $TPInducc = round($TPInducc / $get_total_rows, 0);




      echo "<tr>"
      . "<td colspan='3'>Totales</td>"
      . "<td>$Tasignados</td>"
      . "<td>$Tnuevos</td>"
      . "<td>$Thomologados</td>"
      . "<td>$Tantiguos</td>"
      . "<td>$Tcarac_comp</td>"
      . "<td>$Tcarac_inc</td>"
      . "<td>$sin_carac</td>"
      . "<td>$TPCarac %</td>"
      . "<td>$Tinduccion</td>"
      . "<td>$Tsin_induc</td>"
      . "<td>$TPInducc %</td>"
      . "</tr>";
     */

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
