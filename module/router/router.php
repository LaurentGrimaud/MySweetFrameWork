<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

class router extends mysfw\frame\dna{

    public function redirect(
        $route
    ){
        $url = ($route instanceof route)?$route->build_url():$route;
        $this->report_info('redirecting to ' . $url);
        $response = $this->get_popper()->pop('http_response');
        $response->set('status_code','301');
        exit($response->set_http_response_header('Location',$url)->reveal());
    }

    public function raise($status_code){
        $this->report_info('exiting with http status ' . $status_code);
        $response = $this->get_popper()->pop('http_response');
        exit($response->set('status_code',$status_code)->reveal());
    }
}
