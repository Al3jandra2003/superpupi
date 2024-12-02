<?php
session_start();

// Asegúrate de que el ticket ha sido generado antes de enviar
if (isset($_POST['enviar_correo'])) {
    // Incluir PHPMailer
    require('PHPMailer/src/PHPMailer.php');
    require('PHPMailer/src/SMTP.php');
    require('PHPMailer/src/Exception.php');

    // Crear una instancia de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer(true); // Cambia aquí para evitar el uso de 'use'

    try {
        // Configuraciones del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Cambia si usas otro servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'gomezalejands@gmail.com'; // Tu correo
        $mail->Password = 'yrhg zdoe vfuq pazf'; // Contraseña de aplicación
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Destinatario
        $mail->setFrom('gomezalejands@gmail.com', 'SuperPupi');
        $mail->addAddress('gomezalejands@gmail.com'); // Cambia al correo del usuario

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Tu Ticket de Compra';
        $mail->Body    = 'Aquí tienes tu ticket de compra.';

        // Adjuntar el PDF generado
        $mail->addAttachment('ticket_compra.pdf'); // Asegúrate de que el ticket ha sido generado

        $mail->send();
        echo 'El ticket ha sido enviado por correo.';
    } catch (Exception $e) {
        echo "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "No se ha generado el ticket.";
}
?>
