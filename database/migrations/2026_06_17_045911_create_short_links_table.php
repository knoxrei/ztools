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
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->text('original_url');
            $table->string('password')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_clicks')->nullable();
            $table->integer('clicks_count')->default(0);
            $table->boolean('is_burn_after_use')->default(false);
            $table->string('cloak_title')->nullable();
            $table->text('cloak_desc')->nullable();
            $table->string('connection_type')->default('both'); // clearnet, tor, both
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_links');
    }
};
