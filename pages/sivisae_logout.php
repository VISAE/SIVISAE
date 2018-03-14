<?php
session_start();
session_destroy();
include_once '../config/sivisae_class.php';
header('Location: '.RUTA_PPAL);
exit(0);
?>
