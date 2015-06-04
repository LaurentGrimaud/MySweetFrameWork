<?php
namespace t0t1\mysfw\frame\contract;

 /**
  * Interface that should be implemented to iterate over a resource
  * It is designed to be consumed using foreach or while constructs.
  */
interface resource_iterator extends \Iterator{

 /**
  * Wrap a resource
  */
    public function wrap($resource);

 /**
  * Count results referenced by the resource wrapped
  */
    public function count($use_limit);

 /**
  * Fetch next resource result set or current one if resource has been exhausted.
  */
    public function shift();
}
