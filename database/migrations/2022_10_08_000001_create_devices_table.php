<?php

use App\Models\User;
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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('serial_number')->unique();
            $table->string('name')->nullable();
            $table->string('label')->virtualAs('ifnull(name, serial_number)');
            $table->decimal('latitude', 7, 5);
            $table->decimal('longitude', 8, 5);
            $table->char('currency', 3)->nullable();
            $table->decimal('investment', 8, 2)
                  ->nullable();
            $table->decimal('price_per_kilowatt_hour', 5, 4)
                  ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
