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
        Schema::create('quota_summaries', function (Blueprint $table) {
            $table->id();
            $table->datetime('timestamp');
            $table->foreignIdFor(Device::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->unsignedTinyInteger('state_of_charge')->nullable();
            $table->unsignedDecimal('watt_hours_in_sum', 8, 3);
            $table->unsignedDecimal('watt_hours_in_cumsum', 12, 3);
            $table->unsignedDecimal('watt_hours_out_sum', 8, 3);
            $table->unsignedDecimal('watt_hours_out_cumsum', 12, 3);
            $table->unsignedBigInteger('last_quota_id');
            $table->unsignedTinyInteger('quota_count');
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
        Schema::dropIfExists('quota_summaries');
    }
};
