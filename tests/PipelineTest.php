<?php

namespace Elixir\Test\Kernel;

use Elixir\HTTP\ServerRequestFactory;
use Elixir\Kernel\Middleware\Pipeline;
use Elixir\Kernel\Middleware\PipelineMiddleware;
use Elixir\Test\Kernel\CreateResponseMiddleware;
use Elixir\Test\Kernel\WriteResponseMiddleware;
use PHPUnit_Framework_TestCase;

class PipelineTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $pipeline = new Pipeline([
                new CreateResponseMiddleware(),
                new WriteResponseMiddleware(),
                new PipelineMiddleware(new Pipeline([
                        new WriteResponseMiddleware()
                    ]
                )),
                new WriteResponseMiddleware()
            ]
        );
        
        $response = $pipeline->process(ServerRequestFactory::createFromGlobals());
        
        $this->assertInstanceOf('\Elixir\HTTP\Response', $response);
        $this->assertEquals('Response created->write->write->write->finalized!', (string)$response->getBody());
    }
}
