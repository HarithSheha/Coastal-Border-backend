<?php

namespace Database\Seeders;

use App\Models\Zone;
use App\Models\Sensor;
use App\Models\Report;
use App\Models\SensorReading;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Zones ──────────────────────────────────────────────────────────
        $zones = collect([
            ['name' => 'North Perimeter',     'description' => 'Northern coastal border zone with high foot traffic.',      'type' => 'restricted', 'status' => 'active',   'color' => '#ef4444', 'x_percent' => 5,  'y_percent' => 5,  'width_percent' => 30, 'height_percent' => 25],
            ['name' => 'East Checkpoint',     'description' => 'Primary vehicle inspection and personnel checkpoint.',       'type' => 'checkpoint', 'status' => 'active',   'color' => '#3b82f6', 'x_percent' => 60, 'y_percent' => 10, 'width_percent' => 35, 'height_percent' => 20],
            ['name' => 'Coastal Danger Zone', 'description' => 'Unstable cliff terrain. No unauthorised access permitted.', 'type' => 'danger',     'status' => 'active',   'color' => '#dc2626', 'x_percent' => 10, 'y_percent' => 55, 'width_percent' => 25, 'height_percent' => 35],
            ['name' => 'South Caution Area',  'description' => 'Elevated patrol activity. Restricted after 18:00.',         'type' => 'caution',    'status' => 'breach',   'color' => '#f59e0b', 'x_percent' => 40, 'y_percent' => 60, 'width_percent' => 30, 'height_percent' => 30],
            ['name' => 'West Restricted',     'description' => 'Weapons storage proximity zone. Maximum security.',         'type' => 'restricted', 'status' => 'inactive', 'color' => '#7c3aed', 'x_percent' => 70, 'y_percent' => 50, 'width_percent' => 25, 'height_percent' => 40],
        ])->map(fn ($z) => Zone::create($z));

        [$north, $east, $coastal, $south, $west] = $zones;

        // ── Sensors ────────────────────────────────────────────────────────
        $sensors = collect([
            ['name' => 'Motion Sensor N1',  'zone_id' => $north->id,   'type' => 'motion',    'status' => 'online',  'battery_level' => 87, 'x_percent' => 12, 'y_percent' => 15],
            ['name' => 'Thermal Cam E1',    'zone_id' => $east->id,    'type' => 'thermal',   'status' => 'alert',   'battery_level' => 62, 'x_percent' => 68, 'y_percent' => 18],
            ['name' => 'Camera C1',         'zone_id' => $coastal->id, 'type' => 'camera',    'status' => 'online',  'battery_level' => 95, 'x_percent' => 18, 'y_percent' => 65],
            ['name' => 'Vibration S1',      'zone_id' => $south->id,   'type' => 'vibration', 'status' => 'alert',   'battery_level' => 34, 'x_percent' => 48, 'y_percent' => 72],
            ['name' => 'Gas Detector W1',   'zone_id' => $west->id,    'type' => 'gas',       'status' => 'offline', 'battery_level' => 10, 'x_percent' => 78, 'y_percent' => 58],
            ['name' => 'Smoke Sensor N2',   'zone_id' => $north->id,   'type' => 'smoke',     'status' => 'online',  'battery_level' => 78, 'x_percent' => 22, 'y_percent' => 20],
            ['name' => 'Motion Sensor E2',  'zone_id' => $east->id,    'type' => 'motion',    'status' => 'online',  'battery_level' => 91, 'x_percent' => 75, 'y_percent' => 25],
            ['name' => 'Thermal Sensor C2', 'zone_id' => $coastal->id, 'type' => 'thermal',   'status' => 'online',  'battery_level' => 55, 'x_percent' => 25, 'y_percent' => 75],
        ])->map(fn ($s) => Sensor::create(array_merge($s, [
            'last_ping' => now()->subMinutes(rand(1, 120)),
            'metadata'  => [],
        ])));

        [$motN1, $thermE1, $camC1, $vibS1, $gasW1, $smokeN2, $motE2, $thermC2] = $sensors;

        // ── Reports ────────────────────────────────────────────────────────
        $reports = [
            [
                'title'            => 'Unauthorised Entry — North Gate',
                'description'      => 'Individual spotted crossing the northern perimeter fence at approximately 02:15. Thermal imaging confirmed movement heading south-east.',
                'type'             => 'intrusion',   'severity' => 'critical', 'status' => 'open',          'source' => 'sensor',
                'zone_id'          => $north->id,    'sensor_id' => $motN1->id,
                'reporter_name'    => 'Sensor Auto-Alert', 'reporter_contact' => 'system@coastalborder.local',
                'created_at'       => now()->subHours(2),
            ],
            [
                'title'            => 'Suspicious Vehicle — East Checkpoint',
                'description'      => 'Vehicle with obscured plates idling 200m east of checkpoint for over 40 minutes. Occupants unidentified.',
                'type'             => 'suspicious',  'severity' => 'high',     'status' => 'investigating', 'source' => 'manual',
                'zone_id'          => $east->id,     'sensor_id' => null,
                'reporter_name'    => 'Officer Mwangi', 'reporter_contact' => '+254 712 345 678',
                'created_at'       => now()->subHours(5),
            ],
            [
                'title'            => 'Perimeter Vibration Alert — South Zone',
                'description'      => 'Vibration sensor S1 triggered three consecutive alerts in a 10-minute window. Likely fence tampering.',
                'type'             => 'sensor_alert', 'severity' => 'high',    'status' => 'open',          'source' => 'sensor',
                'zone_id'          => $south->id,    'sensor_id' => $vibS1->id,
                'reporter_name'    => 'Sensor Auto-Alert', 'reporter_contact' => 'system@coastalborder.local',
                'created_at'       => now()->subHours(1),
            ],
            [
                'title'            => 'Vandalism — Coastal Camera C1',
                'description'      => 'Camera housing found cracked and lens spray-painted. Camera offline for 3 hours before detection.',
                'type'             => 'vandalism',   'severity' => 'medium',   'status' => 'resolved',      'source' => 'mobile',
                'zone_id'          => $coastal->id,  'sensor_id' => $camC1->id,
                'reporter_name'    => 'Sgt. Achieng', 'reporter_contact' => '+254 733 112 233',
                'created_at'       => now()->subDays(1),
            ],
            [
                'title'            => 'Gas Detector Offline — West Zone',
                'description'      => 'Gas Detector W1 stopped reporting at 08:30. Battery critically low (10%). Replacement unit required.',
                'type'             => 'environmental', 'severity' => 'medium', 'status' => 'investigating', 'source' => 'sensor',
                'zone_id'          => $west->id,     'sensor_id' => $gasW1->id,
                'reporter_name'    => 'Sensor Auto-Alert', 'reporter_contact' => 'system@coastalborder.local',
                'created_at'       => now()->subHours(8),
            ],
            [
                'title'            => 'Smoke Detected — North Zone',
                'description'      => 'Smoke sensor N2 detected elevated particulate readings. Source identified as controlled burn by local farmers.',
                'type'             => 'environmental', 'severity' => 'low',    'status' => 'resolved',      'source' => 'sensor',
                'zone_id'          => $north->id,    'sensor_id' => $smokeN2->id,
                'reporter_name'    => 'Sensor Auto-Alert', 'reporter_contact' => 'system@coastalborder.local',
                'created_at'       => now()->subDays(2),
            ],
            [
                'title'            => 'Unidentified Watercraft Near Coast',
                'description'      => 'Small vessel with no visible registration observed 500m offshore. No distress signal. Coast guard notified.',
                'type'             => 'suspicious',  'severity' => 'high',     'status' => 'open',          'source' => 'mobile',
                'zone_id'          => $coastal->id,  'sensor_id' => null,
                'reporter_name'    => 'Cpl. Otieno', 'reporter_contact' => '+254 720 987 654',
                'created_at'       => now()->subHours(3),
            ],
            [
                'title'            => 'Thermal Anomaly — East Thermal Cam',
                'description'      => 'Thermal camera E1 recorded a heat signature consistent with a person concealed in dense shrub cover east of checkpoint at 23:47.',
                'type'             => 'intrusion',   'severity' => 'critical', 'status' => 'investigating', 'source' => 'sensor',
                'zone_id'          => $east->id,     'sensor_id' => $thermE1->id,
                'reporter_name'    => 'Sensor Auto-Alert', 'reporter_contact' => 'system@coastalborder.local',
                'created_at'       => now()->subHours(6),
            ],
        ];

        foreach ($reports as $r) {
            $createdAt = $r['created_at'];
            unset($r['created_at']);
            $report = Report::create($r);
            $report->timestamps = false;
            $report->created_at = $createdAt;
            $report->updated_at = $createdAt;
            $report->save();
        }

        // ── Sensor Readings (sample readings per sensor) ───────────────────
        foreach ($sensors as $sensor) {
            for ($i = 0; $i < 10; $i++) {
                SensorReading::create([
                    'sensor_id'   => $sensor->id,
                    'value'       => round(rand(10, 100) + (rand(0, 99) / 100), 2),
                    'unit'        => match ($sensor->type) {
                        'thermal'   => '°C',
                        'gas'       => 'ppm',
                        'vibration' => 'Hz',
                        default     => 'units',
                    },
                    'triggered'   => $sensor->status === 'alert' && $i > 7,
                    'recorded_at' => now()->subMinutes($i * 15),
                ]);
            }
        }
    }
}
