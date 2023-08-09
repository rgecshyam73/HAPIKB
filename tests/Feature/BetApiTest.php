<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BetApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_show_bet_details()
    {

        Log::channel(config('logging.default_test'))->info('== Show Bet Details ==');
        $response = $this->post(route('bet.details.v2'), [
            'operatorid' => 10066,
            'start_time' => date('Y-m-d'),
            'end_time' => date('Y-m-d'),
            'hash' => hash('sha256', '10066'.date('Y-m-d').date('Y-m-d').'m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Bet Details ==');
    }

    /** @test */
    public function it_can_show_current_outstanding_bet()
    {
        Log::channel(config('logging.default_test'))->info('== Show Current Outstanding Bet ==');
        $response = $this->post(route('outstanding.bet.v2'), [
            'operatorid' => 10066,
            'game_id' => 201,
            'hash' => hash('sha256', '10066201m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);

        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Current Outstanding Bet ==');
    }

    /** @test */
    public function it_can_show_outstanding_bet_details()
    {
        Log::channel(config('logging.default_test'))->info('== Show Current Outstanding Bet Details ==');
        $response = $this->post(route('outstanding.bet.details.v2'), [
            'operatorid' => 10066,
            'game_id' => 201,
            'subgame_id' => 1,
            'hash' => hash('sha256', '100662011m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);

        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Current Outstanding Bet Details ==');
    }
}
