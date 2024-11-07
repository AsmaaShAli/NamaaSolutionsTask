<?php

namespace Tests\Unit;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_user_controller(): void
    {
        $this->get(route('api.users'))->assertOk();
        $response = $this->get(route('api.users',[
            'provider'   => 'DataProviderX-test',
            'statusCode' => 'authorised',
            'balanceMin' => '100',
            'balanceMax' => '300',
            'currency'   => 'USD',
        ]))->assertOk()
            ->assertJson([
                'status'  => Response::HTTP_OK,
                'success' => true,
                'data'    => [
                    "users" => [
                        0 => [
                            "id" => "d3d29d70-1d25-11e3-8591-034165a3a613",
                            "status" => "authorised",
                            "balance" => 200,
                            "currency" => "USD",
                            "registration_date" => "2018-11-30",
                            "email" => "2018-11-30"
                        ],
                        1 =>  [
                            "id" => "d3d29d70-1d25-11e3-8591-034165a3a613",
                            "status" => "authorised",
                            "balance" => 200,
                            "currency" => "USD",
                            "registration_date" => "2018-11-30",
                            "email" => "2018-11-30",
                        ]
                ]
            ]
        ]);
    }
}
