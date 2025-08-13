<?php

describe('Plants', function () {

    describe('->routes()', function () {
        describe('plants.dashboard()', function () {
            it('renders the plants dashboard page', function () {
                $user = \App\Models\User::factory()->create();
                $response = $this->actingAs($user)->get(route('plants.dashboard'));
                $response->assertStatus(200);
            });
        });
        describe('plants.index()', function () {
            it('renders the plants index page', function () {
                $user = \App\Models\User::factory()->create();
                $response = $this->actingAs($user)->get(route('plants.index'));
                $response->assertStatus(200);
            });
        });
        describe('plants.create()', function () {
            it('renders the plants create page', function () {
                $user = \App\Models\User::factory()->create();
                $response = $this->actingAs($user)->get(route('plants.create'));
                $response->assertStatus(200);
            });
        });
        describe('plants.show()', function () {
            it('renders the plants show page', function () {
                $user = \App\Models\User::factory()->create();
                $plant = \App\Models\Plant::factory()->create();
                $response = $this->actingAs($user)->get(route('plants.show', ['uuid' => $plant->uuid]));
                $response->assertStatus(200);
            });
        });
    });
});
