<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('services', function (Blueprint $table) {
        $table->id(); // ID
        $table->string('service_name'); // Service Name
        $table->text('description')->nullable(); // Description
        $table->string('thumbnail')->nullable(); // Thumbnail
        $table->json('images')->nullable(); // Images (store as JSON)
        $table->foreignId('category_id')->nullable(); // Foreign key for Category
        $table->integer('estimated_time')->nullable(); // Estimated Time (minutes)
        $table->time('start_time')->nullable(); // Start Time
        $table->decimal('service_cost', 10, 2); // Service Cost
        $table->timestamps(); // Created_at, Updated_at
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
};
