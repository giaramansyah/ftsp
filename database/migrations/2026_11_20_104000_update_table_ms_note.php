<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableMsNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ms_note', function (Blueprint $table) {
            $table->tinyInteger('application_status')->nullable()->after('status');
            $table->tinyInteger('fund_type')->nullable()->after('application_status');
            $table->date('disbursed_date')->nullable()->after('fund_type');
            $table->text('notes')->nullable()->after('disbursed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ms_note', function (Blueprint $table) {
            $table->dropColumn('application_status');
            $table->dropColumn('fund_type');
            $table->dropColumn('disbursed_date');
            $table->dropColumn('notes');
        });
    }
}
