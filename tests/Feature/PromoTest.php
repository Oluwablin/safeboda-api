<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\TestResponse;

class PromoTest extends TestCase
{
    use HasFactory;
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test it cannot create a promo without payload*/
    public function it_does_not_create_a_promo_without_payload()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $promo = Promo::factory()->create();

        $request_data = [];

        $response = $this->json('POST', '/api/v1/promos/create', $request_data, ['Accept' => 'application/json']);
        $response->assertStatus(422);
    }

     /** @test it can create a promo*/
    public function it_can_create_a_promo()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $promo =
            [
                "code" => "Test Code 2",
                "value" => 500,
                "radius" => 100,
                "venue" => "Test Venue",
                "expiry_date" => "2021-09-01",
            ]
        ;

        $response = $this->json('POST', '/api/v1/promos/create', $promo, ['Accept' => 'application/json']);
        $response->assertStatus(201);
        //dd($response);
        $response->assertJson([
            "status" => "success",
            "message" => 'New Promo Code Successfully saved.',
        ]);
    }

    /** @test to see all promos*/
    public function it_can_see_all_promos()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $promo = Promo::factory()->create(
            [
                "code" => "Test Code 2",
                "value" => 500,
                "radius" => 100,
                "venue" => "Test Venue",
            ]
        );

        $response = $this->get('/api/v1/promos/', ['Accept' => 'application/json']);
        $response->assertStatus(200);
    }
}
