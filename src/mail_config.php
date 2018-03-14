<?php

include("./correo/class.phpmailer.php");
include("./correo/class.smtp.php");
//include_once("../config/sivisae_class.php");

class mail_config extends PHPMailer {

    private $mail;

    function __construct() {
        $this->mail = new PHPMailer();
        $this->mail->IsSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = "ssl";
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->Port = 465;
        $this->mail->Username = "auditores.servicios@unad.edu.co";
        $this->mail->Password = "SONIA122";
        $this->mail->From = "no-reply@unad.edu.co";
        $this->mail->FromName = "Sistema de Informaci".chr(243)."n del Estudiante - SIVISAE - VISAE";
    }

    function enviarPass($asunto, $usuario, $password, $correo, $nombre) {
        $this->mail->Subject = $asunto;
        //$mail->AltBody = "Hola, esto es un correo de prueba de la VISAE.";
        $this->mail->MsgHTML(""
                . "<table>"
                . "<tr>"
                . "     <td colspan='2' ><b>Usuario y contraseña para ingresar al SIVISAE.<br></b>"
                . "         Escribir contraseña con los simbolos y puntos<br><br><br></td>"
                . "</tr>"
                . "<tr>"
                . "     <td style='alignment-adjust: middle'> Usuario:</td>"
                . "     <td><b>" . $usuario . "</b></td>"
                . "</tr>"
                . "<tr>"
                . "     <td style='alignment-adjust: middle'> Contraseña:</td>"
                . "     <td><b>" . $password . "</b></td>"
                . "</tr>"
                . "</table>"
                . "<br><br><br>"
                . "Puede ingresar a la aplicación desde <a href='" . RUTA_PPAL . "'>aquí</a><br>");
        //$mail->AddAttachment("files/img03.jpg");
        $this->mail->AddAddress($correo, $nombre);
        $this->mail->IsHTML(true);

        if (!$this->mail->Send()) {
            return "0";
        } else {
            return "1";
        }
    }

}
?>
