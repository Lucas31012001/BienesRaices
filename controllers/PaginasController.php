<?php

namespace Controllers;
use MVC\Router;
use Model\Propiedad;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class PaginasController{
    public static function index(Router $router){

        $propiedades = Propiedad::get(3);
        $inicio = true; // Para que salga el encabezado de la página inicial

        $router->render('paginas/index', [
            'propiedades' => $propiedades,
            'inicio' => $inicio
        ]);
    }

    public static function nosotros(Router $router){

        $router->render('paginas/nosotros');
    }

    public static function propiedades(Router $router){

        $propiedades = Propiedad::all();

        $router->render('paginas/propiedades', [
            'propiedades' => $propiedades
        ]);
    }

    public static function propiedad(Router $router){

        $id = validarORedireccionar('/propiedades');

        // Buscar la propiedad por su id
        $propiedad = Propiedad::find($id);

        $router->render('paginas/propiedad', [
            'propiedad' => $propiedad
        ]);
    }

    public static function blog(Router $router){

        $router->render('paginas/blog');
    }

    public static function entrada(Router $router){

        $router->render('paginas/entrada');
    }

    public static function contacto(Router $router){

        $mensaje = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $respuestas = $_POST['contacto'];

            // Crear una instancia de PHPMailer
            $mail = new PHPMailer();

            // Configurar SMTP(es el protocolo que se utiliza en el envío de emails. Es como el protocolo http para el envío de servidores)
            $mail->isSMTP(); // Le decimos que vamos a utilizar SMTP para el envío de correos
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '6a0e346d763337';
            $mail->Password = '362ed620d1b9f4';
            $mail->SMTPSecure = 'tls'; // Es un protocolo criptográfico que proporciona seguridad y privacidad. Antes se usaba SSL
            $mail->Port = 2525;

            // Configurar el contenido del mail
            $mail->setFrom('admin@bienesraices.com'); // Es quien envía el email
            $mail->addAddress('admin@bienesraices.com', 'BienesRaices.com'); // Es quien recibe el email y en segundo lugar el nombre del destinanatio
            $mail->Subject = 'Tienes un nuevo mensaje'; // Es el asunto del email

            // Habilitar HTML
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8'; // Esto lo ponemos porque vamos a recibir correos en lenguaje de España

            // Definir el contenido
            $contenido = '<html>';
            $contenido .= '<p>Tienes un nuevo mensaje</p>';
            $contenido .= '<p>Nombre: ' . $respuestas['nombre']  . '</p>';

            // Enviar de forma condicional algunos campos de email o teléfono
            if($respuestas['contacto'] === 'telefono'){
                $contenido .= '<p>Eligió ser contactado por teléfono:</p>';
                $contenido .= '<p>Teléfono: ' . $respuestas['telefono']  . '</p>';
                $contenido .= '<p>Fecha Contacto: ' . $respuestas['fecha']  . '</p>';
                $contenido .= '<p>Hora: ' . $respuestas['hora']  . '</p>';
            } else {
                // Es email, entonces agregamos el campo de email
                $contenido .= '<p>Eligió ser contactado por email:</p>';
                $contenido .= '<p>Email: ' . $respuestas['email'] . '</p>';
            }
            $contenido .= '<p>Mensaje: ' . $respuestas['mensaje']  . '</p>';
            $contenido .= '<p>Vende o Compra: ' . $respuestas['tipo']  . '</p>';
            $contenido .= '<p>Precio o Presupuesto: $' . $respuestas['precio']  . '</p>';
            $contenido .= '<p>Prefiere ser contactado por: ' . $respuestas['contacto']  . '</p>';
            $contenido .= '</html>';

            $mail->Body = $contenido;
            $mail->AltBody = 'Esto es texto alternativo sin HTML';

            // Enviar el email
            if($mail->send()){
                $mensaje = "Mensaje enviado correctamente";
            } else {
                $mensaje = "El mensaje no se pudo enviar...";
            }
        }

        $router->render('paginas/contacto', [
            'mensaje' => $mensaje
        ]);

    }
}