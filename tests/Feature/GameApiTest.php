<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_show_jackpot()
    {

        Log::channel(config('logging.default_test'))->info('== Show Jackpot ==');
        $response = $this->post(route('jackpot.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'currency' => 'idr',
            'hash' => hash('sha256', '10066idrm123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Jackpot ==');

    }

    /**
     * @test
     */
    public function it_can_show_number_result()
    {
        Log::channel(config('logging.default_test'))->info('== Show Number Result ==');
        $response = $this->post(route('number.result.v2'), [
            'operatorid' => 10066,
            'type_id' => 5,
            'game_id' => 201,
            'room_id' => 1,
            'length' => 10,
            'hash' => hash('sha256', '100665201110m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Number Test ==');
//        $response->dump();
    }

    /**
     * @test
     */
    public function it_can_show_number_details()
    {
        Log::channel(config('logging.default_test'))->info('== Show Number Details Test ==');
        $response = $this->post(route('number.details.v2'), [
            'operatorid' => 10066,
            'type_id' => 5,
            'game_id' => 201,
            'room_id' => 1,
            'period' => 73,
            'hash' => hash('sha256', '10066201731m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);;
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Number Details Test ==');
    }

    /** @test */
    public function it_can_show_market_time()
    {
        Log::channel(config('logging.default_test'))->info('== Show Market Time Test ==');
        $response = $this->post(route('market.time.v2'), [
            'operatorid' => 10066,
            'game_id' => 201,
            'date' => date('Y-m-d'),
            'hash' => hash('sha256', '10066201'. date('Y-m-d') .'m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Market Time Test ==');

    }

    /** @test */
    public function it_can_show_all_player_balance()
    {
        Log::channel(config('logging.default_test'))->info('== Show All Player Balance ==');
        $response = $this->post(route('player.balance.v2'), [
            'operatorid' => 10066,
            'hash' => hash('sha256', '10066m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);;
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END All Player Balance ==');
    }

    /** @test */
    public function it_can_show_web_count_online_player()
    {
        Log::channel(config('logging.default_test'))->info('== Show Count Online Web Player ==');
        $response = $this->post(route('player.count.online.v2'), [
            'operatorid' => 10066,
            'device' => 1, // Web
            'hash' => hash('sha256', '100661m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);;
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Count Online Web Player ==');
    }

    /** @test */
    public function it_can_show_mobile_count_online_player()
    {
        Log::channel(config('logging.default_test'))->info('== Show Count Online Mobile Player ==');
        $response = $this->post(route('player.count.online.v2'), [
            'operatorid' => 10066,
            'device' => 2, // Mobile
            'hash' => hash('sha256', '100662m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);;
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Count Online Mobile Player ==');
    }

    /** @test */
    public function it_can_show_is_online_player()
    {
        Log::channel(config('logging.default_test'))->info('== Show Is Online Player ==');
        $response = $this->post(route('player.is.online.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Show Is Online Player ==');
    }


    /** @test */
    public function it_can_update_player_setting()
    {
        $_SERVER['REMOTE_ADDR']='192.168.0.100';
        Log::channel(config('logging.default_test'))->info('== Update Player Setting ==');
        $response = $this->post(route('player.update.setting.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'language' => 'id',
            'fullname' => 'ANAKEMAS',
            'email' => 'anakemas@gmail.com',
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2idANAKEMASanakemas@gmail.comm123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END UPDATE Player Setting ==');
    }

}
