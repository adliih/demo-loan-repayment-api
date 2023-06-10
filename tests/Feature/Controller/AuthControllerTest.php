<?php

namespace Tests\Feature\Controller;

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
    $password = $this->faker->password();
    $response = $this->post(route("auth.register"), [
      'email' => $this->faker->email(),
      'password' => $password,
      'password_confirmation' => $password,
      'name' => $this->faker->name(),
      'role' => $this->faker->randomElement(['customer', 'admin']),
    ]);

    $response->assertJsonStructure(['access_token']);
  }

  public function testRegisterUsingSameEmailShouldBeBadRequest(): void
  {
    $password = $this->faker->password();


    $password = $this->faker->password();
    $user = User::factory()->create([
      'email' => $this->faker->email(),
      'password' => $password,
    ]);


    $response = $this->post(route("auth.register"), [
      'email' => $user->email,
      'password' => $password,
      'password_confirmation' => $password,
      'name' => $this->faker->name(),
      'role' => $this->faker->randomElement(['customer', 'admin']),
    ]);

    $response->assertFound();
  }

  public function testLoginShouldReturnAccessToken(): void
  {
    $password = $this->faker->password();
    $user = User::factory()->create([
      'password' => $password,
    ]);

    $response = $this->post(route("auth.login"), [
      'email' => $user->email,
      'password' => $password,
      'password_confirmation' => $password,
      'name' => $this->faker->name(),
      'role' => $this->faker->randomElement(['customer', 'admin']),
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
      'password' => $password,
      'password_confirmation' => $password,
      'name' => $this->faker->name(),
      'role' => $this->faker->randomElement(['customer', 'admin']),
    ]);

    $response->assertBadRequest();
  }
}