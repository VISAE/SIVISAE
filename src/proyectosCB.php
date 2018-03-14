<?php

/*
 * 
 *   @author Ing. Andres Mendez
 * 
 */
session_start();

include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();
$accion = $_POST["accion"];
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);

if ($accion === "listado") {
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
    $cantEst = mysql_fetch_array($consulta->cantRegistros("select count(1) from SIGRA.proyecto where estado_id = 1;"));
    $get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

//Consulta el listado
    $consulta1 = $consulta->traerProyectos($page_position, $item_per_page);
    if (count($consulta1) <= 0) {
        echo 'No existen proyectod';
    } else {

        echo "<br>
        <table id='tb_grilla' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>NOMBRE PROYECTO</th>
						<th>EJE</th>
						<th>LINEA</th>
                                                <th>COBERTURA</th>
						<th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
        while ($row = mysqli_fetch_array($consulta1)) {
            $proy_id = $row[0];
            $nombre = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[1])));
            $eje = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[2])));
            $linea = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[3])));
            $cobertura = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[4])));
            $eje_id = $row[5];
            $linea_id = $row[6];
            $cobertura_id = $row[7];
            echo "<tr>"
            . "<td>$nombre</td>"
            . "<td>$eje</td>"
            . "<td>$linea</td>"
            . "<td>$cobertura</td>"
            . "<td> <button title='Editar Proyecto' " . $_SESSION['opc_ed'] . " id='boton_editar" . $proy_id . "' onclick='activarpopupeditar(" . $proy_id . ")'></button> </td>"
            . "<td> <button title='Eliminar Proyecto' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $proy_id . "' onclick='activarpopupeliminar(" . $proy_id . ")'></button> "
            . "<input type='hidden' id='input_" . $proy_id . "' value='" . $proy_id . "|" . $nombre . "|" . $eje_id . "|" . $linea_id . "|" . $cobertura_id . "'></input> </td>"
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
}

if ($accion === "traer_proy") {
    $pr_id = $_POST['pr_id'];
    $data = mysqli_fetch_array($consulta->traerProyecto($pr_id));

    $lineas = $consulta->getLinea($data[2]);
    $html1 = '<option value=""></option>';
    foreach ($lineas as $linea) {
        $html1 .= '<option value="' . $linea[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($linea[1]))) . '</option>';
    }
    $html1 .= '<option value="o">Otro (Crear)</option>';

    $proyecto = array("id" => $data[0], "nombre" => $data[1], "eje" => $data[2], "linea" => $data[3], "cobertura" => $data[4], "cober" => $data[5], "pres" => $data[9],
        "contenido" => $html1);
    echo json_encode($proyecto);
}

if ($accion === "linea") {
    if ($_POST['accion2'] === "crear") {
        $eje = "";
        if ($_POST['desc_ejec'] != "") {
            $eje = $_POST['desc_ejec'];
        } else {
            $eje = $_POST['desc_ejee'];
        }

        $ins = $consulta->crearLinea($_POST['desc_linea'], $eje);
        $retorno = array();
        $html1 = '<select data-placeholder="Seleccione..." id="linea" name="linea" onchange="c_linea(\'\');">
                                                        <option value=""></option>';
        $lineas = $consulta->getLinea('');
//        while ($linea = mysqli_fetch_array($lineas)) {
        foreach ($lineas as $linea) {
            $html1 .= '<option value="' . $linea[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($linea[1]))) . '</option>';
        }
        $html1 .= '<option value="o">Otro (Crear)</option>
            </select>
            <div id="otro-linea"></div>';
        $html2 = '<select data-placeholder="Seleccione..." id="linea_e" name="linea_e" onchange="c_linea(\'_e\');">
                                                        <option value=""></option>';
        foreach ($lineas as $linea) {
            $html2 .= '<option value="' . $linea[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($linea[1]))) . '</option>';
        }
        $html2 .= '<option value="o">Otro (Crear)</option>
            </select>
            <div id="otro-linea_e"></div>';
        $retorno['id'] = $ins;
        $retorno['html1'] = $html1;
        $retorno['html2'] = $html2;
        echo json_encode($retorno);
    }
    if ($_POST['accion2'] === "campos") {
        $tp = $_POST['tp'];
        $html = '<table>
                    <tr>
                        <td><label for="descripcion">Descripci&oacute;n</label><br/>
                            <input id="desc_linea' . $tp . '" name="desc_linea" type="text" maxlength="30" />
                        <td align="center">
                            <input style="width: 180px;" id="id_el_p" name="id_e_p" type="hidden" maxlength="30" />
                            <p><a class="botones" id="crear_linea' . $tp . '" onclick="return n_linea(\'' . $tp . '\');">Crear</a></p>
                        </td>
                    </tr>
                </table>';
        echo "$html";
    }
}
if ($accion === "cobertura") {
    if ($_POST['accion2'] === "crear") {
        $ins = $consulta->crearCobertura($_POST['desc_cobertura']);
        $retorno = array();
        $html1 = '<select data-placeholder="Seleccione..." id="cobertura" name="cobertura" onchange="c_cobertura(\'\');">
                                                        <option value=""></option>';
        $coberturas = $consulta->getCobertura();
        foreach ($coberturas as $cobertura) {
            $html1 .= '<option value="' . $cobertura[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($cobertura[1]))) . '</option>';
        }
        $html1 .= '<option value="o">Otro (Crear)</option>
            </select>
            <div id="otro-cobertura"></div>';
        $html2 = '<select data-placeholder="Seleccione..." id="cobertura_e" name="cobertura_e" onchange="c_cobertura(\'_e\');">
                                                        <option value=""></option>';
        foreach ($coberturas as $cobertura) {
            $html2 .= '<option value="' . $cobertura[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($cobertura[1]))) . '</option>';
        }
        $html2 .= '<option value="o">Otro (Crear)</option>
            </select>
            <div id="otro-cobertura_e"></div>';
        $retorno['id'] = $ins;
        $retorno['html1'] = $html1;
        $retorno['html2'] = $html2;
//        print_r($retorno);
        echo json_encode($retorno);
    }
    if ($_POST['accion2'] === "campos") {
        $tp = $_POST['tp'];
        $campo = strtolower($_POST['campo']);
        $html = "";
        $res;
        switch ($campo) {
            case "zona":
                $res = $consulta->zonas('T');
                $html = '<select data-placeholder="Seleccione la(s) Zona(s)" id="zona' . $tp . '" name="zona[]' . $tp . '" class="chzn" multiple>
                                                            <option value=""></option>';
                while ($row = mysql_fetch_array($res)) {
                    $html .= "<option value='$row[0]'>" . ucwords($row[1]) . "</option>";
                }
                $html .= '</select>';
                break;
            case "cead":
                $res = $consulta->ceadSegunZonas('T');
                $html = '<select data-placeholder="Seleccione el(los) Centro(s)" id="cead' . $tp . '" name="cead[]' . $tp . '" class="chzn" multiple >
                                                            <option value=""></option>';
                while ($row = mysql_fetch_array($res)) {
                    $html .= "<option value='$row[1]'>" . ucwords($row[2]) . "</option>";
                }
                $html .= '</select>';
                break;
            case "escuela":
                $res = $consulta->escuelas_proyectos();
                $html = '<select data-placeholder="Seleccione la(s) Escuela(s)" id="escuela' . $tp . '" name="escuela[]' . $tp . '" class="chzn" multiple >
                                                            <option value=""></option>';
                while ($row = mysql_fetch_array($res)) {
                    $html .= "<option value='$row[1]'>" . ucwords($row[0]) . "</option>";
                }
                $html .= '</select>';
                break;
            case "programa":
                $res = $consulta->programaSegunEscuela('T', "1", "0");
                $html = '<select data-placeholder="Seleccione el(los) Programa(s)" id="programa' . $tp . '" name="programa[]' . $tp . '" class="chzn" multiple >
                                                            <option value=""></option>';
                while ($row = mysql_fetch_array($res)) {
                    $html .= "<option value='$row[1]'>" . ucwords($row[2]) . "</option>";
                }
                $html .= '</select>';
                break;
            case "otro (crear)":
                $html = '<table>
                    <tr>
                        <td><label for="descripcion">Descripci&oacute;n</label><br/>
                            <input id="desc_cobertura' . $tp . '" name="desc_cobertura' . $tp . '" type="text" maxlength="30" />
                        <td align="center">
                            <input style="width: 180px;" id="id_el_p" name="id_e_p" type="hidden" maxlength="30" />
                            <p><a class="botones" id="crear_cobertura' . $tp . '" onclick="return n_cobertura(\'' . $tp . '\');">Crear</a></p>
                        </td>
                    </tr>
                </table>';
                break;
            default :
                $html = "";
        }
        echo "$html";
    }
}

if ($accion === "linea_eje") {
    if ($_POST['accion2'] === "cargar") {
        $lineas = $consulta->getLinea($_POST['tp']);
        $html1 = '<option value=""></option>';
        foreach ($lineas as $linea) {
            $html1 .= '<option value="' . $linea[0] . '">' . ucwords(preg_replace($sintilde, $tildes, utf8_decode($linea[1]))) . '</option>';
        }
        $html1 .= '<option value="o">Otro (Crear)</option>';
        echo json_encode($html1);
    }
}

if ($accion === "crear_proy") {
    $usr = $_SESSION['usuarioid'];
    $linea_id = $_POST['linea'];
    $eje = $_POST['eje'];
    $cobertura_id = $_POST['cobertura'];
    $nombre = $_POST['nombre'];
    $cobertura = $_POST['chzn'];
    $presupuesto = $_POST['presupuesto'];
    $ins = $consulta->crearProyecto($nombre, $eje, $linea_id, $cobertura_id, $cobertura, $usr, $presupuesto);
    $arr = array('cod' => $ins);

    echo json_encode($arr);
}
if ($accion === "update_proy") {
    $usr = $_SESSION['usuarioid'];
    $proy_id = $_POST['proy_id_e'];
    $linea_id = $_POST['linea_e'];
    $eje = $_POST['eje_e'];
    $cobertura_id = $_POST['cobertura_e'];
    $cobertura = $_POST['chzn'];
    $presupuesto = $_POST['presupuesto_e'];
    $upt = $consulta->updateProyecto($proy_id, $eje, $linea_id, $cobertura_id, $cobertura, $usr, $presupuesto);
    if ($upt) {
        echo "<label style='color: #004669'>Se actualizó el Proyecto correctamente.</label>";
    } else {
        echo "<label style='color: #EC2121'>El Proyecto ya existe, por favor cambiar el nombre.</label>";
    }
}
if ($accion === "elim_proy") {
    $usr = $_SESSION['usuarioid'];
    $proy_id = $_POST['proy_id_el'];
    $upt = $consulta->deleteProyecto($proy_id);
    if ($upt) {
        echo "<label style='color: #004669'>Se eliminó el Proyecto correctamente.</label>";
    } else {
        echo "<label style='color: #EC2121'>El Proyecto no pudo ser eliminado.</label>";
    }
}

$consulta->destruir();
?>
