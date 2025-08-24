@extends('adminlte::master')

@section('adminlte_css_pre')
    <style>
        .error-page {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .error-box {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }
        
        .error-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .error-logo a {
            color: #fff;
            text-decoration: none;
            font-size: 2rem;
            font-weight: 300;
        }
        
        .error-logo img {
            max-width: 100px;
            margin-bottom: 1rem;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .card-header {
            background: #fff;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.5rem;
            text-align: center;
            border-bottom: none;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 1rem 0;
        }
        
        .error-description {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 5px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
@stop

@section('body')
    <div class="error-page">
        <div class="error-box">
            <div class="error-logo">
                @if(config('adminlte.logo_img'))
                    <img src="{{ asset(config('adminlte.logo_img')) }}" alt="{{ config('adminlte.logo_img_alt') }}">
                @endif
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="error-title">{{ __('Registration Disabled') }}</h3>
                </div>
                
                <div class="card-body">
                    <p class="error-description">
                        {{ __('Registration is disabled for this application. Only administrators can add new users.') }}
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop