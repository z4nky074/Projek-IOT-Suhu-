<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LastTwoMonthsDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 10, 5, 12, 0, 0, 'Asia/Jakarta'));

        DB::table('ruangan')->insert([
            'id' => 1,
            'nama_ruangan' => 'Ruang Server',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('alat')->insert([
            'id' => 1,
            'nama_alat' => 'Alat 1',
            'id_ruangan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sensor')->insert([
            [
                'id' => 1,
                'nama_sensor' => 'Sensor 1',
                'id_alat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama_sensor' => 'Sensor 2',
                'id_alat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_returns_only_data_from_the_last_two_months(): void
    {
        DB::table('suhu')->insert([
            [
                'nilai_suhu' => 22.5,
                'id_sensor' => 1,
                'created_at' => '2026-08-05 00:00:00',
                'updated_at' => '2026-08-05 00:00:00',
            ],
            [
                'nilai_suhu' => 99.9,
                'id_sensor' => 1,
                'created_at' => '2026-08-04 23:59:59',
                'updated_at' => '2026-08-04 23:59:59',
            ],
            [
                'nilai_suhu' => 24.5,
                'id_sensor' => 2,
                'created_at' => '2026-09-01 10:00:00',
                'updated_at' => '2026-09-01 10:00:00',
            ],
        ]);

        DB::table('kelembapan')->insert([
            'nilai_kelembapan' => 60,
            'id_sensor' => 1,
            'created_at' => '2026-10-05 11:59:00',
            'updated_at' => '2026-10-05 11:59:00',
        ]);

        $response = $this->getJson('/api/data/dua-bulan-terakhir');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('periode.mulai', '2026-08-05 00:00:00')
            ->assertJsonPath('periode.selesai', '2026-10-05 12:00:00')
            ->assertJsonPath('periode.timezone', 'Asia/Jakarta')
            ->assertJsonPath('total.suhu', 2)
            ->assertJsonPath('total.kelembapan', 1)
            ->assertJsonPath('total.keseluruhan', 3)
            ->assertJsonCount(2, 'data.suhu')
            ->assertJsonCount(1, 'data.kelembapan')
            ->assertJsonMissing(['nilai_suhu' => 99.9]);
    }

    public function test_it_can_filter_the_data_by_sensor(): void
    {
        DB::table('suhu')->insert([
            [
                'nilai_suhu' => 22.5,
                'id_sensor' => 1,
                'created_at' => '2026-09-01 10:00:00',
                'updated_at' => '2026-09-01 10:00:00',
            ],
            [
                'nilai_suhu' => 24.5,
                'id_sensor' => 2,
                'created_at' => '2026-09-01 10:00:00',
                'updated_at' => '2026-09-01 10:00:00',
            ],
        ]);

        $response = $this->getJson('/api/data/dua-bulan-terakhir?id_sensor=1');

        $response
            ->assertOk()
            ->assertJsonPath('filter.id_sensor', 1)
            ->assertJsonPath('total.suhu', 1)
            ->assertJsonCount(1, 'data.suhu')
            ->assertJsonPath('data.suhu.0.id_sensor', 1)
            ->assertJsonPath('data.suhu.0.nama_sensor', 'Sensor 1');
    }
}
