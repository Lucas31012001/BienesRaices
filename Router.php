<?php

namespace MVC;

class Router{

    public $rutasGET = [];
    public $rutasPOST = [];

    public function get($url, $fn){
        $this->rutasGET[$url] = $fn;
    }

    public function post($url, $fn){
        $this->rutasPOST[$url] = $fn;
    }
    
    public function comprobarRutas(){

        session_start();

        $auth = $_SESSION['login'] ?? null;

        // Arreglo de rutas protegidas...
        $rutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', 
        '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];

        $urlActual = $_SERVER['PATH_INFO'] ?? '/';
        $metodo = $_SERVER['REQUEST_METHOD'];

        if($metodo === 'GET'){
            $fn = $this->rutasGET[$urlActual] ?? null;
        } else {
            $fn = $this->rutasPOST[$urlActual] ?? null;
        }

        // Proteger las rutas
        if(in_array($urlActual, $rutas_protegidas) && !$auth){
            header('Location: /');
        }

        if($fn){
            // La URL existe y hay una función asociada
            call_user_func($fn, $this); // Es una función que permite llamar a la función cuando no sabemos como se llama esa función. En este caso no sabemos qué función se va a llamar
        } else {
            echo "Página no encontrada";
        }
    }

    // Muestra una vista
    public function render($view, $datos = []){ // render es el método que nos sirve para mostrar una vista
        foreach($datos as $key => $value){
            $$key = $value; // Al tener dos $$ a la key se le asigna el valor de $value
        }

        ob_start(); // Almacenamiento en memoria de la vista de abajo durante un momento...
        include __DIR__ . "/views/$view.php";

        $contenido = ob_get_clean(); // Limpia el Buffer
        include __DIR__ . "/views/layout.php";
    }
}