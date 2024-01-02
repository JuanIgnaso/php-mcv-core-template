<?php

namespace app\core\form;

class CheckBox extends BaseField
{

    public string $value;
    public function __construct($model, $attribute, $value)
    {
        parent::__construct($model, $attribute);
        $this->value = $attribute;
    }

    public function renderInput(): string
    {
        return sprintf(
            '<input type="checkbox" id="%s" name="%s" class="%s" value="%s">',
            $this->attribute, //id
            $this->attribute, //nombre
            $this->model->hasError($this->attribute) ? 'invalid' : '', //clase
            $this->value, //valor
        );
    }
}