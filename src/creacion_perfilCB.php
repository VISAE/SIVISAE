<?php
session_start();
include '../config/sivisae_class.php';
$consulta = new sivisae_consultas();
$permisos;
$usr_id = $_SESSION['usuarioid'];

if (isset($_POST['accion']) && $_POST['accion'] == 'n') {
    $nombre_perfil = strtoupper($_POST['nombre_perfil']);
//Se valida si el check opcion tiene items seleccionados
    $checked_count = 0;
    if (isset($_POST['opcion'])) {
        $optionArray = $_POST['opcion'];
        for ($i = 0; $i < count($optionArray); $i++) {
            $checked_count++;
        }
    }

    if ($checked_count == 0) {
        echo " Debe seleccionar al menos una opción de perfil";
    } else {
        $banPermisos = 0;
        //Se valida que las opciones seleccionadas cada una tenga permisos
        foreach ($_POST['opcion'] as $selec) {
            $checked_count_permisos = 0;
            if (isset($_POST['perm' . $selec])) {
                $optionArrayPermisos = $_POST['perm' . $selec];
                for ($i = 0; $i < count($optionArrayPermisos); $i++) {
                    $checked_count_permisos++;
                }
            }
            if ($checked_count_permisos == 0) {
                $banPermisos = 1;
            }
        }

        //Se procesa a realizar la transaccion según el resultado de las validaciones
        if ($banPermisos == 1) {
            echo " Debe seleccionar al menos un permiso por cada opción de perfil";
        } else {
            $perfilid = $consulta->crearPerfil($nombre_perfil);
            foreach ($_POST['opcion'] as $selected) {
                $pr = array();
                $p = $_POST['perm' . $selected];
                if (in_array("1", $p)) {
                    $pr[] = "1";
                    if (in_array("2", $p)) {
                        $pr[] = "1";
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    } else {
                        $pr[] = '0';
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    }
                } else {
                    $pr[] = '0';
                    if (in_array("2", $p)) {
                        $pr[] = "1";
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    } else {
                        $pr[] = '0';
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    }
                }
                $permisos = implode(", ", $pr);

                $res = $consulta->crearPerfilOpcion($perfilid, $selected, $permisos, $_POST['filtro_zona'.$selected], $_POST['filtro_escuela'.$selected]);
                if ($res == 1) {
                    $consulta->registrarAccion($usr_id, "CREAR PERFIL", "EXISTOSO");
                    echo "Perfil creado con éxito";
                } else {
                    echo "No se pudo crear el perfil";
                }
            }
        }
    }
}

// Actualizar perfil
if (isset($_POST['accion_e']) && $_POST['accion_e'] == 'e') {
    $nombre_perfil = strtoupper($_POST['nombre_perfil_e']);
    $id_perfil = $_POST['perfil_id'];
    //Se valida si el check opcion tiene items seleccionados
    $checked_count = 0;
    if (isset($_POST['opcion_e'])) {
        $optionArray = $_POST['opcion_e'];
        for ($i = 0; $i < count($optionArray); $i++) {
            $checked_count++;
        }
    }

    if ($checked_count == 0) {
        echo " Debe seleccionar al menos una opción de perfil";
    } else {
        $banPermisos = 0;
        //Se valida que las opciones seleccionadas cada una tenga permisos
        $opc_compare = array();
//        $opc_compare = array('1','2','3','5','6','8','9');
        $val = $consulta->existePerfilOpcion($id_perfil);
        while ($row = mysql_fetch_array($val)) {
            $opc_compare[] = $row[1];
        }
        $delete = array_diff($opc_compare, $_POST['opcion_e']);
        $insert = array_diff($_POST['opcion_e'], $opc_compare);
        $update = array_intersect($opc_compare, $_POST['opcion_e']);
//        echo "delete: " . implode(", ", $delete) . " - insert: " . implode(", ", $insert) . " - update: " . implode(", ", $update);
        if (count($delete) > 0) {
            $consulta->updatePerfilOpcion($id_perfil, implode(", ", $delete), "", "d");
        }
        foreach ($_POST['opcion_e'] as $selected) {
            $checked_count_permisos = 0;
            if (isset($_POST['perm_e' . $selected])) {
                $optionArrayPermisos = $_POST['perm_e' . $selected];
                for ($i = 0; $i < count($optionArrayPermisos); $i++) {
                    $checked_count_permisos++;
                }
            }
            if ($checked_count_permisos == 0) {
                $banPermisos = 1;
            }
        }

        //Se procesa a realizar la transaccion según el resultado de las validaciones
        if ($banPermisos == 1) {
            echo " Debe seleccionar al menos un permiso por cada opción de perfil";
        } else {
            $cont = 0;
            $consulta->updateNombrePerfil($id_perfil, $nombre_perfil);
            foreach ($_POST['opcion_e'] as $selected) {
                $pr = array();
                $p = $_POST['perm_e' . $selected];
                if (in_array("1", $p)) {
                    $pr[] = "1";
                    if (in_array("2", $p)) {
                        $pr[] = "1";
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    } else {
                        $pr[] = '0';
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    }
                } else {
                    $pr[] = '0';
                    if (in_array("2", $p)) {
                        $pr[] = "1";
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    } else {
                        $pr[] = '0';
                        if (in_array("3", $p)) {
                            $pr[] = "1";
                        } else {
                            $pr[] = '0';
                        }
                    }
                }
                $permisos = implode(", ", $pr);

                
                if (in_array($selected, $insert)) {
                    $cont = $consulta->updatePerfilOpcion($id_perfil, $selected, $permisos, "i", $_POST['filtro_zona'.$selected], $_POST['filtro_escuela'.$selected]);
                }
                if (in_array($selected, $update)) {
                    $cont = $consulta->updatePerfilOpcion($id_perfil, $selected, $permisos, "u", $_POST['filtro_zona'.$selected], $_POST['filtro_escuela'.$selected]);
                }
                
            }
            if ($cont==1) {
                $consulta->registrarAccion($usr_id, "EDITAR PERFIL", "EXISTOSO");
                echo "Perfil Actualizado con éxito";
            }
        }
    }
}
$consulta->destruir();
