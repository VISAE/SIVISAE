<?php
//Clase interfaz de los metodos
//Autor: Ing. Andres Camilo Mendez Aguirre
//Fecha: 25/03/2015


//define('RUTA_PPAL', 'https://sivisae.unad.edu.co/sivisae/');
//define('RUTA_PPAL', 'http://192.168.4.25/sivisae/');
define('RUTA_PPAL', 'http://localhost/sivisae/');
define('URL_PAGES', RUTA_PPAL.'pages/');
define('SEPARADOR', '|');
define('FECHA_SEG_ITERACIONES', '2015-10-24');
define('SIN_TILDES', '/fotografia/,/informatic/,/estadistica/,/quimica/,/problematic/,/analitic/,/\bproxim/,/\bautomatic/,/\bsegun\b/,/sincronico\b/,/\bacademico\b/,/\bacademica\b/,/ria\b/,/\bguia\b/,/\bcaracter\b/,/\bpedagogica\b/,/tion\b/,/cion\b/,/ingenieria/,/\bmetodo\b/,/\bmetodos\b/,/sintesis/,/economia/,/calculo/,/tecnologia/,/logia\b/,/regimen/,/ergonomia/,/razon/,/academico/,/autonomo/,/dinamica/,/exito/,/politic/,/catedra/,/matematic/,/logic/,/rectoria/,/dia\b/,/ã­/,/ã©/,/ã‘/,/ã’/,/ã±/,/ã¡/');
define('TILDES', 'fotografía,informátic,estadística,química,problemátic,analític,próxim,automátic,según,sincrónico,académico,académica,ría,guía,carácter,pedagógica,tión,ción,ingeniería,método,métodos,síntesis,economía,cálculo,tecnología,logía,régimen,ergonomía,razón,académico,autónomo,dinámica,éxito,polític,cátedra,matemátic,lógic,rectoría,día,í,é,ñ,ó,ñ,á');
    include_once("Bd.php");
    include_once('sivisae_consultas.php');
?>