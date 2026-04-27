<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@castelobienes.ec'],
            [
                'name'     => 'Administrador Castelo',
                'cedula'   => '1700000001',
                'phone'    => '+593990000000',
                'role'     => 'admin',
                'password' => Hash::make('Castelo2026!'),
            ]
        );

        // Demo properties
        $properties = [
            [
                'title'       => 'Terreno Urbano en Quito Norte',
                'description' => 'Excelente terreno urbano en sector de alta plusvalía, listo para construcción. Todos los servicios básicos.',
                'type'        => 'urbano',
                'province'    => 'Pichincha',
                'canton'      => 'Quito',
                'price'       => 85000,
                'area_m2'     => 300,
                'status'      => 'disponible',
                'latitude'    => -0.1807,
                'longitude'   => -78.4678,
                'soil_type'   => 'Suelo firme, apto para construcción',
                'access_services' => 'Agua, luz, alcantarillado, internet',
                'legal_documents' => 'Escritura libre de gravámenes',
                'published'   => true,
                'views'       => 145,
            ],
            [
                'title'       => 'Lote Agrícola en Santo Domingo',
                'description' => 'Terreno agrícola con suelo fértil ideal para cultivos tropicales. Acceso por carretera principal asfaltada.',
                'type'        => 'agricola',
                'province'    => 'Santo Domingo',
                'canton'      => 'Santo Domingo',
                'price'       => 45000,
                'area_m2'     => 5000,
                'status'      => 'disponible',
                'latitude'    => -0.2543,
                'longitude'   => -79.1719,
                'soil_type'   => 'Arcilloso, alta fertilidad',
                'access_services' => 'Agua, luz',
                'legal_documents' => 'Escritura a nombre del vendedor',
                'published'   => true,
                'views'       => 89,
            ],
            [
                'title'       => 'Terreno Comercial en Guayaquil',
                'description' => 'Estratégico terreno comercial en zona de alta afluencia. Ideal para local comercial, bodega o edificio.',
                'type'        => 'comercial',
                'province'    => 'Guayas',
                'canton'      => 'Guayaquil',
                'price'       => 120000,
                'area_m2'     => 450,
                'status'      => 'disponible',
                'latitude'    => -2.1900,
                'longitude'   => -79.8875,
                'soil_type'   => 'Suelo compactado',
                'access_services' => 'Todos los servicios',
                'legal_documents' => 'Escritura pública, libre de gravámenes',
                'published'   => true,
                'views'       => 213,
            ],
        ];

        foreach ($properties as $propData) {
            $property = Property::firstOrCreate(
                ['title' => $propData['title']],
                [...$propData, 'created_by' => $admin->id]
            );

            if ($property->wasRecentlyCreated && $property->media()->count() === 0) {
                PropertyMedia::create([
                    'property_id' => $property->id,
                    'media_type'  => 'photo',
                    'url'         => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&q=80',
                    'order'       => 1,
                ]);
            }
        }

        $this->command->info('✓ Seeder completado: admin@castelobienes.ec / Castelo2026!');
    }
}
