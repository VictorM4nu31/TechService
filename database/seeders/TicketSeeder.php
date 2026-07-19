<?php

namespace Database\Seeders;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Equipment;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        // Crear equipos técnicos
        $teams = [
            Team::create(['name' => 'Soporte Nivel 1', 'leader_id' => User::role('Agente')->first()->id]),
            Team::create(['name' => 'Soporte Nivel 2', 'leader_id' => User::role('Agente')->skip(1)->first()->id]),
            Team::create(['name' => 'Infraestructura', 'leader_id' => User::role('Agente')->skip(2)->first()->id]),
        ];

        // Asignar agentes a equipos
        $agents = User::role('Agente')->get();
        foreach ($agents as $index => $agent) {
            $teamIndex = $index % 3;
            $teams[$teamIndex]->members()->attach($agent->id);
        }

        $clients = User::role('Cliente')->get();
        $equipment = Equipment::all();

        // Tickets con datos realistas
        $ticketTemplates = [
            [
                'title' => 'Laptop no enciende después de actualización',
                'description' => 'Después de la última actualización de Windows, mi laptop Dell Latitude no enciende. El LED de power parpadea pero no muestra imagen. He intentado reiniciar varias veces sin éxito.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Oficina 301 - Piso 3',
            ],
            [
                'title' => 'Impresora HP atascada constantemente',
                'description' => 'La impresora HP LaserJet del área de contabilidad se atasca cada 2-3 impresiones. Ya limpiamos los rodillos pero el problema persiste. Necesitamos revisión técnica urgente.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Contabilidad - Piso 2',
            ],
            [
                'title' => 'Lentitud extrema en sistema ERP',
                'description' => 'El sistema ERP está extremadamente lento desde ayer. Las consultas que normalmente toman 2 segundos ahora tardan más de 30 segundos. Esto está afectando la operación diaria.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Software',
                'location' => 'Servidor Principal - Data Center',
            ],
            [
                'title' => 'Instalación de software contable',
                'description' => 'Solicito la instalación del software contable SAP Business One en 3 computadoras nuevas del departamento financiero. Ya tenemos las licencias disponibles.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Software',
                'location' => 'Finanzas - Piso 4',
            ],
            [
                'title' => 'Configuración de VPN para trabajo remoto',
                'description' => 'Necesito configurar acceso VPN para 5 empleados del área de ventas que trabajarán remotamente la próxima semana. Requieren acceso a archivos compartidos y sistema CRM.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Red',
                'location' => 'Ventas - Piso 1',
            ],
            [
                'title' => 'Mantenimiento preventivo trimestral de servidores',
                'description' => 'Corresponde el mantenimiento preventivo trimestral a los servidores principales. Incluye limpieza física, revisión de logs, actualización de firmware y verificación de backups.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Data Center',
            ],
            [
                'title' => 'Correo electrónico no sincroniza en móvil',
                'description' => 'Mi correo corporativo no sincroniza en mi teléfono iPhone. Ya verifiqué la configuración de la cuenta y la contraseña es correcta. El problema empezó hace 2 días.',
                'priority' => TicketPriority::Baja,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Software',
                'location' => 'Dirección General',
            ],
            [
                'title' => 'Solicitud de nuevo equipo de cómputo',
                'description' => 'Solicito la adquisición de una laptop para el nuevo empleado del área de marketing. Especificaciones requeridas: i7, 16GB RAM, 512GB SSD, Windows 11 Pro.',
                'priority' => TicketPriority::Baja,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Recursos Humanos',
            ],
            [
                'title' => 'Falla en red WiFi del piso 3',
                'description' => 'La red WiFi del piso 3 está intermitente. Los usuarios reportan desconexiones frecuentes y lentitud. El problema afecta a aproximadamente 20 personas.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Emergencia,
                'maintenance_type' => 'Red',
                'location' => 'Piso 3 - Área general',
            ],
            [
                'title' => 'Backup fallido del servidor de archivos',
                'description' => 'El backup nocturno del servidor de archivos está fallando con error de permisos. El último backup exitoso fue hace 3 días. Se requiere atención inmediata para evitar pérdida de datos.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Emergencia,
                'maintenance_type' => 'Software',
                'location' => 'Data Center - Servidor de Archivos',
            ],
            [
                'title' => 'Actualización de Office 365',
                'description' => 'Solicito la actualización de Office 365 a la versión más reciente en todas las computadoras del departamento administrativo. Son aproximadamente 15 equipos.',
                'priority' => TicketPriority::Baja,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Software',
                'location' => 'Administración - Varios pisos',
            ],
            [
                'title' => 'Monitor con líneas horizontales',
                'description' => 'Mi monitor LG de 27 pulgadas muestra líneas horizontales que aparecen y desaparecen. El problema empezó hace una semana y está empeorando. Creo que necesita reemplazo.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Diseño - Piso 2',
            ],
            [
                'title' => 'Configuración de nuevo access point',
                'description' => 'Necesitamos instalar un nuevo access point en la sala de juntas del piso 5 para mejorar la cobertura WiFi. Ya tenemos el equipo Ubiquiti UniFi disponible.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Red',
                'location' => 'Sala de Juntas - Piso 5',
            ],
            [
                'title' => 'Recuperación de archivos eliminados',
                'description' => 'Un empleado eliminó accidentalmente una carpeta completa con documentos importantes del servidor de archivos. Necesitamos recuperar los archivos del backup más reciente.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Emergencia,
                'maintenance_type' => 'Software',
                'location' => 'Data Center',
            ],
            [
                'title' => 'Teclado y mouse no funcionan',
                'description' => 'El teclado y mouse inalámbricos de mi computadora dejaron de funcionar. Ya cambié las pilas y probé en otro puerto USB sin éxito. Posiblemente necesito reemplazo.',
                'priority' => TicketPriority::Baja,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Atención a Clientes - Piso 1',
            ],
            [
                'title' => 'Migración de correo a nuevo servidor',
                'description' => 'Necesitamos migrar 50 buzones de correo al nuevo servidor Exchange. El proyecto debe completarse en el fin de semana para minimizar el impacto en la operación.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Software',
                'location' => 'Data Center - Servidor de Correo',
            ],
            [
                'title' => 'Instalación de cámaras de seguridad',
                'description' => 'Solicito la instalación de 4 cámaras IP en el área de almacén para mejorar la seguridad. Ya tenemos las cámaras y el cableado necesario.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Almacén - Planta Baja',
            ],
            [
                'title' => 'Problema con licencia de software',
                'description' => 'El software de diseño AutoCAD muestra error de licencia aunque tenemos la licencia activa. El problema empezó después de la última actualización del sistema operativo.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Software',
                'location' => 'Ingeniería - Piso 3',
            ],
            [
                'title' => 'Limpieza física de equipos',
                'description' => 'Solicito limpieza física de 10 computadoras del área de call center. Los equipos tienen acumulación de polvo y necesitan mantenimiento preventivo para evitar sobrecalentamiento.',
                'priority' => TicketPriority::Baja,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Call Center - Piso 1',
            ],
            [
                'title' => 'Falla en sistema de telefonía IP',
                'description' => 'El sistema de telefonía IP tiene intermitencia en las extensiones del piso 4. Algunas llamadas se cortan y hay eco en las conversaciones. Afecta a 30 usuarios.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Red',
                'location' => 'Piso 4 - Área general',
            ],
            [
                'title' => 'Solicitud de acceso a carpeta compartida',
                'description' => 'Necesito acceso a la carpeta compartida "Proyectos 2024" del servidor de archivos. Mi supervisor ya aprobó la solicitud. Usuario: Juan Pérez, Departamento: Marketing.',
                'priority' => TicketPriority::Baja,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Software',
                'location' => 'Marketing - Piso 2',
            ],
            [
                'title' => 'Actualización de firmware en switches',
                'description' => 'Programar actualización de firmware en los 8 switches Cisco de la red principal. Debe realizarse en horario nocturno para minimizar el impacto. Duración estimada: 4 horas.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Preventivo,
                'maintenance_type' => 'Red',
                'location' => 'Data Center - Rack Principal',
            ],
            [
                'title' => 'Computadora con pantalla azul frecuente',
                'description' => 'Mi computadora muestra pantalla azul (BSOD) al menos 3 veces al día. El error varía pero generalmente es MEMORY_MANAGEMENT. Ya ejecuté diagnóstico de memoria sin encontrar problemas.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Desarrollo - Piso 3',
            ],
            [
                'title' => 'Configuración de nuevo empleado',
                'description' => 'Necesitamos configurar el equipo de cómputo para el nuevo empleado que inicia el lunes. Incluye: instalación de software estándar, configuración de correo, acceso a carpetas compartidas y sistema ERP.',
                'priority' => TicketPriority::Media,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Software',
                'location' => 'Recursos Humanos',
            ],
            [
                'title' => 'Problema con impresora de cheques',
                'description' => 'La impresora de cheques del área de tesorería no imprime correctamente. Los caracteres salen borrosos y desalineados. Necesitamos limpieza y calibración urgente.',
                'priority' => TicketPriority::Alta,
                'category' => TicketCategory::Correctivo,
                'maintenance_type' => 'Hardware',
                'location' => 'Tesorería - Piso 2',
            ],
        ];

        foreach ($ticketTemplates as $index => $template) {
            $client = $clients->random();
            $assignedAgent = $agents->random();
            $team = $teams[array_rand($teams)];

            // Distribución realista de estados
            $statusDistribution = [
                TicketStatus::Abierto->value => 40,
                TicketStatus::EnProgreso->value => 35,
                TicketStatus::Cerrado->value => 25,
            ];

            $status = $this->getWeightedRandom($statusDistribution);

            // Fechas realistas
            $createdAt = now()->subDays(rand(0, 60))->subHours(rand(0, 23));
            $dueDate = $createdAt->copy()->addDays(rand(1, 14));

            $ticket = Ticket::create([
                'title' => $template['title'],
                'description' => $template['description'],
                'status' => $status,
                'priority' => $template['priority'],
                'category' => $template['category'],
                'maintenance_type' => $template['maintenance_type'],
                'location' => $template['location'],
                'created_by' => $client->id,
                'assigned_to' => $status !== TicketStatus::Abierto ? $assignedAgent->id : null,
                'team_id' => $team->id,
                'equipment_id' => $equipment->random()->id,
                'due_date' => $dueDate,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Actividad de creación
            Activity::create([
                'user_id' => $client->id,
                'ticket_id' => $ticket->id,
                'type' => 'created',
                'description' => 'creó el ticket',
                'created_at' => $createdAt,
            ]);

            // Si está asignado, agregar actividad de asignación
            if ($ticket->assigned_to) {
                $assignedAt = $createdAt->copy()->addHours(rand(1, 24));
                Activity::create([
                    'user_id' => $agents->random()->id,
                    'ticket_id' => $ticket->id,
                    'type' => 'assigned',
                    'description' => "asignó el ticket a {$assignedAgent->name}",
                    'properties' => ['assigned_to' => $assignedAgent->id],
                    'created_at' => $assignedAt,
                ]);
            }

            // Si está en progreso o cerrado, agregar cambio de estado
            if ($status === TicketStatus::EnProgreso || $status === TicketStatus::Cerrado) {
                $statusChangedAt = $createdAt->copy()->addHours(rand(24, 72));
                Activity::create([
                    'user_id' => $ticket->assigned_to ?? $agents->random()->id,
                    'ticket_id' => $ticket->id,
                    'type' => 'status_updated',
                    'description' => 'cambió el estado de Abierto a En Progreso',
                    'properties' => [
                        'old_status' => TicketStatus::Abierto->value,
                        'new_status' => TicketStatus::EnProgreso->value,
                    ],
                    'created_at' => $statusChangedAt,
                ]);
            }

            // Si está cerrado, agregar actividad de cierre
            if ($status === TicketStatus::Cerrado) {
                $resolvedAt = $createdAt->copy()->addDays(rand(3, 10));
                Activity::create([
                    'user_id' => $ticket->assigned_to ?? $agents->random()->id,
                    'ticket_id' => $ticket->id,
                    'type' => 'resolved',
                    'description' => 'resolvió el ticket',
                    'created_at' => $resolvedAt,
                ]);
            }

            // Agregar comentarios (más comentarios en tickets activos)
            $commentCount = $status === TicketStatus::Cerrado ? rand(2, 5) : rand(0, 3);
            if ($commentCount > 0) {
                $commenters = $clients->merge($agents);
                $lastCommentAt = $createdAt->copy();

                for ($i = 0; $i < $commentCount; $i++) {
                    $commenter = $commenters->random();
                    $commentAt = $lastCommentAt->copy()->addHours(rand(2, 48));

                    $commentContents = [
                        'Gracias por reportar el problema. Estamos revisando la situación.',
                        'Ya identificamos la causa del problema. Procederemos con la solución.',
                        '¿Podrías proporcionarnos más detalles sobre cuándo empezó el problema?',
                        'Hemos aplicado una solución temporal. Programaremos una revisión definitiva.',
                        'El problema ha sido resuelto. Por favor confirma si todo funciona correctamente.',
                        'Necesitamos acceder al equipo para realizar el diagnóstico. ¿Cuándo estarías disponible?',
                        'Ya tenemos el repuesto necesario. Programaremos la instalación para mañana.',
                        'Hemos completado el mantenimiento. El equipo está funcionando correctamente.',
                        'Gracias por la información. Continuamos con el diagnóstico.',
                        'El problema persiste después de la primera intervención. Necesitamos escalar el caso.',
                    ];

                    Comment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $commenter->id,
                        'content' => $commentContents[array_rand($commentContents)],
                        'created_at' => $commentAt,
                    ]);

                    Activity::create([
                        'user_id' => $commenter->id,
                        'ticket_id' => $ticket->id,
                        'type' => 'commented',
                        'description' => 'comentó en el ticket',
                        'properties' => ['comment_id' => Comment::latest()->first()->id],
                        'created_at' => $commentAt,
                    ]);

                    $lastCommentAt = $commentAt;
                }
            }
        }
    }

    private function getWeightedRandom(array $items): string
    {
        $rand = mt_rand(1, array_sum($items));
        foreach ($items as $item => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $item;
            }
        }
        return array_key_first($items);
    }
}
