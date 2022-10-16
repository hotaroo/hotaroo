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
        Schema::table('users', function (Blueprint $table) {
            $table->after('remember_token', function (Blueprint $table) {
                $table->text('timezone')->nullable();
                $table->text('date_format')->nullable();
                $table->text('time_format')->nullable();
                $table->text('ecoflow_key')->nullable();
                $table->text('ecoflow_secret')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ecoflow_key',
                'ecoflow_secret',
            ]);
        });
    }
};
