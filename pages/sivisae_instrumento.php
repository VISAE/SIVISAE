<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$est_id = base64_decode($_GET['st']);
$periodo = base64_decode($_GET['pa']);
$seg_id = isset($_GET['sg']) ? base64_decode($_GET['sg']) : 'n';
?>
<head>
    <?php
    $browser = getenv("HTTP_USER_AGENT");
    if (preg_match("/MSIE/i", "$browser")) {
        //Navegadores no compatibles
        ?>
        <script language="JavaScript" type="text/JavaScript">
            window.location = "sivisae_notifica.php?e=X01";
        </script>

        <?php
    } else {
        //Navegadores compatibles
        // Se valida inicio de sesion
        $sintilde = explode(',', SIN_TILDES);
        $tildes = explode(',', TILDES);
        if (!isset($_SESSION['usuarioid'])) {
            //Debe iniciar sesion
            header("Location: " . RUTA_PPAL . "pages/sivisae_notifica.php?e=X02");
        } else {
            $estudiante = mysql_fetch_array($consulta->traerEstudiante($est_id, $periodo));
            $cedula = $estudiante[0];
            $nombre = ucwords($estudiante[1]);
            $mail = $estudiante[2];
            $cead = ucwords(preg_replace($sintilde, $tildes, $estudiante[3]));
            $prog = ucwords(preg_replace($sintilde, $tildes, $estudiante[4]));
            $skype = $estudiante[5];
            $tel = $estudiante[6];
            $auditor = ucwords($estudiante[7]);
            $tipo_est = ucwords($estudiante[8]);
            $peraca = base64_encode($estudiante[9]);
            $caracterizacion = $estudiante[10];
            $convenio = ucwords(preg_replace($sintilde, $tildes, $estudiante[11]));
            $aud_id = $estudiante[12];
            $enc = base64_encode($cedula);
            $usr = base64_encode($_SESSION['ced']);
            $perf = base64_encode($_SESSION['perfilid']);
            include "../template/sivisae_link_home.php";
            $var_carac = "est='$enc' usr='$usr' perf='$perf' peraca='$peraca'";
            $estado = '0';
            ?>

            <!--contenedor-->
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <link href="js/iCheck/polaris/polaris.css" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <!--<link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />-->
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src='js/Chosen1.4/chosen.jquery.js' type='text/javascript' language='javascript'></script>
            <script src='js/Chosen1.4/chosen.jquery.min.js' type='text/javascript' language='javascript'></script>
            <script src='js/varios/script-cargar-archivos.js' type='text/javascript' language='javascript'></script>
            <script src='js/varios/script-enviar-seguimiento.js' type='text/javascript' language='javascript'></script>
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <link rel="stylesheet" href="js/Contador_TextArea/style.css">
            <link rel="stylesheet" href="js/knob/css/style.css">
            <script src="js/iCheck/icheck.js"></script>
            <link rel="stylesheet" href="js/Steps/css/normalize.css">
            <link rel="stylesheet" href="js/Steps/css/main.css">
            <link rel="stylesheet" href="js/Steps/css/jquery.steps.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link type="text/css" rel="stylesheet" href="js/qtip/jquery.qtip.css" />
            <script type="text/javascript" src="js/qtip/jquery.qtip.js"></script>

            <script src="js/Steps/jquery.steps.js"></script>

            <script type="text/javascript" language="javascript">
                $(document).ready(function () {
                    $('.grales').iCheck({
                        checkboxClass: 'icheckbox_polaris',
                        increaseArea: '-20%' // optional
                    });
                    $('.grales').on('ifChecked', function (event) {
                        agregar('gener', $(this).attr('value'));
                    })
                            .on('ifUnchecked', function () {
                                borrar('gener', $(this).attr('value'));
                            });
                    $(document).on('contextmenu', function (e) {
                        swal({
                            title: '¡Cuidado!',
                            text: 'El clic derecho esta deshabilitado en esta página',
                            type: 'error',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        return false;
                    });
                    $('.boton_aud_med').each(function () {
                        var id = $(this).attr('tp');
                        var seg = $(this).attr('sg');
                        var imp = $(this).attr('imp');
                        if (imp === 's') {
                            $(this).append("<img  width='20px' height='20px'  src='template/imagenes/generales/printer.png' />");
                            $(this).qtip({
                                content: {
                                    title: 'Observación E-mediador',
                                    text: $('#imprimir-' + seg)
                                },
                                hide: {
                                    fixed: true,
                                    delay: 300
                                },
                                style: {classes: 'qtip-tipped'},
                                position: {
                                    adjust: {
                                        scroll: true, // Can be ommited (e.g. default behaviour)
                                        x: -50
                                    }}
                            });
                        }
                    });
                    $('.carac').each(function () {
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
                                    data: {st: id, usr: usr, pf: perfil, pa: periodo},
                                    once: false // Re-fetch the content each time I'm shown
                                }
                            },
                            style: {classes: 'qtip-tipped'},
                            position: {
                                my: 'right top', // Position my top left...
                                at: 'center left', // at the bottom right of...
                                adjust: {
                                    scroll: true
                                }
                            }
                        });
                    });
                });

                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }

                function ShowHide(div) {
                    $('#' + div).animate({'height': 'toggle'}, {duration: 1000});

                    return false;
                }

                function cargarCurso(curso, seguimiento) {
                    $('#steps').animate({'margin': 'hide'}, {duration: 1000});
                    var data = new FormData();
                    data.append('est_id', $('#est_id').val());
                    data.append('periodo', $('#periodo').val());
                    data.append('aud_est_id', $('#aud_est_id').val());
                    data.append('mat_id', curso);
                    var ruta = 'src/instrumentoCB.php';
                    if (seguimiento !== 'n') {
                        ruta = 'src/instrumentoAuditadoCB.php';
                        data.append('segto_id', seguimiento);
                    }
                    //hacemos la petición ajax  
                    $.ajax({
                        url: ruta,
                        type: 'POST',
                        // Form data
                        //datos del formulario
                        data: data,
                        //necesario para subir archivos via ajax
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            //                            $('#spinner').show();
                            startLoad();
                        },
                        success: function (response) {
                            //                            $('#spinner').hide();
                            stopLoad();
                            $("#steps").html(response);
                            $('#steps').animate({'top': 'show'}, {duration: 2000});
                            $('#accion_preven_e').chosen({width: '300px', height: '100px', size: '3'});
                            $('#accion_correc_e').chosen({width: '300px', height: '100px', size: '3'});
                            $('#accion_preven_t').chosen({width: '300px', height: '100px', size: '3'});
                            $('#accion_correc_t').chosen({width: '300px', height: '100px', size: '3'});
                            document.getElementById('steps').scrollIntoView(true);
                            document.getElementById('inf_e-med-0').scrollIntoView(true);
                        }
                    });
                    return false;
                }

                function imprimirObservacion(seg, tipo) {
                    $.ajax({
                        url: "src/imprimirObs.php",
                        type: 'POST',
                        // Form data
                        //datos del formulario
                        data: "acc=g&seg_aud=" + seg + "&tp=" + tipo,
                        //necesario para subir archivos via ajax
                        beforeSend: function () {
                            //                            $('#spinner').show();
                            startLoad();
                        },
                        success: function (response) {
                            stopLoad();
                            //                            alert(response);
                            swal({
                                title: '¡Observación académica generada!',
                                text: "Descarque \n\ <a target='_blank' href='" + response + "' id='pdf' class='botones'>AQUI</a>",
                                type: 'success',
                                html: true,
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                    return false;
                }
            </script>
            <!--scripts de funcionalidad - fin-->

        </head>

        <body onload="nobackbutton();
              ">
            <!--Encabezado - Inicio-->
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->
            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>

                <div id="menu_cont">

                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <!--opciones inicio-->
                    <!--opciones fin-->
                </div>
                <div>
                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            SEGUIMIENTO A ESTUDIANTE 
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <br>
                            <div id="datos_auditor">
                                <input type="hidden" id="perf" name="perf" value="<?php echo $_SESSION['perfilid']; ?>"/>
                                <?php
                                $head = mysql_fetch_array($consulta->inicioSeg($est_id, $periodo));
                                $ced_aud = $head[0];
                                $nom_aud = $head[1];
                                $semana = $head[2];
                                $semana_fin = $head[3];
                                $cead_aud = $head[4];
                                $zona_aud = $head[5];
                                $segtos = $head[6];
                                $aud_est_id = $head[7];

                                $th = "";
                                $td = "";
                                if (isset($_SESSION["perfilid"]) && $_SESSION["perfilid"] != '2') {
                                    $th = "<th class='tcar-qa4j'>Auditor:</th>";
                                    $td = "<td class='tcar-xitf'>$ced_aud - $nom_aud</td>";
                                }
                                $todayh = getdate(); //monday week begin reconvert
                                $d = $todayh['mday'];
                                $m = $todayh['mon'];
                                $y = $todayh['year'];
                                ?>
                                <table class="tcar" >
                                    <tr class="bordes">
                                        <?php echo $th; ?>
                                        <th class="tcar-qa4j">Semana:</th>
                                        <th class="tcar-qa4j">Finalización:</th>
                                        <th class="tcar-qa4j">Zona:</th>
                                        <th class="tcar-qa4j">CEAD:</th>
                                    </tr>
                                    <tr class="bordes">
                                        <?php echo $td; ?>  
                                        <td class="tcar-xitf"><?php echo $semana; ?></td>
                                        <td class="tcar-xitf"><?php echo $semana_fin; ?></td>
                                        <td class="tcar-xitf"><?php echo $zona_aud; ?></td>
                                        <td class="tcar-xitf"><?php echo $cead_aud; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <div align="center">
                                                <br>
                                                <div class="bordes" id="datos_est" >
                                                    <table class="bordes" style="width: 900px;">
                                                        <tr>
                                                            <td rowspan="3" style="background-color: #004669">
                                                                <!--<div class="art-postcontent">-->
                                                                <div align="center" style="background-color: #004669; width: 210px">
                                                                    <h2 id='p_fieldset_autenticacion_2'>
                                                                        Datos Estudiante 
                                                                    </h2>
                                                                </div>
                                                                <input type="hidden" id="est_id" name="est_id" value="<?php echo $est_id; ?>">
                                                                <input type="hidden" id="periodo" name="periodo" value="<?php echo $periodo; ?>">
                                                                <input type="hidden" id="aud_est_id" name="aud_est_id" value="<?php echo $aud_est_id; ?>">
                                                                <input type='hidden' id='seg_id' name='seg_id' value='<?php echo $seg_id; ?>'>
                                                                <input type='hidden' id='aud_id' name='aud_id' value='<?php echo $aud_id; ?>'>
                                                            </td>
                                                            <!--                                                                </tr>
                                                                                                                            <tr>-->
                                                            <td  style="vertical-align: top; ">
                                                                <div class="bordes" style="text-align:center;">
                                                                    <table class="tcar" style="margin: 0 auto; width: 345px">
        <!--                                                                    <colgroup>
                                                                        <col style="width: 119px">
                                                                        <col style="width: 150px">
                                                                    </colgroup>-->
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Cédula:</th>
                                                                            <td class="tcar-xitf"><?php echo $cedula; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Programa:</th>
                                                                            <td class="tcar-xitf"><?php echo $prog; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Correo electrónico:</th>
                                                                            <td class="tcar-xitf"><?php echo $mail; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Teléfono:</th>
                                                                            <td class="tcar-xitf"><?php echo $tel; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Convenio:</th>
                                                                            <td class="tcar-xitf"><?php echo $convenio; ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                            <td style="vertical-align: top;">
                                                                <div class="bordes">
                                                                    <table class="tcar" style="margin: 0 auto; width: 345px">
        <!--                                                                    <colgroup>
                                                                            <col style="width: 119px">
                                                                            <col style="width: 150px">
                                                                        </colgroup>-->
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Nombre:</th>
                                                                            <td class="tcar-xitf"><?php echo $nombre; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">CEAD:</th>
                                                                            <td class="tcar-xitf"><?php echo $cead; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Skype:</th>
                                                                            <td class="tcar-xitf"><?php echo $skype; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Tipo:</th>
                                                                            <td class="tcar-xitf"><?php echo $tipo_est; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="tcar-qa4j">Caracterización:</th>
                                                                            <td class="tcar-xitf carac" <?php echo $var_carac; ?> ><?php echo $caracterizacion; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        if ($seg_id !== 'n') {
                                                                            $estado = $consulta->estadoSeguimiento($seg_id);
                                                                            if ($estado === '2') {
                                                                                echo "<tr>
                                                                                                        <td colspan='2' class='tcar-xitf'>
                                                                                                            <a class='botones' href='#' onclick='return imprimirObservacion(\"$seg_id\",\"e\")'>Imprimir</a>
                                                                                                            <img  width='40px' height='40px'  src='template/imagenes/generales/estudiante_unad.jpg' />
                                                                                                        </td>
                                                                                                    </tr>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                                <div id="cursos" style="text-align: center;">
                                                                    <table>
                                                                        <?php
                                                                        //                                                                              echo "$est_id, $periodo";
                                                                        $cursos = $consulta->materiasEstByPeriodo($est_id, $periodo, $seg_id, "");
                                                                        $cant = mysql_num_rows($cursos);
                                                                        $filas = ceil($cant / 3);
                                                                        for ($i = 1; $i <= $filas; $i++) {
                                                                            echo "<tr>";
                                                                            for ($j = 1; $j <= 3; $j++) {
                                                                                $row = mysql_fetch_array($cursos);
                                                                                if (!empty($row)) {
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
                                                                                    if ($class === 'boton_aud_med') {
                                                                                        if (mysql_num_rows($consulta->observacionesAcad($segto_id, 't')) > 0) {
                                                                                            $imp = "imp='s'";
                                                                                            echo "<div id='imprimir-$segto_id' style='display: none'>
                                                                                                    <a class='botones' href='#' onclick='return imprimirObservacion(\"$segto_id\",\"t\")'>Imprimir</a>
                                                                                                    <img src='template/imagenes/generales/printer.png' />
                                                                                                </div>";
                                                                                        }
                                                                                    }

                                                                                    if ($row[5] > FECHA_SEG_ITERACIONES) {
                                                                                        $iteracion_curso = " <br> Iteración: " . $row[7];
                                                                                    } else {
                                                                                        $iteracion_curso = "";
                                                                                    }

                                                                                    //echo "<td><a $disable class='tipo $class' $imp sg='$segto_id' tp='$nov'>" . $mat . " - " . $consulta->descripcionNovedad($nov) . "- $seg_id - $row[6]</a>$fecha_seg</td>";
                                                                                    echo "<td><a $disable class='tipo $class' $imp sg='$segto_id' tp='$nov'>" . $mat . " <br> Estado: " . $consulta->descripcionNovedad($nov) . " " . $iteracion_curso . "</a>$fecha_seg</td>";
                                                                                }
                                                                            }
                                                                            echo "</tr>";
                                                                        }
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        $generales = $consulta->getGenerales();
                                                        $fil = ceil(mysql_num_rows($generales) / 3);
                                                        echo "<tr>
                                                                                <td colspan='2' align='center'>
                                                                                    <table class='bordes'>
                                                        <tr>
                                                            <td class='bordes' rowspan='$fil' style='background-color: #004669'>
                                                                        <h2 id='p_fieldset_autenticacion_2'>
                                                                            Generalidades
                                                                        </h2>
                                                                    </td>";
                                                        $i = 0;
                                                        $selec = array();
                                                        if ($seg_id !== 'n') {
                                                            $selec = $consulta->getGeneralidades($seg_id);
                                                        }
                                                        while ($row = mysql_fetch_array($generales)) {
                                                            $value = $row[0];
                                                            $checked = "";
                                                            if (count($selec) > 0 && in_array($value, $selec)) {
                                                                $checked = "checked";
                                                            }
                                                            echo "<td><label><input type='checkbox' $checked class='grales' value='$value' name='generales[]' id='generales-$value'>$row[1]</label></td>";
                                                            $i++;
                                                            if ($i === 3) {
                                                                echo "</tr><tr>";
                                                            }
                                                        }
                                                        echo "</tr>
                                                                                            <input type='hidden' id='gener' name='gener' value='" . implode(",", $selec) . "'>
                                                                                            </table>
                                                                                            </td>
                                                        </tr>";
                                                        ?>

                                                    </table>
                                                </div>
                                            </div>                                                                  
                                            </div>                                                                    
                                        </td>
                                    </tr>
                                </table>
                                <div id="spinner" align="center" style="display:none;">
                                    <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                </div>
                                <div id="steps" style="display: none; overflow: auto"></div
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php
            //Pie de pagina
            include "../template/sivisae_footer_home.php";
            ?>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
