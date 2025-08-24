@extends('adminlte::page')

@section('title', 'Edit Item')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Item: {{ $item->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.show', $item) }}">{{ $item->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Item Information</h3>
                    </div>
                    <form action="{{ route('items.update', $item) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Item Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $item->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="active" {{ old('status', $item->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $item->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="discontinued" {{ old('status', $item->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                               id="category" name="category" value="{{ old('category', $item->category) }}" 
                                               list="categories" placeholder="e.g., Consulting, Design">
                                        <datalist id="categories">
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}">
                                            @endforeach
                                        </datalist>
                                        @error('category')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit_type">Unit Type <span class="text-danger">*</span></label>
                                        <select class="form-control @error('unit_type') is-invalid @enderror" 
                                                id="unit_type" name="unit_type" required>
                                            <option value="hour" {{ old('unit_type', $item->unit_type) == 'hour' ? 'selected' : '' }}>Hour</option>
                                            <option value="day" {{ old('unit_type', $item->unit_type) == 'day' ? 'selected' : '' }}>Day</option>
                                            <option value="week" {{ old('unit_type', $item->unit_type) == 'week' ? 'selected' : '' }}>Week</option>
                                            <option value="month" {{ old('unit_type', $item->unit_type) == 'month' ? 'selected' : '' }}>Month</option>
                                            <option value="project" {{ old('unit_type', $item->unit_type) == 'project' ? 'selected' : '' }}>Project</option>
                                            <option value="consultation" {{ old('unit_type', $item->unit_type) == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                            <option value="piece" {{ old('unit_type', $item->unit_type) == 'piece' ? 'selected' : '' }}>Piece</option>
                                        </select>
                                        @error('unit_type')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Service Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Detailed description of the service">{{ old('description', $item->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Service is default -->
                            <input type="hidden" name="is_service" value="1">

                            <!-- Pricing Information -->
                            <h5 class="mt-4 mb-3">Pricing Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="unit_price">Unit Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $settings->currency_symbol ?? 'UGX' }}</span>
                                            </div>
                                            <input type="number" class="form-control @error('unit_price') is-invalid @enderror" 
                                                   id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" 
                                                   step="0.01" min="0" required>
                                        </div>
                                        @error('unit_price')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cost_price">Cost Price</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $settings->currency_symbol ?? 'UGX' }}</span>
                                            </div>
                                            <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                                   id="cost_price" name="cost_price" value="{{ old('cost_price', $item->cost_price) }}" 
                                                   step="0.01" min="0">
                                        </div>
                                        <small class="form-text text-muted">
                                            For profit margin calculation
                                            @if($item->profit_margin)
                                                <span class="text-info">(Current margin: {{ $item->profit_margin }}%)</span>
                                            @endif
                                        </small>
                                        @error('cost_price')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Information -->
                            <h5 class="mt-4 mb-3">Tax Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_taxable" name="is_taxable" 
                                               value="1" {{ old('is_taxable', $item->is_taxable) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_taxable">
                                            This service is taxable
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_rate">Tax Rate (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                                                   id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $item->tax_rate) }}" 
                                                   step="0.01" min="0" max="100">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        @if($item->is_taxable && $item->tax_rate)
                                            <small class="form-text text-muted text-info">
                                                Price with tax: {{ $item->formatted_price_with_tax }}
                                            </small>
                                        @endif
                                        @error('tax_rate')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <h5 class="mt-4 mb-3">Additional Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_url">Image URL</label>
                                        <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                                               id="image_url" name="image_url" value="{{ old('image_url', $item->image_url) }}" 
                                               placeholder="https://example.com/image.jpg">
                                        @error('image_url')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Any additional notes about this service">{{ old('notes', $item->notes) }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Service
                            </button>
                            <a href="{{ route('items.show', $item) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Service
                            </a>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Show/hide tax rate field based on is_taxable checkbox
            function toggleTaxFields() {
                if ($('#is_taxable').is(':checked')) {
                    $('#tax_rate').closest('.form-group').show();
                } else {
                    $('#tax_rate').closest('.form-group').hide();
                    $('#tax_rate').val('');
                }
            }

            // Initial state
            toggleTaxFields();

            // Event listeners
            $('#is_taxable').change(toggleTaxFields);

            // Auto-calculate profit margin when cost price or unit price changes
            function calculateProfitMargin() {
                var unitPrice = parseFloat($('#unit_price').val()) || 0;
                var costPrice = parseFloat($('#cost_price').val()) || 0;
                
                if (costPrice > 0) {
                    var profit = unitPrice - costPrice;
                    var margin = (profit / costPrice) * 100;
                    $('#profit_margin').text(margin.toFixed(2) + '%');
                } else {
                    $('#profit_margin').text('N/A');
                }
            }

            $('#unit_price, #cost_price').on('input', calculateProfitMargin);
        });
    </script>
@stop