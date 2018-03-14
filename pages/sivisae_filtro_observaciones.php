<?php
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$auditoresConsulta = $consulta->auditoresPermisos($_SESSION["usuarioid"]);
$auditores = 0;
//Se recorren los permisos del auditor para consultar todos los registros o solo los de su asignacion
while ($row = mysql_fetch_array($auditoresConsulta)) {
    $auditores++;
}

if ($auditores > 0) {
    $auditores = $_SESSION["usuarioid"];
} else {
    $auditores = 'T';
}

$permisos_filtro = $consulta->filtro_variables($_SESSION['modulo'], $_SESSION['perfilid']);
    while ($row = mysql_fetch_array($permisos_filtro)) {
        $filtro_escuelas = $row[0];
        $filtro_zonas = $row[1];
    }

$periodos = $consulta->periodos();
$zonas = $consulta->zonas();
$escuelas = $consulta->escuelas();
$cead = $consulta->ceadSegunZona("T", $filtro_zonas, $_SESSION["sede"]);
$programa = $consulta->programaSegunEscuela("T", $filtro_escuelas, $_SESSION["programa_usuario"]);
?>

<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        $(".chosen-select-deselect").chosen({allow_single_deselect: true});
        $(".chosen-select").chosen({no_results_text: "Uups, No se encontraron registros!"});
        $('#zona, #escuela').chosen().change(function () {
            filtros($(this).prop("id").toLowerCase());
        });
    });

    function filtros(cual) {
        var dataString = {
            "select": cual,
            "valores": $("#" + cual).val()
        };
        $.ajax({
            type: 'POST',
            url: 'src/filtrosCB.php',
            data: dataString,
            success: function (data) {
                if (data !== null) {
                    if (cual === 'zona') {
                        $("#cead").hide();
                        $(".f").html(data);
                        $(".chosen-select").chosen();
                    }
                    if (cual === 'escuela') {
                        $("#programa").hide();
                        $('.e').html(data);
                        $(".chosen-select").chosen();
                    }
                } else {
                    $("#cead").show();
                    $("#programa").show();
                }
            }

        });
    }
</script>

<table>
    <tr>
        <td>
            Periodo:
            <select id="periodo" name="periodo" data-placeholder="Seleccione un periodo" class="chosen-select-deselect" style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysql_fetch_array($periodos)) {
                    echo "<option value='$row[0]'>" .
                    utf8_encode(ucwords(strtolower($row[1]))) .
                    "</option>";
                }
                ?>
            </select>
        </td>
        <td class="sel_zona">
            Zona:
            <select id="zona" name="zona[]" data-placeholder="Seleccione una Zona" class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysql_fetch_array($zonas)) {
                    echo "<option value='$row[0]'>" .
                    ucwords(strtolower($row[1])) .
                    "</option>";
                }
                ?>
            </select>
        </td>
        <td class="f sel_zona">
            <div id="div-zona">
                CEAD:
                <select id="cead" name="cead[]" data-placeholder="Seleccione un CEAD" class="chosen-select" multiple style="width:180px;" tabindex="4">
                    <option value="T">Todos</option>
                    <?php
                    while ($row = mysql_fetch_array($cead)) {
                        echo "<option value='$row[0]'>" .
                        $row[1] . " - " . ucwords(strtolower($row[2])) .
                        "</option>";
                    }
                    ?>
                </select>
            </div>
        </td>
        <td>
            Escuela:
            <select id="escuela" name="escuela[]" data-placeholder="Seleccione una Escuela"   class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysql_fetch_array($escuelas)) {
                    echo "<option value='$row[0]'>" .
                    ucwords(strtolower($row[0])) .
                    "</option>";
                }
                ?>
            </select>
        </td>
        <td class="e">
            Programa:
            <select id="programa" name="programa[]" data-placeholder="Seleccione un programa" class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysql_fetch_array($programa)) {
                    echo "<option value='$row[0]'>" .
                    $row[1] . " - " . ucwords(strtolower($row[2])) .
                    "</option>";
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="5" align="center"><br>
            <div id="filtro_tb" style="alignment-adjust: central">
                <table>
                    <tr>
                        <td >Registros por p&aacute;gina
                            <select data-placeholder="Seleccione un periodo" class="chosen-select" name='registros' id='registros' style="width: 60px;" tabindex='2'>";
                                <option value='50'>50</option>
                                <option value='100'>100</option>
                                <option value='300'>300</option>
                            </select>
                        </td>
                        <td>
                            Buscar: 
                            <input type="text" id="buscar" name="buscar" />
                        </td>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="5" style="alignment-adjust: central"><br>
            <p><input id="crear" class="submit_fieldset_autenticacion" type="button" onclick="return listaEstudiantes()" value="Ver"/></p>
            <input id="auditores" name="auditores" type="hidden" value="<?php echo $auditores; ?>">
        </td>
    </tr>
</table>