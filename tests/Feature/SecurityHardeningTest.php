<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    public function test_web_responses_include_security_headers(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Request-ID');
    }

    public function test_hermes_secret_is_not_accepted_from_request_body(): void
    {
        config(['hermes.secret' => 'test-hermes-secret']);

        $this->postJson('/hermes-agent', [
            'secret' => 'test-hermes-secret',
            'action' => 'ping',
        ])->assertUnauthorized();
    }

    public function test_hermes_accepts_a_secret_header(): void
    {
        config(['hermes.secret' => 'test-hermes-secret']);

        $this->withHeader('X-Hermes-Secret', 'test-hermes-secret')
            ->postJson('/hermes-agent', ['action' => 'ping'])
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}
