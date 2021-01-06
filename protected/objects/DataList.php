<?php
class DataList
{
    private $objects = [];
    public function __construct(array $data_arr = [], string $key_property = null)
    {
        if($data_arr) {
            foreach ($data_arr as $item) {
                $this->add($item, $key_property);
            }
        }
    }

    public function getByIndex($index)
    {
        return isset($this->objects[$index]) ? $this->objects[$index] : null;
    }

    public function geyByProperty(string $property, $value)
    {
        foreach ($this->objects as $object) {
            $method = 'get' . ucfirst($property);
            if(method_exists($object, $method)) {
                if($object->$method() == $value) {
                    return $object;
                }
            } else {
                throw new Exception('Getter for property ' . $property . ' not exists');
            }
        }
        return null;
    }

    public function getList() : array
    {
        return $this->objects;
    }

    public function add(object $object, string $key_property = null) : void
    {
        if($key_property) {
            $method = 'get' . ucfirst($key_property);
            if(method_exists($object, $method)) {
                $this->objects[$object->$method()] = $object;
                $object->index = $object->$method();
            } else {
                throw new Exception('Getter for property ' . $key_property . ' not exists');
            }
        } else {
            $this->objects[] = $object;
            $object->index = array_keys($this->objects)[count($this->objects) - 1];
        }
    }

    public function isEmpty() : bool
    {
        return empty($this->objects);
    }

    public function count() : int
    {
        return count($this->objects);
    }

    public function remove($index) : bool
    {
        if(isset($this->objects[$index])) {
            unset($this->objects[$index]);
            return true;
        }
        return false;
    }
}