<?php

session_start();
/*
 * 
 *   @author Andres Mendez
 * 
 */

include_once '../config/sivisae_class.php';
include_once './mail_config.php';
include_once './paginador.php';

$accion = isset($_POST['accion']) ? $_POST['accion'] : "n";
$_SESSION['perfilid'] = isset($_POST['perfilid']) ? $_POST['perfilid'] : "0";

if ($accion === "buscar") {
    $documento = $_POST['documento'];
    $codigoverificacion = $_POST['codigo'];
    $interno = $_POST['inter'];
    $consulta = new sivisae_consultas();
    $buscar = $consulta->buscarGraduado($documento);
    if ($buscar !== 'no') {
        $data = mysqli_fetch_array($buscar);

        if ($interno == "yes") {
            $verificador = true;
        } else {
            $verificador = $consulta->verificarGraduadoExterno($documento, $codigoverificacion);
        }

        if ($verificador) {
            $datos = array(
                'graduadoid' => $data['graduado'],
                'tipo_doc' => $data['tipo_doc'],
                'documento' => $data['documento'],
                'nombre' => ucwords($data['nombre']),
                'apellido' => ucwords($data['apellido']),
                'fecha_nac' => $data['fecha_nac'] === "SIN DATO" ? "" : $data['fecha_nac'],
                'pais_nac' => $data['pais_nac'] === "SIN DATO" ? "" : $data['pais_nac'],
                'ciudad_nac' => $data['ciudad_nac'] === "SIN DATO" ? "" : $data['ciudad_nac'],
                'cod_ciudad_nac' => isset($data['cod_ciudad_nac']) ? $data['cod_ciudad_nac'] : "",
                'sexo' => $data['sexo'] === "SIN DATO" ? "" : $data['sexo'],
                'pais_residencia' => $data['pais_residencia'] === "SIN DATO" ? "" : $data['pais_residencia'],
                'cod_ciudad_res' => isset($data['cod_ciudad_res']) ? $data['cod_ciudad_res'] : "",
                'ciudad_residencia' => $data['ciudad_residencia'] === "SIN DATO" ? "" : $data['ciudad_residencia'],
                'direccion_residencia' => $data['direccion_residencia'] === "SIN DATO" ? "" : $data['direccion_residencia'],
                'estrato' => $data['estrato'] === "SIN DATO" ? "" : $data['estrato'],
                'telefono_residencia' => $data['telefono_residencia'] === "SIN DATO" ? "" : $data['telefono_residencia'],
                'telefono_celular' => $data['telefono_celular'] === "SIN DATO" ? "" : $data['telefono_celular'],
                'email' => $data['email'] === "SIN DATO" ? "" : $data['email'],
                'email_2' => $data['email_2'] === "SIN DATO" ? "" : $data['email_2'],
                'estado_civil' => $data['estado_civil'] === "SIN DATO" ? "" : $data['estado_civil'],
                'nombre_fam' => $data['nombre_fam'] === "SIN DATO" ? "" : $data['nombre_fam'],
                'parentezco' => $data['parentezco'] === "SIN DATO" ? "" : $data['parentezco'],
                'tel_fam' => $data['tel_fam'] === "SIN DATO" ? "" : $data['tel_fam'],
                'cel_fam' => $data['cel_fam'] === "SIN DATO" ? "" : $data['cel_fam'],
                'email_fam' => $data['email_fam'] === "SIN DATO" ? "" : $data['email_fam'],
                'situacion' => $data['situacion'] === "SIN DATO" ? "" : $data['situacion'],
                'nombre_empresa' => $data['nombre_empresa'] === "SIN DATO" ? "" : ucwords($data['nombre_empresa']),
                'cargo' => $data['cargo'] === "SIN DATO" ? "" : ucwords($data['cargo']),
                'telefono_of' => $data['telefono_of'] === "SIN DATO" ? "" : $data['telefono_of'],
                'ciiu' => $data['ciiu'] === "SIN DATO" ? "" : $data['ciiu'],
                'relacion_unad' => $data['relacion_unad'] === "SIN DATO" ? "" : $data['relacion_unad'],
                'email_lab' => $data['email_lab'] === "SIN DATO" ? "" : $data['email_lab']);
            echo json_encode($datos);
        } else {
            echo json_encode('na');
        }
    } else {
        echo json_encode($buscar);
    }

}

if ($accion === "guardar") {
    $graduado = $_POST['graduado_id'];
    $nombre = strtoupper($_POST['nombre']);
    $apellido = strtoupper($_POST['apellido']);
    $tipo_doc = $_POST['tipo_doc'];
    $documento = $_POST['documento'];
    $sexo = strtoupper($_POST['sexo']);
    $est_civil = $_POST['est_civil'];
    $fecha_nac = $_POST['fecha_nac'];
    $ciudad_nac = $_POST['city_nac'];
    $ciudad_res = $_POST['city_res'];
    $direccion = strtoupper($_POST['direccion']);
    $estrato = $_POST['estrato'];
    $tel_res = $_POST['tel_res'];
    $tel_cel = $_POST['tel_cel'];
    $email = strtoupper($_POST['email']);
    $email_2 = strtoupper($_POST['email2']);
    $nombre_fam = strtoupper($_POST['nombre_fam']);
    $parentezco = $_POST['parentezco'];
    $tel_res_fam = $_POST['tel_res_fam'];
    $tel_cel_fam = $_POST['tel_cel_fam'];
    $email_fam = strtoupper($_POST['email_fam']);
    $sit_lab = $_POST['sit_lab'];
    $empresa = $_POST['empresa'] !== '' ? strtoupper($_POST['empresa']) : strtoupper($_POST['cual']);
    $cargo = strtoupper($_POST['cargo']);
    $ciiu = $_POST['ciiu'];
    $tel_of = $_POST['tel_of'];
    $email_lab = strtoupper($_POST['email_lab']);
    $relacion = strtoupper($_POST['relacion']);
    $titulos = $_POST['cant_t'];
    $privacy = isset($_POST['privacy']) ? $_POST['privacy'] : "";
    $arrayTitulos = array();
    for ($i = 0; $i < $titulos; $i++) {
        if (isset($_POST['cod_programa' . ($i + 1)])) {
            $arrayTitulos[$i] = array(
//                "titulo_id"         => $_POST['titulo_id'.($i+1)],
                "programa_id" => $_POST['cod_programa' . ($i + 1)],
                "cead_id" => $_POST['cod_cead' . ($i + 1)],
                "fecha_grado" => $_POST['fecha_g' . ($i + 1)]
            );
        }
    }
    $consulta = new sivisae_consultas();
    if ($graduado === 'x') {
        $res = $consulta->crearGraduado($tipo_doc, $documento, $nombre, $apellido, $fecha_nac, $ciudad_nac, $sexo, $ciudad_res, $direccion, $estrato, $tel_res, $tel_cel, $email, $email_2, $est_civil, $nombre_fam, $parentezco, $tel_res_fam, $tel_cel_fam, $email_fam, $sit_lab, $empresa, $cargo, $tel_of, $email_lab, $ciiu, $relacion, $privacy, $arrayTitulos);
    } else {
        $res = $consulta->actualizarGraduado($graduado, $tipo_doc, $documento, $nombre, $apellido, $fecha_nac, $ciudad_nac, $sexo, $ciudad_res, $direccion, $estrato, $tel_res, $tel_cel, $email, $email_2, $est_civil, $nombre_fam, $parentezco, $tel_res_fam, $tel_cel_fam, $email_fam, $sit_lab, $empresa, $cargo, $tel_of, $email_lab, $ciiu, $relacion, $privacy, $arrayTitulos);
    }

    //echo "$res";
}

if ($accion === "escuela") {
    $cod_prgog = $_POST['cod_prog'];
    $consulta = new sivisae_consultas();
    $escuela = $consulta->getEscuela($cod_prgog);

    echo $escuela;
}
if ($accion === "zona") {
    $cead = $_POST['cead'];
    $consulta = new sivisae_consultas();
    $zona = $consulta->getZona($cead);

    echo $zona;
}
if ($accion === "ciudades") {
    $pais = $_POST['pais'];
    $consulta = new sivisae_consultas();
    $mpios = $consulta->getCiudades($pais);
    $city = " <optgroup label=''>"
            . "<option value=''></option>";
    $depto = "";
    while ($row1 = mysqli_fetch_array($mpios)) {
        if ($depto !== $row1[2]) {
            $depto = $row1[2];
            $city .= "</optgroup>"
                    . "<optgroup label='$depto'>";
        }
        $city .= "<option value='$row1[0]'>$row1[1]</option>";
    }

    echo $city;
}

if ($accion === "getTitulos") {
    $documento = $_POST['documento'];
    $id = $_POST['tp'];
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $consulta = new sivisae_consultas();
    $titulo = array();
    $cant = 0;
    $cons = $consulta->getTitulos($documento, $id);
    while ($row3 = mysqli_fetch_array($cons)) {
        $cant++;
//        $titulo [$cant] = array(
//            'titulo_id' => $row3['titulo_id'],
//            'cod_prog' => $row3['cod_prog'],
//            'programa' => ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['programa']))),
//            'escuela' => ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['escuela']))),
//            'niv_aca' => ucwords(preg_replace($sintilde, $tildes, $row3['niv_aca'])),
//            'cod_cead' => $row3['cod_cead'],
//            'cead' => ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['cead']))),
//            'zona' => ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['zona']))),
//            'fecha_grado' => $row3['fecha_grado']
//        $row3['titulo_id']."|".$row3['cod_prog']."|".
//            ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['programa'])))."|".
//            ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['escuela'])))."|".
//            ucwords(preg_replace($sintilde, $tildes, $row3['niv_aca']))."|".
//            $row3['cod_cead']."|".
//            ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['cead'])))."|".
//            ucwords(preg_replace($sintilde, $tildes, utf8_decode($row3['zona'])))."|".
//            $row3['fecha_grado']
//        $titulo = implode("|", $row3);
//        print_r($row3);
        $titulo[] = $row3;
//                );
//        echo json_encode(array("data"=>$row3));
    }
//    $titulo ['cantidad'] = $cant;

//    print_r($titulo);

	array_walk_recursive($titulo, function(&$item, $key) {
			if (!mb_detect_encoding($item, 'utf-8', true)) {
				$item = utf8_encode($item);
			}
		});

    echo json_encode(array("data" => $titulo));
//    $retorno["json"] = json_encode($titulo);
//    echo implode("*", $titulos);
}

if ($accion === "frm") {
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $consulta = new sivisae_consultas();
    $html = '
        <script src="js/sigra/tools-actualizacion.js"></script>
        <script>
                      $(function() {
                        $( ".fecha" ).datepicker({
                          showOtherMonths: true,
                          selectOtherMonths: true,
                          changeMonth: true,
                          changeYear: true,
                          dateFormat: "yy-mm-dd",
                          yearRange: "-80:+0"
                        });
                      });
                </script>
        <form id="frm_actualizar">
                            <div id="actualizacion" >
                                <h3>General</h3>
                                <section >
                                    <table style="width: 90%;" id="inf-gen">
                                        <colgroup>
                                            <col style="width: 50%"/>
                                            <col style="width: 50%"/>
                                        </colgroup>
                                        <tr>
                                            <td><label>Nombres *</label><br/><input type="text" name="nombre" maxlength="50" id="nombre"/></td>
                                            <td><label>Apellidos *</label><br/><input type="text" maxlength="50" name="apellido" id="apellido"/></td>
                                            <input type="hidden" name="graduado_id" id="graduado_id"/>
                                        </tr>
                                        <tr>
                                            <td><label>Tipo Documento *</label><br/>
                                                <select id="tipo_doc" name="tipo_doc" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>
                                                    <option value="1">Cedula de Ciudadania</option>
                                                    <option value="TI">Tarjeta de identidad</option>
                                                    <option value="PI">Pasaporte Internacional</option>
                                                    <option value="RC">Registro Civil</option>
                                                    <option value="2">Cedula de Extranjería</option>
                                                </select>
                                            </td>
                                            <td><label>No. Documento *</label><br/><input type="text" maxlength="50" name="documento" id="documento"/></td>
                                        </tr>
                                        <tr>
                                            <td><label>Sexo *</label><br/>
                                                <label><input type="radio" name="sexo" id="sexo_F" value="F"> Femenino</label>
                                                <label><input type="radio" name="sexo" id="sexo_M" value="M"> Masculino</label>
                                            </td>
                                            <td><label>Estado Civil *</label><br/>
                                                <select id="est_civil" name="est_civil" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>
                                                    <option value="Casado(a)">Casado(a)</option>
                                                    <option value="Madre soltera">Madre soltera</option>
                                                    <option value="Religioso">Religioso</option>
                                                    <option value="Separado(a)">Separado(a)</option>
                                                    <option value="Soltero(a)">Soltero(a)</option>
                                                    <option value="Union Libre">Union Libre</option>
                                                    <option value="Viudo(a)">Viudo(a)</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label id="lbl_pais_nac">País de nacimiento *</label><br/>
                                                <select id="pais_nac" name="pais_nac" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>';
    $pais = $consulta->paises();
    while ($row1 = mysqli_fetch_array($pais)) {
        $html.= "<option value='$row1[0]'>$row1[1]</option>";
    }
    $html.= '
                                                </select>
                                            </td>
                                            <td><label id="lbl_ciudad_nac">Ciudad de nacimiento *</label><br/> 
                                                <select id="city_nac" name="city_nac" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>';
    $html.= '</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Fecha de nacimiento</label><br/><input type="text" id="fecha_nac" name="fecha_nac" class="fecha"/></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><label id="lbl_pais_res">País de residencia *</label><br/>
                                                <select id="pais_res" name="pais_res" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>';
    $pais = $consulta->paises();
    while ($row1 = mysqli_fetch_array($pais)) {
        $html.= "<option value='$row1[0]'>$row1[1]</option>";
    }
    $html.= '
                                                </select>
                                            </td>
                                            <td><label id="lbl_ciudad_res">Ciudad de residencia *</label><br/>
                                                <select id="city_res" name="city_res" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>';
    $html.= '</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Dirección *</label><br/><input type="text" name="direccion" maxlength="150" id="direccion"/></td>
                                            <td><label>Estrato *</label><br/>
                                                <select id="estrato" name="estrato" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>
                                                    <option value="1">Uno</option>
                                                    <option value="2">Dos</option>
                                                    <option value="3">Tres</option>
                                                    <option value="4">Cuatro</option>
                                                    <option value="5">Cinco</option>
                                                    <option value="6">Seis</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Teléfono Residencia *</label><br/><input type="text" maxlength="15" name="tel_res" id="tel_res"/></td>
                                            <td><label>Teléfono Celular *</label><br/><input type="text" name="tel_cel" maxlength="10" id="tel_cel"/></td>
                                        </tr>
                                        <tr>
                                            <td><label>Correo Electrónico *</label><br/><input type="email" maxlength="150" name="email" id="email"/></td>
                                            <td><label>Correo Alterno</label><br/><input type="email" name="email2" maxlength="150" id="email2"/></td>
                                        </tr>
                                    </table>
                                </section>
                                <h3>Académica</h3>
                                <section>
                                    <div id="titulos">
                                        <div id="info-titulo" style="display:none;">
                                            <table style="width: 90%; height: 250px">
                                                <colgroup>
                                                    <col style="width: 50%"/>
                                                    <col style="width: 50%"/>
                                                </colgroup>
                                                <tr>
                                                    <td><label>Programa Graduado</label><br/>
                                                        <select id="programa" name="programa" data-placeholder="Buscar...">
                                                            <option value=""></option>';

    $programa = $consulta->programaSegunEscuelaSIGRA("T");
    while ($row2 = mysqli_fetch_array($programa)) {
        $html.= "<option value='$row2[0]'>" .
                ucwords(preg_replace($sintilde, $tildes, $row2[1])) .
                "</option>";
    }
    $html.= '
                                                        </select>
                                                        <input type="hidden" name="cod_programa" id="cod_programa"/>
                                                        <input type="hidden" name="titulo_id" id="titulo_id" value="n"/>
                                                    </td>
                                                    <td><label>Fecha de Grado</label><br/><input type="text" id="fecha_grado" name="fecha_grado" class="fecha"/></td>
                                                </tr>
                                                <tr>
                                                    <td><label>Escuela</label><br/><input style="width:100%;" type="text" id="escuela" disabled="disabled" name="escuela"/></td>
                                                    <td><label>Nivel de formación</label><br/>
                                                        <input style="width:100%;" type="text" id="niv_aca" disabled="disabled" name="niv_aca"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><label>CEAD</label><br/>
                                                        <select id="cead" name="cead" data-placeholder="Seleccione un CEAD" class="chosen-select" style="width:180px;" tabindex="4">
                                                            <option value=""></option>';
    $cead = $consulta->ceadSegunZonaSIGRA("T");
    while ($row = mysqli_fetch_array($cead)) {
        $html.= "<option value='$row[0]'>" .
                ucwords($row[1]) .
                "</option>";
    }
    $html.= '
                                                        </select>
                                                        <input type="hidden" name="cod_cead" id="cod_cead"/>
                                                    </td>
                                                    <td><label>Zona</label><br/><input type="text" disabled=""disabled name="zona" id="zona"/></td>
                                                </tr>
                                                <tr>
                                                    <td colspann="2">
                                                    <a href="#" id="save" class="tipo botones">Guardar</a>
                                                    <a href="#" id="cancel" class="tipo botones">Cancelar</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>';
    if ($_SESSION['perfilid'] != '0') {
        $html .= '<br/><a href="#" id="add" class="tipo botones" onclick="return agregar();">Agregar</a><br/><br/>';
    }
    $html .= '<div id="grilla-titulos">
                                            <table id="inf-prog" class="tc" style="border-collapse: collapse;">
                                                <thead>
                                                    <tr class="bo">
                                                        <th width="45%" class="tcar-qa4j">PROGRAMA</th>
                                                        <th width="20%" class="tcar-qa4j">FECHA</th>
                                                        <th width="20%" class="tcar-qa4j">CEAD</th>';
    if ($_SESSION['perfilid'] != '0') {
        $html .= '<th width="10%" colspan="2" class="tcar-qa4j"></th>';
    }
    $html .= '</tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="bo" style="display:none;">
                                                        <td class="tcar-xitf"><input type="text" style="background: inherit;background-color:transparent;border:none;text-transform: capitalize;" disabled="disabled" name="programa0" id="programa0"/></td>
                                                        <td class="tcar-xitf2"><input type="text" style="background: inherit;background-color:transparent;border:none;text-transform: capitalize; text-align: center" disabled="disabled" name="fecha_grado0" id="fecha_grado0"/></td>
                                                        <td class="tcar-xitf2"><input type="text" style="background: inherit;background-color:transparent;border:none;text-transform: capitalize; text-align: center" disabled="disabled" name="cead0" id="cead0"/>
                                                        <!--<td nowrap class="editar botones">-->
                                                            <input type="hidden" name="fecha_g0" id="fecha_g0"/>
                                                            <input type="hidden" name="cod_programa0" id="cod_programa0"/>
                                                            <input type="hidden" name="cod_cead0" id="cod_cead0"/>
                                                            <input type="hidden" name="zona0" id="zona0"/>
                                                            <input type="hidden" name="escuela0" id="escuela0"/>
                                                            <input type="hidden" name="titulo_id0" id="titulo_id0"/>
                                                           <!-- Editar-->
                                                        </td>';
    if ($_SESSION['perfilid'] != '0') {
        $html .= '<td style="vertical-align: central;" nowrap class="eliminar tcar-qa4j">Quitar</td>';
    }
    $html .= '</tr>                                                    
                                                </tbody>
                                            </table>
                                            <input type="hidden" name="tot" id="tot" />
                                            <input type="hidden" value="0" name="cant_t" id="cant_t"/>
                                        </div>
                                    </div>
                                </section>
                                <h3>Familiar</h3>
                                <section>
                                    <table style="width: 100%;">
                                        <colgroup>
                                            <col style="width: 30%"/>
                                            <col style="width: 30%"/>
                                            <col style="width: 40%"/>
                                        </colgroup>
                                        <tr>
                                            <td colspan="3"><label>Nota: Ingrese aquí los datos de una persona de contacto (familiar, conyugue, amigo)</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Nombres y Apellidos *</label><br/><input type="text" name="nombre_fam" id="nombre_fam" style="width:80%"/></td>
                                            <td><label>Parentezco *</label><br/>
                                                <select id="parentezco" name="parentezco" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>
                                                    <option value="padres">Padre o Madre</option>
                                                    <option value="hermano">Hermano(a)</option>
                                                    <option value="tio">Tio(a)</option>
                                                    <option value="conyugue">Conyugue</option>
                                                    <option value="primo">Primo(a)</option>
                                                    <option value="hijo">Hijo(a)</option>
                                                    <option value="otro">otro</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Teléfono residencia *</label><br/><input type="text" name="tel_res_fam" id="tel_res_fam"/></td>
                                            <td><label>Teléfono Celular *</label><br/><input type="text" name="tel_cel_fam" id="tel_cel_fam"/></td>
                                        <td><label>Correo Electrónico</label><br/><input type="text" name="email_fam" id="email_fam"/></td>
                                            
                                        </tr>
                                    </table>
                                </section>
                                <h3>Laboral</h3>
                                <section>
                                    <table style="width: 90%; height: 250px" id="laboral">
                                        <colgroup>
                                            <col style="width: 50%"/>
                                            <col style="width: 50%"/>
                                        </colgroup>
                                        <tr>
                                            <td><label>Situación laboral *</label></td>
                                            <td>
                                                <select id="sit_lab" name="sit_lab" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>
                                                    <option value="Empleado">Empleado</option>
                                                    <option value="Desempleado">Desempleado</option>
                                                    <option value="Comerciante">Comerciante</option>
                                                    <option value="Empresario">Empresario</option>
                                                    <option value="Independiente">Independiente </option>
                                                    <option value="Oficios del hogar">Oficios del hogar</option>
                                                    <option value="Estudiando">Estudiando</option>
                                                    <option value="Incapacitado permanente para trabajar">Incapacitado permanente para trabajar</option>
                                                    <option value="Otra actividad (¿cuál?)">Otra actividad (¿cuál?)</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr class="inf_cual" style="display: none">
                                            <td><label>¿Cuál?</label><br/><input type="text" name="cual" id="cual"/></td>  
                                        </tr>
                                        <tr class="inf_lab" style="display: none">
                                            <td><label>Nombre de la Empresa *</label><br/><input type="text" name="empresa" id="empresa"/></td>
                                            <td><label>Cargo *</label><br/><input type="text" name="cargo" id="cargo"/></td>
                                        </tr>
                                        <tr class="inf_lab" style="display: none">
                                            <td><label>Actividad Económica CIIU *</label><br/>
                                                <select id="ciiu" name="ciiu" data-placeholder="Seleccione..." class="chosen-select" >
                                                    <option value=""></option>';
    $ciiu = $consulta->listadoCiiu();
    while ($row = mysqli_fetch_array($ciiu)) {
        $html.= "<option value='$row[0]'>" .
                ucwords($row[1]) .
                "</option>";
    }
    $html.= '
                                                </select>
                                            </td>
                                            <td><label>¿Tiene relación con su programa? *</label><br/>
                                                <select id="relacion" name="relacion" class="chosen-select" data-placeholder="Buscar...">
                                                    <option value=""></option>
                                                    <option value="SI">Si</option>
                                                    <option value="NO">No</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr class="inf_lab" style="display: none">
                                            <td><label>Teléfono</label><br/><input type="text" name="tel_of" id="tel_of"/></td>
                                            <td><label>Correo Electrónico</label><br/><input type="text" name="email_lab" id="email_lab"/></td>
                                        </tr>
                                    </table>
                                </section>
                            </div>';
    if ($_SESSION['perfilid'] === '0') {
        $html .= '<input type="hidden" name="privacy" id="privacy" />';
    }
    $html .= '
                        </form>';

    echo $html;
}
if ($accion === "mail") {
    $doc = base64_decode($_POST['doc']);
    $mail = new mail_config();
    $consulta = new sivisae_consultas();
    $token = $consulta->token();
    $clave = $consulta->token();
    $graduado = mysqli_fetch_array($consulta->buscarGraduado($doc));
    $env = $mail->enviarPass("Credenciales para ingreso al Sistema de Información de Graduados", $doc, $clave, "graduados@unad.edu.co", ucwords($graduado['nombre'] . " " . $graduado['apellido']), base64_encode($token));
    if ($env) {
        $l = $consulta->crearCredencialGraduado($doc, $graduado['email'], RUTA_PPAL . "pages/sivisae_login.php?grc=" . base64_encode($token), $doc, $clave, $token);
    }

    echo json_encode(array("data" => $l));
}

if ($accion === "listado") {
    $consulta = new sivisae_consultas();
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : 'T';
    $programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode("', '", $_POST['programa']) : 'T';
    $cead1 = isset($_POST['cead']) && $_POST['cead'] != '' ? implode("', '", $_POST['cead']) : 'T';
    $zona1 = isset($_POST['zona']) && $_POST['zona'] != '' ? implode("', '", $_POST['zona']) : 'T';
    if (isset($_POST["registros"])) {
        $registros = $_POST["registros"];
    } else {
        $registros = 50;
    }
    if (isset($_POST["page"])) {
        $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
        if (!is_numeric($page_number)) {
            die('N' . chr(250) . 'mero de p' . chr(225) . 'gina incorrecto!');
        } //incase of invalid page number
    } else {
        $page_number = 1; //Si no hay numero de pagina coloca 1 por defecto
    }
//Cantidad de items a mostrar
    $item_per_page = $registros;

    $cantGra;
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $cantGra = mysqli_fetch_array($consulta->cantGraduadosNotif($_POST["buscar"], $programa, $escuela, $cead1, $zona1));
    } else {
        $cantGra = mysqli_fetch_array($consulta->cantGraduadosNotif('', $programa, $escuela, $cead1, $zona1));
//        $cantGra = mysqli_fetch_array($consulta->cantGraduados('', 'T', 'T','T', 'T'));
    }
    $get_total_rows = $cantGra['cant'];
    //Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

    $graduados;
//Consulta que alimenta la tabla de graduados dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $graduados = $consulta->listaGraduadosNotif($page_position, $item_per_page, $_POST["buscar"], $programa, $escuela, $cead1, $zona1);
    } else {
        $graduados = $consulta->listaGraduadosNotif($page_position, $item_per_page, '', $programa, $escuela, $cead1, $zona1);
//        $graduados = $consulta->listaGraduados($page_position, $item_per_page, '', 'T', 'T','T', 'T');
    }
    $html = "";
    if ($get_total_rows <= 0) {
        $html = 'No se encontraron Graduados que cumplan las condiciones de busqueda';
    } else {
        $html = "<br>
        <table id='tb_graduados' class='tg' style='table-layout: fixed; width:100%'>
            <colgroup>
                <col style='width: 10%'>
                <col style='width: 25%'>
                <col style='width: 40%'>
                <col style='width: 13%'>
                <col style='width: 12%'>
            </colgroup>
				<thead>
					<tr>
                                                <th>DOCUMENTO</th>
						<th>NOMBRE</th>
						<th>TITULOS</th>
						<th>ACTUALIZADO</th>
						<th></th>
					</tr>
				</thead>
                        <tbody>
                    ";

        while ($row = mysqli_fetch_array($graduados)) {
            $id = $row[0];
            $documento = ucfirst($row[1]);
            $nombre = ucwords($row[2]);
            $apellido = ucwords($row[3]);
            $mail = $row[4];
            $fecha_mod = $row[5];
            $titulos = $consulta->getTitulos($documento, $id);
            $cant_t = mysqli_num_rows($titulos);
            $strTitulos = array();
            while ($row1 = mysqli_fetch_array($titulos)) {
                $strTitulos[] = " - " . ucwords($row1[1]);
            }
            $doc = base64_encode($documento);
            $html .= "<tr>"
                    . "<input type='hidden' value='$id' id='estid-$id' class='id'/>
                    <td align='center'>$documento</td>
                    <td>$nombre $apellido</td>
                    <td>" . implode("<br/>", $strTitulos) . "</td>
                    <td align='center'>$fecha_mod</td> 
                    <td><a id='$id' class='link_alt' onclick='return send_mail(\"$doc\");' href='#'>Enviar correo</a></td>
                    </tr> ";
        }
        $html .= "     </tbody>
                    </table>";

        $mostrar = $get_total_rows > $registros ? $registros : $get_total_rows;
        $html .= '<div align="center"><br><br>'
                . '<table><tr><td>Mostrando ' . $mostrar . ' registros de ' . $get_total_rows . ' encontrados </td>'
                . '<td>';
        $html .= paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        $pagina = $total_pages > 1 ? "p&aacute;ginas" : "p&aacute;gina";
        $html .= '</td>'
                . '<td> en ' . $total_pages . ' ' . $pagina . '.</td></tr></table></div>'
                . '<div id="oculto">'
                . '<input type="hidden" id="est_hid" name="est_hid[]"/>'
                . '</div>';
    }

    echo $html;
}

if ($accion === "listadoVerificacion") {
    $consulta = new sivisae_consultas();
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $escuela = isset($_POST['escuela']) && $_POST['escuela'] != '' ? implode("', '", $_POST['escuela']) : 'T';
    $programa = isset($_POST['programa']) && $_POST['programa'] != '' ? implode("', '", $_POST['programa']) : 'T';
    $cead1 = isset($_POST['cead']) && $_POST['cead'] != '' ? implode("', '", $_POST['cead']) : 'T';
    $zona1 = isset($_POST['zona']) && $_POST['zona'] != '' ? implode("', '", $_POST['zona']) : 'T';
    if (isset($_POST["registros"])) {
        $registros = $_POST["registros"];
    } else {
        $registros = 50;
    }
    if (isset($_POST["page"])) {
        $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
        if (!is_numeric($page_number)) {
            die('N' . chr(250) . 'mero de p' . chr(225) . 'gina incorrecto!');
        } //incase of invalid page number
    } else {
        $page_number = 1; //Si no hay numero de pagina coloca 1 por defecto
    }
//Cantidad de items a mostrar
    $item_per_page = $registros;

    $cantGra;
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $cantGra = mysqli_fetch_array($consulta->cantListadoVerificacion($_POST["buscar"], $programa, $escuela, $cead1, $zona1));
    } else {
        $cantGra = mysqli_fetch_array($consulta->cantListadoVerificacion('', $programa, $escuela, $cead1, $zona1));
//        $cantGra = mysqli_fetch_array($consulta->cantGraduados('', 'T', 'T','T', 'T'));
    }
    $get_total_rows = $cantGra['cant'];
    //Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

    $graduados;
//Consulta que alimenta la tabla de graduados dependiendo del registro en que debe iniciar y la cantidad de registros por pagina.
    if (isset($_POST["buscar"]) && $_POST["buscar"] != '') {
        $graduados = $consulta->listadoVerificacion($page_position, $item_per_page, $_POST["buscar"], $programa, $escuela, $cead1, $zona1);
    } else {
        $graduados = $consulta->listadoVerificacion($page_position, $item_per_page, '', $programa, $escuela, $cead1, $zona1);
//        $graduados = $consulta->listaGraduados($page_position, $item_per_page, '', 'T', 'T','T', 'T');
    }
    $html = "";
    if ($get_total_rows <= 0) {
        $html = 'No se encontraron Graduados que cumplan las condiciones de busqueda';
    } else {
        $html = "<br>
        <table id='tb_graduados' class='tg' style='table-layout: fixed; width:100%'>
            <colgroup>
                <col style='width: 10%'>
                <col style='width: 25%'>
                <col style='width: 55%'>
                <col style='width: 10%'>
            </colgroup>
				<thead>
					<tr class='bordes'>
                                                <th>DOCUMENTO</th>
						<th>NOMBRE</th>
						<th colspan='2'>TITULOS</th>
					</tr>
				</thead>
                        <tbody>
                    ";

        while ($row = mysqli_fetch_array($graduados)) {
            $id = $row[0];
            $documento = ucfirst($row[1]);
            $nombre = ucwords($row[2]);
            $apellido = ucwords($row[3]);
            $mail = $row[4];
            $fecha_mod = $row[5];
            $titulos = $consulta->titulosPorVerificar($id);
            $cant_t = mysqli_num_rows($titulos);
            $strTitulos = array();
            $strIdTitulos = array();
            while ($row1 = mysqli_fetch_array($titulos)) {
                $strIdTitulos[] = " - " . ucwords($row1[0]);
                $strTitulos[] = " - " . ucwords($row1[1]);
            }
            $doc = base64_encode($documento);
            $html .= "<tr>"
                    . "<input type='hidden' value='$id' id='estid-$id' class='id'/>
                    <td align='center'>$documento</td>
                    <td>$nombre $apellido</td>
                    <td>" . implode("<br/>", $strTitulos) . "</td>
                    <td><a id='$id' class='link_alt' onclick='return activarpopupverificar(\"$id\");' >Verificar</a></td>
                    </tr> ";
        }
        $html .= "     </tbody>
                    </table>";

        $mostrar = $get_total_rows > $registros ? $registros : $get_total_rows;
        $html .= '<div align="center"><br><br>'
                . '<table><tr><td>Mostrando ' . $mostrar . ' registros de ' . $get_total_rows . ' encontrados </td>'
                . '<td>';
        $html .= paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        $pagina = $total_pages > 1 ? "p&aacute;ginas" : "p&aacute;gina";
        $html .= '</td>'
                . '<td> en ' . $total_pages . ' ' . $pagina . '.</td></tr></table></div>'
                . '<div id="oculto">'
                . '<input type="hidden" id="est_hid" name="est_hid[]"/>'
                . '</div>';
    }

    echo $html;
}

if ($accion === "para_verificar") {
    $id = $_POST["id"];
    $consulta = new sivisae_consultas();
    $sintilde = explode(',', SIN_TILDES);
    $tildes = explode(',', TILDES);
    $html = '
        <script>
            $.datepicker.regional["es"] = {
                 closeText: "Cerrar",
                 prevText: "<Ant",
                 nextText: "Sig>",
                 currentText: "Hoy",
                 monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                 monthNamesShort: ["Ene","Feb","Mar","Abr", "May","Jun","Jul","Ago","Sep", "Oct","Nov","Dic"],
                 dayNames: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
                 dayNamesShort: ["Dom","Lun","Mar","Mié","Juv","Vie","Sáb"],
                 dayNamesMin: ["Do","Lu","Ma","Mi","Ju","Vi","Sá"],
                 weekHeader: "Sm",
                 dateFormat: "dd/mm/yy",
                 firstDay: 1,
                 isRTL: false,
                 showMonthAfterYear: false,
                 yearSuffix: ""
             };
             $.datepicker.setDefaults($.datepicker.regional["es"]);
                      $(function() {
                        $( ".fecha" ).datepicker({
                          showOtherMonths: true,
                          selectOtherMonths: true,
                          changeMonth: true,
                          changeYear: true,
                          dateFormat: "yy-mm-dd",
                          yearRange: "-80:+0"
                        });
                      });
                </script>
        <style>
            .dsb {
                background: inherit;
                background-color:transparent;
                border:none;
            }
        </style>
        <table id="inf-prog" class="tc" style="border-collapse: collapse;">
            <thead>
                <tr class="bo">
                    <th width="45%" class="tcar-qa4j">PROGRAMA</th>
                    <th width="10%" class="tcar-qa4j">FECHA</th>
                    <th width="20%" class="tcar-qa4j">CEAD</th>
                    <th width="12%" class="tcar-qa4j">ESTADO</th>
                    <th width="10%" class="tcar-qa4j"></th>
                </tr>
            </thead>
            <tbody>
                ';
    $titulos = $consulta->titulosPorVerificar($id);
    $cant_t = mysqli_num_rows($titulos);
    while ($row1 = mysqli_fetch_array($titulos)) {
        $nom = ucwords($row1[1]);
        $cead = ucwords($row1[4]);
        $cod_cead = $row1[5];
        $fecha = $row1[6];
        $t_id = $row1[0];
        $estado = $row1['estado'];
        $html .= '<tr class="bo" >
                    <td class="tcar-xitf">' . $nom . '</td>
                    <td class="tcar-xitf2"><input type="text" class="fecha dsb" value="' . $fecha . '" style="width:100%;text-transform: capitalize; text-align: center" disabled="disabled" name="fecha_grado-' . $t_id . '" id="fecha_grado-' . $t_id . '"/>
                    </td>
                    <td class="tcar-xitf2">
                        <!--<input type="text" value="' . $cead . '" class="dsb" style="text-transform: capitalize; text-align: center" disabled="disabled" name="cead-' . $t_id . '" id="cead-' . $t_id . '"/>-->
                        <select id="sel_cead-' . $t_id . '" name="sel_cead-' . $t_id . '" data-placeholder="Seleccione un CEAD" class="chosen-select1" style="width:180px;" tabindex="4" disabled="disabled" >
                            <option value=""></option>';
        $s_cead = $consulta->ceadSegunZonas("T");
        while ($row = mysqli_fetch_array($s_cead)) {
            $val = $row[0];
            $sel = $cod_cead === $val ? "selected" : "";
            $html.= "<option value='$val' $sel>" .
                    ucwords($row[1]) .
                    "</option>";
        }
        $html.= '
                        </select>    
                        <input type="hidden" name="cod_cead-' . $t_id . '" id="cod_cead-' . $t_id . '" value="' . $cod_cead . '"/>
                        <!-- Editar-->
                    </td>
                    <td style="vertical-align: central;" nowrap class="tcar-qa4j">
                        <select id="estado-' . $t_id . '" name="estado-' . $t_id . '" data-placeholder="Seleccione..." class="chosen-select2" style="width:80px;" disabled="disabled" >
                            <option value=""></option>
                            <option value="1">Valido</option>
                            <option value="2" selected>Pendiente</option>
                            <option value="3">Incorrecto</option>
                        </select>
                    </td>
                    <td style="vertical-align: central;" nowrap class="tcar-qa4j">
                        <a id="editar-' . $t_id . '" class="tipo botones" onclick="editar(\'' . $t_id . '\')">Editar</a>
                        <a id="guardar-' . $t_id . '" class="tipo botones" style="display:none" onclick="guardar(\'' . $t_id . '\')">Guardar</a>
                    </td>
                </tr>';
    }
    $html .= '</tbody>
        </table>';

    echo $html;
}

if ($accion === 'confirmar') {
    $t_id = $_POST['t_id'];
    $cead = $_POST['cead'];
    $fecha = $_POST['fecha'];
    $estado = $_POST['estado'];
    $consulta = new sivisae_consultas();
    $upd = $consulta->updateTitulo($t_id, $estado, $cead, $fecha);


//    echo mysqli_affected_rows($consulta->getConexion2());
    if ($upd) {
        echo "<label style='color: #004669'>Se actualizo el Título correctamente.</label>";
    } else {
        echo "<label style='color: #EC2121'>No se pudo actualizar el Título.</label>";
    }
}

    $consulta->destruir();
    //$consulta->destruir2();