# Invoice Generator

An advanced invoice generation system built with Laravel 12, designed for small businesses and freelancers to create, manage, and send professional invoices.

## Developed by Mycosoft Technologies

- **Phone Numbers**: +256 750 501151 / 078 177 9477
- **Email**: mycosoftofficial@gmail.com

## Features

- Professional invoice creation with customizable templates
- Client management system
- Item/Service catalog management
- Multiple invoice statuses (Draft, Pending, Paid, Overdue)
- PDF generation for invoices
- Email invoice functionality with PDF attachments
- Dashboard with analytics and reports
- AdminLTE integration for a professional user interface
- User authentication and management
- Responsive design for all devices

## Technology Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vite 7.0.4, Tailwind CSS 4.0.0, Bootstrap 5.2.3
- **Database**: MySQL/SQLite
- **PDF Generation**: barryvdh/laravel-dompdf
- **Admin Panel**: AdminLTE 3.15
- **Testing**: PHPUnit 11.5.3

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/mycosoft/mstinvoices.git
   cd mstinvoices
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install frontend dependencies:
   ```bash
   npm install
   ```

4. Copy and configure the environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your database in the `.env` file

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. Start the development server:
   ```bash
   npm run dev
   php artisan serve
   ```

## Key Components

- **Invoice Management**: Create, edit, view, and delete invoices
- **Client Management**: Maintain a database of clients with contact information
- **Item Management**: Catalog of items/services with pricing
- **Settings**: Configure company information, payment methods, and system preferences
- **Reports**: Dashboard with financial analytics and reporting

## Payment Information

The system supports multiple payment methods:
1. Cash Payment: Visit office during business hours
2. Bank Transfer: Equity Bank, Account Name - SSENJOBE MICHEAL, Account Number - 1043100796103, Swift Code - EQBLUGKA
3. Mobile Money: +256 750501151 / 0781779477, Name - SSENJOBE MICHEAL

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).