<?php

namespace Controllers;

use Models\User;
use Libraries\Response;
use Libraries\Request;
use Libraries\MessageBroker;

class UserController
{
    /**
     * index
     * @param array $params params get
     *
     * @return void
     */
    public function index(array $params = [])
    {
        $data = Request::verifyToken([0, 1]);
        $filter = [];
        if ($data["type"] == 1) {
            $filter["iduser"] = $data["user_id"];
        } elseif (!empty($params[0])) {
            if (is_numeric($params[0])) {
                $filter["iduser"] = $params[0];
            } else {
                $filter["email"] = $params[0];
            }
        }
        $columns = "iduser, name, email, type";
        if ($data['auth']) {
            $columns .= ", password, apikey";
        }
        $users = User::find($columns, $filter);
        if (empty($users)) {
            Response::sendResponse(200, ["msg" => "No Users Found", "info" => []]);
        }
        Response::sendResponse(200, ["msg" => "Users Found", "info" => $users]);
    }

    /**
     * insert
     *
     * @return void
     */
    public function insert()
    {
        Request::verifyToken([0]);
        $post = Request::getPostParams();
        if (empty($post['name']) || empty($post['email']) || empty($post['password']) || !isset($post['type'])) {
            Response::sendResponse(422, ["msg" => "Parameters not found"]);
        }
        if ($user_exist = User::find("*", ["email" => $post['email']])) {
            Response::sendResponse(205, ["msg" => "User Already Exist", "id" => $user_exist[0]->iduser]);
        }
        $response = $this->save($post);
        if ($response) {
            MessageBroker::sendMessage("userCreate", ["iduser" => $response, "name" => $post['name'], "email" => $post['email']]);
            Response::sendResponse(200, ["msg" => "Inserted Success", "id" => $response]);
        } else {
            Response::sendResponse(422, ["msg" => "Error on insert record"]);
        }
    }

    /**
     * update
     *
     * @param  array $params
     * @return void
     */
    public function update(array $params = [])
    {
        $user = Request::verifyToken([0, 1]);
        $us = $this->checkUser($params);
        if ($us && ($user[0]->type == 0 || $user[0]->iduser == $us[0]->iduser)) {
            //only permit admin or same user
            $post = Request::getPostParams();
            if (!empty($post['password'])) {
                $post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
            }
            $post = array_merge((array)$us[0], $post);
            $response = $this->save($post, $us[0]->iduser);
            if ($response) {
                MessageBroker::sendMessage("userUpdate", ["iduser" => $us[0]->iduser, "name" => $post['name'], "email" => $post['email']]);
                Response::sendResponse(200, ["msg" => "Updated Success"]);
            } else {
                Response::sendResponse(422, ["msg" => "Error on updated record"]);
            }
        } else {
            Response::sendResponse(404, ["msg" => "User Not Found"]);
        }
    }


    /**
     * checkUser
     *
     * @param  array $params
     * @return array
     */
    private function checkUser(array $params = [])
    {
        if (empty($params)) {
            Response::sendResponse(422, ["msg" => "Parameters not found"]);
        }
        if (is_numeric($params[0])) { //check if id or email
            $us = User::find("*", ['iduser' => $params[0]]);
        } else {
            $us = User::find("*", ['email' => $params[0]]);
        }
        return $us;
    }


    /**
     * save
     *
     * @param  array $post
     * @param  int $id
     * @return boolean|int
     */
    private function save(array $post, ?int $id = null)
    {
        $user_class = new User();
        $user_class->name = $post['name'];
        $user_class->email = $post['email'];
        $user_class->type = $post['type'];
        if ($id) {
            $user_class->iduser = $id;
            $user_class->password = $post['password'];
            return $user_class->update();
        } else {
            $user_class->apikey = md5(json_encode($post) . date('Y-m-d H:i:s'));
            $user_class->password = password_hash($post['password'], PASSWORD_BCRYPT);
            return $user_class->insert();
        }
    }



    /**
     * delete
     *
     * @param  array $params
     * @return void
     */
    public function delete(array $params = [])
    {
        Request::verifyToken([0]);
        $us = $this->checkUser($params);
        if (!$us) {
            Response::sendResponse(404, ["msg" => "User Not Found"]);
        }
        $user_class = new User();
        $user_class->iduser = $us[0]->iduser;
        if ($user_class->delete()) {
            MessageBroker::sendMessage("userDelete", ["iduser" => $us[0]->iduser]);
            Response::sendResponse(200, ["msg" => "Delete Success"]);
        } else {
            Response::sendResponse(422, ["msg" => "Error on delete record"]);
        }
    }
}
