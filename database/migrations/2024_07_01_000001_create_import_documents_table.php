<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('vehicle_imports')->onDelete('cascade');
            $table->enum('step', [
                'purchase',
                'transport',
                'itv_inspection',
                'taxes',
                'dgt_registration',
                'plates'
            ]);
            $table->enum('type', [
                'compraventa',
                'coc',
                'ficha_tecnica_origen',
                'tarjeta_itv_origen',
                'seguro_transporte',
                'ficha_itv_es',
                'modelo_576',
                'modelo_309_300',
                'modelo_itp',
                'justificante_ivtm',
                'permiso_circulacion',
                'otro'
            ]);
            $table->string('file_path');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('uploaded_at')->useCurrent();

            $table->index(['import_id', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_documents');
    }
};