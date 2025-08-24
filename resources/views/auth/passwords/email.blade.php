<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('adminlte.title', 'MST Invoices') }} - Forgot Password</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .input-field {
            background-color: #f0f5ff;
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }
        
        .card-container {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .logo-container {
            transition: transform 0.3s ease;
        }
        
        .logo-container:hover {
            transform: scale(1.05);
        }
    </style>
</head>
@php
    $passEmailUrl = View::getSection('password_email_url') ?? config('adminlte.password_email_url', 'password/email');

    if (config('adminlte.use_route_url', false)) {
        $passEmailUrl = $passEmailUrl ? route($passEmailUrl) : '';
    } else {
        $passEmailUrl = $passEmailUrl ? url($passEmailUrl) : '';
    }
@endphp
<body class="bg-cover bg-center bg-no-repeat bg-fixed" 
      style="background-image: url('https://images.unsplash.com/photo-1557683316-973673baf926?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2129&q=80');">
    
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <!-- Logo Card -->
        <div class="bg-white rounded-xl p-5 mb-4 w-full max-w-md logo-container card-container">
            <div class="flex justify-center">
                @if(config('adminlte.logo_img'))
                    <img src="{{ asset(config('adminlte.logo_img')) }}" 
                         alt="{{ config('adminlte.logo_img_alt') }}" 
                         class="h-16 object-contain">
                @else
                    <div class="w-32 h-16 flex items-center justify-center">
                        <span class="text-3xl font-bold">{{ config('adminlte.title', 'MST Invoices') }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Password Reset Request Card -->
        <div class="bg-white rounded-xl p-8 w-full max-w-md card-container">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Forgot Password</h1>
                <p class="text-gray-600 mt-2">{{ __('adminlte::adminlte.password_reset_message') }}</p>
            </div>
            
            @if(session('status'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif
            
            <form action="{{ $passEmailUrl }}" method="post">
                @csrf
                
                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">
                        {{ __('adminlte::adminlte.email') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" 
                               name="email" 
                               id="email"
                               class="input-field w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 text-gray-700 focus:border-blue-500"
                               value="{{ old('email') }}" 
                               placeholder="email@example.com" 
                               autofocus>
                    </div>
                    @error('email')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="mb-6">
                    <button type="submit" 
                            class="btn-primary w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white focus:outline-none">
                        <i class="fas fa-share-square mr-2"></i> {{ __('adminlte::adminlte.send_password_reset_link') }}
                    </button>
                </div>
                
                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex justify-center items-center">
                        <i class="fas fa-arrow-left mr-2"></i> {{ __('adminlte::adminlte.back_to_login') }}
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-white/70 text-sm">
                &copy; {{ date('Y') }} {{ config('adminlte.title', 'MST Invoices') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>