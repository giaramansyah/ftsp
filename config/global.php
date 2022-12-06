<?php
return [
    'sysadmin' => [
        'username' => 'sysadmin',
        'privilege' => 1
    ],

    'dateformat' => [
        'view' => 'd M Y H:i:s'
    ],

    'modules' => [
        'code' => [
            'create' => 1,
            'update' => 2,
            'delete' => 3,
            'readall' => 4,
            'readid' => 5,
        ],
        'desc' => [
            'create' => 'Create Data',
            'update' => 'Update Data',
            'delete' => 'Remove Data',
            'readall' => 'Read List Data',
            'readid' => 'Read Data Detail',
        ],
    ],

    'action' => [
        'form' => [
            'add' => 'new',
            'edit' => 'modify',
            'delete' => 'remove'
        ],
        'password' => [
            'add' => 'new',
            'edit' => 'modify',
            'forget' => 'forget'
        ]
    ],

    'privilege' => [
        'hidden' => [
            'privilege',
        ],
        'static' => [
            'id' => [
                'login' => 1001,
                'logout' => 1010,
                'newpswd' => 1009,
                'modifypswd' => 1090,
                'forgetpswd' => 1900,
            ],
            'code' => [
                'login' => 'AUTH',
                'logout' => 'LOUT',
                'newpswd' => 'NPWD',
                'modifypswd' => 'MPWD',
                'forgetpswd' => 'FPWD',
            ],
            'desc' => [
                'login' => 'Login to app',
                'logout' => 'Logout from app',
                'newpswd' => 'Create new password',
                'modifypswd' => 'Modify password',
                'forgetpswd' => 'Forget password',
            ]
        ]
    ],

    'division' => [
        'code' => [
            'fakultas' => 1,
            'arsitektur' => 2,
            'sipil' => 3,
            'mta' => 4,
            'mts' => 5,
        ],
        'desc' => [
            'fakultas' => 'Fakultas',
            'arsitektur' => 'Arsitektur',
            'sipil' => 'Teknik Sipil',
            'mta' => 'Magister Teknik Arsitektur',
            'mts' => 'Magister Teknik Sipil',
        ],
    ],

    'staff' => [
        'code' => [
            'admin' => 1,
            'dekan' => 2,
            'wd1' => 3,
            'wd2' => 4,
            'wd3' => 5,
            'wd4' => 6,
            'kaprodis1' => 7,
            'kaprodis2' => 8,
        ],
        'desc' => [
            'admin' => 'Admin',
            'dekan' => 'Dekan',
            'wd1' => 'Wakil Dekan 1',
            'wd2' => 'Wakil Dekan 2',
            'wd3' => 'Wakil Dekan 3',
            'wd4' => 'Wakil Dekan 4',
            'kaprodis1' => 'kepala Program Studi S1',
            'kaprodis2' => 'Kepala Program Studi S2',
        ],
        'raw' => [
            'admin' => '',
            'dekan' => 'dekan',
            'wd1' => 'wadeki',
            'wd2' => 'wadekii',
            'wd3' => 'wadekiii',
            'wd4' => 'wadekiv',
            'kaprodis1' => 'kaprodis1',
            'kaprodis2' => 'kaprodis2',
        ]
    ],

    'months' => [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ],

    'validation' => [
        'columns' => [
            'ma_id' => 'MA',
            'description' => 'DESKRIPSI',
            'staff' => 'PIC',
            'amount' => 'DANA',
            'division' => 'UNIT',
        ],
        'regex' => [
            'ma_id' => "/[^0-9.]/",
            'description' => "/[^\s+a-zA-Z0-9]/",
            'staff' => "/[^a-zA-Z0-9,]/",
            'amount' => "/[^0-9]/",
            'division' => "/[^a-zA-Z]/",
        ],
        'limitter' => [
            'ma_id' => 20,
            'description' => 100,
            'staff' => 100,
            'amount' => 20,
            'division' => 20,
        ]
    ],
];
