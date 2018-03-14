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
//            echo $_SESSION['usuarioid'];
            header("Location: " . RUTA_PPAL . "pages/sigra_notifica.php?e=X02");
        } else {
            include "../template/sigra_link_home.php";
            $respuesta = $consulta->perfiles();
            $sedes = $consulta->traerSedes();
            $sintilde = explode(',', SIN_TILDES);
            $tildes = explode(',', TILDES);
            ?>

            <!--contenedor-->
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
            <link rel="stylesheet" type="text/css" href="template/css/estilo_index.css">
            <script src="js/popup/bpopup.js" type="text/javascript"></script>
            <link rel="stylesheet" type="text/css" href="template/popup/estilo_popup.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.css">
            <link rel="stylesheet" type="text/css" href="js/Chosen1.4/chosen.min.css">
            <script src="js/sweetalert2-master/dist/sweetalert2.min.js" type="text/javascript" languaje="javascript"></script>
            <link rel="stylesheet" href="js/sweetalert2-master/dist/sweetalert2.css">
            <script src="js/Chosen1.4/chosen.jquery.js" type="text/javascript" language="javascript"></script>
            <script src="js/Chosen1.4/chosen.jquery.min.js" type="text/javascript" language="javascript"></script>
            <link rel="stylesheet" href="js/Steps/css/normalize.css">
            <link rel="stylesheet" href="js/Steps/css/main.css">
            <link rel="stylesheet" href="js/Steps/css/jquery.steps.css">
            <script src="js/Steps/jquery.steps.js"></script>
            <link href="js/iCheck/polaris/polaris.css" rel="stylesheet">
            <script src="js/iCheck/icheck.js"></script>
            <script src="js/sigra/region_calendario.js"></script>
            <script src="js/sigra/enviar-actualizacion.js"></script>
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
        function limpiaForm(miForm) {
            // recorremos todos los campos que tiene el formulario
            $(':input', miForm).each(function () {
                var type = this.type;
                var tag = this.tagName.toLowerCase();
                //limpiamos los valores de los campos…
                if (type === 'text' || type === 'password' || tag === 'textarea' || tag === 'number')
                    this.value = "";
                // excepto de los checkboxes y radios, le quitamos el checked
                // pero su valor no debe ser cambiado
                else if (type === 'checkbox' || type === 'radio')
                    this.checked = false;
                // los selects le ponesmos el indice a -
                else if (tag === 'select')
                {
                    this.selectedIndex = 0;
                    $(this).val('').trigger("chosen:updated");
                }
            });
        }
        ///logica - fin

        $(function () {
            function log(message) {
        //                      $( "<div>" ).text( message ).prependTo( "#log" );
        //                      $( "#log" ).scrollTop( 0 );
            }

        });

            </script>


        </head>

        <body onload="nobackbutton();
                frm();">
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

                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            ACTUALIZACIÓN DE DATOS
                        </h2>
                    </div>
                    <br/>
                    <div align="center">
                        <table style="width: 60%;">
                            <colgroup>
                                <col style="width: 50%"/>
                                <col style="width: 50%"/>
                            </colgroup>
                            <tr>                                
                                <td align="center">
                                    <input type="hidden" name="documento_b" id="documento_b" value="<?php echo $_SESSION['ced']; ?>"/></td>
                            <input type="hidden" id="graduado_id" value="" name="graduado_id"/>
                            </tr>
                        </table>
                    </div>
                    <br/>
                    <div align="center" id="frm_actualizacion">
                    </div> 
                    <br/>
                    <div align="center" style="background-color: #004669">
                        <h2 id='p_fieldset_autenticacion_2'>
                            &nbsp;
                        </h2>
                    </div>
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
