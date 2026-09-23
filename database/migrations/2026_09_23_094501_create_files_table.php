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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('folders')->nullOnDelete();
            $table->string('name');
            $table->string('disk', 30)->default('s3');
            $table->text('path');
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedBigInteger('original_size')->nullable();
            $table->boolean('is_compressed')->default(false);
            $table->boolean('is_optimized')->default(false);
            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->string('category', 20)->default('other'); // image, video, audio, document, archive, other
            $table->boolean('is_starred')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'folder_id']);
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'is_starred']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
