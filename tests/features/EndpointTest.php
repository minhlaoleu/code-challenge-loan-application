<?php declare(strict_types=1);

namespace Tests\features;

use Tests\TestCase;

class EndpointTest extends TestCase
{

    /**
     * Test any undefined routes, should return 404 error
     */
    public function testUndefinedPathShouldReturn404(): void
    {
        $request = $this->get('');

        $request->response->assertJson([
           'error' => true
        ])->assertStatus(404);
    }

    public function testGetVersion(): void
    {
        $this->get(route('v1.version'));

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function testReturnPong(): void
    {
        $request = $this->get(route('v1.ping'));

        $request->response->assertSee('pong')->assertStatus(200);
    }
}
