<?php

namespace juanignaso\phpmvc;

class Request
{
    /**
     * Devuelve la ruta recibida como petición sin las variables.
     * Por ejemplo de /ruta?id=4&var=x -> devuelve: '/ruta'
     * 
     * @return string 
     * 
     */
    public function getPath()
    {
        #tenemos que recoger REQUEST_URI y extraer todo antes del '?'
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        $position = strpos($path, '?'); #determinamos la posición del primer '?'

        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    /**
     * Devuelve el método usado en la petición al server
     */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']); //devuelve 'GET' o 'POST'
    }

    /**
     * Determinar si la petición es get o post.
     */
    public function isGet()
    {
        return $this->method() === 'get';
    }

    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function getBody()
    {
        #esto devería devolver todos los datos.
        if ($this->method() === 'get') {
            return filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS);
            // foreach ($_GET as $key => $value) {
            //         $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            // }
        }
        if ($this->method() === 'post') {
            return filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // foreach ($_POST as $key => $value) {
            //     $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            //}
        }
    }

}

?>