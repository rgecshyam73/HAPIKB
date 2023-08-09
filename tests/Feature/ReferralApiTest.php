<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReferralApiTest extends TestCase
{
    /** @test */
    public function it_can_show_referral()
    {
        Log::channel(config('logging.default_test'))->info('== Show Referral ==');
        $response = $this->post(route('referral.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'start_date' => date('Y-m-d', strtotime("-2 days")),
            'end_date' => date('Y-m-d'),
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2'.date('Y-m-d', strtotime("-2 days")).date('Y-m-d').'m123456'),
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Referral ==');
    }

    /** @test */
    public function it_can_show_paid_bonus_referral()
    {
        Log::channel(config('logging.default_test'))->info('== Show Bonus Referral ==');
        $response = $this->post(route('referral.bonus.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'start_date' => date('Y-m-d', strtotime("-2 days")),
            'end_date' => date('Y-m-d'),
            'status' => 1,
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2'.date('Y-m-d', strtotime("-2 days")).date('Y-m-d').'1m123456'),
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Bonus Referral ==');
    }

    /** @test */
    public function it_can_show_unpaid_bonus_referral()
    {
        Log::channel(config('logging.default_test'))->info('== Show Bonus Referral ==');
        $response = $this->post(route('referral.bonus.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'start_date' => date('Y-m-d', strtotime("-2 days")),
            'end_date' => date('Y-m-d'),
            'status' => 0,
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2'.date('Y-m-d', strtotime("-2 days")).date('Y-m-d').'0m123456'),
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Bonus Referral ==');
    }

    /** @test */
    public function it_can_show_daily_referral()
    {
        Log::channel(config('logging.default_test'))->info('== Show Daily Referral ==');
        $response = $this->post(route('referral.daily.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'start_date' => date('Y-m-d', strtotime("-2 days")),
            'end_date' => date('Y-m-d'),
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2'.date('Y-m-d', strtotime("-2 days")).date('Y-m-d').'m123456'),
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Daily Referral ==');
    }

    /** @test */
    public function it_can_show_downline()
    {
        Log::channel(config('logging.default_test'))->info('== Show Downline ==');
        $response = $this->post(route('downline.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2m123456'),
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Downline ==');
    }

    /** @test */
    public function it_can_show_turnover()
    {
        Log::channel(config('logging.default_test'))->info('== Show Turnover ==');
        $response = $this->post(route('turnover.v2'), [
            'operatorid' => 10066,
            'username' => 'NGA_ANAKEMAS2',
            'start_date' => date('Y-m-d', strtotime("-2 days")),
            'end_date' => date('Y-m-d'),
            'hash' => hash('sha256', '10066NGA_ANAKEMAS2'.date('Y-m-d', strtotime("-2 days")).date('Y-m-d').'m123456'),
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Turnover ==');
    }
}
