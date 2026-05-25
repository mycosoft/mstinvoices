@extends('adminlte::page')

@section('title', 'Invoice Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Invoice Details</h1>
        <div class="btn-group">
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <button type="button" class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('invoices.preview', $invoice) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-eye"></i> Preview
            </a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#emailModal">
                <i class="fas fa-envelope"></i> Send Email
            </button>
        </div>
    </div>
@stop

@section('content')
<!-- Success and Error Messages -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-check"></i> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-ban"></i> {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-ban"></i> Please fix the following errors:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <!-- Invoice Header -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice"></i> {{ $invoice->invoice_number }}
                    <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : ($invoice->status === 'pending' ? 'warning' : 'info')) }} ml-2">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </h3>
                <div class="card-tools">
                    @if($invoice->status !== 'sent' && $invoice->status !== 'paid')
                        <form method="POST" action="{{ route('invoices.mark-sent', $invoice) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-paper-plane"></i> Mark as Sent
                            </button>
                        </form>
                    @endif
                    
                    @if($invoice->status !== 'paid')
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#paymentModal">
                            <i class="fas fa-money-bill"></i> Add Payment
                        </button>
                    @endif
                    
                    
                    <form method="POST" action="{{ route('invoices.duplicate', $invoice) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="fas fa-copy"></i> Duplicate
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Company Info -->
                    <div class="col-md-6">
                        <h5><strong>From:</strong></h5>
                        <address>
                            @if($settings->company_name)
                                <strong>{{ $settings->company_name }}</strong><br>
                            @else
                                <strong>{{ $invoice->user->name }}</strong><br>
                            @endif
                            
                            @if($settings->company_address)
                                {{ $settings->company_address }}<br>
                            @endif
                            
                            @if($settings->company_city || $settings->company_state || $settings->company_postal_code)
                                @if($settings->company_city)
                                    {{ $settings->company_city }}
                                @endif
                                @if($settings->company_city && ($settings->company_state || $settings->company_postal_code))
                                    , 
                                @endif
                                @if($settings->company_state)
                                    {{ $settings->company_state }}
                                @endif
                                @if($settings->company_state && $settings->company_postal_code)
                                     
                                @endif
                                @if($settings->company_postal_code)
                                    {{ $settings->company_postal_code }}
                                @endif
                                <br>
                            @endif
                            
                            @if($settings->company_country)
                                {{ $settings->company_country }}<br>
                            @endif
                            
                            @if($settings->company_phone)
                                Phone: {{ $settings->company_phone }}<br>
                            @endif
                            
                            @if($settings->company_email)
                                Email: {{ $settings->company_email }}<br>
                            @endif
                            
                            @if($settings->company_website)
                                Website: {{ $settings->company_website }}<br>
                            @endif
                            
                            @if($settings->company_tax_number)
                                Tax Number: {{ $settings->company_tax_number }}<br>
                            @endif
                            
                            @if($settings->company_registration_number)
                                Registration Number: {{ $settings->company_registration_number }}<br>
                            @endif
                        </address>
                    </div>
                    
                    <!-- Client Info -->
                    <div class="col-md-6">
                        <h5><strong>Bill To:</strong></h5>
                        <address>
                            <strong>{{ $invoice->client->display_name }}</strong><br>
                            @if($invoice->client->company_name && $invoice->client->company_name !== $invoice->client->display_name)
                                {{ $invoice->client->company_name }}<br>
                            @endif
                            @if($invoice->client->full_address)
                                {{ $invoice->client->full_address }}<br>
                            @endif
                            @if($invoice->client->email)
                                Email: {{ $invoice->client->email }}<br>
                            @endif
                            @if($invoice->client->phone)
                                Phone: {{ $invoice->client->phone }}
                            @endif
                        </address>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Invoice Date:</strong></td>
                                <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Due Date:</strong></td>
                                <td>{{ $invoice->due_date->format('M d, Y') }}
                                    @if($invoice->is_overdue)
                                        <span class="badge badge-danger ml-1">{{ abs($invoice->days_until_due) }} days overdue</span>
                                    @elseif($invoice->days_until_due <= 7 && $invoice->days_until_due > 0)
                                        <span class="badge badge-warning ml-1">{{ $invoice->days_until_due }} days left</span>
                                    @endif
                                </td>
                            </tr>
                            @if($invoice->reference_number)
                            <tr>
                                <td><strong>Reference:</strong></td>
                                <td>{{ $invoice->reference_number }}</td>
                            </tr>
                            @endif
                            @if($invoice->project)
                            <tr>
                                <td><strong>Project:</strong></td>
                                <td>
                                    <a href="{{ route('projects.show', $invoice->project) }}" class="text-primary">
                                        <i class="fas fa-project-diagram"></i> {{ $invoice->project->name }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-sm">
                            @if($invoice->sent_date)
                            <tr>
                                <td><strong>Sent Date:</strong></td>
                                <td>{{ $invoice->sent_date->format('M d, Y') }}</td>
                            </tr>
                            @endif
                            @if($invoice->paid_date)
                            <tr>
                                <td><strong>Paid Date:</strong></td>
                                <td>{{ $invoice->paid_date->format('M d, Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Payment Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $invoice->payment_status === 'paid' ? 'success' : ($invoice->payment_status === 'partial' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($invoice->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Invoice Items -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Invoice Items</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Tax</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->invoiceItems as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->item_name }}</strong>
                                    @if($item->item_sku)
                                        <br><small class="text-muted">SKU: {{ $item->item_sku }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->item_description }}</td>
                                <td class="text-right">{{ $item->quantity }} {{ $item->unit_type }}</td>
                                <td class="text-right">{{ $settings->formatCurrency($item->unit_price) }}</td>
                                <td class="text-right">
                                    @if($item->discount_amount > 0)
                                        {{ $settings->formatCurrency($item->discount_amount) }}
                                        @if($item->discount_percentage > 0)
                                            <br><small class="text-muted">({{ $item->discount_percentage }}%)</small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($item->tax_amount > 0)
                                        {{ $settings->formatCurrency($item->tax_amount) }}
                                        @if($item->tax_percentage > 0)
                                            <br><small class="text-muted">({{ $item->tax_percentage }}%)</small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right"><strong>{{ $settings->formatCurrency($item->total_amount) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Invoice Totals -->
    <div class="col-md-8">
        @if($invoice->notes)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sticky-note"></i> Notes</h3>
            </div>
            <div class="card-body">
                <p>{{ $invoice->notes }}</p>
            </div>
        </div>
        @endif
        
        @if($invoice->terms)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-contract"></i> Terms & Conditions</h3>
            </div>
            <div class="card-body">
                <p>{{ $invoice->terms }}</p>
            </div>
        </div>
        @endif
        
        <!-- Payment History -->
        @if($invoice->payments->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Payment History</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Notes</th>
                                <th>Added By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td><strong>{{ $settings->formatCurrency($payment->amount) }}</strong></td>
                                <td>
                                    @if($payment->payment_method == 'cash')
                                        <span class="badge badge-success">Cash Payment</span>
                                    @elseif($payment->payment_method == 'bank_transfer')
                                        <span class="badge badge-info">Bank Transfer</span>
                                    @elseif($payment->payment_method == 'mobile_money')
                                        <span class="badge badge-primary">Mobile Money</span>
                                    @elseif($payment->payment_method == 'cheque')
                                        <span class="badge badge-secondary">Cheque</span>
                                    @endif
                                </td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                                <td>{{ $payment->creator->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator"></i> Invoice Summary</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">{{ $settings->formatCurrency($invoice->subtotal) }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                    <tr>
                        <td>
                            Discount:
                            @if($invoice->discount_percentage > 0)
                                <small class="text-muted">({{ $invoice->discount_percentage }}%)</small>
                            @endif
                        </td>
                        <td class="text-right text-success">-{{ $settings->formatCurrency($invoice->discount_amount) }}</td>
                    </tr>
                    @endif
                    @if($invoice->tax_amount > 0)
                    <tr>
                        <td>
                            Tax:
                            @if($invoice->tax_percentage > 0)
                                <small class="text-muted">({{ $invoice->tax_percentage }}%)</small>
                            @endif
                        </td>
                        <td class="text-right">{{ $settings->formatCurrency($invoice->tax_amount) }}</td>
                    </tr>
                    @endif
                    @if($invoice->shipping_amount > 0)
                    <tr>
                        <td>Shipping:</td>
                        <td class="text-right">{{ $settings->formatCurrency($invoice->shipping_amount) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <td><strong>Total:</strong></td>
                        <td class="text-right"><strong>{{ $settings->formatCurrency($invoice->total_amount) }}</strong></td>
                    </tr>
                    @if($invoice->paid_amount > 0)
                    <tr>
                        <td>Amount Paid:</td>
                        <td class="text-right text-success">{{ $settings->formatCurrency($invoice->paid_amount) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Balance Due:</strong></td>
                        <td class="text-right"><strong class="text-{{ $invoice->balance_due > 0 ? 'danger' : 'success' }}">{{ $settings->formatCurrency($invoice->balance_due) }}</strong></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">
                    <i class="fas fa-envelope"></i> Send Invoice via Email
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('invoices.send-email', $invoice) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $invoice->client->email) }}" 
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
                               value="{{ old('subject', 'Invoice ' . $invoice->invoice_number . ' from ' . ($settings->company_name ?? 'Invoice Generator')) }}" 
                               required>
                        @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="message">Message (Optional)</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" 
                                  name="message" 
                                  rows="4" 
                                  placeholder="Add a personal message to your client...">{{ old('message', 'Please find your invoice attached. Thank you for your business!') }}</textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="send_copy" 
                               name="send_copy" 
                               value="1" 
                               {{ old('send_copy') ? 'checked' : '' }}>
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('invoices.add-payment', $invoice) }}">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add Payment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="payment_amount">Payment Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ $settings->currency_symbol }}</span>
                            </div>
                            <input type="number" class="form-control" id="payment_amount" name="payment_amount" 
                                   step="0.01" max="{{ $invoice->balance_due }}" required>
                        </div>
                        <small class="text-muted">Balance due: {{ $settings->formatCurrency($invoice->balance_due) }}</small>
                    </div>
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Please select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer (Equity Bank)</option>
                            <option value="mobile_money">Mobile Money (Airtel / MTN)</option>
                            <option value="cheque">Cheque Payment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_notes">Notes (Optional)</label>
                        <textarea class="form-control" id="payment_notes" name="payment_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" onclick="this.disabled=true; this.form.submit();">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    @media print {
        .btn, .card-tools, .content-header, .main-sidebar, .main-header, .control-sidebar {
            display: none !important;
        }
        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@stop

@section('js')
<script>
// Auto-fill payment amount with balance due
document.getElementById('payment_amount').value = {{ $invoice->balance_due }};

// Prevent double form submission
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.querySelector('form[action*="add-payment"]');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Payment...';
        });
    }
});
</script>
@stop