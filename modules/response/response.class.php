<?php
 // XXX WIP
 // example:
 //{{{
 //require '../external/MySweetFW/popper.class.php'; // XXX temp file layout
 //$popper = mysfw_default_popper::itself(__DIR__);  // XXX to be checked
 //require '../includes/configuration.php';          // XXX temp
 //$r = $popper->pop("response");
 //$r->define("response.http_status_code", mysfw_http_response::http_status_code_successful);
 //$v= $popper->pop("view");
 //$v->set('page_title', $c->inform('site_title'));
 //$r->define("response.view",$v);
 //$r->reveal("home");
 //}}}

require_once dirname(__FILE__) . "/http_response.class.php";
class mysfw_response extends mysfw_core implements mysfw_response_interface, mysfw_dna{

    protected $_response;
    

    
    protected function _get_ready() {
        // instantiate a contextualized object depending on current SAPI
        switch(true){
            default:
            case strpos(PHP_SAPI,"apache2"):
                $r= new mysfw_http_response;
            break;
        }
        // XXX
        $r->set_configurator($this->get_configurator());
        $r->get_ready();
        $this->define('response', $r);
    }

    public function reveal($_t) {
        $this->inform('response')->reveal($_t);
    }
}
?>
