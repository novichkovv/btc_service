<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 14.09.17
 * Time: 14:46
 */
class controller extends base
{
    public $breadcrumbs = [];
    protected $request;
    protected $response;
    private $modules = [];
    protected $rules = [];
    protected $user;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->init();
        $this->rules();
        $this->getRules();
        if(!$this->checkAuth()) {
            $this->unauthorized();
        }
        $this->common();
    }

    protected function common()
    {

    }

    protected function unauthorized()
    {
        $this->response->withStatus(401);
        $this->response->withContentType('application/json');
        $this->response->withJson(['status' => 'fail', 'error' => 'unauthorized']);
        $this->response->respond();
    }

    protected function forbidden()
    {
        $this->response->withStatus(403);
        $this->response->withContentType('application/json');
        $this->response->withJson(['status' => 'fail', 'error' => 'forbidden']);
        $this->response->respond();
    }

    protected function error($error = 'Unexpected Error!')
    {
        $this->response->withStatus(500);
        $this->response->withContentType('application/json');
        $this->response->withJson(['status' => 'fail', 'error' => $error]);
        $this->response->respond();
    }

    protected function rules()
    {

    }

    protected function init()
    {

    }

    public function json(array $response)
    {
        $this->response->withJson($response);
        $this->response->withStatus(200);
        $this->response->withContentType('application/json');
        $this->response->respond();
    }

    public function success($response = null)
    {
        $res = [
            'status' => 'success'
        ];
        if(is_array($response)) {
            $res = array_merge($res, $response);
        } elseif($response !== null) {
            $res['template'] = $response;
        }
        $this->json($res);
    }

    public function fail($response = null)
    {
        $res = [
            'status' => 'fail'
        ];
        if(is_array($response)) {
            $res = array_merge($res, $response);
        } elseif($response !== null) {
            $res['template'] = $response;
        }
        $this->json($res);
    }

    public function withJson(array $response)
    {
        $this->response->withJson($response);
    }

    public function module($module)
    {
        if(null === $this->modules[$module]) {
            $class_name = $module . '_module';
            $this->modules[$module] = new $class_name;
        }
        return $this->modules[$module];

    }

    public function checkAuth()
    {

        if($token = $this->request->getHeader('Authorization')) {
            if(authorization::checkToken($token)) {
                registry::set('auth', true);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getRules()
    {
        $rules = $this->getDefaultRules();

        $endpoint = empty(registry::get('endpoint')) ? 'content' : registry::get('endpoint');
        if(!empty($this->rules[$endpoint])) {
            foreach ($this->rules[$endpoint] as $k => $v) {
                $rules[$k] = $v;
            }
        }
        $this->rules = $rules;
    }

    function getDefaultRules()
    {
        return [
            'auth' => true,
            'allowed_methods' => ['POST'],
        ];
    }

    private function checkAllowedMethods($rules)
    {
        if(!empty($rules['allowed_methods'])) {
            return in_array($this->request->getMethod(), $rules['allowed_methods']);
        } else {
            return true;
        }
    }

    private function checkForbiddenMethods($rules)
    {
        if(!empty($rules['forbidden_methods'])) {
            return !in_array($this->request->getMethod(), $rules['forbidden_methods']);
        } else {
            return true;
        }
    }
}