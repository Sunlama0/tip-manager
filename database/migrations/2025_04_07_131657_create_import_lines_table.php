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
        Schema::create('import_lines', function (Blueprint $table) {
            $table->id();

            // Données importées
            $table->string('site_address');
            $table->string('postal_code');
            $table->string('city');

            // Données à compléter
            $table->string('cadaster_number')->nullable();
            $table->string('landlord')->nullable();
            $table->string('landlord_address')->nullable();
            $table->string('landlord_postal_code')->nullable();
            $table->string('landlord_city')->nullable();

            // Pack lié
            $table->foreignId('import_pack_id')->constrained('import_packs')->onDelete('cascade');

            // Attribution collaborateur + statut
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['à compléter', 'en cours', 'terminée'])->default('à compléter');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_lines');
    }
};
