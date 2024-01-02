<?php

namespace app\core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            //marcar para destruirlas
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    /**
     * Función para setear alertas o mensajes al realizar acciones.
     * @param $key - nombre del mensaje
     * @param $message - mensaje a mostrar
     */
    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message,
        ];
    }

    /**
     * Función para llamar a un flash message
     * 
     * @param string $key - Nombre del flash message
     */
    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }


    /**
     * Crea una nueva entrada en arrat $_SESSION pasando clave y valor
     * como parámetro
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Recoge una key de sessión pasando clave como parámetro
     */
    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    /**
     * Elimina la key de $_SESSION pasada como parámetro
     * 
     */
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        //Iterar sobre todas las que estan marcadas y destruirlas
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            //marcar para destruirlas
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }


}