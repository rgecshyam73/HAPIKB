<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LobbyApiTest extends TestCase
{
    /** @test */
    public function it_can_go_to_lobby()
    {
        Log::channel(config('logging.default_test'))->info('== Show lobby ==');
        $response = $this->post(route('lobby.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ISMAILHKB2',
            'token' => 'ZvRisa3pNP01OPksSjIp54X1hTQlYZCQGZcHcUKm',
            'language' => 'en',
            'game_id' => 201,
            'home_url' => '',
        ]);

        $response->assertStatus(200);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Lobby ==');
    }


    public function it_can_register()
    {

    }

    /** @test */
    public function it_can_logout_user()
    {
        Log::channel(config('logging.default_test'))->info('== Show logout ==');
        $_SERVER['REMOTE_ADDR']='192.168.0.100';
        $response = $this->withSession([
          'username' => 'NGA_ISMAILHKB2',
          'operatorid' => 10066
        ])->get(route('logout.v2'));

        $response->assertStatus(200);
        Log::channel(config('logging.default_test'))->info('== END Logout ==');
    }

    /** @test */
    public function it_can_return_error()
    {
        Log::channel(config('logging.default_test'))->info('== Show return Error ==');
        $response = $this->get(route('error.v2'));
        $response->assertStatus(444);
        Log::channel(config('logging.default_test'))->info('== END return Error ==');
    }

    /** @test */
    public function it_can_return_test()
    {
        Log::channel(config('logging.default_test'))->info('== Show test ==');
        $response = $this->get(route('test.v2'));
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Test ==');
    }
}
