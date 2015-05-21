<?php
namespace t0t1\mysfw\frame\contract;

interface resource_iterator{
    public function wrap($resource,$operator_type);
    public function count();
}
