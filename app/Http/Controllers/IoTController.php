<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Suhu;
use App\Models\Kelembapan;

class IoTController extends Controller 
{
    // ========== GET DATA (Universal) ==========

    private function get($table, $filter = null, $paginate = false) {
        $query = DB::table($table);
        if ($filter && request($filter)) $query->where($filter, request($filter));
        $query->orderBy('created_at', 'desc');

        if ($paginate) {
            return response()->json(['success' => true, 'data' => $query->paginate(request('per_page', 50))]);
        }
        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function getRuangan() { return $this->get('ruangan'); }
    public function getAlat() { return $this->get('alat', 'id_ruangan'); }
    public function getSensor() { return $this->get('sensor', 'id_alat'); }
    public function getSuhu() { return $this->get('suhu', 'id_sensor', true); }
    public function getKelembapan() { return $this->get('kelembapan', 'id_sensor', true); }
    public function cekSensor() { return $this->get('sensor'); }
    public function cekAlat() { return $this->get('alat'); }
    public function cekRuangan() { return $this->get('ruangan'); }


public function getSensorById($id)
{
    try {
        $sensor = DB::table('sensor')->where('id', $id)->first();

        if (!$sensor) {
            return response()->json([
                'success' => false,
                'message' => 'Sensor tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sensor
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server',
            'error_detail' => $e->getMessage()
        ], 500);
    }
}

public function getRuanganById($id)
{
    try {
        $ruangan = DB::table('ruangan')->where('id', $id)->first();

        if (!$ruangan) {
            return response()->json(['error' => 'Ruangan tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $ruangan]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Terjadi kesalahan server',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function getAlatById($id)
{
    try {
        $alat = DB::table('alat')->where('id', $id)->first();

        if (!$alat) {
            return response()->json(['error' => 'Alat tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $alat]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Terjadi kesalahan server',
            'message' => $e->getMessage()
        ], 500);
    }
}

    // ========== DASHBOARD ==========

    public function getDashboard() {
        return response()->json(['success' => true, 'data' => DB::select("
            SELECT r.id as ruangan_id, r.nama_ruangan, a.id as alat_id, a.nama_alat, s.id as sensor_id, s.nama_sensor,
                   (SELECT nilai_suhu FROM suhu WHERE id_sensor = s.id ORDER BY created_at DESC LIMIT 1) as suhu_terakhir,
                   (SELECT nilai_kelembapan FROM kelembapan WHERE id_sensor = s.id ORDER BY created_at DESC LIMIT 1) as kelembapan_terakhir,
                   (SELECT created_at FROM suhu WHERE id_sensor = s.id ORDER BY created_at DESC LIMIT 1) as waktu_update
            FROM ruangan r LEFT JOIN alat a ON a.id_ruangan = r.id LEFT JOIN sensor s ON s.id_alat = a.id
            ORDER BY r.id, a.id, s.id
        ")]);
    }

    // ========== STATISTIK & ANALYTICS ==========

    public function getStatistik() {
        $stats = [
            'suhu' => DB::table('suhu')->selectRaw('
                AVG(nilai_suhu) as rata_rata,
                MAX(nilai_suhu) as tertinggi,
                MIN(nilai_suhu) as terendah,
                COUNT(*) as total_data
            ')->first(),
            'kelembapan' => DB::table('kelembapan')->selectRaw('
                AVG(nilai_kelembapan) as rata_rata,
                MAX(nilai_kelembapan) as tertinggi,
                MIN(nilai_kelembapan) as terendah,
                COUNT(*) as total_data
            ')->first(),
            'sensor_aktif' => DB::table('sensor')->count(),
            'data_hari_ini' => [
                'suhu' => DB::table('suhu')->whereDate('created_at', today())->count(),
                'kelembapan' => DB::table('kelembapan')->whereDate('created_at', today())->count()
            ]
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    public function getStatistikBySensor($id_sensor) {
        $stats = [
            'suhu' => DB::table('suhu')->where('id_sensor', $id_sensor)->selectRaw('
                AVG(nilai_suhu) as rata_rata,
                MAX(nilai_suhu) as tertinggi,
                MIN(nilai_suhu) as terendah,
                COUNT(*) as total_data
            ')->first(),
            'kelembapan' => DB::table('kelembapan')->where('id_sensor', $id_sensor)->selectRaw('
                AVG(nilai_kelembapan) as rata_rata,
                MAX(nilai_kelembapan) as tertinggi,
                MIN(nilai_kelembapan) as terendah,
                COUNT(*) as total_data
            ')->first(),
            'sensor_info' => DB::table('sensor')->find($id_sensor)
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    // ========== FILTER BY DATE RANGE ==========

    public function getSuhuByRange(Request $request) {
        $query = DB::table('suhu');

        if ($request->id_sensor) $query->where('id_sensor', $request->id_sensor);
        if ($request->start_date) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('created_at', '<=', $request->end_date);

        return response()->json(['success' => true, 'data' => $query->orderBy('created_at', 'desc')->paginate(request('per_page', 100))]);
    }

    public function getKelembapanByRange(Request $request) {
        $query = DB::table('kelembapan');

        if ($request->id_sensor) $query->where('id_sensor', $request->id_sensor);
        if ($request->start_date) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('created_at', '<=', $request->end_date);

        return response()->json(['success' => true, 'data' => $query->orderBy('created_at', 'desc')->paginate(request('per_page', 100))]);
    }

    // ========== EXPORT CSV ==========

    public function exportSuhu(Request $request) {
        $query = DB::table('suhu')
            ->join('sensor', 'suhu.id_sensor', '=', 'sensor.id')
            ->select('suhu.*', 'sensor.nama_sensor');

        if ($request->id_sensor) $query->where('id_sensor', $request->id_sensor);
        if ($request->start_date) $query->whereDate('suhu.created_at', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('suhu.created_at', '<=', $request->end_date);

        $data = $query->orderBy('suhu.created_at', 'desc')->get();

        $csv = "ID,Nilai Suhu,ID Sensor,Nama Sensor,Waktu\n";
        foreach ($data as $row) {
            $csv .= "{$row->id},{$row->nilai_suhu},{$row->id_sensor},{$row->nama_sensor},{$row->created_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=\"export_suhu_'.date('Y-m-d').'.csv\"');
    }

    public function exportKelembapan(Request $request) {
        $query = DB::table('kelembapan')
            ->join('sensor', 'kelembapan.id_sensor', '=', 'sensor.id')
            ->select('kelembapan.*', 'sensor.nama_sensor');

        if ($request->id_sensor) $query->where('id_sensor', $request->id_sensor);
        if ($request->start_date) $query->whereDate('kelembapan.created_at', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('kelembapan.created_at', '<=', $request->end_date);

        $data = $query->orderBy('kelembapan.created_at', 'desc')->get();

        $csv = "ID,Nilai Kelembapan,ID Sensor,Nama Sensor,Waktu\n";
        foreach ($data as $row) {
            $csv .= "{$row->id},{$row->nilai_kelembapan},{$row->id_sensor},{$row->nama_sensor},{$row->created_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=\"export_kelembapan_'.date('Y-m-d').'.csv\"');
    }

    // ========== POST IoT DATA ==========

    private function save($model, $field, $type, $request) {
        if (!DB::table('sensor')->where('id', $request->id_sensor)->exists())
            return response()->json(['error' => 'Sensor tidak ditemukan'], 404);

        $request->validate([$field => 'required|numeric', 'id_sensor' => 'required|integer']);

        return response()->json([
            'success' => true,
            'message' => "Data $type tersimpan",
            'data' => $model::create($request->only($field, 'id_sensor'))
        ], 201);
    }

    public function simpanSuhu(Request $r) { return $this->save(Suhu::class, 'nilai_suhu', 'suhu', $r); }
    public function simpanKelembapan(Request $r) { return $this->save(Kelembapan::class, 'nilai_kelembapan', 'kelembapan', $r); }

    // ========== POST MASTER DATA ==========

    private function create($table, $rules, $request) {
        $data = $request->validate($rules);
        $id = DB::table($table)->insertGetId(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        return response()->json(['success' => true, 'message' => ucfirst($table) . ' ditambahkan', 'data' => DB::table($table)->find($id)], 201);
    }

    public function simpanRuangan(Request $r) { return $this->create('ruangan', ['nama_ruangan' => 'required|string|max:100'], $r); }
    public function simpanAlat(Request $r) { return $this->create('alat', ['nama_alat' => 'required|string|max:100', 'id_ruangan' => 'required|exists:ruangan,id'], $r); }
    public function simpanSensor(Request $r) { return $this->create('sensor', ['nama_sensor' => 'required|string|max:100', 'id_alat' => 'required|exists:alat,id'], $r); }

    // ========== UPDATE DATA ==========

    private function update($table, $id, $rules, $request) {
        if (!DB::table($table)->where('id', $id)->exists())
            return response()->json(['error' => ucfirst($table) . ' tidak ditemukan'], 404);

        $data = $request->validate($rules);
        DB::table($table)->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        return response()->json(['success' => true, 'message' => ucfirst($table) . ' diupdate', 'data' => DB::table($table)->find($id)]);
    }

    public function updateRuangan(Request $r, $id) { return $this->update('ruangan', $id, ['nama_ruangan' => 'required|string|max:100'], $r); }
    public function updateAlat(Request $r, $id) { return $this->update('alat', $id, ['nama_alat' => 'required|string|max:100', 'id_ruangan' => 'required|exists:ruangan,id'], $r); }
    public function updateSensor(Request $r, $id) { return $this->update('sensor', $id, ['nama_sensor' => 'required|string|max:100', 'id_alat' => 'required|exists:alat,id'], $r); }

    // ========== DELETE DATA ==========

    private function delete($table, $id) {
        if (!DB::table($table)->where('id', $id)->exists())
            return response()->json(['error' => ucfirst($table) . ' tidak ditemukan'], 404);

        DB::table($table)->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => ucfirst($table) . ' dihapus']);
    }

    public function deleteRuangan($id) { return $this->delete('ruangan', $id); }
    public function deleteAlat($id) { return $this->delete('alat', $id); }
    public function deleteSensor($id) { return $this->delete('sensor', $id); }
    public function deleteSuhu($id) { return $this->delete('suhu', $id); }
    public function deleteKelembapan($id) { return $this->delete('kelembapan', $id); }

    // ========== UTILITY ==========

    public function seedTestData() {
        if (DB::table('sensor')->exists()) return response()->json(['message' => 'Data sudah ada']);

        foreach ([
            ['ruangan', ['id' => 1, 'nama_ruangan' => 'Ruang Server', 'created_at' => now(), 'updated_at' => now()]],
            ['alat', ['id' => 1, 'nama_alat' => 'ESP32 Board 1', 'id_ruangan' => 1, 'created_at' => now(), 'updated_at' => now()]],
            ['sensor', ['id' => 1, 'nama_sensor' => 'DHT22 Sensor 1', 'id_alat' => 1, 'created_at' => now(), 'updated_at' => now()]]
        ] as [$t, $d]) DB::table($t)->insert($d);

        return response()->json(['success' => true, 'message' => 'Data test dibuat']);
    }

    public function tes() { return response()->json(['ok' => true, 'message' => 'API running', 'time' => now(), 'version' => '2.0']); }
}
    