<?php
 
namespace Classes;
 
use PHPMailer\PHPMailer\PHPMailer;
use Dotenv\Dotenv as Dotenv;
$dotenv = Dotenv::createImmutable('../includes/.env');
$dotenv->safeLoad();
 
class Email {
 
    protected $email;
    protected $nombre;
    protected $token;
 
    public function __construct($email, $nombre, $token) 
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }
 
    public function enviarConfirmacion() {
        // Crear el objeto de email
        $mail = new PHPMailer();
 
        // Configurar SMTP
        //$mail->getSentMIMEMEssage();
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->Username = $_ENV['MAIL_USER'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['MAIL_SECURE'];
        $mail->AuthType = $_ENV['MAIL_AUTH'];
        $mail->Port = $_ENV['MAIL_PORT'];
 
        // Contenido del email
        $mail->setFrom($_ENV['MAIL_USER'], 'cuentas@uptask.agustinsanchez.dev');
        $mail->addAddress($this->email,$this->nombre);
        $mail->Subject = 'Confirma tu cuenta';
 
        // Habilitar HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
 
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Haz creado tu cuenta en UpTask, solo debes confirmarla presionando el siguiente enlace </p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['SERVER_HOST'] . "/confirmar?token=" . $this->token . "'>Confirmar Cuenta </a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje </p>";
        $contenido .= "</html>";
 
        $mail->Body = $contenido;
 
        // Enviamos el email
        $mail->send();
    }
 
    public function enviarReset() {
        // Crear el objeto de email
        $mail = new PHPMailer();
 
        // Configurar SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->Username = $_ENV['MAIL_USER'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['MAIL_SECURE'];
        $mail->AuthType = $_ENV['MAIL_AUTH'];
        $mail->Port = $_ENV['MAIL_PORT'];
 
        // Contenido del email
        $mail->setFrom($_ENV['MAIL_USER'], 'cuentas@uptask.agustinsanchez.dev');
        $mail->addAddress($this->email,$this->nombre);
        $mail->Subject = 'Reestablece tu password';
 
        // Habilitar HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
 
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Haz solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo. </p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['SERVER_HOST'] . "/reestablecer?token=" . $this->token . "'>Reestablecer Password </a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje </p>";
        $contenido .= "</html>";
 
        $mail->Body = $contenido;
 
        // Enviamos el email
        $mail->send();
    }
}