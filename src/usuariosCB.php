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
$cantEst = mysql_fetch_array($consulta->cantRegistros("select count(*) from usuario where estado_estado_id in (1,2)"));
$get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
$total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
$page_position = (($page_number - 1) * $item_per_page);

//Consulta el listado de usuarios
$estudiantes = $consulta->traerUsuarios($page_position, $item_per_page);

if (count($estudiantes) <= 0) {
    echo 'No existen usuarios';
} else {

    echo "<br>
        <table id='tb_estudiantes' border='1' class='tg' style='undefined;table-layout: fixed; width=100%'>
				<thead>
					<tr>
						<th>CÉDULA</th>
						<th>NOMBRE</th>
						<th>FECHA EXPIRACIÓN</th>
						<th>FECHA CREACIÓN</th>
                                                <th>TELÉFONO</th>
                                                <th>CORREO</th>
                                                <th>ÚLTIMO INGRESO</th>
                                                <th>CENTRO</th>
                                                <th>PERFIL</th>
                                                <th>ESTADO</th>
                                                <th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
    while ($row = mysql_fetch_array($estudiantes)) {
        $cedula = $row[0];
        $nombre = ucwords(strtolower($row[1]));
        $fecha_expiracion = $row[2];
        $fecha_creacion = $row[3];
        $telefono = $row[4];
        $correo = $row[5];
        $ultimo_ingreso = $row[6];
        $centro = $row[7];
        $estado = ucwords($row[8]);
        $usuario_id = $row[9];
        $login_usu = $row[10];
        $perfil_usu = $row[11];
        $sede_usu = $row[12];
        $celular = $row[13];
        $skype = $row[14];
        $desc_perfil = $row[15];
        $codigo_sede = $row[16];
        $usuario_perfil_id = $row[17];


        echo "<tr>"
        . "<td>$cedula</td>"
        . "<td>$nombre</td>"
        . "<td>$fecha_expiracion</td>"
        . "<td>$fecha_creacion</td>"
        . "<td>$telefono</td>"
        . "<td>$correo</td>"
        . "<td>$ultimo_ingreso</td>"
        . "<td>$centro</td>"
        . "<td>$desc_perfil</td>"
        . "<td>$estado</td>"
        . "<td> <button title='Editar Usuario' ".$_SESSION['opc_ed']." id='boton_editar" . $usuario_id . "' onclick='activarpopupeditar(" . $usuario_id . ")'></button> </td>"
        . "<td> <button title='Eliminar Usuario' ".$_SESSION['opc_el']."  id='boton_eliminar" . $usuario_id . "' onclick='activarpopupeliminar(" . $usuario_id . "," . $usuario_perfil_id . ")'></button> </td>"
        . "<input type='hidden' id='input_" . $usuario_id . "' value='" . $usuario_id . "|" . $cedula . "|" . $nombre . "|" . $correo . "|" . $login_usu . "|" . $perfil_usu . "|" . $telefono . "|" . $celular . "|" . $skype . "|" . $sede_usu . "|" . $codigo_sede . "|" . $usuario_perfil_id . "'></input>"
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