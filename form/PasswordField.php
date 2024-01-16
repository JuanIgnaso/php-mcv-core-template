<?php

namespace juanignaso\phpmvc\form;

class PasswordField extends BaseField
{
    public function renderInput(): string
    {
        return sprintf(
            '
            <div>
            <input id="%s" class="%s" name="%s" value="%s" type="password"><button type="button" class="showUp"aria-label="mostrar contraseña"><i class="fa-regular fa-eye"></i></button>
            </div>
            ',
            $this->attribute, //id
            $this->model->hasError($this->attribute) ? 'invalid' : '', //clase
            $this->attribute, //nombre
            $this->model->{$this->attribute}, //valor
        );
    }
}