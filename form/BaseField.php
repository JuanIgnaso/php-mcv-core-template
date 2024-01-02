<?php

namespace app\core\form;

use app\core\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute;

    public function __construct($model, $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }
    /*
    Devería devolver textarea,input o select...
    */
    abstract public function renderInput(): string;

    public function __toString()
    {
        return sprintf('
            <div class="form_group">
                <label><strong>%s</strong></label>
                %s
                <p class="input_error">%s</p>
            </div>
        ',
            $this->model->getLabel($this->attribute), //label
            $this->renderInput(), //renderizar input/textarea...
            $this->model->getFirstError($this->attribute), //Error
        );
    }
}