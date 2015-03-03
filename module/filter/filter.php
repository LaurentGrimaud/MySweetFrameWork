<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

class filter extends mysfw\frame\dna{

    public function apply(
        $value,
        array $filters= null
    ){
        if( $value instanceof \Closure and is_callable($value)) $value = call_user_func($value);
        if( is_array($value)) return $value; //XXX recursively validate array
        if( $filters){
            foreach($filters as $filter){
                if(is_callable($filter)){
                    $value = call_user_func($filter,$value);
                } else if( method_exists($this,$filter)){
                    $value = $this->$filter($value);
                }
            }
        }
        return $value;
    }

    public function filter_email( $email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function filter_url( $url){
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function not_empty( $mixed){
        if( is_array($mixed)){
            $no_empty_valued_array = array();
            foreach( $mixed as $value) if($value) $no_empty_valued_array[]= $value;
            return $no_empty_valued_array;
        }
        return ( $mixed)?:false;
    }

    public function filter_string( $string){
        return filter_var($string, FILTER_SANITIZE_STRING);
    }
    public function filter_date_little_endian( $date){
        return date('d/m/Y',strtotime($date));
    }
}
