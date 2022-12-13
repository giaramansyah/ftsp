<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ms_user')) {
            Schema::create('ms_user', function (Blueprint $table) {
                $table->id();
                $table->string('first_name', 50)->nullable(false);
                $table->string('last_name', 50)->nullable(false);
                $table->string('email', 50)->nullable(false);
                $table->string('username', 100)->nullable(false);
                $table->string('password')->nullable(false);
                $table->string('hash', 32)->nullable(false);
                $table->rememberToken();
                $table->tinyInteger('division_id')->nullable(false)->default(0);
                $table->tinyInteger('staff_id')->nullable(false)->default(0);
                $table->bigInteger('privilege_group_id')->nullable(false);
                $table->tinyInteger('is_remember')->nullable(false)->default(0);
                $table->tinyInteger('is_login')->nullable(false)->default(0);
                $table->tinyInteger('is_new')->nullable(false)->default(1);
                $table->tinyInteger('is_trash')->nullable(false)->default(0);
                $table->string('created_by', 100)->nullable(false);
                $table->string('updated_by', 100)->nullable(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('log_user')) {
            Schema::create('log_user', function (Blueprint $table) {
                $table->id();
                $table->string('username', 100)->nullable(false);
                $table->bigInteger('privilege_id')->nullable(false);
                $table->string('description', 100)->nullable(false);
                $table->string('ip_address', 10)->nullable(false);
                $table->string('agent')->nullable(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ms_parent_menus')) {
            Schema::create('ms_parent_menus', function (Blueprint $table) {
                $table->id();
                $table->string('label', 30)->nullable(false);
                $table->string('alias', 20)->nullable(false);
                $table->string('icon', 100)->nullable(true);
                $table->tinyInteger('order')->nullable(false)->default(0);
                $table->tinyInteger('is_active')->nullable(false)->default(1);
            });
        }

        if (!Schema::hasTable('ms_menus')) {
            Schema::create('ms_menus', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('parent_id')->nullable(false);
                $table->string('label', 30)->nullable(false);
                $table->string('alias', 20)->nullable(false);
                $table->string('url', 100)->nullable(false);
                $table->tinyInteger('order')->nullable(false)->default(0);
                $table->tinyInteger('is_active')->nullable(false)->default(1);
            });
        }

        if (!Schema::hasTable('ms_privilege')) {
            Schema::create('ms_privilege', function (Blueprint $table) {
                $table->id();
                $table->string('code', 4)->nullable(false);
                $table->bigInteger('menu_id')->nullable(false);
                $table->tinyInteger('modules')->nullable(false);
                $table->string('desc', 100)->nullable(false);
            });
        }

        if (!Schema::hasTable('ms_privilege_group')) {
            Schema::create('ms_privilege_group', function (Blueprint $table) {
                $table->id();
                $table->string('name', 20)->nullable(false);
                $table->string('description', 100)->nullable(true);
                $table->string('created_by', 100)->nullable(false);
                $table->string('updated_by', 100)->nullable(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('map_privilege')) {
            Schema::create('map_privilege', function (Blueprint $table) {
                $table->bigInteger('privilege_group_id')->nullable(false);
                $table->bigInteger('privilege_id')->nullable(false);
            });
        }

        if (!Schema::hasTable('ms_data')) {
            Schema::create('ms_data', function (Blueprint $table) {
                $table->id();
                $table->string('ma_id', 20)->nullable(false);
                $table->string('description', 100)->nullable(false);
                $table->integer('year')->nullable(false);
                $table->tinyInteger('division_id')->nullable(false);
                $table->bigInteger('amount')->nullable(false);
                $table->string('filename', 100)->nullable(false);
                $table->tinyInteger('is_trash')->nullable(false)->default(0);
                $table->string('created_by', 100)->nullable(false);
                $table->string('updated_by', 100)->nullable(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('map_data')) {
            Schema::create('map_data', function (Blueprint $table) {
                $table->bigInteger('data_id')->nullable(false);
                $table->bigInteger('staff_id')->nullable(false);
            });
        }

        if (!Schema::hasTable('log_data')) {
            Schema::create('log_data', function (Blueprint $table) {
                $table->id();
                $table->string('filename', 100)->nullable(false);
                $table->string('username', 100)->nullable(false);
                $table->string('ip_address', 10)->nullable(false);
                $table->string('agent')->nullable(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ms_balance')) {
            Schema::create('ms_balance', function (Blueprint $table) {
                $table->id();
                $table->tinyInteger('division_id')->nullable(false);
                $table->bigInteger('amount')->nullable(false);
                $table->tinyInteger('is_trash')->nullable(false)->default(0);
                $table->string('created_by', 100)->nullable(false);
                $table->string('updated_by', 100)->nullable(false);
                $table->timestamps();
            });
        }
        
        if (!Schema::hasTable('ts_history_balance')) {
            Schema::create('ts_history_balance', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('balance_id')->nullable(false);
                $table->string('description')->nullable(false);
                $table->tinyInteger('transaction_id')->nullable(false);
                $table->bigInteger('amount')->nullable(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ts_expense')) {
            Schema::create('ts_expense', function (Blueprint $table) {
                $table->id();
                $table->string('expense_id', 15)->nullable(false);
                $table->date('expense_date')->nullable(false);
                $table->string('reff_no', 50)->nullable(false);
                $table->date('reff_date')->nullable(false);
                $table->string('description', 100)->nullable(false);
                $table->string('sub_description', 250)->nullable(true);
                $table->string('data_id', 20)->nullable(false);
                $table->string('name', 50)->nullable(false);
                $table->tinyInteger('staff_id')->nullable(false);
                $table->bigInteger('amount')->nullable(false);
                $table->string('text_amount', 100)->nullable(false);
                $table->string('account', 20)->nullable(false);
                $table->date('apply_date')->nullable(true);
                $table->string('image', 50)->nullable(true);
                $table->tinyInteger('type')->nullable(false);
                $table->string('created_by', 100)->nullable(false);
                $table->string('updated_by', 100)->nullable(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_user');
        Schema::dropIfExists('log_user');
        Schema::dropIfExists('ms_parent_menus');
        Schema::dropIfExists('ms_menus');
        Schema::dropIfExists('ms_privilege');
        Schema::dropIfExists('ms_privilege_group');
        Schema::dropIfExists('map_privilege');
        Schema::dropIfExists('ms_data');
        Schema::dropIfExists('map_data');
        Schema::dropIfExists('log_data');
        Schema::dropIfExists('ms_balance');
        Schema::dropIfExists('ts_history_balance');
        Schema::dropIfExists('ts_expense');
    }
}
