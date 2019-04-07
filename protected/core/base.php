<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 14.09.17
 * Time: 12:51
 */
class base
{
    protected $vars = [];
    protected $apis = [];
    /**
     * @param $model
     * @param string $table
     * @param string $db
     * @param string $user
     * @param string $password
     * @return model
     */

    protected function model($model, $table = null, $db = null, $user = null, $password = null)
    {
        $models = registry::get('models');
        if(!$m = $models[$model][$table]) {
            $model_file = PROTECTED_DIR . 'models' . DS . $model . '_model.php';
            if(file_exists($model_file)) {
                $model_class = $model . '_model';
                $m = new $model_class($table ? $table : $model, $db, $user, $password);
            } else {
                $m = new default_model($model);
            }
            $models[$model][$table] = $m;
            registry::remove('models');
            registry::set('models', $models);
        }
        return $m;
    }

    /**
     * @param string $file
     * @param mixed $value
     * @param string $mode
     * @param bool $json
     */

    public function writeLog($file, $value, $mode = 'a+', $json = true) {
        if(is_array($value)) {
            if($json) {
                $value = json_encode($value);
            } else {
                $value = print_r($value, 1);
            }
        }
        if(!$value) {
            $value = gettype($value);
        }
        if(mb_detect_encoding($value) != 'UTF-8') {
            $value = mb_convert_encoding($value, 'cp1251', 'utf8');
        }
        $file_name = PUBLIC_DIR . 'tmp' . DS . 'logs' . DS . $file . '.log';
        $array = explode('/', $file_name);
        array_pop($array);
        $folder = implode('/', $array);
        if(!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        $f = fopen(PUBLIC_DIR . 'tmp' . DS . 'logs' . DS . $file . '.log', $mode);
        fwrite($f, date('Y-m-d H:i:s') . ' - ' . $value . "\n");
        fclose($f);
    }

    /**
     * @param string $key
     * @return string
     */

    protected function getConfig($key)
    {
        if(!$key) {
            return false;
        }
        if(!$value = registry::get('config')[$key]) {
            $config = registry::get('config');
            $config[$key] = $this->model('system_config')->getByField('config_key', $key)['config_value'];
            registry::remove('config');
            registry::set('config', $key);
            return $config[$key];
        } else {
            return $value;
        }
    }

    protected function getLocale($table, $key)
    {
        $row = array(
            'language' => registry::get('language'),
            'locale_key' => $key,
            'locale_table' => $table
        );
        return $this->model('locale')->getByFields($row)['locale_value'];
    }

    protected function getAllLocale($table)
    {

    }

    /**
     * @param string $key
     * @param mixed $value
     */

    protected function render($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * @param string $template
     * @return string
     * @throws Exception
     */

    public function fetch($template)
    {
        if(registry::get('common_vars')) {
            $this->render('common_vars', registry::get('common_vars'));
        }
        $template_file = TEMPLATE_DIR . $template . '.php';
        if(!file_exists($template_file)) {
            throw new Exception('cannot find template in ' . $template_file);
        }
        foreach($this->vars as $k => $v) {
            $$k = $v;
        }
        ob_start();
        @require($template_file);
        return ob_get_clean();
    }
}