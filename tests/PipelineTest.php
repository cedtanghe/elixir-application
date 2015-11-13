<?php

namespace Elixir\Test\Foundation;

use Elixir\Foundation\Middleware\Pipeline;
use Elixir\Foundation\Middleware\PipelineMiddleware;
use Elixir\HTTP\ServerRequestFactory;
use Elixir\Test\Foundation\CreateResponseMiddleware;
use Elixir\Test\Foundation\WriteResponseMiddleware;
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
