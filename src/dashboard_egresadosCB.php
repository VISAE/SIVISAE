<?php
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$onload = "";
?>
<!--Dash de conteos - inicio-->
<table  id="tb_conteos" width="100%"  cellspacing='0'>
    <tr width="100%">
        <td width="20%" align="center">
            <div class="div_subtext_count_one" >
                <img src="template/imagenes/dash/img_count_1.png" width="50" height="50" />
            </div>
            <div class="div_text_count_one">
                <?php echo number_format($consulta->conteoTitulos(), 0); ?>    
            </div>
            <div class="div_subtext_count_one">
                Títulos entregados
            </div>
        </td>
        <td width="20%" align="center">
            <div class="div_subtext_count_two" >
                <img src="template/imagenes/dash/img_count_2.png" width="50" height="50" />
            </div>
            <div class="div_text_count_two">
                <?php echo number_format($consulta->conteoPersonas(), 0); ?>    
            </div>
            <div class="div_subtext_count_two">
                Personas Egresadas
            </div>
        </td>
        <td width="20%" align="center">
            <div class="div_subtext_count_three" >
                <img src="template/imagenes/dash/img_count_3.png" width="50" height="50" />
            </div>
            <div class="div_text_count_three">
                <?php echo number_format($consulta->conteoTelefonos(), 0); ?>    
            </div>
            <div class="div_subtext_count_three">
                Egresados con Teléfono
            </div>
        </td>
        <td width="20%" align="center">
            <div class="div_subtext_count_four" >
                <img src="template/imagenes/dash/img_count_4.png" width="50" height="50" />
            </div>
            <div class="div_text_count_four">
                <?php echo number_format($consulta->conteoDireccion(), 0); ?>    
            </div>
            <div class="div_subtext_count_four">
                Egresados con Dirección
            </div>
        </td>
        <td width="20%" align="center">
            <div class="div_subtext_count_five" >
                <img src="template/imagenes/dash/img_count_5.png" width="50" height="50" />
            </div>
            <div class="div_text_count_five">
                <?php echo number_format($consulta->conteoEmail(), 0); ?>    
            </div>
            <div class="div_subtext_count_five">
                Egresados con Email
            </div>
        </td>
    </tr>
</table>
<!--Dash de conteos - fin-->

<!--Dash de Datas - inicio-->
<br>

<table  id="tb_conteos" width="100%"  cellspacing='0'>

    <tr width="100%">
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Zona
            </div>
        </td>
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Área Geográfica
            </div>
        </td>
    </tr>
    <tr width="100%">
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder" style="width:100%">
                <canvas id="grafico_zona_egresados" />
            </div>
            <?php
            echo $consulta->DashCrearBar("bar_zona_egresados", $consulta->estructurarDataGrafico($consulta->EgresadosXZona(), 2), "Zona");
            $onload.=$consulta->CrearOnload("grafico_zona_egresados", 1, "bar_zona_egresados", "bar");
            ?>
        </td>
        <td width="33.3%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_10" style="width:80%">
                <canvas id="grafico_areageo_egresados" />
            </div>
            <?php
            echo $consulta->DashCrearDonut("donut_areageo_egresados", $consulta->estructurarDataGrafico($consulta->AreaGeografica(), 3), "Área Geográfica");
            $onload.=$consulta->CrearOnload("grafico_areageo_egresados", 3, "donut_areageo_egresados", "");
            ?>
        </td>
    </tr>
    <tr>
        <td width="50%" align="left" colspan='2'>
            <div class="div_text_data_head">
                Centro
            </div>
        </td>
    </tr>
    <tr>
        <td width="100%" align="center" class='div_text_data_body' colspan="2">
            <div id="canvas-holder_2" style="width:100%">
                <canvas id="grafico_centro_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearBar("bar_centro_egresados", $consulta->estructurarDataGrafico($consulta->EgresadosXCentro(), 2), "Centro - Top 15");
            $onload.=$consulta->CrearOnload("grafico_centro_egresados", 1, "bar_centro_egresados", "horizontalBar");
            ?>
        </td>
    </tr>

    <tr width="100%">
        <td width="50%" align="left" colspan="2">
            <div class="div_text_data_head">
                Escuela
            </div>
        </td>
    </tr>
    <tr>
        <td width="100%" align="center" class='div_text_data_body' colspan="2">
            <div id="canvas-holder_3" style="width:70%">
                <canvas id="grafico_escuela_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearBarMultiColor("bar_escuela_egresados", $consulta->estructurarDataGrafico($consulta->EgresadosXEscuela(), 1), "Escuela");
            $onload.=$consulta->CrearOnload("grafico_escuela_egresados", 1, "bar_escuela_egresados", "bar");
            ?>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" colspan="2">
            <div class="div_text_data_head">
                Programa
            </div>
        </td>
    </tr>

    <tr>
        <td width="100%" align="center" class='div_text_data_body' colspan="2">
            <div id="canvas-holder_4" style="width:100%">
                <canvas id="grafico_programa_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearBar("bar_programa_egresados", $consulta->estructurarDataGrafico($consulta->EgresadosXPrograma(), 2), "Programa - Top 10");
            $onload.=$consulta->CrearOnload("grafico_programa_egresados", 1, "bar_programa_egresados", "horizontalBar");
            ?>
        </td>
    </tr>

    <tr width="100%">
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Nivel Académico
            </div>
        </td>
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Egresados Nivel de Formación
            </div>
        </td>
    </tr>
    <tr width="100%">
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_5"  style="width:80%">
                <canvas id="grafico_nivelaca_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearDonut("donut_nivelaca_egresados", $consulta->estructurarDataGrafico($consulta->EgresadosXNivelAcademico(), 3), "Nivel Académico");
            $onload.=$consulta->CrearOnload("grafico_nivelaca_egresados", 3, "donut_nivelaca_egresados", "");
            ?>
        </td>
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_6" style="width:100%">
                <canvas id="grafico_nivelfor_egresados" />
            </div>
            <?php
            echo $consulta->DashCrearBar("bar_nivelfor_egresados", $consulta->estructurarDataGrafico($consulta->EgresadosXNivelFormacion(), 2), "Nivel de Formación");
            $onload.=$consulta->CrearOnload("grafico_nivelfor_egresados", 1, "bar_nivelfor_egresados", "horizontalBar");
            ?>
        </td>
    </tr>
    <tr width="100%">
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Situación Laboral
            </div>
        </td>
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Género
            </div>
        </td>
    </tr>
    <tr width="100%">
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_7" style="width:90%">
                <canvas id="grafico_situlab_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearPie("pie_sitlab_egresados", $consulta->estructurarDataGrafico($consulta->SituacionLaboral(), 3), "Situación Laboral");
            $onload.=$consulta->CrearOnload("grafico_situlab_egresados", 2, "pie_sitlab_egresados", "");
            ?>
        </td>
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_8" style="width:100%">
                <canvas id="grafico_genero_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearBarMultiColor("bar_genero_egresados", $consulta->estructurarDataGrafico($consulta->Genero(), 1), "Género");
            $onload.=$consulta->CrearOnload("grafico_genero_egresados", 1, "bar_genero_egresados", "bar");
            ?>
        </td>
    </tr>
    <tr width="100%">
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Relación Trabajo - Programa Estudiado
            </div>
        </td>
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Egresados Cargos
            </div>
        </td>
    </tr>
    <tr width="100%">
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_7" style="width:90%">
                <canvas id="grafico_reltrab_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearBar("bar_reltrab_egresados", $consulta->estructurarDataGrafico($consulta->RelacionPrograma(), 2), "Relación Trabajo");
            $onload.=$consulta->CrearOnload("grafico_reltrab_egresados", 1, "bar_reltrab_egresados", "horizontalBar");
            ?>
        </td>
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_8" style="width:100%">
                <canvas id="grafico_egrecargo_egresados"></canvas>
            </div>
            <?php
            echo $consulta->DashCrearBarMultiColor("bar_egrecargo_egresados", $consulta->estructurarDataGrafico($consulta->CargoEgresados(), 2), "Género");
            $onload.=$consulta->CrearOnload("grafico_egrecargo_egresados", 1, "bar_egrecargo_egresados", "horizontalBar");
            ?>
        </td>
    </tr>
</table>

<table  id="tb_conteos" width="100%"  cellspacing='0'>
    <tr width="100%">
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Estrato
            </div>
        </td>
        <td width="50%" align="left">
            <div class="div_text_data_head">
                Vigencia
            </div>
        </td>
    </tr>
    <tr width="100%">
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_9" style="width:100%">
                <canvas id="grafico_estrato_egresados" />
            </div>
            <?php
            echo $consulta->DashCrearBar("pie_estrato_egresados", $consulta->estructurarDataGrafico($consulta->Estrato(), 2), "Estrato");
            $onload.=$consulta->CrearOnload("grafico_estrato_egresados", 1, "pie_estrato_egresados", "horizontalBar");
            ?>
        </td>
        <td width="50%" align="center" class='div_text_data_body'>
            <div id="canvas-holder_11" style="width:100%">
                <canvas id="grafico_vigencia_egresados" />
            </div>
            <?php
            echo $consulta->DashCrearBar("pie_vigencia_egresados", $consulta->estructurarDataGrafico($consulta->Vigencia(), 2), "Estado Vigencia");
            $onload.=$consulta->CrearOnload("grafico_vigencia_egresados", 1, "pie_vigencia_egresados", "horizontalBar");
            ?>
        </td>
    </tr>
</table>

<?php
// Se dibujan los gráficos
echo '<script>
        window.onload = function () { ' . $onload . ' };
    </script>';
?>

<!--Dash de Datas - fin-->