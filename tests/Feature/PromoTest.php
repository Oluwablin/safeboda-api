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
    //use RefreshDatabase;
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
}
