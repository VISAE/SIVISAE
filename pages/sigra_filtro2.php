<?php

/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */

include_once '../config/sigra_class.php';
$consulta = new sigra_consultas();
$proyectos = $consulta->filtros("proyecto");
$eventos = $consulta->filtros("evento");
$lineas = $consulta->filtros("linea");
$coberturas = $consulta->filtros("cobertura");
$pf = $_SESSION['perfilid'];
?>

<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        $(".chosen-select-deselect").chosen({allow_single_deselect: true});
        $(".chosen-select").chosen({no_results_text: "No se encontraron registros!"});
//        $('#zona, #escuela').chosen().change(function () {
//            filtros($(this).prop("id").toLowerCase());
//        });
    });

//    function filtros(cual) {
//        var dataString = {
//            "select": cual,
//            "valores": $("#" + cual).val()
//        };
//        $.ajax({
//            type: 'POST',
//            url: 'src/filtrosCB.php',
//            data: dataString,
//            success: function (data) {
//                if (data !== null) {
//                    if (cual === 'zona') {
//                        $("#cead").hide();
//                        $(".f").html(data);
//                        $(".chosen-select").chosen();
//                    }
//                    if (cual === 'escuela') {
//                        $("#programa").hide();
//                        $('.e').html(data);
//                        $(".chosen-select").chosen();
//                    }
//                } else {
//                    $("#cead").show();
//                    $("#programa").show();
//                }
//            }
//
//        });
//    }
</script>

<table>
    <tr>
        <td >
            Proyecto:
            <select id="proyecto" name="proyecto[]" data-placeholder="Seleccione..." class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysqli_fetch_array($proyectos)) {
                    echo "<option value='$row[0]'>" .
                    ucwords($row[1]) .
                    "</option>";
                }
                ?>
            </select>
        </td>
        <td >
            <div>
                Evento:
                <select id="evento" name="evento[]" data-placeholder="Seleccione..." class="chosen-select" multiple style="width:180px;" tabindex="4">
                    <option value=""></option>
                    <?php
                    while ($row = mysqli_fetch_array($eventos)) {
                        echo "<option value='$row[0]'>" .
                        ucwords($row[1]) .
                        "</option>";
                    }
                    ?>
                </select>
            </div>
        </td>
        <td>
            Linea:
            <select id="linea" name="linea[]" data-placeholder="Seleccione..."   class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysqli_fetch_array($lineas)) {
                    echo "<option value='$row[0]'>" .
                    ucwords($row[1]) .
                    "</option>";
                }
                ?>
            </select>
        </td>
        <td >
            Cobertura:
            <select id="cobertura" name="cobertura[]" data-placeholder="Seleccione..." class="chosen-select" multiple style="width:180px;" tabindex="4">
                <option value=""></option>
                <?php
                while ($row = mysqli_fetch_array($coberturas)) {
                    echo "<option value='$row[0]'>" .
                    ucwords($row[1]) .
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
                            <select class="chosen-select" name='registros' id='registros' style="width: 60px;" tabindex='2'>";
                                <option value='50'>50</option>
                                <option value='100'>100</option>
                                <option value='300'>300</option>
                            </select>
                        </td>
                        <td>
                            Buscar: 
                            <input type="text" id="buscar" name="buscar" />
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="5" style="alignment-adjust: central"><br>
            <p><input id="crear" class="submit_fieldset_autenticacion" type="button" onclick="return listaGraduados()" value="Ver"/></p>
        </td>
    </tr>
</table>