<?php
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$auditores = $consulta->auditores();
$pf = $_SESSION['perfilid'];

if (isset($pf) && $pf !== '1' && $pf !== '3' && $pf !== '4' && $pf !== '6' && $pf !== '7') {
    $periodos = $consulta->periodos();
} else {
    $periodos = $consulta->periodos_administrador();
}

$_SESSION['modulo'] = $modulo;

// Filtros de permisos 
$permisos_filtro = $consulta->filtro_variables($modulo, $pf);
while ($row = mysql_fetch_array($permisos_filtro)) {
    $filtro_escuelas = $row[0];
    $filtro_zonas = $row[1];
}
$zonas = $consulta->filtro_zonas($filtro_zonas, $_SESSION["sede"]);
$cead = $consulta->filtro_ceads($filtro_zonas, $_SESSION["sede"]);
$escuelas = $consulta->filtro_escuelas($filtro_escuelas, $_SESSION["programa_usuario"]);
$programa = $consulta->filtro_programas($filtro_escuelas, $_SESSION["programa_usuario"]);

?>

<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        $(".chosen-select-deselect").chosen({allow_single_deselect: true});
        $(".chosen-select").chosen({no_results_text: "No se encontraron registros!"});
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
            <?php
            if ($modulo != 12) {
                ?>
                * Periodo:
                <select id="periodo" name="periodo" data-placeholder="Seleccione un periodo" class="chosen-select-deselect" style="width:180px;" tabindex="4">
                    <option value=""></option>
                    <?php
                    while ($row = mysql_fetch_array($periodos)) {
                        echo "<option value='$row[0]'>" .
                        utf8_encode(ucwords($row[1])) .
                        "</option>";
                    }
                    ?>
                </select>
            <?php } ?>
        </td>
        <td class="sel_zona">
            Zona:
            <select id="zona" name="zona[]" data-placeholder="Seleccione una Zona" class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysql_fetch_array($zonas)) {
                    echo "<option value='$row[0]'>" .
                    ucwords($row[1]) .
                    "</option>";
                }
                ?>
            </select>
        </td>
        <td class="f sel_zona">
            <div id="div-zona">
                CEAD:
                <select id="cead" name="cead[]" data-placeholder="Seleccione un CEAD" class="chosen-select" multiple style="width:180px;" tabindex="4">
                    <?php
                    if ($filtro_zonas === '3') {
                        ?>
                        <option value="T">Todos</option>
                        <?php
                    }
                    while ($row = mysql_fetch_array($cead)) {
                        echo "<option value='$row[0]'>" .
                        $row[1] . " - " . ucwords($row[2]) .
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
                    ucwords($row[0]) .
                    "</option>";
                }
                ?>
            </select>
        </td>
        <td class="e">
            Programa:
            <select id="programa" name="programa[]" data-placeholder="Seleccione un Programa" class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysql_fetch_array($programa)) {
                    echo "<option value='$row[0]'>" .
                    $row[1] . " - " . ucwords($row[2]) .
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
                        <?php
                        if ($modulo != 12) {
                            ?>
                            <td >Registros por p&aacute;gina
                                <select class="chosen-select" name='registros' id='registros' style="width: 60px;" tabindex='2'>";
                                    <option value='50'>50</option>
                                    <option value='100'>100</option>
                                    <option value='300'>300</option>
                                    <option value='500'>500</option>
                                </select>
                            </td>
                            <?php
                        }
                        ?>
                        <td>
                            Buscar: 
                            <input type="text" id="buscar" name="buscar" />
                        </td>



                        <?php
                        switch ($modulo) {
                            case 5: //Tablero de Asignacion
                                include "sivisae_filtro_consejeria.php";
                                ?>
                                <td>Tipo de Asignación
                                    <select id="tipo_asignacion" name="tipo_asignacion" data-placeholder="Seleccione un tipo de asignación" class="chosen-select-deselect" style="width:100px;" tabindex="2">
                                        <option value='3'>Todos</option>
                                        <option value='1'>Curso</option>
                                        <option value='2'>Centro</option>
                                    </select>
                                </td>
                                <?php
                                break;

                            case 9: // Reporte de Matriculados
                                break;

                            case 7: // Reporte de seguimiento gestores
                                include "sivisae_filtro_consejeria.php";
                                if ($pf !== '2') {
                                    ?>
                                    <td class="aud">Fecha Inicial 
                                        <input style="width: 180px;" id="fecha_inicio" name="fecha_inicio"  type="date" required="Por favor ingrese la fecha inicio."/>
                                    </td>
                                    <td class="aud">Fecha Final
                                        <input style="width: 180px;" id="fecha_fin" name="fecha_fin"  type="date" required="Por favor ingrese la fecha fin."/>
                                    </td>
                                    <?php
                                }
                                break;

                            case 12: // Reporte de Atenciones
                                include "sivisae_filtro_consejeria.php";
                                ?>
                                <td class="aud">Fecha Inicial
                                    <input style="width: 180px;" id="fecha_inicio" name="fecha_inicio"  type="date" required="Por favor ingrese la fecha inicio."/>
                                </td>
                                <td class="aud">Fecha Final
                                    <input style="width: 180px;" id="fecha_fin" name="fecha_fin"  type="date" required="Por favor ingrese la fecha fin."/>
                                </td>
                                <?php
                                break;

                            case 14: // Reporte de seguimientos de auditores
                                include "sivisae_filtro_auditores.php";
                                ?>
                                <td class="aud">Fecha Inicial *
                                    <input style="width: 180px;" id="fecha_inicio" name="fecha_inicio"  type="date" required="Por favor ingrese la fecha inicio."/>
                                </td>
                                <td class="aud">Fecha Final *
                                    <input style="width: 180px;" id="fecha_fin" name="fecha_fin"  type="date" required="Por favor ingrese la fecha fin."/>
                                </td>
                                <?php
                                break;

                            case 15: // Reporte de Atenciones
                                include "sivisae_filtro_monitoria.php";
                                ?>
                                <td class="aud">Fecha Inicial
                                    <input style="width: 180px;" id="fecha_inicio" name="fecha_inicio"  type="date" required="Por favor ingrese la fecha inicio."/>
                                </td>
                                <td class="aud">Fecha Final
                                    <input style="width: 180px;" id="fecha_fin" name="fecha_fin"  type="date" required="Por favor ingrese la fecha fin."/>
                                </td>
                                <?php
                                break;
                            
                            case 23: // Reporte de Psicosocial
                                ?>
                                <td class="aud">Fecha Inicial
                                    <input style="width: 180px;" id="fecha_inicio" name="fecha_inicio"  type="date" required="Por favor ingrese la fecha inicio."/>
                                </td>
                                <td class="aud">Fecha Final
                                    <input style="width: 180px;" id="fecha_fin" name="fecha_fin"  type="date" required="Por favor ingrese la fecha fin."/>
                                </td>
                                <?php
                                break;


                            default:
                                break;
                        }
                        ?>


                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="5" style="alignment-adjust: central"><br>
            <p><input id="crear" class="submit_fieldset_autenticacion" type="button" onclick="return listaEstudiantes()" value="Ver"/></p>
        </td>
    </tr>
</table>