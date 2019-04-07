<?php
/**
 * Created by PhpStorm.
 * User: enovichkov
 * Date: 26.05.2015
 * Time: 10:10
 */
class tools_class extends base
{
    private static $t;
    private static $rates;
    private static $modules = [];
    private static $maths;
    private static $image;

    public static $months_rus = array(
        '01' => 'Января',
        '02' => 'Февраля',
        '03' => 'Марта',
        '04' => 'Апреля',
        '05' => 'Мая',
        '06' => 'Июня',
        '07' => 'Июля',
        '08' => 'Августа',
        '09' => 'Сентября',
        '10' => 'Октября',
        '11' => 'Ноября',
        '12' => 'Декабря',
    );

    public static function gmDate()
    {
        return gmdate('Y-m-d H:i:s');
    }

    public static function maths()
    {
        if(null === self::$maths) {
            self::$maths = new maths_class();
        }
        return self::$maths;
    }

    public static function getTestData($key, $params = [])
    {
        require_once PROTECTED_DIR . 'tests/data/api_responses.php';
        $data = data::get($key, $params);
        if($data) {
            return $data;
        }
        return false;
    }

    public static function image()
    {
        if(null === self::$image) {
            require_once PROTECTED_DIR . 'vendor/autoload.php';
            self::$image = new \claviska\SimpleImage();
        }
        return self::$image;
    }

    public static function tbot()
    {
        if(self::$t === null) {
            require_once PROTECTED_DIR . 'vendor/autoload.php';
            self::$t = new Telegram\Bot\Api(WINKL_BOT);
        }
        return self::$t;
    }

    public static function transliterate($st) {
        $st = strtr($st,
            "абвгдежзийклмнопрстуфыэАБВГДЕЖЗИЙКЛМНОПРСТУФЫЭ",
            "abvgdegziyklmnoprstufieABVGDEGZIYKLMNOPRSTUFIE"
        );
        $st = strtr($st, array(
            'ё'=>"yo",    'х'=>"h",  'ц'=>"ts",  'ч'=>"ch", 'ш'=>"sh",
            'щ'=>"shch",  'ъ'=>'',   'ь'=>'',    'ю'=>"yu", 'я'=>"ya",
            'Ё'=>"Yo",    'Х'=>"H",  'Ц'=>"Ts",  'Ч'=>"Ch", 'Ш'=>"Sh",
            'Щ'=>"Shch",  'Ъ'=>'',   'Ь'=>'',    'Ю'=>"Yu", 'Я'=>"Ya",
        ));
        return $st;
    }

    public static function translit($s) {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        $s = preg_replace("/-+/", '-', $s);
        return $s; // возвращаем результат
    }

    public static function divideText($text, $limit)
    {
        $sentences = explode('.', $text);
        $length = 0;
        $count = 0;
        $parts = [];
        foreach ($sentences as $sentence) {
            $length += strlen($sentence);
            echo $length;
            if($length >= $limit) {
                $count ++;
                $length = strlen($sentence);
            }
            $parts[$count][] = $sentence;
        }
        $text = [];
        foreach ($parts as $part) {
            $text[] = implode('. ', $part);
        }
        return $text;
    }

    public static function module($module_name)
    {
        if(null === self::$modules[$module_name]) {
            $module_class = $module_name . '_module';
            self::$modules[$module_name] = new $module_class();
        }
        return self::$modules[$module_name];
    }

    /**
     * @param string $date
     * @param string $timezone_from
     * @param string $timezone_to
     * @param string $format
     * @return string
     */

    public static function convertDateTime($date, $timezone_from, $timezone_to = 'UTC', $format = 'Y-m-d H:i:s')
    {
        $obj = new DateTime($date, new DateTimeZone($timezone_from));
        $obj->setTimezone(new DateTimeZone($timezone_to));
        return $obj->format($format);
    }

    /**
     * @param $sum
     * @param $currency
     * @return bool|mixed
     */

    public static function convertToDollars($sum, $currency)
    {
        if(self::$rates === null) {
            $rates = [];
            foreach (self::model('currency_rates')->getAll() as $item) {
                $rates[$item['currency_code']] = $item['rate'];
            }
            self::$rates = $rates;
        }
        if($currency != 'USD') {
            $rate = self::$rates[$currency];
            if(!$rate) {
                return false;
            }
            $sum /= $rate;
        }
        return $sum;
    }

    /**
     * @param $sum
     * @param $currency
     * @return bool|mixed
     */

    public static function convertFromDollars($sum, $currency)
    {
        if(self::$rates === null) {
            $rates = [];
            foreach (self::model('currency_rates')->getAll() as $item) {
                $rates[$item['currency_code']] = $item['rate'];
            }
            self::$rates = $rates;
        }
        if($currency != 'USD') {
            $rate = self::$rates[$currency];
            if(!$rate) {
                return false;
            }
            $sum *= $rate;
        }
        return $sum;
    }

    public static function trafficBack()
    {

    }

    public static function sendAll($message)
    {
        if(!DEVELOPER_MODE && !$_GET['test']) {
            foreach (self::model('bot_chats')->getAll() as $item) {
                self::tbot()->sendMessage([
                    'chat_id' => $item['chat_id'],
                    'text' => $message
                ]);
            }
        } else {
            $chat_id = self::model('bot_chats')->getByField('user_id', 1)['chat_id'];
            self::tbot()->sendMessage([
                'chat_id' => $chat_id,
                'text' => $message
            ]);
        }

    }

    /**
     * @param string $subject
     * @param string $message
     * @param string $to
     * @param string $from
     * @param string string $name
     * @return bool
     * @throws Exception
     * @throws phpmailerException
     */

    public static function mail($subject, $message, $to, $from = 'info@' . DOMAIN, $name = 'Client')
    {
        require_once LIBS_DIR.'phpmailer/class.phpmailer.php';
        $mail = new PHPMailer();
        $mail->SetFrom($from, DOMAIN);
        $mail->AddAddress($to, $name);
        $mail->Subject = $subject;
        $mail->MsgHTML($message);
        return $mail->Send();
    }

    public static function formatTime($seconds)
    {
        $sec = $seconds % 60;
        $sec = $sec < 10 ? '0' . $sec : $sec;
        $seconds = floor($seconds / 60);
        $min = $seconds % 60;
        $min = $min < 10 ? '0' . $min : $min;
        $hours = floor($seconds / 60);
        $hours = $hours < 10 ? '0' . $hours : $hours;
        return $hours . ':' . $min . ':' . $sec;
    }

    public static function checkImgUrl($url)
    {
        $headers = @get_headers($url);
        return strpos($headers[0], '200');
    }

    public function simpleHtml()
    {
    }

    public static function cropContent($html, $length)
    {
        $out = '';
        $arr = preg_split('/(<.+?>|&#?\\w+;)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        $tagStack = array();

        for($i = 0, $l = 0; $i < count($arr); $i++) {
            if( $i & 1 ) {
                if( substr($arr[$i], 0, 2) == '</' or substr($arr[$i], 0, 2) == '[/') {
                    array_pop($tagStack);
                } elseif( $arr[$i][0] == '&' ) {
                    $l++;
                } elseif( substr($arr[$i], -2) != '/>' or substr($arr[$i], -2) != '/]') {
                    array_push($tagStack, $arr[$i]);
                }

                $out .= $arr[$i];
            } elseif( substr($arr[$i], -2) != '/>' ) {
                if( ($l += strlen($arr[$i])) >= $length ) {
                    $out .= substr($arr[$i], 0, $length - $l + strlen($arr[$i]));
                    break;
                } else {
                    $out .= $arr[$i];
                }
            }
        }
        $x = false;
        while( ($tag = array_pop($tagStack)) !== NULL ) {
            $out .= (!$x ? ' <span class="read_all">read more...</span>' : '') . '</' . strtok(substr($tag, 1), " \t>") . '>';
            $x = true;
        }

        return $out;
    }

    public static function readFolder($folder)
    {
        $list = [];
        if ($dir = opendir($folder))  {
            while (false !== ($file = readdir($dir))) {
                if ($file == "." || $file == "..") continue;
                $list[] = $file;
            }
            closedir($dir);
        }
        return $list;
    }

    /**
     * @return int
     */

    public static function startTime()
    {
        $micro_time = microtime();
        $micro_time = explode(" ",$micro_time);
        $micro_time = $micro_time[1] + $micro_time[0];
        return $micro_time;
    }

    /**
     * @param int $start_time
     * @return float
     */

    public static function getTime($start_time)
    {
        $micro_time = microtime();
        $micro_time = explode(" ",$micro_time);
        $micro_time = $micro_time[1] + $micro_time[0];
        $tend = $micro_time;
        $total_time = ($tend - $start_time);
        $total_time = round($total_time, 4);
        return $total_time;
    }

    public static function detectDevice()
    {
        require_once PROTECTED_DIR . 'vendor/autoload.php';
        $device_api = new Mobile_Detect();
        if($device_api->isMobile()) {
            return 'mobile';
        }
        if($device_api->isTablet()) {
            return 'tablet';
        }
        return 'desktop';
    }

    public static function pdf($mode = 'BLANK', $format = 'A4', $default_font_size = 0, $default_font= 0, $margin_left = 15, $margin_right = 15, $margin_top = 16, $margin_bottom = 16, $margin_header = 9, $margin_footer = 9 )
    {
        require_once(PROTECTED_DIR . 'libs/mpdf60' . DS . 'mpdf.php');
        $mpdf = new mPDF($mode, $format, $default_font_size, $default_font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer);
        return $mpdf;
    }


}