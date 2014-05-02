<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

class filter extends mysfw\frame\dna{

    public function apply(
        $value,
        array $filters= null
    ){
        if( is_callable($value)) $value = call_user_func($value);
        if( is_array($value)) return $value; //XXX recursively validate array
        if( $filters){
            foreach($filters as $filter){
                if(is_callable($filter)) $value = call_user_func($filter,$value);
            }
        }
        return $value;
    }

    public function filter_email( $email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function filter_string( $string){
        return filter_var($string, FILTER_SANITIZE_STRING);
    }
}
