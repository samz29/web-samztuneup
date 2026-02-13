<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'SamzTune-Up',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application name displayed throughout the site',
                'is_public' => true,
            ],
            [
                'key' => 'app_description',
                'value' => 'Professional motorcycle tuning and maintenance services',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Short description of the application',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@samztune-up.com',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Primary contact email address',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 812-3456-7890',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Primary contact phone number',
                'is_public' => true,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Motorcycle Street No. 123, Jakarta, Indonesia',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Business address',
                'is_public' => true,
            ],

            // Branding Settings
            [
                'key' => 'logo',
                'value' => null,
                'type' => 'file',
                'group' => 'branding',
                'description' => 'Main logo image (PNG, JPG, SVG recommended)',
                'is_public' => true,
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'file',
                'group' => 'branding',
                'description' => 'Favicon for browser tab (ICO, PNG recommended)',
                'is_public' => true,
            ],
            [
                'key' => 'primary_color',
                'value' => '#007bff',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Primary brand color (hex code)',
                'is_public' => true,
            ],

            // Social Media Settings
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/samztuneup',
                'type' => 'string',
                'group' => 'social',
                'description' => 'Facebook page URL',
                'is_public' => true,
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/samztuneup',
                'type' => 'string',
                'group' => 'social',
                'description' => 'Instagram profile URL',
                'is_public' => true,
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '+6281234567890',
                'type' => 'string',
                'group' => 'social',
                'description' => 'WhatsApp business number',
                'is_public' => true,
            ],

            // System Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable maintenance mode',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Application timezone',
                'is_public' => true,
            ],
            // Workshop Location Settings
            [
                'key' => 'workshop_address',
                'value' => 'Jakarta, Indonesia',
                'type' => 'string',
                'group' => 'location',
                'description' => 'Workshop address for map center and distance calculations',
                'is_public' => true,
            ],
            [
                'key' => 'workshop_latitude',
                'value' => '-6.2088',
                'type' => 'string',
                'group' => 'location',
                'description' => 'Workshop latitude coordinate',
                'is_public' => true,
            ],
            [
                'key' => 'workshop_longitude',
                'value' => '106.8456',
                'type' => 'string',
                'group' => 'location',
                'description' => 'Workshop longitude coordinate',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
