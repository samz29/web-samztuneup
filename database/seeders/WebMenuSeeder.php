<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // Header Menus - Main navigation
            [
                'title' => 'Home',
                'url' => '/',
                'icon' => 'fas fa-home',
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'target' => '_self',
                'location' => 'header',
            ],
            [
                'title' => 'Services',
                'url' => '#services',
                'icon' => 'fas fa-cogs',
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'target' => '_self',
                'location' => 'header',
            ],
            [
                'title' => 'Book Service',
                'url' => '/booking/create',
                'icon' => 'fas fa-calendar-plus',
                'parent_id' => null,
                'sort_order' => 3,
                'is_active' => true,
                'target' => '_self',
                'location' => 'header',
            ],
            [
                'title' => 'Track Booking',
                'url' => '/booking/track',
                'icon' => 'fas fa-search',
                'parent_id' => null,
                'sort_order' => 4,
                'is_active' => true,
                'target' => '_self',
                'location' => 'header',
            ],
            [
                'title' => 'About',
                'url' => '#about',
                'icon' => 'fas fa-info-circle',
                'parent_id' => null,
                'sort_order' => 5,
                'is_active' => true,
                'target' => '_self',
                'location' => 'header',
            ],
            [
                'title' => 'Contact',
                'url' => '#contact',
                'icon' => 'fas fa-phone',
                'parent_id' => null,
                'sort_order' => 6,
                'is_active' => true,
                'target' => '_self',
                'location' => 'header',
            ],

            // Footer Menus
            [
                'title' => 'Privacy Policy',
                'url' => '#privacy',
                'icon' => 'fas fa-shield-alt',
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'target' => '_self',
                'location' => 'footer',
            ],
            [
                'title' => 'Terms of Service',
                'url' => '#terms',
                'icon' => 'fas fa-file-contract',
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'target' => '_self',
                'location' => 'footer',
            ],
            [
                'title' => 'Support',
                'url' => '#support',
                'icon' => 'fas fa-headset',
                'parent_id' => null,
                'sort_order' => 3,
                'is_active' => true,
                'target' => '_self',
                'location' => 'footer',
            ],
        ];

        foreach ($menus as $menu) {
            \App\Models\WebMenu::create($menu);
        }
    }
}
