<?php
session_start();
include_once '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
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

        if (!isset($_SESSION['usuarioid'])) {
            //Debe iniciar sesion
            header("Location: " . RUTA_PPAL . "pages/sivisae_notifica.php?e=X02");
        } else {

            //Se configuran los permisos (crear, editar, eliminar)
            $modulo = $_GET["op"];

            if ($modulo != "") {
                $copy = 0;
                $edit = 0;
                $delete = 0;
                $permisos = $consulta->permisos($modulo, $_SESSION["perfilid"]);
                while ($row = mysql_fetch_array($permisos)) {
                    $copy = $row[0];
                    $edit = $row[1];
                    $delete = $row[2];
                }
            } else {
                $copy = 0;
                $edit = 0;
                $delete = 0;
            }

            //Se configuran imagenes y acceso de los iconos
            if ($copy == 0) {
                $class_copy = "boton_c_bloq";
                $disabled_copy = "disabled";
            } else {
                $class_copy = "boton_c";
                $disabled_copy = "";
            }

            if ($edit == 0) {
                $class_edit = "boton_e_bloq";
                $disabled_edit = "disabled";
            } else {
                $class_edit = "boton_e";
                $disabled_edit = "";
            }
            if ($delete == 0) {
                $class_delete = "boton_el_bloq";
                $disabled_delete = "disabled";
            } else {
                $class_delete = "boton_el";
                $disabled_delete = "";
            }

            $_SESSION['opc_ed'] = "class='$class_edit' $disabled_edit";
            $_SESSION['opc_el'] = "class='$class_delete' $disabled_delete";


            include "../template/sivisae_link_home.php";
            $periodos_crear = $consulta->periodos_crearSeguimiento("crear");
            $sedes = $consulta->traerSedes();
            ?>

            <!--contenedor-->

            <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <script src="js/sweetalert-master/dist/sweetalert-dev.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert-master/dist/sweetalert.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>

            <!--scripts de funcionalidad - inicio-->
            <script >

                ///Seguridad - Inicio
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

                    $(".chosen-select").chosen({no_results_text: "Uups, No se encontraron registros!"});
                });

                function nobackbutton() {
                    window.location.hash = "no-back-button";
                    window.location.hash = "Again-No-back-button" //chrome
                    window.onhashchange = function () {
                        window.location.hash = "no-back-button";
                    }
                }
                ///Seguridad - Fin

                ///validaciones - inicio

                $(document).ready(function () {
                    $("#semanas, #seguimientos").keydown(function (event) {
                        if (event.shiftKey)
                        {
                            event.preventDefault();
                        }

                        if (event.keyCode === 46 || event.keyCode === 8) {
                        }
                        else {
                            if (event.keyCode < 95) {
                                if (event.keyCode < 48 || event.keyCode > 57) {
                                    event.preventDefault();
                                }
                            }
                            else {
                                if (event.keyCode < 96 || event.keyCode > 105) {
                                    event.preventDefault();
                                }
                            }
                        }
                    });
                });

                ///validaciones - fin

                ///Popup - inicio

                function activarpopupcrear() {
                    // Crear
                    $('#boton_crear').bind('click', function (e) {
                        e.preventDefault();
                        var form = document.form_crear;
                        limpiaForm(form);
                        $('#result').html('');
                        $('#popup_crear').bPopup();
                        $("#periodo").chosen();

                    });
                }

                function activarpopupeditar(id) {
                    //Editar

                    $('#boton_editar' + id).bind('click', function (e) {
                        e.preventDefault();
                        var form = document.form_editar;
                        limpiaForm(form);
                        cargar_popup_editar(id);
                        $('#popup_editar').bPopup();
                        $("#periodo_e").chosen();
                    });
                }

                function cargar_popup_editar(id) {
                    var str = $('#input_' + id).val();
                    var ids = str.split("|");
                    //Se llenan los campos segun el formulario

                    document.getElementById("id_e").value = ids[0];
                    $("#periodo_e").val(ids[1]);
                    document.getElementById("semanas_e").value = ids[3];
                    document.getElementById("fecha_inicio_e").value = ids[4];
                    document.getElementById("fecha_fin_e").value = ids[5];
                    document.getElementById("seguimientos_e").value = ids[6];

                    //Se limpia estado
                    $('#result_e').html('');
                }

                function activarpopupeliminar(id, perfil) {
                    //Eliminar
                    $('#boton_eliminar' + id).bind('click', function (e) {
                        e.preventDefault();
                        document.getElementById("id_el").value = id;
                        document.getElementById("id_el_p").value = perfil;
                        $('#result_el').html('');
                        $('#popup_eliminar').bPopup();
                    });
                }
                ///Popup - fin


                ///logica - inicio
                function listaGrilla() {
                    var form = document.form_cortes;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/cortesSeguimientoCB.php',
                        data: dataString,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            $('#list_grilla').html(data);
                            $('#tb_grilla').show();

                            $("#list_grilla").on("click", ".pagination a", function (e) {
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_grilla").load("src/cortesSeguimientoCB.php", {
                                    "page": page

                                },
                                function () { //get content from PHP page

                                });
                            });
                        }

                    });
                    return false;
                }

                ///Crear
                function submitFormCrear() {
                    $('#btn_submit').attr("disabled", true);
                    $("#spinner").show();
                    var form = document.form_crear;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/creacion_corteSeguimientoCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit').attr("disabled", false);
                            $('#result').html(data);
                            $("#spinner").hide();
                            //Se recarga la grilla
                            listaGrilla();
                            setTimeout("CerrarPopup(1)", 1000);
                        }
                    });
                    return false;
                }

                ///Editar
                function submitFormEditar() {
                    $('#btn_submit_e').attr("disabled", true);
                    $("#spinner_e").show();
                    var form = document.form_editar;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/actualiza_corteSeguimientoCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit_e').attr("disabled", false);
                            $('#result_e').html(data);
                            $("#spinner_e").hide();
                            //Se recarga la grilla
                            listaGrilla();
                            setTimeout("CerrarPopup(2)", 1000);
                        }
                    });
                    return false;
                }

                ///Eliminar
                function submitFormEliminar() {
                    $('#btn_submit_el').attr("disabled", true);
                    $("#spinner_el").show();
                    var form = document.form_eliminar;
                    var dataString = $(form).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'src/elimina_corteSeguimientoCB.php',
                        data: dataString,
                        success: function (data) {
                            limpiaForm(form);
                            $('#btn_submit_el').attr("disabled", false);
                            $('#result_el').html(data);
                            $("#spinner_el").hide();
                            //Se recarga la grilla
                            listaGrilla();
                            setTimeout("CerrarPopup(3)", 1000);
                        }
                    });
                    return false;
                }

                function CerrarPopup(popup) {
                    if (popup == 1)
                    {
                        $('#popup_crear').bPopup().close();
                    }
                    if (popup == 2)
                    {
                        $('#popup_editar').bPopup().close();
                    }
                    if (popup == 3)
                    {
                        $('#popup_eliminar').bPopup().close();
                    }
                }

                function limpiaForm(miForm) {
                    // recorremos todos los campos que tiene el formulario
                    $(':input', miForm).each(function () {
                        var type = this.type;
                        var tag = this.tagName.toLowerCase();
                        //limpiamos los valores de los campos…
                        if (type === 'text' || type === 'password' || tag === 'textarea' || type === 'number')
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
                ///logica - fin

                $(document).ready(function () {
                    $("#fecha_inicio").change(function () {
                        var fecha = $('#fecha_inicio').val();
                        var arrayFecha = fecha.split('/');
                        var sem = $('#semanas').val();
                        var interv = 7 * parseInt(sem);
                        var operacion = '+';
                        var dia = arrayFecha[2];
                        var mes = arrayFecha[1];
                        var anio = arrayFecha[0];
                        var fechaInicial = new Date(anio, mes - 1, dia);
                        var fechaFinal = fechaInicial;

                        if (operacion === "+")
                            fechaFinal.setDate(fechaInicial.getDate() + parseInt(interv));

                        dia = fechaFinal.getDate();
                        mes = fechaFinal.getMonth() + 1;
                        anio = fechaFinal.getFullYear();

                        dia = (dia.toString().length == 1) ? "0" + dia.toString() : dia;
                        mes = (mes.toString().length == 1) ? "0" + mes.toString() : mes;

                        $('#fecha_fin').val(anio + "/" + mes + "/" + dia)
                    });
                });

            </script>
            <!--scripts de funcionalidad - fin-->


            <!--inicio calendario firefox-->
            <?php
            $navegador = getenv("HTTP_USER_AGENT");
            if (preg_match("/Firefox/i", "$navegador")) {
                ?>


                <script>
                    $(function () {
                        $.datepicker.setDefaults($.datepicker.regional["es"]);
                        $("#fecha_inicio").datepicker({
                            firstDay: 1,
                            changeYear: true,
                            changeMonth: true,
                            dateFormat: 'yy/mm/dd',
                            yearRange: '-0:+0'
                        });

                        $("#fecha_fin").datepicker({
                            firstDay: 1,
                            changeYear: true,
                            changeMonth: true,
                            dateFormat: 'yy/mm/dd',
                            yearRange: '-0:+0'
                        });

                        $("#fecha_inicio_e").datepicker({
                            firstDay: 1,
                            changeYear: true,
                            changeMonth: true,
                            dateFormat: 'yy/mm/dd',
                            yearRange: '-0:+0'
                        });

                        $("#fecha_fin_e").datepicker({
                            firstDay: 1,
                            changeYear: true,
                            changeMonth: true,
                            dateFormat: 'yy/mm/dd',
                            yearRange: '-0:+0'
                        });

                    });
                </script>

                <style>
                    /*div principal del datepicker*/
                    .ui-datepicker
                    {
                        width: auto;
                        background: #004669;
                    }

                    /*Tabla con los días del mes*/
                    .ui-datepicker table
                    {
                        font-size: 9px;
                    }

                    /*La cabecera*/
                    .ui-datepicker .ui-datepicker-header
                    {
                        font-size: 10px;
                        background: #FFFFFF;
                    }

                    /*Para los días de la semana: Sa Mo ... */
                    .ui-datepicker th
                    {
                        color: #FFFFFF;
                    }

                    /*Para items con los días del mes por defecto */
                    .ui-datepicker .ui-state-default
                    {
                        background: #FFFFFF;
                    }

                    /*Para el item del día del mes seleccionado */
                    .ui-datepicker .ui-state-active
                    {
                        background: orange;
                        color: #FFFFFF;
                    }
                </style>

                <?php
            }
            ?>
            <!--fin calendario firefox-->

        </head>

        <body onload="nobackbutton();
                        listaGrilla();">
            <!--Encabezado - Inicio-->
            <?php include "../template/sivisae_head_home.php"; ?>
            <!--Encabezado - Fin-->
            <!--Menu - Inicio-->
            <?php include "sivisae_menu.php"; ?>
            <!--Menu - Fin-->
            <main>

                <div >

                    <!--Barra de estado inicio-->
                    <?php include "sivisae_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <!--opciones inicio-->

                    <div align="right">
                        <button title="Crear Corte" id="boton_crear" onclick="activarpopupcrear()" <?php echo $disabled_copy; ?> class="<?php echo $class_copy; ?>"></button>    
                    </div>

                    <!--opciones fin-->


                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            CORTES DE SEGUIMIENTO
                        </h2>
                    </div>

                    <div align="center" id="list_grilla"></div>

                    <!--listado de fin-->

                    <!--creacion de inicio-->
                    <div id="popup_crear">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Crear Corte de Seguimiento
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_crear" name="form_crear" method="post" onsubmit="return submitFormCrear()" action="src/creacion_eventoCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td>Periodo (*):</td>
                                                <td>
                                                    <?php
                                                    $sbHtML2 = "<select required='required' style='width: 180px;' name='periodo' id='periodo' data-placeholder='Seleccione un periodo...' style='width: 150px' tabindex='2'>";
                                                    $sbHtML2.= "<option value=''></option>";
                                                    while ($row = mysql_fetch_array($periodos_crear)) {
                                                        $sbHtML2.="<option value=$row[0]>";
                                                        $sbHtML2.= $row[1];
                                                        $sbHtML2.="</option>";
                                                    }
                                                    $sbHtML2.= "</select>";
                                                    echo $sbHtML2;
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="cant_semanas">Cant. de Semanas (1-16) (*):</label></td>
                                                <td><input style="width: 180px;" min="1" max="16" id="semanas" name="semanas" type="number" maxlength="2" required="Por favor ingrese el número de semanas."/></td>
                                            </tr>
                                            <tr>
                                                <td>Fecha Inicio (*):</td>
                                                <td><input style="width: 180px;" id="fecha_inicio" name="fecha_inicio"  type="date" required="Por favor ingrese la fecha fin del corte."/></td>
                                            </tr>   
                                            <tr>
                                                <td>Fecha Fin (*):</td>
                                                <td><input style="width: 180px;" id="fecha_fin" name="fecha_fin"  type="date" required="Por favor ingrese la fecha fin del corte."/></td>
                                            </tr>   
                                            <tr>
                                                <td><label for="no_seguimientos">No. Seguimientos (*):</label></td>
                                                <td><input style="width: 180px;" min="1" max="16" id="seguimientos" name="seguimientos" type="number" maxlength="2" required="Por favor ingrese el número de seguimientos a realizar."/></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <p><input class="submit_fieldset_autenticacion" type="submit" value="Crear"/></p>
                                                    <div align="center" id="result"></div>
                                                    <div id="spinner" align="center" style="display:none;">
                                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </form>                  
                            </div>
                        </div>
                    </div>
                    <!--creacion de fin-->

                    <!--edicion de inicio-->
                    <div id="popup_editar">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Editar Corte Seguimiento
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center">
                                <form id="form_editar" name="form_editar" method="post" onsubmit="return submitFormEditar()" action="src/actualiza_eventoCB.php">
                                    <div style="background-color: #E8E8E8">
                                        <table style="width: 400px">
                                            <tr>
                                                <td>Periodo (*):</td>
                                                <td>
                                                    <?php
                                                    $periodos_crear = $consulta->periodos_crearSeguimiento("editar");
                                                    $sbHtML2 = "<select required='required' style='width: 180px;' name='periodo_e' id='periodo_e' data-placeholder='Seleccione un periodo...' style='width: 150px' tabindex='2'>";
                                                    $sbHtML2.= "<option value=''></option>";
                                                    while ($row = mysql_fetch_array($periodos_crear)) {
                                                        $sbHtML2.="<option value=$row[0]>";
                                                        $sbHtML2.= $row[1];
                                                        $sbHtML2.="</option>";
                                                    }
                                                    $sbHtML2.= "</select>";
                                                    echo $sbHtML2;
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="cant_semanas_e">Cant. de Semanas (1-16) (*):</label></td>
                                                <td><input style="width: 180px;" min="1" max="16" id="semanas_e" name="semanas_e" type="number" maxlength="2" required="Por favor ingrese el número de semanas."/></td>
                                            </tr>
                                            <tr>
                                                <td>Fecha Inicio (*):</td>
                                                <td><input style="width: 180px;" id="fecha_inicio_e" name="fecha_inicio_e"  type="date" required="Por favor ingrese la fecha fin del corte."/></td>
                                            </tr>   
                                            <tr>
                                                <td>Fecha Fin (*):</td>
                                                <td><input style="width: 180px;" id="fecha_fin_e" name="fecha_fin_e"  type="date" required="Por favor ingrese la fecha fin del corte."/></td>
                                            </tr>   
                                            <tr>
                                                <td><label for="no_seguimientos_e">No. Seguimientos (*):</label></td>
                                                <td><input style="width: 180px;" min="1" max="16" id="seguimientos_e" name="seguimientos_e" type="number" maxlength="2" required="Por favor ingrese el número de seguimientos a realizar."/></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input style="width: 180px;" id="id_e" name="id_e" type="hidden" maxlength="30" />
                                                    <p><input id="btn_submit_e" name="btn_submit_e" class="submit_fieldset_autenticacion" type="submit" value="Actualizar"/></p>
                                                    <div align="center" id="result_e"></div>
                                                    <div id="spinner_e" align="center" style="display:none;">
                                                        <img id="img-spinner" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </form>                  
                            </div>
                        </div>
                    </div>
                    <!--edicion de fin-->

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
