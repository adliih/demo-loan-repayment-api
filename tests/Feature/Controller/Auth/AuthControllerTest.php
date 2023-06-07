<?php

namespace Feature\Controller\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
  use WithFaker;
  use RefreshDatabase;
  public function testRegisterShouldReturnAccessToken(): void
  {
    $response = $this->post(route("auth.register"), [
      'email' => $this->faker->email(),
      'password' => $this->faker->password(),
    ]);

    $response->assertJsonStructure(['access_token']);
  }

  public function testRegisterUsingSameEmailShouldBeBadRequest(): void
  {
    $response = $this->post(route("auth.register"), [
      'email' => $this->faker->email(),
      'password' => $this->faker->password(),
    ]);

    $response->assertBadRequest();
  }

  public function testLoginShouldReturnAccessToken(): void
  {
    $password = $this->faker->password();
    $user = User::factory()->create([
      'password' => $password,
    ]);

    $response = $this->post(route("auth.login"), [
      'email' => $user->email,
      'password' => $password
    ]);

    $response->assertJsonStructure(['access_token']);
  }

  public function testLoginUsingNonRegisteredEmailShouldReturnBadRequest(): void
  {
    $password = $this->faker->password();
    $user = User::factory()->create([
      'password' => $password,
    ]);

    $response = $this->post(route("auth.login"), [
      'email' => 'different-prefix-' . $user->email,
      'password' => $password
    ]);

    $response->assertBadRequest();
  }
}