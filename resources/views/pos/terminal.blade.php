@extends('adminlte::page')

@section('title', 'POS Terminal')

@section('content_header')
    <h1>POS Terminal</h1>
@stop

@section('content')
<div class="row" id="pos-app">
    <!-- Left: Products Panel -->
    <div class="col-lg-7 col-xl-8 mb-3">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-gradient-primary text-white py-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="input-group input-group-sm" style="max-width: 280px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-primary"></i></span>
                        </div>
                        <input type="text" id="product-search" class="form-control" placeholder="Search products...">
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button class="btn btn-sm btn-light category-tab active" data-cat="all">
                            <i class="fas fa-th"></i> All
                        </button>
                        @foreach($categories as $cat)
                            <button class="btn btn-sm btn-outline-light category-tab" data-cat="{{ $cat }}">
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-body p-2" style="max-height: 520px; overflow-y: auto; background: #f4f6f9;">
                <div class="row" id="product-grid">
                    @forelse($items as $category => $categoryItems)
                        @foreach($categoryItems as $item)
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6 mb-2 product-card"
                                 data-category="{{ $item->category ?? 'Uncategorized' }}"
                                 data-name="{{ strtolower($item->name) }}">
                                <div class="product-btn {{ $item->track_inventory && $item->stock_quantity <= 0 ? 'disabled' : '' }}"
                                     data-id="{{ $item->id }}"
                                     data-name="{{ $item->name }}"
                                     data-price="{{ $item->unit_price }}"
                                     data-taxable="{{ $item->is_taxable ? '1' : '0' }}"
                                     data-tax="{{ $item->tax_rate ?? 0 }}"
                                     data-category="{{ $item->category ?? 'Uncategorized' }}">
                                    <div class="product-icon">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div class="product-name">{{ Str::limit($item->name, 22) }}</div>
                                    <div class="product-price">{{ number_format($item->unit_price, 0) }}</div>
                                    @if($item->track_inventory && $item->stock_quantity <= 0)
                                        <div class="product-badge">OUT OF STOCK</div>
                                    @elseif($item->track_inventory && $item->stock_quantity <= $item->low_stock_alert)
                                        <div class="product-stock-low">{{ $item->stock_quantity }} left</div>
                                    @elseif($item->track_inventory)
                                        <div class="product-stock">{{ $item->stock_quantity }} in stock</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No products available. <a href="{{ route('items.create') }}">Add products</a> first.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Cart Panel -->
    <div class="col-lg-5 col-xl-4 mb-3">
        <div class="card card-outline card-warning shadow-sm h-100">
            <div class="card-header bg-gradient-warning text-white py-2 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-shopping-cart"></i> <strong>Shopping Cart</strong></span>
                <span class="badge badge-light" id="cart-count">0 items</span>
            </div>

            <div class="card-body p-0" style="max-height: 280px; overflow-y: auto;">
                <table class="table table-sm table-borderless mb-0" id="cart-table">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:40%">Item</th>
                            <th class="text-center" style="width:30%">Qty</th>
                            <th class="text-right" style="width:20%">Total</th>
                            <th style="width:10%"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-items"></tbody>
                </table>
                <div class="text-center py-5 text-muted" id="cart-empty">
                    <i class="fas fa-cart-plus fa-3x mb-2"></i>
                    <p>Cart is empty</p>
                    <small>Click on a product to add it</small>
                </div>
            </div>

            <div class="card-body bg-light border-top" style="padding: 0.75rem;">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right font-weight-bold"><span id="cart-subtotal">0</span></td>
                    </tr>
                    <tr class="bg-white" style="border-radius: 4px;">
                        <td class="h5 mb-0 py-2"><strong>TOTAL:</strong></td>
                        <td class="text-right h5 mb-0 py-2 text-success font-weight-bold">
                            <span id="cart-total">0</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card-footer">
                <!-- Payment Row -->
                <div class="row mb-2">
                    <div class="col-6">
                        <select id="client-select" class="form-control form-control-sm">
                            <option value="">Walk-in Customer</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <select id="payment-method" class="form-control form-control-sm">
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit" class="credit-option">Credit (On Account)</option>
                        </select>
                    </div>
                </div>

                <!-- Quick Amounts + Paid Input -->
                <div class="row mb-2">
                    <div class="col-12 d-flex gap-1 mb-1 flex-wrap">
                        <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="5000">5,000</button>
                        <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="10000">10,000</button>
                        <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="20000">20,000</button>
                        <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="50000">50,000</button>
                        <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="100000">100,000</button>
                        <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="exact">Exact</button>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-7">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Paid</span>
                            </div>
                            <input type="number" id="amount-paid" class="form-control" placeholder="0" min="0" step="100">
                        </div>
                    </div>
                    <div class="col-5 d-flex align-items-center">
                        <span class="badge badge-success p-2 w-100 text-left" style="font-size: 0.95rem;">
                            Change: <strong><span id="change-amount">0</span></strong>
                        </span>
                    </div>
                </div>

                <button class="btn btn-success btn-block font-weight-bold py-2" id="checkout-btn">
                    <i class="fas fa-check-circle"></i> Complete Sale
                </button>

                <button class="btn btn-outline-danger btn-block btn-sm mt-1" id="clear-cart-btn">
                    <i class="fas fa-trash-alt"></i> Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
<style>
#pos-app {
    margin-top: -10px;
}
/* Product Cards */
.product-btn {
    background: linear-gradient(145deg, #ffffff, #f0f2f5);
    border: 1px solid #e0e3e8;
    border-radius: 10px;
    padding: 12px 8px;
    text-align: center;
    cursor: pointer;
    min-height: 115px;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.product-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    border-color: #007bff;
    background: linear-gradient(145deg, #f0f7ff, #ffffff);
}
.product-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.product-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f0f0f0;
}
.product-btn.disabled:hover {
    transform: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    border-color: #e0e3e8;
}
.product-icon {
    font-size: 1.4rem;
    color: #6c757d;
    margin-bottom: 4px;
}
.product-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: #333;
    line-height: 1.2;
    margin-bottom: 2px;
    word-break: break-word;
}
.product-price {
    font-size: 0.85rem;
    font-weight: 700;
    color: #007bff;
}
.product-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #dc3545;
    color: white;
    font-size: 0.55rem;
    padding: 1px 5px;
    border-radius: 4px;
    font-weight: 700;
}
.product-stock {
    font-size: 0.6rem;
    color: #28a745;
    margin-top: 2px;
}
.product-stock-low {
    font-size: 0.6rem;
    color: #ffc107;
    font-weight: 600;
    margin-top: 2px;
}

/* Category Tabs */
.category-tab {
    font-size: 0.75rem;
    border-radius: 20px !important;
    padding: 2px 12px !important;
    font-weight: 500;
}
.category-tab.active {
    background: white !important;
    color: #007bff !important;
    font-weight: 700;
    border-color: white !important;
}

/* Cart */
#cart-items tr {
    transition: background 0.2s;
}
#cart-items tr:hover {
    background: #fff9e6;
}
#cart-items td {
    padding: 6px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}
.qty-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    background: white;
}
.qty-control button {
    border: none;
    background: #f8f9fa;
    width: 26px;
    height: 26px;
    font-size: 0.7rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #495057;
    padding: 0;
    line-height: 1;
}
.qty-control button:hover {
    background: #e9ecef;
}
.qty-control input {
    width: 28px;
    text-align: center;
    border: none;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    font-size: 0.75rem;
    font-weight: 600;
    height: 26px;
    padding: 0;
}
.remove-item {
    color: #dc3545;
    cursor: pointer;
    font-size: 1rem;
    opacity: 0.5;
    transition: opacity 0.15s;
    background: none;
    border: none;
    padding: 2px 6px;
}
.remove-item:hover {
    opacity: 1;
}

/* Quick Amount Buttons */
.quick-amount {
    font-size: 0.7rem !important;
    padding: 2px 10px !important;
    border-radius: 4px !important;
}
.quick-amount:hover {
    background: #007bff !important;
    color: white !important;
    border-color: #007bff !important;
}

/* Scrollbar */
#product-grid::-webkit-scrollbar, #cart-table::-webkit-scrollbar {
    width: 4px;
}
#product-grid::-webkit-scrollbar-thumb, #cart-table::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}
</style>
@endpush

@push('js')
<script>
const cart = [];

// --- Add to cart ---
document.querySelectorAll('.product-btn:not(.disabled)').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const id = parseInt(this.dataset.id);
        const existing = cart.find(c => c.item_id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                item_id: id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                taxable: this.dataset.taxable === '1',
                tax: parseFloat(this.dataset.tax),
                qty: 1
            });
        }
        renderCart();
        highlightCart();
    });
});

// --- Clear Cart ---
document.getElementById('clear-cart-btn').addEventListener('click', function() {
    if (cart.length === 0) return;
    if (confirm('Clear all items from cart?')) {
        cart.length = 0;
        renderCart();
    }
});

function renderCart() {
    const tbody = document.getElementById('cart-items');
    tbody.innerHTML = '';
    let subtotal = 0;

    cart.forEach((item, idx) => {
        const lineTotal = item.price * item.qty;
        subtotal += lineTotal;

        tbody.innerHTML += `<tr>
            <td>
                <div style="font-size:0.78rem;font-weight:600;color:#333;">${item.name}</div>
                <small class="text-muted" style="font-size:0.65rem;">@ ${Number(item.price).toFixed(0)}</small>
            </td>
            <td class="text-center">
                <div class="qty-control">
                    <button onclick="updateQty(${idx}, -1)">&minus;</button>
                    <input type="text" value="${item.qty}" readonly>
                    <button onclick="updateQty(${idx}, 1)">+</button>
                </div>
            </td>
            <td class="text-right" style="font-weight:600;font-size:0.82rem;">${Number(lineTotal).toFixed(0)}</td>
            <td class="text-center"><button class="remove-item" onclick="removeItem(${idx})" title="Remove">&times;</button></td>
        </tr>`;
    });

    document.getElementById('cart-empty').style.display = cart.length ? 'none' : 'block';
    document.getElementById('cart-count').textContent = cart.length + ' item' + (cart.length !== 1 ? 's' : '');
    document.getElementById('cart-subtotal').textContent = Number(subtotal).toFixed(0);
    document.getElementById('cart-total').textContent = Number(subtotal).toFixed(0);
    calcChange();
}

function updateQty(idx, delta) {
    cart[idx].qty = Math.max(0.01, cart[idx].qty + delta);
    if (cart[idx].qty <= 0) cart.splice(idx, 1);
    renderCart();
}

function removeItem(idx) {
    cart.splice(idx, 1);
    renderCart();
}

// --- Change Calculation ---
document.getElementById('amount-paid').addEventListener('input', calcChange);
function calcChange() {
    const total = parseFloat(document.getElementById('cart-total').textContent) || 0;
    const paid = parseFloat(document.getElementById('amount-paid').value) || 0;
    document.getElementById('change-amount').textContent = Math.max(0, paid - total).toFixed(0);
}

// --- Quick Amount Buttons ---
document.querySelectorAll('.quick-amount').forEach(btn => {
    btn.addEventListener('click', function() {
        const total = parseFloat(document.getElementById('cart-total').textContent) || 0;
        if (this.dataset.amount === 'exact') {
            document.getElementById('amount-paid').value = total;
        } else {
            document.getElementById('amount-paid').value = parseInt(this.dataset.amount);
        }
        calcChange();
    });
});

// --- Category Filtering ---
document.querySelectorAll('.category-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const cat = this.dataset.cat;
        document.getElementById('product-search').value = '';
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = (cat === 'all' || card.dataset.category === cat) ? '' : 'none';
        });
    });
});

// --- Client/Credit Toggle ---
function toggleCreditOption() {
    const clientSelect = document.getElementById('client-select');
    const creditOption = document.querySelector('.credit-option');
    if (creditOption) {
        if (clientSelect.value === '') {
            creditOption.disabled = true;
            creditOption.style.display = 'none';
            // Switch away from credit if currently selected
            const pm = document.getElementById('payment-method');
            if (pm.value === 'credit') pm.value = 'cash';
        } else {
            creditOption.disabled = false;
            creditOption.style.display = '';
        }
    }
}

document.getElementById('client-select').addEventListener('change', toggleCreditOption);
toggleCreditOption();

// --- Product Search ---
document.getElementById('product-search').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const match = card.dataset.name.includes(search);
        card.style.display = match ? '' : 'none';
    });
});

// --- Cart highlight animation ---
function highlightCart() {
    const header = document.querySelector('.card-header.bg-gradient-warning');
    header.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
    setTimeout(() => {
        header.style.background = '';
    }, 400);
}

// --- Checkout ---
document.getElementById('checkout-btn').addEventListener('click', function() {
    if (cart.length === 0) {
        showNotification('Cart is empty!', 'warning');
        return;
    }
    const paid = parseFloat(document.getElementById('amount-paid').value) || 0;
    const total = parseFloat(document.getElementById('cart-total').textContent);
    if (paid < total) {
        showNotification('Amount paid (' + Number(paid).toFixed(0) + ') is less than total (' + Number(total).toFixed(0) + ')', 'warning');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const items = cart.map(c => ({
        item_id: c.item_id,
        quantity: c.qty,
        unit_price: c.price,
        discount: 0
    }));

    fetch('{{ route("pos.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            client_id: document.getElementById('client-select').value || null,
            payment_method: document.getElementById('payment-method').value,
            amount_paid: paid,
            items: items
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification('Sale #' + data.pos_number + ' completed! Change: ' + Number(data.change_amount).toFixed(0), 'success');
            setTimeout(() => {
                window.location.href = '{{ route("pos.receipt", ":id") }}'.replace(':id', data.sale_id);
            }, 800);
        } else {
            showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(e => {
        showNotification('Error: ' + e.message, 'error');
    })
    .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-check-circle"></i> Complete Sale';
    });
});

// --- Notification Toast ---
function showNotification(message, type) {
    const existing = document.querySelector('.pos-toast');
    if (existing) existing.remove();

    const bgColor = type === 'success' ? '#28a745' : type === 'warning' ? '#ffc107' : '#dc3545';
    const textColor = type === 'warning' ? '#333' : '#fff';
    const icon = type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-circle' : 'fa-times-circle';

    const toast = document.createElement('div');
    toast.className = 'pos-toast';
    toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 99999;
        background: ${bgColor}; color: ${textColor}; padding: 12px 20px;
        border-radius: 8px; font-weight: 600; font-size: 0.9rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        animation: slideInRight 0.3s ease;
        max-width: 380px;
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(50px)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Inject animation
const styleSheet = document.createElement('style');
styleSheet.textContent = `
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
`;
document.head.appendChild(styleSheet);
</script>
@endpush