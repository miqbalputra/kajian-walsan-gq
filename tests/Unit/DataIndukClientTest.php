<?php

namespace Tests\Unit;

use App\Services\DataIndukClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DataIndukClientTest extends TestCase
{
    public function test_it_fetches_the_active_roster_with_the_api_key_and_unit_filter(): void
    {
        config()->set('data-induk.enabled', true);
        config()->set('data-induk.base_url', 'https://data-induk.test/api/v1');
        config()->set('data-induk.api_key', 'mdi_test_key');
        config()->set('data-induk.unit_id', 'unit-kajian');

        Http::fake([
            'https://data-induk.test/api/v1/integrations/students*' => Http::response([
                'data' => [['id' => 'student-uuid', 'nis' => '0001', 'fullName' => 'Santri Uji']],
            ]),
        ]);

        $roster = app(DataIndukClient::class)->activeStudentRoster();

        $this->assertSame('student-uuid', $roster[0]['id']);
        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://data-induk.test/api/v1/integrations/students?unitId=unit-kajian'
                && $request->hasHeader('X-API-Key', 'mdi_test_key');
        });
    }
}
