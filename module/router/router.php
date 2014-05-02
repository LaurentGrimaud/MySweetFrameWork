<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

class router extends mysfw\frame\dna{

    public function redirect(
        $route
    ){
        $url = ($route instanceof route)?$route->build_url():$route;
        $this->report_info('redirecting to ' . $url);
        exit($this->get_popper()->pop('http_response')->set_http_response_header('Location',$url)->reveal());
    }
}
