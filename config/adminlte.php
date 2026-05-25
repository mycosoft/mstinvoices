<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'MST ERP SYSTEM',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => 'MYCOSOFT TECHNOLOGIES',
    'logo_img' => '',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => '',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => null,
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        
        // MAIN DASHBOARD
        [
            'text' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'fas fa-fw fa-home',
        ],
        
        ['header' => 'SALES & INVOICING'],
        [
            'text' => 'Invoices',
            'icon' => 'fas fa-fw fa-file-invoice-dollar',
            'submenu' => [
                [
                    'text' => 'All Invoices',
                    'url' => 'invoices',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Create Invoice',
                    'url' => 'invoices/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
                [
                    'text' => 'Sent Invoices',
                    'url' => 'invoices?status=sent',
                    'icon' => 'fas fa-fw fa-paper-plane',
                    'label_color' => 'info',
                ],
                [
                    'text' => 'Paid Invoices',
                    'url' => 'invoices?payment_status=paid',
                    'icon' => 'fas fa-fw fa-check-circle',
                    'label_color' => 'success',
                ],
                [
                    'text' => 'Overdue Invoices',
                    'url' => 'invoices?overdue=1',
                    'icon' => 'fas fa-fw fa-exclamation-triangle',
                    'label_color' => 'danger',
                ],
            ],
        ],
        [
            'text' => 'Quotations',
            'icon' => 'fas fa-fw fa-file-contract',
            'submenu' => [
                [
                    'text' => 'All Quotations',
                    'url' => 'quotations',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Create Quotation',
                    'url' => 'quotations/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
                [
                    'text' => 'Sent Quotations',
                    'url' => 'quotations?status=sent',
                    'icon' => 'fas fa-fw fa-paper-plane',
                    'label_color' => 'info',
                ],
                [
                    'text' => 'Accepted Quotations',
                    'url' => 'quotations?status=accepted',
                    'icon' => 'fas fa-fw fa-check-circle',
                    'label_color' => 'success',
                ],
            ],
        ],
        
        ['header' => 'BUSINESS MANAGEMENT'],
        [
            'text' => 'Clients',
            'icon' => 'fas fa-fw fa-users',
            'submenu' => [
                [
                    'text' => 'All Clients',
                    'url' => 'clients',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Add Client',
                    'url' => 'clients/create',
                    'icon' => 'fas fa-fw fa-user-plus',
                ],
            ],
        ],
        [
            'text' => 'Projects',
            'icon' => 'fas fa-fw fa-project-diagram',
            'submenu' => [
                [
                    'text' => 'Project Dashboard',
                    'url' => 'projects-dashboard',
                    'icon' => 'fas fa-fw fa-tachometer-alt',
                ],
                [
                    'text' => 'All Projects',
                    'url' => 'projects',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Create Project',
                    'url' => 'projects/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
            ],
        ],
        [
            'text' => 'Services',
            'icon' => 'fas fa-fw fa-cogs',
            'submenu' => [
                [
                    'text' => 'All Services',
                    'url' => 'items',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Add Service',
                    'url' => 'items/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
            ],
        ],
        [
            'text' => 'Domain Management',
            'icon' => 'fas fa-fw fa-globe',
            'submenu' => [
                [
                    'text' => 'All Domains',
                    'url' => 'domains',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Add Domain',
                    'url' => 'domains/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
                [
                    'text' => 'Expiring Soon',
                    'url' => 'domains?expiry_filter=expiring_soon',
                    'icon' => 'fas fa-fw fa-exclamation-triangle',
                    'label_color' => 'warning',
                ],
                [
                    'text' => 'Expired Domains',
                    'url' => 'domains?expiry_filter=expired',
                    'icon' => 'fas fa-fw fa-times-circle',
                    'label_color' => 'danger',
                ],
            ],
        ],
        
        ['header' => 'FINANCIAL MANAGEMENT'],
        [
            'text' => 'Expenses',
            'icon' => 'fas fa-fw fa-receipt',
            'submenu' => [
                [
                    'text' => 'All Expenses',
                    'url' => 'expenses',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Create Expense',
                    'url' => 'expenses/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
                [
                    'text' => 'Approved Expenses',
                    'url' => 'expenses?status=approved',
                    'icon' => 'fas fa-fw fa-check-circle',
                    'label_color' => 'success',
                ],
                [
                    'text' => 'Paid Expenses',
                    'url' => 'expenses?payment_status=paid',
                    'icon' => 'fas fa-fw fa-check-double',
                    'label_color' => 'success',
                ],
            ],
        ],
        
        ['header' => 'ANALYTICS & REPORTS'],
        [
            'text' => 'Quick Reports',
            'icon' => 'fas fa-fw fa-chart-line',
            'submenu' => [
                [
                    'text' => 'Revenue Report',
                    'url' => 'reports/quick/revenue',
                    'icon' => 'fas fa-fw fa-dollar-sign',
                ],
                [
                    'text' => 'Monthly Report',
                    'url' => 'reports/quick/monthly',
                    'icon' => 'fas fa-fw fa-calendar-alt',
                ],
                [
                    'text' => 'Yearly Report',
                    'url' => 'reports/quick/yearly',
                    'icon' => 'fas fa-fw fa-calendar',
                ],
                [
                    'text' => 'Client Report',
                    'url' => 'reports/quick/client',
                    'icon' => 'fas fa-fw fa-user-friends',
                ],
                [
                    'text' => 'Cash Flow',
                    'url' => 'reports/quick/cash-flow',
                    'icon' => 'fas fa-fw fa-chart-area',
                ],
                [
                    'text' => 'Top Clients',
                    'url' => 'reports/quick/top-clients',
                    'icon' => 'fas fa-fw fa-trophy',
                ],
                [
                    'text' => 'Overdue Analysis',
                    'url' => 'reports/quick/overdue-invoices',
                    'icon' => 'fas fa-fw fa-exclamation-triangle',
                ],
            ],
        ],
        [
            'text' => 'Custom Reports',
            'icon' => 'fas fa-fw fa-chart-bar',
            'submenu' => [
                [
                    'text' => 'All Reports',
                    'url' => 'reports',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Create Report',
                    'url' => 'reports/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
                [
                    'text' => 'Revenue Reports',
                    'url' => 'reports?type=revenue',
                    'icon' => 'fas fa-fw fa-dollar-sign',
                ],
                [
                    'text' => 'Client Reports',
                    'url' => 'reports?type=client',
                    'icon' => 'fas fa-fw fa-user-friends',
                ],
                [
                    'text' => 'Time-based Reports',
                    'url' => 'reports?type=monthly',
                    'icon' => 'fas fa-fw fa-clock',
                ],
            ],
        ],
        
        ['header' => 'POINT OF SALE'],
        [
            'text' => 'POS Terminal',
            'url' => 'pos/terminal',
            'icon' => 'fas fa-fw fa-cash-register',
            'label_color' => 'success',
        ],
        [
            'text' => 'POS Sales',
            'url' => 'pos',
            'icon' => 'fas fa-fw fa-shopping-bag',
        ],
        [
            'text' => 'Daily Summary',
            'url' => 'pos-daily-summary',
            'icon' => 'fas fa-fw fa-calendar-day',
        ],
        
        ['header' => 'PURCHASES & SUPPLIERS'],
        [
            'text' => 'Purchases',
            'icon' => 'fas fa-fw fa-truck-loading',
            'submenu' => [
                [
                    'text' => 'All Purchases',
                    'url' => 'purchases',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Create Purchase',
                    'url' => 'purchases/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
            ],
        ],
        [
            'text' => 'Suppliers',
            'icon' => 'fas fa-fw fa-truck',
            'submenu' => [
                [
                    'text' => 'All Suppliers',
                    'url' => 'suppliers',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Add Supplier',
                    'url' => 'suppliers/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
            ],
        ],
        
        ['header' => 'INVENTORY'],
        [
            'text' => 'Stock Overview',
            'url' => 'inventory',
            'icon' => 'fas fa-fw fa-cubes',
        ],
        [
            'text' => 'Stock Adjustments',
            'url' => 'inventory/adjustments',
            'icon' => 'fas fa-fw fa-sliders-h',
        ],
        [
            'text' => 'Stock Movements',
            'url' => 'inventory/movements',
            'icon' => 'fas fa-fw fa-exchange-alt',
        ],
        [
            'text' => 'Low Stock Alerts',
            'url' => 'inventory/low-stock',
            'icon' => 'fas fa-fw fa-exclamation-triangle',
            'label_color' => 'danger',
        ],
        
        ['header' => 'ACCOUNTING'],
        [
            'text' => 'Chart of Accounts',
            'icon' => 'fas fa-fw fa-book',
            'submenu' => [
                [
                    'text' => 'All Accounts',
                    'url' => 'accounts',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'New Account',
                    'url' => 'accounts/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
            ],
        ],
        [
            'text' => 'Journal Entries',
            'icon' => 'fas fa-fw fa-journal-whills',
            'submenu' => [
                [
                    'text' => 'All Entries',
                    'url' => 'journal-entries',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'New Entry',
                    'url' => 'journal-entries/create',
                    'icon' => 'fas fa-fw fa-plus',
                ],
            ],
        ],
        
        ['header' => 'FINANCIAL REPORTS'],
        [
            'text' => 'Trial Balance',
            'url' => 'financial-reports/trial-balance',
            'icon' => 'fas fa-fw fa-balance-scale',
        ],
        [
            'text' => 'Income Statement',
            'url' => 'financial-reports/income-statement',
            'icon' => 'fas fa-fw fa-chart-line',
        ],
        [
            'text' => 'Balance Sheet',
            'url' => 'financial-reports/balance-sheet',
            'icon' => 'fas fa-fw fa-file-invoice',
        ],
        [
            'text' => 'Cash Flow',
            'url' => 'financial-reports/cash-flow',
            'icon' => 'fas fa-fw fa-money-bill-wave',
        ],
        [
            'text' => 'General Ledger',
            'url' => 'financial-reports/general-ledger',
            'icon' => 'fas fa-fw fa-book',
        ],
        [
            'text' => 'Journal Report',
            'url' => 'financial-reports/journal-report',
            'icon' => 'fas fa-fw fa-scroll',
        ],
        
        ['header' => 'SYSTEM'],
        [
            'text' => 'Settings',
            'url' => 'settings',
            'icon' => 'fas fa-fw fa-cog',
        ],
        
        ['header' => 'ACCESS CONTROL'],
        [
            'text' => 'Users',
            'url' => 'users',
            'icon' => 'fas fa-fw fa-users',
            'can' => 'view-users',
        ],
        [
            'text' => 'Activity Logs',
            'url' => 'activity-logs',
            'icon' => 'fas fa-fw fa-history',
            'can' => 'view-activity-logs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'jQuery' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//code.jquery.com/jquery-3.6.0.min.js',
                ],
            ],
        ],
        'Bootstrap' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
                ],
            ],
        ],
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
