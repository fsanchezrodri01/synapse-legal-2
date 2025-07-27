<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    /*
    name
    email
    message
    */
    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
        $name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim($_POST["message"]);
        $apellido = strip_tags(trim($_POST["apellido"]));
        $apellido = str_replace(array("\r","\n"),array(" "," "),$apellido);

        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'mail.synapse-legal.com.mx';
            $mail->SMTPAuth = true;
            $mail->Username = 'contacto@synapse-legal.com.mx';
            $mail->Password = 'dddddd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Internal team email
            $mail->setFrom('contacto@synapse-legal.com.mx', 'Synapse Legal Website');
            $mail->addAddress('contacto@synapse-legal.com.mx');
            $mail->addAddress('marredondo@synapse-legal.com.mx');
            $mail->addAddress('ebocanegra@synapse-legal.com.mx');
            $mail->addAddress('evazquez@synapse-legal.com.mx');

            $mail->Subject = 'Nueva consulta desde el formulario de contacto';
            $mail->Body = "Detalles de la consulta:\n\nNombre: $name\nApellido: $apellido\nCorreo Electrónico: $email\nTeléfono: $phone\nComentarios: $message";
            $mail->send();

            // Prospect email
            if (!empty($email)) {
                $mail->clearAddresses();
                $mail->addAddress($email);
                $mail->addBCC('marredondo@synapse-legal.com.mx');
                $mail->addBCC('ebocanegra@synapse-legal.com.mx');
                $mail->addBCC('evazquez@synapse-legal.com.mx');

                $mail->Subject = 'Gracias por contactar a Synapse Legal';
                $mail->Body = "Estimado/a $name,\n\nGracias por contactarnos. Hemos recibido su mensaje y nos pondremos en contacto con usted pronto.\n\nSaludos cordiales,\nEquipo Synapse Legal";
                $mail->send();
            }

            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! Your message has been sent.";
        } catch (Exception $e) {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong ande we couldn't send your message.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }

?>