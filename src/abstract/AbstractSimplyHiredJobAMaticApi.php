<?php
abstract class AbstractSimplyHiredJobAMaticApi {

    protected function parseDate($date) {
        return date('Y-m-d H:i:s T',strtotime((string) $date));
    }
    
    public function toArray(){
        $class_vars = get_object_vars($this);
        $properties = array();
        foreach ($class_vars as $key => $val) {
            if(!is_object($val)){
                $properties[$key] = $val;
            } elseif(method_exists($val,'toArray')){
                $properties[$key] = $val->toArray();
            }
        }
        return $properties;
    }

}