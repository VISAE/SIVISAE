<?php

/*
 * 
 *   @author Ing. Andres Mendez
 * 
 */
session_start();

include_once '../config/sigra_class.php';
include './paginador.php';
$consulta = new sigra_consultas();
$accion = $_POST["accion"];
$sintilde = explode(',', SIN_TILDES);
$tildes = explode(',', TILDES);

if ($accion==="listado") {
    if (isset($_POST["page"])) {
        $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
        if (!is_numeric($page_number)) {
            die('N' . chr(250) . 'mero de p' . chr(225) . 'gina incorrecto!');
        } //incase of invalid page number
    } else {
        $page_number = 1; //Si no hay numero de pagina coloca 1 por defecto
    }
//Cantidad de items a mostrar
    $item_per_page = 10;



//Obtiene la cantidad total de registros desde BD para crear la paginacion
    $cantEst = mysql_fetch_array($consulta->cantRegistros("select count(1) from SIGRA.encuesta where estado_id = 1;"));
    $get_total_rows = $cantEst[0];

//Divide la cantidad de registros totales entre la cantidad de registros a mostrar para saber cuantas paginas se crearan
    $total_pages = ceil($get_total_rows / $item_per_page);

//Obtengo la posicion en que debe arrancar la siguiente consulta.
    $page_position = (($page_number - 1) * $item_per_page);

//Consulta el listado
    $consulta1 = $consulta->getEncuestas($page_position, $item_per_page);
    $consulta->destruir();
    $consulta->destruir2();
    if (count($consulta1) <= 0) {
        echo 'No existen Encuestas';
    } else {

        echo "<br>
        <table id='tb_grilla' border='1' class='tg' width=90%'>
				<thead>
					<tr>
						<th>NOMBRE</th>
						<th>DESCRIPCIÓN</th>
						<th colspan='2'>ACCIONES (Doble click)</th>
					</tr>
				</thead>
                        <tbody>
                    ";
        while ($row = mysqli_fetch_array($consulta1)) {
            $encuesta_id = $row['encuesta_id'];
            $nombre = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row['nombre'])));
            $descripcion = ucfirst(preg_replace($sintilde, $tildes, utf8_decode($row['descripcion'])));
            $enc_enc = base64_encode($encuesta_id);
//            $organizador = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[6])));
//            $poblacion = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[7])));
//            $asistencia = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[14])));
//            $proyecto = ucwords(preg_replace($sintilde, $tildes, utf8_decode($row[13])));
            echo "<tr>"
            . "<td style='width:30%'>$nombre</td>"
            . "<td style='width:60%'>$descripcion</td>"
//            . "<td>$organizador</td>"
//            . "<td>$poblacion</td>"
//            . "<td>$asistencia</td>"
//            . "<td>$proyecto</td>"
            . "<td style='width:5%'> <button title='Editar Encuesta' " . $_SESSION['opc_ed'] . " id='boton_editar" . $encuesta_id . "' onclick='window.open(\"". URL_PAGES."sigra_gst_encuesta.php?eta=".$enc_enc."\")'></button> </td>"
            . "<td style='width:5%'> <button title='Eliminar Encuesta' " . $_SESSION['opc_el'] . "  id='boton_eliminar" . $encuesta_id . "' onclick='activarpopupeliminar(" . $encuesta_id . ")'></button> "
            . "<input type='hidden' id='input_" . $encuesta_id . "' value='" . $encuesta_id . "'></input> </td>"
            . "</tr>";
        }

        echo "     </tbody>
                    </table>";

        echo '<div align="center"><br><br>';
        /* We call the pagination function here to generate Pagination link for us. 
          As you can see I have passed several parameters to the function. */
        echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
        echo '</div>';
    }
}

if ($accion === "nueva_enc") {
    $nombre = $_POST["nombre"];
    $desc_enc = $_POST["desc_enc"];
    $data = mysqli_fetch_array($consulta->nuevaEnc($nombre, $_SESSION['usuarioid'], $desc_enc));
    $consulta->destruir();
    $consulta->destruir2();
    echo json_encode(array("data" => $data));
}

if ($accion === "modulos") {
    $filas = $_POST['filas'];
    $encuesta_id = $_POST['enc_id'];
    $err = array();
    for($i = 1 ; $i <= ($filas-1) ; $i++){
        $nombre = $_POST['nombre_mod'.$i];
        $descripcion = $_POST['desc_mod'.$i];
        $orden = $_POST['orden_mod'.$i];
        $data = $consulta->crearModulo($encuesta_id, $nombre, $descripcion, $orden);
        if($data !== '1'){
            $err[] = $data;
        }
    }
    $consulta->destruir();
    $consulta->destruir2();
    echo count($err)>0 ? json_encode(array("data"=> implode("<br/>", $err))) : json_encode(array("data"=>"n"));
}

if($accion === "carg_mod"){
    
    $encuesta_id = $_POST['enc_id'];
    $modulos = $consulta->modulos($encuesta_id);
    $html = "<option value=''></option>";
    while ($row = mysqli_fetch_array($modulos)) {
        $html .= "<option value='".$row[0]."'>".$row[1]." - ".$row[2]."</option>";
    }
    $consulta->destruir();
    $consulta->destruir2();
    echo $html;
}

if($accion === "traer_modulos"){
    $encuesta_id = $_POST['enc_id'];
    $modulos = $consulta->modulos($encuesta_id);
    $html = "";
    if(mysqli_num_rows($modulos)>0){
        $html = '
                <script>
                    $(document).ready(function(){
                       // $("input:checkbox:not([safari])").checkbox();
                    });
                </script>
                <table style="width: 700px" id="tb_pre" class="tg">
                    <tr style="background-color: #004669">
                        <th  style="color: #FFFFFF">Nombre</th>
                        <th  style="color: #FFFFFF">Descripción</th>
                        <th  style="color: #FFFFFF">Orden</th>
                        <th  style="color: #FFFFFF">Estado</th>
                        <th  style="color: #FFFFFF"></th>
                    </tr>';
        $hid_orden = array();
        while ($row = mysqli_fetch_array($modulos)) {
            $estado = $row['estado_id'] === '1' ? "Activo" : "Inactivo";
            $mod_id = $row['modulo_encuesta_id'];
            $nombre_mod = $row['nombre'];
            $descr_mod = $row['descripcion'];
            $orden_mod = $row['orden'];
            $slA = $estado==='Activo'?'checked':'';
            $slI = $estado==='Inactivo'?'selected="selected"':'';
            $hid_orden[] = $orden_mod;
            $html .= 
                    '<tr>
                        <td style="width: 40%;">
                            <label class="lblm-'.$mod_id.'" id="nom_mod-'.$mod_id.'">'.$nombre_mod.'</label>
                            <input style="width: 100%;display:none" class="edt-'.$mod_id.'" id="nombre_mod-'.$mod_id.'" name="nombre_mod-'.$mod_id.'" type="text" maxlength="25" value="'.$nombre_mod.'"/>
                        </td>
                        <td style="width: 50%;">
                            <p class="lblm-'.$mod_id.'" align="justify" id="d_mod-'.$mod_id.'">'.$descr_mod.'</p>
                            <input style="width: 100%;display:none" class="edt-'.$mod_id.'" id="desc_mod-'.$mod_id.'" name="desc_mod-'.$mod_id.'" type="text" value="'.$descr_mod.'"/>
                        </td>
                        <td align="center" style="width: 10%;">
                            <label class="lblm-'.$mod_id.'" id="ord_mod-'.$mod_id.'">'.$orden_mod.'</label>
                            <input style="width: 100%;display:none" class="nume1 edt-'.$mod_id.'" id="orden_mod-'.$mod_id.'" name="orden_mod-'.$mod_id.'" type="number" value="'.$orden_mod.'"/>
                        </td>
                        <td align="center" style="width: 10%;">
                            <label class="lblm-'.$mod_id.'" id="est_mod-'.$mod_id.'">'.$estado.'</label>
                            	<div class="switch edt-'.$mod_id.'" style="width: 100%;display:none"><input name="est-'.$mod_id.'" type="checkbox" id="est-'.$mod_id.'" '.$slA.' class="cmn-toggle cmn-toggle-yes-no">
                                    <label for="est-'.$mod_id.'" data-on="Activo" data-off="Inactivo"></div>
                        </td>
                        <td align="center" style="width: 10%;">
                            <a class="botones" id="ed_mod-'.$mod_id.'" onclick="editarMod(\''.$mod_id.'\');">Editar</a>
                            <a class="botones" id="g_mod-'.$mod_id.'" style="display:none" onclick="guardaEMod(\''.$mod_id.'\');">Guardar</a>
                            <div id="loading-'.$mod_id.'" style="display:none"></div>
                        </td>
                    </tr>';
        }
                 $html .= '</table><input type="hidden" name="hid_orden_mod" id="hid_orden_mod" value="'.implode(",", $hid_orden).'" />';
    }else {
        $html = "";
    }
    $consulta->destruir();
    $consulta->destruir2();
    echo $html;
}

if($accion === 'crear_pregunta'){
    $modulo_encuesta_id = $_POST['modulo'];
    $enunciado = $_POST['enunciado'];
    $tipo_preg = $_POST['tp_preg'];
    $orden = $_POST['orden_preg'];
    $hid_orden = $_POST['hid_orden'];
    $descripcion = $_POST['desc_preg'];
    $referencia = $_POST['referencia'];
    $url_imgen = $_POST['hid_url_preg'];
    $hipervinculo = $_POST['hipervinculo'];
    $usuario_crea = $_SESSION['usuarioid'];
    if (in_array($orden, explode(',', $hid_orden))){
        echo json_encode(array("data"=>"n"));
    }else {
        $ins = $consulta->crearPregunta($modulo_encuesta_id, $enunciado, $tipo_preg, $orden, $descripcion, $referencia, $url_imgen, $hipervinculo, $usuario_crea);
        $consulta->destruir();
        $consulta->destruir2();
        echo json_encode(array("data"=>"s"));
    }
}

if($accion === "traer_preguntas"){
    $modulo = $_POST['mod_id'];
    $preguntas = $consulta->traerPreguntas($modulo);
    $html = "";
    if(mysqli_num_rows($preguntas)>0){
        $html = '<table style="width: 95%;table-layout:fixed" class="tg">
                    <tr class="bordes" style="background-color: #004669" >
                        <th style="width: 15%;color: #FFFFFF">Enunciado</th>
                        <th style="width: 40%;color: #FFFFFF">Descripción</th>
                        <th style="width: 5%;color: #FFFFFF">Orden</th>
                        <th style="width: 5%;color: #FFFFFF">Estado</th>
                        <th style="width: 8%;color: #FFFFFF"></th>
                    </tr>';
        $hid_orden = array();
        while ($pregunta = mysqli_fetch_array($preguntas)) {
            $preg_id = $pregunta[0];
            $or = $pregunta[4];
            $hid_orden[] = $or;
            $estado = $pregunta['estado_id'] === '1' ? "Activo" : "Inactivo";
            $html .= 
                    '<tr class="bordes">
                        <td style="width: 15%;"><p align="justify" style="width:100%">'.$pregunta[2].'</p></td>
                        <td style="width: 40%;"><p align="justify" style="width:100%">'.$pregunta[5].'</p></td>
                        <td style="width: 5%;" align="center" ><label id="ord_preg-'.$preg_id.'">'.$or.'</label></td>
                        <td style="width: 5%;" align="center" >'.$estado.'</td>
                        <td style="width: 8%;" align="center">
                            <a class="botones" id="editar_preg-'.$preg_id.'" onclick="editarPregunta(\''.$preg_id.'\')">Editar</a>
                                <div id="loading_pr-'.$preg_id.'" style="display:none"></div>
                        </td>
                    </tr>';
        }
                 $html .= '</table><input type="hidden" name="hid_orden_preg" id="hid_orden_preg" value="'.implode(",", $hid_orden).'" />';
    }else {
        $html = "";
    }
    $consulta->destruir();
    $consulta->destruir2();
    echo $html;
}

if($accion==='editar_mod'){
    if($_POST['accion2']==="traer"){
        $modulo_id = $_POST['modulo_id'];
        $modulo = mysqli_fetch_array($consulta->getModulo($modulo_id));
        $consulta->destruir();
        $consulta->destruir2();
        echo json_encode(array("data"=>$modulo));
    }
    if($_POST['accion2']==="guardar"){
        $modulo_id = $_POST['mod_id'];
        $estado_id = $_POST['estado'];
        $nombre = $_POST['nombre_mod'];
        $descripcion = $_POST['desc_mod'];
        $orden_mod = $_POST['orden_mod'];
        $modulo = $consulta->updateModulo($modulo_id, $estado_id, $nombre, $descripcion, $orden_mod);
        if($modulo){
            echo "1";
        }else {
            echo "0";
        }
    }
}
if($accion==='editar_preg'){
    if($_POST['accion2']==="traer"){
        $preg_id = $_POST['preg_id'];
        $pregunta = mysqli_fetch_array($consulta->getPregunta($preg_id));
        echo json_encode(array("data"=>$pregunta));
    }
    if($_POST['accion2']==="guardar"){
        $modulo_encuesta_id = $_POST['modulo'];
        $enunciado = $_POST['enunciado'];
        $tipo_preg = $_POST['tp_preg'];
        $orden = $_POST['orden_preg'];
        $hid_orden = $_POST['hid_orden'];
        $descripcion = $_POST['desc_preg'];
        $referencia = $_POST['referencia'];
        $url_imgen = $_POST['hid_url_preg'];
        $estado = $_POST['estado'];
        $hipervinculo = $_POST['hipervinculo'];
        $preg_id = $_POST['hid_preg_id'];
        $usuario_modifica = $_SESSION['usuarioid'];
//        if (in_array($orden, explode(',', $hid_orden))){
//            echo json_encode(array("data"=>"n"));
//        }else {
            $ins = $consulta->updatePregunta($preg_id, $enunciado, $tipo_preg, $orden, $descripcion, $referencia, $url_imgen, $hipervinculo, $usuario_modifica, $estado);
            if($ins){
              echo json_encode(array("data"=>"s"));
            }else {
              echo json_encode(array("data"=>"n"));                
            }
//        }
    }
}

if($accion === "carg_preg"){
    
    $encuesta_id = $_POST['enc_id'];
    $modulos = $consulta->preguntasXRespuesta($encuesta_id);
    $html = " <optgroup label=''>"
            . "<option value=''></option>";
    $mod = "";
    while ($row = mysqli_fetch_array($modulos)) {
        if($mod !== $row[3]){
            $mod = $row[3];
            $html .= "</optgroup>"
                    . "<optgroup label='$mod'>";
        }
        $html .= "<option tp='$row[4]' value='".$row[0]."'>".$row[1]." - ".$row[2]."</option>";
    }
    $html .= "</optgroup>";
    echo $html;
}

if($accion === "traer_respuestas"){
    $pregunta = $_POST['preg_id'];
    $respuestas = $consulta->traerRespuestas($pregunta);
    $html = "";
    if(mysqli_num_rows($respuestas)>0){
        $html = '<table id="respuestas" style="width: 95%" class="tg">
                    <tr style="background-color: #004669">
                        <th  style="color: #FFFFFF">Enunciado</th>
                        <th  style="color: #FFFFFF">Descripción</th>
                        <th  style="color: #FFFFFF">Valor</th>
                        <th  style="color: #FFFFFF">Imágen</th>
                        <th  style="color: #FFFFFF">Orden</th>
                        <th  style="color: #FFFFFF">Estado</th>
                    </tr>';
        $hid_orden = array();
        $cont = 1;
        while ($respuesta = mysqli_fetch_array($respuestas)) {
            $resp_id = $respuesta['respuesta_id'];
            $enunciado = $respuesta['enunciado'];
            $descripcion = $respuesta['descripcion'];
            $url_imgen = $respuesta['url_imagen'];
            $valor = $respuesta['valor'];
            $or = $respuesta['orden'];
            $hid_orden[] = $or;
            $estado = $respuesta['estado_id'] === '1' ? "Activo" : "Inactivo";
            $slA = $estado==='Activo'?'checked':'';
            $html .= 
                    '<tr>
                        <td style="width: 30%;">
                            <input style="width: 100%;" value="'.$enunciado.'" id="enunc_resp'.$cont.'" name="enunc_resp'.$cont.'" type="text" maxlength="25"  required/></td>
                        <td style="width: 40%;">
                            <textarea style="width: 100%;"  id="desc_resp'.$cont.'" name="desc_resp'.$cont.'" type="text" cols="10" rows="2" >'.$descripcion.'</textarea>
                        </td>
                        <td style="width: 7%;">
                            <input style="width: 100%;" value="'.$valor.'" class="nume3" id="valor_resp'.$cont.'" name="valor_resp'.$cont.'" type="number"/>
                        </td>
                        <td style="width: 10%">
                            <input type="file" id="imagen_resp'.$cont.'" name="imagen_resp'.$cont.'" onchange="subirArchivosResp(this.id)"/>
                            <!--<input type="hidden" id="url_imagen_resp'.$cont.'" value="'.$url_imgen.'" name="url_imagen_resp'.$cont.'" />-->
                            <div id="carg-img_resp'.$cont.'" name="carg-img_resp'.$cont.'" style="display: none">'.$url_imgen.'</div>
                        </td>
                        <td style="width: 8%;">
                            <input style="width: 100%;" class="nume2" value="'.$or.'" id="orden_resp'.$cont.'" name="orden_resp'.$cont.'" type="number" required />
                        </td>
                        <td style="width: 5%;">
                            <div class="switch" style="width: 100%;">
                                <input name="estado_resp'.$cont.'" type="checkbox" id="estado_resp'.$cont.'" '.$slA.' class="cmn-toggle cmn-toggle-yes-no">
                                <label for="estado_resp'.$cont.'" id="lbl_est_resp'.$cont.'" data-on="Activo" data-off="Inactivo"/>
                            </div>
                        </td>
                     <input type="hidden" class="hid_resp" name="hid_resp_id'.$cont.'" id="hid_resp_id'.$cont.'" value="'.$resp_id.'" />
                    </tr>';
            $cont++;
        }
                 $html .= '</table><input type="hidden" name="hid_orden_resp" id="hid_orden_resp" value="'.implode(",", $hid_orden).'" />';
    }else {
        $html = '<table id="respuestas" style="width: 95%" class="tg">
                    <tr style="background-color: #004669">
                        <th  style="color: #FFFFFF">Enunciado</th>
                        <th  style="color: #FFFFFF">Descripción</th>
                        <th  style="color: #FFFFFF">Valor</th>
                        <th  style="color: #FFFFFF">Imágen</th>
                        <th  style="color: #FFFFFF">Orden</th>
                        <th  style="color: #FFFFFF">Estado</th>
                    </tr>
                    <tr>
                        <td style="width: 30%;">
                            <input style="width: 100%;" id="enunc_resp1" name="enunc_resp1" type="text" maxlength="25" required/></td>
                        <td style="width: 40%;">
                            <textarea style="width: 100%;" id="desc_resp1" name="desc_resp1" type="text" cols="10" rows="2" ></textarea>
                        </td>
                        <td style="width: 7%;">
                            <input style="width: 100%;" class="nume3" id="valor_resp1" name="valor_resp1" type="number"/>
                        </td>
                        <td style="width: 10%">
                            <input type="file" id="imagen_resp1" name="imagen_resp1" onchange="subirArchivosResp(this.id)"/>
                            <!--<input type="hidden" id="url_imagen_resp1" name="url_imagen_resp1" />-->
                            <div id="carg-img_resp1" name="carg-img_resp1" style="display: none"></div>
                        </td>
                    <td style="width: 8%;">
                        <input style="width: 100%;" class="nume2" id="orden_resp1" name="orden_resp1" type="number" required />
                    </td>
                    <td style="width: 5%;">
                        <div class="switch" style="width: 100%;">
                            <input name="estado_resp1" type="checkbox" id="estado_resp1" checked="checked" class="cmn-toggle cmn-toggle-yes-no">
                            <label for="estado_resp1" id="lbl_est_resp1" data-on="Activo" data-off="Inactivo"/>
                        </div>
                    </td>
                    <input type="hidden" class="hid_resp" name="hid_resp_id1" id="hid_resp_id1" value="n" />
                    </tr>
                </table>';
    }
    $consulta->destruir();
    $consulta->destruir2();
    echo $html;
}

if ($accion === "respuestas") {
    $filas = $_POST['filas'];
    $preg_id = $_POST['preg_id'];
    $err = array();
    for($i = 1 ; $i <= ($filas-1) ; $i++){
        $enunciado = $_POST['enunc_resp'.$i];
        $descripcion = $_POST['desc_resp'.$i];
        $orden = $_POST['orden_resp'.$i];
        $valor = $_POST['valor_resp'.$i];
        $estado = isset($_POST['estado_resp'.$i]) ? "1" : "2" ;
        $url_imagen = $_POST['url_imagen_resp'.$i];
        if(isset($_POST['hid_resp_id'.$i]) && $_POST['hid_resp_id'.$i]!==''){
            $respuesta_id = $_POST['hid_resp_id'.$i];
            $resp = $consulta->updateRespuesta($respuesta_id, $enunciado, $preg_id, $valor, $url_imagen, $descripcion, $orden, $estado);
            echo $resp;
        }else {
            $resp = $consulta->crearRespuesta($enunciado, $preg_id, $valor, $url_imagen, $descripcion, $orden, $estado);
            echo $resp;
        }
    }
    
//    echo json_encode(array("data"=> $err));
}

if($accion === "traer_pregunta"){
    $pregunta_id = $_POST['preg_id'];
    $preg = mysqli_fetch_array($consulta->getPregunta($pregunta_id));
    echo json_encode(array("data"=>$preg['tipo_preg']));
}

if($accion === "borrar_resp"){
    $respuesta_id = $_POST['resp_id'];
    echo $consulta->eliminarRespuesta($respuesta_id);
}