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
                        'label' => 'Dosen & Tendik',
                        'alias' => 'employee',
                        'url' => 'master.employee.index',
                        'order' => 2,
                        'privilege' => array(
                            array(
                                'code' => 'EMCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new master employee'
                            ),
                            array(
                                'code' => 'EMUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing master employee'
                            ),
                            array(
                                'code' => 'EMRM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing master employee'
                            ),
                            array(
                                'code' => 'EMRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of master employee'
                            ),
                            array(
                                'code' => 'EMRD',
                                'modules' => Config::get('global.modules.code.readid'),
                                'desc' => 'Read detail of master employee'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Mata Anggaran',
                        'alias' => 'data',
                        'url' => 'master.data.index',
                        'order' => 3,
                        'privilege' => array(
                            array(
                                'code' => 'DACR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new master data'
                            ),
                            array(
                                'code' => 'DAUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing master data'
                            ),
                            array(
                                'code' => 'DARM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing master data'
                            ),
                            array(
                                'code' => 'DARA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of master data'
                            ),
                            array(
                                'code' => 'DARD',
                                'modules' => Config::get('global.modules.code.readid'),
                                'desc' => 'Read detail of master data'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Saldo Rekening',
                        'alias' => 'balance',
                        'url' => 'master.balance.index',
                        'order' => 4,
                        'privilege' => array(
                            array(
                                'code' => 'BACR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new master balance'
                            ),
                            array(
                                'code' => 'BAUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing master balance'
                            ),
                            array(
                                'code' => 'BARM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing master balance'
                            ),
                            array(
                                'code' => 'BARA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of master balance'
                            ),
                            array(
                                'code' => 'BARD',
                                'modules' => Config::get('global.modules.code.readid'),
                                'desc' => 'Read detail of master balance'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Tahun Akademik',
                        'alias' => 'years',
                        'url' => 'master.years.index',
                        'order' => 0,
                        'privilege' => array(
                            array(
                                'code' => 'YRCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new master years'
                            ),
                            array(
                                'code' => 'YRRM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing master years'
                            ),
                            array(
                                'code' => 'YRRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of master years'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Surat Pengajuan',
                        'alias' => 'note',
                        'url' => 'master.note.index',
                        'order' => 1,
                        'privilege' => array(
                            array(
                                'code' => 'NTCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new master note'
                            ),
                            array(
                                'code' => 'NTUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update new master note'
                            ),
                            array(
                                'code' => 'NTRM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing master note'
                            ),
                            array(
                                'code' => 'NTRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of master note'
                            ),
                            array(
                                'code' => 'NTRD',
                                'modules' => Config::get('global.modules.code.readid'),
                                'desc' => 'Read detail of master note'
                            ),
                        )
                    )
                )
            ),
            array(
                'label' => 'Transaksi',
                'alias' => 'transaction',
                'icon' => 'fa-money-bill-transfer',
                'order' => 2,
                'child' => array(
                    array(
                        'label' => 'Penerimaan',
                        'alias' => 'reception',
                        'url' => 'transaction.reception.index',
                        'order' => 1,
                        'privilege' => array(
                            array(
                                'code' => 'RECR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new transaction reception'
                            ),
                            array(
                                'code' => 'REUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing transaction reception'
                            ),
                            array(
                                'code' => 'RERA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of transaction reception'
                            ),
                            array(
                                'code' => 'RERD',
                                'modules' => Config::get('global.modules.code.readid'),
                                'desc' => 'Read detail of transaction reception'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Pengeluaran',
                        'alias' => 'expense',
                        'url' => 'transaction.expense.index',
                        'order' => 2,
                        'privilege' => array(
                            array(
                                'code' => 'EXCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new transaction expense'
                            ),
                            array(
                                'code' => 'EXUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing transaction expense'
                            ),
                            array(
                                'code' => 'EXRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of transaction expense'
                            ),
                            array(
                                'code' => 'EXRD',
                                'modules' => Config::get('global.modules.code.readid'),
                                'desc' => 'Read detail of transaction expense'
                            ),
                        )
                    )
                )
            ),
            array(
                'label' => 'Laporan',
                'alias' => 'report',
                'icon' => 'fa-file-invoice',
                'order' => 3,
                'child' => array(
                    array(
                        'label' => 'Pertanggung Jawaban',
                        'alias' => 'accountability',
                        'url' => 'report.accountability.index',
                        'order' => 1,
                        'privilege' => array(
                            array(
                                'code' => 'ACCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new report accountability'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Harian',
                        'alias' => 'daily',
                        'url' => 'report.daily.index',
                        'order' => 2,
                        'privilege' => array(
                            array(
                                'code' => 'DLCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new report daily'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Rekapitulasi',
                        'alias' => 'recapitulation',
                        'url' => 'report.recapitulation.index',
                        'order' => 3,
                        'privilege' => array(
                            array(
                                'code' => 'RCRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'view report recapitulation'
                            ),
                        )
                    ),
                )
            ),
            array(
                'label' => 'Pengaturan',
                'alias' => 'settings',
                'icon' => 'fa-cog',
                'order' => 5,
                'child' => array(
                    array(
                        'label' => 'Grup Akses',
                        'alias' => 'privigroup',
                        'url' => 'settings.privigroup.index',
                        'order' => 1,
                        'privilege' => array(
                            array(
                                'code' => 'PGCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new privilege group data'
                            ),
                            array(
                                'code' => 'PGUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing privilege group data'
                            ),
                            array(
                                'code' => 'PGRM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing privilege group data'
                            ),
                            array(
                                'code' => 'PGRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of privilege group data'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Akun',
                        'alias' => 'user',
                        'url' => 'settings.user.index',
                        'order' => 2,
                        'privilege' => array(
                            array(
                                'code' => 'USCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new user account data'
                            ),
                            array(
                                'code' => 'USUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing user account data'
                            ),
                            array(
                                'code' => 'USRM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing user account data'
                            ),
                            array(
                                'code' => 'USRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of user account data'
                            ),
                            array(
                                'code' => 'USRD',
                                'modules' => Config::get('global.modules.code.readid'),
                                'desc' => 'Read detail of user account data'
                            ),
                        )
                    ),
                    array(
                        'label' => 'Hak Akses',
                        'alias' => 'privilege',
                        'url' => 'settings.privilege.index',
                        'order' => 3,
                        'privilege' => array(
                            array(
                                'code' => 'PRCR',
                                'modules' => Config::get('global.modules.code.create'),
                                'desc' => 'Add new privilege data'
                            ),
                            array(
                                'code' => 'PRUP',
                                'modules' => Config::get('global.modules.code.update'),
                                'desc' => 'Update existing privilege data'
                            ),
                            array(
                                'code' => 'PRRM',
                                'modules' => Config::get('global.modules.code.delete'),
                                'desc' => 'Remove existing privilege data'
                            ),
                            array(
                                'code' => 'PRRA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of privilege data'
                            ),
                        )
                    ),
                )
            ),
            array(
                'label' => 'Logs',
                'alias' => 'logs',
                'icon' => 'fa-keyboard',
                'order' => 6,
                'child' => array(
                    array(
                        'label' => 'Aktivitas Akun',
                        'alias' => 'activity',
                        'url' => 'logs.activity.index',
                        'order' => 1,
                        'privilege' => array(
                            array(
                                'code' => 'LURA',
                                'modules' => Config::get('global.modules.code.readall'),
                                'desc' => 'Read list of user activity data'
                            ),
                        )
                    ),
                    // array(
                    //     'label' => 'Aktivitas Upload File',
                    //     'alias' => 'file',
                    //     'url' => 'logs.file.index',
                    //     'order' => 1,
                    //     'privilege' => array(
                    //         array(
                    //             'code' => 'LFRA',
                    //             'modules' => Config::get('global.modules.code.readall'),
                    //             'desc' => 'Read list of upload file activity data'
                    //         ),
                    //     )
                    // )
                )
            ),
        );

        $privilegegroup = array(
            'name' => 'SUPERSU',
            'description' => 'Superuser Privilege',
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_by' => 'system',
            'updated_at' => Carbon::now()->toDateTimeString(),
            'updated_by' => 'system',
        );

        DB::table('ms_user')->where('id', 1)->delete();
        DB::table('ms_parent_menus')->truncate();
        DB::table('ms_menus')->truncate();
        DB::table('ms_privilege')->truncate();
        DB::table('ms_privilege_group')->where('id', 1)->delete();
        DB::table('map_privilege')->where('privilege_id', 1)->delete();

        DB::table('ms_user')->insert($supersu);

        $idPrivigroup = DB::table('ms_privilege_group')->insertGetId($privilegegroup);

        foreach ($menus as $value) {
            $idParent = DB::table('ms_parent_menus')->insertGetId([
                'label' => $value['label'],
                'alias' => $value['alias'],
                'icon' => $value['icon'],
                'order' => $value['order'],
            ]);

            foreach ($value['child'] as $val) {
                $idMenu = DB::table('ms_menus')->insertGetId([
                    'parent_id' => $idParent,
                    'label' => $val['label'],
                    'alias' => $val['alias'],
                    'url' => $val['url'],
                    'order' => $val['order']
                ]);

                foreach ($val['privilege'] as $v) {
                    $idPrivi = DB::table('ms_privilege')->insertGetId([
                        'code' => $v['code'],
                        'menu_id' => $idMenu,
                        'modules' => $v['modules'],
                        'desc' => $v['desc']
                    ]);

                    DB::table('map_privilege')->insert([
                        'privilege_group_id' => $idPrivigroup,
                        'privilege_id' => $idPrivi
                    ]);
                }
            }
        }
    }
}
