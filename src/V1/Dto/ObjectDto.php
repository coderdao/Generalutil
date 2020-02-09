<?php
/**
 * Function: 对象 数组 转换, 需要往下自定义
 * Author: YUN
 * Date: 2018/5/31 下午2:14
 * Description:
 */

namespace Abo\Generalutil\V1\Dto;


class ObjectDto
{
    /**
     * 转对象
     * @param $data
     * @throws \ReflectionException
     */
    public function fromArray($data)
    {
        $class_name = get_class($this);
        $class = new \ReflectionClass($class_name);
        $methods = $class->getMethods();

        foreach ($methods as $value) {
            if($value->class == $class_name) {
                $methodAccess = new \ReflectionMethod($class_name,$value->name);
                if($methodAccess->isPublic()) {
                    $func_name = $this->uncamelize($value->name);
                    if (strpos($func_name, 'set_') === 0 && strlen($func_name)> 4 ) {
                        $set_func = $value->name;
                        $prop_name = substr($func_name, 4);

                        if (isset($data[$prop_name])) {
                            $this->$set_func($data[$prop_name]);
                        }
                    }
                }
            }
        }
    }

    /**
     * 转数组
     * @param bool $ignore_null 是否忽略NULL值
     * @return array
     */
    public function toArray($ignore_null=true)
    {
        $class_name = get_class($this);
        $class = new \ReflectionClass($class_name);
        $methods = $class->getMethods();

        $data =[];
        foreach ($methods as $value) {
            if($value->class == $class_name) {
                $methodAccess = new \ReflectionMethod($class_name,$value->name);
                if($methodAccess->isPublic()) {
                    $func_name = $this->uncamelize($value->name);
                    if (strpos($func_name, 'get_') === 0 && strlen($func_name)> 4 ) {
                        $get_func = $value->name;
                        $prop_name = substr($func_name, 4);
                        $v = $this->$get_func();

                        if (is_null($v)) {
                            if ( $ignore_null == false) {
                                $data[$prop_name] = null;
                            }
                        } else {
                            $data[$prop_name] = $v;
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function uncamelize($camelCaps,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}