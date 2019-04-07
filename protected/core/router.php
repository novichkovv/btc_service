<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 14.09.17
 * Time: 12:48
 */
class router
{
    const ROUTES = [
        'addresses/generate' => [
            'controller' => 'addresses_controller',
            'action' => 'generate',
            'methods' => ['GET']
        ]
    ];
    private $route;

    public function __construct()
    {
        $this->getRoute();
        $this->init();
    }

    private function getRoute()
    {
        if(isset($_GET['route'])) {
            $route = trim($_GET['route'], '\\/');
        } else {
            $route = '';
        }
        registry::set('route', $route);
        $this->route = $route;
    }

    protected function init()
    {
        $request = new request();
        $response = new response();
        if(!isset(self::ROUTES[$this->route])) {
            $response->withJson(['status' => 'fail', 'error' => 'Not Found']);
            $response->withStatus(404);
            $response->respond();
        } else {
            $item = self::ROUTES[$this->route];
            if(!in_array($request->getMethod(), $item['methods'])) {
                $response->withJson(['status' => 'fail', 'error' => 'Method Not Allowed']);
                $response->withStatus(405);
                $response->respond();
            }
            $controller = $item['controller'];
            $action = $item['action'];
            $controller_file = PROTECTED_DIR . 'controllers/' . $controller . '.php';
            if(file_exists($controller_file) && class_exists($controller)) {
                $instance = new $controller($request, $response);
                if(method_exists($instance, $action)) {
                    $instance->$action();
                    exit;
                }
            }
        }
        $response->withJson(['status' => 'fail', 'error' => 'Unexpected Error']);
        $response->withStatus(500);
        $response->respond();
    }
}