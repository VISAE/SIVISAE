<?php

session_start();

include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();

echo ' <div style="background-color: #ffffff" align="center">
                                    <table style="width: 50%" border="0">
                                        <tr>
                                            <td style="width: 40%" rowspan="8" align="center">
                                               <img src="template/imagenes/generales/avatar.png" width="100%" height="100%"></img>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <h2 class="p_fieldset_autenticacion">' . $_SESSION["nom"] . '</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                ' . $_SESSION["ced"] . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                ' . $_SESSION["correo"] . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                ' . $_SESSION["fecha_nac_compl"] . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                ' . $_SESSION["telefono"] . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                ' . $_SESSION["celular"] . '
                                            </td>
                                        </tr>
                                         <tr>
                                            <td align="left">
                                                ' . $_SESSION["skype"] . '
                                            </td>
                                        </tr>
                                    </table>
                                </div>';


//consultar datos de usuario segun perfil consejeros o monitores
if ($_SESSION["perfilid"] === "5" || $_SESSION["perfilid"] === "9" || $_SESSION["perfilid"] === "7") {
// Cargar horarios de atencion guardados en BD
    $horarios = $consulta->consultarHorarios($_SESSION["usuarioid"], $_SESSION["perfilid"]);

//Se muestran los horarios
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
    $get_total_rows = 6;

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

    if (count($horarios) <= 0) {
        echo 'No existen usuarios';
    } else {

        echo "<br><div style='background-color: #ffffff' align='center'>
        <table id='tb_estudiantes' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>DÍA</th>
						<th>HORA</th>
						<th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
        while ($row = mysql_fetch_array($horarios)) {
            $id_consejero = $row[0];
            $usuario_id = $row[1];
            $estado = $row[2];
            $cead = $row[3];
            $con_general = $row[4];
            $lunes = ($row[5] != '') ? $row[5] : "No hay horario registrado";
            $martes = ($row[6] != '') ? $row[6] : "No hay horario registrado";
            $miercoles = ($row[7] != '') ? $row[7] : "No hay horario registrado";
            $jueves = ($row[8] != '') ? $row[8] : "No hay horario registrado";
            $viernes = ($row[9] != '') ? $row[9] : "No hay horario registrado";
            $sabado = ($row[10] != '') ? $row[10] : "No hay horario registrado";
        }


        $guia_usuario_id = $usuario_id;

        echo "<tr>"
        . "<td>Lunes</td>"
        . "<td>$lunes</td>"
        . "<td> <button title='Editar Horario' " . $_SESSION['opc_ed'] . " id='boton_editar" . $guia_usuario_id . "' onclick='activarpopupeditar(" . $guia_usuario_id . ")'></button> </td>"
        . "<td> <button title='Eliminar Horario' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $guia_usuario_id . "' onclick='activarpopupeliminar(" . $guia_usuario_id . ", 1)'></button> </td>"
        . "<input type='hidden' id='input_" . $guia_usuario_id . "' value='" . $usuario_id . "|lunes|" . $lunes . "'></input>"
        . "</tr>";
        $guia_usuario_id++;
        echo "<tr>"
        . "<td>Martes</td>"
        . "<td>$martes</td>"
        . "<td> <button title='Editar Horario' " . $_SESSION['opc_ed'] . " id='boton_editar" . $guia_usuario_id . "' onclick='activarpopupeditar(" . $guia_usuario_id . ")'></button> </td>"
        . "<td> <button title='Eliminar Horario' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $guia_usuario_id . "' onclick='activarpopupeliminar(" . $guia_usuario_id . ",2)'></button> </td>"
        . "<input type='hidden' id='input_" . $guia_usuario_id . "' value='" . $usuario_id . "|martes|" . $martes . "'></input>"
        . "</tr>";
        $guia_usuario_id++;
        echo "<tr>"
        . "<td>Miércoles</td>"
        . "<td>$miercoles</td>"
        . "<td> <button title='Editar Horario' " . $_SESSION['opc_ed'] . " id='boton_editar" . $guia_usuario_id . "' onclick='activarpopupeditar(" . $guia_usuario_id . ")'></button> </td>"
        . "<td> <button title='Eliminar Horario' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $guia_usuario_id . "' onclick='activarpopupeliminar(" . $guia_usuario_id . ",3)'></button> </td>"
        . "<input type='hidden' id='input_" . $guia_usuario_id . "' value='" . $usuario_id . "|miercoles|" . $miercoles . "'></input>"
        . "</tr>";
        $guia_usuario_id++;
        echo "<tr>"
        . "<td>Jueves</td>"
        . "<td>$jueves</td>"
        . "<td> <button title='Editar Horario' " . $_SESSION['opc_ed'] . " id='boton_editar" . $guia_usuario_id . "' onclick='activarpopupeditar(" . $guia_usuario_id . ")'></button> </td>"
        . "<td> <button title='Eliminar Horario' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $guia_usuario_id . "' onclick='activarpopupeliminar(" . $guia_usuario_id . ",4)'></button> </td>"
        . "<input type='hidden' id='input_" . $guia_usuario_id . "' value='" . $usuario_id . "|jueves|" . $jueves . "'></input>"
        . "</tr>";
        $guia_usuario_id++;
        echo "<tr>"
        . "<td>Viernes</td>"
        . "<td>$viernes</td>"
        . "<td> <button title='Editar Horario' " . $_SESSION['opc_ed'] . " id='boton_editar" . $guia_usuario_id . "' onclick='activarpopupeditar(" . $guia_usuario_id . ")'></button> </td>"
        . "<td> <button title='Eliminar Horario' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $guia_usuario_id . "' onclick='activarpopupeliminar(" . $guia_usuario_id . ",5)'></button> </td>"
        . "<input type='hidden' id='input_" . $guia_usuario_id . "' value='" . $usuario_id . "|viernes|" . $viernes . "'></input>"
        . "</tr>";
        $guia_usuario_id++;
        echo "<tr>"
        . "<td>Sábado</td>"
        . "<td>$sabado</td>"
        . "<td> <button title='Editar Horario' " . $_SESSION['opc_ed'] . " id='boton_editar" . $guia_usuario_id . "' onclick='activarpopupeditar(" . $guia_usuario_id . ")'></button> </td>"
        . "<td> <button title='Eliminar Horario' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $guia_usuario_id . "' onclick='activarpopupeliminar(" . $guia_usuario_id . ",6)'></button> </td>"
        . "<input type='hidden' id='input_" . $guia_usuario_id . "' value='" . $usuario_id . "|sabado|" . $sabado . "'></input>"
        . "</tr>";

        echo "     </tbody>
                    </table>";

        echo '<div align="center"><br><br>';
        /* We call the pagination function here to generate Pagination link for us. 
          As you can see I have passed several parameters to the function. */
        echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        echo '</div></div>';
    }
}

