<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $users = User::role('Cliente')->get();

        $equipmentData = [
            ['name' => 'Laptop Dell Latitude 5420', 'brand' => 'Dell', 'model' => 'Latitude 5420', 'serial_number' => 'DL5420-001', 'type' => 'Computadora'],
            ['name' => 'PC HP ProDesk 400', 'brand' => 'HP', 'model' => 'ProDesk 400 G7', 'serial_number' => 'HP400-002', 'type' => 'Computadora'],
            ['name' => 'Impresora HP LaserJet Pro', 'brand' => 'HP', 'model' => 'M404dn', 'serial_number' => 'HPLJ-003', 'type' => 'Impresora'],
            ['name' => 'Servidor Dell PowerEdge R740', 'brand' => 'Dell', 'model' => 'PowerEdge R740', 'serial_number' => 'DPE-004', 'type' => 'Servidor'],
            ['name' => 'Switch Cisco Catalyst 2960', 'brand' => 'Cisco', 'model' => 'Catalyst 2960-X', 'serial_number' => 'CSC-005', 'type' => 'Red'],
            ['name' => 'Laptop Lenovo ThinkPad T14', 'brand' => 'Lenovo', 'model' => 'ThinkPad T14 Gen 2', 'serial_number' => 'LNT14-006', 'type' => 'Computadora'],
            ['name' => 'Monitor LG UltraWide 34"', 'brand' => 'LG', 'model' => '34WN80C-B', 'serial_number' => 'LGUW-007', 'type' => 'Otro'],
            ['name' => 'Impresora Epson EcoTank', 'brand' => 'Epson', 'model' => 'ET-4760', 'serial_number' => 'EPET-008', 'type' => 'Impresora'],
            ['name' => 'Access Point Ubiquiti UniFi', 'brand' => 'Ubiquiti', 'model' => 'UniFi AP AC Pro', 'serial_number' => 'UBAP-009', 'type' => 'Red'],
            ['name' => 'PC Lenovo ThinkCentre M70q', 'brand' => 'Lenovo', 'model' => 'ThinkCentre M70q Gen 2', 'serial_number' => 'LNM70-010', 'type' => 'Computadora'],
            ['name' => 'Laptop HP EliteBook 840', 'brand' => 'HP', 'model' => 'EliteBook 840 G8', 'serial_number' => 'HPEB-011', 'type' => 'Computadora'],
            ['name' => 'Router MikroTik RB4011', 'brand' => 'MikroTik', 'model' => 'RB4011iGS+', 'serial_number' => 'MTRT-012', 'type' => 'Red'],
            ['name' => 'Servidor HP ProLiant DL380', 'brand' => 'HP', 'model' => 'ProLiant DL380 Gen10', 'serial_number' => 'HPPL-013', 'type' => 'Servidor'],
            ['name' => 'Impresora Brother MFC-L8900CDW', 'brand' => 'Brother', 'model' => 'MFC-L8900CDW', 'serial_number' => 'BRMFC-014', 'type' => 'Impresora'],
            ['name' => 'Laptop Dell XPS 15', 'brand' => 'Dell', 'model' => 'XPS 15 9510', 'serial_number' => 'DLXPS-015', 'type' => 'Computadora'],
            ['name' => 'Firewall Fortinet FortiGate 60F', 'brand' => 'Fortinet', 'model' => 'FortiGate 60F', 'serial_number' => 'FTFG-016', 'type' => 'Red'],
            ['name' => 'PC Dell OptiPlex 7090', 'brand' => 'Dell', 'model' => 'OptiPlex 7090', 'serial_number' => 'DLOP-017', 'type' => 'Computadora'],
            ['name' => 'NAS Synology DS920+', 'brand' => 'Synology', 'model' => 'DiskStation DS920+', 'serial_number' => 'SYDS-018', 'type' => 'Servidor'],
            ['name' => 'Teléfono IP Cisco 8845', 'brand' => 'Cisco', 'model' => 'IP Phone 8845', 'serial_number' => 'CSCP-019', 'type' => 'Teléfono'],
            ['name' => 'Laptop Lenovo IdeaPad 5', 'brand' => 'Lenovo', 'model' => 'IdeaPad 5 Pro 16"', 'serial_number' => 'LNIP-020', 'type' => 'Computadora'],
        ];

        foreach ($equipmentData as $index => $equipment) {
            $client = $clients->random();
            $user = $users->random();

            Equipment::create([
                'name' => $equipment['name'],
                'brand' => $equipment['brand'],
                'model' => $equipment['model'],
                'serial_number' => $equipment['serial_number'],
                'type' => $equipment['type'],
                'client_id' => $client->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
