<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
include_once './crear_reporteCB.php';
$consulta = new sivisae_consultas();
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);
$periodo = $_POST['periodo'];
$tipo_asignacion = $_POST['tipo_asignacion'];
$escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : 'T';
$programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode(", ", $_POST['programa']) : 'T';

$fecha_ini = isset($_POST['fecha_inicio']) != '' ? $_POST['fecha_inicio'] : '';
$fecha_fin = isset($_POST['fecha_fin']) != '' ? $_POST['fecha_fin'] : '';



if ($tipo_asignacion == 3) {
    $tipo_asignacion = "1,2";
}


//$pagina = $_POST["page"];
$auditor = isset($_POST['auditor']) && $_POST['auditor'] != '' ? $_POST['auditor'] : 'T';
$registros;
$buscar;
$seleccionados = array();

if (isset($_SESSION['ced'])) {

    if (isset($_POST["registros"])) {
        $registros = $_POST["registros"];
    } else {
        $registros = 50;
    }
// $auditor = 'T';
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
        $cantEst = mysql_fetch_array($consulta->filtrarCantEstudiantesAsignados2Consejeria($auditor, $_POST["buscar"], $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion));
    } else {
        $cantEst = mysql_fetch_array($consulta->cantEstudiantesAsignados2Consejeria($auditor, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion));
    }
    $get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

    if (isset($_POST['selec']) && $_POST['selec'] != '') {
        $seleccionados = split(",", $_POST['selec']);
    }

    $estudiantes;
//Consulta que alimenta la tabla de estudiantes dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $estudiantes = $consulta->filtrarEstudiantesAsignados2Consejeria($auditor, $page_position, $item_per_page, $_POST["buscar"], $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion);
    } else {
        $estudiantes = $consulta->estudiantesAsignados2Consejeria($auditor, $page_position, $item_per_page, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion);
    }
    
    if (count($estudiantes) <= 0) {
        echo 'Este consejero no tiene estudiantes asignados';
    } else {

        echo "
        <script>

$('.comp').each(function() {
var id = $(this).attr('est');
         $(this).qtip({
         
             content: {
                title: 'Datos complementarios',
                 text: $('#estudiante-'+id)
             }, 
             style: { classes: 'qtip-tipped' },
             position:{
             adjust: {
            scroll: true, // Can be ommited (e.g. default behaviour)
            x: -50
        }}
         });
     });
$('.comp1').each(function() {
var id = $(this).attr('est');
         $(this).qtip({         
             content: {
                title: 'Datos complementarios',
                 text: $('#estudiante2-'+id)
             }, 
             style: { classes: 'qtip-tipped' }
         });
     });
     $('.ind').each(function() {
var id = $(this).attr('est');
         $(this).qtip({
         
             content: {
                title: 'Inducciones realizadas',
                 text: $('#induccion-'+id)
             }, 
             style: { classes: 'qtip-tipped' }
         });
     });
     
$('.seg').each(function() {
var id = $(this).attr('est');
         $(this).qtip({
         
             content: {
                title: 'Seguimientos realizados',
                 text: $('#seguimiento-'+id)
             },
              hide: {
                fixed: true,
                delay: 300
            },
             style: { classes: 'qtip-tipped' },
             position:{
             adjust: {
            scroll: true, // Can be ommited (e.g. default behaviour)
            x: -50
        }}
         });
     });
     
     $('.notif').each(function() {
         $(this).qtip({         
             content: {
                
                 text: 'De clic para mas información'
             }, 
             style: { classes: 'qtip-blue' },position: {
        my: 'bottom center',  // Position my top left...
        at: 'top center' // at the bottom right of...   
    }
             
         });
     });
$('.carac').each(function() {
var id = $(this).attr('est');
var usr = $(this).attr('usr');
var perfil = $(this).attr('perf');
var periodo = $(this).attr('peraca');
         $(this).qtip({
         show: 'click',
         hide: {
        event: false
    },
             content: {
             text: 'Cargando...',
             button: 'Cerrar',

ajax: {
            url: 'pages/sivisae_reporte_caracterizacion.php',
            data: { st: id, usr: usr, pf: perfil, pa: periodo},
            once: false // Re-fetch the content each time I'm shown
        }
            }, 
             style: { classes: 'qtip-tipped' },
             position:{
             adjust: {
            scroll: true, // Can be ommited (e.g. default behaviour)
            x: -20
        }},
             position: {
        my: 'top center',  // Position my top left...
        at: 'bottom center' // at the bottom right of...
        
    }
         });

     });
    
$('.segto').each(function() {
var id = $(this).attr('est');
         $(this).qtip({         
             content: {
                title: 'Seguimientos realizados',
                 text: $('#seguimiento-'+id)
             }, 
             style: { classes: 'qtip-tipped' }
         });
     });
  </script>  ";

        echo "<br><a href='#' onclick='return crearReporte()'  id='excel-asignados' class='botones'>Descargue listado completo de asignación</a>
    <br><br>
        <table id='tb_estudiantes' class='tg' style='table-layout: fixed; width:100%'>
				<thead>
					<tr>
                                                <th width='10%'>CEDULA</th>
						<th width='20%'>NOMBRE</th>
						<th width='15%'>CARACTERIZACION</th>
						<th width='15%'>INDUCCION</th>
					</tr>
				</thead>
                        <tbody>
                    ";

        if (isset($_POST['selec_est'])) {
            $seleccionados = $_POST['selec_est'];
        }
        $completo = "class='completo'>";
        $incompleto = "class='incompleto'>";
        $notiene = "class='no-tiene'>";
        while ($row = mysql_fetch_array($estudiantes)) {
            $id = $row[0];
            $cedula = ucfirst(strtolower($row[1]));
            $nombre = ucwords(strtolower($row[2]));
            $cead = ucwords(strtolower($row[3]));
            $cod_prog = (strtolower($row[4]));
            $prog = ucwords(strtolower($row[5]));
            $escuela = ucwords(strtolower($row[6]));
            $peraca = base64_encode($row[7]);
            $per_desc = ucwords(strtolower($row[8]));
            $seguimiento = $row[9];

            $porcent_seguimeintos = $row[10];
            $tp_estudiante = ucwords(preg_replace($sintilde, $tildes, $row[15]));
            $caracterizacion = $row[11];

            if ($tp_estudiante != 'Antiguo') {
                $induccion = $row[12] !== 'Sin asistencia' ? "class='completo ind' est='$id'>" . $row[12] . "" : $notiene . $row[12];
            } else {
                $induccion = "class='posgrado'>Antiguo";
            }
            $auditor_id = $row[13];
            $auditor_estudiante_id = $row[14];

            $estado_seg = $row[17];
            $celda_seguimiento;

            if ($row[9] == 1)
                $etiqueta_seg = "Seguimiento";
            else
                $etiqueta_seg = "Seguimientos";

            if ($seguimiento !== "No tiene") {
                if ($estado_seg === '3') {
                    $celda_seguimiento = "class='completo seg' est='$id'> " . $row[9] . " " . $etiqueta_seg . " <br> Completo";
                } else {
                    if ($estado_seg === '2') {
                        $celda_seguimiento = "class='incompleto seg' est='$id'> " . $row[9] . " " . $etiqueta_seg . " <br> Acciones Abiertas";
                    } else {
                        $celda_seguimiento = "class='faltan seg' est='$id'> " . $row[9] . " " . $etiqueta_seg . " <br> Faltan Cursos";
                    }
                }
            } else {
                $celda_seguimiento = $notiene . $row[9];
            }
            $enc = base64_encode($cedula);
            $usr = base64_encode($_SESSION['ced']);
            $perf = base64_encode($_SESSION['perfilid']);
            $enc_id = base64_encode($id);
            $enc_per = base64_encode($periodo);
            if ($row[18] != '1') {
                $caracterizacion = "class='posgrado'>Posgrado";
            } else {
                if ($tp_estudiante != 'Antiguo') {
                    switch ($row[11]) {
                        case 'Faltante':
                            $caracterizacion = $notiene . "Faltante";
                            break;
                        case 'Completa':
                            $caracterizacion = "class='completo notif carac' est='$enc' usr='$usr' perf='$perf' peraca='$peraca'>Completa";
                            break;
                        case 'Incompleta':
                            $caracterizacion = "class='incompleto notif carac' est='$enc' usr='$usr' perf='$perf' peraca='$peraca'>Incompleta";
                            break;
                    }
                } else {
                    $caracterizacion = "class='posgrado'>Antiguo";
                }
            }
            $estado_est = $row[19];
            $est_celda = "";
            $est_link = "";
            $est_celda = $estado_est != 'A' ? "style='background-color:#9b9b9b;'" : "";
            $est_link = $estado_est != 'A' ? "" : "class='link_alt' target='_blank' href='" . URL_PAGES . "sivisae_instrumento.php?st=$enc_id&pa=$enc_per'";

            echo "<tr>"
            . "<input type='hidden' value='$id' id='estid-$id' class='id'/>"
            . "<td $est_celda class='tg-0ord comp1' est='$id'>$cedula</td>"
            . "<td $est_celda class='comp' est='$id'>$nombre</td>"
            . "<td $est_celda " . $caracterizacion . "</td>"
            . "<td $est_celda est='$id' " . $induccion . "</td>"
            . "</tr>"
            . "<div id='estudiante-$id' style='display:none'>"
            . "CEAD: <b>$cead</b><br>"
            . "Programa: <b>$cod_prog - $prog</b><br>"
            . "Escuela: <b>$escuela</b><br>"
            . "Periodo: <b>$per_desc</b><br>"
            . "Tipo estudiante: <b>$tp_estudiante</b><br>"
            . "</div>"
            . "<div id='estudiante2-$id' style='display:none'>"
            . "CEAD: <b>$cead</b><br>"
            . "Programa: <b>$cod_prog - $prog</b><br>"
            . "Escuela: <b>$escuela</b><br>"
            . "Periodo: <b>$per_desc</b><br>"
            . "Tipo estudiante: <b>$tp_estudiante</b><br>"
            . "</div>";
            if ($induccion != '0') {
                $inducciones = $consulta->induccionesRealizadas($id);
                echo "<div id='induccion-$id' style='display:none'>";
                $cont = 1;
                while ($row1 = mysql_fetch_array($inducciones)) {
                    echo "$cont - Tipo: <b>$row1[0]</b><br>"
                    . "&nbsp&nbsp&nbsp&nbsp&nbsp Fecha: <b>$row1[1]</b><br>";
                    $cont = $cont + 1;
                }
                echo "</div>";
            }
            if ($seguimiento !== "No tiene") {
                $seguimientos = $consulta->seguimientosRealizados($auditor_estudiante_id);

                echo "<div id='seguimiento-$id' style='display:none'>";
                $cont = 1;
                $contador_iteraciones = 0;
                while ($row1 = mysql_fetch_array($seguimientos)) {
                    $seg_id = base64_encode($row1[0]);
                    $cierre = ($row1[3] <> '') ? $row1[3] : "Sin cerrar";
                    $actualizacion = ($row1[3] <> '') ? $row1[3] : $row1[2];

                    echo "<b>Seguimiento No. $cont </b></br></br> "
                    . "Creación: <b>$row1[1] ||</b> Última actualización: <b>$actualizacion</b> ||</b> Cierre: <b>$cierre</b></br>"
                    . "Cursos: <b>$row1[4] || </b> Auditados: <b>$row1[5] ||</b> Estado: <b>$row1[6]</b> </br> ";

                    // Resumen de seguimientos - Modificacion 23-10-2015 - acma
                    $est_id = $id;
                    $cursos = $consulta->materiasEstByPeriodo($est_id, $periodo, $row1[0], 2);
                    echo "<br><b>Listado de Cursos - Seg. $cont </b><br><br>";

                    while ($row = mysql_fetch_array($cursos)) {
                        $mat_id = $row[0];
                        $mat = ucwords(preg_replace($sintilde, $tildes, $row[1]));
                        $segto_id = $seg_id != 'n' ? $row[3] : 'n';
                        $nov = $row[4]; // !== 'A' ? '*' : '';
                        $class = $seg_id != 'n' ? $row[2] : "botones";
                        $fecha_seg = "<input type='hidden' id='fecha-seg_$segto_id' name='fecha-seg_$segto_id' value='" . $row[5] . "'/>";
                        $disable = "href='#' onclick=\"return cargarCurso('$mat_id', '$segto_id');\"";
                        if ($nov !== 'A') {
                            $class = "btn_inactivo";
                            $disable = "";
                        }
                        $imp = "";


                        if ($row[5] > FECHA_SEG_ITERACIONES && $row[5] != 'no') {
                            $iteracion_curso = "|| Iteración: " . $row[7];
                            if ($row[7] > $contador_iteraciones) {
                                $contador_iteraciones = $row[7];
                            }
                        } else {
                            $iteracion_curso = "";
                        }

                        echo "<div $disable class='tipo $class' $imp sg='$segto_id' tp='$nov'> <font size='2'>" . $mat . " || Estado: " . $consulta->descripcionNovedad($nov) . " " . $iteracion_curso . "<font></div><br>";
                    }

                    if ($contador_iteraciones > 0) {
                        echo "<br> Iteración del Seguimiento: " . $contador_iteraciones . "";
                    }


                    echo "<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"
                    . "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"
                    . "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"
                    . "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";



                    echo "<a target='_blank' href='" . URL_PAGES . "sivisae_instrumento.php?st=$enc_id&pa=$enc_per&sg=$seg_id' class='botones_ver_seg'>Ver/Editar</a> "
                    . "<a target='_blank' href='" . URL_PAGES . "sivisae_soliciitud_eliminar_seguimiento.php?st=$enc_id&pa=$enc_per&sg=$seg_id' class='botones_elim_seg'>Eliminar</a><br><br>";
                    $cont = $cont + 1;
                }
                echo "<br></div> ";
            }
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
}
$consulta->destruir();
