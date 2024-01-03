<?php
namespace juanignaso\phpmvc;

class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    /**
     * Redirigir al usuario a la ubicación pasada como parámetro
     * 
     * @param string $url - ruta de destino, ej: '/' o '/info'
     */
    public function redirect(string $url)
    {
        header('Location: ' . $url);
    }
}
?>