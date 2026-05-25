<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class TechServicesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        $services = [
            [
                'name' => 'Custom Software Development',
                'description' => 'Tailored software solutions built from scratch to meet specific business requirements.',
                'category' => 'Software Development',
                'unit_price' => 500000.00,
                'cost_price' => 200000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes 3 months support and maintenance'
            ],
            [
                'name' => 'Web Application Development',
                'description' => 'Modern web applications using Laravel, React, Vue.js with responsive design.',
                'category' => 'Software Development',
                'unit_price' => 800000.00,
                'cost_price' => 300000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes hosting setup and 6 months support'
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Native and cross-platform mobile applications for iOS and Android.',
                'category' => 'Software Development',
                'unit_price' => 1200000.00,
                'cost_price' => 500000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes app store submission and 1 year support'
            ],
            [
                'name' => 'Corporate Website Development',
                'description' => 'Professional corporate websites with modern design and SEO optimization.',
                'category' => 'Website Development',
                'unit_price' => 400000.00,
                'cost_price' => 150000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes 1 year hosting and maintenance'
            ],
            [
                'name' => 'E-commerce Website Development',
                'description' => 'Complete online store with shopping cart and payment integration.',
                'category' => 'Website Development',
                'unit_price' => 600000.00,
                'cost_price' => 250000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes payment gateway setup and SSL certificate'
            ],
            [
                'name' => 'WordPress Website Development',
                'description' => 'Custom WordPress websites with themes and plugins.',
                'category' => 'Website Development',
                'unit_price' => 250000.00,
                'cost_price' => 80000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes theme customization and plugin setup'
            ],
            [
                'name' => 'Network Setup & Configuration',
                'description' => 'Complete network infrastructure setup with security configuration.',
                'category' => 'Networking',
                'unit_price' => 300000.00,
                'cost_price' => 120000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes equipment and installation'
            ],
            [
                'name' => 'Network Security Implementation',
                'description' => 'Firewall configuration, VPN setup, and network monitoring.',
                'category' => 'Networking',
                'unit_price' => 250000.00,
                'cost_price' => 100000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes security audit and documentation'
            ],
            [
                'name' => 'Shared Web Hosting',
                'description' => 'Reliable shared hosting with cPanel and SSL certificate.',
                'category' => 'Web Hosting',
                'unit_price' => 120000.00,
                'cost_price' => 40000.00,
                'unit_type' => 'year',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes 10GB storage and unlimited bandwidth'
            ],
            [
                'name' => 'VPS Hosting',
                'description' => 'Virtual Private Server with dedicated resources and root access.',
                'category' => 'Web Hosting',
                'unit_price' => 300000.00,
                'cost_price' => 120000.00,
                'unit_type' => 'year',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes 50GB SSD storage and 2GB RAM'
            ],
            [
                'name' => 'Domain Name Registration (.com)',
                'description' => 'Register .com domain names with DNS management.',
                'category' => 'Domain Registration',
                'unit_price' => 15000.00,
                'cost_price' => 8000.00,
                'unit_type' => 'year',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes DNS management and privacy protection'
            ],
            [
                'name' => 'Domain Name Registration (.ug)',
                'description' => 'Register .ug domain names for Ugandan businesses.',
                'category' => 'Domain Registration',
                'unit_price' => 25000.00,
                'cost_price' => 12000.00,
                'unit_type' => 'year',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes local registration and support'
            ],
            [
                'name' => 'IT Consulting',
                'description' => 'Strategic IT consulting for digital transformation.',
                'category' => 'IT Consulting',
                'unit_price' => 200000.00,
                'cost_price' => 80000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes detailed analysis and recommendations'
            ],
            [
                'name' => 'Database Design & Development',
                'description' => 'Custom database design and optimization for business applications.',
                'category' => 'Database Services',
                'unit_price' => 350000.00,
                'cost_price' => 150000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes performance optimization and backup strategy'
            ],
            [
                'name' => 'Technical Support & Maintenance',
                'description' => 'Ongoing technical support and system maintenance.',
                'category' => 'Support & Maintenance',
                'unit_price' => 100000.00,
                'cost_price' => 40000.00,
                'unit_type' => 'month',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes remote support and system monitoring'
            ],
            [
                'name' => 'SEO & Digital Marketing',
                'description' => 'Search engine optimization and digital marketing campaigns.',
                'category' => 'Digital Marketing',
                'unit_price' => 180000.00,
                'cost_price' => 70000.00,
                'unit_type' => 'month',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes monthly reports and analytics'
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'User interface and user experience design for websites and apps.',
                'category' => 'Design Services',
                'unit_price' => 250000.00,
                'cost_price' => 100000.00,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'status' => 'active',
                'is_service' => true,
                'notes' => 'Includes wireframes, mockups, and prototypes'
            ]
        ];

        $this->command->info('Seeding tech services...');

        foreach ($services as $service) {
            $service['user_id'] = $user->id;
            Item::create($service);
            $this->command->info("Created service: {$service['name']}");
        }

        $this->command->info('Successfully seeded ' . count($services) . ' tech services!');
    }
}