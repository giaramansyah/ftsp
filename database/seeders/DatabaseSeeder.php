<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $supersu = array(
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'sysadmin@laravel.app',
            'username' => 'sysadmin',
            'password' => '$2y$10$jDOFyJFN68wCJSbsyZSAfedoP9d88U7aBPs0rEn3Ib4kCyjDAvdnS',
            'hash' => '72a0d5ab745445acc798e65b2e67782b',
            'division_id' => 0,
            'staff_id' => 1,
            'privilege_group_id' => 1,
            'is_login' => 0,
            'is_remember' => 0,
            'is_trash' => 0,
            'is_new' => 0,
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_by' => 'system',
            'updated_at' => Carbon::now()->toDateTimeString(),
            'updated_by' => 'system',
        );

        $menus = array(
            array(
                'label' => 'Master',
                'alias' => 'master',
                'icon' => 'fa-folder',
                'order' => 1,
                'child' => array(
                    array(
                        'label' => 'Rencana Anggaran',
                        'alias' => 'data',
                        'url' => 'master.data.index',
                        'order' => 1,
                    )
                )
            ),
            array(
                'label' => 'Pengaturan',
                'alias' => 'settings',
                'icon' => 'fa-cog',
                'order' => 3,
                'child' => array(
                    array(
                        'label' => 'Hak Akses',
                        'alias' => 'privilege',
                        'url' => 'settings.privilege.index',
                        'order' => 3,
                    ),
                    array(
                        'label' => 'Grup Akses',
                        'alias' => 'privigroup',
                        'url' => 'settings.privigroup.index',
                        'order' => 1,
                    ),
                    array(
                        'label' => 'Akun',
                        'alias' => 'user',
                        'url' => 'settings.user.index',
                        'order' => 2,
                    )
                )
            ),
            array(
                'label' => 'Logs',
                'alias' => 'logs',
                'icon' => 'fa-keyboard',
                'order' => 4,
                'child' => array(
                    array(
                        'label' => 'Aktivitas Akun',
                        'alias' => 'activity',
                        'url' => 'logs.activity.index',
                        'order' => 1,
                    )
                )
            ),
            array(
                'label' => 'Transaksi',
                'alias' => 'transaction',
                'icon' => 'fa-book',
                'order' => 2,
                'child' => array(
                    array(
                        'label' => 'Pengajuan Anggaran',
                        'alias' => 'offer',
                        'url' => 'transaction.offer.index',
                        'order' => 1,
                    )
                )
            ),
        );

        $privilege = array(
            //rencana anggaran
            array(
                'code' => 'DACR',
                'menu_id' => 1,
                'modules' => Config::get('global.modules.code.create'),
                'desc' => 'Add new master data'
            ),
            array(
                'code' => 'DAUP',
                'menu_id' => 1,
                'modules' => Config::get('global.modules.code.update'),
                'desc' => 'Update existing master data'
            ),
            array(
                'code' => 'DARM',
                'menu_id' => 1,
                'modules' => Config::get('global.modules.code.delete'),
                'desc' => 'Remove existing master data'
            ),
            array(
                'code' => 'DARA',
                'menu_id' => 1,
                'modules' => Config::get('global.modules.code.readall'),
                'desc' => 'Read list of master data'
            ),
            array(
                'code' => 'DARD',
                'menu_id' => 1,
                'modules' => Config::get('global.modules.code.readid'),
                'desc' => 'Read detail of master data'
            ),
            //privilege
            array(
                'code' => 'PRCR',
                'menu_id' => 2,
                'modules' => Config::get('global.modules.code.create'),
                'desc' => 'Add new privilege data'
            ),
            array(
                'code' => 'PRUP',
                'menu_id' => 2,
                'modules' => Config::get('global.modules.code.update'),
                'desc' => 'Update existing privilege data'
            ),
            array(
                'code' => 'PRRM',
                'menu_id' => 2,
                'modules' => Config::get('global.modules.code.delete'),
                'desc' => 'Remove existing privilege data'
            ),
            array(
                'code' => 'PRRA',
                'menu_id' => 2,
                'modules' => Config::get('global.modules.code.readall'),
                'desc' => 'Read list of privilege data'
            ),
            //privilege group
            array(
                'code' => 'PGCR',
                'menu_id' => 3,
                'modules' => Config::get('global.modules.code.create'),
                'desc' => 'Add new privilege group data'
            ),
            array(
                'code' => 'PGUP',
                'menu_id' => 3,
                'modules' => Config::get('global.modules.code.update'),
                'desc' => 'Update existing privilege group data'
            ),
            array(
                'code' => 'PGRM',
                'menu_id' => 3,
                'modules' => Config::get('global.modules.code.delete'),
                'desc' => 'Remove existing privilege group data'
            ),
            array(
                'code' => 'PGRA',
                'menu_id' => 3,
                'modules' => Config::get('global.modules.code.readall'),
                'desc' => 'Read list of privilege group data'
            ),
            //user account
            array(
                'code' => 'USCR',
                'menu_id' => 4,
                'modules' => Config::get('global.modules.code.create'),
                'desc' => 'Add new user account data'
            ),
            array(
                'code' => 'USUP',
                'menu_id' => 4,
                'modules' => Config::get('global.modules.code.update'),
                'desc' => 'Update existing user account data'
            ),
            array(
                'code' => 'USRM',
                'menu_id' => 4,
                'modules' => Config::get('global.modules.code.delete'),
                'desc' => 'Remove existing user account data'
            ),
            array(
                'code' => 'USRA',
                'menu_id' => 4,
                'modules' => Config::get('global.modules.code.readall'),
                'desc' => 'Read list of user account data'
            ),
            array(
                'code' => 'USRD',
                'menu_id' => 4,
                'modules' => Config::get('global.modules.code.readid'),
                'desc' => 'Read detail of user account data'
            ),
            //user log
            array(
                'code' => 'LURA',
                'menu_id' => 5,
                'modules' => Config::get('global.modules.code.readall'),
                'desc' => 'Read list of user activity data'
            ),
            //pengajuan anggaran
            array(
                'code' => 'TRCR',
                'menu_id' => 6,
                'modules' => Config::get('global.modules.code.create'),
                'desc' => 'Add new transaction data'
            ),
            array(
                'code' => 'TRUP',
                'menu_id' => 6,
                'modules' => Config::get('global.modules.code.update'),
                'desc' => 'Update existing transaction data'
            ),
            array(
                'code' => 'TRRA',
                'menu_id' => 6,
                'modules' => Config::get('global.modules.code.readall'),
                'desc' => 'Read list of transaction data'
            ),
            array(
                'code' => 'TRRD',
                'menu_id' => 6,
                'modules' => Config::get('global.modules.code.readid'),
                'desc' => 'Read detail of transaction data'
            ),
        );

        $privilegegroup = array(
            array(
                'name' => 'SUPERSU',
                'description' => 'Superuser Privilege',
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => 'system',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => 'system',
            ),
        );

        DB::table('ms_user')->truncate();
        DB::table('ms_parent_menus')->truncate();
        DB::table('ms_menus')->truncate();
        DB::table('ms_privilege')->truncate();
        DB::table('ms_privilege_group')->truncate();
        DB::table('map_privilege')->truncate();

        DB::table('ms_user')->insert($supersu);

        foreach($menus as $value) {
            $id = DB::table('ms_parent_menus')->insertGetId(array(
                'label' => $value['label'],
                'alias' => $value['alias'],
                'icon' => $value['icon'],
                'order' => $value['order'],
            ));

            foreach($value['child'] as $val) {
                $val['parent_id'] = $id;
                DB::table('ms_menus')->insert($val);
            }
        }

        foreach($privilegegroup as $value) {
            $id = DB::table('ms_privilege_group')->insertGetId($value);
            foreach($privilege as $val) {
                $id2 = DB::table('ms_privilege')->insertGetId($val);
                DB::table('map_privilege')->insert(array('privilege_group_id' => $id, 'privilege_id' => $id2));
            }
        }
    }
}
