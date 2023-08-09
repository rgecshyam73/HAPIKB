<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionApiTest extends TestCase
{
    public function it_can_transfer()
    {
        $response = $this->post(route('transfer.v2'), [
            'username' => 'NGA_ISMAILHKB2',
            'trans_id' => 1849,
            'amount' => 10000,
            'dir' => 0,
            'operatorid' => 10066,
            'currency' => 'IDR',
            'hash' => hash('sha256', '100665201110m123456')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
    }

    /** @test */
    public function it_can_show_transaction_result()
    {
        Log::channel(config('logging.default_test'))->info('== Show Transaction Result ==');
        $response = $this->post(route('transaction.result.v2'), [
            'operatorid' => 10017,
            'version_key' => '280556',
            'hash' => hash('sha256', '10017280556jcd@1234')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Transaction Result ==');
    }

    public function it_can_show_transaction_details()
    {
        Log::info('== Show Transaction Details ==');
        $response = $this->post(route('transaction.details.v2'), [
            'operatorid' => 10017,
            'trans_id' => '1010392',
            'period' => '221',
            'game_id' => '101',
            'hash' => hash('sha256', '100171012211010392jcd@1234')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::debug($response->json());
        Log::info('== END Transaction Details ==');
    }

    /** @test */
    public function it_can_show_invoice_togel()
    {
        Log::channel(config('logging.default_test'))->info('== Show Invoice Togel ==');
        $response = $this->post(route('transaction.invoice.togel.v2'), [
            'operatorid' => 10017,
            'game_id' => 201,
            'period' => 225,
            'hash' => hash('sha256', '10017201225jcd@1234')
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 0,
                'msg' => 'Success',
            ]);
        Log::channel(config('logging.default_test'))->debug($response->json());
        Log::channel(config('logging.default_test'))->info('== END Invoice Togel ==');
    }
}
