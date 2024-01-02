<?php

namespace app\core\form;

use app\core\Model;
use app\core\form\InputField;

class Form
{
    public static function begin($action, $method)
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        return '</form>';
    }

    /**
     * Crea un nuevo field en el formulario.
     * 
     * @param Model - Modelo a usar
     * @param string $attribute - Nombre del atributo(nombre y id)
     * @param string $label - Texto del label del input
     * 
     */
    public function field(Model $model, $attribute)
    {
        return new InputField($model, $attribute);
    }
}