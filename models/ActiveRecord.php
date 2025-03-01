<?php

namespace Model;

class ActiveRecord {
    // Base de datos
    protected static $db;
    protected static $columnasDB = [];
    protected static $tabla = '';

    // Errores
    protected static $errores = [];

    // Definir la conexión a la BD
    public static function setDB($database){
        self::$db = $database;
    }

    public function guardar(){
        if(!is_null($this->id)){
            // Actualizar
            $this->actualizar();
        } else {
            // Creando un nuevo registro
            $this->crear();
        }
    }

    public function crear(){
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        /*
        $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedores_id) 
        VALUES ('$this->titulo', '$this->precio', '$this->imagen', '$this->descripcion', '$this->habitaciones', '$this->wc', '$this->estacionamiento', '$this->creado', '$this->vendedorId')";
        */

        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        $resultado = self::$db->query($query);

        // Mensaje de éxito
        if($resultado){
            // Redireccionar al usuario
            header('Location: /admin?resultado=1');
        }
    }

    public function actualizar(){
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach($atributos as $key => $value){
            $valores[] = "{$key}='{$value}'";
        }

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";

        $resultado = self::$db->query($query);

        if($resultado){
            // Redireccionar al usuario
            header('Location: /admin?resultado=2');
        }    
    }

    // Eliminar un registro
    public function eliminar(){
        // Eliminar la propiedad
        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado){
            $this->borrarImagen();
            header('Location: /admin?resultado=3');
        }
    }

    // Identifica y une los atributos de la BD
    public function atributos(){
        $atributos = [];
        foreach(static::$columnasDB as $columna){
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos(){
        $atributos = $this->atributos();
        $sanitizado = [];

        foreach($atributos as $key => $value){
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Subida de archivos
    public function setImagen($imagen){
        // Elimina la imagen previa
        if(!is_null($this->id)){
            $this->borrarImagen();
        }
        // Asignar al atributo de imagen el nombre de la imagen
        if($imagen){
            $this->imagen = $imagen;
        }
    }

    // Elimina el archivo
    public function borrarImagen(){
        // Comprobar si existe el archivo
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        if($existeArchivo){
            unlink(CARPETA_IMAGENES . $this->imagen);
        }
    }

    // Validación
    public static function getErrores(){
        return static::$errores;
    }

    public function validar(){
        static::$errores = [];
        return static::$errores;
    }

    // Lista todos los registros
    public static function all(){
        $query = "SELECT * FROM " . static::$tabla; // static hace que se llame al atributo de $tabla en cada una de las clases que heredan de ActiveRecord
        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    // Lista todos los registros
    public static function get($cantidad){
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad;
        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id){
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = $id";

        $resultado = self::consultarSQL($query);

        return(array_shift($resultado)); // array_shift elimina el primer elemento de un array
    }

    public static function consultarSQL($query){
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()){
            $array[] = static::crearObjeto($registro);
        }

        // Liberar la memoria
        $resultado->free(); // Liberamos la memoria porque ya terminamos de hacer la consulta

        // Retornar los resultados
        return $array;
    }

    protected static function crearObjeto($registro){
        $objeto = new static; 

        foreach($registro as $key => $value){
            if(property_exists($objeto, $key)){ // Verifica si la propiedad existe en el objeto. Para cada clave $key, se verifica si existe una propiedad con el mismo nombre en el objeto $objeto
                $objeto->$key = $value; // Asigna el valor del registro a la propiedad correspondiente del objeto
            }
        }
        return $objeto; // Devuelve el objeto de tipo Propiedad con las propiedades establecidas
    }

    // Sincroniza el objeto en memoria con los cambios realizados por el usuario
    public function sincronizar($args = []){
        foreach($args as $key => $value){
            if(property_exists($this, $key) && !is_null($value)){
                $this->$key = $value;
            }
        }
    }
}

// fetch_assoc() se utiliza para obtener una fila de resultados de una consulta SQL como un array asociativo