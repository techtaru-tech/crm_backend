<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('invoice_number', 40)->unique();
            $table->string('status', 20)->default('draft'); // draft, sent, paid, partial, overdue, cancelled, refunded
            $table->string('public_token', 64)->unique();

            $table->char('currency', 3)->default('USD');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);

            $table->date('issued_date');
            $table->date('due_date')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();
            $table->json('billing_address')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'lead_id']);
            $table->index(['tenant_id', 'due_date']);
        });

        // Now that invoices exists, bolt on the FK from quotes.invoice_id
        // that was deferred in 2026_04_20_000030_create_quotes_table.
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign('invoice_id')
                ->references('id')->on('invoices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            // dropForeign requires the generated name: {table}_{column}_foreign
            $table->dropForeign(['invoice_id']);
        });

        Schema::dropIfExists('invoices');
    }
};
