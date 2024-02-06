<?php

namespace Models;

use Config\Database;

class User
{
    public int $iduser;
    public string $name;
    public string $email;
    public string $password;
    public string $apikey;
    public int $type;
    public string $created_at;
    public string $updated_at;

    /**
     * find
     *
     * @param  string $columns
     * @param  array $filters
     * @return array|null
     */
    public static function find(string $columns = "*", array $filters = [])
    {
        $sql = "SELECT " . $columns . " FROM user  ";
        if (!empty($filters)) {
            $sql .= " WHERE ";
            $count = 0;
            foreach ($filters as $column => $value) {
                if ($count > 0) {
                    $sql .= " AND ";
                }
                $sql .= $column . " = :" . $column;
                $count++;
            }
        }
        return Database::getResults($sql, $filters);
    }

    /**
     * insert
     *
     * @return boolean|int
     */
    public function insert()
    {
        $sql = " INSERT INTO `user`
        (`iduser`, `name`, `email`, `password`, `apikey`, `type`)
        VALUES 
        (null,     :name,   :email,  :password,  :apikey, :type);";
        $values = [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
            "apikey" => $this->apikey,
            "type" => $this->type
        ];
        return Database::operation($sql, $values);
    }


    /**
     * update
     *
     * @return boolean
     */
    public function update()
    {
        $sql = "UPDATE `user` SET 
        `name` = :name, `email` = :email, `password` = :password, `type` = :type
        WHERE `iduser` = :iduser";
        $values = [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
            "iduser" => $this->iduser,
            "type" => $this->type
        ];
        return Database::operation($sql, $values);
    }


    /**
     * delete
     *
     * @return boolean
     */
    public function delete()
    {
        $sql = "DELETE FROM `user`  WHERE `iduser` = :iduser";
        $values = [
            "iduser" => $this->iduser
        ];
        return Database::operation($sql, $values);
    }
}
