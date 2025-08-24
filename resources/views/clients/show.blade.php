@extends('adminlte::page')

@section('title', 'Client Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Client Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                <li class="breadcrumb-item active">{{ $client->display_name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ $client->display_name }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $client->status === 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($client->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td>{{ $client->name }}</td>
                                </tr>
                                @if($client->company_name)
                                <tr>
                                    <td><strong>Company:</strong></td>
                                    <td>{{ $client->company_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><a href="mailto:{{ $client->email }}">{{ $client->email }}</a></td>
                                </tr>
                                @if($client->phone)
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><a href="tel:{{ $client->phone }}">{{ $client->phone }}</a></td>
                                </tr>
                                @endif
                                @if($client->website)
                                <tr>
                                    <td><strong>Website:</strong></td>
                                    <td><a href="{{ $client->website }}" target="_blank">{{ $client->website }}</a></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Address Information</h5>
                            @if($client->full_address)
                                <address>
                                    {{ $client->full_address }}
                                </address>
                            @else
                                <p class="text-muted">No address provided</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($client->tax_number || $client->business_type)
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Business Information</h5>
                            <table class="table table-borderless">
                                @if($client->tax_number)
                                <tr>
                                    <td width="150"><strong>Tax Number:</strong></td>
                                    <td>{{ $client->tax_number }}</td>
                                </tr>
                                @endif
                                @if($client->business_type)
                                <tr>
                                    <td><strong>Business Type:</strong></td>
                                    <td>{{ $client->business_type }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if($client->notes)
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Notes</h5>
                            <p>{{ $client->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Client
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display: inline;" class="float-right">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this client? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete Client
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Quick Stats</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Invoices</span>
                            <span class="info-box-number">{{ $stats['total_invoices'] }}</span>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                            <span class="info-box-number">{{ $settings->formatCurrency($stats['total_revenue']) }}</span>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Amount</span>
                            <span class="info-box-number">{{ $settings->formatCurrency($stats['pending_amount']) }}</span>
                        </div>
                    </div>
                    
                    @if($stats['overdue_amount'] > 0)
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Overdue Amount</span>
                            <span class="info-box-number">{{ $settings->formatCurrency($stats['overdue_amount']) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Client Since</h3>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-calendar"></i> {{ $client->created_at->format('F d, Y') }}</p>
                    <p><i class="fas fa-clock"></i> {{ $client->created_at->diffForHumans() }}</p>
                    <p><i class="fas fa-edit"></i> Last updated: {{ $client->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
    {{-- Add here extra JavaScript --}}
@stop