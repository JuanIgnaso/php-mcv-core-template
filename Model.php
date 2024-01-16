<?php

namespace juanignaso\phpmvc;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_REGEX = 'regex';
    public const RULE_UNIQUE = 'unique';
    public const RULE_CHECKED = 'checked';
    public const RULE_WHITE_SPACE = 'white space';
    public array $errors = [];

    public function loadData($data)
    {
        /*
        con esto estamos cogiendo los datos y asignandolos a las
        propiedades del modelo

        */
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    #funcion abstracta, cada clase hija podrá definirla a su manera
    abstract function rules(): array;

    public function labels(): array
    {
        return [];
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /**
     * Función genérica para validar que los atributos cargados en el modelo cumplen
     * con las validaciones especificadas, devuelve el error asociado con la validación en
     * el método errorMessages()
     * 
     * @see errorMessages()
     * @return bool - devuelve si está o no vacío el array de errores
     */
    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {

            $value = $this->{$attribute};

            foreach ($rules as $rule) {

                $ruleName = $rule;

                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_WHITE_SPACE && ctype_space($value)) {
                    $this->addErrorForRule($attribute, self::RULE_WHITE_SPACE);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_REGEX && !preg_match($rule['regex'], $value)) {
                    $this->addErrorForRule($attribute, self::RULE_REGEX, $rule);
                }
                if ($ruleName === self::RULE_CHECKED && !in_array($value, Application::$app->request->getBody())) {
                    $this->addErrorForRule($attribute, self::RULE_CHECKED);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Añade el error correspondiente al array de errores.
     * 
     * @param string $attribute - Nombre del atributo.
     * @param string $rule - Regla de validación.
     * @param array $params  
     */
    private function addErrorForRule(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            //Reemplaza el valor con el texto entre {} del mensaje de error
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }


    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * Devuelve un array con los mensajes de error asociados con cada regla de validación
     * 
     * @return array - mensajes de error.
     */
    public function errorMessages(): array
    { //Array con los mensajes de error
        return [
            self::RULE_REQUIRED => 'Este campo es obligatorio',
            self::RULE_EMAIL => 'Tienes que escribir un email válido',
            self::RULE_MIN => 'El largo mínimo debe de ser de {min}',
            self::RULE_MAX => 'El largo máximo debe de ser de {max}',
            self::RULE_MATCH => 'Este campo debe de ser igual a <strong>{match}</strong>',
            self::RULE_REGEX => 'Este campo no cumple con el patrón requerido: {text}',
            self::RULE_UNIQUE => 'El valor escrito en <strong>{field}</strong> ya existe',
            self::RULE_CHECKED => 'Debes de marcar esta opción',
            self::RULE_WHITE_SPACE => 'No se aceptan cadenas vacías',
        ];
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Recoge el primer error al atributo, ya que puede que haya más de un error si
     * el atributo tiene más de una validación.
     * 
     * @return string mensaje del error
     * @return bool false - si no tiene ninguno.
     */
    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
}
