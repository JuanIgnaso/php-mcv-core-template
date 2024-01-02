<?php

namespace app\core\form;

use app\core\Model;

class InputField extends BaseField
{

    public string $type; //tipo del input
    public const TYPE_TEXT = 'text';
    public const TYPE_DATE = 'date';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_COLOR = 'color';
    public const TYPE_CHECKBOX = 'checkbox';

    public function __construct($model, $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }


    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function dateField()
    {
        $this->type = self::TYPE_DATE;
        return $this;
    }

    public function colorField()
    {
        $this->type = self::TYPE_COLOR;
        return $this;
    }

    public function checkBox()
    {
        $this->type = self::TYPE_CHECKBOX;
        return $this;
    }

    public function renderInput(): string
    {
        return sprintf(
            '<input type="%s" id="%s" name="%s" value="%s"  class="%s">',
            $this->type, //tipo o type
            $this->attribute, //id
            $this->attribute, //nombre
            $this->model->{$this->attribute}, //valor
            $this->model->hasError($this->attribute) ? 'invalid' : '', //clase
        );
    }
}