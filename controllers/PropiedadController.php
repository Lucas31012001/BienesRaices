<?php

namespace Controllers;
use MVC\Router;
use Model\Propiedad;
use Model\Vendedor;


class PropiedadController{

    public static function index(Router $router){ // Le pasamos el Router que hemos creado en index.php
        $propiedades = Propiedad::all();
        $vendedores = Vendedor::all();

        // Muestra mensaje condicional
        $resultado = $_GET['resultado'] ?? null;

        $router->render('propiedades/admin', [
            'propiedades' => $propiedades,
            'resultado' => $resultado,
            'vendedores' => $vendedores
        ]);
    }

    public static function crear(Router $router){ // Le pasamos el Router que hemos creado en index.php
        $propiedad = new Propiedad;
        $vendedores = Vendedor::all();

        // Arreglo con mensajes de errores
        $errores = Propiedad::getErrores();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            /** CREA UNA NUEVA INSTANCIA */
            $propiedad = new Propiedad($_POST['propiedad']);

            /** SUBIDA DE ARCHIVOS */
            // Crear carpeta
            if(!is_dir(CARPETA_IMAGENES)){
            mkdir(CARPETA_IMAGENES, 0755, true); // 0755 son permisos de lectura y escritura para el propietario, y solo lectura para el grupo y otros
            }

            // Generar un nombre único 
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            if($_FILES['propiedad']['tmp_name']['imagen']){ // Más abajo explico lo que hace esto
                move_uploaded_file($_FILES['propiedad']['tmp_name']['imagen'], CARPETA_IMAGENES . $nombreImagen);
                $propiedad->setImagen($nombreImagen);
            }
            
            // Validar
            $errores = $propiedad->validar();

            if(empty($errores)){            

                // Guarda en la base de datos
                $propiedad->guardar();
            }
        }
    
        $router->render('propiedades/crear', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }

    public static function actualizar(Router $router){
        $id = validarORedireccionar('/admin');

        $propiedad = Propiedad::find($id);

        $vendedores = Vendedor::all();

        $errores = Propiedad::getErrores();

        // Método POST para actualizar
        if($_SERVER['REQUEST_METHOD'] === 'POST'){ // Esta línea de código PHP verifica si la solicitud HTTP que está siendo procesada es del tipo POST

            // Asignar los atributos
            $args = $_POST['propiedad'];
    
            $propiedad->sincronizar($args);
    
            // Validación
            $errores = $propiedad->validar();
    
            /** SUBIDA DE ARCHIVOS */
    
            if(!is_dir(CARPETA_IMAGENES)){
                mkdir(CARPETA_IMAGENES, 0755, true); // 0755 son permisos de lectura y escritura para el propietario, y solo lectura para el grupo y otros
            }
    
            // Generar un nombre único 
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
    
            if($_FILES['propiedad']['tmp_name']['imagen']){ // Más abajo explico lo que hace esto
                move_uploaded_file($_FILES['propiedad']['tmp_name']['imagen'], CARPETA_IMAGENES . $nombreImagen);
                $propiedad->setImagen($nombreImagen);
            }
    
            if(empty($errores)){
                $propiedad ->guardar();
            }
        }

        $router->render('/propiedades/actualizar', [
            'propiedad' => $propiedad,
            'errores' => $errores,
            'vendedores' => $vendedores
        ]);
    }

    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            // Validar id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
    
            if($id){
                $tipo = $_POST['tipo'];
                if(validarTipoContenido($tipo)){
                    $propiedad = Propiedad::find($id);
                    $propiedad->eliminar();
                }
            }
        }
    }
}

