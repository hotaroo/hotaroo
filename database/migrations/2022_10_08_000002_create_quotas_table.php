<?php

use App\Models\Device;
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
        Schema::create('quotas', function (Blueprint $table) {
            $table->id();
            $table->datetime('timestamp');
            $table->foreignIdFor(Device::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->unsignedSmallInteger('state_of_charge')->nullable();
            $table->unsignedSmallInteger('watts_in_sum')->nullable();
            $table->unsignedSmallInteger('watts_out_sum')->nullable();
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
        Schema::dropIfExists('quotas');
    }
};
