<?php

//Clase para las transacciones del SIVISAE
//Autor: Ing. Andres Camilo Mendez Aguirre
//Fecha: 25/03/2015

include_once "Bd.php";

class sivisae_consultas extends Bd {

    function validar_numero($campo) {

        if ($campo == '') {
            $mensaje = "Por favor digite los valores requeridos";
            $this->mensaje($mensaje);
        }

//obtengo la longitud del campo

        $lon = strlen($campo);

//recorro el campo

        for ($i = 0; $i < $lon; $i++) {

            if (is_numeric($campo[$i])) {

                print $documento[$i];
            } else {

                $mensaje = "Por favor digite solo números o verifique que no haya espacios";

                $this->mensaje($mensaje);
            }
        }
    }

    //Metodo que retorna la fecha del servidor
    function obtenerFechaServer($opc) {
        //Se valida si el usuario existe
        if ($opc == 1) {
            $sqlC = "select curdate()";
        } else {
            $sqlC = "select now()";
        }
        $resultado = mysql_query($sqlC);
        // Se obtiene el conteo 
        while ($row = mysql_fetch_array($resultado)) {
            $fecha_serv = $row[0];
        }
        return $fecha_serv;
    }

    /*     * **
     * Inicio Metodos Login
     * *** */

    function prueba() {
        $sql = "select cedula from usuario limit 1";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    /*     * **
     * Fin Metodos Login
     * *** */

    function darSubCategoriasReportes($idCategoria) {
        $sql = "select id_reporte_subcategoria as id, descripcion_subcategoria as descripcion, nombre_bd_subcategoria, tabla
			from visae_reporte_subcategoria where estado=1 and fk_reporte_categoria=$idCategoria";
        $resultado = mysql_query($sql);
        $cadena = '<resultado>';
        while ($row = mysql_fetch_array($resultado)) {
            $id = $row[0];
            $descripcion = $row[1];
            $nombre_bd_subcategoria = $row[2];
            $tabla = $row[3];
            $cadena .= '<SubCategoriaReporte>';
            $cadena .= '<id>';
            $cadena .= $id;
            $cadena .= '</id>';
            $cadena .= '<descripcion>';
            $cadena .= $descripcion;
            $cadena .= '</descripcion>';
            $cadena .= '<nombre_bd_subcategoria>';
            $cadena .= $nombre_bd_subcategoria;
            $cadena .= '</nombre_bd_subcategoria>';
            $cadena .= '<tabla>';
            $cadena .= $tabla;
            $cadena .= '</tabla>';
            $cadena .= '</SubCategoriaReporte>';
        }
        $cadena .= '</resultado>';
        return $cadena;
    }

    //Metodo que valida la autenticacion del usuario
    function inicioSesion($usuario, $contrasena) {
        $sql = "CALL SIVISAE.iniciar_sesion('$usuario', '$contrasena');";
        //"select vsu.id_usuario as iduser, vsu.usuario, vsu.perfil, vsu.correo, vsu.nombre, vsu.centro, vsz.codigo_zona, vsper.descripcion as nombre_perfil from visae_siie_usuario vsu, visae_siie_cead vsc, visae_siie_zona vsz, visae_siie_perfil vsper where vsu.centro=vsc.codigocead and vsc.zona=vsz.codigo_zona and usuario=upper('$usuario') and contrasena=md5(upper('$contrasena')) and vsu.estado=1 and vsper.id_perfil=vsu.perfil";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    //Metodo que realiza cambio de contraseña de un usuario
    function cambioPass($usuario, $contrasena_nva, $contrasena_ant) {
        $sql = "CALL SIVISAE.cambio_pass('$usuario', '$contrasena_ant', '$contrasena_nva');";
        //"update visae_siie_usuario set contrasena=md5(upper('$contrasena_nva')) where usuario=upper('$usuario') and contrasena=md5(upper('$contrasena_ant'))  ";
        $resultado = mysql_query($sql) or die(mysql_error() . " " . $sql);
        return $resultado;
    }

    //Metodo que genera una contraseña aleatoria enviada al correo
    function generarPass($usuario) {
        $caracteres = array('!', '#', '$', '%', '&', '/', '(', ')', '=', '?', '¡', '+', '*', '-', '_', '.', '<', '>', '¿');
        $rand_pass = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(48, 57)) . chr(rand(48, 57)) . $caracteres[rand(0, 18)] . $caracteres[rand(0, 18)];
        $sql = "CALL SIVISAE.generar_pass('$usuario', '$rand_pass');";
        $resultado = mysql_query($sql);
        while ($fila = mysql_fetch_array($resultado)) {
            $codigo = $fila[0];
            if ($codigo === '1') {
                return $rand_pass;
            } else {
                return $resultado;
            }
        }
    }

    //Metodo que guarda las noticias del portal
    function guardarNoticia($titulo, $descripcion, $fecha, $link, $perfil) {
        $sql = "INSERT INTO SIVISAE.`noticias` (`titulo_noticia`,`fecha_noticia`,`descripcion_noticia`,`link`,`imagen`,`perfil`,`estado`) VALUES ('$titulo','$fecha','$descripcion','$link','',$perfil, 1)";
        $res = mysql_query($sql);
        //Se retorna el identity
        $noticiaid = mysql_insert_id();
        return $noticiaid;
    }

    //Metodo que guarda la ruta de la imgen de la noticia
    function guardarImagenNoticia($id, $nombre) {
        $sql = "update SIVISAE.noticias set imagen='noticias_imagenes/$nombre' where id_noticia=$id";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    //Metodo que actualiza los cortes de seguimiento del portal
    function actualizaCorteSeguimiento($periodo, $semanas, $fecha_inicio, $fecha_fin, $seguimientos, $id_upd) {
        $sql = "update SIVISAE.corte_seguimiento set periodo_academico_id=$periodo,no_semanas=$semanas,fecha_inicio='$fecha_inicio',fecha_fin='$fecha_fin',iteraciones=$seguimientos where corte_id=$id_upd";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que actualiza las noticias del portal
    function actualizarNoticia($titulo, $descripcion, $fecha_noticia, $link, $id_upd) {
        $sql = "update SIVISAE.noticias set titulo_noticia='$titulo', fecha_noticia='$fecha_noticia', descripcion_noticia='$descripcion',link='$link' where id_noticia=$id_upd";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que guarda los eventos del portal
    function guardarEvento($titulo, $descripcion, $fecha) {
        $sql = "insert into SIVISAE.eventos (titulo_evento,descripcion_evento,fecha_evento,estado) values ('$titulo','$descripcion','$fecha',1)";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que guarda los cortes de seguimiento del portal
    function guardarCorteSeguimiento($periodo, $semanas, $fecha_inicio, $fecha_fin, $seguimientos) {
        $sql = "insert into SIVISAE.corte_seguimiento (periodo_academico_id,no_semanas,fecha_inicio,fecha_fin,iteraciones) values ($periodo, $semanas, '$fecha_inicio', '$fecha_fin', $seguimientos )";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que verifica que no se repitan los login de usuarios
    function validarLoginUsuario($login) {
        //Se valida si el usuario existe
        $sqlC = "select count(*) from SIVISAE.usuario where login like '%$login%'";
        $resultado = mysql_query($sqlC);
        // Se obtiene el conteo 
        while ($row = mysql_fetch_array($resultado)) {
            $contador = $row[0];
        }
        return $contador;
    }

//Metodo para la tranasaccionalidad de la creacion de usuarios
    function crearUsuario($cedula, $nombre, $login, $correo, $telefono, $celular, $skype, $sede) {
        //Se inserta el usuario
        $sql = "INSERT INTO SIVISAE.usuario (cedula, nombre, login, correo, celular, telefono, skype, estado_estado_id, fecha_creacion, cambio_pass, sede) VALUES
                ('$cedula', '$nombre', '$login', '$correo', '$celular', '$telefono', '$skype', '1', CURRENT_TIMESTAMP, 1, $sede);";
        $res = mysql_query($sql);
        //Se retorna el identity
        $usuarioid = mysql_insert_id();
        return $usuarioid;
    }

    //Metodo para la tranasaccionalidad de la actualizacion del horario de usuarios
    function actualizarHorarioUsuario($dia, $hora_ini, $hora_fin, $jor_ini, $jor_fin, $id_user, $perfil) {
        $tabla = "";
        if ($perfil === "5" || $perfil === "7") {
            $tabla = "consejero";
        } else {
            $tabla = "monitor";
        }

        $sql2 = "UPDATE `$tabla` c SET c.`$dia`='DE $hora_ini $jor_ini A $hora_fin $jor_fin' WHERE c.`usuario_usuario_id`=$id_user";

        //echo $sql2;

        mysql_query($sql2);
        if (mysql_affected_rows() > 0) {
            $rta = "1";
            $this->registrarAccion($id_user, "ACTUALIZÓ HORARIO USUARIO ID: " . $id_user, "EXISTOSO");
        } else {
            $rta = "2";
            $this->registrarAccion($id_user, "ACTUALIZÓ HORARIO USUARIO: " . $id_user, "FALLO");
        }

        return $rta;
    }

    //Metodo para la tranasaccionalidad de la eliminacion del horario de usuarios
    function eliminarHorarioUsuario($dia, $id_user, $perfil) {
        $tabla = "";
        if ($perfil === "5" || $perfil === "7") {
            $tabla = "consejero";
        } else {
            $tabla = "monitor";
        }

        $sql2 = "UPDATE `$tabla` c SET c.`$dia`='' WHERE c.`usuario_usuario_id`=$id_user";

        //echo $sql2;

        mysql_query($sql2);
        if (mysql_affected_rows() > 0) {
            $rta = "1";
            $this->registrarAccion($id_user, "ACTUALIZÓ HORARIO USUARIO ID: " . $id_user, "EXISTOSO");
        } else {
            $rta = "2";
            $this->registrarAccion($id_user, "ACTUALIZÓ HORARIO USUARIO: " . $id_user, "FALLO");
        }

        return $rta;
    }

    //Metodo para la tranasaccionalidad de la actualizacion del perfil de usuarios
    function actualizarPerfilUsuario($correo, $telefono, $celular, $skype, $f_nac, $id_upd) {

        //Se actualiza el usuario
        $sql2 = "update SIVISAE.usuario set correo='$correo', telefono=$telefono, celular=$celular, skype='$skype', fecha_nacimiento='$f_nac', actualiza_datos=0 where usuario_id=$id_upd";
        //echo $sql2;
        mysql_query($sql2);
        if (mysql_affected_rows() > 0) {
            $rta = "1";
            $this->registrarAccion($id_upd, "ACTUALIZÓ PERFIL USUARIO ID: " . $id_upd, "EXISTOSO");
        } else {
            $rta = "2";
            $this->registrarAccion($id_upd, "ACTUALIZÓ PERFIL USUARIO: " . $id_upd, "FALLO");
        }

        return $rta;
    }

//Metodo para la tranasaccionalidad de la actualizacion de usuarios
    function actualizarUsuario($cedula, $nombre, $correo, $telefono, $celular, $skype, $sede, $id_upd, $id_per_upd, $perfil, $usuarioid) {

        //Se actualiza el usuario perfil
        $sql = "update SIVISAE.usuario_perfil set perfil_perfil_id=$perfil where usuario_perfil_id=$id_per_upd";
        mysql_query($sql);

        //Se actualiza el usuario
        $sql2 = "update SIVISAE.usuario set cedula='$cedula', nombre='$nombre', correo='$correo', telefono=$telefono, celular=$celular, skype='$skype', sede=$sede, fecha_creacion=CURRENT_TIMESTAMP where usuario_id=$id_upd";
        mysql_query($sql2);
        if (mysql_affected_rows() > 0) {
            $rta = "Se actualizó la informacion del usuario correctamente.";
            $this->registrarAccion($usuarioid, "ACTUALIZAR USUARIO ID: " . $id_upd, "EXISTOSO");
        } else {
            $rta = "No se pudo actualizar la información del usuario, por favor intente nuevamente.";
            $this->registrarAccion($usuarioid, "ACTUALIZAR USUARIO: " . $id_upd, "FALLO");
        }

        return $rta;
    }

//Metodo para la tranasaccionalidad de la actualizacion de eventos
    function actualizarEvento($titulo, $descripcion, $fecha_evento, $id_upd, $usuarioid) {
        //Se actualiza el evento
        $sql = "update SIVISAE.eventos set titulo_evento='$titulo', descripcion_evento='$descripcion', fecha_evento='$fecha_evento', estado=1 where id_evento=$id_upd";
        mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $rta = "Se actualizó la informacion del evento correctamente.";
            $this->registrarAccion($usuarioid, "ACTUALIZAR EVENTO ID: " . $id_upd, "EXISTOSO");
        } else {
            $rta = "No se pudo actualizar la información del evento, por favor intente nuevamente.";
            $this->registrarAccion($usuarioid, "ACTUALIZAR EVENTO: " . $id_upd, "FALLO");
        }
        return $rta;
    }

//Metodo para la tranasaccionalidad de la eliminacion de usuarios
    function eliminarUsuario($id_upd, $id_per_upd, $usuarioid) {
        // eliminar la relacion perfil usuario
        $sql = "update SIVISAE.usuario_perfil set estado_estado_id=3 where usuario_perfil_id=$id_per_upd";
        mysql_query($sql);
        $banAct = mysql_affected_rows();

        if ($banAct > 0) {
            //eliminar el usuario
            $sql2 = "update usuario set estado_estado_id=3 where usuario_id=$id_upd";
            mysql_query($sql2);
            $banAct = mysql_affected_rows();
            if ($banAct > 0) {
                $rta = "Se eliminó el usuario correctamente.";
                $this->registrarAccion($usuarioid, "ELIMINAR USUARIO: " . $id_upd, "EXITOSO");
            } else {
                $rta = "El usuario no pudo ser eliminado, por favor intente nuevamente.";
                $this->registrarAccion($usuarioid, "ELIMINAR USUARIO: " . $id_upd, "FALLO");
            }
        } else {
            $rta = "No se pudo eliminar los permisos de perfil, por favor intente nuevamente.";
            $this->registrarAccion($usuarioid, "ELIMINAR PERMISOS PERFIL USUARIO: " . $id_upd, "FALLO");
        }

        return $rta;
    }

//Metodo para la tranasaccionalidad de la eliminacion de noticias
    function eliminarNoticia($id_upd, $usuarioid) {
        $sql = "update SIVISAE.noticias set estado=3 where id_noticia=$id_upd";
        mysql_query($sql);
        $banAct = mysql_affected_rows();
        if ($banAct > 0) {
            $rta = "Se eliminó la noticia correctamente.";
            $this->registrarAccion($usuarioid, "ELIMINAR NOTICIA: " . $id_upd, "EXITOSO");
        } else {
            $rta = "La noticia no pudo ser eliminado, por favor intente nuevamente.";
            $this->registrarAccion($usuarioid, "ELIMINAR NOTICIA: " . $id_upd, "FALLO");
        }
        return $rta;
    }

//Metodo para la tranasaccionalidad de la eliminacion de seguimientos
    function eliminarSeguimiento($id_eliminar, $usuarioid) {
        $sql = "update SIVISAE.eliminacion_seguimientos set estado_id=3, fecha_eliminacion=CURRENT_TIMESTAMP, usuario_elimina=$usuarioid where eliminacion_id=$id_eliminar";

        mysql_query($sql);
        $banAct = mysql_affected_rows();
        if ($banAct > 0) {
            //Desencadena en la actualizacion del numero de seguimientos realizados. Restar ese seguimiento en la asignacion_estudiante
            $sql = "UPDATE SIVISAE.`auditor_estudiante` SET `cant_seguimientos`=`cant_seguimientos`-1 WHERE `auditor_estudiante_id` IN (
                    SELECT * FROM (
                    SELECT 
                    ae.`auditor_estudiante_id`
                    FROM SIVISAE.`auditor_estudiante` ae, SIVISAE.`eliminacion_seguimientos` es, SIVISAE.`seguimiento` s
                    WHERE s.`seguimiento_id`=es.`seguimiento_id`
                    AND ae.`auditor_estudiante_id`=s.`auditor_estudiante_id`
                    AND es.`eliminacion_id`=$id_eliminar) AS a
                    )";

            mysql_query($sql);

            // Se actualiza el estado del seguimiento
            $sql = "UPDATE SIVISAE.`seguimiento` SET `estado`=3 WHERE `seguimiento_id` in (SELECT * FROM (
                    SELECT 
                    s.`seguimiento_id`
                    FROM `auditor_estudiante` ae, `eliminacion_seguimientos` es, `seguimiento` s
                    WHERE s.`seguimiento_id`=es.`seguimiento_id`
                    AND ae.`auditor_estudiante_id`=s.`auditor_estudiante_id`
                    AND es.`eliminacion_id`=$id_eliminar) AS a)";

            mysql_query($sql);

            $rta = "Se eliminó el seguimiento correctamente.";
            $this->registrarAccion($usuarioid, "ELIMINAR SEGUIMIENTO: " . $id_eliminar, "EXITOSO");
        } else {
            $rta = "El seguimiento no pudo ser eliminado, por favor intente nuevamente.";
            $this->registrarAccion($usuarioid, "ELIMINAR SEGUIMIENTO: " . $id_eliminar, "FALLO");
        }
        return $rta;
    }

//Metodo para la tranasaccionalidad de la eliminacion de eventos
    function eliminarEvento($id_upd, $usuarioid) {
        $sql = "update SIVISAE.eventos set estado=3 where id_evento=$id_upd";
        mysql_query($sql);
        $banAct = mysql_affected_rows();
        if ($banAct > 0) {
            $rta = "Se eliminó el evento correctamente.";
            $this->registrarAccion($usuarioid, "ELIMINAR EVENTO: " . $id_upd, "EXITOSO");
        } else {
            $rta = "El evento no pudo ser eliminado, por favor intente nuevamente.";
            $this->registrarAccion($usuarioid, "ELIMINAR EVENTO: " . $id_upd, "FALLO");
        }
        return $rta;
    }

    function rollbackUsuario($usuarioid, $perfilid, $usuario_log) {
        //Se elimina la relacion de perfil
        $sql = "update SIVISAE.usuario_perfil set estado_estado_id=3 where usuario_perfil_id=$perfilid";
        mysql_query($sql);
        //Se elimina el usuario
        $sql = "update SIVISAE.usuario set estado_estado_id=3 where usuario_id=$usuarioid";
        mysql_query($sql);
        //Se hace la auditoria de la tabla
        $this->registrarAccion($usuario_log, 'CREAR USUARIO', 'FALLO ENVIO CORREO CREANDO USUARIO: ' . $usuarioid);
    }

//Metodo para la tranasaccionalidad de la eliminacion de perfiles
    function eliminarPerfil($id_upd, $usuarioid) {
        //se listan los usuarios asociados al perfil
        $sql = "select SIVISAE.usuario_usuario_id from SIVISAE.usuario_perfil where perfil_perfil_id=$id_upd";
        $resUsu = mysql_query($sql);
        $banAct = mysql_affected_rows();
        if ($banAct > 0) {
            $listUsuarios = "";
            $cont = 0;
            while ($row = mysql_fetch_array($resUsu)) {
                if ($cont == 0)
                    $listUsuarios .= $row[0];
                else
                    $listUsuarios = $listUsuarios . " , " . $row[0];
                $cont++;
            }
            //se desactivan los usuarios asociados al perfil  
            $sqlUs = "update SIVISAE.usuario set estado_estado_id=3 where usuario_id in ($listUsuarios)";
            mysql_query($sqlUs);
            //se desactiva la asociacion del perfil y usuarios
            $sqlUs = "update SIVISAE.usuario_perfil set estado_estado_id=3 where perfil_perfil_id=$id_upd";
            mysql_query($sqlUs);
        }

        // se desactivan las opciones del perfil
        $sqlPer = "update SIVISAE.perfil_opcion set estado_estado_id=3 where perfil_perfil_id=$id_upd";
        mysql_query($sqlPer);
        //se desactiva el perfil
        $sqlPer = "update SIVISAE.perfil set estado_estado_id=3 where perfil_id=$id_upd";
        mysql_query($sqlPer);

        //se hace auditoria
        $this->registrarAccion($usuarioid, "ELIMINAR PERFIL: " . $id_upd, "EXITOSO");

        $rta = "El perfil se eliminó correctamente.";
        return $rta;
    }

    function crearUsuarioPerfil($usuarioid, $perfilid) {
        $sql = "INSERT INTO SIVISAE.usuario_perfil (usuario_usuario_id, perfil_perfil_id, estado_estado_id) VALUES "
                . "($usuarioid, $perfilid, 1);";
        $resultado = mysql_query($sql);
        $usuarioPerfilId = mysql_insert_id();
        return $usuarioPerfilId;
    }

    function crearPerfil($nombre) {
        $sqlinsert1 = "INSERT INTO SIVISAE.perfil (descripcion, estado_estado_id) VALUES ('$nombre', 1);";
        $resultado = mysql_query($sqlinsert1);
        $perfilid = mysql_insert_id();

        return $perfilid;
    }

    function crearConsejero($usuario_id, $perfil_id) {
        $con = $perfil_id !== '5' ? '1' : '0';
        $sql = "INSERT INTO SIVISAE.consejero (usuario_usuario_id, estado_estado_id, con_general) 
                VALUES ( $usuario_id, 1, $con)";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function crearAuditor($usuario_id, $perfil_id) {
        $aud = $perfil_id !== '2' ? '1' : '0';
        $sql = "INSERT INTO SIVISAE.auditor (usuario_usuario_id, estado_estado_id, aud_general) 
                VALUES ( $usuario_id, 1, $aud)";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function updatePerfil($nombre, $perfil_id) {
        $sql = "UPDATE SIVISAE.perfil SET descripcion = '$nombre' WHERE perfil_id = $perfil_id;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function crearPerfilOpcion($perfilid, $opcion, $permisos, $filtro_zona, $filtro_escuela) {
        $sql2 = "INSERT INTO SIVISAE.perfil_opcion (perfil_perfil_id, opcion_opcion_id, opcion_crear, opcion_editar, opcion_eliminar, estado_estado_id, filtro_escuela, filtro_zona) VALUES 
                ($perfilid, $opcion, $permisos, 1, $filtro_escuela, $filtro_zona);";
        $resultado = mysql_query($sql2);

        return $resultado;
    }

    function existePerfilOpcion($perfilid) {
        $validar = "SELECT perfil_perfil_id, opcion_opcion_id, CONCAT(opcion_crear, ', ', opcion_editar, ', ', opcion_eliminar) AS permisos \n"
                . "FROM SIVISAE.perfil_opcion \n"
                . "WHERE perfil_perfil_id = $perfilid ;";
        $resultado = mysql_query($validar);
        return $resultado;
    }

    function updatePerfilOpcion($perfilid, $opcion, $permisos, $accion, $filtro_zona, $filtro_escuela) {

        if ($accion == 'u') {
            $perm = split(", ", $permisos);
            $sql = "UPDATE SIVISAE.perfil_opcion SET opcion_crear = $perm[0], opcion_editar = $perm[1], opcion_eliminar = $perm[2], filtro_zona =$filtro_zona, filtro_escuela=$filtro_escuela "
                    . " WHERE perfil_perfil_id = $perfilid AND opcion_opcion_id = $opcion;";
        }
        if ($accion == 'd') {
            $sql = "DELETE FROM SIVISAE.perfil_opcion WHERE perfil_perfil_id = $perfilid AND opcion_opcion_id IN ($opcion);";
        }
        if ($accion == 'i') {
            $sql = "INSERT INTO perfil_opcion (perfil_perfil_id, opcion_opcion_id, opcion_crear, opcion_editar, opcion_eliminar, estado_estado_id, filtro_zona, filtro_escuela) VALUES 
                ($perfilid, $opcion, $permisos, 1, $filtro_zona, $filtro_escuela);";
        }
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function opcionesPerfil() {
        $sql = "SELECT opcion_id, descripcion, url FROM SIVISAE.opcion WHERE estado_estado_id = 1";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function permisos($mod, $per) {
        $sql = "select opcion_crear,opcion_editar,opcion_eliminar from SIVISAE.perfil_opcion where opcion_opcion_id=$mod and perfil_perfil_id=$per";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function perfiles() {
        $sql = "SELECT perfil_id, descripcion FROM SIVISAE.perfil WHERE estado_estado_id = 1 order by perfil_id asc";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function traerSedes() {
        $sql = "select codigo, descripcion from SIVISAE.cead where estado_estado_id=1 order by descripcion asc";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function traerCentrosDirectorio($zona) {
        $sql = "SELECT c.`codigo`,c.`descripcion`, c.`direccion`,c.`telefono`,c.`correo_director`,c.`director`,c.`correo_centro`, UPPER( z.`descripcion`) AS zona FROM cead c, zona z WHERE`cead_id` NOT IN (148,149) AND c.`estado_estado_id` = 1 AND z.`zona_id`= c.`zona_zona_id` and `zona_zona_id`=$zona order by descripcion asc";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function consultarConsejerosCentro($centro) {
        $sql = "SELECT u.`nombre`, u.`correo`, u.`skype`, c.lunes, c.martes, c.miercoles, c.jueves, c.viernes, c.sabado FROM usuario u, consejero c WHERE u.sede=$centro AND u.`estado_estado_id`=1 AND c.`usuario_usuario_id`=u.`usuario_id`";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function registrarAccion($usuario_id, $accion, $valor) {
        $sql = "INSERT INTO SIVISAE.auditoria_usuario (usuario_id, accion, valor) VALUES "
                . "($usuario_id,'$accion','$valor');";
        $resultado = mysql_query($sql); // or die("USUARIO: $usuario_id, ".mysql_error()."\n ". $sql);
        return $resultado;
    }

    function traeUsrId($login) {
        $sql = "SELECT usuario_id, nombre, correo FROM SIVISAE.usuario WHERE login = '$login' AND estado_estado_id = 1 LIMIT 1;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function menu($usuarioid) {
        $sql = "SELECT 
                    DISTINCT m.menu_id AS menuid, m.descripcion AS descripcion, m.dashboard
                FROM 
                    usuario_perfil up 
                    INNER JOIN perfil_opcion po ON po.perfil_perfil_id = up.perfil_perfil_id 
                    INNER JOIN opcion o ON o.opcion_id = po.opcion_opcion_id 
                    INNER JOIN menu m ON m.menu_id = o.menu_menu_id 
                WHERE 
                    up.usuario_usuario_id = $usuarioid AND up.estado_estado_id = 1 and m.estado_estado_id=1
                ORDER BY m.menu_id ASC ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function opciones($usuarioid, $menuid) {
        $sql = "SELECT 
                    o.descripcion, o.url, o.opcion_id
                FROM 
                    usuario_perfil up 
                    INNER JOIN perfil_opcion po ON po.perfil_perfil_id = up.perfil_perfil_id 
                    INNER JOIN opcion o ON o.opcion_id = po.opcion_opcion_id 
                WHERE 
                    up.usuario_usuario_id = $usuarioid AND o.menu_menu_id = $menuid AND up.estado_estado_id = 1 AND o.estado_estado_id = 1  
                ORDER BY o.opcion_id ASC ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function auditores() {
        $sql = "SELECT a.auditor_id AS id, LOWER(u.nombre)  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 2 THEN 'AUDITOR' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 3 THEN 'LIDER NACIONAL DE AUDITORES' 
                        ELSE CASE WHEN up.perfil_perfil_id = 4 THEN 'GESTOR DE AUDITORES' END 
                        END
                    END AS tp_auditor
                FROM SIVISAE.auditor a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE u.estado_estado_id = 1  
                ORDER BY u.nombre ASC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function consejeros() {
        $sql = "SELECT a.consejero_id AS id, LOWER(u.nombre)  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 5 THEN 'CONSEJERO' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 6 THEN 'LIDER NACIONAL DE CONSEJERÍA' 
                        ELSE CASE WHEN up.perfil_perfil_id = 7 THEN 'GESTOR DE CONSEJERÍA' END 
                        END
                    END AS tp_consejero
                FROM SIVISAE.consejero a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE u.estado_estado_id = 1  
                ORDER BY u.nombre ASC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function monitores() {
        $sql = "SELECT a.consejero_id AS id, LOWER(u.nombre)  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 9 THEN 'MONITOR' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 8 THEN 'LIDER NACIONAL DE MONITORES' 
                        ELSE CASE WHEN up.perfil_perfil_id = 7 THEN 'GESTOR DE CONSEJERÍA' END 
                        END
                    END AS tp_consejero
                FROM SIVISAE.monitor a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE u.estado_estado_id = 1  
                ORDER BY u.nombre ASC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function asesor_bienestar() {
        $sql = "SELECT a.consejero_id AS id, LOWER(u.nombre)  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 9 THEN 'MONITOR' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 8 THEN 'LIDER NACIONAL DE MONITORES' 
                        ELSE CASE WHEN up.perfil_perfil_id = 7 THEN 'GESTOR DE CONSEJERÍA' END 
                        END
                    END AS tp_consejero
                FROM SIVISAE.monitor a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE u.estado_estado_id = 1  
                ORDER BY u.nombre ASC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function funcionarios($usuarioid) {
        $sql = "SELECT u.usuario_id, LOWER(u.nombre) AS nombre, 
                    CASE up.perfil_perfil_id 
			WHEN 2 THEN 'Auditor' 
			WHEN 3 THEN 'Lider Nacional de Auditores' 
                        WHEN 4 THEN 'Gestor de Auditores' 
                        WHEN 1 THEN 'Administrador'
                    END AS tp_auditor
                FROM SIVISAE.usuario u 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE u.usuario_id NOT IN (1, $usuarioid) AND u.estado_estado_id = 1
                ORDER BY u.nombre ASC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function semanasCortesSeguimiento($periodo) {
        $sql = "SELECT `iteraciones` FROM SIVISAE.`corte_seguimiento` WHERE `periodo_academico_id`=$periodo";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function auditoresPermisos($id_auditores) {
        $sql = "SELECT a.auditor_id AS id, u.nombre  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 2 THEN 'AUDITOR' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 3 THEN 'LIDER NACIONAL DE AUDITORES' 
                        ELSE CASE WHEN up.perfil_perfil_id = 4 THEN 'GESTOR DE AUDITORES' END 
                        END
                    END AS tp_auditor
                FROM SIVISAE.auditor a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE u.usuario_id = $id_auditores
                ORDER BY u.nombre ASC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function cantEstudiantesNoAsignadosConsejeria($auditor, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT COUNT(e.estudiante_id)  
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id  ";

        if ($auditor == 'T') {
            $sql .=" WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.consejero_estudiante) "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE ae.consejero_consejero_id = $auditor "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " and m.numero_matriculas = 1 ;";

        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function cantEstudiantesNoAsignados($auditor, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT COUNT(e.estudiante_id)  
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id  ";

        if ($auditor == 'T') {
            $sql .=" WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE ae.auditor_auditor_id = $auditor "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " and m.numero_matriculas = 1 ;";

        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function filtroCantEstudiantesNoAsignadosConsejeria($auditor, $filtro, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT COUNT(e.estudiante_id)  
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor === 'T') {
            $sql .=" WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND 
                e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.consejero_estudiante) "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND ae.consejero_consejero_id=  $auditor "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }
        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= " and m.numero_matriculas = 1 ;";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtroCantEstudiantesNoAsignados($auditor, $filtro, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT COUNT(e.estudiante_id)  
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor === 'T') {
            $sql .=" WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND 
                e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND ae.auditor_auditor_id = $auditor "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }
        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= " and m.numero_matriculas = 1 ;";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function estudiantesNoAsignadosConsejeria($auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion, p.codigo, p.descripcion, p.escuela, pa.descripcion 
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor == 'T') {
            $sql .=" WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.consejero_estudiante) "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE ae.consejero_consejero_id = $auditor "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " and m.numero_matriculas = 1 ORDER BY c.descripcion, p.escuela, p.descripcion, e.nombre ASC"
                . " LIMIT $page_position, $item_per_page; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function estudiantesNoAsignados($auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion, p.codigo, p.descripcion, p.escuela, pa.descripcion 
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor == 'T') {
            $sql .=" WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE ae.auditor_auditor_id = $auditor "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " and m.numero_matriculas = 1 ORDER BY c.descripcion, p.escuela, p.descripcion, e.nombre ASC"
                . " LIMIT $page_position, $item_per_page; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarEstudiantesNoAsignadosConsejeria($auditor, $page_position, $item_per_page, $filtro, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion, p.codigo, p.descripcion, p.escuela, pa.descripcion 
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor === 'T') {
            $sql .=" WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND 
                e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.consejero_estudiante) "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND ae.consejero_consejero_id = $auditor "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }
        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " and m.numero_matriculas = 1 ORDER BY c.descripcion, p.escuela, p.descripcion, e.nombre ASC "
                . " LIMIT $page_position, $item_per_page; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarEstudiantesNoAsignados($auditor, $page_position, $item_per_page, $filtro, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion, p.codigo, p.descripcion, p.escuela, pa.descripcion 
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor === 'T') {
            $sql .=" WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND 
                e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND ae.auditor_auditor_id = $auditor "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) AND p.tipo_programa_tipo_programa_id in (1,2,7) ";
        }
        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " and m.numero_matriculas = 1 ORDER BY c.descripcion, p.escuela, p.descripcion, e.nombre ASC "
                . " LIMIT $page_position, $item_per_page; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna las noticias del portal
    function consultarNoticias($pf) {

        if ($pf == 1) {
            $sql = "select id_noticia,titulo_noticia,fecha_noticia,descripcion_noticia,link,imagen from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 ";
        } else if ($pf == 2 || $pf == 3 || $pf == 4) {
            $sql = "select id_noticia,titulo_noticia,fecha_noticia,descripcion_noticia,link,imagen from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 and perfil in (0,3) ";
        } else if ($pf == 5 || $pf == 6 || $pf == 7) {
            $sql = "select id_noticia,titulo_noticia,fecha_noticia,descripcion_noticia,link,imagen from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 and perfil in (0,2,5) ";
        } else if ($pf == 8 || $pf == 9) {
            $sql = "select id_noticia,titulo_noticia,fecha_noticia,descripcion_noticia,link,imagen from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 and perfil in (0,5) ";
        } else if ($pf == 10 || $pf == 11) {
            $sql = "select id_noticia,titulo_noticia,fecha_noticia,descripcion_noticia,link,imagen from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 and perfil in (0,4) ";
        } else if ($pf == 12 || $pf == 13 || $pf == 14 || 16) {
            $sql = "select id_noticia,titulo_noticia,fecha_noticia,descripcion_noticia,link,imagen from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 and perfil in (0,6) ";
        } else {
            $sql = "select id_noticia,titulo_noticia,fecha_noticia,descripcion_noticia,link,imagen from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 and perfil in (0) ";
        }

        $sql.=" order by n.fecha_noticia desc limit 3";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna los eventos del año en curso del portal
    function consultarEventos() {
        $sql = "select id_evento,titulo_evento,descripcion_evento,year(fecha_evento), month(fecha_evento), day(fecha_evento), hour(fecha_evento), fecha_evento from eventos order by fecha_evento asc";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function periodos() {
        $sql = "SELECT periodo_academico_id, LOWER(descripcion) FROM SIVISAE.periodo_academico WHERE estado_estado_id = 1  ORDER BY codigo_peraca DESC ;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function periodos_administrador() {
        $sql = "SELECT periodo_academico_id, LOWER(descripcion) FROM SIVISAE.periodo_academico WHERE estado_estado_id in (1,2) ORDER BY codigo_peraca DESC ;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function cohortes_graduados() {
        $sql = " SELECT DISTINCT `ANIO`, anio FROM SIGRA.`tmp_titulos` ORDER BY ANIO; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function zonas($filtro) {
        if ($filtro === 'T' || $filtro === 'SEDE NACIONAL - JCM') {//Sede nacional
            $sql = "SELECT zona_id, LOWER(descripcion) FROM SIVISAE.zona WHERE estado_estado_id = 1 ORDER BY descripcion ASC;";
        } else {
            $sql = "SELECT z.zona_id, LOWER(z.descripcion) FROM SIVISAE.zona z, SIVISAE.cead c WHERE z.estado_estado_id = 1 AND c.`zona_zona_id`=z.`zona_id` AND c.`descripcion`='$filtro' ORDER BY z.descripcion ASC;";
        }
//        echo $sql; 
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function escuelas() {
        $sql = "SELECT DISTINCT(LOWER(escuela)) FROM SIVISAE.programa WHERE LOWER(escuela) NOT LIKE '%vicerrectoría%' AND LOWER(escuela) NOT LIKE '%gerencia%'  ORDER BY descripcion ASC;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function escuelas_proyectos() {
        $sql = "SELECT DISTINCT(LOWER(escuela)), escuela FROM SIVISAE.programa WHERE LOWER(escuela) NOT LIKE '%vicerrectoría%' AND LOWER(escuela) NOT LIKE '%gerencia%'  ORDER BY descripcion ASC;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function categoriasAtencion($modulo) {

        $eje = "";
        if ($modulo === '12') {
            $eje = "eje_atencion in (1)";
        } else if ($modulo === '15') {
            $eje = "eje_atencion in (2)";
        } else if ($modulo === '23') {
            $eje = "eje_atencion in (3)";
        } else if ($modulo === '8') {
            $eje = "eje_atencion in (3)";
        } else if ($modulo === '7') {
            $eje = "eje_atencion in (2)";
        } else if ($modulo === '4') {
            $eje = "eje_atencion in (1)";
        }

        $sql = "SELECT `id_categoria`, `descripcion`, 0 as contador FROM  `atencion_categorias` WHERE `estado_id`=1 and $eje ORDER BY `descripcion` ASC";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function ceadSegunZona($zona, $filtro_zonas, $centro_usuario) {
        $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE ";
        if ($zona != 'T') {
            $sql.= " zona_zona_id IN ($zona) AND ";
        }

        if ($filtro_zonas === '3') {
            $sql.= " descripcion IN ('$centro_usuario') AND ";
        }

        if ($filtro_zonas === '2') {
            $sql.= " zona_zona_id IN ( SELECT `zona_zona_id` FROM `cead` WHERE `descripcion`= '$centro_usuario') AND ";
        }

        $sql.="estado_estado_id = 1 "
                . " ORDER BY descripcion ASC;";
        //echo $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function ceadSegunZonas($zona) {
        $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE";
        if ($zona != 'T') {
            $sql.= " zona_zona_id IN ($zona) AND ";
        }

        $sql.=" estado_estado_id = 1 "
                . " ORDER BY descripcion ASC;";
        //echo $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function ceadPrecargaZona($filtro) {

        if ($filtro === 'SEDE NACIONAL - JCM') {
            $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE estado_estado_id = 1 ORDER BY descripcion ASC; ";
        } else {
            $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE estado_estado_id = 1  AND `zona_zona_id` IN (SELECT `zona_zona_id` FROM cead WHERE `descripcion`='$filtro') ORDER BY descripcion ASC;";
        }

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function programaSegunEscuelaSIGRA($escuela) {
        $sql = "SELECT codigo, LOWER(descripcion) FROM SIGRA.programa WHERE ";
        if ($escuela != 'T') {
            $sql.= "  escuela IN ('$escuela') AND ";
        }
        $sql.=" tipo_programa_tipo_programa_id IN (1,2,4,5,7) ORDER BY descripcion ASC;";
//        $resultado = mysql_query($sql);
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function programaSegunEscuela($escuela, $filtro_escuelas, $pro_usuario) {
        $sql = "SELECT programa_id, codigo, LOWER(descripcion) FROM SIVISAE.programa WHERE ";
        if ($escuela != 'T') {
            $sql.= "  escuela IN ('$escuela') AND ";
        }



        if ($filtro_escuelas === '3') {
            if ($pro_usuario != '0') {
                $sql.= " codigo IN ($pro_usuario) AND ";
            }
        }

        if ($filtro_escuelas === '2') {
            $sql.= " escuela IN (SELECT `escuela` FROM `programa` WHERE `codigo`=$pro_usuario) AND ";
        }


        $sql.=" tipo_programa_tipo_programa_id IN (1,2,7) ORDER BY descripcion ASC;";
        $resultado = mysql_query($sql);
        //echo $sql;
        return $resultado;
    }

    function programaSegunEscuelaAtencion($escuela) {
        $sql = "SELECT programa_id, codigo, LOWER(descripcion) FROM SIVISAE.programa WHERE ";
        if ($escuela != 'T') {
            $sql.= "  escuela IN ('$escuela') AND ";
        }
        $sql.=" tipo_programa_tipo_programa_id IN (1,2,7,5) ORDER BY descripcion ASC;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    //Metodo para consultar la cantidad de registros de una tabla
    function consultarHorarios($id_usuario, $perfil) {
        $tabla = "";
        if ($perfil === "5" || $perfil === "7") {
            $tabla = "consejero";
        } else {
            $tabla = "monitor";
        }

        $sql = "SELECT c.* FROM $tabla c, usuario u
                WHERE c.`usuario_usuario_id`=u.`usuario_id`
                AND u.`usuario_id`=$id_usuario";
        //echo $sql;
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

//Metodo para consultar la cantidad de registros de una tabla
    function cantRegistros($sql) {
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function ceadSegunZonaSIGRA($zona) {
        $sql = "SELECT c.codigo, LOWER(c.descripcion) FROM SIGRA.cead c inner join SIGRA.zona z on z.zona_id = c.zona_zona_id WHERE ";
        if ($zona != 'T') {
            $sql.= " nomenclatura IN ('$zona') AND ";
        }
        $sql.="c.estado_estado_id = 1 "
                . " ORDER BY c.descripcion ASC;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function ceadSegunZona_Atenciones($zona) {
        $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE ";
        if ($zona != 'T') {
            $sql.= " zona_zona_id IN ($zona) AND ";
        }
        $sql.="estado_estado_id = 1 "
                . " ORDER BY descripcion ASC;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de usuarios
    function traerUsuarios($page_position, $item_per_page) {
        $sql = "select u.cedula, u.nombre, u.fecha_expiracion, u.fecha_creacion, u.telefono, u.correo, u.ultimo_ing, c.descripcion, e.descripcion, u.usuario_id, u.login, up.perfil_perfil_id, c.cead_id, u.celular, u.skype , per.descripcion, u.sede, up.usuario_perfil_id from usuario u, cead c, estado e, usuario_perfil up, perfil per where u.estado_estado_id in (1,2) and c.codigo=u.sede and e.estado_id=u.estado_estado_id and up.usuario_usuario_id=u.usuario_id  and per.perfil_id=up.perfil_perfil_id order by u.nombre asc limit $page_position, $item_per_page";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de perfiles
    function traerPerfiles($page_position, $item_per_page) {
        $sql = "select p.perfil_id, p.descripcion, e.descripcion from perfil p, estado e where p.estado_estado_id in (1,2) and e.estado_id=p.estado_estado_id order by p.descripcion asc limit $page_position, $item_per_page";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de eventos
    function traerEventos($page_position, $item_per_page) {
        $sql = "select e.id_evento, e.titulo_evento, e.descripcion_evento, Date(e.fecha_evento) as fecha_evento, es.descripcion, TIME(e.fecha_evento) as hora_evento, HOUR(e.fecha_evento) as hora from eventos e, estado es where e.estado=es.estado_id and e.estado in (1,2) order by e.id_evento desc limit $page_position, $item_per_page";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de eventos
    function traerEventosCalendario() {
        $sql = "SELECT e.id_evento, e.titulo_evento, e.descripcion_evento, DATE(e.fecha_evento) AS fecha_evento, es.descripcion, TIME(e.fecha_evento) AS hora_evento, HOUR(e.fecha_evento) AS hora FROM eventos e, estado es WHERE e.estado=es.estado_id AND e.estado IN (1) AND e.`fecha_evento` = CURDATE() ORDER BY e.id_evento DESC";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    //Metodo que retorna el listado de eventos
    function traerCumpeañosCalendario() {
        $sql = "SELECT u.`nombre`, c.`descripcion` FROM usuario u, cead c WHERE MONTH(u.fecha_nacimiento)= MONTH(CURDATE()) AND DAY(u.fecha_nacimiento)=DAY(CURDATE()) AND c.`codigo`=u.sede AND u.`estado_estado_id`=1";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de cortes de seguimiento
    function traerCortesSeguimiento($page_position, $item_per_page) {
        $sql = "select cs.corte_id, cs.periodo_academico_id, pa.descripcion as nombre_periodo, cs.no_semanas, cs.fecha_inicio, cs.fecha_fin, cs.iteraciones from corte_seguimiento cs, periodo_academico pa where cs.periodo_academico_id=pa.periodo_academico_id order by periodo_academico_id desc limit $page_position, $item_per_page";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de noticias
    function traerNoticias($page_position, $item_per_page) {
        $sql = "select n.id_noticia,n.titulo_noticia,n.fecha_noticia,n.descripcion_noticia,n.link,e.descripcion from noticias n, estado e where n.estado=e.estado_id and e.estado_id=1 order by n.fecha_noticia desc limit $page_position, $item_per_page";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de solicitudes para eliminar seguimientos
    function traerSolicitudes($page_position, $item_per_page) {
        $sql = "SELECT es.`eliminacion_id`,es.`observacion`,es.`seguimiento_id`,es.`fecha_radicacion`,es.`estado_id`, ae.`estudiante_estudiante_id`, e.`nombre`,ae.`periodo_academico_periodo_academico_id`, pa.`descripcion`,u.`nombre`
                FROM eliminacion_seguimientos es, `auditor_estudiante` ae, seguimiento s, auditor a, usuario u, `periodo_academico` pa, estudiante e
                WHERE es.`estado_id`=1
                AND ae.`auditor_estudiante_id`=s.`auditor_estudiante_id`
                AND es.`seguimiento_id`=s.`seguimiento_id`
                AND a.`auditor_id`=ae.`auditor_auditor_id`
                AND u.`usuario_id`=a.`usuario_usuario_id`
                AND pa.`periodo_academico_id`=ae.`periodo_academico_periodo_academico_id`
                AND e.`estudiante_id`=ae.`estudiante_estudiante_id`
                ORDER BY es.`fecha_radicacion` ASC limit $page_position, $item_per_page";
        $resultado = mysql_query($sql);
        return $resultado;
    }

//Metodo que retorna el listado de periodos disponibles para crear cortes
    function periodos_crearSeguimiento($opc) {
        if ($opc == "crear")
            $sql = "select periodo_academico_id, descripcion from periodo_academico where anno>=2015 and periodo_academico_id not in (select periodo_academico_id from corte_seguimiento) order by periodo_academico_id asc ";
        else
            $sql = "select periodo_academico_id, descripcion from periodo_academico where anno>=2015 order by periodo_academico_id asc ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function asignarEstudiantes($auditor, $cadenaEstudiantes, $usuario_log, $cadenaPeriodo) {
        $estudiantes = split(",", $cadenaEstudiantes);
        $inserts = array();
        $sql = "INSERT INTO `SIVISAE`.`auditor_estudiante`
            (`auditor_auditor_id`,
             `estudiante_estudiante_id`,
             `periodo_academico_periodo_academico_id`,
             `fecha_asignacion`,
             `usuario_asigno_id`) VALUES ";
        foreach ($estudiantes as $est) {
            $cons = "SELECT pa.periodo_academico_id 
                        FROM SIVISAE.periodo_academico pa 
                            INNER JOIN SIVISAE.matricula m ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id  
                        WHERE  
                            m.estudiante_estudiante_id = $est AND pa.periodo_academico_id IN ($cadenaPeriodo) "
                    . " AND m.estudiante_estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante); ";
            $periodo = mysql_query($cons);
            while ($row = mysql_fetch_array($periodo)) {
                $inserts[] = "($auditor, $est, $row[0], CURRENT_TIMESTAMP, $usuario_log)";
            }
        }
        $sql.= implode(", ", $inserts);
        $sql.=";";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function asignarEstudiantesConsejeria($consejero, $cadenaEstudiantes, $usuario_log, $cadenaPeriodo, $tipoAsg) {
        $estudiantes = split(",", $cadenaEstudiantes);
        $inserts = array();
        $sql = "INSERT INTO `SIVISAE`.`consejero_estudiante`
            (`consejero_consejero_id`,
             `estudiante_estudiante_id`,
             `periodo_academico_periodo_academico_id`,
             `fecha_asignacion`,
             `usuario_asigno_id`,
             tipo_asignacion ) VALUES ";
        foreach ($estudiantes as $est) {
            $cons = "SELECT pa.periodo_academico_id 
                        FROM SIVISAE.periodo_academico pa 
                            INNER JOIN SIVISAE.matricula m ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id  
                        WHERE  
                            m.estudiante_estudiante_id = $est AND pa.periodo_academico_id IN ($cadenaPeriodo) "
                    . " AND m.estudiante_estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.consejero_estudiante); ";
            $periodo = mysql_query($cons);
            while ($row = mysql_fetch_array($periodo)) {
                $inserts[] = "($consejero, $est, $row[0], CURRENT_TIMESTAMP, $usuario_log, $tipoAsg)";
            }
        }
        $sql.= implode(", ", $inserts);
        $sql.=";";
        //echo $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function validarEstudiantes($cadenaEstudiantes) {
//        $estudiantes = split(",", $cadenaEstudiantes);
        $sql = "SELECT COUNT(A.tipo_est), A.tipo_est 
                FROM ( 
                    SELECT CASE WHEN COUNT(m.periodo_academico_periodo_academico_id)>1 THEN 'Antiguo' ELSE 'Nuevo' END AS tipo_est,
                        m.estudiante_estudiante_id, e.cedula, periodo_academico_periodo_academico_id, programa_programa_id 
                    FROM SIVISAE.matricula m 
                        INNER JOIN SIVISAE.estudiante e ON e.estudiante_id = m.estudiante_estudiante_id 
                    WHERE e.cedula IN ('$cadenaEstudiantes')	 
                        AND m.estudiante_estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) 
                    GROUP BY estudiante_estudiante_id )A 
                GROUP BY A.tipo_est ";

        $sql.=";";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function cargarAsignarEstudiantes($cadenaEstudiantes, $usuario_log) {
        $resp = array();
        $sql = "INSERT INTO `SIVISAE`.`tmp_cargue_asignacion`
            (`cedula_estudiante`,
             `cedula_auditor`,
             `peraca`,
             `usuario_id`)
                VALUES ";

        foreach ($cadenaEstudiantes as $fila) {
            $est = explode(";", $fila);
            $tmp [] = "('" . implode("','", $est) . "', $usuario_log)";
        }
        $sql.= implode(", ", $tmp);
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        if ($resultado == 1) {
            $asignados = "SELECT  e.cedula, LOWER(e.nombre), u.cedula, LOWER(u.nombre) 
                    FROM SIVISAE.tmp_cargue_asignacion tmp 
                        INNER JOIN SIVISAE.usuario u ON u.cedula = tmp.cedula_auditor 
                        INNER JOIN SIVISAE.auditor a ON a.usuario_usuario_id = u.usuario_id 
                        INNER JOIN SIVISAE.estudiante e ON e.cedula = tmp.cedula_estudiante 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.codigo_peraca = tmp.peraca
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id AND m.periodo_academico_periodo_academico_id = pa.periodo_academico_id 
                WHERE e.estudiante_id IN (SELECT estudiante_estudiante_id FROM auditor_estudiante WHERE periodo_academico_periodo_academico_id = pa.periodo_academico_id)";
            $resultado_asignados = mysql_query($asignados);
            if (mysql_num_rows($resultado_asignados) > 0) {
                $resp [] = $resultado_asignados;
            } else {
                $resp [] = '1';
            }
            $por_asignar = "SELECT e.cedula, LOWER(e.nombre), u.cedula, LOWER(u.nombre) 
                    FROM SIVISAE.tmp_cargue_asignacion tmp 
                        INNER JOIN SIVISAE.usuario u ON u.cedula = tmp.cedula_auditor 
                        INNER JOIN SIVISAE.auditor a ON a.usuario_usuario_id = u.usuario_id 
                        INNER JOIN SIVISAE.estudiante e ON e.cedula = tmp.cedula_estudiante 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.codigo_peraca = tmp.peraca 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id AND m.periodo_academico_periodo_academico_id = pa.periodo_academico_id 
                WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM auditor_estudiante WHERE periodo_academico_periodo_academico_id = pa.periodo_academico_id)";
            $resultado_por_asignar = mysql_query($por_asignar);
            if (mysql_num_rows($resultado_por_asignar) > 0) {
                $resp [] = $resultado_por_asignar;
                $insert = "INSERT INTO `SIVISAE`.`auditor_estudiante` 
                                (`auditor_auditor_id`, `estudiante_estudiante_id`, `periodo_academico_periodo_academico_id`, `fecha_asignacion`,  
                                   `usuario_asigno_id`) 
                    SELECT  a.auditor_id, e.estudiante_id, pa.periodo_academico_id, CURRENT_TIMESTAMP, $usuario_log  
                    FROM SIVISAE.tmp_cargue_asignacion tmp 
                        INNER JOIN SIVISAE.usuario u ON u.cedula = tmp.cedula_auditor 
                        INNER JOIN SIVISAE.auditor a ON a.usuario_usuario_id = u.usuario_id 
                        INNER JOIN SIVISAE.estudiante e ON e.cedula = tmp.cedula_estudiante 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.codigo_peraca = tmp.peraca 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id AND m.periodo_academico_periodo_academico_id = pa.periodo_academico_id 
                    WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM auditor_estudiante WHERE periodo_academico_periodo_academico_id = pa.periodo_academico_id)";
                $resultado2 = mysql_query($insert) or die(mysql_error() . $insert);
            } else {
                $resp [] = '0';
            }

            $limpia_tmp = mysql_query("DELETE FROM tmp_cargue_asignacion WHERE usuario_id = $usuario_log");
        }
        $resp [] = count($cadenaEstudiantes);
        return $resp;
    }

    function cargarAsignarEstudiantesConsejeria($cadenaEstudiantes, $usuario_log) {
        $resp = array();
        $sql = "INSERT INTO `SIVISAE`.`tmp_cargue_asignacion_consejeros`
            (`cedula_estudiante`,
             `cedula_auditor`,
             `peraca`,
             `tipo_asignacion`,
             `usuario_id`)
                VALUES ";
        foreach ($cadenaEstudiantes as $fila) {
            $est = explode(";", $fila);
            $tmp [] = "('" . implode("','", $est) . "', $usuario_log)";
        }
        $sql.= implode(", ", $tmp);
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        if ($resultado == 1) {
            $asignados = "SELECT e.cedula, LOWER(e.nombre), u.cedula, LOWER(u.nombre),tipo_asignacion 
                    FROM SIVISAE.tmp_cargue_asignacion_consejeros tmp 
                        INNER JOIN SIVISAE.usuario u ON u.cedula = tmp.cedula_auditor 
                        INNER JOIN SIVISAE.consejero a ON a.usuario_usuario_id = u.usuario_id 
                        INNER JOIN SIVISAE.estudiante e ON e.cedula = tmp.cedula_estudiante 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.codigo_peraca = tmp.peraca
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id AND m.periodo_academico_periodo_academico_id = pa.periodo_academico_id 
                WHERE e.estudiante_id IN (SELECT estudiante_estudiante_id FROM consejero_estudiante WHERE periodo_academico_periodo_academico_id = pa.periodo_academico_id)";
            $resultado_asignados = mysql_query($asignados);
            if (mysql_num_rows($resultado_asignados) > 0) {
                $resp [] = $resultado_asignados;
            } else {
                $resp [] = '1';
            }
            $por_asignar = "SELECT e.cedula, LOWER(e.nombre), u.cedula, LOWER(u.nombre), tipo_asignacion 
                    FROM SIVISAE.tmp_cargue_asignacion_consejeros tmp 
                        INNER JOIN SIVISAE.usuario u ON u.cedula = tmp.cedula_auditor 
                        INNER JOIN SIVISAE.consejero a ON a.usuario_usuario_id = u.usuario_id 
                        INNER JOIN SIVISAE.estudiante e ON e.cedula = tmp.cedula_estudiante 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.codigo_peraca = tmp.peraca 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id AND m.periodo_academico_periodo_academico_id = pa.periodo_academico_id 
                WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM consejero_estudiante WHERE periodo_academico_periodo_academico_id = pa.periodo_academico_id)";
            $resultado_por_asignar = mysql_query($por_asignar);
            if (mysql_num_rows($resultado_por_asignar) > 0) {
                $resp [] = $resultado_por_asignar;
                $insert = "INSERT INTO `SIVISAE`.`consejero_estudiante` 
                                (`consejero_consejero_id`, `estudiante_estudiante_id`, `periodo_academico_periodo_academico_id`, `fecha_asignacion`,  
                                   `usuario_asigno_id`, tipo_asignacion) 
                    SELECT  a.consejero_id, e.estudiante_id, pa.periodo_academico_id, CURRENT_TIMESTAMP, $usuario_log , tipo_asignacion 
                    FROM SIVISAE.tmp_cargue_asignacion_consejeros tmp 
                        INNER JOIN SIVISAE.usuario u ON u.cedula = tmp.cedula_auditor 
                        INNER JOIN SIVISAE.consejero a ON a.usuario_usuario_id = u.usuario_id 
                        INNER JOIN SIVISAE.estudiante e ON e.cedula = tmp.cedula_estudiante 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.codigo_peraca = tmp.peraca 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id AND m.periodo_academico_periodo_academico_id = pa.periodo_academico_id 
                    WHERE e.estudiante_id NOT IN (SELECT estudiante_estudiante_id FROM consejero_estudiante WHERE periodo_academico_periodo_academico_id = pa.periodo_academico_id)";
                $resultado2 = mysql_query($insert) or die(mysql_error() . $insert);
            } else {
                $resp [] = '0';
            }

            $limpia_tmp = mysql_query("DELETE FROM tmp_cargue_asignacion WHERE usuario_id = $usuario_log");
        }
        $resp [] = count($cadenaEstudiantes);
        return $resp;
    }

    function permisosPerfil($perfil_id) {
        $sql = "SELECT opcion_id, descripcion, CASE WHEN opcion_id= (SELECT opcion_opcion_id FROM SIVISAE.perfil_opcion WHERE perfil_perfil_id = $perfil_id AND opcion_opcion_id = opcion_id) THEN '1' ELSE '0' END AS opc, 
                    opcion_crear, opcion_editar, opcion_eliminar, filtro_escuela, filtro_zona 
                FROM SIVISAE.opcion
                    LEFT OUTER JOIN perfil_opcion ON opcion_opcion_id = opcion_id AND perfil_perfil_id = $perfil_id";
        //echo $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function updateNombrePerfil($perfil_id, $nombre) {
        $sql = "UPDATE SIVISAE.perfil SET descripcion = '$nombre' WHERE perfil_id = $perfil_id";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function cantEstudiantesAsignados($auditor, $periodo, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT COUNT(e.estudiante_id)  
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id  ";

        if ($auditor == 'T') {
            $sql .=" WHERE e.estudiante_id IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE ae.auditor_auditor_id = $auditor "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.=";";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function filtroCantEstudiantesAsignados($auditor, $filtro, $periodo, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT COUNT(e.estudiante_id)  
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor === 'T') {
            $sql .=" WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND 
                e.estudiante_id IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND ae.auditor_auditor_id = $auditor "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= ";";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function estudiantesAsignados($auditor, $page_position, $item_per_page, $periodo, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion, p.codigo, p.descripcion, p.escuela, pa.descripcion 
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor == 'T') {
            $sql .=" WHERE e.estudiante_id IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE ae.auditor_auditor_id = $auditor "
                    . "AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= "ORDER BY c.descripcion, p.escuela, p.descripcion, e.nombre ASC"
                . " LIMIT $page_position, $item_per_page; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarEstudiantesAsignados($auditor, $page_position, $item_per_page, $filtro, $periodo, $escuela, $programa) {
        $sql = "SELECT  
                    DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion, p.codigo, p.descripcion, p.escuela, pa.descripcion 
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id ";

        if ($auditor === 'T') {
            $sql .=" WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND 
                e.estudiante_id IN (SELECT estudiante_estudiante_id FROM SIVISAE.auditor_estudiante) "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        } else {
            $sql .= " INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  "
                    . " WHERE (e.cedula LIKE '%$filtro%' OR LOWER(e.nombre) LIKE '%$filtro%' OR LOWER(c.descripcion) LIKE '%$filtro%' 
	 	OR p.descripcion LIKE '%$filtro%' OR p.codigo LIKE '%$filtro%') AND ae.auditor_auditor_id = $auditor "
                    . " AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " ORDER BY c.descripcion, p.escuela, p.descripcion, e.nombre ASC "
                . " LIMIT $page_position, $item_per_page; ";
        $resultado = mysql_query($sql);

        return $resultado;
    }

    function cantEstudiantesAsignados2Consejeria($auditor, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion) {
        $sql = "SELECT DISTINCT COUNT(e.estudiante_id)
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                    LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                    ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`auditor_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor == 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " "
                    . " AND ae.consejero_consejero_id = $auditor "
                    . "AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.=" AND ae.`tipo_asignacion` IN ($tipo_asignacion) ;";
        //echo ' <br><br> ' . $sql;
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function cantEstudiantesAsignados2($auditor, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin) {
        $sql = "SELECT  DISTINCT COUNT(e.estudiante_id)
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                    LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                    ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`auditor_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor == 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " "
                    . " AND ae.auditor_auditor_id = $auditor "
                    . "AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.=";";
        //echo ' <br><br> '.$sql;
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    /*
      function estudiantesAsignadosExcelConsejeria($auditor, $periodo, $escuela, $programa) {
      $sql = "SELECT  DISTINCT LOWER(e.nombre), e.cedula, LOWER(p.descripcion) AS nom_prog, LOWER(c.descripcion) AS cead, z.descripcion as zona,LOWER(p.escuela),
      CASE WHEN m.tipo_estudiante = 'H' THEN
      CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END
      ELSE 'nuevo' END AS tipo_est,
      e.telefono, e.correo, LOWER(pa.descripcion) AS periodo
      FROM SIVISAE.estudiante e
      INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id
      INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id
      INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id
      LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
      INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id   ";

      // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion

      if ($auditor == 'T') {
      $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
      } else {
      $sql .= " AND ae.consejero_consejero_id = $auditor "
      . "AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
      }

      if ($escuela != "T") {
      $sql.= " AND p.escuela IN ('$escuela') ";
      }
      if ($programa != "T") {
      $sql.= " AND p.programa_id IN ($programa) ";
      }
      $sql.= " ORDER BY e.nombre ASC"
      . " ; ";
      //echo ' <br><br> :  ' . $sql;
      $resultado = mysql_query($sql);
      return $resultado;
      }
     */

    function estudiantesAsignados2Consejeria($auditor, $page_position, $item_per_page, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion) {

        // 1. Faltan Cursos - 2. Acciones Abiertas - 3. Completo
        $sql = "SELECT  DISTINCT e.estudiante_id, e.cedula, LOWER(e.nombre), LOWER(c.descripcion) AS cead, p.codigo AS cod_prog, LOWER(p.descripcion) AS nom_prog, 
                    LOWER(p.escuela), pa.codigo_peraca, LOWER(pa.descripcion) AS periodo, 
                    CASE WHEN ae.cant_seguimientos IS NULL OR ae.cant_seguimientos = '' THEN 'No tiene' ELSE ae.cant_seguimientos END AS cant_seguimientos,
                    CASE WHEN CURDATE()>cs.fecha_fin THEN 
                        (ae.cant_seguimientos/(CEILING(DATEDIFF(cs.fecha_fin,cs.fecha_inicio)/7)*cs.iteraciones)*100) ELSE 
                        (ae.cant_seguimientos/(CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*cs.iteraciones)*100) END AS percent,
                    CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                        ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                    END AS est_carac, 	  
                    CASE WHEN (
                        SELECT COUNT(estudiante_id) 
                        FROM SIVISAE.induccion_estudiante ie
                        WHERE ie.estudiante_id = e.estudiante_id )>0 
                    THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, 
                    ae.consejero_consejero_id,  ae.consejero_estudiante_id, 
                    CASE WHEN m.tipo_estudiante = 'H' THEN 
                        CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END 
                    ELSE 'nuevo' END AS tipo_est, z.descripcion as zona, 
                   (
                    SELECT CASE WHEN s.cant_cursos != s.cant_auditados THEN '1' ELSE CASE WHEN ase.estado='a' THEN '2' ELSE '3' END END AS esta
                    FROM SIVISAE.seguimiento s LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                    LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                    WHERE s.auditor_estudiante_id = ae.consejero_estudiante_id and s.estado=1
                    ORDER BY esta ASC LIMIT 1 
                    ) AS estado, p.tipo_programa_tipo_programa_id,  
                    (SELECT DISTINCT novedad FROM SIVISAE.estudiante_materia es WHERE novedad IN ('A') AND es.estudiante_id = e.estudiante_id and es.periodo_academico_id = $periodo) AS nov       
                    , ae.`tipo_asignacion` AS tip_asg
                    
                    FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                    LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                    INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id   ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`auditor_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor == 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND ae.consejero_consejero_id = $auditor "
                    . "AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= " AND ae.`tipo_asignacion` IN ($tipo_asignacion) ORDER BY e.nombre ASC"
                . " LIMIT $page_position, $item_per_page; ";
        //echo ' <br><br> estudiantesAsignados2Consejeria:  ' . $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function estudiantesAsignados2($auditor, $page_position, $item_per_page, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin) {

        // 1. Faltan Cursos - 2. Acciones Abiertas - 3. Completo
        $sql = "SELECT  DISTINCT e.estudiante_id, e.cedula, LOWER(e.nombre), LOWER(c.descripcion) AS cead, p.codigo AS cod_prog, LOWER(p.descripcion) AS nom_prog, 
                    LOWER(p.escuela), pa.codigo_peraca, LOWER(pa.descripcion) AS periodo, 
                    CASE WHEN ae.cant_seguimientos IS NULL OR ae.cant_seguimientos = '' THEN 'No tiene' ELSE ae.cant_seguimientos END AS cant_seguimientos,
                    CASE WHEN CURDATE()>cs.fecha_fin THEN 
                        (ae.cant_seguimientos/(CEILING(DATEDIFF(cs.fecha_fin,cs.fecha_inicio)/7)*cs.iteraciones)*100) ELSE 
                        (ae.cant_seguimientos/(CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*cs.iteraciones)*100) END AS percent,
                    CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                        ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                    END AS est_carac, 	  
                    CASE WHEN (
                        SELECT COUNT(estudiante_id) 
                        FROM SIVISAE.induccion_estudiante ie
                        WHERE ie.estudiante_id = e.estudiante_id )>0 
                    THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, 
                    ae.auditor_auditor_id,  ae.auditor_estudiante_id, 
                    CASE WHEN m.tipo_estudiante = 'H' THEN 
                        CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END 
                    ELSE 'nuevo' END AS tipo_est, z.descripcion as zona, 
                   (
                    SELECT CASE WHEN s.cant_cursos != s.cant_auditados THEN '1' ELSE CASE WHEN ase.estado='a' THEN '2' ELSE '3' END END AS esta
                    FROM SIVISAE.seguimiento s LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                    LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                    WHERE s.auditor_estudiante_id = ae.auditor_estudiante_id and s.estado=1
                    ORDER BY esta ASC LIMIT 1 
                    ) AS estado, p.tipo_programa_tipo_programa_id,  
                    (SELECT DISTINCT novedad FROM SIVISAE.estudiante_materia es WHERE novedad IN ('A') AND es.estudiante_id = e.estudiante_id and es.periodo_academico_id = $periodo) AS nov       
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                    LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                    INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id   ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`auditor_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor == 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND ae.auditor_auditor_id = $auditor "
                    . "AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= "ORDER BY e.nombre ASC"
                . " LIMIT $page_position, $item_per_page; ";
        //echo ' <br><br> '.$sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function induccionesRealizadas($est_id) {
        $sql = "SELECT CASE WHEN tipo_induccion = 1 THEN 'Presencial'	ELSE 'Virtual' END AS tipo_induccion, SUBSTRING(fecha,1,10) AS fecha  
                FROM induccion_estudiante 
                WHERE estudiante_id = $est_id";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarCantHallazgos($auditor, $filtro, $periodo, $escuela, $programa) {
        $sql = "select count(h.id_hallazgo) as conteo
                from 
                hallazgos h, seguimiento_auditor_estudiante sae, auditor_estudiante ae, estudiante e, auditor a, usuario u, estudiante_materia em,
                materia m, tutor t, matricula mat, programa pro
                where
                sae.seguimiento_aduditor_estudiante_id=h.fk_seguimiento
                and ae.auditor_estudiante_id=sae.auditor_estudiante_id
                and ae.estudiante_estudiante_id=e.estudiante_id
                and a.auditor_id=ae.auditor_auditor_id
                and a.usuario_usuario_id=u.usuario_id
                and sae.estudainte_materia_id=em.estudiante_materia_id
                and m.materia_id=em.materia_id
                and t.tutor_id=em.tutor_id
                and em.periodo_academico_id in ($periodo)
                and mat.matricula_id=em.periodo_academico_id
                and mat.programa_programa_id=pro.programa_id";

        if ($auditor != 'T') {
            $sql.=' and ae.auditor_auditor_id=' . $auditor;
        }
        if ($escuela != "T") {
            $sql.= "  and pro.escuela in ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " and pro.programa_id in ($programa) ";
        }

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarHallazgos($auditor, $page_position, $item_per_page, $filtro, $periodo, $escuela, $programa) {
        $sql = "select h.id_hallazgo, h.fk_seguimiento, case h.estado_hallazgo when 1 then 'Abierto' when 2 then 'Cerrado' end as estado_hallazgo, h.fecha_creacion, ae.estudiante_estudiante_id, e.nombre, ae.auditor_auditor_id, u.nombre, em.estudiante_materia_id, em.materia_id, m.descripcion as materia, em.tutor_id, t.nombre as tutor, em.periodo_academico_id, mat.programa_programa_id, pro.descripcion as programa, pro.escuela, e.cedula
                from 
                hallazgos h, seguimiento_auditor_estudiante sae, auditor_estudiante ae, estudiante e, auditor a, usuario u, estudiante_materia em,
                materia m, tutor t, matricula mat, programa pro
                where
                sae.seguimiento_aduditor_estudiante_id=h.fk_seguimiento
                and ae.auditor_estudiante_id=sae.auditor_estudiante_id
                and ae.estudiante_estudiante_id=e.estudiante_id
                and a.auditor_id=ae.auditor_auditor_id
                and a.usuario_usuario_id=u.usuario_id
                and sae.estudainte_materia_id=em.estudiante_materia_id
                and m.materia_id=em.materia_id
                and t.tutor_id=em.tutor_id
                and em.periodo_academico_id in ($periodo)
                and mat.matricula_id=em.periodo_academico_id
                and mat.programa_programa_id=pro.programa_id
                LIMIT $page_position, $item_per_page";

        if ($auditor != 'T') {
            $sql.=' and ae.auditor_auditor_id=' . $auditor;
        }
        if ($escuela != "T") {
            $sql.= "  and pro.escuela in ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " and pro.programa_id in ($programa) ";
        }

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarHallazgos2($auditor, $page_position, $item_per_page, $periodo, $escuela, $programa) {
        $sql = "select h.id_hallazgo, h.fk_seguimiento, case h.estado_hallazgo when 1 then 'Abierto' when 2 then 'Cerrado' end as estado_hallazgo, h.fecha_creacion, ae.estudiante_estudiante_id, e.nombre, ae.auditor_auditor_id, u.nombre, em.estudiante_materia_id, em.materia_id, m.descripcion as materia, em.tutor_id, t.nombre as tutor, em.periodo_academico_id, mat.programa_programa_id, pro.descripcion as programa, pro.escuela, e.cedula
                from 
                hallazgos h, seguimiento_auditor_estudiante sae, auditor_estudiante ae, estudiante e, auditor a, usuario u, estudiante_materia em,
                materia m, tutor t, matricula mat, programa pro
                where
                sae.seguimiento_aduditor_estudiante_id=h.fk_seguimiento
                and ae.auditor_estudiante_id=sae.auditor_estudiante_id
                and ae.estudiante_estudiante_id=e.estudiante_id
                and a.auditor_id=ae.auditor_auditor_id
                and a.usuario_usuario_id=u.usuario_id
                and sae.estudainte_materia_id=em.estudiante_materia_id
                and m.materia_id=em.materia_id
                and t.tutor_id=em.tutor_id
                and em.periodo_academico_id in ($periodo)
                and mat.matricula_id=em.periodo_academico_id
                and mat.programa_programa_id=pro.programa_id
                LIMIT $page_position, $item_per_page";

        if ($auditor != 'T') {
            $sql.=' and ae.auditor_auditor_id=' . $auditor;
        }
        if ($escuela != "T") {
            $sql.= "  and pro.escuela in ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " and pro.programa_id in ($programa) ";
        }

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function cantHallazgos2($auditor, $periodo, $escuela, $programa) {
        $sql = "select count(h.id_hallazgo) as conteo
                from 
                hallazgos h, seguimiento_auditor_estudiante sae, auditor_estudiante ae, estudiante e, auditor a, usuario u, estudiante_materia em,
                materia m, tutor t, matricula mat, programa pro
                where
                sae.seguimiento_aduditor_estudiante_id=h.fk_seguimiento
                and ae.auditor_estudiante_id=sae.auditor_estudiante_id
                and ae.estudiante_estudiante_id=e.estudiante_id
                and a.auditor_id=ae.auditor_auditor_id
                and a.usuario_usuario_id=u.usuario_id
                and sae.estudainte_materia_id=em.estudiante_materia_id
                and m.materia_id=em.materia_id
                and t.tutor_id=em.tutor_id
                and em.periodo_academico_id in ($periodo)
                and mat.matricula_id=em.periodo_academico_id
                and mat.programa_programa_id=pro.programa_id";

        if ($auditor != 'T') {
            $sql.=' and ae.auditor_auditor_id=' . $auditor;
        }
        if ($escuela != "T") {
            $sql.= "  and pro.escuela in ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " and pro.programa_id in ($programa) ";
        }

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarCantEstudiantesAsignados2Consejeria($auditor, $filtro, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion) {

        $sql = "SELECT COUNT(1) FROM (
                    SELECT  DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion AS cead, p.codigo AS cod_prog, p.descripcion AS nom_prog, 
                        p.escuela, pa.codigo_peraca, pa.descripcion AS periodo, 
                        CASE WHEN ae.cant_seguimientos IS NULL OR ae.cant_seguimientos = '' THEN 'No tiene' ELSE ae.cant_seguimientos END AS cant_seguimientos, 
                        CASE WHEN CURDATE()>cs.fecha_fin THEN 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(cs.fecha_fin,cs.fecha_inicio)/7)*cs.iteraciones)*100) ELSE 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*cs.iteraciones)*100) END AS percent, 
                        CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                            ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                        END AS est_carac, 	  
                        CASE WHEN (
                            SELECT COUNT(estudiante_id) 
                            FROM SIVISAE.induccion_estudiante ie
                            WHERE ie.estudiante_id = e.estudiante_id )>0 
                        THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, z.descripcion as zona 
                    FROM SIVISAE.estudiante e 
                        INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  
                        INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`auditor_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor === 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND  ae.consejero_consejero_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= " AND ae.`tipo_asignacion` IN ($tipo_asignacion)  ORDER BY e.nombre ASC )A
                WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR LOWER(A.nombre) LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%' 
                     OR A.nom_prog LIKE '%$filtro%' OR A.cead LIKE '%$filtro%'  OR A.est_carac LIKE '%$filtro%'  OR A.zona LIKE '%$filtro%'  ; ";
        //echo ' <br><br> ' . $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarCantEstudiantesAsignados2($auditor, $filtro, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin) {
        $sql = "SELECT COUNT(1) FROM (
                    SELECT  DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion AS cead, p.codigo AS cod_prog, p.descripcion AS nom_prog, 
                        p.escuela, pa.codigo_peraca, pa.descripcion AS periodo, 
                        CASE WHEN ae.cant_seguimientos IS NULL OR ae.cant_seguimientos = '' THEN 'No tiene' ELSE ae.cant_seguimientos END AS cant_seguimientos, 
                        CASE WHEN CURDATE()>cs.fecha_fin THEN 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(cs.fecha_fin,cs.fecha_inicio)/7)*cs.iteraciones)*100) ELSE 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*cs.iteraciones)*100) END AS percent, 
                        CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                            ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                        END AS est_carac, 	  
                        CASE WHEN (
                            SELECT COUNT(estudiante_id) 
                            FROM SIVISAE.induccion_estudiante ie
                            WHERE ie.estudiante_id = e.estudiante_id AND ie.periodo_academico_id = pa.periodo_academico_id )>0 
                        THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, z.descripcion as zona 
                    FROM SIVISAE.estudiante e 
                        INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id  
                        INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`auditor_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor === 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND  ae.auditor_auditor_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= "    ORDER BY e.nombre ASC )A
                WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR LOWER(A.nombre) LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%' 
                     OR A.nom_prog LIKE '%$filtro%' OR A.cead LIKE '%$filtro%'  OR A.est_carac LIKE '%$filtro%'  OR A.zona LIKE '%$filtro%'  ; ";
        //echo ' <br><br> '.$sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    /*
      function filtrarEstudiantesAsignadosExcelConsejeria($auditor, $filtro, $periodo, $escuela, $programa) {
      $sql = "SELECT * FROM (
      SELECT  DISTINCT e.cedula, e.nombre, p.descripcion AS nom_prog, c.descripcion AS cead, z.descripcion as zona,  p.escuela,
      CASE WHEN m.tipo_estudiante = 'H' THEN
      CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END
      ELSE 'nuevo' END AS tipo_est, e.telefono, e.correo,
      pa.descripcion AS periodo
      FROM SIVISAE.estudiante e
      INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id
      INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id
      INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id
      LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
      INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id  ";

      // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
      if ($auditor === 'T') {
      $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
      } else {
      $sql .= " AND ae.consejero_consejero_id = $auditor "
      . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
      }
      if ($escuela !== 'T') {
      $sql.= " AND p.escuela IN ('$escuela') ";
      }
      if ($programa !== 'T') {
      $sql.= " AND p.programa_id IN ($programa) ";
      }

      $sql.= " AND ORDER BY e.nombre ASC )A
      WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR LOWER(A.nombre) LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%'
      OR A.nom_prog LIKE '%$filtro%' OR A.cead LIKE '%$filtro%'  OR A.est_carac LIKE '%$filtro%' OR A.zona LIKE '%$filtro%'
      OR A.tipo_est LIKE '%$filtro%'
      ; ";
      //echo ' <br><br> ' . $sql;
      $resultado = mysql_query($sql);
      return $resultado;
      }
     */

    function filtrarEstudiantesAsignadosExcelConsejeria($auditor, $filtro, $periodo, $escuela, $programa, $tipo_asignacion) {
        $sql = "SELECT * FROM (
                    SELECT  DISTINCT e.cedula, e.nombre, c.descripcion AS cead, p.descripcion AS nom_prog, 
                        p.escuela, pa.descripcion AS periodo, 
                        CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                            ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                        END AS est_carac, 	  
                        CASE WHEN (
                            SELECT COUNT(estudiante_id) 
                            FROM SIVISAE.induccion_estudiante ie
                            WHERE ie.estudiante_id = e.estudiante_id )>0 
                        THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, 
                        CASE WHEN m.tipo_estudiante = 'H' THEN 
                            CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END 
                        ELSE 'nuevo' END AS tipo_est, 
                        z.descripcion as zona, 
                   ( 
                        SELECT CASE WHEN s.cant_cursos != s.cant_auditados THEN '1' ELSE CASE WHEN ase.estado='a' THEN '2' ELSE '3' END END AS esta
                        FROM SIVISAE.seguimiento s LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                        LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                        WHERE s.auditor_estudiante_id = ae.consejero_estudiante_id and s.estado=1
                        ORDER BY esta ASC LIMIT 1                    
                    ) AS estado,
                    ae.`tipo_asignacion` AS tip_asg,
                    usu.nombre as consejero,
                    e.correo, e.genero, e.telefono, concat(e.usuario,'@unadvirtual.edu.co') as institucional
                    FROM SIVISAE.estudiante e 
                        INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                        INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id  
                        INNER JOIN SIVISAE.consejero con ON con.consejero_id=ae.consejero_consejero_id
                    INNER JOIN SIVISAE.usuario usu ON con.usuario_usuario_id=usu.usuario_id";

        if ($auditor === 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND ae.consejero_consejero_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }
        if ($escuela !== 'T') {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa !== 'T') {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " AND ae.`tipo_asignacion` IN ($tipo_asignacion)  ORDER BY e.nombre ASC )A
                WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR LOWER(A.nombre) LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%' 
                     OR A.nom_prog LIKE '%$filtro%' OR A.cead LIKE '%$filtro%'  OR A.est_carac LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' 
                         OR A.tipo_est LIKE '%$filtro%' ; ";
        //echo ' filtro excel ' . $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function estudiantesAsignadosExcelConsejeria($auditor, $periodo, $escuela, $programa, $tipo_asignacion) {
        // 1. Faltan Cursos - 2. Acciones Abiertas - 3. Completo
        $sql = "SELECT  DISTINCT e.cedula, LOWER(e.nombre) as nombre, LOWER(c.descripcion) AS cead, LOWER(p.descripcion) AS nom_prog, 
                    LOWER(p.escuela) as escuela, LOWER(pa.descripcion) AS periodo, 
                    CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                        ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                    END AS est_carac, 	  
                    CASE WHEN (
                        SELECT COUNT(estudiante_id) 
                        FROM SIVISAE.induccion_estudiante ie
                        WHERE ie.estudiante_id = e.estudiante_id )>0 
                    THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, 
                    CASE WHEN m.tipo_estudiante = 'H' THEN 
                        CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END 
                    ELSE 'nuevo' END AS tipo_est, 
                    z.descripcion as zona, 
                   (
                    SELECT CASE WHEN s.cant_cursos != s.cant_auditados THEN '1' ELSE CASE WHEN ase.estado='a' THEN '2' ELSE '3' END END AS esta
                    FROM SIVISAE.seguimiento s LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                    LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                    WHERE s.auditor_estudiante_id = ae.consejero_estudiante_id and s.estado=1
                    ORDER BY esta ASC LIMIT 1 
                    ) AS estado, 
                    ae.`tipo_asignacion` AS tip_asg,
                    usu.nombre as consejero,
                    e.correo, e.genero, e.telefono, concat(e.usuario,'@unadvirtual.edu.co') as institucional
                    FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                    LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                    INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id   
                    INNER JOIN SIVISAE.consejero con ON con.consejero_id=ae.consejero_consejero_id
                    INNER JOIN SIVISAE.usuario usu ON con.usuario_usuario_id=usu.usuario_id";

        if ($auditor == 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND ae.consejero_consejero_id = $auditor "
                    . "AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= " AND ae.`tipo_asignacion` IN ($tipo_asignacion) ORDER BY e.nombre ASC";
        //echo ' abierto excel:  ' . $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarEstudiantesAsignados2Consejeria($auditor, $page_position, $item_per_page, $filtro, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin, $tipo_asignacion) {
        $sql = "SELECT * FROM (
                    SELECT  DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion AS cead, p.codigo AS cod_prog, p.descripcion AS nom_prog, 
                        p.escuela, pa.codigo_peraca, pa.descripcion AS periodo, 
                        CASE WHEN ae.cant_seguimientos IS NULL THEN 'No tiene' ELSE ae.cant_seguimientos END AS cant_seguimientos, 
                        CASE WHEN CURDATE()>cs.fecha_fin THEN 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(cs.fecha_fin,cs.fecha_inicio)/7)*cs.iteraciones)*100) ELSE 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*cs.iteraciones)*100) END AS percent, 
                        CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                            ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                        END AS est_carac, 	  
                        CASE WHEN (
                            SELECT COUNT(estudiante_id) 
                            FROM SIVISAE.induccion_estudiante ie
                            WHERE ie.estudiante_id = e.estudiante_id )>0 
                        THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, 
                        ae.consejero_consejero_id,  ae.consejero_estudiante_id, 
                        CASE WHEN m.tipo_estudiante = 'H' THEN 
                            CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END 
                        ELSE 'nuevo' END AS tipo_est, z.descripcion as zona, 
                   ( 
                        SELECT CASE WHEN s.cant_cursos != s.cant_auditados THEN '1' ELSE CASE WHEN ase.estado='a' THEN '2' ELSE '3' END END AS esta
                        FROM SIVISAE.seguimiento s LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                        LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                        WHERE s.auditor_estudiante_id = ae.consejero_estudiante_id and s.estado=1
                        ORDER BY esta ASC LIMIT 1                    
                    ) AS estado, p.tipo_programa_tipo_programa_id,  
                    (SELECT DISTINCT novedad FROM SIVISAE.estudiante_materia es WHERE novedad IN ('A') AND es.estudiante_id = e.estudiante_id and es.periodo_academico_id = $periodo) AS nov   
                    , ae.`tipo_asignacion` AS tip_asg
                    FROM SIVISAE.estudiante e 
                        INNER JOIN SIVISAE.consejero_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                        INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id  ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`consejero_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor === 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND ae.consejero_consejero_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }
        if ($escuela !== 'T') {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa !== 'T') {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " AND ae.`tipo_asignacion` IN ($tipo_asignacion)  ORDER BY e.nombre ASC )A
                WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR LOWER(A.nombre) LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%' 
                     OR A.nom_prog LIKE '%$filtro%' OR A.cead LIKE '%$filtro%'  OR A.est_carac LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' 
                         OR A.tipo_est LIKE '%$filtro%'  
                 LIMIT $page_position, $item_per_page; ";
        //echo ' <br><br> ' . $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtrarEstudiantesAsignados2($auditor, $page_position, $item_per_page, $filtro, $periodo, $escuela, $programa, $fecha_ini, $fecha_fin) {
        $sql = "SELECT * FROM (
                    SELECT  DISTINCT e.estudiante_id, e.cedula, e.nombre, c.descripcion AS cead, p.codigo AS cod_prog, p.descripcion AS nom_prog, 
                        p.escuela, pa.codigo_peraca, pa.descripcion AS periodo, 
                        CASE WHEN ae.cant_seguimientos IS NULL THEN 'No tiene' ELSE ae.cant_seguimientos END AS cant_seguimientos, 
                        CASE WHEN CURDATE()>cs.fecha_fin THEN 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(cs.fecha_fin,cs.fecha_inicio)/7)*cs.iteraciones)*100) ELSE 
                            (ae.cant_seguimientos/(CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*cs.iteraciones)*100) END AS percent, 
                        CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                            ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                        END AS est_carac, 	  
                        CASE WHEN (
                            SELECT COUNT(estudiante_id) 
                            FROM SIVISAE.induccion_estudiante ie
                            WHERE ie.estudiante_id = e.estudiante_id AND ie.periodo_academico_id = pa.periodo_academico_id )>0 
                        THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, 
                        ae.consejero_consejero_id,  ae.auditor_estudiante_id, 
                        CASE WHEN m.tipo_estudiante = 'H' THEN 
                            CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END 
                        ELSE 'nuevo' END AS tipo_est, z.descripcion as zona, 
                   ( 
                        SELECT CASE WHEN s.cant_cursos != s.cant_auditados THEN '1' ELSE CASE WHEN ase.estado='a' THEN '2' ELSE '3' END END AS esta
                        FROM SIVISAE.seguimiento s LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                        LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                        WHERE s.auditor_estudiante_id = ae.auditor_estudiante_id and s.estado=1
                        ORDER BY esta ASC LIMIT 1                    
                    ) AS estado, p.tipo_programa_tipo_programa_id,  
                    (SELECT DISTINCT novedad FROM SIVISAE.estudiante_materia es WHERE novedad IN ('A') AND es.estudiante_id = e.estudiante_id and es.periodo_academico_id = $periodo) AS nov   
                    FROM SIVISAE.estudiante e 
                        INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                        INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id  ";

        // modificacion 23-10-2015 Traer seguimientos por fecha de actualizacion
        if ($fecha_ini != '') {
            $sql .= " , seguimiento s WHERE s.`auditor_estudiante_id`=ae.`auditor_estudiante_id` AND "
                    . " DATE(s.`fecha_act`) BETWEEN DATE('$fecha_ini') AND DATE ('$fecha_fin') ";
        }

        if ($auditor === 'T') {
            $sql .=" AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " AND ae.auditor_auditor_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }
        if ($escuela !== 'T') {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa !== 'T') {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= "    ORDER BY e.nombre ASC )A
                WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR LOWER(A.nombre) LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%' 
                     OR A.nom_prog LIKE '%$filtro%' OR A.cead LIKE '%$filtro%'  OR A.est_carac LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' 
                         OR A.tipo_est LIKE '%$filtro%'  
                 LIMIT $page_position, $item_per_page; ";
        //echo ' <br><br> '.$sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function seguimientosRealizados($auditor_estudiante_id) {
        /* $sql = "SELECT s.seguimiento_id, CASE WHEN s.fecha_fin IS NULL THEN s.fecha_act ELSE s.fecha_fin END AS fecha, 
          CASE WHEN s.cant_cursos != s.cant_auditados THEN 'Faltan cursos' ELSE
          CASE WHEN SUM(CASE WHEN ase.estado='a'  THEN 1 ELSE 0 END) > 0 THEN 'Acciones abiertas' ELSE 'Completo' END
          END AS estado
          FROM SIVISAE.seguimiento s
          LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id
          LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id
          WHERE s.auditor_estudiante_id = $auditor_estudiante_id
          GROUP BY s.seguimiento_id
          ORDER BY s.seguimiento_id desc;";

          //23-07-2015 Modificación Resumen de Seguimientos

          $sql = "SELECT s.seguimiento_id,
          CASE WHEN s.fecha_fin IS NULL THEN s.fecha_act ELSE s.fecha_fin END AS ultima_fecha,
          s.fecha_inicia as fecha_creacion,
          s.cant_cursos,
          s.cant_auditados,
          CASE WHEN s.cant_cursos != s.cant_auditados THEN 'Faltan cursos' ELSE
          CASE WHEN SUM(CASE WHEN ase.estado='a'  THEN 1 ELSE 0 END) > 0 THEN 'Acciones abiertas' ELSE 'Completo' END END AS estado
          FROM SIVISAE.seguimiento s
          LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id
          LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id
          WHERE s.auditor_estudiante_id = $auditor_estudiante_id
          AND s.estado=1
          GROUP BY s.seguimiento_id
          ORDER BY s.seguimiento_id asc;
          ";
         */

        //08-10-2015 Modificación forma de mostrar las fechas

        $sql = "SELECT s.seguimiento_id, 
                s.`fecha_inicia`, s.`fecha_act`, s.`fecha_fin`,
                s.cant_cursos,
                s.cant_auditados,
                CASE WHEN s.cant_cursos != s.cant_auditados THEN 'Faltan cursos' ELSE 
                CASE WHEN SUM(CASE WHEN ase.estado='a'  THEN 1 ELSE 0 END) > 0 THEN 'Acciones abiertas' ELSE 'Completo' END END AS estado 
                FROM SIVISAE.seguimiento s
                LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                WHERE s.auditor_estudiante_id = $auditor_estudiante_id  
                AND s.estado=1
                GROUP BY s.seguimiento_id  
                ORDER BY s.seguimiento_id ASC;";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function traerEstudiante($est_id, $periodo) {
        $sql = "SELECT DISTINCT e.cedula, LOWER(e.nombre) AS estudiante, LOWER(CONCAT(e.correo, ', ', e.usuario,'@unadvirtual.edu.co')) AS correos, LOWER(c.descripcion) AS cead, LOWER(p.descripcion) AS programa,
	   LOWER(e.skype) AS skype, e.telefono, LOWER(u.nombre) AS auditor, 
                    CASE WHEN m.tipo_estudiante = 'H' THEN 
                        CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END 
                    ELSE 'nuevo' END AS tipo_est, pa.codigo_peraca, 
                    CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' 
                        ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END 
                    END AS est_carac, 
                    CASE WHEN co.descripcion IS NULL THEN 'no tiene' ELSE LOWER(co.descripcion) END AS convenio, a.auditor_id 
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                    INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                    INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                    LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                    LEFT OUTER JOIN SIVISAE.convenio_estudiante ce ON ce.estudiante_estudiante_id = ae.estudiante_estudiante_id AND ce.periodo_academico_periodo_academico_id = ae.periodo_academico_periodo_academico_id
                    LEFT OUTER JOIN SIVISAE.convenios co ON co.convenios_id = ce.convenios_convenios_id  
                WHERE ae.estudiante_estudiante_id = $est_id  AND ae.periodo_academico_periodo_academico_id = $periodo;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function traerAuditor($auditor_id, $usuario_id) {
        $per = "";
        if ($auditor_id !== null) {
            $per = "a.auditor_id = " . $auditor_id;
        } else if ($usuario_id !== null) {
            $per = "u.usuario_id = " . $usuario_id;
        }
        $sql = "SELECT a.auditor_id AS id, u.nombre  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 2 THEN 'AUDITOR' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 3 THEN 'LIDER NACIONAL DE AUDITORES' 
                        ELSE CASE WHEN up.perfil_perfil_id = 4 THEN 'GESTOR DE AUDITORES' END 
                        END
                    END AS tp_auditor
                FROM SIVISAE.auditor a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE $per  
                ORDER BY u.nombre ASC; ";
        $resultado = mysql_query($sql);

        return $resultado;
    }

    function traerConsejero($auditor_id, $usuario_id) {
        $per = "";
        if ($auditor_id !== null) {
            $per = "a.consejero_id = " . $auditor_id;
        } else if ($usuario_id !== null) {
            $per = "u.usuario_id = " . $usuario_id;
        }
        $sql = "SELECT a.consejero_id AS id, u.nombre  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 5 THEN 'CONSEJERO' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 3 THEN 'LIDER NACIONAL DE CONSEJERIA' 
                        ELSE CASE WHEN up.perfil_perfil_id = 4 THEN 'GESTOR DE CONSEJEROS' END 
                        END
                    END AS tp_auditor
                FROM SIVISAE.consejero a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE $per  
                ORDER BY u.nombre ASC; ";
        //echo $sql;
        $resultado = mysql_query($sql);

        return $resultado;
    }

    function traerMonitor($auditor_id, $usuario_id) {
        $per = "";
        if ($auditor_id !== null) {
            $per = "a.consejero_id = " . $auditor_id;
        } else if ($usuario_id !== null) {
            $per = "u.usuario_id = " . $usuario_id;
        }
        $sql = "SELECT a.consejero_id AS id, u.nombre  AS nombre, 
                    CASE WHEN up.perfil_perfil_id = 9 THEN 'MONITOR' 
                    ELSE 
                        CASE WHEN up.perfil_perfil_id = 8 THEN 'LIDER NACIONAL DE MONITORES' 
                        ELSE CASE WHEN up.perfil_perfil_id = 4 THEN 'GESTOR DE CONSEJEROS' END 
                        END
                    END AS tp_auditor
                FROM SIVISAE.monitor a 
                    INNER JOIN SIVISAE.usuario u ON a.usuario_usuario_id = u.usuario_id 
                    INNER JOIN SIVISAE.usuario_perfil up ON up.usuario_usuario_id = u.usuario_id 
                WHERE $per  
                ORDER BY u.nombre ASC; ";
        //echo $sql;
        $resultado = mysql_query($sql);

        return $resultado;
    }

    function traerAcciones($tipo) {
        $sql = "SELECT acciones_id, titulo, tipo, asunto FROM SIVISAE.acciones WHERE estado=1 and tipo = '$tipo';";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function inicioSeg($est_id, $periodo) {
        $sql = "SELECT u.cedula, u.nombre, 
                    CASE WHEN CURDATE()>cs.fecha_fin THEN cs.no_semanas ELSE CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7) END AS semana, 
                    CASE WHEN CURDATE()>cs.fecha_fin THEN cs.fecha_fin ELSE DATE_FORMAT(DATE_ADD(cs.fecha_inicio,INTERVAL CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*7 DAY) ,'%Y-%m-%d 23:59:59') END AS fecha_fin_semana, 
                    c.descripcion AS cead, z.descripcion AS zona, CASE WHEN ae.cant_seguimientos IS NULL THEN '0' ELSE ae.cant_seguimientos END AS cant_seguimientos, 
                    ae.auditor_estudiante_id 
                FROM SIVISAE.auditor_estudiante ae 
                    INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                    INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                    INNER JOIN SIVISAE.cead c ON c.cead_id = a.cead_cead_id 
                    INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id 
                    INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                WHERE ae.estudiante_estudiante_id = $est_id  AND ae.periodo_academico_periodo_academico_id = $periodo;";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function materiasEstByPeriodo($est_id, $periodo, $seg_id, $tipo) {
        $join = "";
        if ($seg_id !== 'n') {
            $join = "AND sae.seguimiento_id = $seg_id ";
        }
        $sql = "SELECT A.materia_id, A.descripcion, 
                    CASE WHEN A.tot_acc>0 AND A.cerradas<A.tot_acc AND A.seguimiento!='n' THEN 'boton_aud_med$tipo' ELSE 
                            CASE WHEN A.tot_acc>=0 AND A.cerradas=A.tot_acc AND A.seguimiento!='n' THEN 'boton_aud_ok$tipo' ELSE 'botones$tipo' END END AS class,
                    A.seguimiento, A.novedad, A.fecha_seguimiento, A.estudiante_materia_id, A.iteracion
                FROM 
                    (SELECT ma.materia_id, LOWER(ma.descripcion) AS descripcion, em.novedad,  
                         COUNT(ase.seguimiento_auditor_estudiante_id) AS tot_acc, 
                         SUM(CASE WHEN ase.estado='c'  THEN 1 ELSE 0 END) AS cerradas, 
                         CASE WHEN (sae.seguimiento_aduditor_estudiante_id > 0) THEN sae.seguimiento_aduditor_estudiante_id ELSE 'n' END AS seguimiento, 
                         CASE WHEN (sae.seguimiento_aduditor_estudiante_id > 0) THEN sae.fecha_seguimiento ELSE 'no' END AS fecha_seguimiento,  
                         CASE WHEN (sae.seguimiento_aduditor_estudiante_id > 0) THEN sae.estudiante_materia_id ELSE 'no' END AS estudiante_materia_id
                         , sae.iteracion
                    FROM SIVISAE.estudiante e 
                      INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                      INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                      INNER JOIN SIVISAE.estudiante_materia em ON em.estudiante_id = e.estudiante_id 
                      INNER JOIN SIVISAE.materia ma ON ma.materia_id = em.materia_id 
                      LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.estudiante_materia_id = em.estudiante_materia_id  $join
                      LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                    WHERE e.estudiante_id = $est_id AND m.periodo_academico_periodo_academico_id = $periodo and em.periodo_academico_id = $periodo 
                    GROUP BY ma.materia_id)A; ";
        //echo $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function descripcionNovedad($nov) {
        if ($nov == 'A') {
            $nov = 'Activo';
        } else if ($nov == 'B') {
            $nov = 'Cancelación S';
        } else if ($nov == 'B') {
            $nov = 'Cancelación M';
        } else if ($nov == 'D') {
            $nov = 'Aplaza M';
        } else if ($nov == 'F') {
            $nov = 'Aplaza S';
        } else if ($nov == 'N') {
            $nov = 'Eliminado';
        }
        return $nov;
    }

    function detalleEstMateria($est_id, $periodo, $mat_id) {
        $sql = "SELECT ma.consecutivo_rca, LOWER(ma.descripcion), t.tutor_id, t.cedula, LOWER(t.nombre), LOWER(t.correo), LOWER(t.skype), t.telefono, em.estudiante_materia_id  
                FROM SIVISAE.estudiante e 
                    INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                    INNER JOIN SIVISAE.estudiante_materia em ON em.estudiante_id = e.estudiante_id 
                    INNER JOIN SIVISAE.materia ma ON ma.materia_id = em.materia_id 
                    INNER JOIN SIVISAE.tutor t ON t.tutor_id = em.tutor_id
                WHERE e.estudiante_id = $est_id AND m.periodo_academico_periodo_academico_id = $periodo AND em.materia_id = $mat_id AND em.`periodo_academico_id`=$periodo; ";


        $resultado = mysql_query($sql);
        return $resultado;
    }

    function traerSeguimiento($est_id, $periodo, $auditor_estudiante_id, $tipo) {
        $sql = "CALL SIVISAE.traer_seguimiento($auditor_estudiante_id, $est_id, $periodo, '$tipo');";
        $cons2 = mysql_query($sql) or die(mysql_error() . $sql);
        $seg = mysql_fetch_array($cons2);
        return $seg[0];
    }

    function crearSeguimiento($seguimiento_id, $auditor_estudiante_id, $estudiante_materia_id, $web_c, $chat, $msj, $foro, $seg_eva, $observacion, $pqr, $h_acomp, $web_c_t, $chat_t, $msj_t, $foro_t, $seg_eva_t, $resp_t, $observacion_t) {
        $sql = "INSERT INTO SIVISAE.seguimiento_auditor_estudiante 
                    (auditor_estudiante_id, fecha_seguimiento, web_conference_est, chat_est, mensajeria_interna_est, foro, evaluacion_seg_instancia, 
                        observacion, pqr_estudiante, horas_acompanamiento, web_conference_tutor, chat_tutor, mensajeria_interna_tutor, 
                        foro_tutor, evaluacion_seg_inst_tutor, observacion_accion_tutor, respuesta_tutor, estudiante_materia_id, seguimiento_id, iteracion) 
                VALUES 
                    ('$auditor_estudiante_id', CURRENT_TIMESTAMP, '$web_c', '$chat', '$msj', '$foro', '$seg_eva', '$observacion', '$pqr', 
                    '$h_acomp', '$web_c_t', '$chat_t', '$msj_t', '$foro_t', '$seg_eva_t', '$observacion_t', '$resp_t', '$estudiante_materia_id', '$seguimiento_id', 1);";

        $res = mysql_query($sql) or die(mysql_error());


        $cons = mysql_query("SELECT seguimiento_aduditor_estudiante_id 
                    FROM SIVISAE.seguimiento_auditor_estudiante
                    WHERE auditor_estudiante_id = $auditor_estudiante_id AND estudiante_materia_id = $estudiante_materia_id and seguimiento_id=$seguimiento_id ");
        $resultado = mysql_fetch_array($cons);

        $seguimiento = $resultado[0];

//        $hallazgo = mysql_query("INSERT INTO SIVISAE.hallazgos (fk_seguimiento,estado_hallazgo,fecha_creacion) VALUES "
//                . "('$seguimiento', '1', CURRENT_DATE);") or die(mysql_error());

        return $seguimiento;
    }

    function updateSeguimientoAuditor($seguimiento_aduditor_estudiante_id, $web_c, $chat, $msj, $foro, $seg_eva, $observacion, $pqr, $h_acomp, $web_c_t, $chat_t, $msj_t, $foro_t, $seg_eva_t, $resp_t, $observacion_t) {
        $sql = "UPDATE SIVISAE.seguimiento_auditor_estudiante
                SET 
                  web_conference_est = '$web_c',
                  chat_est = '$chat',
                  mensajeria_interna_est = '$msj',
                  foro = '$foro',
                  evaluacion_seg_instancia = '$seg_eva',
                  observacion = '$observacion',
                  fecha_edicion = CURRENT_TIMESTAMP,
                  pqr_estudiante = '$pqr',
                  horas_acompanamiento = '$h_acomp',
                  web_conference_tutor = '$web_c_t',
                  chat_tutor = '$chat_t',
                  mensajeria_interna_tutor = '$msj_t',
                  foro_tutor = '$foro_t',
                  evaluacion_seg_inst_tutor = '$seg_eva_t',
                  observacion_accion_tutor = '$observacion_t',
                  respuesta_tutor = '$resp_t',
                  iteracion = iteracion + 1
                WHERE seguimiento_aduditor_estudiante_id = '$seguimiento_aduditor_estudiante_id';";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function crearAccionSeguimiento($seguimiento_id, $arr_acciones) {

        $sql = "INSERT INTO SIVISAE.acciones_seguimiento
            (seguimiento_auditor_estudiante_id, acciones_id, fecha_crea, estado )
            VALUES  ";
        $insert = array();
        foreach ($arr_acciones as $accion) {
            $insert [] = "('$seguimiento_id', '$accion', CURRENT_TIMESTAMP, 'a')";
        }
        $sql .= implode(", ", $insert);
        $sql .=";";

        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function updateSeguimiento($seguimiento_id, $auditor_estudiante_id, $est_id, $periodo) {
        $sql = "CALL SIVISAE.update_seguimiento($seguimiento_id, $auditor_estudiante_id, $est_id, $periodo);";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function crearObservacion($seguimiento, $observacion, $tipo) {
        $sql = "INSERT INTO SIVISAE.seguimiento_observacion (seguimiento_auditor_estudiante_id, observacion, fecha, tipo) VALUES "
                . "('$seguimiento', '$observacion', CURRENT_TIMESTAMP, '$tipo');";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function accionesXSeguimientoHistorial($seguimiento_aud_est_id) {
        $sql = "SELECT acc.`titulo`, 
                CASE acc.`tipo` WHEN 'preven_e' THEN 'Preventiva Estudiante' WHEN 'correc_e' THEN 'Correctiva Estudiante' WHEN 'preven_t' THEN 'Preventiva E-mediador'  WHEN 'correc_t' THEN 'Correctiva E-mediador'  END AS tipo,
                accs.`fecha_crea`, 
                CASE accs.`estado` WHEN 'a' THEN 'Abierta' WHEN 'c' THEN 'Cerrada'  END AS estado
                FROM `acciones_seguimiento` accs, `acciones` acc
                WHERE accs.`seguimiento_auditor_estudiante_id`=$seguimiento_aud_est_id 
                AND accs.`acciones_id`=acc.`acciones_id`
                ORDER BY accs.`fecha_crea` ASC;";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function observacionesXSeguimientoHistorial($seguimiento_aud_est_id) {
        $sql = "SELECT sobs.`observacion`, sobs.`fecha`, 
                CASE sobs.`tipo` WHEN 'g' THEN 'Comentario Auditor' WHEN 'e' THEN 'Observación Estudiante' WHEN 't' THEN 'Observación E-mediador' END AS tipo
                FROM `seguimiento_observacion` sobs 
                WHERE sobs.`seguimiento_auditor_estudiante_id`=$seguimiento_aud_est_id 
                ORDER BY sobs.`fecha` ASC; ";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function accionesXSeguimiento($seguimiento_aud_est_id) {
        $sql = "SELECT ase.acciones_seguimiento_id, ase.acciones_id, a.titulo, a.tipo
                    FROM SIVISAE.acciones_seguimiento ase 
                       INNER JOIN SIVISAE.acciones a ON a.acciones_id = ase.acciones_id 
                    WHERE ase.seguimiento_auditor_estudiante_id = $seguimiento_aud_est_id AND ase.estado ='a' AND a.tipo LIKE '%_e'
                    UNION
                    SELECT 'c', 'c', 'c', 'c' 
                    UNION
                    SELECT ase.acciones_seguimiento_id, ase.acciones_id, a.titulo, a.tipo
                    FROM SIVISAE.acciones_seguimiento ase 
                       INNER JOIN SIVISAE.acciones a ON a.acciones_id = ase.acciones_id 
                    WHERE ase.seguimiento_auditor_estudiante_id = $seguimiento_aud_est_id AND ase.estado ='a' AND a.tipo LIKE '%_t';";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function cerrarAcciones($arr_acciones_seg) {
        foreach ($arr_acciones_seg as $accion_seg) {
            $sql = "UPDATE SIVISAE.acciones_seguimiento SET    
                            estado = 'c',  
                            fecha_cierre = CURRENT_TIMESTAMP    
                         WHERE acciones_seguimiento_id = $accion_seg;  ";
            $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        }


        return $resultado;
    }

    function mover_recursivo($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    $this->mover_recursivo($src . '/' . $file, $dst . '/' . $file);
                } else {
                    rename($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    function borrarDirectorio($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->borrarDirectorio("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    function infObservacionAca($seg_aud, $tipo) {
        $where = " WHERE sae.seguimiento_aduditor_estudiante_id = $seg_aud";
        if ($tipo === 'e') {
            $where = " INNER JOIN SIVISAE.seguimiento s ON s.seguimiento_id = sae.seguimiento_id "
                    . " WHERE s.seguimiento_id = $seg_aud "
                    . "limit 1 ";
        }
        $sql = "SELECT  
                   LOWER(mu.municipio), LOWER(t.nombre), LOWER(ma.descripcion), LOWER(tp.descripcion), LOWER(u.nombre), LOWER(c.descripcion), 
                   e.cedula, LOWER(e.nombre), ma.consecutivo_rca, sae.seguimiento_id, LOWER(p.descripcion)    
                FROM SIVISAE.seguimiento_auditor_estudiante sae 
                   INNER JOIN SIVISAE.estudiante_materia em ON em.estudiante_materia_id = sae.estudiante_materia_id 
                   INNER JOIN SIVISAE.tutor t ON t.tutor_id = em.tutor_id 
                   INNER JOIN SIVISAE.estudiante e ON e.estudiante_id = em.estudiante_id 
                   INNER JOIN SIVISAE.materia ma ON ma.materia_id = em.materia_id 
                   INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                   INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                   INNER JOIN SIVISAE.tipo_programa tp ON tp.tipo_programa_id = p.tipo_programa_tipo_programa_id 
                   INNER JOIN SIVISAE.auditor_estudiante ae ON ae.auditor_estudiante_id = sae.auditor_estudiante_id 
                   INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                   INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                   INNER JOIN SIVISAE.cead c ON c.codigo = u.sede 
                   INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id 
                   INNER JOIN SIVISAE.municipio mu ON mu.municipio_id = c.municipio_municipio_id
                $where ; ";

        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function observacionesAcad($seg_aud, $tipo) {
        $sql = "";
        if ($tipo !== 'g') {
            $sql = "SELECT DISTINCT titulo, tipo, asunto, observacion 
                    FROM SIVISAE.acciones_seguimiento ase 
                       INNER JOIN SIVISAE.acciones a ON a.acciones_id = ase.acciones_id AND a.tipo LIKE '%_$tipo'
                    WHERE ase.seguimiento_auditor_estudiante_id = $seg_aud AND ase.estado = 'a';";
        } else {
            $sql = "SELECT SUBSTRING(fecha,1,10), observacion "
                    . "FROM SIVISAE.seguimiento_observacion "
                    . "WHERE seguimiento_auditor_estudiante_id = $seg_aud and tipo = '$tipo';";
        }
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function observacionesAcadEst($seguimiento_id) {
        $sql = "SELECT DISTINCT 
                  a.acciones_id, a.titulo, a.observacion, LOWER(m.descripcion) 
                FROM SIVISAE.seguimiento s 
                  INNER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id 
                  LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                  INNER JOIN SIVISAE.acciones a ON a.acciones_id = ase.acciones_id AND a.tipo IN ('preven_e', 'correc_e') 
                  INNER JOIN SIVISAE.estudiante_materia em ON em.estudiante_materia_id = sae.estudiante_materia_id 
                  INNER JOIN SIVISAE.materia m ON m.materia_id = em.materia_id 
                WHERE s.seguimiento_id = $seguimiento_id AND ase.estado = 'a'";
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        $obsFinal = array();
        $compare = "";
        while ($row = mysql_fetch_array($resultado)) {
            $acc_id = $row[0];
            $titulo = $row[1];
            if ($acc_id !== $compare) {
                $obsFinal[] = $titulo;
                $obsFinal[$titulo][] = $row[2];
                $obsFinal[$titulo][] = $row[3];
                $compare = $acc_id;
            } else {
                $obsFinal[$titulo][] = $row[3];
                $compare = $acc_id;
            }
        }
//        print_r($obsFinal);
        return $obsFinal;
    }

    /*
      function estudiantesAsignadosExcel($auditor, $periodo, $escuela, $programa) {
      $sql = "SELECT  DISTINCT LOWER(e.nombre), e.cedula, LOWER(p.descripcion) AS nom_prog, LOWER(c.descripcion) AS cead, z.descripcion AS zona, LOWER(p.escuela),
      CASE WHEN m.numero_matriculas > 1 THEN 'antiguo' ELSE
      CASE tipo_estudiante
      WHEN 'H' THEN 'homologacion'
      WHEN 'G' THEN 'nuevo'
      END END AS tipo_est,
      e.telefono, CONCAT(e.correo, ', ', e.usuario,'@unadvirtual.edu.co') AS correos, LOWER(u.nombre) as auditor, LOWER(pa.descripcion) as periodo
      FROM SIVISAE.estudiante e
      INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id
      INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id
      INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = m.periodo_academico_periodo_academico_id
      LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = m.periodo_academico_periodo_academico_id
      INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id
      INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id
      INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id  ";

      if ($auditor == 'T') {
      $sql .=" WHERE m.periodo_academico_periodo_academico_id IN ($periodo) ";
      } else {
      $sql .= " WHERE ae.auditor_auditor_id = $auditor "
      . "AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
      }

      if ($escuela != "T") {
      $sql.= " AND p.escuela IN ('$escuela') ";
      }
      if ($programa != "T") {
      $sql.= " AND p.programa_id IN ($programa) ";
      }
      $sql.= "ORDER BY e.nombre ASC; ";

      $resultado = mysql_query($sql);
      return $resultado;
      } */

    // 10-08-2015 Modificacion de la consulta porque no generaba el mismo nuemero de asignados

    function estudiantesAsignadosExcel($auditor, $periodo, $escuela, $programa) {
        $sql = "SELECT * FROM (
                    SELECT  DISTINCT LOWER(e.nombre) AS nombre,
                            e.cedula AS cedula,
                            LOWER(p.descripcion) AS nom_prog, 
                            LOWER(c.descripcion) AS cead,
                            z.descripcion AS zona, 
                            LOWER(p.escuela) AS escuela,
                            CASE WHEN m.tipo_estudiante = 'H' THEN CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END ELSE 'nuevo' END AS tipo_est,
                            e.telefono,
                            CONCAT(e.correo, ', ', e.usuario,'@unadvirtual.edu.co') AS correos,
                            LOWER(u.nombre) AS auditor,
                            LOWER(pa.descripcion) AS periodo
                    FROM SIVISAE.estudiante e 
                        INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                        INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id
                        INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                        INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id ";

        if ($auditor === 'T') {
            $sql .=" WHERE ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " WHERE  ae.auditor_auditor_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }
        if ($escuela !== 'T') {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa !== 'T') {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= " ORDER BY e.nombre ASC )A; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    /*
      function filtrarEstudiantesAsignadosExcel($auditor, $filtro, $periodo, $escuela, $programa) {
      $sql = "SELECT * FROM (
      SELECT  DISTINCT LOWER(e.nombre) AS nombre, e.cedula AS cedula, LOWER(p.descripcion) AS nom_prog, LOWER(c.descripcion) AS cead, z.descripcion AS zona,
      LOWER(p.escuela) AS escuela,
      CASE WHEN m.numero_matriculas > 1 THEN 'antiguo' ELSE
      CASE tipo_estudiante
      WHEN 'H' THEN 'homologacion'
      WHEN 'G' THEN 'nuevo'
      END END AS tipo_est,
      e.telefono, CONCAT(e.correo, ', ', e.usuario,'@unadvirtual.edu.co') AS correos, LOWER(u.nombre) AS auditor, LOWER(pa.descripcion) AS periodo,
      CASE WHEN ae.cant_seguimientos IS NULL THEN 'No tiene' ELSE ae.cant_seguimientos END AS cant_seguimientos,
      CASE WHEN CURDATE()>cs.fecha_fin THEN
      (ae.cant_seguimientos/(CEILING(DATEDIFF(cs.fecha_fin,cs.fecha_inicio)/7)*cs.iteraciones)*100) ELSE
      (ae.cant_seguimientos/(CEILING(DATEDIFF(CURDATE(),cs.fecha_inicio)/7)*cs.iteraciones)*100) END AS percent,
      CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante'
      ELSE CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END
      END AS est_carac,
      CASE WHEN (
      SELECT COUNT(estudiante_id)
      FROM SIVISAE.induccion_estudiante ie
      WHERE ie.estudiante_id = e.estudiante_id AND ie.periodo_academico_id = pa.periodo_academico_id )>0
      THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion
      FROM SIVISAE.estudiante e
      INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id
      INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id
      INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id
      LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id
      INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
      INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id
      INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id
      INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id  ";

      if ($auditor === 'T') {
      $sql .=" WHERE ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
      } else {
      $sql .= " WHERE  ae.auditor_auditor_id = $auditor "
      . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.periodo_academico_periodo_academico_id IN ($periodo) ";
      }
      if ($escuela !== 'T') {
      $sql.= " AND p.escuela IN ('$escuela') ";
      }
      if ($programa !== 'T') {
      $sql.= " AND p.programa_id IN ($programa) ";
      }

      $sql.= "    ORDER BY e.nombre ASC )A
      WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR A.nombre LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%'
      OR A.nom_prog LIKE '%$filtro%' OR A.est_carac LIKE '%$filtro%' ; ";

      $resultado = mysql_query($sql) or die(mysql_error());
      return $resultado;
      }
     */

    // 10-08-2015 Modificacion de la consulta porque no generaba el mismo nuemero de asignados

    function filtrarEstudiantesAsignadosExcel($auditor, $filtro, $periodo, $escuela, $programa) {
        $sql = "SELECT * FROM (
                    SELECT  DISTINCT e.nombre, e.cedula, p.descripcion AS nom_prog, c.descripcion AS cead,
                                z.descripcion AS zona, LOWER(p.escuela) AS escuela, 
                                CASE WHEN m.tipo_estudiante = 'H' THEN CASE WHEN m.numero_matriculas = 1 THEN 'homologado' ELSE 'antiguo' END ELSE 'nuevo' END AS tipo_est, 
                                e.telefono, CONCAT(e.correo, ', ', e.usuario,'@unadvirtual.edu.co') AS correos, LOWER(u.nombre) AS auditor, pa.descripcion AS periodo, 
                    
                                CASE WHEN (
                                SELECT COUNT(estudiante_id) 
                                FROM SIVISAE.induccion_estudiante ie
                                WHERE ie.estudiante_id = e.estudiante_id AND ie.periodo_academico_id = pa.periodo_academico_id )>0 
                                THEN 'Realizada' ELSE 'Sin asistencia' END AS induccion, 

                                CASE WHEN ec.estado_caracterizacion IS NULL 
                                THEN 'Faltante' ELSE CASE 
                                WHEN ec.estado_caracterizacion = 1 THEN 'Completa' ELSE 'Incompleta' END END AS est_carac	  
                        
                    FROM SIVISAE.estudiante e 
                        INNER JOIN SIVISAE.auditor_estudiante ae ON ae.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.cead c ON c.cead_id = e.cead_cead_id 
                        INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                        INNER JOIN SIVISAE.periodo_academico pa ON pa.periodo_academico_id = ae.periodo_academico_periodo_academico_id 
                        LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = e.estudiante_id 
                        INNER JOIN SIVISAE.corte_seguimiento cs ON cs.periodo_academico_id = ae.periodo_academico_periodo_academico_id
                        INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id
                        INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                        INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id";

        if ($auditor === 'T') {
            $sql .=" WHERE ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " WHERE  ae.auditor_auditor_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND m.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        }
        if ($escuela !== 'T') {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa !== 'T') {
            $sql.= " AND p.programa_id IN ($programa) ";
        }

        $sql.= "    ORDER BY e.nombre ASC )A
                WHERE A.induccion LIKE '%$filtro%' OR A.cedula LIKE '%$filtro%' OR LOWER(A.nombre) LIKE '%$filtro%' OR A.escuela LIKE '%$filtro%' 
                     OR A.nom_prog LIKE '%$filtro%' OR A.cead LIKE '%$filtro%'  OR A.est_carac LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' 
                         OR A.tipo_est LIKE '%$filtro%'; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function consultarAsignacionesEstudianteDirectorio($id_estudiante) {
        $sql = "SELECT u.`nombre`, u.`correo`, u.`telefono`, u.`celular`, c.`descripcion` AS centro, pa.`descripcion` "
                . "FROM `auditor_estudiante` ae, `auditor` a, usuario u, `cead` c, `periodo_academico` pa "
                . "WHERE `estudiante_estudiante_id`=$id_estudiante AND a.`auditor_id`=ae.`auditor_auditor_id` AND u.`usuario_id`=a.`usuario_usuario_id` AND c.`codigo`=u.`sede` "
                . "AND pa.`periodo_academico_id`= ae.`periodo_academico_periodo_academico_id` ORDER BY  ae.`periodo_academico_periodo_academico_id` DESC";
        $resultado = mysql_query($sql) or die(mysql_error());
        return $resultado;
    }

    function buscarEstudianteDirectorio($documento_buscar) {
        $sql = "SELECT e.`estudiante_id`, e.`cedula`,e.`nombre`,e.`correo`, CONCAT(e.`usuario`,'@unadvirtual.edu.co') AS institucional,e.`skype`,"
                . "e.`fecha_nacimiento`,e.`genero`,e.`estado_civil`,e.`telefono`,  c.`descripcion` AS centro "
                . "FROM `estudiante` e, `cead` c "
                . "WHERE (e.nombre LIKE '%$documento_buscar%' OR e.cedula LIKE '%$documento_buscar%' OR e.`correo` LIKE '%$documento_buscar%' ) AND c.`cead_id`=e.`cead_cead_id`";
        $resultado = mysql_query($sql) or die(mysql_error());
        return $resultado;
    }

    function buscarFuncDirectorio($buscar, $tipo_per) {
        $sql = "";
        if ($tipo_per === 't') {
            $sql = "SELECT LOWER(nombre), telefono, correo 
                    FROM tutor 
                    WHERE (nombre LIKE '%$buscar%' OR cedula LIKE'%$buscar%') AND  estado_estado_id = 1  
                    ORDER BY nombre ASC;";
        } else if ($tipo_per === 'a') {
            $sql = "SELECT LOWER(u.nombre), u.telefono, u.correo 
                    FROM SIVISAE.auditor a 
                       INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                    WHERE (u.nombre LIKE '%$buscar%' OR u.cedula LIKE '%$buscar%') AND a.estado_estado_id = 1 
                    ORDER BY u.nombre ASC;";
        } else if ($tipo_per === 'c') {
            $sql = "SELECT LOWER(u.nombre), u.telefono, u.correo 
                    FROM SIVISAE.consejero a 
                       INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                    WHERE (u.nombre LIKE '%$buscar%' OR u.cedula LIKE '%$buscar%') AND a.estado_estado_id = 1 
                    ORDER BY u.nombre ASC;";
        }
        $resultado = mysql_query($sql) or die(mysql_error() . $sql);
        return $resultado;
    }

    function consultarMatriculasEstudianteDirectorio($id_est) {
        $sql = "SELECT m.`estudiante_estudiante_id`, p.`descripcion`, pro.`descripcion`, m.`tipo_estudiante`, m.`numero_matriculas` "
                . "FROM `matricula` m, `periodo_academico` p, programa pro WHERE `estudiante_estudiante_id`=$id_est AND m.`periodo_academico_periodo_academico_id`=p.`periodo_academico_id` AND pro.`programa_id`=m.`programa_programa_id`";
        $resultado = mysql_query($sql) or die(mysql_error());
        return $resultado;
    }

    //Metodo para la tranasaccionalidad de la creacion de usuarios
    function generarSolicitudEliminacion($observacion_solicitud, $id_seguimiento) {
        //Se valida si ya existe la solicitud
        $sql = "select count(*) as conteo from SIVISAE.eliminacion_seguimientos where `seguimiento_id`=$id_seguimiento";
        $resultado = mysql_query($sql) or die(mysql_error());

        while ($fila = mysql_fetch_assoc($resultado)) {
            $solicitud = $fila['conteo'];
        }

        $rta = 0;
        if ($solicitud > 0) {
            $rta = 2;
        } else {
            //Se inserta la solicitud
            $sql = "INSERT INTO SIVISAE.`eliminacion_seguimientos` (`observacion`,`seguimiento_id`,`estado_id`, fecha_radicacion) VALUES ('$observacion_solicitud',$id_seguimiento,1, CURRENT_TIMESTAMP)";
            $res = mysql_query($sql);
            //Se retorna el identity
            $id = 0;
            $id = mysql_insert_id();
            if ($id > 0) {
                $rta = 1;
            } else {
                $rta = 3;
            }
        }
        return $rta;
    }

    // Inicio - Metodos de Reportes
    //Modificacion 01-09-2015 Optimizacion consultas y cifras del reporte 
    function cantReporteGestionAud($filtro, $auditor, $cead, $zona) {
        $sql = "SELECT count(u.`nombre`) as conteo FROM usuario u, `auditor` a, cead c, zona z "
                . "WHERE u.`usuario_id`=a.`usuario_usuario_id` AND u.`estado_estado_id`=1 AND c.`codigo`=u.`sede` "
                . "AND c.`zona_zona_id`=z.`zona_id`";

        if ($auditor != 'T') {
            $sql.= " AND a.`auditor_id`=$auditor";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND u.nombre LIKE '%$filtro%' OR z.descripcion LIKE '%$filtro%' OR c.descripcion LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY u.`nombre` ASC;";

        $resultado = mysql_query($sql);

        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo'];
        }


        return $res;
    }

    function reporteGestionAudListado($filtro, $auditor, $cead, $zona, $page_position, $item_per_page) {
        $sql = "SELECT a.`auditor_id`, u.cedula, u.`nombre`, c.`descripcion` AS cead, z.`descripcion` AS zona FROM usuario u, `auditor` a, cead c, zona z "
                . "WHERE u.`usuario_id`=a.`usuario_usuario_id` AND u.`estado_estado_id`=1 AND c.`codigo`=u.`sede` "
                . "AND c.`zona_zona_id`=z.`zona_id`";

        if ($auditor != 'T') {
            $sql.= " AND a.`auditor_id`=$auditor";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }

        if ($filtro !== 'n') {
            $sql.= " and u.nombre LIKE '%$filtro%' OR z.descripcion LIKE '%$filtro%' OR c.descripcion LIKE '%$filtro%' ";
        }
        $sql.= " ORDER BY u.`nombre` ASC LIMIT $page_position, $item_per_page;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function reporteGestionAudAsignados($auditor, $periodo, $programa, $escuela, $tipo) {

        $sql = "SELECT COUNT(ae.`estudiante_estudiante_id`) AS asignados 
                FROM `auditor_estudiante` ae, matricula m, programa p WHERE ae.`auditor_auditor_id`=$auditor AND ae.`periodo_academico_periodo_academico_id`=$periodo
                AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id`
                AND m.`periodo_academico_periodo_academico_id`=ae.`periodo_academico_periodo_academico_id`
                AND m.`periodo_academico_periodo_academico_id`=$periodo AND p.`programa_id`=m.`programa_programa_id`";

        if ($programa != "T") {
            $sql.= " AND m.`programa_programa_id` IN ($programa) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }

        switch ($tipo) { // 1. Asignados || 2. Nuevos || 3. Homologados || 4. Antiguos
            case 2:
                $sql.= " AND m.`tipo_estudiante` = 'G' ";
                break;
            case 3:
                $sql.= " AND m.`tipo_estudiante` = 'H' AND m.`numero_matriculas`=1 ";
                break;
            case 4:
                $sql.= " AND m.`tipo_estudiante` = 'H' AND m.`numero_matriculas`>1 ";
                break;
        }


        $resultado = mysql_query($sql);
        return $resultado;
    }

    function reporteGestionAudCaracterizacion($auditor, $periodo, $programa, $escuela, $tipo) {

        $sql = "SELECT 
                COUNT(CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' END) AS carac_comp,
                COUNT(CASE WHEN ec.estado_caracterizacion != 1 THEN 'Inccompleta' END) AS carac_incom,
                COUNT(CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' END) AS sin_carac
                FROM `auditor_estudiante` ae
                LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = ae.estudiante_estudiante_id,
                matricula m, programa p
                WHERE ae.`auditor_auditor_id`=$auditor AND ae.`periodo_academico_periodo_academico_id`=$periodo
                AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id`
                AND m.`periodo_academico_periodo_academico_id`=$periodo
                AND p.`programa_id`=m.`programa_programa_id`
                AND p.`tipo_programa_tipo_programa_id`=1 ";

        if ($programa != "T") {
            $sql.= " AND m.`programa_programa_id` IN ($programa) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }

        switch ($tipo) { // 2. Nuevos || 3. Homologados || 4. Antiguos
            case 2:
                $sql.= " AND m.`tipo_estudiante` = 'G' ";
                break;
            case 3:
                $sql.= " AND m.`tipo_estudiante` = 'H' AND m.`numero_matriculas`=1 ";
                break;
            case 4:
                $sql.= " AND m.`tipo_estudiante` = 'H' AND m.`numero_matriculas`>1 ";
                break;
        }


        $resultado = mysql_query($sql);
        return $resultado;
    }

    function reporteGestionAudInducciones($auditor, $periodo, $programa, $escuela, $tipo) {

        $sql = "SELECT  ae.`auditor_auditor_id` AS id_auditor,  COUNT(DISTINCT ae.estudiante_estudiante_id) AS induccion
                FROM `auditor_estudiante` ae
                LEFT JOIN `induccion_estudiante` ie ON ae.`estudiante_estudiante_id`= ie.`estudiante_id`,
                matricula m, programa p 
                WHERE ie.`tipo_induccion` IS NOT NULL
                AND ae.`auditor_auditor_id`=$auditor AND ae.`periodo_academico_periodo_academico_id`=$periodo AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id` 
                AND m.`periodo_academico_periodo_academico_id`=$periodo AND p.`programa_id`=m.`programa_programa_id`";

        if ($programa != "T") {
            $sql.= " AND m.`programa_programa_id` IN ($programa) ";
        }

        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }

        switch ($tipo) { // 2. Nuevos || 3. Homologados || 4. Antiguos
            case 2:
                $sql.= " AND m.`tipo_estudiante` = 'G' ";
                break;
            case 3:
                $sql.= " AND m.`tipo_estudiante` = 'H' AND m.`numero_matriculas`=1 ";
                break;
            case 4:
                $sql.= " AND m.`tipo_estudiante` = 'H' AND m.`numero_matriculas`>1 ";
                break;
        }


        $resultado = mysql_query($sql);
        return $resultado;
    }

    function reporteGestionAudColor($var) {
        $color = '';
        if ($var <= 40) {
            $color = '#FF6767';
        }
        if ($var >= 41 && $var <= 60) {
            $color = '#FCA205';
        }

        if ($var >= 61 && $var <= 90) {
            $color = '#FFE779';
        }

        if ($var >= 91) {
            $color = '#41EE41';
        }
        if ($var === 'N/A') {
            $color = '#999999';
        }
        return $color;
    }

    /**
      function cantReporteGestionAud($filtro, $auditor, $periodo, $cead, $zona, $escuela, $programa) {
      $sql = "SELECT COUNT(A.nombre) FROM (
      SELECT LOWER(u.nombre) AS nombre, LOWER(c.descripcion) AS cead, LOWER(z.descripcion) AS zona, COUNT(ae.estudiante_estudiante_id) AS asignados,
      COUNT(CASE WHEN m.tipo_estudiante != 'H' THEN 'N' END) AS nuevos,
      COUNT(CASE WHEN m.tipo_estudiante = 'H' AND m.numero_matriculas = 1 THEN 'H' END) AS homolog,
      COUNT(CASE WHEN m.tipo_estudiante = 'H' AND m.numero_matriculas > 1 THEN 'A' END) AS antiguos,
      COUNT(CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' END) AS carac_comp,
      COUNT(CASE WHEN ec.estado_caracterizacion != 1 THEN 'Inccompleta' END) AS carac_incom,
      COUNT(CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' END) AS sin_carac,
      COUNT(ie.estudiante_id) AS induccion,
      (COUNT(ae.estudiante_estudiante_id) - COUNT(ie.estudiante_id)) AS sin_induccion
      FROM SIVISAE.auditor_estudiante ae
      INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id
      INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id
      INNER JOIN SIVISAE.cead c ON c.codigo = u.sede
      INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id
      INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = ae.estudiante_estudiante_id
      LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = ae.estudiante_estudiante_id
      LEFT OUTER JOIN SIVISAE.induccion_estudiante ie ON ie.estudiante_id = ae.estudiante_estudiante_id
      INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id
      ";
      if ($auditor === 'T') {
      $sql .=" WHERE ae.periodo_academico_periodo_academico_id IN ($periodo) ";
      } else {
      $sql .= " WHERE  ae.auditor_auditor_id = $auditor "
      . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) ";
      }

      if ($cead != "T") {
      $sql.= " AND c.cead_id IN ($cead) ";
      }
      if ($zona != "T") {
      $sql.= " AND c.zona_zona_id IN ($zona) ";
      }
      if ($escuela != "T") {
      $sql.= " AND p.escuela IN ('$escuela') ";
      }
      if ($programa != "T") {
      $sql.= " AND p.programa_id IN ($programa) ";
      }
      $sql.= "GROUP BY ae.auditor_auditor_id)A  ";
      if ($filtro !== 'n') {
      $sql.= " WHERE A.nombre LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' OR A.cead LIKE '%$filtro%' ";
      }
      $sql.= ";";

      $resultado = mysql_query($sql);
      return $resultado;
      }

      function reporteGestionAudInducciones($periodo) {
      $sql = "SELECT  ae.`auditor_auditor_id` as id_auditor,  COUNT(DISTINCT ae.estudiante_estudiante_id) AS induccion
      FROM `auditor_estudiante` ae
      LEFT JOIN `induccion_estudiante` ie ON ae.`estudiante_estudiante_id`= ie.`estudiante_id`
      WHERE ae.periodo_academico_periodo_academico_id=$periodo
      AND ie.`tipo_induccion` IS NOT NULL
      GROUP BY ae.`auditor_auditor_id`;";
      $resultado = mysql_query($sql);

      while ($fila = mysql_fetch_assoc($resultado)) {
      $arreglo[] = array(
      'id_auditor' => $fila['id_auditor'],
      'induccion' => $fila['induccion']
      );
      }

      return $arreglo;
      }
     * */
    function reporteGestionAud($tipo, $filtro, $auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa) {
        if ($tipo !== 'ex') {
            $sql = "SELECT * FROM (
                SELECT ae.`auditor_auditor_id`, LOWER(u.nombre) AS nombre,  LOWER(c.descripcion) AS cead, LOWER(z.descripcion) AS zona,
                COUNT(ae.estudiante_estudiante_id) AS asignados,
                COUNT(CASE WHEN m.tipo_estudiante != 'H' THEN 'N' END) AS nuevos,
                COUNT(CASE WHEN m.tipo_estudiante = 'H' AND m.numero_matriculas = 1 THEN 'H' END) AS homolog,
                COUNT(CASE WHEN m.tipo_estudiante = 'H' AND m.numero_matriculas > 1 THEN 'A' END) AS antiguos,
                COUNT(CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' END) AS carac_comp,
                COUNT(CASE WHEN ec.estado_caracterizacion != 1 THEN 'Inccompleta' END) AS carac_incom,
                COUNT(CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' END) AS sin_carac,
                '0' as inducciones,
                '1' as sin_inducciones
                FROM SIVISAE.auditor_estudiante ae
                LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = ae.estudiante_estudiante_id,
                SIVISAE.`auditor` a, SIVISAE.`usuario` u, SIVISAE.`cead` c, SIVISAE.`zona` z, SIVISAE.`matricula` m
                WHERE ae.`periodo_academico_periodo_academico_id` IN ($periodo)
                AND m.`periodo_academico_periodo_academico_id` IN ($periodo)
                AND a.`auditor_id`=ae.`auditor_auditor_id`
                AND u.`usuario_id`=a.`usuario_usuario_id`
                AND c.`codigo`=u.`sede`
                AND z.`zona_id`=c.`zona_zona_id`
                AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id`";
        } else {
            $sql = "SELECT * FROM (
                SELECT LOWER(u.nombre) AS nombre,  LOWER(c.descripcion) AS cead, LOWER(z.descripcion) AS zona,
                COUNT(ae.estudiante_estudiante_id) AS asignados,
                COUNT(CASE WHEN m.tipo_estudiante != 'H' THEN 'N' END) AS nuevos,
                COUNT(CASE WHEN m.tipo_estudiante = 'H' AND m.numero_matriculas = 1 THEN 'H' END) AS homolog,
                COUNT(CASE WHEN m.tipo_estudiante = 'H' AND m.numero_matriculas > 1 THEN 'A' END) AS antiguos,
                COUNT(CASE WHEN ec.estado_caracterizacion = 1 THEN 'Completa' END) AS carac_comp,
                COUNT(CASE WHEN ec.estado_caracterizacion != 1 THEN 'Inccompleta' END) AS carac_incom,
                COUNT(CASE WHEN ec.estado_caracterizacion IS NULL THEN 'Faltante' END) AS sin_carac
                FROM SIVISAE.auditor_estudiante ae
                LEFT OUTER JOIN SIVISAE.estado_caracterizacion ec ON ec.estudiante_id = ae.estudiante_estudiante_id,
                SIVISAE.`auditor` a, SIVISAE.`usuario` u, SIVISAE.`cead` c, SIVISAE.`zona` z, SIVISAE.`matricula` m
                WHERE ae.`periodo_academico_periodo_academico_id` IN ($periodo)
                AND m.`periodo_academico_periodo_academico_id` IN ($periodo)
                AND a.`auditor_id`=ae.`auditor_auditor_id`
                AND u.`usuario_id`=a.`usuario_usuario_id`
                AND c.`codigo`=u.`sede`
                AND z.`zona_id`=c.`zona_zona_id`
                AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id`";
        }
        if ($auditor != 'T') {
            $sql .= " AND  ae.auditor_auditor_id = $auditor ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= "GROUP BY ae.auditor_auditor_id  "
                . "ORDER BY u.nombre ASC)A ";
        if ($filtro !== 'n') {
            $sql.= " WHERE A.nombre LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' OR A.cead LIKE '%$filtro%' ";
        }
        if ($tipo !== 'ex') {
            $sql.= " LIMIT $page_position, $item_per_page";
        }
        $sql.=";";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function cantReporteSeguimientoAud($filtro, $auditor, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT COUNT(A.nombre) FROM (
                SELECT LOWER(u.nombre) AS nombre, LOWER(c.descripcion) AS centro, LOWER(z.descripcion) AS zona
                , COUNT(DISTINCT ae.estudiante_estudiante_id) AS asignados
                , COUNT(s.`seguimiento_id`) AS seguimientos
                , COUNT(DISTINCT s.`auditor_estudiante_id`) AS estudiantes_con_seguimiento
                , COUNT(DISTINCT ae.estudiante_estudiante_id) - COUNT(DISTINCT s.`auditor_estudiante_id`) AS estudiantes_sin_seguimiento
                FROM SIVISAE.auditor_estudiante ae 
                INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                INNER JOIN SIVISAE.cead c ON c.codigo = u.sede 
                INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id 
                INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = ae.estudiante_estudiante_id 
                INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                LEFT OUTER JOIN SIVISAE.seguimiento s ON s.`auditor_estudiante_id`= ae.`auditor_estudiante_id`";
        if ($auditor === 'T') {
            $sql .=" WHERE ae.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " WHERE  ae.auditor_auditor_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= "GROUP BY ae.auditor_auditor_id ) A  ";

        if ($filtro !== 'n') {
            $sql.= " WHERE A.nombre LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' OR A.centro LIKE '%$filtro%' ";
        }
        $sql.= ";";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function reporteSeguimientoAud($tipo, $filtro, $auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa) {
        $sql = "SELECT * FROM (
                SELECT LOWER(u.nombre) AS nombre, LOWER(c.descripcion) AS centro, LOWER(z.descripcion) AS zona
                , COUNT(DISTINCT ae.estudiante_estudiante_id) AS asignados
                , COUNT(s.`seguimiento_id`) AS seguimientos
                , COUNT(DISTINCT s.`auditor_estudiante_id`) AS estudiantes_con_seguimiento
                , COUNT(DISTINCT ae.estudiante_estudiante_id) - COUNT(DISTINCT s.`auditor_estudiante_id`) AS estudiantes_sin_seguimiento
                , ae.auditor_auditor_id
                FROM SIVISAE.auditor_estudiante ae 
                INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                INNER JOIN SIVISAE.cead c ON c.codigo = u.sede 
                INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id 
                INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = ae.estudiante_estudiante_id 
                INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                LEFT OUTER JOIN SIVISAE.seguimiento s ON s.`auditor_estudiante_id`= ae.`auditor_estudiante_id` ";
        if ($auditor === 'T') {
            $sql .=" WHERE ae.`periodo_academico_periodo_academico_id` IN ($periodo) ";
        } else {
            $sql .= " WHERE  ae.auditor_auditor_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= "GROUP BY ae.auditor_auditor_id  "
                . "ORDER BY u.nombre ASC)A ";

        if ($filtro !== 'n') {
            $sql.= " WHERE A.nombre LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' OR A.centro LIKE '%$filtro%' ";
        }
        if ($tipo !== 'ex') {
            $sql.= " LIMIT $page_position, $item_per_page";
        }
        $sql.=";";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function cantidadSeguimientoSemanal($periodo, $fecha_ini, $fecha_fin, $asignados) {
        $sql = "SELECT IF(SUM(CEIL($asignados/css.`valor_semana`)) IS NULL,0,SUM(CEIL(239/css.`valor_semana`))) AS cantidad
                FROM `corte_semanas_seguimiento` css, corte_seguimiento cs 
                WHERE css.`id_corte_seguimiento`=cs.`corte_id`
                AND cs.`periodo_academico_id`=$periodo
                AND css.`fecha_inicia_cs` >=DATE('$fecha_ini') AND css.`fecha_fin_cs`<=DATE('$fecha_fin')";

        $resultado = mysql_query($sql);

        $cantidad = 0;
        while ($row = mysql_fetch_array($resultado)) {
            $cantidad = $row[0];
        }

        //echo $sql;
        return $cantidad;
    }

    function reporteCorteSeguimientoAud($tipo, $filtro, $auditor, $page_position, $item_per_page, $periodo, $cead, $zona, $escuela, $programa, $f_ini, $f_fin) {
        $sql = "SELECT * FROM (
                SELECT LOWER(u.nombre) AS nombre, LOWER(c.descripcion) AS centro, LOWER(z.descripcion) AS zona
                , COUNT(DISTINCT ae.estudiante_estudiante_id) AS asignados
                , COUNT(s.`seguimiento_id`) AS seguimientos
                , COUNT(DISTINCT s.`auditor_estudiante_id`) AS estudiantes_con_seguimiento
                , COUNT(DISTINCT ae.estudiante_estudiante_id) - COUNT(DISTINCT s.`auditor_estudiante_id`) AS estudiantes_sin_seguimiento
                , ae.auditor_auditor_id
                FROM SIVISAE.auditor_estudiante ae 
                INNER JOIN SIVISAE.auditor a ON a.auditor_id = ae.auditor_auditor_id 
                INNER JOIN SIVISAE.usuario u ON u.usuario_id = a.usuario_usuario_id 
                INNER JOIN SIVISAE.cead c ON c.codigo = u.sede 
                INNER JOIN SIVISAE.zona z ON z.zona_id = c.zona_zona_id 
                INNER JOIN SIVISAE.matricula m ON m.estudiante_estudiante_id = ae.estudiante_estudiante_id 
                INNER JOIN SIVISAE.programa p ON p.programa_id = m.programa_programa_id 
                LEFT OUTER JOIN SIVISAE.seguimiento s ON s.`auditor_estudiante_id`= ae.`auditor_estudiante_id` ";
        if ($auditor === 'T') {
            $sql .=" WHERE ae.`periodo_academico_periodo_academico_id` IN ($periodo) AND DATE(s.fecha_act) BETWEEN DATE('$f_ini') AND DATE ('$f_fin')";
        } else {
            $sql .= " WHERE  ae.auditor_auditor_id = $auditor "
                    . " AND ae.periodo_academico_periodo_academico_id IN ($periodo) AND DATE(s.fecha_act) BETWEEN DATE('$f_ini') AND DATE ('$f_fin') ";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.programa_id IN ($programa) ";
        }
        $sql.= "GROUP BY ae.auditor_auditor_id  "
                . "ORDER BY u.nombre ASC)A ";

        if ($filtro !== 'n') {
            $sql.= " WHERE A.nombre LIKE '%$filtro%' OR A.zona LIKE '%$filtro%' OR A.centro LIKE '%$filtro%' ";
        }
        if ($tipo !== 'ex') {
            $sql.= " LIMIT $page_position, $item_per_page";
        }
        $sql.=";";

        $result = mysql_query($sql);
        return $result;
    }

    function getNotificaciones($usr_id, $page_position, $item_per_page) {
//        $sql = "SELECT * FROM notificaciones WHERE usr_recibe = $usr_id AND estado = 1 AND fecha_lectura IS NULL ";
        $sql = "SELECT LOWER(u.nombre), 
                   CASE n.tipo WHEN 'r' THEN 'recomendacion' 
                                WHEN 'c' THEN 'correccion' 
                                WHEN 'a' THEN 'aviso' 
                                WHEN 'm' THEN 'memorando' 
                                WHEN 'f' THEN 'felicitacion' END AS tipo, 
                   n.fecha_envio,  
                   CASE n.estado WHEN 1 THEN 'Sin leer' 
                                 WHEN 2 THEN CONCAT('Leido ',n.fecha_lectura) END AS estado, 
                    n.notificacion_id 
                 FROM SIVISAE.notificaciones n 
                   INNER JOIN usuario u ON u.usuario_id = n.usr_envia 
                 WHERE usr_recibe = $usr_id 
                 ORDER BY n.fecha_envio DESC 
                  LIMIT $page_position, $item_per_page;";
        $result = mysql_query($sql);
        return $result;
    }

    function getCantNotificaciones($usr_id) {
        $sql = "SELECT COUNT(1) FROM SIVISAE.notificaciones WHERE usr_recibe = $usr_id AND estado = 1 AND fecha_lectura IS NULL;";
        $result = mysql_fetch_array(mysql_query($sql));
        return $result[0];
    }

    function getTotalNotificaciones($usr_id) {
        $sql = "SELECT COUNT(1) FROM SIVISAE.notificaciones WHERE usr_recibe = $usr_id;";
        $result = mysql_fetch_array(mysql_query($sql));
        return $result[0];
    }

    function crearNotificacion($usr_envia, $usr_recibe, $notif, $tipo) {
        $sql = "INSERT INTO SIVISAE.notificaciones
                   (`notificacion`, `tipo`, `usr_envia`, `usr_recibe`, `estado`, `fecha_envio`)
                VALUES 
                   ('$notif', '$tipo', '$usr_envia', '$usr_recibe', '1', CURRENT_TIMESTAMP);";
        $result = mysql_query($sql) or die(mysql_error() . $sql);
        return $result;
    }

    function getNotificacion($notif_id) {
//        $sql = "SELECT * FROM notificaciones WHERE usr_recibe = $usr_id AND estado = 1 AND fecha_lectura IS NULL ";
        $sql = "SELECT n.notificacion, 
                   CASE n.tipo WHEN 'r' THEN 'success' 
                                WHEN 'c' THEN 'warning' 
                                WHEN 'a' THEN 'warning' 
                                WHEN 'm' THEN 'error' 
                                WHEN 'f' THEN 'success' END AS tipo_al, 
                   CASE n.tipo WHEN 'r' THEN 'recomendacion' 
                                WHEN 'c' THEN 'correccion' 
                                WHEN 'a' THEN 'aviso' 
                                WHEN 'm' THEN 'memorando' 
                                WHEN 'f' THEN 'felicitacion' END AS tipo, 
                   n.fecha_envio  
                 FROM SIVISAE.notificaciones n 
                   INNER JOIN usuario u ON u.usuario_id = n.usr_envia
                 WHERE n.notificacion_id = $notif_id;";
        $result = mysql_query($sql);
        return $result;
    }

    function leerNotif($notif_id) {
        $sql = "UPDATE SIVISAE.notificaciones "
                . "SET fecha_lectura = CURRENT_TIMESTAMP, estado = 2 "
                . "WHERE notificacion_id = $notif_id AND estado = 1 AND fecha_lectura IS NULL;";
        $result = mysql_query($sql);
        return $result;
    }

    function estadoSeguimiento($seguimiento_id) {
        $sql = "SELECT CASE WHEN s.cant_cursos != s.cant_auditados THEN '1' ELSE CASE WHEN ase.estado='a' THEN '2' ELSE '3' END END AS esta
                FROM SIVISAE.seguimiento s 
                  LEFT OUTER JOIN SIVISAE.seguimiento_auditor_estudiante sae ON sae.seguimiento_id = s.seguimiento_id  
                  LEFT OUTER JOIN SIVISAE.acciones_seguimiento ase ON ase.seguimiento_auditor_estudiante_id = sae.seguimiento_aduditor_estudiante_id 
                  INNER JOIN SIVISAE.acciones a ON a.acciones_id = ase.acciones_id AND tipo IN ('preven_e', 'correc_e')
                WHERE s.seguimiento_id = $seguimiento_id
                ORDER BY esta ASC LIMIT 1; ";
        $result = mysql_query($sql);
        $est = "";
        if (mysql_num_rows($result) > 0) {
            $est = mysql_fetch_array($result);
        } else {
            $est = "n";
        }
        return $est[0];
    }

    function getGenerales() {
        $sql = "Select generalidad_id, descripcion from SIVISAE.generalidades where estado =1;";
        $result = mysql_query($sql);
        return $result;
    }

    function crearGeneralidad($seguimiento_id, $generalidades) {

        $sql = "INSERT INTO `SIVISAE`.`seguimiento_generalidad`
                (`seguimiento_id`,`generalidad_id`,`fecha`) VALUES ";
        $arr = array();
        foreach ($generalidades as $genral) {
            $sqlGralidades = "select * from `SIVISAE`.`seguimiento_generalidad` where generalidad_id = " . $genral . " and seguimiento_id = $seguimiento_id and fecha_cierre is null;";
            //echo $sqlGralidades;
            $valida = mysql_query($sqlGralidades);
            if (mysql_num_rows($valida) === 0) {
                $arr[] = "('$seguimiento_id', '$genral', CURRENT_TIMESTAMP)";
            }
        }
        $sql .= implode(",", $arr);
        $sql .= ";";

        if (count($arr) > 0) {
            $result = mysql_query($sql) or die(mysql_error() . $sql);
            return $result;
        }
    }

    function getGeneralidades($seguimiento_id) {
        $gnral = mysql_query("select generalidad_id from `SIVISAE`.`seguimiento_generalidad` where seguimiento_id = $seguimiento_id and fecha_cierre is null;");
        $arr = array();
        while ($row = mysql_fetch_array($gnral)) {
            $arr [] = $row[0];
        }
        return $arr;
    }

    function cerrarGeneralidades($seguimiento_id, $generalidades) {
        $sql = "UPDATE `SIVISAE`.`seguimiento_generalidad`
                SET 
                  `fecha_cierre` = CURRENT_TIMESTAMP
                WHERE `generalidad_id` IN ($generalidades) AND `seguimiento_id` = '$seguimiento_id' and fecha_cierre is null;";
        $result = mysql_query($sql) or die(mysql_error() . $sql);
        return $result;
    }

    function filtro_variables($modulo, $pf) {
        $sql = "SELECT `filtro_escuela`, `filtro_zona` FROM `perfil_opcion` WHERE `opcion_opcion_id`=$modulo AND `perfil_perfil_id`=$pf";
        //echo $sql; 
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtro_zonas($filtro_zonas, $centro_usuario) {
        if ($filtro_zonas === '1') {
            $sql = "SELECT zona_id, LOWER(descripcion) FROM SIVISAE.zona WHERE estado_estado_id = 1 ORDER BY descripcion ASC;";
        } else {
            $sql = "SELECT z.zona_id, LOWER(z.descripcion) FROM SIVISAE.zona z, SIVISAE.cead c WHERE z.estado_estado_id = 1 AND c.`zona_zona_id`=z.`zona_id` AND c.`descripcion`='$centro_usuario' ORDER BY z.descripcion ASC;";
        }
//        echo $sql; 
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtro_ceads($filtro_zonas, $centro_usuario) {
        switch ($filtro_zonas) {
            case 1:
                $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE estado_estado_id = 1 ORDER BY descripcion ASC; ";
                break;
            case 2:
                $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE estado_estado_id = 1  AND `zona_zona_id` IN (SELECT `zona_zona_id` FROM cead WHERE `descripcion`='$centro_usuario') ORDER BY descripcion ASC;";
                break;
            case 3:
                $sql = " SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE estado_estado_id = 1  AND `descripcion`='$centro_usuario' ORDER BY descripcion ASC;";
                break;
            default:
                $sql = "SELECT cead_id, codigo, LOWER(descripcion) FROM SIVISAE.cead WHERE estado_estado_id = 1 ORDER BY descripcion ASC; ";
                break;
        }

//        echo $sql; 
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtro_escuelas($filtro_escuelas, $programa_usuario) {

        if ($programa_usuario <= '1') {
            //Todas las escuelas
            $sql = "SELECT DISTINCT(LOWER(escuela)) FROM SIVISAE.programa WHERE LOWER(escuela) NOT LIKE '%vicerrectoría%' AND LOWER(escuela) NOT LIKE '%gerencia%'  ORDER BY descripcion ASC;";
        } else {
            $sql = "SELECT DISTINCT(LOWER(escuela)) FROM SIVISAE.programa WHERE `codigo`=$programa_usuario ORDER BY descripcion ASC;";
        }

//        echo $sql; 
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function filtro_programas($filtro_escuelas, $programa_usuario) {
        switch ($filtro_escuelas) {
            case 1:
                $sql = "SELECT programa_id, codigo, LOWER(descripcion) FROM SIVISAE.programa WHERE tipo_programa_tipo_programa_id IN (1,2,7) ORDER BY descripcion ASC;";
                break;
            case 2:
                $sql = "SELECT programa_id, codigo, LOWER(descripcion) FROM SIVISAE.programa WHERE `escuela`=(SELECT `escuela` FROM SIVISAE.programa  WHERE `codigo`=$programa_usuario); ";
                break;
            case 3:
                $sql = "SELECT programa_id, codigo, LOWER(descripcion) FROM SIVISAE.programa WHERE codigo=$programa_usuario";
                break;
            default:
                $sql = "SELECT programa_id, codigo, LOWER(descripcion) FROM SIVISAE.programa WHERE tipo_programa_tipo_programa_id IN (1,2,7) ORDER BY descripcion ASC;";
                break;
        }

        //echo $sql;
        $resultado = mysql_query($sql);
        return $resultado;
    }

    // Consultas Reporte de Asistencia a inducciones

    function cantReporteAsistenciaInduccion($filtro, $periodo, $zona, $cead, $escuela, $programa, $auditor, $modalidad, $tipoConsulta) {

        if ($tipoConsulta == "1") {
            $sql = "SELECT COUNT(*) as conteo FROM ( 
                SELECT DISTINCT ae.`auditor_auditor_id` AS id_auditor, e.cedula, e.`nombre`, e.`telefono`, e.`correo`, p.`descripcion`
                FROM `auditor_estudiante` ae,
                matricula m, programa p, estudiante e, cead c
                WHERE ae.`periodo_academico_periodo_academico_id`=$periodo AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id` 
                AND m.`periodo_academico_periodo_academico_id`=$periodo AND p.`programa_id`=m.`programa_programa_id`
                AND e.`estudiante_id`=ae.estudiante_estudiante_id
                AND c.`cead_id`=e.`cead_cead_id` AND m.`numero_matriculas`=1 ";
        } else {

            $sql = "SELECT COUNT(*) as conteo FROM ( 
                SELECT DISTINCT ae.`auditor_auditor_id` AS id_auditor, e.cedula, e.`nombre`, e.`telefono`, e.`correo`, p.`descripcion`
                FROM `auditor_estudiante` ae
                LEFT JOIN `induccion_estudiante` ie ON ae.`estudiante_estudiante_id`= ie.`estudiante_id`,
                matricula m, programa p, estudiante e, cead c
                WHERE ie.`tipo_induccion` IS NOT NULL
                AND ae.`periodo_academico_periodo_academico_id`=$periodo AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id` 
                AND m.`periodo_academico_periodo_academico_id`=$periodo AND p.`programa_id`=m.`programa_programa_id`
                AND e.`estudiante_id`=ae.estudiante_estudiante_id
                AND c.`cead_id`=e.`cead_cead_id` AND m.`numero_matriculas`=1 ";
        }

        if ($auditor != 'T') {
            $sql.= " AND ae.`auditor_auditor_id`=$auditor";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.`escuela` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND e.`cedula` LIKE '%$filtro%' ";
        }

        if ($modalidad != "0") {
            $sql.= " AND ie.`tipo_induccion` = $modalidad ";
        }



        $sql.= " ORDER BY e.`nombre` ASC )AS a ";

        //echo $sql.' ';

        $resultado = mysql_query($sql);

        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo'];
        }

        return $res;
    }

    function reporteAsistenciaInducciones($filtro, $periodo, $zona, $cead, $escuela, $programa, $auditor, $page_position, $item_per_page) {

        $sql = "SELECT * FROM ( 
                SELECT DISTINCT ae.`auditor_auditor_id` AS id_auditor, e.cedula, e.`nombre`, e.`telefono`, e.`correo`, p.`descripcion`
                FROM `auditor_estudiante` ae
                LEFT JOIN `induccion_estudiante` ie ON ae.`estudiante_estudiante_id`= ie.`estudiante_id`,
                matricula m, programa p, estudiante e, cead c
                WHERE ie.`tipo_induccion` IS NOT NULL
                AND ae.`periodo_academico_periodo_academico_id`=$periodo AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id` 
                AND m.`periodo_academico_periodo_academico_id`=$periodo AND p.`programa_id`=m.`programa_programa_id`
                AND e.`estudiante_id`=ae.estudiante_estudiante_id
                AND c.`cead_id`=e.`cead_cead_id` AND m.`numero_matriculas`=1 ";

        if ($auditor != 'T') {
            $sql.= " AND ae.`auditor_auditor_id`=$auditor";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.`escuela` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            //$sql.= " AND e.`nombre` LIKE '%$filtro%' OR z.descripcion LIKE '%$filtro%' OR c.descripcion LIKE '%$filtro%' ";
            $sql.= " AND e.`cedula` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY e.`nombre` ASC )AS a LIMIT $page_position, $item_per_page;";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function reporteAsistenciaInduccionesIndicadores($periodo, $zona, $cead, $escuela, $programa, $tipo_consulta, $modalidad) {

        switch ($modalidad) {
            case 1:
                $tabla = "induccion_evaluacion_presencial_resultados";
                $id_filtro = "68";
                break;
            case 2:
                $tabla = "induccion_evaluacion_virtual_resultados";
                $id_filtro = "81";
                break;
            case 3:
                $tabla = "induccion_evaluacion_presencial_resultados";
                $id_filtro = "67";
                break;
            case 4:
                $tabla = "induccion_evaluacion_virtual_resultados";
                $id_filtro = "80";
                break;
        }

        switch ($tipo_consulta) {
            case 1:
                $sql = "SELECT COUNT(ietr.estudiante) AS conteo, iep.`descripcion`
                        FROM induccion.`$tabla` ietr, induccion.`induccion_estudiante` ie, induccion.`induccion_evaluacion_respuesta` ier, 
                            induccion.`induccion_evaluacion_pregunta` iep, SIVISAE.periodo_academico pa
                        WHERE 
                        ietr.`id_induccion`=ie.`id_induccion`
                        AND pa.`periodo_academico_id`=$periodo
                        AND ier.`id_respuesta`=ietr.`id_respuesta`
                        AND ier.`descripcion`='NO'
                        AND iep.`id_pregunta`=ier.`induccion_evaluacion_pregunta_id_pregunta`
                        AND pa.`codigo_peraca`=ie.`periodo`
                        GROUP BY ietr.id_respuesta
                        ORDER BY COUNT(ietr.estudiante) DESC
                        LIMIT 5";
                break;
            case 2:
                $sql = "SELECT COUNT(ietr.estudiante) AS conteo, ier.`descripcion`
                        FROM induccion.`$tabla` ietr, induccion.`induccion_estudiante` ie, induccion.`induccion_evaluacion_respuesta` ier, 
                            induccion.`induccion_evaluacion_pregunta` iep, SIVISAE.periodo_academico pa
                        WHERE 
                        ietr.`id_induccion`=ie.`id_induccion`
                        AND pa.`periodo_academico_id`=$periodo
                        AND ier.`id_respuesta`=ietr.`id_respuesta`
                        AND iep.`id_pregunta`=ier.`induccion_evaluacion_pregunta_id_pregunta`
                        AND iep.`id_pregunta`= $id_filtro
                        AND pa.`codigo_peraca`=ie.`periodo`
                        GROUP BY ietr.id_respuesta
                        ORDER BY COUNT(ietr.estudiante) DESC
                        LIMIT 5";
                break;
            case 3:
                $sql = "SELECT COUNT(ietr.estudiante) AS conteo, ier.`descripcion`
                        FROM induccion.`$tabla` ietr, induccion.`induccion_estudiante` ie, induccion.`induccion_evaluacion_respuesta` ier, 
                            induccion.`induccion_evaluacion_pregunta` iep, SIVISAE.periodo_academico pa
                        WHERE 
                        ietr.`id_induccion`=ie.`id_induccion`
                        AND pa.`periodo_academico_id`=$periodo
                        AND ier.`id_respuesta`=ietr.`id_respuesta`
                        AND iep.`id_pregunta`=ier.`induccion_evaluacion_pregunta_id_pregunta`
                        AND iep.`id_pregunta`= $id_filtro
                        AND pa.`codigo_peraca`=ie.`periodo`
                        GROUP BY ietr.id_respuesta
                        ORDER BY COUNT(ietr.estudiante) DESC
                        LIMIT 5";
                break;
        }

//        if ($cead != "T") {
//            $sql.= " AND c.cead_id IN ($cead) ";
//        }
//        if ($zona != "T") {
//            $sql.= " AND c.zona_zona_id IN ($zona) ";
//        }
//        if ($escuela != "T") {
//            $sql.= " AND p.`escuela` IN ('$escuela') ";
//        }
//        if ($programa != "T") {
//            $sql.= " AND p.`programa_id` IN ($programa) ";
//        }
//        $sql.= " ORDER BY e.`nombre` ASC )AS a LIMIT $page_position, $item_per_page;";
        //echo $sql."<br>";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function reporteAsistenciaInduccionesExcel($filtro, $periodo, $zona, $cead, $escuela, $programa, $auditor, $tipo) {

        if ($tipo == 1) {//Asistentes
            $sql = "SELECT * FROM ( 
                SELECT DISTINCT ae.`auditor_auditor_id` AS id_auditor, e.cedula, e.`nombre`, e.`telefono`, e.`correo`, p.`descripcion`
                FROM `auditor_estudiante` ae
                LEFT JOIN `induccion_estudiante` ie ON ae.`estudiante_estudiante_id`= ie.`estudiante_id`,
                matricula m, programa p, estudiante e, cead c
                WHERE ie.`tipo_induccion` IS NOT NULL
                AND ae.`periodo_academico_periodo_academico_id`=$periodo AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id` 
                AND m.`periodo_academico_periodo_academico_id`=$periodo AND p.`programa_id`=m.`programa_programa_id`
                AND e.`estudiante_id`=ae.estudiante_estudiante_id
                AND c.`cead_id`=e.`cead_cead_id` AND m.`numero_matriculas`=1 ";
        } else {//Faltantes
            $sql = "SELECT * FROM ( 
                SELECT DISTINCT ae.`auditor_auditor_id` AS id_auditor, e.cedula, e.`nombre`, e.`telefono`, e.`correo`, p.`descripcion`
                FROM `auditor_estudiante` ae
                LEFT JOIN `induccion_estudiante` ie ON ae.`estudiante_estudiante_id`= ie.`estudiante_id`,
                matricula m, programa p, estudiante e, cead c
                WHERE ie.`tipo_induccion` IS NULL
                AND ae.`periodo_academico_periodo_academico_id`=$periodo AND m.`estudiante_estudiante_id`=ae.`estudiante_estudiante_id` 
                AND m.`periodo_academico_periodo_academico_id`=$periodo AND p.`programa_id`=m.`programa_programa_id`
                AND e.`estudiante_id`=ae.estudiante_estudiante_id
                AND c.`cead_id`=e.`cead_cead_id` AND m.`numero_matriculas`=1 ";
        }



        if ($auditor != 'T') {
            $sql.= " AND ae.`auditor_auditor_id`=$auditor";
        }

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.`escuela` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            //$sql.= " AND e.`nombre` LIKE '%$filtro%' OR z.descripcion LIKE '%$filtro%' OR c.descripcion LIKE '%$filtro%' ";
            $sql.= " AND e.`cedula` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY e.`nombre` ASC )AS a ";

        $resultado = mysql_query($sql);
        return $resultado;
    }

    function evaluacionInducccionEstudiante($doc_estudiante, $pa_estudiante) {
        // Se trae tipo de evaluacion
        $sql = "SELECT `id_induccion`,`estudiante`,`fecha`,`tipo_induccion`,`periodo`,`marca` "
                . "FROM induccion.`induccion_estudiante` ie, SIVISAE.`periodo_academico` pa "
                . "WHERE ie.`estudiante`= $doc_estudiante AND pa.`codigo_peraca`=ie.`periodo` AND pa.`periodo_academico_id`=$pa_estudiante"
                . " ORDER BY `fecha` DESC LIMIT 1";

        //echo $sql;

        $resultado = mysql_query($sql);
        //Segun el tipo se busca en la tabla
        $id_induccion = 0;
        $tipo = 0;

        while ($row = mysql_fetch_array($resultado)) {
            $id_induccion = $row[0];
            $tipo = $row[3];
        }

        if ($tipo == 1) { //Presencial
            $sql = "SELECT iepr.`id_resultado`,iepr.`fecha`,iepr.`estudiante`,iepr.`observacion`,iepr.`sugerencias`,iepr.`id_induccion`, iepre.`descripcion` AS pregunta, 
                    ier.`descripcion` AS respuesta
                    FROM induccion.`induccion_evaluacion_presencial_resultados` iepr, induccion.`induccion_evaluacion_respuesta` ier,  induccion.`induccion_evaluacion_pregunta` iepre
                    WHERE iepr.`id_induccion`=$id_induccion AND ier.`id_respuesta`=iepr.`id_respuesta` "
                    . "AND ier.`induccion_evaluacion_pregunta_id_pregunta`=iepre.`id_pregunta`";
        } else {// Virtual
            $sql = "SELECT ievr.`id_resultado`,ievr.`fecha`,ievr.`estudiante`,ievr.`observacion`,ievr.`sugerencias`,ievr.`id_induccion`, iepre.`descripcion` AS pregunta, 
                    ier.`descripcion` AS respuesta
                    FROM induccion.`induccion_evaluacion_virtual_resultados` ievr, induccion.`induccion_evaluacion_respuesta` ier,  induccion.`induccion_evaluacion_pregunta` iepre
                    WHERE ievr.`id_induccion`=$id_induccion AND ier.`id_respuesta`=ievr.`id_respuesta` "
                    . "AND ier.`induccion_evaluacion_pregunta_id_pregunta`=iepre.`id_pregunta`";
        }

        //echo '  '.$sql;
        //Se retornan los resultados
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function cantReporteAtenciones($consejero) {

        if ($consejero == "T") {
            $query = "SELECT count(id_Atencion) as conteo FROM `atencion_registro`";
        } else {
            $query = "SELECT count(ar.id_Atencion) as conteo FROM `atencion_registro` ar, `consejero` c WHERE ar.`usu_log`=c.`usuario_usuario_id` AND c.`consejero_id`= $consejero";
        }

        //echo $query;
        $resultado = mysql_query($query);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo'];
        }
        return $res;
    }

    function ReporteAtenciones($consejero, $opc, $perfil, $modulo) {
        $tabla = "";
        switch ($perfil) {
            case 1://ADMINISTRADOR
                if ($modulo === '12') {
                    $tabla = "consejero";
                    $where = " eje_atencion=1 ";
                    $llave = "usuario_usuario_id";
                } else if ($modulo === '15') {
                    $tabla = "monitor";
                    $where = " eje_atencion=2 ";
                    $llave = "usuario_usuario_id";
                } else if ($modulo === '23') {
                    $where = " eje_atencion=3 ";
                    $tabla = "usuario";
                    $llave = "usuario_id";
                }
                break;
            case 5://CONSEJERO
                $tabla = "consejero";
                $where = " eje_atencion=1 ";
                $llave = "usuario_usuario_id";
                break;
            case 6://LIDER NACIONAL DE CONSEJERIA
                $tabla = "consejero";
                $where = " eje_atencion=1 ";
                $llave = "usuario_usuario_id";
                break;
            case 7://GESTOR DE CONSEJERÍA
                $tabla = "consejero";
                $where = " eje_atencion=1 ";
                $llave = "usuario_usuario_id";
                break;
            case 8://LÍDER NACIONAL DE MONITORES
                $tabla = "monitor";
                $where = " eje_atencion=2 ";
                $llave = "usuario_usuario_id";
                break;
            case 9://MONITOR
                $tabla = "monitor";
                $where = " eje_atencion=2 ";
                $llave = "usuario_usuario_id";
                break;
            case 12://LÍDER NACIONAL DE BIENESTAR 
                if ($modulo === '12') {
                    $tabla = "consejero";
                    $where = " eje_atencion=1 ";
                    $llave = "usuario_usuario_id";
                } else if ($modulo === '15') {
                    $tabla = "monitor";
                    $where = " eje_atencion=2 ";
                    $llave = "usuario_usuario_id";
                } else if ($modulo === '23') {
                    $where = " eje_atencion=3 ";
                    $tabla = "usuario";
                    $llave = "usuario_id";
                }
                break;
            case 16://LÍDER LÍNEA CRECIMIENTO PERSONAL
                $where = " eje_atencion=3 ";
                $tabla = "usuario";
                $llave = "usuario_id";
                break;
        }


        if ($opc == 1) {
            if ($consejero == "T") {
                $query = "SELECT * FROM `atencion_registro` where " . $where . " ORDER BY id_Atencion";
            } else {
                $query = "SELECT ar.* FROM `atencion_registro` ar, `$tabla` c WHERE " . $where . " and ar.`usu_log`=c.`$llave` AND c.`consejero_id`= $consejero ORDER BY `id_Atencion`";
            }
        } else {
            if ($consejero == "T") {
                $query = "SELECT * FROM `atencion_registro` where " . $where . " ORDER BY id_Atencion";
            } else {
                $query = "SELECT ar.* FROM `atencion_registro` ar, `$tabla` c WHERE " . $where . " and ar.`usu_log`=c.`$llave` AND c.`consejero_id`= $consejero ORDER BY `id_Atencion`";
            }
        }


        //echo ($query);
        $resultado = mysql_query($query);
        return $resultado;
    }

    function ConteoDatosReporteAtenciones($id, $filtro, $zona, $cead, $escuela, $programa, $f_inicial, $f_final) {
        $opc = 0;
        //Se consulta como estudiante
        $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, estudiante e, matricula m, `programa` p, cead c, zona z, usuario u WHERE ar.`id_Atencion`=$id AND e.`cedula`=ar.`documento` AND m.`estudiante_estudiante_id`=e.`estudiante_id` AND p.`programa_id`=m.`programa_programa_id` AND e.`cead_cead_id`=c.`cead_id` AND c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log LIMIT 1";
        $contRes = mysql_query($sql);
        while ($fila = mysql_fetch_assoc($contRes)) {
            $res = $fila['conteo'];
        }
        if ($res > 0) {
            $sql = "SELECT ar.`documento`
                    FROM `atencion_registro` ar, estudiante e, matricula m, `programa` p, cead c, zona z, usuario u
                    WHERE ar.`id_Atencion`=$id AND e.`cedula`=ar.`documento` AND m.`estudiante_estudiante_id`=e.`estudiante_id` AND p.`programa_id`=m.`programa_programa_id` AND 
                    e.`cead_cead_id`=c.`cead_id` AND c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log ";
            $opc = 1;
        } else {//Se consulta como graduado
            $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, SIGRA.`tmp_graduados` stg, SIGRA.`tmp_titulos` stt, programa p, cead c, usuario u WHERE  ar.`id_Atencion`=$id AND stg.DOCUMENTO=ar.`documento` AND ar.`documento`=stt.`DOCUMENTO` AND stt.`CODIGO_PROGRAMA`=p.`programa_id` AND stt.`CODIGO_CENTRO`=c.`codigo` AND u.`usuario_id`=ar.usu_log  LIMIT 1;";
            $contRes = mysql_query($sql);
            while ($fila = mysql_fetch_assoc($contRes)) {
                $res = $fila['conteo'];
            }
            if ($res > 0) {
                $sql = "SELECT ar.`documento`
                        FROM `atencion_registro` ar, SIGRA.`tmp_graduados` stg, SIGRA.`tmp_titulos` stt, programa p, cead c, usuario u
                        WHERE  ar.`id_Atencion`=$id AND stg.DOCUMENTO=ar.`documento` AND ar.`documento`=stt.`DOCUMENTO` AND
                        stt.`CODIGO_PROGRAMA`=p.`programa_id` AND stt.`CODIGO_CENTRO`=c.`codigo` AND u.`usuario_id`=ar.usu_log ";
                $opc = 2;
            } else {//Se consulta como aspirante
                $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, `atencion_aspirante` aa, programa p, cead c, zona z, usuario u WHERE ar.`id_Atencion`=$id AND ar.`documento`=aa.`cedula` AND p.`programa_id`=aa.`programa` AND c.`cead_id`=aa.`centro` AND  c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log LIMIT 1;";
                $contRes = mysql_query($sql);
                while ($fila = mysql_fetch_assoc($contRes)) {
                    $res = $fila['conteo'];
                }
                if ($res > 0) {
                    $sql = "SELECT ar.`documento`
                        FROM `atencion_registro` ar, `atencion_aspirante` aa, programa p, cead c, zona z, usuario u
                        WHERE ar.`id_Atencion`=$id AND ar.`documento`=aa.`cedula` AND p.`programa_id`=aa.`programa` AND c.`cead_id`=aa.`centro` AND 
                        c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log ";
                    $opc = 3;
                } else { //No existe se retorna data sin informacion
                    $sql = "SELECT ar.`documento`
                            FROM `atencion_registro` ar, usuario u, cead c
                            WHERE ar.`id_Atencion`=$id AND u.`usuario_id`=ar.usu_log AND c.`codigo`=u.`sede` ";
                    $opc = 4;
                }
            }
        }

        // Filtros

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($f_inicial != "T") {
            $sql.= " AND ar.`fecha_atencion`>='$f_inicial' ";
        }
        if ($f_final != "T") {
            $sql.= " AND ar.`fecha_atencion`<='$f_final' ";
        }

        if ($opc != 4 && $opc != 0) {
            if ($escuela != "T") {
                $sql.= " AND p.`escuela` IN ('$escuela') ";
            }
            if ($programa != "T") {
                $sql.= " AND p.`programa_id` IN ($programa) ";
            }

            if ($filtro !== 'n') {
                $sql.= " AND ar.documento LIKE '%$filtro%' ";
            }
        }

        $sql.=" LIMIT 1;";
        //echo " ".$sql;
        $dataRes = mysql_query($sql);
        return $dataRes;
    }

    function DatosReporteAtenciones($id, $filtro, $zona, $cead, $escuela, $programa, $f_inicial, $f_final) {
        $opc = 0;
        //Se consulta como estudiante
        $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, estudiante e, matricula m, `programa` p, cead c, zona z, usuario u WHERE ar.`id_Atencion`=$id AND e.`cedula`=ar.`documento` AND m.`estudiante_estudiante_id`=e.`estudiante_id` AND p.`programa_id`=m.`programa_programa_id` AND e.`cead_cead_id`=c.`cead_id` AND c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log LIMIT 1";
        $contRes = mysql_query($sql);
        while ($fila = mysql_fetch_assoc($contRes)) {
            $res = $fila['conteo'];
        }
        if ($res > 0) {
            $sql = "SELECT ar.`documento`, e.`nombre` as nombre_estudiante, p.`descripcion` AS programa, p.`escuela` as escuela, c.`descripcion` AS centro, z.`descripcion` AS zona, 
                    e.`telefono`, e.`correo`, 
                    (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                    REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, ar.`fecha_atencion`, ar.usu_log, c.`cead_id`, z.`zona_id`, p.`programa_id`, u.nombre,1
                    FROM `atencion_registro` ar, estudiante e, matricula m, `programa` p, cead c, zona z, usuario u
                    WHERE ar.`id_Atencion`=$id AND e.`cedula`=ar.`documento` AND m.`estudiante_estudiante_id`=e.`estudiante_id` AND p.`programa_id`=m.`programa_programa_id` AND 
                    e.`cead_cead_id`=c.`cead_id` AND c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log ";
            $opc = 1;
        } else {//Se consulta como graduado
            $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, SIGRA.`tmp_graduados` stg, SIGRA.`tmp_titulos` stt, programa p, cead c, usuario u WHERE  ar.`id_Atencion`=$id AND stg.DOCUMENTO=ar.`documento` AND ar.`documento`=stt.`DOCUMENTO` AND stt.`CODIGO_PROGRAMA`=p.`programa_id` AND stt.`CODIGO_CENTRO`=c.`codigo` AND u.`usuario_id`=ar.usu_log  LIMIT 1;";
            $contRes = mysql_query($sql);
            while ($fila = mysql_fetch_assoc($contRes)) {
                $res = $fila['conteo'];
            }
            if ($res > 0) {
                $sql = "SELECT ar.`documento`, CONCAT (stg.`NOMBRES`,' ',stg.`APELLIDOS`) as nombre_estudiante, stt.`NOMBRE_PROGRAMA` AS programa, stt.`ESCUELA` AS escuela, stt.`CENTRO` AS centro, stt.`ZONA` AS zona, 
                        stg.`TELEFONO` AS telefono, stg.`EMAIL` AS correo, 
                        (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                        REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, ar.`fecha_atencion`,ar.`usu_log`, c.`cead_id`, c.`zona_zona_id`, p.`programa_id`, u.nombre,2
                        FROM `atencion_registro` ar, SIGRA.`tmp_graduados` stg, SIGRA.`tmp_titulos` stt, programa p, cead c, usuario u
                        WHERE  ar.`id_Atencion`=$id AND stg.DOCUMENTO=ar.`documento` AND ar.`documento`=stt.`DOCUMENTO` AND
                        stt.`CODIGO_PROGRAMA`=p.`programa_id` AND stt.`CODIGO_CENTRO`=c.`codigo` AND u.`usuario_id`=ar.usu_log ";
                $opc = 2;
            } else {//Se consulta como aspirante
                $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, `atencion_aspirante` aa, programa p, cead c, zona z, usuario u WHERE ar.`id_Atencion`=$id AND ar.`documento`=aa.`cedula` AND p.`programa_id`=aa.`programa` AND c.`cead_id`=aa.`centro` AND  c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log LIMIT 1;";
                $contRes = mysql_query($sql);
                while ($fila = mysql_fetch_assoc($contRes)) {
                    $res = $fila['conteo'];
                }
                if ($res > 0) {
                    $sql = "SELECT ar.`documento`, aa.`nombre` as nombre_estudiante, p.`descripcion` as programa, p.`escuela` as escuela, c.`descripcion` cead, z.`descripcion` zona, aa.`telefono`, 
                        aa.`correo`, 
                        (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                        REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, ar.`fecha_atencion`, ar.`usu_log`, c.`cead_id`, z.`zona_id`, p.`programa_id`, u.nombre,3
                        FROM `atencion_registro` ar, `atencion_aspirante` aa, programa p, cead c, zona z, usuario u
                        WHERE ar.`id_Atencion`=$id AND ar.`documento`=aa.`cedula` AND p.`programa_id`=aa.`programa` AND c.`cead_id`=aa.`centro` AND 
                        c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log ";
                    $opc = 3;
                } else { //No existe se retorna data sin informacion
                    $sql = "SELECT ar.`documento`, 'nombre' as nombre_estudiante, 'programa' as programa, 'escuela' as escuela, c.descripcion as cead, z.descripcion as zona, 'telefono','correo', 
                            (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                            REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, ar.`fecha_atencion`, ar.`usu_log`, 1,1,1,u.nombre,4
                            FROM `atencion_registro` ar, usuario u, cead c, zona z
                            WHERE ar.`id_Atencion`=$id AND u.`usuario_id`=ar.usu_log AND c.`codigo`=u.`sede`AND z.`zona_id`=c.`zona_zona_id`";
                    $opc = 4;
                }
            }
        }

        // Filtros

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($f_inicial != "T") {
            $sql.= " AND ar.`fecha_atencion`>='$f_inicial' ";
        }
        if ($f_final != "T") {
            $sql.= " AND ar.`fecha_atencion`<='$f_final' ";
        }
        if ($opc != 4 && $opc != 0) {
            if ($escuela != "T") {
                $sql.= " AND p.`escuela` IN ('$escuela') ";
            }
            if ($programa != "T") {
                $sql.= " AND p.`programa_id` IN ($programa) ";
            }

            if ($filtro !== 'n') {
                $sql.= " AND ar.documento LIKE '%$filtro%' ";
            }
        }

        $sql.=" LIMIT 1;";
        //echo " ".$sql;
        $dataRes = mysql_query($sql);
        return $dataRes;
    }

    function DatosReporteAtencionesExcel($id, $filtro, $zona, $cead, $escuela, $programa, $f_inicial, $f_final) {
        $opc = 0;
        //Se consulta como estudiante
        $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, estudiante e, matricula m, `programa` p, cead c, zona z, usuario u WHERE ar.`id_Atencion`=$id AND e.`cedula`=ar.`documento` AND m.`estudiante_estudiante_id`=e.`estudiante_id` AND p.`programa_id`=m.`programa_programa_id` AND e.`cead_cead_id`=c.`cead_id` AND c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log LIMIT 1";
        $contRes = mysql_query($sql);
        while ($fila = mysql_fetch_assoc($contRes)) {
            $res = $fila['conteo'];
        }
        if ($res > 0) {
            $sql = "SELECT ar.`documento`, e.`nombre` as nombre_estudiante, p.`descripcion` AS programa, p.`escuela` as escuela, c.`descripcion` AS centro,
                z.`descripcion` AS zona, 
                    e.`telefono`, e.`correo`, 
                    (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                    REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, 
                    ar.`fecha_atencion`, u.nombre as consejero
                    FROM `atencion_registro` ar, estudiante e, matricula m, `programa` p, cead c, zona z, usuario u
                    WHERE ar.`id_Atencion`=$id AND e.`cedula`=ar.`documento` AND m.`estudiante_estudiante_id`=e.`estudiante_id` AND p.`programa_id`=m.`programa_programa_id` AND 
                    e.`cead_cead_id`=c.`cead_id` AND c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log ";
            $opc = 1;
        } else {//Se consulta como graduado
            $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, SIGRA.`tmp_graduados` stg, SIGRA.`tmp_titulos` stt, programa p, cead c, usuario u WHERE  ar.`id_Atencion`=$id AND stg.DOCUMENTO=ar.`documento` AND ar.`documento`=stt.`DOCUMENTO` AND stt.`CODIGO_PROGRAMA`=p.`programa_id` AND stt.`CODIGO_CENTRO`=c.`codigo` AND u.`usuario_id`=ar.usu_log  LIMIT 1;";
            $contRes = mysql_query($sql);
            while ($fila = mysql_fetch_assoc($contRes)) {
                $res = $fila['conteo'];
            }
            if ($res > 0) {
                $sql = "SELECT ar.`documento`, CONCAT (stg.`NOMBRES`,' ',stg.`APELLIDOS`) as nombre_estudiante, stt.`NOMBRE_PROGRAMA` AS programa, stt.`ESCUELA` AS escuela, stt.`CENTRO` AS centro, stt.`ZONA` AS zona, 
                        stg.`TELEFONO` AS telefono, stg.`EMAIL` AS correo, 
                        (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                        REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, ar.`fecha_atencion`,u.nombre as consejero
                        FROM `atencion_registro` ar, SIGRA.`tmp_graduados` stg, SIGRA.`tmp_titulos` stt, programa p, cead c, usuario u
                        WHERE  ar.`id_Atencion`=$id AND stg.DOCUMENTO=ar.`documento` AND ar.`documento`=stt.`DOCUMENTO` AND
                        stt.`CODIGO_PROGRAMA`=p.`programa_id` AND stt.`CODIGO_CENTRO`=c.`codigo` AND u.`usuario_id`=ar.usu_log ";
                $opc = 2;
            } else {//Se consulta como aspirante
                $sql = "SELECT COUNT(ar.`documento`) AS conteo FROM `atencion_registro` ar, `atencion_aspirante` aa, programa p, cead c, zona z, usuario u WHERE ar.`id_Atencion`=$id AND ar.`documento`=aa.`cedula` AND p.`programa_id`=aa.`programa` AND c.`cead_id`=aa.`centro` AND  c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log LIMIT 1;";
                $contRes = mysql_query($sql);
                while ($fila = mysql_fetch_assoc($contRes)) {
                    $res = $fila['conteo'];
                }
                if ($res > 0) {
                    $sql = "SELECT ar.`documento`, aa.`nombre` as nombre_estudiante, p.`descripcion` as programa, p.`escuela` as escuela, c.`descripcion` cead, z.`descripcion` zona, aa.`telefono`, 
                        aa.`correo`, 
                        (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                        REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, ar.`fecha_atencion`, u.nombre as consejero
                        FROM `atencion_registro` ar, `atencion_aspirante` aa, programa p, cead c, zona z, usuario u
                        WHERE ar.`id_Atencion`=$id AND ar.`documento`=aa.`cedula` AND p.`programa_id`=aa.`programa` AND c.`cead_id`=aa.`centro` AND 
                        c.`zona_zona_id`=z.`zona_id` AND u.`usuario_id`=ar.usu_log ";
                    $opc = 3;
                } else { //No existe se retorna data sin informacion
                    $sql = "SELECT ar.`documento`, 'nombre' as nombre_estudiante, 'programa' as programa, 'escuela' as escuela, c.descripcion as cead, z.descripcion as zona, 'telefono','correo', 
                            (SELECT GROUP_CONCAT(`descripcion`) FROM `atencion_categorias` WHERE FIND_IN_SET (`id_categoria`, ar.`categorias`)) AS categorias,
                            REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2','Estudiante'),'3','Graduado') AS tipo_atencion, ar.`fecha_atencion`, u.nombre as consejero
                            FROM `atencion_registro` ar, usuario u, cead c, zona z
                            WHERE ar.`id_Atencion`=$id AND u.`usuario_id`=ar.usu_log AND c.`codigo`=u.`sede`AND z.`zona_id`=c.`zona_zona_id`";
                    $opc = 4;
                }
            }
        }

        // Filtros

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($f_inicial != "T") {
            $sql.= " AND ar.`fecha_atencion`>='$f_inicial' ";
        }
        if ($f_final != "T") {
            $sql.= " AND ar.`fecha_atencion`<='$f_final' ";
        }
        if ($opc != 4 && $opc != 0) {
            if ($escuela != "T") {
                $sql.= " AND p.`escuela` IN ('$escuela') ";
            }
            if ($programa != "T") {
                $sql.= " AND p.`programa_id` IN ($programa) ";
            }

            if ($filtro !== 'n') {
                $sql.= " AND ar.documento LIKE '%$filtro%' ";
            }
        }

        $sql.=" LIMIT 1;";
        $dataRes = mysql_query($sql);
        return $dataRes;
    }

    function cantReporteEgresados($filtro, $cohorte, $zona, $cead, $escuela, $programa, $momentos) {
        $sql = "select COUNT(*) AS conteo FROM ( SELECT tt.`DOCUMENTO`, tg.`NOMBRES`, tg.`APELLIDOS`, CONCAT(`NOMBRES`,' ',`APELLIDOS`) AS COMPLETO, tg.`AREA_GEOFRAFICA`, tg.`GENERO`, tg.`DIRECCION`, tg.`EMAIL`, tg.`TELEFONO`, 
                `CIUDAD_RESIDENCIA`, tt.`NOMBRE_PROGRAMA`, tt.`ESCUELA`, tt.`CENTRO`, tt.`ZONA`, tt.mes, tt.anio , tt.`NIVEL_ACADEMICO`, tg.`SITUACION_LABORAL`, tg.`ACTIVIDAD_ECONOMICA`, 
                tg.`NOMBRE_EMPRESA`, tg.`DIRECCION_EMPRESA`, tg.`TELEFONO_EMPRESA`, tg.`ANTIGUEDAD`, tg.`TIEMPO_DESEMPLEADO`, tg.`CARGO`, tg.`SECTOR`, tg.`RELACION_PROGRAMA_TRABAJO`, ESTADO_IDENTIFICACION, tt.MOMENTO
                FROM SIGRA.`tmp_titulos` tt , SIGRA.`tmp_graduados` tg
                WHERE tg.`DOCUMENTO`=tt.`DOCUMENTO` ";

        if ($momentos != "Todos") {
            $sql.= " AND tt.MOMENTO = '$momentos' ";
        }

        if ($cohorte != "") {
            $sql.= " AND tt.anio = $cohorte ";
        }

        if ($cead != "T") {
            $sql.= " AND tt.`cead_id` IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND tt.`zona_id` IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND tt.`ESCUELA` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND tt.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND tt.`DOCUMENTO` LIKE '%$filtro%' AND tg.`NOMBRES` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY tg.`NOMBRES` ASC )AS a ";
        //echo $sql;
        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo'];
        }

        return $res;
    }

    function cantReporteMatriculados($filtro, $periodo, $zona, $cead, $escuela, $programa) {
        $sql = "select COUNT(*) AS conteo FROM (SELECT e.`cedula`, e.nombre, e.`telefono`, e.`correo`, CONCAT(e.`usuario`,'@unadvirtual.edu.co') AS institucional, p.`descripcion` AS programa, p.`escuela`, 
                c.`descripcion` AS centro, z.`descripcion` AS zona, pa.`descripcion` AS periodo, m.tipo_estudiante, m.numero_matriculas
                FROM matricula m, estudiante e, programa p, cead c, zona z, `periodo_academico` pa
                WHERE 
                m.`estudiante_estudiante_id`= e.`estudiante_id`
                AND p.`programa_id`= m.`programa_programa_id`
                AND c.`cead_id`= e.`cead_cead_id`
                AND z.`zona_id`= c.`zona_zona_id`
                AND pa.`periodo_academico_id`= m.`periodo_academico_periodo_academico_id`
                AND m.`periodo_academico_periodo_academico_id`= $periodo";

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.`escuela` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND e.`cedula` LIKE '%$filtro%' AND e.`nombre` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY e.`nombre` ASC )AS a ";

        // echo $sql.' ';

        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo'];
        }

        return $res;
    }

    function ReporteMatriculados($filtro, $periodo, $zona, $cead, $escuela, $programa, $page_position, $item_per_page) {
        $sql = "select * FROM (SELECT e.`cedula`, e.nombre, e.`telefono`, e.`correo`, CONCAT(e.`usuario`,'@unadvirtual.edu.co') AS institucional, p.`descripcion` AS programa, p.`escuela`, 
                c.`descripcion` AS centro, z.`descripcion` AS zona, pa.`descripcion` AS periodo, m.tipo_estudiante, m.numero_matriculas
                FROM matricula m, estudiante e, programa p, cead c, zona z, `periodo_academico` pa
                WHERE 
                m.`estudiante_estudiante_id`= e.`estudiante_id`
                AND p.`programa_id`= m.`programa_programa_id`
                AND c.`cead_id`= e.`cead_cead_id`
                AND z.`zona_id`= c.`zona_zona_id`
                AND pa.`periodo_academico_id`= m.`periodo_academico_periodo_academico_id`
                AND m.`periodo_academico_periodo_academico_id`= $periodo";

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.`escuela` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND e.`cedula` LIKE '%$filtro%' AND e.`nombre` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY e.`nombre` ASC )AS a LIMIT $page_position, $item_per_page;";

        //echo $sql.' ';

        $res = mysql_query($sql);

        return $res;
    }

    function cantReporteInducciones($filtro, $periodo, $zona, $cead, $escuela, $programa) {

        $sql = "SELECT count(*) as conteo FROM (
              SELECT vm.`cedula`, vm.nombre, vm.`telefono`, vm.`correo`, vm.`descripcion` AS programa, vm.`escuela`, 
              vm.`descripcion` AS centro, vm.`descripcion` AS zona, vm.`descripcion` AS periodo, vm.tipo_estudiante, vm.numero_matriculas, ie.fecha, ie.tipo_induccion, ie.participacion           
              FROM `vta_matricula` vm, induccion_estudiante ie
              WHERE vm.`periodo_academico_id` = $periodo AND vm.`numero_matriculas`<=1
              AND ie.`estudiante_id` =vm.`estudiante_id` ";

        if ($cead != "T") {
            $sql.= " AND vm.cead_cead_id IN ($cead) ";
        }

        if ($zona != "T") {
            $sql.= " AND vm.zona_id IN ($zona) ";
        }

        if ($escuela != "T") {
            $sql.= " AND vm.`escuela` IN ('$escuela') ";
        }

        if ($programa != "T") {
            $sql.= " AND vm.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND vm.`cedula` LIKE '%$filtro%' AND vm.`nombre` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY vm.`nombre` ASC )AS a ";

        //echo $sql.' ';

        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo'];
        }

        return $res;
    }

    function ReporteInducciones($filtro, $periodo, $zona, $cead, $escuela, $programa, $page_position, $item_per_page) {
        $sql = "SELECT * from (SELECT vm.`cedula`, vm.nombre, vm.`telefono`, vm.`correo`, 
                                vm.programa AS programa, vm.`escuela`, 
                                vm.cead AS centro, vm.zona AS zona, 
                                vm.`descripcion` AS periodo, vm.tipo_estudiante, 
                                vm.numero_matriculas, ie.fecha, ie.tipo_induccion, ie.participacion 
              FROM `vta_matricula` vm, induccion_estudiante ie
              WHERE vm.`periodo_academico_id` = $periodo AND vm.`numero_matriculas`<=1
              AND ie.`estudiante_id` =vm.`estudiante_id` ";

        if ($cead != "T") {
            $sql.= " AND vm.cead_cead_id IN ($cead) ";
        }

        if ($zona != "T") {
            $sql.= " AND vm.zona_id IN ($zona) ";
        }

        if ($escuela != "T") {
            $sql.= " AND vm.`escuela` IN ('$escuela') ";
        }

        if ($programa != "T") {
            $sql.= " AND vm.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND vm.`cedula` LIKE '%$filtro%' AND vm.`nombre` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY vm.`nombre` ASC )AS a LIMIT $page_position, $item_per_page;";

        // echo $sql.' ';

        $res = mysql_query($sql);

        return $res;
    }

    function ReporteInduccionesExcel($filtro, $periodo, $zona, $cead, $escuela, $programa) {
        $sql = "SELECT * from (SELECT vm.`cedula`, vm.nombre, vm.`telefono`, vm.`correo`, 
                              vm.programa AS programa, vm.`escuela`, vm.cead AS centro, vm.zona AS zona, vm.`descripcion` AS periodo, 
                              CASE vm.tipo_estudiante WHEN 'H' THEN 'Antiguo' WHEN 'G' THEN 'Nuevo' END AS  tipo_estudiante,
                              ie.fecha,
                              CASE ie.tipo_induccion WHEN '1' THEN 'Presencial' WHEN '2' THEN 'Virtual' END AS  tipo_induccion, 
                              CASE ie.participacion WHEN '1' THEN 'Primera Vez' WHEN '2' THEN 'Reinducción' END AS  participacion
              FROM `vta_matricula` vm, induccion_estudiante ie
              WHERE vm.`periodo_academico_id` = $periodo AND vm.`numero_matriculas`<=1
              AND ie.`estudiante_id` =vm.`estudiante_id` ";

        if ($cead != "T") {
            $sql.= " AND vm.cead_cead_id IN ($cead) ";
        }

        if ($zona != "T") {
            $sql.= " AND vm.zona_id IN ($zona) ";
        }

        if ($escuela != "T") {
            $sql.= " AND vm.`escuela` IN ('$escuela') ";
        }

        if ($programa != "T") {
            $sql.= " AND vm.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND vm.`cedula` LIKE '%$filtro%' AND vm.`nombre` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY vm.`nombre` ASC )AS a ;";

        //echo $sql;

        $res = mysql_query($sql);

        return $res;
    }

    function ReporteEgresados($filtro, $cohorte, $zona, $cead, $escuela, $programa, $momentos, $page_position, $item_per_page) {
        $sql = "select * FROM ( SELECT tt.`DOCUMENTO`, tg.`NOMBRES`, tg.`APELLIDOS`, CONCAT(`NOMBRES`,' ',`APELLIDOS`) AS COMPLETO, tg.`AREA_GEOFRAFICA`,
                tg.`GENERO`, tg.`DIRECCION`, tg.`EMAIL`, 
                CASE WHEN tg.`TELEFONO` IS not NULL THEN tg.`TELEFONO` ELSE 'SIN DATO' END AS TELEFONO,
                `CIUDAD_RESIDENCIA`, tt.`NOMBRE_PROGRAMA`, tt.`ESCUELA`, tt.`CENTRO`, tt.`ZONA`, tt.mes, tt.anio , tt.`NIVEL_ACADEMICO`, 
                tg.`SITUACION_LABORAL`, tg.`ACTIVIDAD_ECONOMICA`, 
                tg.`NOMBRE_EMPRESA`, tg.`DIRECCION_EMPRESA`, 
                CASE WHEN tg.`TELEFONO_EMPRESA` IS not NULL THEN tg.`TELEFONO_EMPRESA` ELSE 'SIN DATO' END AS TELEFONO_EMPRESA,
                tg.`ANTIGUEDAD`, tg.`TIEMPO_DESEMPLEADO`, tg.`CARGO`, tg.`SECTOR`, tg.`RELACION_PROGRAMA_TRABAJO`, ESTADO_IDENTIFICACION, tt.MOMENTO
                FROM SIGRA.`tmp_titulos` tt , SIGRA.`tmp_graduados` tg
                WHERE tg.`DOCUMENTO`=tt.`DOCUMENTO` ";


        if ($momentos != "Todos") {
            $sql.= " AND tt.MOMENTO = '$momentos' ";
        }

        if ($cohorte != "") {
            $sql.= " AND tt.anio = $cohorte ";
        }

        if ($cead != "T") {
            $sql.= " AND tt.`cead_id` IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND tt.`zona_id` IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND tt.`ESCUELA` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND tt.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND tt.`DOCUMENTO` LIKE '%$filtro%' AND tg.`NOMBRES` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY tg.`NOMBRES` ASC )AS a LIMIT $page_position, $item_per_page;";

        //echo $sql;

        $resultado = mysql_query($sql);

        return $resultado;
    }

    function ReporteEgresadosExcel($filtro, $cohorte, $zona, $cead, $escuela, $programa, $momentos, $tipo_reporte) {

        if ($tipo_reporte === "1") {
            $sql = "select * FROM (select tt.`DOCUMENTO`, CONCAT(`NOMBRES`,' ',`APELLIDOS`) AS COMPLETO, tg.`AREA_GEOFRAFICA`, tg.`GENERO`, tg.`DIRECCION`,
                tg.`EMAIL`, CASE WHEN tg.`TELEFONO` IS NOT NULL THEN tg.`TELEFONO` ELSE 'SIN DATO' END AS TELEFONO,
                `CIUDAD_RESIDENCIA`, tt.`NOMBRE_PROGRAMA`, tt.`ESCUELA`, tt.`CENTRO`, tt.`ZONA`, tt.mes, tt.anio , tt.`NIVEL_ACADEMICO`,
                tg.`SITUACION_LABORAL`, tg.`ACTIVIDAD_ECONOMICA`, tg.`NOMBRE_EMPRESA`, tg.`DIRECCION_EMPRESA`, 
                CASE WHEN tg.`TELEFONO_EMPRESA` IS NOT NULL THEN tg.`TELEFONO_EMPRESA` ELSE 'SIN DATO' END AS TELEFONO_EMPRESA,
                tg.`ANTIGUEDAD`, tg.`TIEMPO_DESEMPLEADO`, tg.`RELACION_PROGRAMA_TRABAJO`, ESTADO_IDENTIFICACION, tt.MOMENTO
                FROM SIGRA.`tmp_titulos` tt , SIGRA.`tmp_graduados` tg
                WHERE tg.`DOCUMENTO`=tt.`DOCUMENTO` ";
        } if ($tipo_reporte === "2") {
            $sql = "select * FROM (select tt.`DOCUMENTO`, CONCAT(`NOMBRES`,' ',`APELLIDOS`) AS COMPLETO, tg.`EMAIL`, tt.`NOMBRE_PROGRAMA`, tt.`ESCUELA`, tt.`CENTRO`, tt.`ZONA`, tt.mes, tt.anio , 
                tt.`NIVEL_ACADEMICO`, ESTADO_IDENTIFICACION, tt.MOMENTO, tg.CODIGO_VERIFICACION, tg.PROTECCION_DATOS
                FROM SIGRA.`tmp_titulos` tt , SIGRA.`tmp_graduados` tg
                WHERE tg.`DOCUMENTO`=tt.`DOCUMENTO` ";
        } if ($tipo_reporte === "3") {
            $sql = "select * FROM (
                    SELECT me.`fk_Codigo_Estudiante` AS DOCUMENTO, me.`nombre` AS  COMPLETO, me.`NOMBRE_PROGRAMA`, me.`ESCUELA`, me.`CENTRO`, me.`ZONA`, 
                    me.`pregunta`, me.`descripcion_Respuesta`
                    FROM SIGRA.`M0_Encuesta` me
                    WHERE  me.`fk_Codigo_Estudiante`!='0' ";
        }


        if ($tipo_reporte === "1") {
            if ($momentos != "Todos") {
                $sql.= " AND tt.MOMENTO = '$momentos' ";
            }

            if ($cohorte != "") {
                $sql.= " AND tt.anio = $cohorte ";
            }

            if ($cead != "T") {
                $sql.= " AND tt.`cead_id` IN ($cead) ";
            }
            if ($zona != "T") {
                $sql.= " AND tt.`zona_id` IN ($zona) ";
            }
            if ($escuela != "T") {
                $sql.= " AND tt.`ESCUELA` IN ('$escuela') ";
            }
            if ($programa != "T") {
                $sql.= " AND tt.`programa_id` IN ($programa) ";
            }

            if ($filtro !== 'n') {
                $sql.= " AND tt.`DOCUMENTO` LIKE '%$filtro%' AND tg.`NOMBRES` LIKE '%$filtro%' ";
            }

            $sql.= " ORDER BY tg.`NOMBRES` ASC )AS a ";
        }

        if ($tipo_reporte === "2") {
            if ($momentos != "Todos") {
                $sql.= " AND tt.MOMENTO = '$momentos' ";
            }

            if ($cohorte != "") {
                $sql.= " AND tt.anio = $cohorte ";
            }

            if ($cead != "T") {
                $sql.= " AND tt.`cead_id` IN ($cead) ";
            }
            if ($zona != "T") {
                $sql.= " AND tt.`zona_id` IN ($zona) ";
            }
            if ($escuela != "T") {
                $sql.= " AND tt.`ESCUELA` IN ('$escuela') ";
            }
            if ($programa != "T") {
                $sql.= " AND tt.`programa_id` IN ($programa) ";
            }

            if ($filtro !== 'n') {
                $sql.= " AND tt.`DOCUMENTO` LIKE '%$filtro%' AND tg.`NOMBRES` LIKE '%$filtro%' ";
            }

            $sql.= " ORDER BY tg.`NOMBRES` ASC )AS a ";
        }

        if ($tipo_reporte === "3") {
            if ($cead != "T") {
                $sql.= " AND me.`cead_id` IN ($cead) ";
            }
            if ($zona != "T") {
                $sql.= " AND me.`zona_id` IN ($zona) ";
            }
            if ($escuela != "T") {
                $sql.= " AND me.`ESCUELA` IN ('$escuela') ";
            }
            if ($programa != "T") {
                $sql.= " AND me.`programa_id` IN ($programa) ";
            }
            if ($filtro !== 'n') {
                $sql.= " AND me.`fk_Codigo_Estudiante` LIKE '%$filtro%' AND me.`COMPLETO` LIKE '%$filtro%' ";
            }

            $sql.= " ORDER BY me.`fk_Codigo_Estudiante` ASC )AS a ";
        }


        $resultado = mysql_query($sql);

        return $resultado;
    }

    function ReporteMatriculadosExcel($filtro, $periodo, $zona, $cead, $escuela, $programa) {
        $sql = "select * FROM (SELECT e.`cedula`, e.nombre, e.`telefono`, e.`correo`, CONCAT(e.`usuario`,'@unadvirtual.edu.co') AS institucional, p.`descripcion` AS programa, p.`escuela`, 
                c.`descripcion` AS centro, z.`descripcion` AS zona, pa.`descripcion` AS periodo, CASE m.tipo_estudiante WHEN 'H' THEN 'Antiguo' WHEN 'G' THEN 'Nuevo' END AS  tipo_estudiante , m.numero_matriculas
                FROM matricula m, estudiante e, programa p, cead c, zona z, `periodo_academico` pa
                WHERE 
                m.`estudiante_estudiante_id`= e.`estudiante_id`
                AND p.`programa_id`= m.`programa_programa_id`
                AND c.`cead_id`= e.`cead_cead_id`
                AND z.`zona_id`= c.`zona_zona_id`
                AND pa.`periodo_academico_id`= m.`periodo_academico_periodo_academico_id`
                AND m.`periodo_academico_periodo_academico_id`= $periodo";

        if ($cead != "T") {
            $sql.= " AND c.cead_id IN ($cead) ";
        }
        if ($zona != "T") {
            $sql.= " AND c.zona_zona_id IN ($zona) ";
        }
        if ($escuela != "T") {
            $sql.= " AND p.`escuela` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $sql.= " AND p.`programa_id` IN ($programa) ";
        }

        if ($filtro !== 'n') {
            $sql.= " AND e.`cedula` LIKE '%$filtro%' AND e.`nombre` LIKE '%$filtro%' ";
        }

        $sql.= " ORDER BY e.`nombre` ASC )AS a ";

        //echo $sql.' ';

        $res = mysql_query($sql);

        return $res;
    }

    // Fin - Metodos de Reportes
    // 
    // INICIO METODO ATENCION
    function consultarEstudiante($documento) {
        $sql = "SELECT 
                e.`cedula`, e.`nombre`, e.`correo`, e.`telefono`, pro.`descripcion` AS programa, pro.`escuela`, c.`descripcion` AS cead, z.`descripcion` AS zona, m.`tipo_estudiante`, m.`numero_matriculas`, pa.`descripcion`
                FROM matricula m, `programa` pro, `estudiante` e, cead c, zona z, `periodo_academico` pa
                WHERE m.`periodo_academico_periodo_academico_id` IN (161,162,163,164,165,32,34,35,36,37,22,23,24,28,4,2)
                AND pro.`programa_id`=m.`programa_programa_id`
                AND e.`estudiante_id`=m.`estudiante_estudiante_id`
                AND e.`cead_cead_id`=c.`cead_id`
                AND z.`zona_id`=c.`zona_zona_id`
                AND pa.`periodo_academico_id`=m.`periodo_academico_periodo_academico_id`
                AND e.cedula=$documento";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function consultarGraduado($documento) {
        $sql = "SELECT tt.`DOCUMENTO`, CONCAT(tg.`NOMBRES`,' ',tg.`APELLIDOS`) AS NOMBRE, tg.`EMAIL`, tg.`TELEFONO`,  tt.`NOMBRE_PROGRAMA`, tt.`ESCUELA`,tt.`CENTRO`, tt.`ZONA`, tt.`ANIO`
                FROM SIGRA.`tmp_titulos` tt , SIGRA.`tmp_graduados` tg
                WHERE tg.`DOCUMENTO`=tt.`DOCUMENTO` AND tt.DOCUMENTO=$documento";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function consultarAspirante($documento) {
        $sql = "SELECT cedula, nombre,programa,centro,telefono,direccion,correo FROM `atencion_aspirante` WHERE cedula=$documento";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function consultarAtenciones($documento, $perfil) {
        $sql2 = "";

        if ($perfil === "16") {
            $sql2 = " AND ar.eje_atencion in (1,2,3) ";
        } else {
            $sql2 = " AND ar.eje_atencion in (1,2) ";
        }

        $sql = "SELECT ar.`fecha_atencion`,ar.`categorias`,
               CONCAT(u.`nombre`,' (',CASE ar.`eje_atencion` WHEN 1 THEN 'Consejeria' WHEN 2 THEN 'Monitoria' WHEN 3 THEN 'Crecimiento Personal' END,')') AS nombre, 
               REPLACE(REPLACE(REPLACE(ar.`tipo_atencion`,'1','Aspirante'),'2', 'Estudiante'),'3', 'Graduado') AS tipo, 
               ar.observacion
                FROM `atencion_registro` ar, `usuario` u WHERE ar.`documento`=$documento AND u.`usuario_id`=ar.`usu_log` "
                . $sql2 . " ORDER BY ar.fecha_atencion DESC LIMIT 5 ";

        $resultado = mysql_query($sql);

        $atenciones = "";
        $conteoA = 0;
        while ($row = mysql_fetch_array($resultado)) {
            $fecha_atencion = $row[0];
            $cat = $row[1];
            $atendido_por = $row[2];
            $tipo_atencion = $row[3];
            $observaciones = $row[4];
            $categorias = "";

            //CONSULTAR LAS CATEGORIAS
            $sqlC = "SELECT `descripcion` FROM `atencion_categorias` WHERE `id_categoria` IN ($cat)";
            $resultadoC = mysql_query($sqlC);
            while ($row = mysql_fetch_array($resultadoC)) {
                $categorias.="-" . $row[0] . " ";
            }
            $atenciones.= '<tr>
                <td>' . $fecha_atencion . '</td>
                <td>' . $categorias . '</td>
                <td>' . $atendido_por . '</td>
                <td>' . $tipo_atencion . '</td>
                <td>' . $observaciones . '</td>
            </tr>';
            $conteoA ++;
        }

        if ($conteoA <= 0) {
            $atenciones = '<tr>
                                    <td colspan="2">No hay registros de atención</td>
                                </tr>';
        }
        return $atenciones;
    }

    function registraAspirante($nombre, $correo, $programa_at, $centro_at, $telefono, $direccion, $cedula_at, $usuario_log) {
        $sql = "select count(cedula) as conteo from atencion_aspirante where cedula=$cedula_at";
        $result = mysql_query($sql) or die(mysql_error() . $sql);
        $con = 0;
        while ($row = mysql_fetch_array($result)) {
            $con = $row[0];
        }

        if ($con <= 0) {
            $sql = "INSERT INTO SIVISAE.`atencion_aspirante` (`cedula`,`nombre`,`programa`,`centro`,`telefono`,`direccion`,`correo`, usu_log, fecha_log)
                VALUES ('$cedula_at','$nombre',$programa_at,$centro_at,'$telefono','$direccion','$correo', $usuario_log, CURRENT_TIMESTAMP)";
        } else {
            $sql = "UPDATE `atencion_aspirante` SET `nombre`='$nombre', `programa`=$programa_at,`centro`=$centro_at,`telefono`='$telefono',`direccion`='$direccion', `correo`='$correo', usu_log=$usuario_log, fecha_log=CURRENT_TIMESTAMP WHERE cedula=$cedula_at";
        }

        $result = mysql_query($sql) or die(mysql_error() . $sql);
        return $result;
    }

    function registraAtencion($cedula_at, $cat_atencion, $atencion_b, $usuario_log, $perfil, $observacion) {
        $eje_atencion = 0;
        if ($perfil == 9) {
            $eje_atencion = 2;
        } else if ($perfil == 16) {
            $eje_atencion = 3;
        } else {
            $eje_atencion = 1;
        }

        $sql = "INSERT INTO `atencion_registro` (`documento`,`categorias`,`tipo_atencion`,`fecha_atencion`, usu_log, eje_atencion, observacion)
                VALUES ($cedula_at,'$cat_atencion', '$atencion_b', CURRENT_TIMESTAMP, $usuario_log, $eje_atencion, '$observacion')";
        //echo $sql;
        $result = mysql_query($sql) or die(mysql_error() . $sql);
        return $result;
    }

    // FIN METODO ATENCION
    // 
    // 
    // 
    // 
    // 
    // 
    // 
    //INICIO - METODOS SIGRA


    function ciudades() {
        $sql = "SELECT DISTINCT municipio_id, municipio FROM SIGRA.municipio where estado = 1 order by municipio asc";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function getCiudades($pais) {
        $sql = "SELECT DISTINCT l.id, l.nombre, r.nombre
                FROM SIGRA.localidades l 
                   INNER JOIN SIGRA.regiones r ON r.id = l.id_region AND r.id_idioma = 7 
                WHERE l.id_pais = $pais AND l.id_idioma = 7 ORDER BY r.nombre ASC;";
        $resultado = $this->consulta2($sql);
//        echo $sql;
        return $resultado;
    }

    function paises() {
        $sql = "SELECT id, nombre FROM SIGRA.paises WHERE id_idioma = 7 ORDER BY nombre ASC; ";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function buscarGraduado($documento) {
        $sql = "SELECT g.graduado_id AS graduado, 
                    g.tipo_doc, g.documento, LOWER(g.nombre) AS nombre, LOWER(g.apellido) AS apellido, g.fecha_nac, c.id_pais as pais_nac, 
                    g.ciudad_nac as cod_ciudad_nac, c.nombre as ciudad_nac, g.sexo, c2.id_pais as pais_residencia, g.ciudad_residencia as cod_ciudad_res, 
                    c2.nombre as ciudad_residencia, g.direccion_residencia, g.estrato, g.telefono_residencia, g.telefono_celular, LOWER(g.email) AS email, 
                    LOWER(g.email_2) AS email_2, g.estado_civil, LOWER(f.nombre) AS nombre_fam, f.parentezco, f.telefono AS tel_fam, f.celular as cel_fam, 
                    f.email AS email_fam, l.situacion, LOWER(l.nombre_empresa) AS nombre_empresa, LOWER(l.cargo) AS cargo, l.telefono_of, 
                    l.sector_economico_id as ciiu, l.relacion_unad, LOWER(l.email_of) as email_lab  
                FROM SIGRA.graduado g 
                  LEFT OUTER JOIN SIGRA.datos_familiares f ON f.graduado_id = g.graduado_id 
                  LEFT OUTER JOIN SIGRA.datos_laborales l ON l.graduado_id = g.graduado_id 
                  LEFT OUTER JOIN SIGRA.localidades c ON c.id = g.ciudad_nac 
                  LEFT OUTER JOIN SIGRA.localidades c2 ON c2.id = g.ciudad_residencia  
                WHERE g.documento = '$documento' order by g.graduado_id desc limit 1;";
        $resultado1 = $this->consulta2($sql);
        if (mysqli_num_rows($resultado1) === 0) {
            $sql2 = "SELECT 'x' AS graduado, 
                      g.`TIPO_DOCUMENTO` AS tipo_doc, g.`DOCUMENTO` AS documento, LOWER(g.`NOMBRES`) AS nombre, LOWER(g.`APELLIDOS`) AS apellido, 
                      g.`FECHA_NACIMIENTO` AS fecha_nac, '' AS pais_nac, g.`CIUDAD_NACIMIENTO` as ciudad_nac, g.`GENERO` AS sexo, '' AS pais_residencia, 
                      g.`CIUDAD_RESIDENCIA` AS ciudad_residencia, g.`DIRECCION` AS direccion_residencia, g.`ESTRATO` AS estrato, 
                      g.`TELEFONO` AS telefono_residencia, g.`MOVIL` AS telefono_celular, LOWER(REPLACE(REPLACE(`EMAIL`,'||',';'),' ', '')) AS email,'' AS email_2, 
                      g.`ESTADO_CIVIL` AS estado_civil, '' AS nombre_fam,'' AS parentezco,'' AS tel_fam,'' AS cel_fam,'' AS email_fam,
                      g.`SITUACION_LABORAL` AS situacion, LOWER(g.`NOMBRE_EMPRESA`) AS nombre_empresa, LOWER(g.`CARGO`) AS cargo, 
                      g.`TELEFONO_EMPRESA` AS telefono_of, g.`CODIGO_CIIU` AS ciiu, g.`RELACION_PROGRAMA_TRABAJO` AS relacion_unad, '' as email_lab 
                    FROM `SIGRA`.`tmp_graduados` g 
                      LEFT OUTER JOIN SIGRA.tmp_titulos t ON t.`DOCUMENTO` = g.`DOCUMENTO` 
                    WHERE g.`DOCUMENTO` = '$documento' AND g.estado in (1, 99)
                    ORDER BY documento DESC
                    LIMIT 1;";
            $resultado2 = $this->consulta2($sql2);
            if (mysqli_num_rows($resultado2) === 0) {
                return 'no';
            } else {
                return $resultado2;
            }
        } else {
            return $resultado1;
        }
    }

    function verificarGraduadoExterno($documento, $codigo) {
        $sql = "SELECT DOCUMENTO FROM SIGRA.tmp_graduados WHERE DOCUMENTO='$documento' AND CODIGO_VERIFICACION='$codigo'";
        $resultado1 = $this->consulta2($sql);

        if (mysqli_num_rows($resultado1) === 0) {
            return false;
        } else {
            return true;
        }
    }

    function crearGraduado($tipo_doc, $documento, $nombre, $apellido, $fecha_nac, $ciudad_nac, $sexo, $ciudad_residencia, $direccion_residencia, $estrato, $telefono_residencia, $telefono_celular, $email, $email_2, $estado_civil, $nombre_fam, $parentezco, $telefono_fam, $cel_fam, $email_fam, $situacion, $nombre_empresa, $cargo, $telefono_of, $email_lab, $sector_economico_id, $relacion_unad, $privacy, $arrayTitulos) {
        $sql = "INSERT INTO `SIGRA`.`graduado`
                    (`tipo_doc`,`documento`,`nombre`,`apellido`,`fecha_nac`,`ciudad_nac`,`sexo`,`ciudad_residencia`,`direccion_residencia`,`estrato`,
                        `telefono_residencia`,`telefono_celular`,`email`,`email_2`,`estado_civil`,`fecha_creacion`, `privacy`)
                VALUES ('$tipo_doc','$documento',UPPER('$nombre'),UPPER('$apellido'),'$fecha_nac', '$ciudad_nac','$sexo','$ciudad_residencia',
                        UPPER('$direccion_residencia'),'$estrato','$telefono_residencia','$telefono_celular',UPPER('$email'),UPPER('$email_2'),
                        '$estado_civil',CURRENT_TIMESTAMP, '$privacy');";
        $resultado = $this->consulta2($sql);
        $graduado_id = mysqli_insert_id($this->getConexion2());
//        $graduado_id = "3";
//        echo "SQL: ".$sql;
        $sql2 = "INSERT INTO `SIGRA`.`datos_familiares`
                    (`graduado_id`,`nombre`,`parentezco`,`telefono`,`celular`,`email`)
                VALUES ('$graduado_id',UPPER('$nombre_fam'),'$parentezco','$telefono_fam','$cel_fam',UPPER('$email_fam'));";
        $resultado += $this->consulta2($sql2);
//        echo "<br>SQL2: ".$sql2;
        $sql3 = "INSERT INTO `SIGRA`.`datos_laborales`
                    (`graduado_id`,`situacion`,`nombre_empresa`,`cargo`,`telefono_of`,`email_of`,`sector_economico_id`,`relacion_unad`)
                VALUES ('$graduado_id','$situacion',UPPER('$nombre_empresa'),'$cargo','$telefono_of',UPPER('$email_lab'), 
                    '$sector_economico_id','$relacion_unad');";
        $resultado += $this->consulta2($sql3);
//        echo "<br>SQL3: ".$sql3;
        $resultado +=$this->agregarTitulos($graduado_id, $arrayTitulos);

        $sql6 = "UPDATE `SIGRA`.`tmp_graduados` SET FECHA_ACTUALIZACION = now() WHERE `DOCUMENTO` = '$documento';";
        $resultado += $this->consulta2($sql6);
        $sql5 = "UPDATE `SIGRA`.`tmp_graduados` SET `estado` = '2' WHERE `DOCUMENTO` = '$documento' AND `estado` in (1,99);";
        $resultado += $this->consulta2($sql5);

        return $resultado;
    }

    function actualizarGraduado($graduado_id, $tipo_doc, $documento, $nombre, $apellido, $fecha_nac, $ciudad_nac, $sexo, $ciudad_residencia, $direccion_residencia, $estrato, $telefono_residencia, $telefono_celular, $email, $email_2, $estado_civil, $nombre_fam, $parentezco, $telefono_fam, $cel_fam, $email_fam, $situacion, $nombre_empresa, $cargo, $telefono_of, $email_lab, $sector_economico_id, $relacion_unad, $privacy, $arrayTitulos) {

        $sql = "CALL SIGRA.update_graduado($graduado_id);";
        $resultado = $this->consulta2($sql);
        $sql2 = "UPDATE `SIGRA`.`graduado`
                SET 
                  `tipo_doc` = '$tipo_doc',
                  `documento` = '$documento',
                  `nombre` = '$nombre',
                  `apellido` = '$apellido',
                  `fecha_nac` = '$fecha_nac',
                  `ciudad_nac` = '$ciudad_nac',
                  `sexo` = '$sexo',
                  `ciudad_residencia` = '$ciudad_residencia',
                  `direccion_residencia` = '$direccion_residencia',
                  `estrato` = '$estrato',
                  `telefono_residencia` = '$telefono_residencia',
                  `telefono_celular` = '$telefono_celular',
                  `email` = '$email',
                  `email_2` = '$email_2',
                  `estado_civil` = '$estado_civil',
                  `fecha_modificacion` = CURRENT_TIMESTAMP, 
                  `privacy` = '$privacy'
                WHERE `graduado_id` = '$graduado_id';";
//        echo $sql2;
        $resultado += $this->consulta2($sql2);
        $sql3 = "UPDATE `SIGRA`.`datos_familiares`
                SET 
                  `nombre` = '$nombre_fam',
                  `parentezco` = '$parentezco',
                  `telefono` = '$telefono_fam',
                  `celular` = '$cel_fam',
                  `email` = '$email_fam'
                WHERE `graduado_id` = '$graduado_id';";
        $resultado += $this->consulta2($sql3);
        $sql4 = "UPDATE `SIGRA`.`datos_laborales`
                SET 
                  `situacion` = '$situacion',
                  `nombre_empresa` = '$nombre_empresa',
                  `cargo` = '$cargo',
                  `telefono_of` = '$telefono_of',
                  `email_of` = '$email_lab',
                  `sector_economico_id` = '$sector_economico_id',
                  `relacion_unad` = '$relacion_unad'
                WHERE `graduado_id` = '$graduado_id';";

        $sql6 = "UPDATE `SIGRA`.`tmp_graduados` SET FECHA_ACTUALIZACION = now() WHERE `DOCUMENTO` = '$documento';";
        $resultado += $this->consulta2($sql6);

        $resultado += $this->consulta2($sql4);
        $resultado +=$this->agregarTitulos($graduado_id, $arrayTitulos);
        return $resultado;
    }

    function getTitulos($documento, $id) {
        $sql = "";
        if ($id === 'x') {
            $sql = "SELECT 'n' as titulo_id, 
                          lower(p.descripcion) as programa, p.codigo AS cod_prog, lower(p.escuela) as escuela, 
                          lower(c.descripcion) as cead, c.codigo AS cod_cead, 
                          CONCAT(`ANIO`,'-',
                                CASE LOWER(`MES`) 
                                   WHEN 'enero' THEN '01' 
                                   WHEN 'febrero' THEN '02' 
                                   WHEN 'marzo' THEN '03' 
                                   WHEN 'abril' THEN '04' 
                                   WHEN 'mayo' THEN '05' 
                                   WHEN 'junio' THEN '06' 
                                   WHEN 'julio' THEN '07' 
                                   WHEN 'agosto' THEN '08' 
                                   WHEN 'septiembre' THEN '09' 
                                   WHEN 'octubre' THEN '10' 
                                   WHEN 'noviembre' THEN '11' 
                                   WHEN 'diciembre' THEN '12' END
                                ,'-',`DIA`) AS fecha_grado, 
                          lower(z.descripcion) AS zona
                        FROM `SIGRA`.`tmp_titulos` tt
                          INNER JOIN SIGRA.programa p ON p.codigo = tt.`CODIGO_PROGRAMA` 
                          INNER JOIN SIGRA.cead c ON c.codigo = tt.`CODIGO_CENTRO` 
                          INNER JOIN SIGRA.zona z ON z.zona_id = c.zona_zona_id
                        WHERE `DOCUMENTO` = $documento;";
        } else {
            $sql = "SELECT tg.titulo_id, 
                          lower(p.descripcion) as programa, p.codigo AS cod_prog, lower(p.escuela) as escuela, 
                          lower(c.descripcion) as cead, c.codigo AS cod_cead, tg.fecha_grado AS fecha_grado, lower(z.descripcion) AS zona 
                        FROM SIGRA.titulo_graduado tg 
                          INNER JOIN SIGRA.programa p ON p.codigo = tg.programa_id 
                          INNER JOIN SIGRA.cead c ON c.codigo = tg.cead_id 
                          INNER JOIN SIGRA.zona z ON z.zona_id = c.zona_zona_id 
                        WHERE tg.graduado_id = $id 
                        ORDER BY tg.fecha_grado DESC;";
        }
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function getEscuela($cod_programa) {
        $sql = "SELECT DISTINCT LOWER(escuela) AS escuela, LOWER(tp.descripcion) AS tp_prog
                FROM SIGRA.programa p
                 LEFT OUTER JOIN SIGRA.tipo_programa tp ON tp.tipo_programa_id = p.tipo_programa_tipo_programa_id 
                WHERE codigo = $cod_programa;";
        $resultado = $this->consulta2($sql);
        $escuela = mysqli_fetch_array($resultado);
        return ucwords($escuela[0]) . '|' . ucwords($escuela[1]);
    }

    function getZona($cead) {
        $sql = "SELECT lower(z.descripcion) as zona  
                FROM zona z 
                   INNER JOIN SIGRA.cead c ON c.zona_zona_id = z.zona_id AND c.codigo = $cead;";
        $resultado = $this->consulta2($sql);
        $zona = mysqli_fetch_array($resultado);
        return ucwords($zona[0]);
    }

    function listadoCiiu() {
        $sql = "select * from SIGRA.ciiu order by descripcion asc;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function agregarTitulos($graduado_id, $arrayTitulos) {
        $sql = "DELETE FROM SIGRA.titulo_graduado WHERE graduado_id = $graduado_id;";
        $resultado = $this->consulta2($sql);

        $sql2 = "INSERT INTO `SIGRA`.`titulo_graduado`
                    (`graduado_id`,`programa_id`,`cead_id`,`fecha_grado`,estado)
                    VALUES ";
        $titulos = array();
        foreach ($arrayTitulos as $titulo) {
            $titulos[] = "('$graduado_id','" . implode("','", $titulo) . "',2)";
        }
        $sql2 .= implode(",", $titulos) . ";";
//        echo "SQL4: ".$sql4;
        $resultado += $this->consulta2($sql2);
        return $resultado;
    }

    function cantGraduados($buscar, $programa, $escuela, $cead, $zona) {
        $arrwhere1 = array();
        $arrwhere2 = array();
        if ($escuela != "T") {
            $arrwhere1[] = " p.escuela IN ('$escuela') ";
            $arrwhere2[] = " lower(tt.`ESCUELA`) IN ('$escuela') ";
        }
        if ($programa != "T") {
            $arrwhere1[] = " p.codigo IN ('$programa') ";
            $arrwhere2[] = " tt.`CODIGO_PROGRAMA` IN ('$programa') ";
        }
        if ($cead != "T") {
            $arrwhere1[] = " c.codigo IN ('$cead') ";
            $arrwhere2[] = " tt.`CODIGO_CENTRO` IN ('$cead') ";
        }
        if ($zona != "T") {
            $arrwhere1[] = " c.zona_zona_id IN (select zona_id from zona where nomenclatura in ('$zona')) ";
            $arrwhere2[] = " tt.`NOMENCLATURA_ZONA` IN ('$zona') ";
        }
        $where1 = count($arrwhere1) > 0 ? " WHERE " . implode(" AND ", $arrwhere1) : "";
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT COUNT(1) as cant
                FROM (
                    SELECT DISTINCT 
                      g.graduado_id, g.documento,LOWER(g.nombre) AS nombre,LOWER(g.apellido) AS apellido, 
                      CASE WHEN g.fecha_modificacion IS not NULL THEN g.fecha_modificacion ELSE g.fecha_creacion END AS fecha_mod
                    FROM SIGRA.graduado g 
                      INNER JOIN SIGRA.datos_laborales dl ON dl.graduado_id = g.graduado_id 
                      INNER JOIN SIGRA.titulo_graduado t ON t.graduado_id = g.graduado_id 
                      INNER JOIN SIGRA.programa p ON p.programa_id = t.programa_id 
                      INNER JOIN SIGRA.cead c ON c.cead_id = t.cead_id 
                      $where1 
                    UNION
                    SELECT DISTINCT 
                      'x' AS grduado_id, tg.`DOCUMENTO` AS documento, LOWER(tg.`NOMBRES`) AS nombre, LOWER(tg.`APELLIDOS`) AS apellido, 'NO' AS fecha_mod    
                    FROM `SIGRA`.`tmp_graduados` tg 
                      INNER JOIN SIGRA.tmp_titulos tt ON tt.`DOCUMENTO` = tg.`DOCUMENTO` 
                    WHERE tg.estado = '1' $where2 )A
                WHERE A.documento LIKE '%$buscar%' OR A.nombre LIKE '%$buscar%' OR A.apellido LIKE '%$buscar%' ";

        $sql .= ";";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function listaGraduados($page_position, $item_per_page, $buscar, $programa, $escuela, $cead, $zona) {
        $arrwhere1 = array();
        $arrwhere2 = array();
        if ($escuela != "T") {
            $arrwhere1[] = " p.escuela IN ('$escuela') ";
            $arrwhere2[] = " tt.`ESCUELA` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $arrwhere1[] = " p.codigo IN ('$programa') ";
            $arrwhere2[] = "tt.`CODIGO_PROGRAMA` IN ('$programa') ";
        }
        if ($cead != "T") {
            $arrwhere1[] = " c.codigo IN ('$cead') ";
            $arrwhere2[] = " tt.`CODIGO_CENTRO` IN ('$cead') ";
        }
        if ($zona != "T") {
            $arrwhere1[] = " c.zona_zona_id IN (select zona_id from zona where nomenclatura in ('$zona')) ";
            $arrwhere2[] = " tt.`NOMENCLATURA_ZONA` IN ('$zona') ";
        }
        $where1 = count($arrwhere1) > 0 ? " WHERE " . implode(" AND ", $arrwhere1) : "";
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT distinct A.* 
                FROM (
                    SELECT DISTINCT 
                      g.graduado_id, g.documento,LOWER(g.nombre) AS nombre,LOWER(g.apellido) AS apellido, lower(g.email) as email, 
                      CASE WHEN g.fecha_modificacion IS not NULL THEN g.fecha_modificacion ELSE g.fecha_creacion END AS fecha_mod
                    FROM SIGRA.graduado g 
                      INNER JOIN SIGRA.datos_laborales dl ON dl.graduado_id = g.graduado_id 
                      INNER JOIN SIGRA.titulo_graduado t ON t.graduado_id = g.graduado_id 
                      INNER JOIN SIGRA.programa p ON p.programa_id = t.programa_id 
                      INNER JOIN SIGRA.cead c ON c.cead_id = t.cead_id 
                      $where1 
                    UNION
                    SELECT DISTINCT 
                      'x' AS grduado_id, tg.`DOCUMENTO` AS documento, LOWER(tg.`NOMBRES`) AS nombre, LOWER(tg.`APELLIDOS`) AS apellido,
                      lower(tg.`EMAIL`) AS email, 'NO' AS fecha_mod    
                    FROM `SIGRA`.`tmp_graduados` tg 
                      INNER JOIN SIGRA.tmp_titulos tt ON tt.`DOCUMENTO` = tg.`DOCUMENTO` 
                    WHERE tg.estado = '1' $where2 )A
                WHERE A.documento LIKE '%$buscar%' OR A.nombre LIKE '%$buscar%' OR A.apellido LIKE '%$buscar%' ";

        $sql .= " ORDER BY A.apellido ASC "
                . "LIMIT $page_position, $item_per_page  ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function crearCredencialGraduado($doc, $mail, $link, $usuario, $clave, $token) {
        $sql = "INSERT INTO `SIGRA`.`login_graduado`
                            (`documento`,`email`,`link`,`estado`,`fecha_envio`,`usuario`,`clave`,`token`)
                VALUES ('$doc','$mail','$link','1',CURRENT_TIMESTAMP,'$usuario',MD5('$clave'),'$token');";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function token() {
        $rand_pass = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(48, 57)) . chr(rand(48, 57)) . chr(rand(48, 57)) . chr(rand(48, 57));
        return $rand_pass;
    }

    function getLinea($eje) {
        $sql = "SELECT linea_id, LOWER(descripcion) FROM SIVISAE.`linea` WHERE estado_id = 1";
        if ($eje != "") {
            $sql.=" and ejes='$eje' ";
        }
        $resultado = $this->consulta2($sql);
        $arr = array();
        while ($linea = mysqli_fetch_array($resultado)) {
            $arr[] = $linea;
        }
        return $arr;
    }

    function getCobertura() {
        $sql = "SELECT cobertura_id, LOWER(descripcion) FROM SIVISAE.`cobertura` WHERE estado_id = 1;";
        $resultado = $this->consulta2($sql);
        $arr = array();
        while ($cobertura = mysqli_fetch_array($resultado)) {
            $arr[] = $cobertura;
        }
        return $arr;
    }

    function crearLinea($descripcion, $eje) {
        $ver = "SELECT `linea_id`  FROM `SIVISAE`.`linea`  WHERE descripcion = '$descripcion';";
        $resultado = $this->consulta2($ver);
        if (mysqli_num_rows($resultado) === 0) {
            $sql = "INSERT INTO `SIVISAE`.`linea` (`descripcion`,`estado_id`, ejes) 
                VALUES (UPPER('$descripcion'),'1', '$eje') ;";
            $resultado = $this->consulta2($sql);
            return mysqli_insert_id($this->getConexion2());
        } else {
            return '0';
        }
    }

    function crearCobertura($descripcion) {
        $ver = "SELECT  `cobertura_id` FROM `SIVISAE`.`cobertura`  WHERE descripcion = '$descripcion';";
        $resultado = $this->consulta2($ver);
        if (mysqli_num_rows($resultado) === 0) {
            $sql = "INSERT INTO `SIVISAE`.`cobertura` (`descripcion`,`estado_id`) 
                VALUES (UPPER('$descripcion'),'1') ;";
            $resultado = $this->consulta2($sql);
            return mysqli_insert_id($this->getConexion2());
        } else {
            return '0';
        }
    }

    function crearProyecto($nombre, $eje, $linea_id, $cobertura_id, $cobertura, $usuario_id, $presupuesto) {
        $ver = "SELECT `proyecto_id` FROM `SIVISAE`.`proyecto` WHERE nombre = '$nombre';";
        $resultado = $this->consulta2($ver);
        if (mysqli_num_rows($resultado) === 0) {
            $sql = "INSERT INTO `SIVISAE`.`proyecto` (`nombre`,`eje`,`linea_id`,`cobertura_id`,cobertura,`fecha_creacion`,`usuario_id`,`estado_id`, presupuesto) 
                VALUES (UPPER('$nombre'),'$eje','$linea_id','$cobertura_id',UPPER('$cobertura'),CURRENT_TIMESTAMP,'$usuario_id','1', '$presupuesto') ;";
            $resultado = $this->consulta2($sql);
//            return mysqli_insert_id($this->getConexion2());
            return "uno";
        } else {
            return "cero";
        }
    }

    function traerProyectos($page_position, $item_per_page) {
        $limit = "";
        if ($page_position !== '') {
            $limit = " LIMIT $page_position, $item_per_page ";
        }
        $sql = "SELECT 
                  `proyecto_id`,LOWER(`nombre`),LOWER(`eje`),LOWER(l.`descripcion`),LOWER(c.`descripcion`), eje, l.linea_id, c.cobertura_id   
                FROM `SIVISAE`.`proyecto`  p
                  INNER JOIN SIVISAE.`linea` l ON l.`linea_id`=p.`linea_id` 
                  INNER JOIN SIVISAE.`cobertura` c ON c.`cobertura_id`=p.`cobertura_id` 
                 WHERE p.estado_id = 1
                 $limit ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function traerProyecto($id) {
        $sql = "SELECT 
                  `proyecto_id`,LOWER(`nombre`), eje, l.linea_id, c.cobertura_id, 
                  cobertura as cobertura, LOWER(eje) as ejee, LOWER(l.descripcion) as linea, LOWER(c.descripcion) as cobertura, 
                  presupuesto
                FROM `SIVISAE`.`proyecto`  p
                  INNER JOIN SIVISAE.`linea` l ON l.`linea_id`=p.`linea_id` 
                  INNER JOIN SIVISAE.`cobertura` c ON c.`cobertura_id`=p.`cobertura_id` 
                 WHERE p.estado_id = 1 and proyecto_id = $id;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function updateProyecto($proy_id, $eje, $linea_id, $cobertura_id, $cobertura, $usuario_id, $presupuesto) {
        $sql = "UPDATE `SIVISAE`.`proyecto`
                    SET
                        `eje` = '$eje',
                        `linea_id` = $linea_id,
                        `cobertura_id` = $cobertura_id,
                         cobertura = UPPER('$cobertura'), 
                        `fecha_edicion` = CURRENT_TIMESTAMP,
                        `usr_id_modif` = $usuario_id,
                        presupuesto = '$presupuesto'
                    WHERE `proyecto_id` = $proy_id;";
        $resultado = $this->consulta2($sql);
        return $resultado;
//        }else {
//            return 'cero';
//        }
    }

    function deleteProyecto($proy_id) {
        $sql = "UPDATE `SIVISAE`.`proyecto`
                    SET `estado_id` = 3 
                    WHERE `proyecto_id` = $proy_id;";
        $resultado = $this->consulta2($sql);
        $banAct = mysqli_affected_rows($this->getConexion2());
        if ($banAct > 0) {
            return true;
        } else {
            return false;
        }
    }

    function crearOrganizador($nombre, $telefono, $mail) {
        $ver = "SELECT 
                  `organizador_id`,`nombre`,`telefono`,  `email`
                FROM
                  `SIGRA`.`organizador` 
                WHERE nombre LIKE '%$nombre%' or email LIKE '%$mail%';";
        $resultado = $this->consulta2($ver);
        if (mysqli_num_rows($resultado) === 0) {
            $sql = "INSERT INTO SIGRA.organizador "
                    . "(nombre, telefono, email, estado_id) "
                    . "VALUES (UPPER('$nombre'), '$telefono', UPPER('$mail'), '1');";
            $resultado = $this->consulta2($sql);
            return mysqli_insert_id($this->getConexion2());
        } else {
            return '0';
        }
    }

    function getOrganizadores() {
        $sql = "SELECT 
                  `organizador_id`,`nombre`,`telefono`,  `email`
                FROM
                  `SIGRA`.`organizador` ;";
        $resultado = $this->consulta2($sql);
        $arr = array();
        while ($org = mysqli_fetch_array($resultado)) {
            $arr[] = $org;
        }
        return $arr;
    }

    function crearEvento($nombre, $fecha_inicio, $fecha_fin, $lugar, $organizador_id, $poblacion, $cant_cupos, $url_banner, $url_doc_soporte, $proyecto_id, $tipo_asistencia, $usuario_id_crea) {
        $ver = "SELECT `evento_id` FROM `SIGRA`.`evento` WHERE nombre = '$nombre';";
        $resultado = $this->consulta2($ver);
        if (mysqli_num_rows($resultado) === 0) {
            $sql = "INSERT INTO `SIGRA`.`evento` (
                    `nombre`,  `fecha_inicio`,`fecha_fin`,`lugar`,`organizador_id`,`poblacion`,`cant_cupos`, `inscritos`,`url_banner`,
                    `url_doc_soporte`,`estado_id`,`proyecto_id`,`tipo_asistencia`,`fecha_creacion`,`usuario_id_crea`) 
                VALUES
                      (UPPER('$nombre'),'$fecha_inicio 00:00:00','$fecha_fin 23:59:59',UPPER('$lugar'),'$organizador_id','$poblacion','$cant_cupos', '0','$url_banner','$url_doc_soporte',
                      '1','$proyecto_id','$tipo_asistencia',CURRENT_TIMESTAMP,'$usuario_id_crea');";
            $resultado = $this->consulta2($sql);
            $even_id = mysqli_insert_id($this->getConexion2());
            $serv = $_SERVER['DOCUMENT_ROOT'] . '/sigra/';
            $temporal = $serv . "tmp/";
            $busc_arch = $usuario_id_crea . SEPARADOR . "tmp" . SEPARADOR;
            $ruta = $serv . "eventos/" . date("Y") . "/";
            if (file_exists($temporal)) {
                if (!file_exists($ruta)) {
                    mkdir($ruta, 0777, TRUE);
                }
            }
            $arch = array();
            if ($aux = opendir($temporal)) {
                while (($archivo = readdir($aux)) !== false) {
                    if ($archivo != "." && $archivo != "..") {
                        if (stristr($archivo, $busc_arch) !== false) {
                            $newArch = explode(SEPARADOR, $archivo);
                            if (rename($temporal . $archivo, $ruta . $even_id . SEPARADOR . $newArch[2] . SEPARADOR . $newArch[3])) {
                                $arch[] = $even_id . SEPARADOR . $newArch[2] . SEPARADOR . $newArch[3];
                            }
                        }
                    }
                }
            }
            closedir($aux);
            $this->consulta2("UPDATE  `SIGRA`.`evento`  
                                                    SET `url_banner` = '$arch[0]', `url_doc_soporte` = '$arch[1]' 
                                                    WHERE evento_id = $even_id ;");
            return "uno";
        } else {
            return "cero";
        }
    }

    function getEventos($page_position, $item_per_page) {
        $limit = "";
        if ($page_position !== '') {
            $limit = " LIMIT $page_position, $item_per_page ";
        }
        $sql = "SELECT 
                  e.`evento_id`,lower(e.`nombre`) as nombre,substring(e.`fecha_inicio`,1,10) as fecha_inicio,substring(e.`fecha_fin`,1,10) as fecha_fin,
                  lower(e.`lugar`) as lugar,e.`organizador_id`,lower(o.`nombre`) AS organizador,lower(e.`poblacion`) as poblacion, e.`cant_cupos`,
                  e.`url_banner`,e.`url_doc_soporte`,e.`estado_id`,e.`proyecto_id`, lower(p.`nombre`) AS proyecto,lower(e.`tipo_asistencia`) as asist,
                  e.`fecha_creacion`,e.`usuario_id_crea`  
                FROM
                  `SIGRA`.`evento`   e 
                  INNER JOIN SIGRA.`proyecto` p ON p.`proyecto_id` = e.`proyecto_id` 
                  INNER JOIN SIGRA.`organizador` o ON o.`organizador_id` = e.`organizador_id` 
                WHERE e.estado_id = 1 
                $limit;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function traerEvento($evento_id) {
        $sql = "SELECT 
                  e.`evento_id`,lower(e.`nombre`) as nombre,substring(e.`fecha_inicio`,1,10) as fecha_inicio,substring(e.`fecha_fin`,1,10) as fecha_fin,
                  lower(e.`lugar`) as lugar,e.`organizador_id`,lower(o.`nombre`) AS organizador,lower(e.`poblacion`) as poblacion, e.`cant_cupos`,
                  e.`url_banner`,e.`url_doc_soporte`,e.`estado_id`,e.`proyecto_id`, lower(p.`nombre`) AS proyecto,lower(e.`tipo_asistencia`) as asist,
                  e.`fecha_creacion`,e.`usuario_id_crea`, e.poblacion as poblacion_id, e.tipo_asistencia as asist_id   
                FROM
                  `SIGRA`.`evento`   e 
                  INNER JOIN SIGRA.`proyecto` p ON p.`proyecto_id` = e.`proyecto_id` 
                  INNER JOIN SIGRA.`organizador` o ON o.`organizador_id` = e.`organizador_id` 
                WHERE e.evento_id = $evento_id;";
//        echo $sql;
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function updateEvento($evento_id, $fecha_inicio, $fecha_fin, $lugar, $organizador_id, $poblacion, $cant_cupos, $url_banner, $url_doc_soporte, $proyecto_id, $tipo_asistencia, $usuario_id_mod) {
        $sql = "UPDATE `SIGRA`.`evento`
                SET
                    `fecha_inicio` = '$fecha_inicio 00:00:00',
                    `fecha_fin` = '$fecha_fin 23:59:59',
                    `lugar` = UPPER('$lugar'),
                    `organizador_id` = $organizador_id,
                    `poblacion` = UPPER('$poblacion'),
                    `cant_cupos` = $cant_cupos,
                    `url_banner` = '$url_banner',
                    `url_doc_soporte` = '$url_doc_soporte',
                    `proyecto_id` = $proyecto_id,
                    `tipo_asistencia` = '$tipo_asistencia',
                    `fecha_edicion` = CURRENT_TIMESTAMP,
                    `usuario_id_modif` = $usuario_id_mod
                WHERE `evento_id` = $evento_id;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function buscarParticipante($documento) {
        $sql = "SELECT `participante_id`,`documento`,`nombre`,`estamento`,`celular`,`telefono`,`email` 
                FROM `SIGRA`.`participante_evento` 
                WHERE documento = '$documento';";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function cantListadoVerificacion($buscar, $programa, $escuela, $cead, $zona) {
        $arrwhere1 = array();
        if ($escuela != "T") {
            $arrwhere1[] = " p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $arrwhere1[] = " p.codigo IN ('$programa') ";
        }
        if ($cead != "T") {
            $arrwhere1[] = " c.codigo IN ('$cead') ";
        }
        if ($zona != "T") {
            $arrwhere1[] = " c.zona_zona_id IN (select zona_id from zona where nomenclatura in ('$zona')) ";
        }
        $where1 = count($arrwhere1) > 0 ? " AND " . implode(" AND ", $arrwhere1) : "";
        $sql = "SELECT COUNT(DISTINCT g.graduado_id) AS cant
                    FROM SIGRA.graduado g 
                      INNER JOIN SIGRA.titulo_graduado t ON t.graduado_id = g.graduado_id 
                WHERE t.estado = 2 $where1 
                    AND (g.documento LIKE '%$buscar%' OR g.nombre LIKE '%$buscar%' OR g.apellido LIKE '%$buscar%'); ";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function listadoVerificacion($page_position, $item_per_page, $buscar, $programa, $escuela, $cead, $zona) {
        $arrwhere1 = array();
        if ($escuela != "T") {
            $arrwhere1[] = " p.escuela IN ('$escuela') ";
        }
        if ($programa != "T") {
            $arrwhere1[] = " p.codigo IN ('$programa') ";
        }
        if ($cead != "T") {
            $arrwhere1[] = " c.codigo IN ('$cead') ";
        }
        if ($zona != "T") {
            $arrwhere1[] = " c.zona_zona_id IN (select zona_id from zona where nomenclatura in ('$zona')) ";
        }
        $where1 = count($arrwhere1) > 0 ? " AND " . implode(" AND ", $arrwhere1) : "";
        $sql = "SELECT DISTINCT 
                      g.graduado_id, g.documento,LOWER(g.nombre) AS nombre,LOWER(g.apellido) AS apellido, lower(g.email) as email, 
                      CASE WHEN g.fecha_modificacion IS not NULL THEN g.fecha_modificacion ELSE g.fecha_creacion END AS fecha_mod
                    FROM SIGRA.graduado g 
                      INNER JOIN SIGRA.titulo_graduado t ON t.graduado_id = g.graduado_id 
                WHERE t.estado = 2 $where1 
                    AND (g.documento LIKE '%$buscar%' OR g.nombre LIKE '%$buscar%' OR g.apellido LIKE '%$buscar%') ";

        $sql .= " ORDER BY g.apellido ASC "
                . "LIMIT $page_position, $item_per_page  ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function updateTitulo($titulo_id, $estado, $cead_id, $fecha_grado) {
        $sql = "UPDATE `SIGRA`.`titulo_graduado`
                SET 
                  `cead_id` = '$cead_id',
                  `fecha_grado` = '$fecha_grado',
                  `estado` = '$estado'
                WHERE `titulo_id` = '$titulo_id';";
        $resultado = $this->consulta2($sql);
        return $resultado;
//        return mysqli_affected_rows($this->getConexion2());
    }

    function gestParticipante($documento, $nombre, $tel, $cel, $mail, $estamento) {
        $sql = "INSERT INTO `SIGRA`.`participante_evento`
                (`documento`,`nombre`,`estamento`,`celular`,`telefono`,`email`,`fecha_inscripcion`)
                VALUES ('$documento',UPPER('$nombre'),'$estamento','$cel','$tel',UPPER('$mail'),CURRENT_TIMESTAMP) 
                ON DUPLICATE KEY UPDATE 
                      `nombre` = UPPER('$nombre'),
                      `estamento` = '$estamento',
                      `celular` = '$cel',
                      `telefono` = '$tel',
                      `email` = UPPER('$mail'),
                      `fecha_actualizacion` = CURRENT_TIMESTAMP;";
        $resultado = $this->consulta2($sql);
        return mysqli_insert_id($this->getConexion2());
    }

    function nuevaEnc($nombre, $usuario, $desc_enc) {
        $ver = "SELECT 'n' as `encuesta_id` FROM `SIGRA`.`encuesta` WHERE UPPER(nombre) = UPPER('$nombre');";
        $resultado = $this->consulta2($ver);
//        print_r($resultado);
        if (mysqli_num_rows($resultado) > 0) {
            return $resultado;
        } else {
            $sql = "INSERT INTO `SIGRA`.`encuesta`
                (`nombre`,`descripcion`,`fecha_creacion`,`usuario_id`,`estado_id`)
                VALUES (UPPER('$nombre'),UPPER('$desc_enc'),CURRENT_TIMESTAMP,'$usuario','1');";
            $resultado = $this->consulta2($sql);
            return $this->getEncuesta(mysqli_insert_id($this->getConexion2()));
        }
    }

    function getEncuestas($page_position, $item_per_page) {
        $limit = "";
        if ($page_position !== '') {
            $limit = " LIMIT $page_position, $item_per_page ";
        }
        $sql = "SELECT
                  `encuesta_id`,LOWER(`nombre`) AS nombre,`cant_preguntas`,LOWER(`descripcion`) AS descripcion,`link`, fecha_creacion  
                FROM `SIGRA`.`encuesta`
                WHERE estado_id = '1' 
                $limit;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function getEncuesta($encuesta_id) {
        $sql = "SELECT
                  `encuesta_id`,LOWER(`nombre`) AS nombre,`cant_preguntas`,LOWER(`descripcion`) AS descripcion,`link`, fecha_creacion  
                FROM `SIGRA`.`encuesta`
                WHERE encuesta_id = $encuesta_id";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function crearModulo($encuesta_id, $nombre, $descripcion, $orden) {
        $ver = "SELECT DISTINCT 'n' FROM modulo_encuesta WHERE (nombre = UPPER('$nombre') OR orden = $orden) AND encuesta_id = $encuesta_id;";
        $resultado = $this->consulta2($ver);
//        print_r($resultado);
        if (mysqli_num_rows($resultado) > 0) {
            return $nombre;
        } else {
            $sql = "INSERT INTO `SIGRA`.`modulo_encuesta`
                            (`encuesta_id`,`nombre`,`descripcion`,`orden`,`fecha_creacion`,`estado_id`)
                VALUES ('$encuesta_id',UPPER('$nombre'),UPPER('$descripcion'),'$orden',CURRENT_TIMESTAMP,'1');";
//        echo $sql;
            $resultado = $this->consulta2($sql);
            return $resultado ? '1' : '0';
        }
    }

    function updateModulo($modulo_id, $estado_id, $nombre, $descripcion, $orden) {
        $sql = "UPDATE `SIGRA`.`modulo_encuesta`
                SET 
                  `nombre` = UPPER('$nombre'),
                  `descripcion` = UPPER('$descripcion'),
                  `orden` = '$orden',
                  `fecha_edicion` = CURRENT_TIMESTAMP,
                  `estado_id` = '$estado_id'
                WHERE `modulo_encuesta_id` = '$modulo_id';";
//        echo $sql;
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function modulos($encuesta_id) {
        $sql = "SELECT
                  `modulo_encuesta_id`,`nombre`,`descripcion`,`orden`,estado_id  
                FROM `SIGRA`.`modulo_encuesta`
                WHERE encuesta_id = $encuesta_id
                ORDER BY orden ASC ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function getModulos($encuesta_id) {
        $sql = "SELECT
                  `modulo_encuesta_id`,`nombre`,`descripcion`,`orden`,estado_id  
                FROM `SIGRA`.`modulo_encuesta`
                WHERE encuesta_id = $encuesta_id and estado_id = 1 
                ORDER BY orden ASC ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function getModulo($modulo_id) {
        $sql = "SELECT
                  `modulo_encuesta_id`,`nombre`,`descripcion`,`orden`,estado_id  
                FROM `SIGRA`.`modulo_encuesta`
                WHERE modulo_encuesta_id = $modulo_id;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function crearPregunta($modulo_encuesta_id, $enunciado, $tipo_preg, $orden, $descripcion, $referencia, $url_imagen, $hipervinculo, $usuario_crea) {
        $sql = "INSERT INTO `SIGRA`.`pregunta_modulo`
                    (`modulo_encuesta_id`,`enunciado`,`tipo_preg`,`orden`,`descripcion`,`referencia`,`url_imagen`,`hipervinculo`,`estado_id`,`usuario_crea`,`fecha_crea`)
            VALUES ('$modulo_encuesta_id',UPPER('$enunciado'),'$tipo_preg','$orden',UPPER('$descripcion'),UPPER('$referencia'),'$url_imagen',UPPER('$hipervinculo'),'1','$usuario_crea', CURRENT_TIMESTAMP);";
//            echo $sql;
        $resultado = $this->consulta2($sql);
        $preg_id = mysqli_insert_id($this->getConexion2());
        $serv = $_SERVER['DOCUMENT_ROOT'] . '/sigra/';
        $temporal = $serv . "tmp/";
        $busc_arch = $url_imagen;
        $ruta = $serv . "encuestas/" . date("Y") . "/preguntas/";
        if (file_exists($temporal)) {
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, TRUE);
            }
        }
        $arch = "";
        if ($aux = opendir($temporal)) {
            while (($archivo = readdir($aux)) !== false) {
                if ($archivo != "." && $archivo != "..") {
                    if (stristr($archivo, $busc_arch) !== false) {
                        $newArch = explode(SEPARADOR, $archivo);
                        if (rename($temporal . $archivo, $ruta . $newArch[0] . SEPARADOR . $newArch[1] . SEPARADOR . $preg_id . SEPARADOR . $newArch[3])) {
                            $arch = $newArch[0] . SEPARADOR . $newArch[1] . SEPARADOR . $preg_id . SEPARADOR . $newArch[3];
                        }
                    }
                }
            }
        }
        closedir($aux);
        $update = "UPDATE `SIGRA`.`pregunta_modulo` SET url_imagen = '$arch' WHERE pregunta_id = '$preg_id';";
        $resultado = $this->consulta2($update);
        return $resultado;
    }

    function traerPreguntas($modulo_id) {
        $sql = "SELECT
                  `pregunta_id`,`modulo_encuesta_id`,`enunciado`,`tipo_preg`,`orden`,`descripcion`,`referencia`,`url_imagen`,`hipervinculo`, estado_id 
                FROM `SIGRA`.`pregunta_modulo` 
                WHERE modulo_encuesta_id = $modulo_id and estado_id = 1 
                ORDER BY orden ASC;";
//        echo $sql;
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function titulosPorVerificar($id) {
        $sql = "SELECT tg.titulo_id, 
                          lower(p.descripcion) as programa, p.codigo AS cod_prog, lower(p.escuela) as escuela, 
                          lower(c.descripcion) as cead, c.codigo AS cod_cead, tg.fecha_grado AS fecha_grado, lower(z.descripcion) AS zona, estado 
                        FROM SIGRA.titulo_graduado tg 
                          INNER JOIN SIGRA.programa p ON p.codigo = tg.programa_id 
                          INNER JOIN SIGRA.cead c ON c.codigo = tg.cead_id 
                          INNER JOIN SIGRA.zona z ON z.zona_id = c.zona_zona_id 
                        WHERE tg.graduado_id = $id and tg.estado = 2 
                        ORDER BY tg.fecha_grado DESC;";
        $resultado = $this->consulta2($sql);
//        echo "$sql";
        return $resultado;
    }

    function eventosDisp() {
        $sql = "SELECT 
                  e.`evento_id`,lower(e.`nombre`) as nombre,substring(e.`fecha_inicio`,1,10) as fecha_inicio,substring(e.`fecha_fin`,1,10) as fecha_fin,
                  lower(e.`lugar`) as lugar,e.`organizador_id`,lower(o.`nombre`) AS organizador,lower(e.`poblacion`) as poblacion, e.`cant_cupos`,
                  e.`url_banner`,e.`url_doc_soporte`,e.`estado_id`,e.`proyecto_id`, lower(p.`nombre`) AS proyecto,lower(e.`tipo_asistencia`) as asist,
                  e.`fecha_creacion`,e.`usuario_id_crea`  
                FROM
                  `SIGRA`.`evento`   e 
                  INNER JOIN SIGRA.`proyecto` p ON p.`proyecto_id` = e.`proyecto_id` 
                  INNER JOIN SIGRA.`organizador` o ON o.`organizador_id` = e.`organizador_id` 
                WHERE e.estado_id = 1 and e.inscritos<e.cant_cupos;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function token2() {
        $rand_pass = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(48, 57)) . chr(rand(48, 57)) . chr(rand(48, 57)) . chr(rand(48, 57)) . chr(rand(48, 57));
        return $rand_pass;
    }

    function validarToken() {
        $cod_inscripcion = $this->token2();
        $ver = "SELECT inscripcion_id from `SIGRA`.`inscripcion` WHERE cod_inscripcion = '$cod_inscripcion'";
        $r = $this->consulta2($ver);
        if (mysqli_num_rows($r) > 0) {
            $cod_inscripcion = $this->token2();
            $this->validarToken();
        } else {
            return $cod_inscripcion;
        }
    }

    function inscribir($participante_id, $evento_id, $cod_inscripcion) {
        $sql = " INSERT INTO `SIGRA`.`inscripcion`
                        (`participante_id`,`evento_id`,`fecha_inscripcion`,`cod_inscripcion`,`fecha_envio_mail`)
                    VALUES ('$participante_id','$evento_id',CURRENT_TIMESTAMP,'$cod_inscripcion', CURRENT_TIMESTAMP);";
        $resultado = $this->consulta2($sql);
        if ($resultado) {
            $upd = "UPDATE `SIGRA`.`evento` SET `inscritos` = inscritos + 1 WHERE `evento_id` = '$evento_id';";
            $resultado = $this->consulta2($upd);
        }
        return $resultado;
    }

    function buscarAsistente($valor) {
        $sql = "";
        if (is_numeric($valor)) {
            $sql = "SELECT DISTINCT pe.nombre AS participante, pe.estamento, lower(e.nombre) as evento, i.*
                    FROM SIGRA.inscripcion i 
                      INNER JOIN SIGRA.participante_evento pe ON pe.participante_id = i.participante_id 
                      INNER JOIN SIGRA.evento e ON e.evento_id = i.evento_id 
                    WHERE pe.documento = '123' AND CURRENT_TIMESTAMP BETWEEN e.fecha_inicio AND e.fecha_fin;";
        } else {
            $sql = "SELECT DISTINCT pe.nombre AS participante, pe.estamento, lower(e.nombre) as evento, i.*
                    FROM SIGRA.inscripcion i 
                      INNER JOIN SIGRA.participante_evento pe ON pe.participante_id = i.participante_id 
                      INNER JOIN SIGRA.evento e ON e.evento_id = i.evento_id 
                    WHERE i.cod_inscripcion = '$valor' AND CURRENT_TIMESTAMP BETWEEN e.fecha_inicio AND e.fecha_fin;";
        }
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function traerAsistencia($inscripcion_id) {
        $sql = "SELECT
                  `asistencia_id`,`inscripcion_id`,SUBSTRING(fecha_asistencia, 1,10) AS asistencia,`tp_conf`,`usuario_confirma`, u.nombre AS usuario 
                FROM `SIGRA`.`asistencia` a 
                  INNER JOIN SIGRA.usuario u ON u.usuario_id = a.usuario_confirma 
                WHERE inscripcion_id = $inscripcion_id AND SUBSTRING(fecha_asistencia, 1,10) = CURRENT_DATE;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function confirmarAsistencia($inscr_id, $tp_conf, $usuario) {
        $sql = "INSERT INTO `SIGRA`.`asistencia`
                    (`inscripcion_id`,`fecha_asistencia`,`tp_conf`,`usuario_confirma`)
                VALUES ('$inscr_id',CURRENT_TIMESTAMP,'$tp_conf','$usuario');";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function cantGraduadosNotif($buscar, $programa, $escuela, $cead, $zona) {
        $arrwhere2 = array();
        if ($escuela != "T") {
            $arrwhere2[] = " lower(tt.`ESCUELA`) IN ('$escuela') ";
        }
        if ($programa != "T") {
            $arrwhere2[] = " tt.`CODIGO_PROGRAMA` IN ('$programa') ";
        }
        if ($cead != "T") {
            $arrwhere2[] = " tt.`CODIGO_CENTRO` IN ('$cead') ";
        }
        if ($zona != "T") {
            $arrwhere2[] = " tt.`NOMENCLATURA_ZONA` IN ('$zona') ";
        }
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT COUNT(1) as cant
                FROM (
                    SELECT DISTINCT 
                      'x' AS grduado_id, tg.`DOCUMENTO` AS documento, LOWER(tg.`NOMBRES`) AS nombre, LOWER(tg.`APELLIDOS`) AS apellido, 'NO' AS fecha_mod    
                    FROM `SIGRA`.`tmp_graduados` tg 
                      INNER JOIN SIGRA.tmp_titulos tt ON tt.`DOCUMENTO` = tg.`DOCUMENTO` 
                    WHERE tg.estado = '1' AND UPPER(`EMAIL`) != 'SIN DATO' AND `EMAIL` IS NOT NULL $where2 )A
                WHERE (A.documento LIKE '%$buscar%' OR A.nombre LIKE '%$buscar%' OR A.apellido LIKE '%$buscar%')";

        $sql .= ";";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function listaGraduadosNotif($page_position, $item_per_page, $buscar, $programa, $escuela, $cead, $zona) {
        $arrwhere2 = array();
        if ($escuela != "T") {
            $arrwhere2[] = " tt.`ESCUELA` IN ('$escuela') ";
        }
        if ($programa != "T") {
            $arrwhere2[] = "tt.`CODIGO_PROGRAMA` IN ('$programa') ";
        }
        if ($cead != "T") {
            $arrwhere2[] = " tt.`CODIGO_CENTRO` IN ('$cead') ";
        }
        if ($zona != "T") {
            $arrwhere2[] = " tt.`NOMENCLATURA_ZONA` IN ('$zona') ";
        }
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT distinct A.* 
                FROM (
                    SELECT DISTINCT 
                      'x' AS grduado_id, tg.`DOCUMENTO` AS documento, LOWER(tg.`NOMBRES`) AS nombre, LOWER(tg.`APELLIDOS`) AS apellido,
                      lower(tg.`EMAIL`) AS email, 'NO' AS fecha_mod    
                    FROM `SIGRA`.`tmp_graduados` tg 
                      INNER JOIN SIGRA.tmp_titulos tt ON tt.`DOCUMENTO` = tg.`DOCUMENTO` 
                    WHERE tg.estado = '1' AND UPPER(`EMAIL`) != 'SIN DATO' AND `EMAIL` IS NOT NULL $where2 )A
                WHERE (A.documento LIKE '%$buscar%' OR A.nombre LIKE '%$buscar%' OR A.apellido LIKE '%$buscar%') 
                     ";

        $sql .= " ORDER BY A.apellido ASC "
                . "LIMIT $page_position, $item_per_page  ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function getPregunta($preg_id) {
        $sql = "SELECT 
                  `pregunta_id`,`modulo_encuesta_id`,`enunciado`,`tipo_preg`,`orden`,`descripcion`,`referencia`,`url_imagen`,`hipervinculo`,`estado_id` 
                FROM
                  `SIGRA`.`pregunta_modulo` 
                WHERE pregunta_id = $preg_id;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function updatePregunta($preg_id, $enunciado, $tipo_preg, $orden, $descripcion, $referencia, $url_imagen, $hipervinculo, $usuario_modifica, $estado_id) {
        $sql = "UPDATE 
                  `SIGRA`.`pregunta_modulo` 
                SET
                  `enunciado` = UPPER('$enunciado'),
                  `tipo_preg` = '$tipo_preg',
                  `orden` = '$orden',
                  `descripcion` = UPPER('$descripcion'),
                  `referencia` = UPPER('$referencia'),
                  `url_imagen` = '$url_imagen',
                  `hipervinculo` = UPPER('$hipervinculo'),
                  `estado_id` = '$estado_id',
                  `usuario_modifica` = '$usuario_modifica',
                  `fecha_mod` = CURRENT_TIMESTAMP 
                WHERE `pregunta_id` = '$preg_id' ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function preguntasXRespuesta($encuesta_id) {
        $sql = "SELECT pm.`pregunta_id`,  pm.`enunciado`, pm.descripcion, me.`nombre` AS modulo, pm.tipo_preg 
                FROM SIGRA.`pregunta_modulo` pm 
                  INNER JOIN SIGRA.`modulo_encuesta` me ON me.`modulo_encuesta_id` = pm.`modulo_encuesta_id` 
                  INNER JOIN SIGRA.`encuesta` e ON e.`encuesta_id` = me.`encuesta_id` 
                WHERE e.encuesta_id = $encuesta_id AND pm.estado_id = 1 AND pm.tipo_preg != 'ABIERTA';";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function crearRespuesta($enunciado, $pregunta_id, $valor, $url_imagen, $descripcion, $orden, $estado) {
        $sql = "INSERT INTO `SIGRA`.`respuesta_pregunta` 
                   (`enunciado`,`pregunta_id`,`valor`,`url_imagen`,  `descripcion`,`orden`, estado_id) 
                VALUES
                  (UPPER('$enunciado'),'$pregunta_id','$valor','$url_imagen',UPPER('$descripcion'),'$orden', '$estado');";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function updateRespuesta($respuesta_id, $enunciado, $pregunta_id, $valor, $url_imagen, $descripcion, $orden, $estado) {
        $sql = "UPDATE 
                  `SIGRA`.`respuesta_pregunta` 
                SET
                  `enunciado` = UPPER('$enunciado'),
                  `pregunta_id` = '$pregunta_id',
                  `valor` = '$valor',
                  `url_imagen` = '$url_imagen',
                  `descripcion` = UPPER('$descripcion'),
                  `orden` = '$orden',
                  `estado_id` = '$estado' 
                WHERE `respuesta_id` = '$respuesta_id' ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function eliminarRespuesta($respuesta_id) {
        $sql = "UPDATE 
                  `SIGRA`.`respuesta_pregunta` 
                SET
                  `estado_id` = '3' 
                WHERE `respuesta_id` IN ($respuesta_id) ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function traerRespuestas($preg_id) {
        $sql = "SELECT 
                  `respuesta_id`,`enunciado`,`pregunta_id`,`valor`,`url_imagen`,`descripcion`,`orden`,`estado_id` 
                FROM
                  `SIGRA`.`respuesta_pregunta` 
                WHERE pregunta_id = $preg_id and estado_id != 3 
                ORDER BY orden ASC;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function getRespuesta($resp_id) {
        $sql = "SELECT 
                  `respuesta_id`,`enunciado`,`pregunta_id`,`valor`,`url_imagen`,`descripcion`,`orden`,`estado_id` 
                FROM
                  `SIGRA`.`respuesta_pregunta` 
                WHERE respuesta_id = $resp_id  and estado_id != 3 ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function buscarParticipanteEncuesta($documento, $encuesta) {
        $sql = "SELECT `participante_id`,`documento`,`nombre`,`estamento`,`celular`,`telefono`,`email` 
                FROM SIGRA.`participante_evento` pe 
                  LEFT OUTER JOIN SIGRA.`encuesta_participante` ep ON ep.`participante_id` = pe.`participante_id` 
                WHERE pe.`documento` = '$documento' AND ep.`encuesta_id` = '$encuesta';";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function filtros($cual) {
        $sql = "";
        switch ($cual) {
            case "proyecto":
                $sql = "SELECT proyecto_id, nombre FROM SIGRA.proyecto WHERE estado_id = 1;";
                break;
            case "evento":
                $sql = "SELECT evento_id, nombre FROM SIGRA.evento WHERE estado_id = 1;";
                break;
            case "linea":
                $sql = "SELECT linea_id, descripcion FROM SIGRA.linea WHERE estado_id = 1;";
                break;
            case "cobertura":
                $sql = "SELECT cobertura_id, descripcion FROM SIGRA.cobertura WHERE estado_id = 1;";
                break;
        }
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function reporteAsistentes($page_position, $item_per_page, $buscar, $evento, $proyecto, $linea, $cobertura) {
        $limit = "";
        if ($page_position !== '') {
            $limit = " LIMIT $page_position, $item_per_page ";
        }
        $arrwhere2 = array();
        if ($proyecto != "T") {
            $arrwhere2[] = " p.proyecto_id IN ('$proyecto') ";
        }
        if ($evento != "T") {
            $arrwhere2[] = " e.evento_id IN ('$evento') ";
        }
        if ($linea != "T") {
            $arrwhere2[] = " p.linea_id IN ('$linea') ";
        }
        if ($cobertura != "T") {
            $arrwhere2[] = " p.cobertura_id IN ('$cobertura') ";
        }
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT pe.`documento`, pe.`nombre` AS asistente, p.`nombre` AS proyecto, e.`nombre` AS evento, i.`fecha_inscripcion`, a.fecha_asistencia 
                FROM SIGRA.`asistencia` a 
                  INNER JOIN SIGRA.`inscripcion` i ON i.`inscripcion_id` = a.`inscripcion_id` 
                  INNER JOIN SIGRA.`participante_evento` pe ON pe.`participante_id` = i.`participante_id` 
                  INNER JOIN SIGRA.`evento` e ON e.`evento_id` = i.`evento_id` 
                  INNER JOIN SIGRA.`proyecto` p ON p.`proyecto_id` = e.`proyecto_id` 
                WHERE (pe.nombre like '%$buscar%' or pe.documento like '%$buscar%') $where2 
                ORDER BY e.`nombre`, p.`nombre`, pe.`nombre` ASC 
                $limit ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function cantReporteAsistentes($buscar, $evento, $proyecto, $linea, $cobertura) {
        $arrwhere2 = array();
        if ($proyecto != "T") {
            $arrwhere2[] = " p.proyecto_id IN ('$proyecto') ";
        }
        if ($evento != "T") {
            $arrwhere2[] = " e.evento_id IN ('$evento') ";
        }
        if ($linea != "T") {
            $arrwhere2[] = " p.linea_id IN ('$linea') ";
        }
        if ($cobertura != "T") {
            $arrwhere2[] = " p.cobertura_id IN ('$cobertura') ";
        }
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT count(1) as cant
                FROM SIGRA.`asistencia` a 
                  INNER JOIN SIGRA.`inscripcion` i ON i.`inscripcion_id` = a.`inscripcion_id` 
                  INNER JOIN SIGRA.`participante_evento` pe ON pe.`participante_id` = i.`participante_id` 
                  INNER JOIN SIGRA.`evento` e ON e.`evento_id` = i.`evento_id` 
                  INNER JOIN SIGRA.`proyecto` p ON p.`proyecto_id` = e.`proyecto_id` 
                WHERE (pe.nombre like '%$buscar%' or pe.documento like '%$buscar%') $where2;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function reporteRegistrados($page_position, $item_per_page, $buscar, $evento, $proyecto, $linea, $cobertura) {
        $limit = "";
        if ($page_position !== '') {
            $limit = " LIMIT $page_position, $item_per_page ";
        }
        $arrwhere2 = array();
        if ($proyecto != "T") {
            $arrwhere2[] = " p.proyecto_id IN ('$proyecto') ";
        }
        if ($evento != "T") {
            $arrwhere2[] = " e.evento_id IN ('$evento') ";
        }
        if ($linea != "T") {
            $arrwhere2[] = " p.linea_id IN ('$linea') ";
        }
        if ($cobertura != "T") {
            $arrwhere2[] = " p.cobertura_id IN ('$cobertura') ";
        }
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT pe.`documento`, pe.`nombre` AS asistente, p.`nombre` AS proyecto, e.`nombre` AS evento, i.`fecha_inscripcion`, a.fecha_asistencia 
                FROM SIGRA.`inscripcion` i 
                  LEFT OUTER JOIN SIGRA.`asistencia` a ON i.`inscripcion_id` = a.`inscripcion_id` 
                  INNER JOIN SIGRA.`participante_evento` pe ON pe.`participante_id` = i.`participante_id` 
                  INNER JOIN SIGRA.`evento` e ON e.`evento_id` = i.`evento_id` 
                  INNER JOIN SIGRA.`proyecto` p ON p.`proyecto_id` = e.`proyecto_id` 
                WHERE (pe.nombre like '%$buscar%' or pe.documento like '%$buscar%') $where2 
                ORDER BY e.`evento_id`,i.`fecha_inscripcion` ASC 
                $limit ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    function cantReporteRegistrados($buscar, $evento, $proyecto, $linea, $cobertura) {
        $arrwhere2 = array();
        if ($proyecto != "T") {
            $arrwhere2[] = " p.proyecto_id IN ('$proyecto') ";
        }
        if ($evento != "T") {
            $arrwhere2[] = " e.evento_id IN ('$evento') ";
        }
        if ($linea != "T") {
            $arrwhere2[] = " p.linea_id IN ('$linea') ";
        }
        if ($cobertura != "T") {
            $arrwhere2[] = " p.cobertura_id IN ('$cobertura') ";
        }
        $where2 = count($arrwhere2) > 0 ? " AND " . implode(" AND ", $arrwhere2) : "";
        $sql = "SELECT COUNT(1) AS cant
                FROM SIGRA.`inscripcion` i 
                  LEFT OUTER JOIN SIGRA.`asistencia` a ON i.`inscripcion_id` = a.`inscripcion_id` 
                  INNER JOIN SIGRA.`participante_evento` pe ON pe.`participante_id` = i.`participante_id` 
                  INNER JOIN SIGRA.`evento` e ON e.`evento_id` = i.`evento_id` 
                  INNER JOIN SIGRA.`proyecto` p ON p.`proyecto_id` = e.`proyecto_id` 
                WHERE (pe.nombre like '%$buscar%' or pe.documento like '%$buscar%') $where2 ;";
        $resultado = $this->consulta2($sql);
        return $resultado;
    }

    // FIN - METODOS SIGRA
    //INICIO DASHBOARDS
    //
    //DASH EGRESADOS - INICIO
    //conteos

    function conteoTitulos() {
        $sql = "SELECT COUNT(`DOCUMENTO`) AS conteo_titulos FROM SIGRA.`tmp_titulos`; ";
        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo_titulos'];
        }
        return $res;
    }

    function conteoPersonas() {
        $sql = "SELECT COUNT(`DOCUMENTO`) AS conteo_personas FROM SIGRA.`tmp_graduados`; ";
        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo_personas'];
        }

        return $res;
    }

    function conteoTelefonos() {
        $sql = "SELECT COUNT(DOCUMENTO) AS conteo_tel FROM SIGRA.`tmp_graduados` WHERE `TELEFONO`<>0; ";
        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo_tel'];
        }

        return $res;
    }

    function conteoDireccion() {
        $sql = "SELECT COUNT(DOCUMENTO) AS conteo_dir FROM SIGRA.`tmp_graduados` WHERE `DIRECCION`<>'SIN DATO'; ";
        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo_dir'];
        }

        return $res;
    }

    function conteoEmail() {
        $sql = "SELECT COUNT(DOCUMENTO) AS conteo_email FROM SIGRA.`tmp_graduados` WHERE `EMAIL`<>'SIN DATO'; ";
        $resultado = mysql_query($sql);
        $res = 0;
        while ($fila = mysql_fetch_assoc($resultado)) {
            $res = $fila['conteo_email'];
        }

        return $res;
    }

    //datas

    function RelacionPrograma() {
        $sql = "SELECT COUNT(DOCUMENTO) AS numerico, `RELACION_PROGRAMA_TRABAJO` AS label FROM SIGRA.`tmp_graduados` GROUP BY `RELACION_PROGRAMA_TRABAJO` ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function CargoEgresados() {
        $sql = "SELECT COUNT(DOCUMENTO) AS numerico, `CARGO` AS label FROM SIGRA.`tmp_graduados` GROUP BY `CARGO` ORDER BY numerico DESC LIMIT 10 ; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function SituacionLaboral() {
        $sql = "SELECT COUNT(DOCUMENTO) AS numerico, `SITUACION_LABORAL` AS label FROM SIGRA.`tmp_graduados` GROUP BY `SITUACION_LABORAL` ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function Genero() {
        $sql = "SELECT COUNT(DOCUMENTO) AS numerico, `GENERO` AS label FROM SIGRA.`tmp_graduados` GROUP BY `GENERO` ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function Estrato() {
        $sql = "SELECT COUNT(DOCUMENTO) AS numerico, CONCAT('Estrato: ',`ESTRATO`) AS label FROM SIGRA.`tmp_graduados` GROUP BY `ESTRATO` ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function AreaGeografica() {
        $sql = "SELECT COUNT(DOCUMENTO) AS numerico, `AREA_GEOFRAFICA` AS label FROM SIGRA.`tmp_graduados` GROUP BY `AREA_GEOFRAFICA` ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function Vigencia() {
        $sql = "SELECT COUNT(DOCUMENTO) AS numerico, `ESTADO_IDENTIFICACION` AS label FROM SIGRA.`tmp_graduados` GROUP BY `ESTADO_IDENTIFICACION` ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function EgresadosXPrograma() {
        $sql = "SELECT COUNT(*) AS numerico, `NOMBRE_PROGRAMA` AS label
                FROM SIGRA.`tmp_titulos` 
                GROUP BY `NOMBRE_PROGRAMA`
                ORDER BY numerico DESC
                LIMIT 10; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function EgresadosXEscuela() {
        $sql = "SELECT COUNT(*) AS numerico, REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`ESCUELA`,'ESCUELA DE CIENCIAS ADMINISTRATIVAS, CONTABLES, ECONÓMICAS Y DE NEGOCIOS','ECACEN')
                    ,'ESCUELA DE CIENCIAS SOCIALES, ARTES Y HUMANIDADES','ECSAH')
                    ,'ESCUELA DE CIENCIAS BÁSICAS, TECNOLOGÍA E INGENIERÍA','ECBTI')
                    ,'ESCUELA DE CIENCIAS DE LA EDUCACIÓN','ECEDU')
                    ,'ESCUELA DE CIENCIAS DE LA SALUD','ECCISA')
                    ,'ESCUELA DE CIENCIAS AGRÍCOLAS, PECUARIAS Y DEL MEDIO AMBIENTE','ECAPMA')
                     AS label
                                    FROM SIGRA.`tmp_titulos` 
                                    GROUP BY `ESCUELA`
                                    ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function EgresadosXCentro() {
        $sql = "SELECT COUNT(*) AS numerico, `CENTRO` AS label
                FROM SIGRA.`tmp_titulos` 
                GROUP BY `CENTRO`
                ORDER BY numerico DESC
                LIMIT 15; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function EgresadosXZona() {
        $sql = "SELECT COUNT(*) AS numerico, REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (REPLACE (`ZONA`,'CENTRO BOGOTA Y CUNDINAMARCA', 'ZCBC')
					,'CARIBE', 'ZCAR')
					,'CENTRO SUR', 'ZCSUR') 
					,'SUR', 'ZSUR') 
					,'OCCIDENTE', 'ZOCC') 
					,'CENTRO BOYACA', 'ZCBOY') 
					,'CENTRO ORIENTE', 'ZCORI') 
					,'AMAZONAS Y ORINOQUIA', 'ZAO')  AS label
                FROM SIGRA.`tmp_titulos` 
                GROUP BY `ZONA`
                ORDER BY numerico DESC; ";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function EgresadosXNivelAcademico() {
        $sql = "SELECT COUNT(*) AS numerico, `NIVEL_ACADEMICO` as label
                FROM SIGRA.`tmp_titulos` 
                GROUP BY `NIVEL_ACADEMICO`
                ORDER BY numerico DESC;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    function EgresadosXNivelFormacion() {
        $sql = "SELECT COUNT(*) AS numerico, `NIVEL_DE_FORMACION` as label
                FROM SIGRA.`tmp_titulos` 
                GROUP BY `NIVEL_DE_FORMACION`
                ORDER BY numerico DESC;";
        $resultado = mysql_query($sql);
        return $resultado;
    }

    //DASH EGRESADOS - FIN

    function estructurarDataGrafico($data, $tipoColor) {
        $dataSet_d = "";
        $dataSet_l = "";
        $dataSet_c = "";
        $cont = 0;
        while ($fila = mysql_fetch_assoc($data)) {
            // Sacar cantidad

            if ($cont > 0) {
                $dataSet_d.=",";
                $dataSet_l.=",";
            }
            $dataSet_l.="'" . $fila['label'] . " - " . number_format($fila['numerico'], 0) . "'";
            // Sacar labels
            $dataSet_d.= $fila['numerico'];
            $cont++;
        }

        $dataSet_c.= $this->GenerarSetColor($cont, $tipoColor);
        //echo $dataSet_d;
        return $dataSet_d . "|" . $dataSet_l . "|" . $dataSet_c;
    }

    function GenerarSetColor($cantidad, $tipoColor) {
        $cont = 0;
        $setColor = "";
        if ($tipoColor === 1) {
            switch ($cantidad) {
                case 1:
                    $setColor = " backgroundColor: [
                                'rgba(255, 99, 132, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)'
                               
                            ],";
                    break;
                case 2:
                    $setColor = " backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)'
                               
                            ],";
                    break;
                case 3:
                    $setColor = " backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],";
                    break;
                case 4:
                    $setColor = " backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)'
                            ],";
                    break;
                case 5:
                    $setColor = " backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],";
                    break;
                case 6:
                    $setColor = " backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],";
                    break;
            }
        } else if ($tipoColor === 2) {
            $color = rand(1, 5);
            switch ($color) {
                case 1:
                    $setColor = " backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                            borderColor: window.chartColors.red, ";
                    break;
                case 2:
                    $setColor = " backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                            borderColor: window.chartColors.blue, ";
                    break;
                case 3:
                    $setColor = " backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
                            borderColor: window.chartColors.green, ";
                    break;
                case 4:
                    $setColor = " backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
                            borderColor: window.chartColors.orange, ";
                    break;
                case 5:
                    $setColor = " backgroundColor: color(window.chartColors.yellow).alpha(0.5).rgbString(),
                            borderColor: window.chartColors.yellow, ";
                    break;
            }
        } else {
            switch ($cantidad) {
                case 1:
                    $setColor = "backgroundColor: [
                                    window.chartColors.red,
                                ],";
                    break;
                case 2:
                    $setColor = "backgroundColor: [
                                    window.chartColors.blue,
                                    window.chartColors.green,
                                ],";
                    break;
                case 3:
                    $setColor = "backgroundColor: [
                                    window.chartColors.yellow,
                                    window.chartColors.orange,
                                    window.chartColors.blue,
                                ],";
                    break;
                case 4:
                    $setColor = "backgroundColor: [
                                    window.chartColors.green,
                                    window.chartColors.yellow,
                                    window.chartColors.red,
                                    window.chartColors.orange,
                                ],";
                    break;
                case 5:
                    $setColor = "backgroundColor: [
                                    window.chartColors.red,
                                    window.chartColors.orange,
                                    window.chartColors.yellow,
                                    window.chartColors.green,
                                    window.chartColors.blue,
                                ],";
                    break;
            }
        }

        return $setColor;
    }

    function DashCrearBarMultiColor($dataname, $data, $title) {
        //Se recorre data
        $estructura = explode("|", $data);
        $graph = " 
                <script>
                var $dataname = {
                    labels: [$estructura[1]],
                    datasets: [
                        {
                            label: '$title',
                           $estructura[2]
                            borderWidth: 1,
                            data: [$estructura[0]],
                        }
                    ]
                };
                </script>
             ";

        return $graph;
    }

    function DashCrearBar($dataname, $data, $title) {
        $estructura = explode("|", $data);
        $graph = " <script>
                var color = Chart.helpers.color;
                var $dataname = {
                    labels: [$estructura[1]],
                    datasets: [{
                            label: '$title',
                                $estructura[2]
                            borderWidth: 1,
                            data: [
                                $estructura[0]
                            ]
                        }]

                };
            </script> ";
        return $graph;
    }

    function DashCrearPie($dataname, $data, $title) {
        $estructura = explode("|", $data);
        $graph = " <script>
                var $dataname = {
                    type: 'pie',
                    data: {
                        datasets: [{
                                data: [
                                     $estructura[0]
                                ],
                                $estructura[2]
                                label: '$title'
                            }],
                        labels: [
                            $estructura[1]
                        ]
                    },
                    options: {
                        responsive: true,
                        legend: {                                                                              
                        position: 'right'                                                                     
                    }  
                    }
                };
            </script> ";
        return $graph;
    }

    function DashCrearDonut($dataname, $data, $title) {
        $estructura = explode("|", $data);
        $graph = " <script>
                var $dataname = {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                                data: [
                                   $estructura[0]
                                ],
                                 $estructura[2]
                                label: '$title'
                            }],
                        labels: [
                           $estructura[1]
                        ]
                    },
                    options: {
                        responsive: true,
                        legend: {                                                                              
                        position: 'right'                                                                     
                    }  
                    }
                };
            </script> ";
        return $graph;
    }

    function CrearOnload($nombre, $tipo, $data, $type) {

        switch ($tipo) {
            case 1: //bar
                $etiqueta = " var ctx = document.getElementById('$nombre').getContext('2d');
                                window.myBar = new Chart(ctx, {
                                    type: '$type',
                                    data: $data,
                                    options: {
                                        responsive: true,
                                        legend: {
                                            position: 'top',
                                        },
                                        title: {
                                            display: true
                                        }
                                    }
                                }); ";
                break;
            case 2: //pie
                $etiqueta = " var ctx = document.getElementById('$nombre').getContext('2d');
                           window.myPie = new Chart(ctx, $data); ";
                break;
            case 3: //donut
                $etiqueta = " var ctx = document.getElementById('$nombre').getContext('2d');
                           window.myDoughnut = new Chart(ctx, $data); ";
                break;
        }


        return $etiqueta;
    }

    //FIN DASHBOARDS
}

//fin clase
?>
