<?php

use Libraries\Response;


class Start
{
    
    /**
     * init
     *
     * @throws \Exception
     * @return void
     */
    public function init()
    {
        $function_name = $this->createRoute($_SERVER);
        if (!isset($_GET['param'])) {
            Response::sendResponse(404, ["msg" => "Unauthorized"]);
        }
        $controller = explode("/", $_GET['param']);
        try {
            $class_name = "\\Controllers\\" . ucfirst($controller[1]) . "Controller";

            if (!class_exists($class_name)) {
                throw new \Exception("Controller Not Found");
            }
            $class = new $class_name();
            if (!method_exists($class, $function_name)) {
                throw new \Exception("Method Not Found");
            }
            $params_url = $controller;
            unset($params_url[0], $params_url[1]);
            $class->{$function_name}(array_values($params_url));
        } catch (\Exception $e) {
            Response::sendResponse(404, ["msg" => $e->getMessage()]);
        }
    }
    
    /**
     * createRoute
     *
     * @param  array $params
     * @return string
     */
    private function createRoute(array $params = [])
    {
        $function_name = "";
        switch ($params['REQUEST_METHOD']) {
            case 'GET':
                $function_name = "index";
                break;
            case 'POST':
                $function_name = "insert";
                break;
            case 'PUT':
                $function_name = "update";
                break;
            case 'DELETE':
                $function_name = "delete";
                break;
        }
        return $function_name;
    }
}
