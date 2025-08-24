@extends('adminlte::page')

@section('title', 'Quotation Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quotation {{ $quotation->quotation_number }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
                    <li class="breadcrumb-item active">{{ $quotation->quotation_number }}</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Quotation Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-2"></i>
                            Quotation {{ $quotation->quotation_number }}
                        </h3>
                        <div class="card-tools">
                            @if(!in_array($quotation->status, ['accepted', 'converted']))
                                <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('quotations.preview', $quotation) }}" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                            <a href="{{ route('quotations.pdf', $quotation) }}" class="btn btn-secondary btn-sm" target="_blank">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                            @if($quotation->status === 'draft')
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#emailModal">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            @else
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#emailModal">
                                    <i class="fas fa-envelope"></i> Email
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-user mr-2"></i>Client Information</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="30%">Client:</th>
                                        <td>{{ $quotation->client->display_name }}</td>
                                    </tr>
                                    @if($quotation->client->company_name && $quotation->client->company_name !== $quotation->client->name)
                                        <tr>
                                            <th>Company:</th>
                                            <td>{{ $quotation->client->company_name }}</td>
                                        </tr>
                                    @endif
                                    @if($quotation->client->email)
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $quotation->client->email }}</td>
                                        </tr>
                                    @endif
                                    @if($quotation->client->phone)
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ $quotation->client->phone }}</td>
                                        </tr>
                                    @endif
                                    @if($quotation->client->full_address)
                                        <tr>
                                            <th>Address:</th>
                                            <td>{{ $quotation->client->full_address }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-info-circle mr-2"></i>Quotation Details</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="30%">Quotation #:</th>
                                        <td>{{ $quotation->quotation_number }}</td>
                                    </tr>
                                    @if($quotation->reference_number)
                                        <tr>
                                            <th>Reference:</th>
                                            <td>{{ $quotation->reference_number }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ $quotation->quotation_date->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Valid Until:</th>
                                        <td>
                                            {{ $quotation->valid_until->format('M d, Y') }}
                                            @if($quotation->is_expired)
                                                <span class="badge badge-danger ml-2">Expired</span>
                                            @elseif($quotation->days_until_expiry <= 3)
                                                <span class="badge badge-warning ml-2">Expires Soon</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'draft' => 'secondary',
                                                    'sent' => 'info',
                                                    'viewed' => 'primary',
                                                    'accepted' => 'success',
                                                    'rejected' => 'danger',
                                                    'expired' => 'warning',
                                                    'converted' => 'dark'
                                                ];
                                                $statusClass = $statusClasses[$quotation->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }}">
                                                {{ ucfirst($quotation->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Currency:</th>
                                        <td>{{ $quotation->currency }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if(in_array($quotation->status, ['sent', 'viewed']))
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Quotation Actions</h5>
                            <form method="POST" action="{{ route('quotations.accept', $quotation) }}" style="display: inline-block;" class="mr-2">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Mark this quotation as accepted?')">
                                    <i class="fas fa-check"></i> Accept Quotation
                                </button>
                            </form>
                            <form method="POST" action="{{ route('quotations.reject', $quotation) }}" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Mark this quotation as rejected?')">
                                    <i class="fas fa-times"></i> Reject Quotation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($quotation->status === 'accepted')
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Convert to Invoice</h5>
                            <p class="text-muted">This quotation has been accepted. You can now convert it to an invoice.</p>
                            <form method="POST" action="{{ route('quotations.convert-to-invoice', $quotation) }}" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Convert this quotation to an invoice?')">
                                    <i class="fas fa-exchange-alt"></i> Convert to Invoice
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Items -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Quotation Items
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th width="10%" class="text-center">Qty</th>
                                        <th width="15%" class="text-right">Unit Price</th>
                                        <th width="15%" class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotation->quotationItems as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->item_name }}</strong>
                                                @if($item->item_description)
                                                    <br><small class="text-muted">{{ $item->item_description }}</small>
                                                @endif
                                                @if($item->item_sku)
                                                    <br><small class="text-info">SKU: {{ $item->item_sku }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->quantity }} {{ $item->unit_type }}</td>
                                            <td class="text-right">{{ $settings->formatCurrency($item->unit_price) }}</td>
                                            <td class="text-right">{{ $settings->formatCurrency($item->total_amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calculator mr-2"></i>
                            Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>Subtotal:</th>
                                <td class="text-right">{{ $settings->formatCurrency($quotation->subtotal) }}</td>
                            </tr>
                            @if($quotation->discount_amount > 0)
                                <tr>
                                    <th>
                                        Discount:
                                        @if($quotation->discount_type === 'percentage')
                                            ({{ $quotation->discount_value }}%)
                                        @endif
                                    </th>
                                    <td class="text-right text-success">-{{ $settings->formatCurrency($quotation->discount_amount) }}</td>
                                </tr>
                            @endif
                            @if($quotation->tax_amount > 0)
                                <tr>
                                    <th>Tax:</th>
                                    <td class="text-right">{{ $settings->formatCurrency($quotation->tax_amount) }}</td>
                                </tr>
                            @endif
                            <tr class="table-active">
                                <th>Total:</th>
                                <th class="text-right">{{ $settings->formatCurrency($quotation->total_amount) }}</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($quotation->notes)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sticky-note mr-2"></i>
                                Notes
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>{{ $quotation->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">
                        <i class="fas fa-envelope"></i> Send Quotation via Email
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('quotations.send-email', $quotation) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $quotation->client->email) }}" 
                                   required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject', 'Quotation ' . $quotation->quotation_number . ' from ' . ($settings->company_name ?? 'Your Company')) }}" 
                                   required>
                            @error('subject')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="5" 
                                      placeholder="Optional personal message to include in the email">{{ old('message', 'Dear ' . $quotation->client->display_name . ',\n\nPlease find attached your quotation for review.\n\nThis quotation is valid until ' . $quotation->valid_until->format('M d, Y') . '.\n\nIf you have any questions, please don\'t hesitate to contact us.\n\nBest regards,\n' . ($settings->company_name ?? 'Your Company')) }}</textarea>
                            @error('message')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="send_copy" name="send_copy" value="1">
                            <label class="form-check-label" for="send_copy">
                                Send a copy to me ({{ $settings->company_email ?? auth()->user()->email }})
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th {
            border-top: none;
        }
        .card-title i {
            color: #007bff;
        }
    </style>
@stop

@section('js')
@stop