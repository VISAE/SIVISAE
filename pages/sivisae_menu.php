<script src="js/varios/notificar.js" type="text/javascript"></script>
<script src="js/growl/jquery.growl.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
<link rel="stylesheet" type="text/css" href="js/growl/jquery.growl.css">
<link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
<link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
<link rel="stylesheet" href="template/popup/style.min.css">
<script src="js/popup/bpopup.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
<script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
<script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
<script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
<link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
<script src="js/popup/bpopup.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
<script type="text/javascript" languaje="javascript">
    $(document).ready(function () {
        cargar_push();
    });
<?php
if ($_SESSION['perfilid'] !== '2' && $_SESSION['perfilid'] !== '5' && $_SESSION['perfilid'] !== '9') {
    ?>
        function activarpopupNotif() {
            // Crear
            //        $('#btn_notif').bind('click', function (e) {
            //            e.preventDefault();
            $("#func_not").chosen({width: "200px"});
            $("#tp_notif").chosen({width: "200px"});
            var form = document.directorio;
            limpiaForm(form);
            $('#result_el').html('');
            $("#func_not").chosen({width: "200px"});
            $("#tp_notif").chosen({width: "200px"});
            $('#popup_notif').bPopup();
            //        });
        }


        function activarpopupCalendario() {
            $('#popup_calendario').bPopup();
        }


        function submitFormEnviarNotif() {
            $('#btn_submit').attr("disabled", true);
            $("#spinner").show();
            var form = document.notificacion;
            var dataString = $(form).serialize();
            $.ajax({
                type: 'POST',
                url: 'src/notificacionesCB.php',
                data: dataString,
                beforeSend: function () {
                },
                success: function (data) {
                    limpiaForm(form);
                    $('#btn_submit').attr("disabled", false);
                    $("#spinner").hide();
                    $("#func_not").chosen({width: "200px"});
                    $("#tp_notif").chosen({width: "200px"});
                    swal("¡Notificación enviada!", "", "success");
                }
            });
            return false;
        }
<?php } ?>
</script>

<div id="cssmenu" class="align-center">
    <ul>
        <?php
        $usuarioid = $_SESSION['usuarioid'];
        $menu = $consulta->menu($usuarioid);
        $not = $consulta->getCantNotificaciones($usuarioid);
        while ($row = mysql_fetch_array($menu)) {
            $menuid = $row[0];
            $descripcion = $row[1];
            $dashboard = $row[2];

            echo "<li class='has-sub' ><a href='$dashboard'><span>$descripcion</span></a>"
            . "<ul>";
            $opciones = $consulta->opciones($usuarioid, $menuid);
            while ($row1 = mysql_fetch_array($opciones)) {
                $opcion = $row1[0];
                $url = $row1[1];
                $id_opcion = $row1[2];

                if ($id_opcion == 22) {
                    echo "<li class='last' ><a href='" . RUTA_PPAL . $url . "&op=" . $id_opcion . "'><span >$opcion</span></a></li>";
                } else {
                    echo "<li class='last' ><a href='" . RUTA_PPAL . $url . "?op=" . $id_opcion . "'><span >$opcion</span></a></li>";
                }
            }
            echo '</ul></li>';
        }
        if ($not > 0) {
            echo "<li class='last_not' id='a_notif' ><a href='" . RUTA_PPAL . "pages/sivisae_notificaciones.php'><span id='notificaciones' style='color:red; font-size: 15px' >☎ ($not)</span></a></li>";
        } else {
            echo "<li class='last' id='a_notif'><a href='" . RUTA_PPAL . "pages/sivisae_notificaciones.php'><span id='notificaciones' style='color:#777777; font-size: 15px' >☎</span></a></li>";
        }
        ?>

    </ul>

</div>
<a class="notificaciones" id="btn_notif" onclick="return activarpopupNotif()">
    <img src="template/imagenes/generales/crm-notifications-icons.png" /><span>Notificar</span>
</a>


<a class="calendario" id="btn_calendario" onclick="return activarpopupCalendario()">
    <img src="template/imagenes/generales/calendario.png" /><span>Calendario Académico</span>
    <div style="align: right;background-color: #E8E8E8; margin-left: 60px; height: 600px; overflow-y: scroll;">

        <div align="right" style="width: 95%; margin-left: 5px; margin-right: 5px; margin-bottom: 5px;">

            <?php
            //Consulta el listado
            //Fecha Actual
            $fechaActual = $consulta->obtenerFechaServer(1);
            ?>

            <div align="center" >
                <h2 id='p_fieldset_autenticacion_3'>
                    <?php echo $fechaActual; ?>
                </h2>
            </div>

            <div align="center" style="background-color: #E26C0E" >
                <h2 id='p_fieldset_autenticacion_4'>
                    Eventos del día
                </h2>
            </div>


            <?php
            $eventosCalendario = $consulta->traerEventosCalendario();
            //Eventos del calendario
            $contE = 0;

            while ($rowEC = mysql_fetch_array($eventosCalendario)) {
                ?><h2 id='p_calendario_11'><?php echo $rowEC[1]; ?></h2><?php
                ?><h2 id='p_calendario_2'><?php echo $rowEC[2]; ?></h2><?php
                $contE++;
            }

            if ($contE == 0) {
                ?>
                <h2 id='p_calendario_13'>
                    No hay eventos para hoy.
                </h2>
                <?php
            }
            ?>

            <div align="center" style="background-color: #4bc14e" >
                <h2 id='p_fieldset_autenticacion_4'>
                    Cumpleaños
                </h2>
            </div>
            <?php
            $cumpleañosCalendario = $consulta->traerCumpeañosCalendario();
            //Eventos del calendario
            $contC = 0;
            while ($rowCC = mysql_fetch_array($cumpleañosCalendario)) {
                $contC++;
                if ($contC == 1) {
                    ?><h2 id='p_calendario_13'>En este día a...</h2><?php
                }
                ?><h2 id='p_calendario_12'><?php echo $rowCC[0] . ' - ' . $rowCC[1]; ?></h2><?php
            }
            if ($contC == 0) {
                ?>
                <h2 id='p_calendario_13'>
                    No hay cumpleaños para hoy.
                </h2>
                <?php
            } else {
                ?><h2 id='p_calendario_13'>La Vicerrectoría de Servicios a Aspirantes estudiante y Egresados – VISAE – les desea un feliz cumpleaños.</h2><?php
            }
            ?>
        </div>
    </div>
</a>

<?php
if ($_SESSION['perfilid'] !== '2' && $_SESSION['perfilid'] !== '5') {
    ?>
    <div id="popup_notif" style="display: none">
        <span class="button_cerrar b-close"></span>
        <div align="center" style="background-color: #004669" >
            <h2 id='p_fieldset_autenticacion_2'>
                Envío de Notificaciones
            </h2>
        </div>
        <div  class="art-postcontent">
            <div align="center">
                <form id="notificacion" name="notificacion" method="post" onsubmit="return submitFormEnviarNotif()">
                    <input type="hidden" id="accion" value="enviar" name="accion" >
                    <div style="background-color: #E8E8E8" >
                        <table style="width: 400px">
                            <tr>
                                <td><label for="func_not">Funcionario (*):</label></td>
                                <td>
                                    <select data-placeholder="Seleccione"  name='func_not' id='func_not' style="width: 200px;" tabindex='2'>";
                                        <option value=''></option>
                                        <?php
                                        $funcionarios = $consulta->funcionarios($usuarioid);
                                        while ($row1 = mysql_fetch_array($funcionarios)) {
                                            $aud_id = $row1[0];
                                            $aud_nombre = ucwords($row1[1]);
                                            $gen = $row1[2];
                                            echo "<option value='$aud_id'>";
                                            echo $aud_nombre . " - " . $gen;
                                            echo "</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="tp_notif">Tipo Notificación</label></td>
                                <td>
                                    <select data-placeholder="Seleccione"  name='tp_notif' id='tp_notif' style="width: 200px;">";
                                        <option value=''></option>
                                        <option value='r'>Recomendación</option>
                                        <option value='c'>Corrección</option>
                                        <option value='a'>Aviso</option>
                                        <option value='m'>Memorando</option>
                                        <option value='f'>Felicitación</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="cedula_e">Notificación</label></td>
                                <td><textarea style="width: 180px;" rows="3" id="txt_notif" name="txt_notif" required="Escriba el mensaje a enviar" maxlength="200"></textarea></td>  
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <p><input id="btn_submit" class="submit_fieldset_autenticacion" type="submit" value="Enviar"/></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>                  
            </div>
        </div>
    </div>
<?php } ?>

<div id="popup_calendario" style="display: none">
    <span class="button_cerrar b-close"></span>
    <div align="center" style="background-color: #004669" >
        <h2 id='p_fieldset_autenticacion_2'>
            Calendario Académico
        </h2>
    </div>
    <div  class="art-postcontent">
        <div align="center">
            <iframe src="pages/sivisae_calendario.php" height="620" width="310" scrolling="no" frameborder="0"></iframe>
        </div>
    </div>
</div>

