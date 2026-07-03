<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_imports', function (Blueprint $table) {
            // Purchase step fields
            $table->string('seller_name', 150)->nullable();
            $table->string('seller_contact', 150)->nullable();
            $table->decimal('vehicle_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->string('purchase_country', 2)->default('DE');

            // Transport step fields
            $table->string('transport_type')->nullable();
            $table->string('transport_provider', 150)->nullable();
            $table->decimal('transport_cost', 10, 2)->nullable();
            $table->date('transport_eta')->nullable();
            $table->date('transport_date')->nullable();
            $table->string('tracking_number', 100)->nullable();

            // ITV step fields
            $table->string('itv_station', 150)->nullable();
            $table->date('itv_date')->nullable();
            $table->string('itv_time')->nullable();
            $table->enum('itv_result', ['passed', 'passed_repairs', 'failed'])->nullable();
            $table->string('homologation_provider', 150)->nullable();
            $table->decimal('homologation_cost', 10, 2)->nullable();

            // Taxes step fields
            $table->boolean('iedmt_paid')->default(false);
            $table->decimal('iedmt_amount', 10, 2)->nullable();
            $table->boolean('itp_paid')->default(false);
            $table->decimal('itp_amount', 10, 2)->nullable();
            $table->boolean('ivtm_paid')->default(false);
            $table->decimal('ivtm_amount', 10, 2)->nullable();
            $table->string('tax_provider', 150)->nullable();
            $table->decimal('tax_provider_cost', 10, 2)->nullable();

            // DGT step fields
            $table->string('dgt_provider', 150)->nullable();
            $table->string('dgt_provider_contact', 150)->nullable();
            $table->string('dgt_file_number', 50)->nullable();
            $table->date('dgt_submission_date')->nullable();

            // Plates step fields
            $table->string('plates_provider', 150)->nullable();
            $table->decimal('plates_cost', 10, 2)->nullable();
            $table->date('plates_installation_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_imports', function (Blueprint $table) {
            // Drop purchase step fields
            $table->dropColumn([
                'seller_name', 'seller_contact', 'vehicle_price', 'currency', 'purchase_country',
            ]);

            // Drop transport step fields
            $table->dropColumn([
                'transport_type', 'transport_provider', 'transport_cost',
                'transport_eta', 'transport_date', 'tracking_number',
            ]);

            // Drop ITV step fields
            $table->dropColumn([
                'itv_station', 'itv_date', 'itv_time', 'itv_result',
                'homologation_provider', 'homologation_cost',
            ]);

            // Drop taxes step fields
            $table->dropColumn([
                'iedmt_paid', 'iedmt_amount', 'itp_paid', 'itp_amount',
                'ivtm_paid', 'ivtm_amount', 'tax_provider', 'tax_provider_cost',
            ]);

            // Drop DGT step fields
            $table->dropColumn([
                'dgt_provider', 'dgt_provider_contact', 'dgt_file_number', 'dgt_submission_date',
            ]);

            // Drop plates step fields
            $table->dropColumn([
                'plates_provider', 'plates_cost', 'plates_installation_date',
            ]);
        });
    }
};
