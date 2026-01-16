<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->text('content');
            $table->foreignId('locale_id')->constrained('locales');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
