<?php

session_start();

include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();
if ($_POST['accion'] === 'n') {
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
    $cantEst = mysql_fetch_array($consulta->cantRegistros("select count(*) from perfil where estado_estado_id in (1,2)"));
    $get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

//Consulta el listado de usuarios
    $perfiles = $consulta->traerPerfiles($page_position, $item_per_page);

    if (count($perfiles) <= 0) {
        echo 'No existen perfiles';
    } else {

        echo "<br>
        <table id='tb_perfiles' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>PERFIL</th>
						<th>ESTADO</th>
						<th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
        while ($row = mysql_fetch_array($perfiles)) {
            $id_perfil = $row[0];
            $descripcion_perfil = ucwords(strtolower($row[1]));
            $estado = $row[2];

            echo "<tr>"
            . "<td>$descripcion_perfil</td>"
            . "<td>$estado</td>"
            . "<td> <button title='Editar Perfil'  " . $_SESSION['opc_ed'] . " id='boton_editar" . $id_perfil . "' onclick='activarpopupeditar(" . $id_perfil . ")'></button> </td>"
            . "<td> <button title='Eliminar Perfil' " . $_SESSION['opc_el'] . " id='boton_eliminar" . $id_perfil . "' onclick='activarpopupeliminar(" . $id_perfil . ")'></button> </td>"
            . "<input type='hidden' id='input_" . $id_perfil . "' value='" . $id_perfil . "|$descripcion_perfil|$estado'></input>"
            . "</tr>";
        }

        echo "     </tbody>
                    </table>";

        echo '<div align="center"><br><br>';
        echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        echo '</div>';
    }
} else {
    if (isset($_POST["accion"]) && $_POST['accion'] === 'e' && isset($_POST["perfil"])) {
        $id_perfil = $_POST['perfil'];
        $desc_perfil = $_POST['desc_perfil'];
        $tabla = '
            <script type="text/javascript">

                ///Seguridad - Inicio
                $(document).ready(function () {
                    $(".icheck_e").on("ifChecked", function (event) {
                        mostrarPermisosE($(this).val());
                        agregar($(this).val());
                    })
                            .on("ifUnchecked", function () {
                                mostrarPermisosE($(this).val());
                                quitar($(this).val());
                            });
                });
            </script>

<table>
                    <tr>
                        <td style="width: 200px"><label for="nombre_perfil_e">Nombre del Perfil (*):</label></td>
                        <td><input id="nombre_perfil_e" name="nombre_perfil_e" type="text" maxlength="30" required="Falta el nombre" value="' . $desc_perfil . '"/></td>
                            <input type="hidden" id="perfil_id" name="perfil_id" value="' . $id_perfil . '"/>
                    </tr>
                    <tr>                                
                        <td>
                            <h4>Opciones:</h4>
                        </td>
                    </tr>';
        $result = $consulta->permisosPerfil($id_perfil);
        while ($row = mysql_fetch_array($result)) {
            $opid = $row[0];
            $desc = $row[1];
            $opc = $row[2];
            $crear = $row[3] === '1' ? "checked" : "";
            $editar = $row[4] === '1' ? "checked" : "";
            $eliminar = $row[5] === '1' ? "checked" : "";
            $filtro_escuela = $row[6];
            $selectEscuela = '<td style="width: 80px">
                            <select id="filtro_escuela' . $opid . '" name="filtro_escuela' . $opid . '" data-placeholder="Seleccione un nivel de consulta de la escuela" class="chosen-select-deselect" style="width:80px;" tabindex="4">';
            switch ($filtro_escuela) {
                case 1:
                    $selectEscuela.='<option selected value="1">Todos</option><option  value="2">Escuela</option><option value="3">Programa</option>';
                    break;
                case 2:
                    $selectEscuela.='<option value="1">Todos</option><option selected value="2">Escuela</option><option value="3">Programa</option>';
                    break;
                case 3:
                    $selectEscuela.='<option value="1">Todos</option><option value="2">Escuela</option><option selected value="3">Programa</option>';
                    break;
                default:
                    $selectEscuela.='<option selected value="0">Seleccione</option><option value="1">Todos</option><option value="2">Escuela</option><option value="3">Programa</option>';
                    break;
            }
            $selectEscuela.='</select>
                        </td>';

            $filtro_zona = $row[7];

            $selectZona = '<td style="width: 80px">
                            <select id="filtro_zona' . $opid . '" name="filtro_zona' . $opid . '" data-placeholder="Seleccione un nivel de consulta de la zona" class="chosen-select-deselect" style="width:80px;" tabindex="4">';
            switch ($filtro_zona) {
                case 1:
                    $selectZona.='<option selected value="1">Todos</option><option value="2">Zona</option><option value="3">Centro</option>';
                    break;
                case 2:
                    $selectZona.='<option value="1">Todos</option><option selected value="2">Zona</option><option value="3">Centro</option>';
                    break;
                case 3:
                    $selectZona.='<option value="1">Todos</option><option value="2">Zona</option><option selected value="3">Centro</option>';
                    break; 
                default:
                    $selectZona.='<option selected value="0">Seleccione</option><option value="1">Todos</option><option value="2">Zona</option><option value="3">Centro</option>';
                    break; 
            }
            $selectZona.='</select>
                        </td>';

            $checked = "";
            $display = " style='display: none'";
            $check_per = "";
            if ($opc === '1') {
                $checked = "checked";
                $display = "";
            }
            $tabla.='<tr>
                        <td>
                            <label><input type="checkbox" class="icheck_e" ' . $checked . ' value="' . $opid . '" name="opcion_e[]" id="opcion_e' . $opid . '"> ' . $desc . '</label>
                        </td>
                        <td style="width: 300px">
                            <div id="divperm_e' . $opid . '" ' . $display . '>
                                <label><input type="checkbox" class="icheck" ' . $crear . ' value="1" name="perm_e' . $opid . '[]" id="perm_e' . $opid . '"> Crear</label>
                                <label><input type="checkbox" class="icheck" ' . $editar . ' value="2" name="perm_e' . $opid . '[]" id="perm_e' . $opid . '"> Editar</label>
                                <label><input type="checkbox" class="icheck" ' . $eliminar . ' value="3" name="perm_e' . $opid . '[]" id="perm_e' . $opid . '"> Eliminar</label>
                            </div>
                        </td> ' . $selectEscuela . ' ' . $selectZona . ' </tr>';
        }
        $tabla.=' <tr>
            <td style = "alignment-adjust: central" colspan = "3" >
            <p><input id = "editar" class = "submit_fieldset_autenticacion" type = "submit" onclick = "return submitFormEditar()" value = "Crear Perfil"/></p>
            <div align = "center" id = "result"></div>
            </td>
            </tr>
            </table>';
        echo $tabla;
    }
}
