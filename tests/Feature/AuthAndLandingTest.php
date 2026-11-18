<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('landing page can be rendered successfully', function () {
    $response = $this->get(route('landing'));

    $response->assertOk()
        ->assertSee('Kostly')
        ->assertSee('Kelola Bisnis Kost')
        ->assertSee('Daftar Gratis');
});

test('login page can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('Masuk ke Akun Pemilik')
        ->assertSee('owner@kostly.id');
});

test('register page can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('Buat Akun Pemilik Kost')
        ->assertSee('Nomor WhatsApp');
});

test('owner can login with valid credentials and is redirected to dashboard', function () {
    $response = $this->post(route('login'), [
        'email' => 'owner@kostly.id',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('owner.dashboard'));
    $this->assertAuthenticated();
});

test('owner cannot login with invalid credentials', function () {
    $response = $this->post(route('login'), [
        'email' => 'owner@kostly.id',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('new owner can register and is authenticated to dashboard', function () {
    $response = $this->post(route('register'), [
        'name' => 'Juragan Baru',
        'email' => 'juraganbaru@example.com',
        'phone' => '081234567899',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('owner.dashboard'));
    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'juraganbaru@example.com',
        'name' => 'Juragan Baru',
    ]);
});

test('authenticated user can logout and is redirected to landing page', function () {
    $user = User::where('email', 'owner@kostly.id')->first();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('landing'));
    $this->assertGuest();
});

test('unauthenticated user is redirected to login when visiting dashboard', function () {
    $response = $this->get(route('owner.dashboard'));

    $response->assertRedirect(route('login'));
});
