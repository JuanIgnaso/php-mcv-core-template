<?php

namespace juanignaso\phpmvc\db;

use juanignaso\phpmvc\Model;
use juanignaso\phpmvc\Application;

abstract class DBModel extends Model
{
    /**
     * Devuelve el nombre de la tabla que va a usar el modelo
     * @return string
     */
    abstract public function tableName(): string;

    //Debería traer todos los nombre de las columnas de la tabla
    abstract public function attributes(): array;
    abstract public function primaryKey(): string;

    /**
     * Guarda un registro dentro de la tabla del modelo
     */
    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attributes) . ") 
        VALUES(" . implode(',', $params) . ")");
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    /**
     * Devuelve una lista de los valores de un atributo que coincidan
     * con el valor cargado en ese atributo(el atributo en cuestión debe de estar previamente
     * cargado en el modelo mediante $modelo->loadData()).
     * 
     * @param $attr
     */
    public function getAttrList($attr)
    {
        $tableName = $this->tableName();
        $statement = self::prepare("SELECT $attr  FROM  $tableName WHERE $attr LIKE :search ORDER BY 1");
        $statement->bindValue(":search", "%" . $this->{$attr} . "%");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Borra registro dentro de la tabla especificada en el modelo, devolviendo true o false dependiendo si ha logrado hacer la operación
     * @return bool
     */
    public function delete(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $sql = implode(' AND ', array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("DELETE FROM $tableName WHERE $sql");
        //bind values
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();

        return $statement->rowCount() != 0;
    }

    /**
     * Recoge todos los datos de la tabla especificada en el modelo
     */
    public function getAll()
    {
        $tableName = $this->tableName();
        return self::query("SELECT * FROM $tableName ORDER BY 1")->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function query($sql)
    {
        return Application::$app->db->pdo->query($sql);
    }

    /**
     * Encuentra una ocurrencia dentro de la tabla definida dentro del modelo
     * @param array $where
     */
    public function findOne($where) //Ej: [email => 'email', username => 'name']
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(' AND ', array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
        //SELECT * FROM $tableName WHERE email=:email AND username = :username
    }
}