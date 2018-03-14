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
            $enc = base64_encode($cedula);
            $usr = base64_encode($_SESSION['ced']);
            $perf = base64_encode($_SESSION['perfilid']);
            include "../template/sivisae_link_home.php";
            $var_carac = "est='$enc' usr='$usr' perf='$perf' peraca='$peraca'";
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
                    $('.tipo').each(function () {
                        var id = $(this).attr('tp');
                        var tipo = '';
                        switch (id) {
                            case 'A':
                                tipo = 'Curso Activo';
                                break;
                            case 'B':
                                tipo = 'Cancelación Semestre';
                                break;
                            case 'C':
                                tipo = 'Cancelación Materia';
                                break;
                            case 'D':
                                tipo = 'Aplazamiento Materia';
                                break;
                            case 'F':
                                tipo = 'Aplazamiento de Semestre';
                                break;
                            case 'N':
                                tipo = 'Eliminación Curso';
                                break;
                        }
                        $(this).qtip({
                            content: {
                                title: 'Estado del Curso',
                                text: id + ': ' + tipo
                            },
                            style: {classes: 'qtip-tipped'},
                            position: {
                                adjust: {
                                    scroll: true, // Can be ommited (e.g. default behaviour)
                                    x: -50
                                }}
                        });
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

                ///Solicitud Eliminar
                function submitFormSolicitudEliminar() {
                    if ($('#observacion_eliminacion').val() !== '') {
                        $('#btn_submit').attr("disabled", true);
                        var form = document.eliminar_seguimiento;
                        var dataString = $(form).serialize();

                        $.ajax({
                            type: 'POST',
                            url: 'src/solicitud_eliminar_seguimientoCB.php',
                            data: dataString,
                            success: function (data) {
                                $('#btn_submit').attr("disabled", false);
                                document.getElementById("observacion_eliminacion").value = "";

                                if (data === "1")
                                {
                                    swal({
                                        title: 'La solicitud ha sido generada.',
                                        text: '',
                                        type: 'success',
                                        confirmButtonColor: '#004669',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }

                                if (data === "2")
                                {
                                    swal({
                                        title: 'Ya existe una solicitud para la eliminación de este seguimiento.',
                                        text: '',
                                        type: 'error',
                                        confirmButtonColor: '#004669',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }

                                if (data === "3")
                                {
                                    swal({
                                        title: 'La solicitud no se ha podido generar, por favor intente nuevamente.',
                                        text: '',
                                        type: 'error',
                                        confirmButtonColor: '#004669',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }


                            }
                        });
                        return false;

                    } else
                    {
                        swal({
                            title: 'Por favor ingrese la razón por la cual desea eliminar el seguimiento. Máximo 100 caracteres.',
                            text: '',
                            type: 'error',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        $('#observacion_eliminacion').focus();
                        return false;
                    }
                }

                function limpiaForm(miForm) {
                    // recorremos todos los campos que tiene el formulario
                    $(':input', miForm).each(function () {
                        var type = this.type;
                        var tag = this.tagName.toLowerCase();
                        //limpiamos los valores de los campos…
                        if (type === 'text' || type === 'password' || tag === 'textarea')
                            this.value = "";
                        // excepto de los checkboxes y radios, le quitamos el checked
                        // pero su valor no debe ser cambiado
                        else if (type === 'checkbox' || type === 'radio')
                            this.checked = false;
                        // los selects le ponesmos el indice a -
                        else if (tag === 'select')
                        {
                            this.selectedIndex = 0;
                            $(this).chosen('destroy');
                            $(this).prop('selectedIndex', 0);
                        }
                    });
                }

                //Loader
                function startLoad() {
                    $("#dynElement").introLoader({
                        animation: {
                            name: 'simpleLoader',
                            options: {
                                stop: false,
                                fixed: false,
                                exitFx: 'fadeOut',
                                ease: "linear",
                                style: 'light'
                            }
                        },
                        spinJs: {
                            lines: 13, // The number of lines to draw 
                            length: 20, // The length of each line 
                            width: 10, // The line thickness 
                            radius: 30, // The radius of the inner circle 
                            corners: 1, // Corner roundness (0..1) 
                            color: '#004669', // #rgb or #rrggbb or array of colors 
                        }
                    });
                }
                function stopLoad() {
                    var loader = $('#dynElement').data('introLoader');
                    loader.stop();
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
                            SOLICITUD ELIMINAR SEGUIMIENTO A ESTUDIANTE 
                        </h2>
                    </div>
                    <div class="art-postcontent">
                        <div align="center">
                            <br>
                            <div id="datos_auditor">
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

                                    <form id="eliminar_seguimiento" name="eliminar_seguimiento" method="post" onsubmit="return submitFormSolicitudEliminar()" action="src/solicitud_eliminar_seguimientoCB.php">
                                        <tr class="bordes2">
                                            <th colspan="5" class="tcar-qa4j2">Confirmación eliminar seguimiento</th>
                                        </tr>
                                        <tr class="bordes2">
                                            <th colspan="5" class="tcar-xitf2">Estimado usuari@, por favor confirme sí realmente desea eliminar este seguimiento. Recuerde que su solicitud quedará en estado pendiente para que sea realizada por el administrador del sistema. A continuación ingrese la razón de la eliminación y de clic en CONFIRMAR.</th>
                                        </tr>
                                        <tr class="bordes2">
                                            <th colspan="5" class="tcar-xitf2">
                                                <textarea style="width: 60%; overflow:auto; resize:none; " id="observacion_eliminacion" name="observacion_eliminacion" type="text" rows="2" cols="100" maxlength="200"></textarea>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="alignment-adjust: central"><br>
                                                <input id="crear" class="delete_fieldset_autenticacion" type="submit" value="Confirmar"/>
                                                <input type="hidden" id="est_id" name="est_id" value="<?php echo $est_id; ?>">
                                                <input type="hidden" id="periodo" name="periodo" value="<?php echo $periodo; ?>">
                                                <input type="hidden" id="aud_est_id" name="aud_est_id" value="<?php echo $aud_est_id; ?>">
                                                <input type='hidden' id='seg_id' name='seg_id' value='<?php echo $seg_id; ?>'>
                                            </td>
                                        </tr>
                                    </form>


                                    <tr class="bordes2">
                                        <th colspan="5" class="tcar-qa4j2">Detalle del seguimiento a eliminar</th>
                                    </tr>

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
                                                            <td rowspan="2" style="background-color: #004669">
                                                                <div class="art-postcontent">
                                                                    <div align="center" style="background-color: #004669; width: 210px">
                                                                        <h2 id='p_fieldset_autenticacion_2'>
                                                                            Datos Estudiante 
                                                                        </h2>
                                                                    </div>

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
                                                                                    $class = $seg_id != 'n' ? $row[2] : "botones";
                                                                                    $segto_id = $seg_id != 'n' ? $row[3] : 'n';
                                                                                    $nov = $row[4]; // !== 'A' ? '*' : '';

                                                                                    echo "<td class='tipo $class' tp='$nov'>" . $mat . " - $nov</td>  ";
                                                                                }
                                                                            }
                                                                            echo "</tr>";

                                                                            echo "<tr>";
                                                                            echo "<td colspan='3'></br><td>";
                                                                            echo "</tr>";
                                                                        }
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
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
