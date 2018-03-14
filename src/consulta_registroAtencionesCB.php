<?php

session_start();
include_once '../config/sivisae_class.php';
include './paginador.php';
$consulta = new sivisae_consultas();


// Se cargan categorias según el perfil
$categorias = $consulta->categoriasAtencion($_SESSION["modulo"]);

$limite = ($_SESSION["perfilid"] === "16") ? 1000 : 200;

$ceadLista = $consulta->ceadSegunZona_Atenciones("T");
$programaLista = $consulta->programaSegunEscuelaAtencion("T");
//Se inician variables
$cedula = "";
$nombre = "";
$programa = "";
$cead = "";
$telefono = "";
$direccion = "";
$correo = "";


if (isset($_POST['documento'])) {

    echo "            
                <script type='text/javascript' language='javascript'>

                $('#observacion').keyup(function (e) {
                    var t = '';
                    if ($(this).attr('id') === 'observacion_t') {
                        t = '_t';
                    }
                    var limite = " . $limite . ";
                    var box = $(this).val();
                    var value = (box.length * 100) / limite;
                    var resta = limite - box.length;
                    if (box.length <= limite) {
                        if (resta == 0) {
                            $('#divContador' + t).html('Ha superado el límite de caracteres');
                        } else {
                            $('#divContador' + t).html(resta);
                        }
                        $('#divProgreso' + t).animate({'width': value + '%'}, 1);
                        if (value < 50) {
                            $('#divProgreso' + t).removeClass();
                            $('#divProgreso' + t).addClass('verde');
                        } else
                        if (value < 85) { // si no se llegó al 85% que sea amarilla
                            $('#divProgreso' + t).removeClass();
                            $('#divProgreso' + t).addClass('amarillo');
                        } else { // si se superó el 85% que sea roja
                            $('#divProgreso' + t).removeClass();
                            $('#divProgreso' + t).addClass('rojo');
                        }
                        ;
                    } else {
                        e.preventDefault();
                    }
                });
            </script>
";


    $documento = $_POST['documento'];
    $datosEstudiante = $consulta->consultarEstudiante($documento);
    $datosGraduado = $consulta->consultarGraduado($documento);
    $atenciones = $consulta->consultarAtenciones($documento, $_SESSION["perfilid"]);
    echo '     <table  style="width: 1000px; height:500px; background-color: #F7FDFA;" border="0">
                            <tr valign="top">
                                <td valign="top">
                                    <table style="width: 550px" cellspacing="0">
                                        <tr>
                                            <td align="center" colspan="2">
                                                <div align="center" style="background-color: #004669">
                                                    <h2 id="p_fieldset_autenticacion_2">
                                                        Datos Básicos
                                                    </h2>
                                                </div>
                                            </td>
                                        </tr>';


    $conteoE = 0;
    $conteoG = 0;

    //Se precargan datos
    while ($row = mysql_fetch_array($datosEstudiante)) {
        $cedula = $row[0];
        $nombre = $row[1];
        $correo = $row[2];
        $telefono = $row[3];
        $programa = $row[4];
        $escuela = $row[5];
        $cead = $row[6];
        $zona = $row[7];
        $conteoE++;
    }

    $titulos = "";
    while ($row = mysql_fetch_array($datosGraduado)) {
        if ($conteoE <= 0) {
            $cedula = $row[0];
            $nombre = $row[1];
            $correo = $row[2];
            $telefono = $row[3];
            $programa = $row[4];
            $escuela = $row[5];
            $cead = $row[6];
            $zona = $row[7];
        }
        $titulo = $row[4];
        $anio = $row[8];
        $titulos.= '<tr>
                <td>' . $titulo . '</td>
                <td>' . $anio . '</td>
            </tr>';
        $conteoG++;
    }





    $opc = 0;

    if ($conteoE <= 0 && $conteoG <= 0) {
        $datosAspirante = $consulta->consultarAspirante($documento);
        while ($row = mysql_fetch_array($datosAspirante)) {
            $cedula = $row[0];
            $nombre = $row[1];
            $programa = $row[2];
            $cead = $row[3];
            $telefono = $row[4];
            $direccion = $row[5];
            $correo = $row[6];
        }




        $opc = 1; //No existen datos
        echo '<tr><td><table style="width: 400px;" >
                            <tr>
                                <td><label for="nombre">Nombre:(*)</label></td>
                                <td colspan="3"><input style="width: 443px;" id="nombre" name="nombre" value="' . $nombre . '" type="text" maxlength="250"/></td>
                            </tr>
                            <tr>
                                <td><label for="correo">Correo:(*)</label></td>
                                <td colspan="3"><input style="width: 443px;" id="correo" name="correo" value="' . $correo . '" type="email" maxlength="150"/></td>
                            </tr>

                            <tr>
                                <td><label for="programa"> Programa:(*)</label></td>


                                            <td>
                                                <select id="programa_at" name="programa_at[]" data-placeholder="Seleccione un programa" class="chosen-select-deselect" style="width:180px;" tabindex="4">
                                                    <option value=""></option>';
        while ($row = mysql_fetch_array($programaLista)) {
            if ($row[0] == $programa) {
                echo '<option selected value="' . $row[0] . '">' .
                $row[1] . " - " . ucwords($row[2]) .
                '</option>';
            } else {
                echo '<option value="' . $row[0] . '">' .
                $row[1] . " - " . ucwords($row[2]) .
                '</option>';
            }
        }
        echo '</select>
                                            </td>
                                                                        

                                <td><label for="centro"> Centro:(*)</label></td>
                                <td>
                                                <select id="centro_at" name="centro_at[]" data-placeholder="Seleccione un centro" class="chosen-select-deselect"  style="width:180px;" tabindex="4">
                                                    <option value=""></option>';
        while ($row = mysql_fetch_array($ceadLista)) {
            if ($row[0] == $cead) {
                echo '<option selected value="' . $row[0] . '">' .
                $row[1] . " - " . ucwords($row[2]) .
                '</option>';
            } else {
                echo '<option value="' . $row[0] . '">' .
                $row[1] . " - " . ucwords($row[2]) .
                '</option>';
            }
        }
        echo '</select>
                                            </td>
                            </tr>

                            <tr>
                                <td><label for="telefono"> Teléfono:(*)</label></td>
                                <td><input style="width: 180px;" id="telefono" name="telefono" value="' . $telefono . '" type="text" maxlength="15"/></td>
                                <td><label for="direccion">Dirección:(*)</label></td>
                                <td><input style="width: 180px;" id="direccion" value="' . $direccion . '" name="direccion" type="text" maxlength="100"/></td>
                            </tr>
                        </table></td></tr>';
    } else {
        $opc = 2; // con datos
        echo '<tr>
            <td align="center" colspan="2"><p><span style="color:#696969;"><strong>' . $nombre . '</strong></span></p></td>
        </tr>
        <tr>
            <td><label><strong>Documento:</strong></label></td>
            <td><p>' . $cedula . '</p></td>
        </tr>
        <tr>
            <td><label><strong>Teléfono:</strong></label></td>
            <td><p>' . $telefono . '</p></td>
        </tr>
        <tr>
            <td><label><strong>Correo:</strong></label></td>
            <td><p>' . $correo . '</p></td>
        </tr>';
        if ($conteoE > 0) {
            echo '<tr>
            <td colspan="2">
                <div align="center" style="background-color: #E26C0E">
                    <h2 id="p_fieldset_autenticacion_2">
                        Estudiante
                    </h2>
                </div>
            </td>
        </tr>
        <tr>
            <td><label><strong>Programa:</strong></label></td>
            <td><p>' . $programa . '</p></td>
        </tr>
        <tr>
            <td><label><strong>Escuela:</strong></label></td>
            <td><p>' . $escuela . '</p></td>
        </tr>';
        }
        echo'<tr>
            <td><label><strong>Centro:</strong></label></td>
            <td><p>' . $cead . '</p></td>
        </tr>
        <tr>
            <td><label><strong>Zona:</strong></label></td>
            <td><p>' . $zona . '</p></td>
        </tr>';
    }

    //GRADUADOS
    echo '<tr>
            <td colspan="2">
                <div align="center" style="background-color: #E26C0E">
                    <h2 id="p_fieldset_autenticacion_2">
                        Graduado
                    </h2>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table id="tb_grados" class="tg" style="width: 550px">
                    <thead>
                        <tr>
                            <th>TITULO</th>                                                
                            <th>AÑO</th>
                        </tr>
                    </thead>
                    <tbody>';
    if ($conteoG <= 0) {
        echo '<tr>
                <td colspan="2">No hay registros como graduado</td>
            </tr>';
    } else {
        echo $titulos;
    }

    echo '</tbody>
                </table> 
            </td>
        </tr>';

    echo '</table>
                                </td>
                                <td valign="top">
                                    <table style="width: 450px">
                                        <tr>
                                            <td>
                                                <div align="center" style="background-color: #004669">
                                                    <h2 id="p_fieldset_autenticacion_2">
                                                        Atención Brindada
                                                    </h2>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <tr align="center">
                                            <td><label><strong>Categorias de atención</strong></label></td>
                                        </tr>

                                        <tr align="center">
                                            <td>
                                                <select id="cat_atencion" name="cat_atencion[]" data-placeholder="Seleccione una categoria"   class="chosen-select" multiple style="width:380px;" tabindex="4">
                                                    <option value=""></option>';
    while ($row = mysql_fetch_array($categorias)) {
        echo '<option value="' . $row[0] . '">' .
        ucwords($row[1]) .
        '</option>';
    }
    echo '</select>
                                            </td>
                                        </tr>

                                        <tr align="center">
                                            <td><label><strong>Atención brindada como</strong></label></td>
                                        </tr>

                                        <tr align="center">
                                            <td>';
    $mensaje = "Para el número de documento: " . $documento;
    echo '<label><input type="checkbox" value="1" name="atencion_b[]" id="atencion_asp">Aspirante</label>';
    echo '<label><input type="checkbox" value="2" name="atencion_b[]" id="atencion_est">Estudiante</label>';
//Se valida si es graduado o estudiante (activo), de lo contrario se registra como aspirante
    if ($conteoE > 0) {
        $mensaje.=", esta asociado a un estudiante";
    } else {
        $mensaje.=", NO se han encontrado coincidencias con estudiantes";
    }
    if ($conteoG > 0) {
        echo '<label><input type="checkbox" value="3" name="atencion_b[]" id="atencion_gra">Graduado</label>';
        $mensaje.=", esta asociado a un graduado.";
    } else {
        $mensaje.=", NO se han encontrado coincidencias con graduados.";
    }
    echo ' </td>
                                        </tr>







                                        <tr align="center">
                                            <td><label><strong>Observaciones (sugerencias, dificultades, comentarios finales)</strong></label></td>
                                        </tr>
                                        <tr align="center">
                                            <td>
                                                <div id="divContenedor" class="divContenedor">
                                                <div id="divContador" class="divContador">' . $limite . '</div>
                                                <div id="divCajaProgreso" class="divCajaProgreso">
                                                    <div id="divProgreso" class="divProgreso"></div>
                                                </div>
                                                <textarea id="observacion" class="observacion" name="observacion" maxlength="' . $limite . '"></textarea>
                                            </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <p>
                                                <input class="submit_fieldset_autenticacion" onclick="registrarAtencion(' . $opc . ');" type="button" value="Registrar Atención"/>
                                                </p>
                                                <div align="center" id="result2"></div>
                                                <div id="spinner2" align="center" style="display:none;">
                                                    <img id="img-spinner2" width="50" height="50" src="template/imagenes/generales/loading.gif" alt="Loading"/>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input style="width: 180px;" id="cedula_at" name="cedula_at" type="hidden" value="' . $documento . '"/>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" valign="top">
                                    <div align="center" style="background-color: #E26C0E">
                                        <h2 id="p_fieldset_autenticacion_2">
                                            Histórico de Atenciones
                                        </h2>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table id="tb_grados" class="tg" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>Fecha Atención</th>                                                
                                                <th>Categorias</th>
                                                <th>Atención Brindada Por</th>
                                                <th>Atención Brindada Como</th>
                                                <th>Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
    echo $atenciones;
    echo '</tbody>
                                    </table> 
                                </td>
                            </tr>
                        </table>';


    echo '<script type="text/javascript" language="javascript">
        $(document).ready(function () {
                    swal({
                        title: "Atención",
                        text: "' . $mensaje . '",
                        type: "info",
                        confirmButtonColor: "#004669",
                        confirmButtonText: "Aceptar"
                    });
                });
        </script>';

    ////guardar info aspirantes
    ///guardar info estudiantes graduados
    // tabla de atenciones anteriores histórico
    //boton registrar otra atencion
} else {
    echo "Error";
}
?>