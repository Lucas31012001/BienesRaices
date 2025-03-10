<?php

define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . 'funciones.php');
define('CARPETA_IMAGENES', __DIR__ . '/../public/imagenes/');

function incluirTemplate($nombre, $inicio = false){
    include TEMPLATES_URL . "/${nombre}.php";
}

function estaAutenticado() {
    session_start();

    if(!$_SESSION['login']){
        header('Location: /bienesraices_inicio/index.php');
    }
}

function debuguear($variable){
    echo "<pre>";
    var_dump($variable);
    echo "<pre>";
    exit;
}

// Sanitizar el HTML
function s($html) : string{
    $s = htmlspecialchars($html); // Se utiliza para convertir caracteres especiales en entidades HTML. Esto es útil principalmente para prevenir ataques de Cross-site Scripting (XSS) al asegurar que cualquier texto que incluya caracteres especiales se muestre correctamente en el navegador sin ser interpretado como código HTML o JavaScript
    return $s;
}

// Validar tipo de contenido
function validarTipoContenido($tipo) {
    $tipos = ['vendedor', 'propiedad'];

    return in_array($tipo, $tipos); //in_array() se encarga de buscar un valor en un array
}

// Muestra los mensajes
function mostrarNotificacion($codigo){
    $mensaje = '';

    switch($codigo) {
        case 1:
            $mensaje = 'Creado Correctamente';
            break;
        case 2:
            $mensaje = 'Actualizado Correctamente';
            break;
        case 3:
            $mensaje = 'Eliminado Correctamente';
            break;
        default:
            $mensaje = false;
            break;
    }
    
    return $mensaje;
}

function validarORedireccionar(string $url){
    // Validar la URL por ID válido
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id){
        header("Location: ${url}");
    }

    return $id;
}