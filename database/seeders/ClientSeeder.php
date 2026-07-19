<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Corporativo García S.A. de C.V.',
                'contact_email' => 'contacto@corporativogarcia.com',
                'contact_phone' => '+52 55 1234 5678',
                'address' => 'Av. Reforma 250, Piso 15, Col. Juárez, CDMX',
                'tax_id' => 'CGA950101ABC',
            ],
            [
                'name' => 'Tecnologías del Norte S.A.',
                'contact_email' => 'info@tecnologiasdelnorte.mx',
                'contact_phone' => '+52 81 2345 6789',
                'address' => 'Calle Industrial 456, Monterrey, N.L.',
                'tax_id' => 'TDN020315XYZ',
            ],
            [
                'name' => 'Servicios Médicos Integrales',
                'contact_email' => 'admin@serviciosmedicos.mx',
                'contact_phone' => '+52 33 3456 7890',
                'address' => 'Blvd. López Mateos 1234, Guadalajara, Jal.',
                'tax_id' => 'SMI080520DEF',
            ],
            [
                'name' => 'Grupo Educativo del Pacífico',
                'contact_email' => 'soporte@educativopacifico.edu.mx',
                'contact_phone' => '+52 664 456 7890',
                'address' => 'Av. Universidad 789, Tijuana, B.C.',
                'tax_id' => 'GEP150708GHI',
            ],
            [
                'name' => 'Constructora y Asociados',
                'contact_email' => 'proyectos@constructoraya.com',
                'contact_phone' => '+52 222 123 4567',
                'address' => 'Calle 5 de Mayo 321, Puebla, Pue.',
                'tax_id' => 'CAA120918JKL',
            ],
            [
                'name' => 'Distribuidora Nacional de Alimentos',
                'contact_email' => 'ti@dna-alimentos.com',
                'contact_phone' => '+52 55 5678 9012',
                'address' => 'Carretera a Querétaro Km 15, Naucalpan, Edo. Méx.',
                'tax_id' => 'DNA050630MNO',
            ],
            [
                'name' => 'Bufete Jurídico Martínez y Asociados',
                'contact_email' => 'admin@bufetemartinez.mx',
                'contact_phone' => '+52 55 6789 0123',
                'address' => 'Av. Insurgentes Sur 1500, CDMX',
                'tax_id' => 'BJM980412PQR',
            ],
            [
                'name' => 'Hotel Plaza Continental',
                'contact_email' => 'sistemas@plazacontinental.com',
                'contact_phone' => '+52 998 234 5678',
                'address' => 'Blvd. Kukulcán Km 12.5, Cancún, Q.R.',
                'tax_id' => 'HPC101105STU',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
