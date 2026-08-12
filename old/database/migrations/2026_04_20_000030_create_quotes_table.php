<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('quote_number', 40)->unique();
            $table->string('status', 20)->default('draft'); // draft, sent, accepted, declined, expired, converted
            $table->string('public_token', 64)->unique();

            $table->string('title');
            $table->text('introduction')->nullable();
            $table->text('terms')->nullable();

            $table->char('currency', 3)->default('USD');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);      // percent
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamp('valid_until')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();

            $table->string('signed_name')->nullable();
            $table->string('signed_ip', 45)->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('decline_reason')->nullable();

            // FK added in 2026_04_20_000032 once invoices table exists.
            $table->unsignedBigInteger('invoice_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
