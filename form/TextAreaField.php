<?php

namespace app\core\form;

class TextAreaField extends BaseField
{
    public function renderInput(): string
    {
        return sprintf(
            '<textarea id="%s" name="%s" class="%s">%s</textarea>',
            $this->attribute, //id
            $this->attribute, //nombre
            $this->model->hasError($this->attribute) ? 'invalid' : '', //clase
            $this->model->{$this->attribute}, //valor
        );
    }
}