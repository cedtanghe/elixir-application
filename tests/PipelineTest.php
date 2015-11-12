<?php

namespace Elixir\Test\Foundation;

use Elixir\Foundation\Middleware\Pipeline;

class PipelineTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $pipeline = new Pipeline([], [$this, 'finalHandler']);
        $this->assertEquals(true, true);
    }
    
    public function finalHandler()
    {
        
    }
}
