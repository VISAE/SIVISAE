<?php

/*
  scripts para actualizar los usuarios registrados en los nodos
 * //Autor: Ing. Andres Camilo Mendez Aguirre
  //Fecha: 21/02/2017
 */
session_start();
include "../config/nodos_consultas.php";

$nodosTX = new nodos_consultas();
// Se actualizan documentos de nuevos usuarios en los nodos
$resUpd = $nodosTX->actualizarUsuariosNodos();
$nodosTX->destruir();
echo 'Se ha realizado la operación'; 
