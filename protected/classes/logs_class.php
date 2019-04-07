<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 06.07.18
 * Time: 0:21
 */
class logs_class extends staticBase
{
    public static function getLogs() {
        $tmp = tools_class::readFolder(PUBLIC_DIR . 'tmp/logs/');
        $logs = [];
        $logs['folders'] = [];
        $logs['logs'] = [];
        foreach ($tmp as $item) {
            if($item == '.htaccess') {
                continue;
            }
            if(!strpos($item, '.')) {
                $folder_logs = [];
                foreach (tools_class::readFolder(PUBLIC_DIR . 'tmp/logs/' . $item) as $folder_log) {
                    if($folder_log == '.htaccess') {
                        continue;
                    }
                    $folder_logs[] = $folder_log;
                }
                $logs['folders'][] = [
                    'name' => $item,
                    'logs' => $folder_logs
                ];
            } else {
                $logs['logs'][] = $item;
            }
        }
        return $logs;
    }

    public static function cleanLogs()
    {
        $logs = self::getLogs();
        foreach ($logs['folders'] as $folder) {
            if($folder['logs']) {
                foreach ($folder['logs'] as $log) {
                    $log = preg_match('/.*([0-9]{4}-[0-9]{2}-[0-9]{2}).*/', $log, $matches);
                    if($date = $matches[1]) {
                        if(strtotime($date) < time() - 5 * 24 * 3600) {
                            unlink(PUBLIC_DIR  . 'tmp/logs/' . $folder . '/' . $log);
                        }
                    }
                }
            }
        }
    }

    public static function write($file, $log, $mode = 'a+', $json = true)
    {
        self::writeLog($file, $log, $mode, $json);
    }

    public static function date($folder, $log, $prefix = null, $mode = 'a+', $json = true)
    {
        $file = $folder . '/' . ($prefix ? $prefix . '_' : '') . gmdate('Y-m-d');
        self::writeLog($file, $log, $mode, $json);
    }
}