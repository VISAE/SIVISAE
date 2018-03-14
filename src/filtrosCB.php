<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();

if (isset($_POST['select'])) {
    $permisos_filtro = $consulta->filtro_variables($_SESSION['modulo'], $_SESSION['perfilid']);
    while ($row = mysql_fetch_array($permisos_filtro)) {
        $filtro_escuelas = $row[0];
        $filtro_zonas = $row[1];
    }

    $select = $_POST['select'];
    $valor;
    if ($_POST['valores'] != null) {
        $valor = implode("', '", $_POST['valores']);
    } else {
        $valor = "T";
    }
    $opcion;

    if ($select !== "escuela") {
        $opcion = $consulta->ceadSegunZona($valor, $filtro_zonas, $_SESSION["sede"]);
        echo "CEAD:
            <select id='cead' name='cead[]' data-placeholder='Seleccione un CEAD' class='chosen-select' multiple style='width:180px;' tabindex='4'>";
    } else {
        $opcion = $consulta->programaSegunEscuela($valor, $filtro_escuelas, $_SESSION["programa_usuario"]);
        echo "Programa:
            <select id='programa' name='programa[]' data-placeholder='Seleccione un Programa' class='chosen-select' multiple style='width:180px;' tabindex='4'>";
    }
    while ($row = mysql_fetch_array($opcion)) {
        echo '<option value="' . $row[0] . '">' .
        $row[1] . ' - ' . ucwords($row[2]) .
        '</option>';
    }
    echo "</select>";
}
