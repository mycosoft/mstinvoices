<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null'); // Optional - expense can be associated with a client
            
            // Expense Identification
            $table->string('expense_number')->unique();
            $table->string('reference_number')->nullable(); // Vendor invoice number or receipt number
            
            // Expense Details
            $table->string('title'); // Expense title/description
            $table->text('description')->nullable(); // Detailed description
            $table->string('category'); // Expense category (Office Supplies, Travel, etc.)
            $table->string('vendor_name')->nullable(); // Vendor or supplier name
            $table->string('vendor_email')->nullable();
            $table->string('vendor_phone')->nullable();
            
            // Financial Information
            $table->decimal('amount', 10, 2); // Total expense amount
            $table->decimal('tax_amount', 10, 2)->default(0); // Tax amount if applicable
            $table->decimal('net_amount', 10, 2); // Amount before tax
            $table->decimal('tax_rate', 5, 2)->default(0); // Tax rate percentage
            
            // Dates
            $table->date('expense_date'); // Date when expense occurred
            $table->date('due_date')->nullable(); // Payment due date
            $table->date('paid_date')->nullable(); // Date when expense was paid
            
            // Status and Payment
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'paid', 'cancelled'])->default('draft');
            $table->enum('payment_status', ['unpaid', 'paid', 'partial'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'check', 'paypal', 'other'])->nullable();
            
            // Approval Workflow
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            // Currency and Locale
            $table->string('currency', 3)->default('USD');
            $table->string('locale', 5)->default('en_US');
            
            // Additional Information
            $table->text('notes')->nullable(); // Internal notes
            $table->boolean('is_billable')->default(false); // Can this expense be billed to a client?
            $table->boolean('is_reimbursable')->default(false); // Is this a reimbursable expense?
            $table->boolean('is_recurring')->default(false); // Is this a recurring expense?
            $table->string('recurring_frequency')->nullable(); // monthly, quarterly, yearly
            $table->date('recurring_end_date')->nullable();
            
            // Attachments and Receipts
            $table->json('attachments')->nullable(); // File paths for receipts/invoices
            $table->string('receipt_number')->nullable();
            
            // Tracking and Analytics
            $table->string('project_code')->nullable(); // For project-based expenses
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->json('tags')->nullable(); // Additional tags for categorization
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['client_id']);
            $table->index(['expense_date']);
            $table->index(['due_date']);
            $table->index(['category']);
            $table->index(['vendor_name']);
            $table->index(['status', 'payment_status']);
            $table->index(['is_billable']);
            $table->index(['is_reimbursable']);
            $table->index(['requires_approval']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
