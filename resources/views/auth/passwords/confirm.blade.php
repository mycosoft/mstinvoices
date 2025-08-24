@extends('adminlte::master')

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        .login-page {
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
        
        .login-box {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .login-logo a {
            color: #fff;
            text-decoration: none;
            font-size: 2rem;
            font-weight: 300;
        }
        
        .login-logo img {
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
        
        .form-control {
            border-radius: 5px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        
        .input-group-text {
            border-radius: 5px 0 0 5px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-right: none;
        }
        
        .input-group .form-control {
            border-radius: 0 5px 5px 0;
            border-left: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 5px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1rem;
        }
        
        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .login-box-msg {
            padding: 0;
            margin: 0 0 1.5rem 0;
            text-align: center;
            color: #495057;
            font-weight: 400;
        }
        
        .help-block {
            text-align: center;
            margin: 1rem 0;
            color: #6c757d;
        }
    </style>
@stop

@php
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');
    $dashboardUrl = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home');

    if (config('adminlte.use_route_url', false)) {
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
        $dashboardUrl = $dashboardUrl ? route($dashboardUrl) : '';
    } else {
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
        $dashboardUrl = $dashboardUrl ? url($dashboardUrl) : '';
    }
@endphp

@section('body')
    <div class="login-box">
        <div class="login-logo">
            @if(config('adminlte.logo_img'))
                <img src="{{ asset(config('adminlte.logo_img')) }}" alt="{{ config('adminlte.logo_img_alt') }}">
            @endif
            {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="login-box-msg">{{ __('adminlte::adminlte.confirm_password') }}</h3>
            </div>
            
            <div class="card-body">
                <div class="help-block">
                    {{ __('adminlte::adminlte.confirm_password_message') }}
                </div>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    {{-- Password field --}}
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <input id="password" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="{{ __('adminlte::adminlte.password') }}" required autofocus>
                        
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Confirm password button --}}
                    <button type="submit" class="btn btn-primary">
                        {{ __('adminlte::adminlte.confirm_password') }}
                    </button>
                </form>
            </div>
            
            <div class="card-footer">
                <div class="auth-footer">
                    {{-- Password reset link --}}
                    @if($passResetUrl)
                        <p class="mb-1">
                            <a href="{{ $passResetUrl }}">
                                {{ __('adminlte::adminlte.i_forgot_my_password') }}
                            </a>
                        </p>
                    @endif
                    
                    <p class="mb-0">
                        <a href="{{ $dashboardUrl }}">
                            <i class="fas fa-arrow-left"></i> {{ __('adminlte::adminlte.back_to_home') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop