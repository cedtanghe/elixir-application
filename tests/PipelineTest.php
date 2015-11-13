<?php

namespace Elixir\Test\Foundation;

use Elixir\Foundation\Middleware\Pipeline;
use Elixir\HTTP\ServerRequestFactory;
use Elixir\Test\Foundation\CreateResponseMiddleware;
use Elixir\Test\Foundation\WriteResponseMiddleware;
use PHPUnit_Framework_TestCase;

class PipelineTest extends PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $pipeline = new Pipeline([
                new CreateResponseMiddleware(),
                new WriteResponseMiddleware()
            ], 
            function($request, $response = null){ return $this->finalHandler($request, $response); }
        );
        
        $response = $pipeline->run(ServerRequestFactory::createFromGlobals());
        
        $this->assertInstanceOf('\Elixir\HTTP\Response', $response);
        $this->assertEquals('Response created.Write.Finalized.', (string)$response->getBody());
    }
    
    protected function finalHandler($request, $response = null)
    {
        $response->getBody()->write('Finalized.');
        return $response;
    }
}
