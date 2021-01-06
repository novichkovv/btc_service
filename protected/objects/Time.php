<?php
class Time
{
    private $time;
    private $time_zone;
    public function __construct($time = null, $time_zone = null)
    {
        if($time_zone === null) {
            $time_zone = date_default_timezone_get();
        }
        if(strtolower($time_zone) === 'utc' || strtolower($time_zone) === 'gmt') {
            $this->time_zone = 'UTC';
            if(is_numeric($time)) {
                $this->time = $time;
            } else if(is_string($time)) {
                $this->time = strtotime($time);
            } else if($time === null){
                $this->time = time();
            }
        } else {
            $arr = explode('/', $time_zone);
            $time_zone = ucfirst($arr[0]);
            if(isset($arr[1])) {
                $time_zone .= '/' . ucfirst($arr[1]);
            }
            $this->time_zone = $time_zone;
            if(is_numeric($time)) {
                $this->time = strtotime(tools_class::convertDateTime(date('Y-m-d H:i:s', $time), $time_zone, 'UTC'));
            } else if(is_string($time)) {
                $this->time = strtotime(tools_class::convertDateTime($time, $time_zone, 'UTC'));
            } else if($time === null){
                $this->time = strtotime(tools_class::gmDate());
            }
        }
    }

    public function getDateTime($time_zone = null, $format = null) : string
    {
        if(null === $format) {
            $format = 'Y-m-d H:i:s';
        }
        if($time_zone) {
            if($time_zone === 'UTC') {
                return date($format, $this->time);
            } else {
                return tools_class::convertDateTime(date('Y-m-d H:i:s', $this->time), 'UTC', $time_zone);
            }
        } else {
            if($this->time_zone === 'UTC') {
                return date($format, $this->time);
            } else {

                return tools_class::convertDateTime(date('Y-m-d H:i:s', $this->time), 'UTC', $this->time_zone);
            }
        }
    }

    public function getDate($time_zone = null, $format = null) : string
    {
        if(null === $format) {
            $format = 'Y-m-d';
        }
        if($time_zone) {
            if($time_zone === 'UTC') {
                return date($format, $this->time);
            } else {
                return tools_class::convertDateTime(date('Y-m-d H:i:s', $this->time), 'UTC', $time_zone, 'Y-m-d');
            }
        } else {
            if($this->time_zone === 'UTC') {
                return date($format, $this->time);
            } else {
                return tools_class::convertDateTime(date('Y-m-d H:i:s', $this->time), 'UTC', $this->time_zone, 'Y-m-d');
            }
        }

    }

    public function getGMTDateTime($format = null) : string
    {
        if(null === $format) {
            $format = 'Y-m-d H:i:s';
        }
        return date($format, $this->time);
    }

    public function getGMTDate($format = null) : string
    {
        if(null === $format) {
            $format = 'Y-m-d';
        }
        return date($format, $this->time);
    }

    public function getTimeZone() : string
    {
        return $this->time_zone;
    }

    public function plusDays(int $days)
    {
        $this->time += $days * 24 * 3600;
    }

    public function minusDays(int $days)
    {
        $this->time -= $days * 24 * 3600;
    }

}