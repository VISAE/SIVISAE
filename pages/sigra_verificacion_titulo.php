<?php
/*
 * 
 *   @author Ing. Andres Mendez
 * 
 */

session_start();
include_once '../config/sigra_class.php';
$consulta = new sigra_consultas();
?>
<head>
    <?php
    $browser = getenv("HTTP_USER_AGENT");
    if (preg_match("/MSIE/i", "$browser")) {
        //Navegadores no compatibles
        ?>
        <script language="JavaScript" type="text/JavaScript">
            window.location = "sigra_notifica.php?e=X01";
        </script>

        <?php
    } else {
        //Navegadores compatibles
        // Se valida inicio de sesion

        if (!isset($_SESSION['usuarioid'])) {
            //Debe iniciar sesion
            header("Location: " . RUTA_PPAL . "pages/sigra_notifica.php?e=X02");
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


            include "../template/sigra_link_home.php";
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            ?>

            <!--contenedor-->

<!--            <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>-->
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="template/grilla/estilo_grilla.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <script src="js/sweetalert2-master/dist/sweetalert2.min.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert2-master/dist/sweetalert2.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <link type="text/css" rel="stylesheet" href="js/introLoader/css/introLoader.min.css" />
            <script src="js/introLoader/jquery.introLoader.js" type="text/javascript" language="javascript"></script>
            <script src="js/introLoader/spin.min.js" type="text/javascript" language="javascript"></script>
            <script src="js/jquery-validation/dist/jquery.validate.js" type="text/javascript" language="javascript"></script>
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
            $("#hora").keydown(function (event) {
                if (event.shiftKey)
                {
                    event.preventDefault();
                }

                if (event.keyCode === 46 || event.keyCode === 8) {
                } else {
                    if (event.keyCode < 95) {
                        if (event.keyCode < 48 || event.keyCode > 57) {
                            event.preventDefault();
                        }
                    } else {
                        if (event.keyCode < 96 || event.keyCode > 105) {
                            event.preventDefault();
                        }
                    }
                }
            });
        });

        ///validaciones - fin

        function listaGraduados() {
        //                    var form = document.form_eventos;
        //                    var dataString = $('#filtros').find("select, input:hidden").serialize();
            var dataString = {
                "accion": "listadoVerificacion",
                "buscar": $("#buscar").val(),
                "cead": $("#cead").val(),
                "zona": $("#zona").val(),
                "programa": $("#programa").val(),
                "escuela": $("#escuela").val(),
                "registros": $("#registros").val()
            };
            $.ajax({
                type: 'POST',
                url: 'src/actualizacion_datosCB.php',
        //                        data: "accion=listado",
                data: dataString,
                beforeSend: function () {
                    startLoad("carg");
                },
                success: function (data) {
        //                            alert(data);
                    $('#list_graduados').html(data);
        //alert(data);
                    stopLoad("carg");

                    $("#list_graduados").on("click", ".pagination a", function (e) {
                        startLoad("carg");
                        e.preventDefault();
                        var page = $(this).attr("data-page"); //get page number from link
                        $("#list_graduados").load("src/actualizacion_datosCB.php", {
                            "accion": "listadoVerificacion",
                            "page": page,
                            "buscar": $("#buscar").val(),
                            "cead": $("#cead").val(),
                            "zona": $("#zona").val(),
                            "programa": $("#programa").val(),
                            "escuela": $("#escuela").val(),
                            "registros": $("#registros").val()
                        },
                        function () { //get content from PHP page
                            stopLoad("carg");
                        });
                        document.getElementById('p_fieldset_autenticacion_2').scrollIntoView(true);
                    });
                }

            });
            return false;
        }

        function startLoad(div) {
            $('#' + div).show();
            if (div !== "carg") {
                $("#" + div).introLoader({
                    animation: {
                        name: 'simpleLoader',
                        options: {
                            stop: false,
                            fixed: false,
                            exitFx: 'fadeOut',
                            ease: "linear",
                            style: 'light',
                            customGifBgColor: '#E8E8E8'
                        }
                    },
                    spinJs: {
                        lines: 13, // The number of lines to draw 
                        length: 10, // The length of each line 
                        width: 5, // The line thickness 
                        radius: 10, // The radius of the inner circle 
                        corners: 1, // Corner roundness (0..1) 
                        color: '#004669' // #rgb or #rrggbb or array of colors 
                    }
                });
            } else {
                $("#" + div).introLoader({
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
                        lines: 15, // The number of lines to draw 
                        length: 30, // The length of each line 
                        width: 10, // The line thickness 
                        radius: 30, // The radius of the inner circle 
                        corners: 1, // Corner roundness (0..1) 
                        color: '#004669' // #rgb or #rrggbb or array of colors 
                    }
                });
            }
        }
        function stopLoad(div) {
        //                    $('#list_graduados').show();
            $('#' + div).hide();
            var loader = $('#' + div).data('introLoader');
            loader.stop();
        }
        function send_mail(doc) {
            var dataString = {
                "accion": "mail",
                "doc": doc
            };
            $.ajax({
                type: "POST",
                url: "src/actualizacion_datosCB.php",
                data: dataString,
                beforeSend: function () {

                },
                success: function (data) {
                    alert(data);
                }
            });
            return false;
        }
        
        function activarpopupverificar(id) {
                var form = document.form_verificar;
                $('#popup_verificar').bPopup();
                startLoad("frm");
//                limpiaForm(form);
                $('#result').html("");
                $.ajax({
                    type: 'POST',
                    url: 'src/actualizacion_datosCB.php',
                    data: "accion=para_verificar&id=" + id,
//                    dataType: "JSON",
                    beforeSend: function () {
                        $("#data").html("");
                    },
                    success: function (data) {
                        $("#data").html(data);
                        $(".chosen-select1").chosen({no_results_text: "No se encontraron Coincidencias!", width: "180px"});
                        $(".chosen-select2").chosen({no_results_text: "No se encontraron Coincidencias!", width: "100%"});
//                        $("#sel_cead-"+id).val($("#cod_cead").val()).trigger("chosen:updated");
                        stopLoad("frm");
                    }
                });
                return false;
        }
        

        function CerrarPopup(popup) {
            if (popup === 1)
            {
                listaGraduados();
                $('#popup_verificar').bPopup().close();
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
        
        function editar(id){
            $("#fecha_grado-"+id).removeAttr("disabled");
            $("#sel_cead-"+id).removeAttr("disabled").trigger("chosen:updated");
            $("#estado-"+id).removeAttr("disabled").trigger("chosen:updated");
//            $("#sel_cead_"+id+"_chosen").removeClass("chosen-disabled");
            $("#fecha_grado-"+id).removeClass("dsb");
            $("#editar-"+id).slideUp("100",function(){
                $("#guardar-"+id).slideDown("100");                
            });
        }
        
        function guardar(id){
            var dataString = {
                "accion": "confirmar",
                "fecha": $("#fecha_grado-"+id).val(),
                "cead": $("#sel_cead-"+id).val(),
                "estado": $("#estado-"+id).val(),
                "t_id": id
            };
            $.ajax({
                type: 'POST',
                url: 'src/actualizacion_datosCB.php',
                data: dataString,dataType: "HTML",
                beforeSend: function () {
                    startLoad("frm");
                },
                success: function (data) {
                    $("#result").html(data);
                },
                complete: function (){
                    stopLoad("frm");
                    $("#guardar-"+id).slideUp("100",function(){
                        $("#fecha_grado-"+id).attr("disabled",true);
                        $("#fecha_grado-"+id).addClass("dsb");
                        $("#sel_cead-"+id).attr("disabled",true).trigger("chosen:updated");
                        $("#estado-"+id).attr("disabled",true).trigger("chosen:updated");
                        $("#editar-"+id).slideDown("100");  
//                        setTimeout(listaGraduados(), 1000);
                    });
                }
            });
        }
        
    </script>
            <!--scripts de funcionalidad - fin-->


            <!--inicio calendario firefox-->
            <?php
            $navegador = getenv("HTTP_USER_AGENT");
            if (preg_match("/Firefox/i", "$navegador")) {
                ?>


<!--                <script>
                    $(function () {
                        $.datepicker.setDefaults($.datepicker.regional["es"]);
                        $("#fecha").datepicker({
                            firstDay: 1,
                            changeYear: true,
                            changeMonth: true,
                            dateFormat: 'yy/mm/dd',
                            yearRange: '-0:+0'
                        });
                    });
                </script>-->

                <style>
/*                    div principal del datepicker
                    .ui-datepicker
                    {
                        width: auto;
                        background: #004669;
                    }

                    Tabla con los días del mes
                    .ui-datepicker table
                    {
                        font-size: 9px;
                    }

                    La cabecera
                    .ui-datepicker .ui-datepicker-header
                    {
                        font-size: 10px;
                        background: #FFFFFF;
                    }

                    Para los días de la semana: Sa Mo ... 
                    .ui-datepicker th
                    {
                        color: #FFFFFF;
                    }

                    Para items con los días del mes por defecto 
                    .ui-datepicker .ui-state-default
                    {
                        background: #FFFFFF;
                    }

                    Para el item del día del mes seleccionado 
                    .ui-datepicker .ui-state-active
                    {
                        background: orange;
                        color: #FFFFFF;
                    }*/
                </style>

                <?php
            }
            ?>
            <!--fin calendario firefox-->

        </head>

        <body onload="nobackbutton();">
            <!--Encabezado - Inicio-->
            <?php include "../template/sigra_head_home.php"; ?>
            <!--Encabezado - Fin-->
            <main>

                <div >
                    <!--Menu - Inicio-->
                    <?php include "sigra_menu.php"; ?>
                    <!--Menu - Fin-->
                    <!--Barra de estado inicio-->
                    <?php include "sigra_barra_estado.php"; ?>
                    <!--Barra de estado fin-->
                    <!--opciones inicio-->
<!--                    <div align="right">
                        <button title="Crear Noticia" id="boton_crear"  onclick="activarpopupcrear()" <?php echo $disabled_copy; ?> class="<?php echo $class_copy; ?>"></button>    
                    </div>-->


                    <!--listado de inicio-->

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>VERIFICAR TÍTULOS</h2>
                    </div>
                    <br/>                    
                    <div>
                        <div id="filtros">
                            <input type="hidden" id="accion" value="listado"/>
                            <?php include "sigra_filtro.php"; ?>
                        </div>
                        <br/>
                        <div>
                            <div id="carg" >  </div>
                            <div align="center" id="list_graduados">
                            </div>
                        </div>
                    </div>

                    <!--inicio-popup de verificación-->
                    <!--creacion de inicio-->
                    <div id="popup_verificar" style="width: 80%;">
                        <span class="button_cerrar b-close"></span>
                        <div align="center" style="background-color: #004669" >
                            <h2 id='p_fieldset_autenticacion_2'>
                                Títulos por Verificar
                            </h2>
                        </div>
                        <div  class="art-postcontent">
                            <div align="center" >
                                <form id="form_verificar" name="form_verificar" method="post">
                                    <div id="data" style="background-color: #E8E8E8; height: 350px; overflow: auto">
                                    </div>
                                        <div align="center" id="frm" style="display: none; height: 100px;background: #E8E8E8"></div>
                                                    <div align="center" id="result"></div>
                                </form> 
                                <a id="editar-'.$t_id.'" class="tipo botones" onclick="CerrarPopup(1)">Cerrar</a>
                            </div>
                        </div>
                    </div>
                    <!--creacion de fin-->
                    <!--fin-popup de verificación-->

                </div>
            </main>

            <?php
            //Pie de pagina
            include "../template/sigra_footer_home.php";
            ?>
        </body>
        <?php
    }
}
$consulta->destruir();
?>
