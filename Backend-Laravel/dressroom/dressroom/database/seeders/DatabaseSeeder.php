<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Site Settings
        SiteSetting::firstOrCreate([], [
            'site_name' => 'Virtual Dress Room',
            'tagline' => 'Virtual Try-on & Fitting Suite',
            'welcome_title' => 'The future of fitting rooms.',
            'welcome_description' => 'Browse products and try them on virtually using our advanced AI technology.',
            'editorial_image' => '/storage/products/cloth_1780302248_1825.jpg',
            'editorial_small_text' => 'AI-DRIVEN COUTURE',
            'editorial_heading' => 'The future of fitting rooms.'
        ]);

        // Seed Users
        User::firstOrCreate(
            ['email' => 'admin@virtualdressroom.com'],
            [
                'username' => 'admin',
                'phone' => '1234567890',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@virtualdressroom.com'],
            [
                'username' => 'user',
                'phone' => '1234567890',
                'password' => Hash::make('password'),
                'role' => 'user'
            ]
        );

        // Seed Products using existing filenames in garments directory
        $products = [
            [
                'name' => 'Classic Structured Blazer',
                'description' => 'A structured silhouette blazer suitable for formal and office settings.',
                'price' => 120.00,
                'category' => 'Outerwear',
                'image_url' => '/storage/products/cloth_1779581008_2458.jpg'
            ],
            [
                'name' => 'Satin Slip Gown',
                'description' => 'Premium luxury satin slip gown with deep back tailoring.',
                'price' => 180.00,
                'category' => 'Dresses',
                'image_url' => '/storage/products/cloth_1779770879_9762.jpg'
            ],
            [
                'name' => 'Sculptural Modern Gown',
                'description' => 'A formal avant-garde gown designed for high-fashion elegance.',
                'price' => 350.00,
                'category' => 'Dresses',
                'image_url' => '/storage/products/cloth_1779772043_7896.jpg'
            ],
            [
                'name' => 'Relaxed Linen Tee',
                'description' => 'Lightweight linen blended tee for warm summer outings.',
                'price' => 45.00,
                'category' => 'Shirts',
                'image_url' => '/storage/products/cloth_1779836149_5885.jpg'
            ],
            [
                'name' => 'Classic Summer Blouse',
                'description' => 'Casual floral patterned summer blouse with breathable fabric.',
                'price' => 55.00,
                'category' => 'Shirts',
                'image_url' => '/storage/products/cloth_1779836407_2544.jpg'
            ],
            [
                'name' => 'Evening Silk Dress',
                'description' => 'Elegant flowing evening silk dress in monochrome tone.',
                'price' => 220.00,
                'category' => 'Dresses',
                'image_url' => '/storage/products/cloth_1779997262_5161.jpg'
            ],
            [
                'name' => 'Royal Velvet Evening Dress',
                'description' => 'Exquisite soft velvet texture dress designed for evening elegance.',
                'price' => 290.00,
                'category' => 'Dresses',
                'image_url' => '/storage/products/cloth_1780026760_9931.jpg'
            ],
            [
                'name' => 'Tailored Woolen Coat',
                'description' => 'Warm woolen winter coat with high neck buttons.',
                'price' => 240.00,
                'category' => 'Outerwear',
                'image_url' => '/storage/products/cloth_1780105417_8736.jpg'
            ],
            [
                'name' => 'Minimalist Cotton T-Shirt',
                'description' => '100% organic cotton basic t-shirt in pure white.',
                'price' => 30.00,
                'category' => 'Shirts',
                'image_url' => '/storage/products/cloth_1780105750_9536.jpg'
            ],
            [
                'name' => 'Vintage Denim Jacket',
                'description' => 'Heavyweight cotton vintage blue denim jacket.',
                'price' => 95.00,
                'category' => 'Outerwear',
                'image_url' => '/storage/products/cloth_1780121654_1245.jpg'
            ],
            [
                'name' => 'Avant-Garde Architectural Dress',
                'description' => 'A statement runway dress with sculptural styling.',
                'price' => 450.00,
                'category' => 'Dresses',
                'image_url' => '/storage/products/cloth_1780302248_1825.jpg'
            ],
            [
                'name' => 'Urban Relaxed Tee',
                'description' => 'A minimalist relaxed black cotton t-shirt.',
                'price' => 35.00,
                'category' => 'Shirts',
                'image_url' => '/storage/products/cloth_1780302783_5518.jpg'
            ]
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}
