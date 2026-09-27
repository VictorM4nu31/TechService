<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use Illuminate\Database\Seeder;

class MaintenanceScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $equipment = Equipment::whereIn('serial_number', [
            'HPLJ-003',
            'BRMFC-014',
            'CSC-005',
            'DPE-004',
            'HPPL-013',
            'MTRT-012',
            'SYDS-018',
            'UBAP-009',
            'FTFG-016',
        ])->get()->keyBy('serial_number');

        $plans = [
            [
                'serial_number' => 'HPLJ-003',
                'name' => 'Limpieza de rodillos y fusor',
                'description' => 'Mantenimiento preventivo trimestral de la impresora compartida del piso 3.',
                'checklist' => [
                    'Retirar unidad dúplex y rodillos de alimentación',
                    'Limpiar fusor con hisopo seco (apagada y fría)',
                    'Verificar nivel de tóner y nivel de residuo',
                    'Imprimir página de prueba y verificar calibración',
                ],
                'frequency_days' => 90,
                'last_run_days_ago' => 96,
                'is_active' => true,
            ],
            [
                'serial_number' => 'BRMFC-014',
                'name' => 'Revisión de tambor y nivel de tóner',
                'description' => 'Control preventivo del consumible crítico para la impresora de contabilidad.',
                'checklist' => [
                    'Medir nivel de tóner cian, magenta, amarillo y negro',
                    'Inspeccionar tambor en busca de desgaste',
                    'Limpiar rodillo de entrada de papel',
                ],
                'frequency_days' => 60,
                'last_run_days_ago' => 32,
                'is_active' => true,
            ],
            [
                'serial_number' => 'CSC-005',
                'name' => 'Auditoría de puertos y configuración de VLANs',
                'description' => 'Switch principal de la sede: revisión de puertos libres, VLANs y trunks.',
                'checklist' => [
                    'Exportar running-config como respaldo',
                    'Verificar VLANs críticas (voz, datos, cámaras)',
                    'Revisar puertos sin uso y desactivar',
                    'Actualizar firmware a la última versión estable',
                ],
                'frequency_days' => 180,
                'last_run_days_ago' => 200,
                'is_active' => true,
            ],
            [
                'serial_number' => 'DPE-004',
                'name' => 'Limpieza del flujo de aire y cambio de pasta térmica',
                'description' => 'Servidor principal en el centro de datos: control térmico y prevención de fallos.',
                'checklist' => [
                    'Apagar y etiquetar el servidor antes de intervenir',
                    'Limpiar filtros de aire y ventiladores',
                    'Aplicar pasta térmica nueva en la CPU',
                    'Verificar temperaturas con sensores tras 24 horas',
                ],
                'frequency_days' => 120,
                'last_run_days_ago' => 40,
                'is_active' => true,
            ],
            [
                'serial_number' => 'HPPL-013',
                'name' => 'Respaldo y verificación de RAID',
                'description' => 'Servidor secundario: validar la integridad del RAID y la rotación de respaldos.',
                'checklist' => [
                    'Ejecutar verificación de salud del RAID',
                    'Confirmar último respaldo restaurable',
                    'Revisar temperatura de discos y bahías',
                ],
                'frequency_days' => 45,
                'last_run_days_ago' => 20,
                'is_active' => true,
            ],
            [
                'serial_number' => 'MTRT-012',
                'name' => 'Actualización de RouterOS y revisión de logs',
                'description' => 'Router principal perimetral: firmware, licencias y análisis de tráfico.',
                'checklist' => [
                    'Respaldar configuración y exportarla',
                    'Revisar logs de firewall en busca de bloqueos',
                    'Actualizar RouterOS a la versión estable',
                ],
                'frequency_days' => 90,
                'last_run_days_ago' => 110,
                'is_active' => true,
            ],
            [
                'serial_number' => 'SYDS-018',
                'name' => 'Verificación SMART de discos y scrub de datos',
                'description' => 'NAS departamental: salud de discos y corrupción silenciosa de datos.',
                'checklist' => [
                    'Revisar atributos SMART de todos los volúmenes',
                    'Programar scrub semanal de datos',
                    'Verificar uso de espacio en el volumen de respaldos',
                ],
                'frequency_days' => 30,
                'last_run_days_ago' => 12,
                'is_active' => true,
            ],
            [
                'serial_number' => 'UBAP-009',
                'name' => 'Ajuste de canal y potencia de puntos de acceso',
                'description' => 'Puntos de acceso por piso: evitar interferencias entre canales vecinos.',
                'checklist' => [
                    'Revisar canal ocupado por piso',
                    'Ajustar potencia de transmisión',
                    'Validar roaming entre puntos de acceso',
                ],
                'frequency_days' => 150,
                'last_run_days_ago' => 30,
                'is_active' => true,
            ],
            [
                'serial_number' => 'FTFG-016',
                'name' => 'Actualización de firmware del firewall perimetral',
                'description' => 'FortiGate de borde: aplicar firmware y revisar firmas de intrusiones.',
                'checklist' => [
                    'Respaldar configuración actual',
                    'Actualizar a la última versión de firmware estable',
                    'Verificar suscripción de firmas y actualización de definiciones',
                ],
                'frequency_days' => 120,
                'last_run_days_ago' => 60,
                'is_active' => false,
            ],
        ];

        foreach ($plans as $plan) {
            $asset = $equipment->get($plan['serial_number']);

            if (! $asset) {
                continue;
            }

            MaintenanceSchedule::updateOrCreate(
                [
                    'equipment_id' => $asset->id,
                    'name' => $plan['name'],
                ],
                [
                    'description' => $plan['description'],
                    'checklist' => $plan['checklist'],
                    'frequency_days' => $plan['frequency_days'],
                    'last_run_at' => now()->subDays($plan['last_run_days_ago']),
                    'next_run_at' => now()->addDays($plan['frequency_days'] - $plan['last_run_days_ago']),
                    'is_active' => $plan['is_active'],
                ]
            );
        }
    }
}
