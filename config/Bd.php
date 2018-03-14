<?php

//Clase transaccional a la BD
//Autor: Ing. Andres Camilo Mendez Aguirre
//Fecha: 25/03/2015

class Bd {

    private $usuario;
    private $clave;
    private $bd;
    private $conexion;
    private $conexion2;

    function __construct() {
        $array = $this->getCredenciales();
        $this->usuario = $array[1];
        $this->clave = $array[3];
        $this->bd = $array[2];
        $this->conectarBD($array[0]);
        $this->conectarBD2($array[0]);
    }

    public function getCredenciales() {
        $archivo = fopen($_SERVER['DOCUMENT_ROOT'] . "/sivisae/config/property.property", "r");
        while (!feof($archivo)) {
            $cadena = fgets($archivo);
        }
        fclose($archivo);
        $cadena2 = $this->getTexto($cadena);
        $array = explode("|", $cadena2);
        for ($i = 0; $i < (count($array) - 1); $i++) {
            $array[$i] = $this->decrypt($array[$i], $this->getKey());
        }
        $array[0] = 'localhost';
        $array[3] = 'Febrero2018*';
        return $array;
    }

    private function getBinario($str) {
        $str_arr = str_split($str, 4);

        for ($i = 0; $i < count($str_arr); $i++)
            $bin = $bin . str_pad(decbin(hexdec(bin2hex($str_arr[$i]))), strlen($str_arr[$i]) * 8, "0", STR_PAD_LEFT);

        return $bin;
    }

    private function getTexto($bin_str) {
        $text_str = "";
        $chars = explode("\n", chunk_split(str_replace("\n", "", $bin_str), 8));
        $_l = count($chars);
        for ($i = 0; $i < $_l; $text_str .= chr(bindec($chars[$i])), $i++)
            ;
        return $text_str;
    }

    private function setKey() {
        $archivo = fopen("key3.property", "r");
        while (!feof($archivo)) {
            $cadena = fgets($archivo);
        }
        fclose($archivo);
        $longitud = strlen($cadena);
        $canCaracteres = $longitud * pow(10, $longitud);
        $cadenaEscribir = $this->setCadena($canCaracteres);
        $salto = pow($longitud, $longitud);
        $j = 1;
        while ($j <= $longitud) {
            $cadenaEscribir[($salto * $j)] = $cadena[$j - 1];
            $j++;
        }
        $archivo = fopen("key2.property", "a");
        fwrite($archivo, $cadenaEscribir);
        fclose($archivo);
        $archivo = fopen("key.property", "a");
        fwrite($archivo, $this->getBinario($cadenaEscribir));
        fclose($archivo);
    }

    public function getKey() {
        $archivo = fopen($_SERVER['DOCUMENT_ROOT'] . "/sivisae/config/key.property", "r");
        while (!feof($archivo)) {
            $cadenaBin = fgets($archivo);
        }
        fclose($archivo);
        $cadenaEn = $this->getTexto($cadenaBin);
        $longitud = strlen($cadenaEn);
        while ($longitud > 10) {
            $longitud = (int) $longitud / 10;
        }
        $salto = pow($longitud, $longitud);
        $j = 1;
        $llave = "";
        while ($j <= $longitud) {
            $llave.=$cadenaEn[$salto * $j];
            $j++;
        }
        return $llave;
    }

    private function setCadena($longitud) {
        $clave = "";
        for ($i = 0; $i < $longitud; $i++) {
            $eleccion = floor(rand(0, 2));
            switch ($eleccion) {
                case 0:
                    $clave = $clave . $this->Mayusculas();
                    break;
                case 1:
                    $clave = $clave . $this->Minusculas();
                    break;
                case 2:
                    $clave = $clave . $this->Numeros();
                    break;
            }
        }
        return $clave;
    }

    private function Numeros() {
        $num = floor(rand(0, 9));
        return $num;
    }

    private function Mayusculas() {
        $clave = floor(rand(65, 91));
        return chr($clave);
    }

    private function Minusculas() {
        $clave = floor(rand(97, 123));
        return chr($clave);
    }

    public function setCredenciales() {
        $archivo = fopen("property2.property", "r");
        while (!feof($archivo)) {
            $cadena = fgets($archivo);
        }
        fclose($archivo);
        $array = explode("|", $cadena);
        $archivo = fopen("property.property", "a");
        $cadena = "";
        for ($i = 0; $i < count($array) - 1; $i++) {
            $cadena.=$this->encrypt($array[$i], $this->getKey()) . "|";
        }
        echo $cadena;
        fwrite($archivo, $this->getBinario($cadena));
        fclose($archivo);
    }

    private function conectarBD($servidor) {
        if (!$this->conexion = mysqli_connect($servidor, $this->usuario, $this->clave, $this->bd)) {
            echo "Error al conectar con el servidor";
            echo mysqli_error($this->conexion);
        } else {
            mysqli_set_charset($this->conexion, 'utf8');
            if (!mysqli_select_db($this->conexion, $this->bd)) {
                echo "Error conectando con la base de datos";
            }
        }
    }
    
    private function conectarBD2($servidor) {
        if (!$this->conexion2 = mysqli_connect($servidor, $this->usuario, $this->clave, $this->bd)) {
            echo "Error al conectar con el servidor";
            echo mysqli_error($this->conexion);
        } else {
            mysqli_set_charset($this->conexion, 'utf8');
            if (!mysqli_select_db($this->conexion, $this->bd)) {
                echo "Error conectando con la base de datos";
            }
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
    
    public function getConexion2() {
        return $this->conexion2;
    }

    public function destruir() {
        mysqli_close($this->getConexion());
    }
    
    public function destruir2() {
        mysqli_close($this->getConexion2());
    }

    public function encrypt($string, $key) {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        }
        $result = base64_encode($result);
        $result = str_replace(chr(43), chr(33), $result);
        return $result;
    }

    public function decrypt($string, $key) {
        $string = str_replace(chr(33), chr(43), $string);
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result.=$char;
        }
        return $result;
    }

    /*     * **
     * Inicio Metodos Transaccionales
     * *** */

    public function consulta($sql) {
        $resultado = mysql_query($sql, $this->getConexion());
        return $resultado;
    }

    public function insertar($tabla, $campos, $valores) {
        $sql = "INSERT INTO " . $tabla . " (" . $campos . ") VALUES (" . $valores . ");";
        $resultado = $this->consulta($sql);
        //echo mysqli_error($this->getConexion());
        //echo $this->getConexion()->error;
        //return mysqli_affected_rows($this->getConexion());
        return mysql_affected_rows($this->getConexion());
        //return $this->getConexion()->affected_rows;
    }
    
    public function consulta2($sql) {
        $resultado = mysqli_query($this->conexion2, $sql);
        return $resultado;
    }

    /*     * **
     * Fin Metodos Transaccionales
     * *** */
}

?>