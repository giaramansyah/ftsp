<?php
return [
    'home' => 'Beranda',

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
            'sipil' => 3,
            'arsitektur' => 2,
            'mts' => 5,
            'mta' => 4,
            'dta' => 6,
        ],
        'desc' => [
            'fakultas' => 'Fakultas',
            'sipil' => 'Teknik Sipil',
            'arsitektur' => 'Arsitektur',
            'mts' => 'Magister Teknik Sipil',
            'mta' => 'Magister Teknik Arsitektur',
            'dta' => 'Doktor Arsitektur',
        ],
        'report' => [
            'fakultas' => 'FAKULTAS TEKNIK SIPIL DAN PERENCANAAN UNIVERSITAS TRISAKTI',
            'sipil' => 'PROGRAM STUDI TEKNIK SIPIL UNIVERSITAS TRISAKTI',
            'arsitektur' => 'PROGRAM STUDI TEKNIK ARSITEKTUR UNIVERSITAS TRISAKTI',
            'mts' => 'PROGRAM STUDI MAGISTER TEKNIK SIPIL (S2 MTS) UNIVERSITAS TRISAKTI',
            'mta' => 'PROGRAM STUDI MAGISTER TEKNIK ARSITEKTUR (S2 MTA) UNIVERSITAS TRISAKTI',
            'dta' => 'PROGRAM STUDI DOKTOR ARSITEKTUR (S3 DTA) UNIVERSITAS TRISAKTI',
        ]
    ],

    'compact_division' => [
        'code' => [
            'fakultas' => 99,
            'mts' => 5,
            'mta' => 4,
            'dta' => 6,
        ],
        'desc' => [
            'fakultas' => 'Fakultas S1',
            'mts' => 'Magister Teknik Sipil',
            'mta' => 'Magister Teknik Arsitektur',
            'dta' => 'Doktor Arsitektur',
        ],
        'report' => [
            'fakultas' => 'FAKULTAS TEKNIK SIPIL DAN PERENCANAAN UNIVERSITAS TRISAKTI',
            'mts' => 'PROGRAM STUDI MAGISTER TEKNIK SIPIL (S2 MTS) UNIVERSITAS TRISAKTI',
            'mta' => 'PROGRAM STUDI MAGISTER TEKNIK ARSITEKTUR (S2 MTA) UNIVERSITAS TRISAKTI',
            'dta' => 'PROGRAM STUDI DOKTOR ARSITEKTUR (S3 DTA) UNIVERSITAS TRISAKTI',
        ]
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
            'kaprodis3' => 9,
        ],
        'desc' => [
            'admin' => 'Admin',
            'dekan' => 'Dekan',
            'wd1' => 'Wakil Dekan 1',
            'wd2' => 'Wakil Dekan 2',
            'wd3' => 'Wakil Dekan 3',
            'wd4' => 'Wakil Dekan 4',
            'kaprodis1' => 'Kepala Program Studi S1',
            'kaprodis2' => 'Kepala Program Studi S2',
            'kaprodis3' => 'Kepala Program Studi S3',
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
            'kaprodis3' => 'kaprodis3',
        ],
        'export' => [
            'admin' => '',
            'dekan' => 'Dekan',
            'wd1' => 'Wadek 1',
            'wd2' => 'Wadek 2',
            'wd3' => 'Wadek 3',
            'wd4' => 'Wadek 4',
            'kaprodis1' => 'Kaprodi S1',
            'kaprodis2' => 'Kaprodi S2',
            'kaprodis3' => 'Kaprodi S3',
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

    'type' => [
        'code' => [
            'white' => 1,
            'green' => 2,
            'red' => 3,
        ],
        'desc' => [
            'white' => 'Bon Putih',
            'green' => 'Bon Hijau',
            'red' => 'Bon Merah',
        ],
        'status' => [
            'white' => 'UMD',
            'green' => 'Masuk',
            'red' => 'Selesai',
        ]
    ],

    'status' => [
        'code' => [
            'unfinished' => 0,
            'finished' => 1,
        ],
        'desc' => [
            'unfinished' => 'Belum Selesai',
            'finished' => 'Selesai',
        ],
    ],

    'transaction' => [
        'code' => [
            'debet' => 1,
            'credit' => 2
        ],
        'desc' => [
            'debet' => 'Debit',
            'credit' => 'Kredit'
        ]
    ],

    'report' => [
        'code' => [
            'accountability_fakultas' => 1,
            'accountability' => 2,
            'accountability_umd' => 3,
            'daily' => 4,
            'masterdata' => 5,
        ],
        'desc' => [
            'accountability_fakultas' => 'Lap. Pertanggung Jawaban (Fakultas)',
            'accountability' => 'Lap. Pertanggung Jawaban',
            'accountability_umd' => 'Lap. Pertanggung Jawaban (UMD)',
            'daily' => 'Lap. Harian',
            'masterdata' => 'Data Mata Anggaran',
        ],
        'header' => [
            'accountability_fakultas' => [
                'NO',
                'NO SURAT',
                'TGL SURAT',
                'M.A.',
                'URAIAN',
                '',
                'KREDIT (RP)',
                'DEBET (RP)',
                'DIAJUKAN',
                'DISETUJUI',
                'LAPORAN',
                'SELISIH',
                'SALDO',
                'KET.',
            ],
            'accountability' => [
                'NO',
                'NO SURAT',
                'TGL SURAT',
                'M.A.',
                'URAIAN',
                'DIAJUKAN',
                'DISETUJUI',
                'LAPORAN',
                'SELISIH',
                'SALDO',
                'KET.',
            ],
            'accountability_umd' => [
                'NO',
                'TGL',
                'M.A.',
                'URAIAN',
                'PIC',
                'Rp.',
            ],
            'daily' => [
                'NO',
                'TGL. TRANSAKSI',
                'DESKRIPSI',
                'KREDIT (RP)',
                'DEBET (RP)',
                'NOMINAL (RP)',
                'NO. REKENING',
                'A/N',
            ],
            'masterdata' => [
                'T.A.',
                'MA',
                'DESKRIPSI',
                'PIC',
                'DANA',
                'UNIT',
                'NO. SURAT',
                'TGL. SURAT',
                'TGL. UPLOAD SURAT',
                'PERIHAL',
                'LINK URL',
                'PENGAJUAN DANA',
                'REALISASI DANA',
                'PERSENTASE',
                'SELISIH DANA',
                'BELUM TERSERAP',
                'STATUS',
            ]
        ],
        'excel' => [
            'title' => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ],
            'subtitle' => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ],
            'header' => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    ],
                ]
            ],
            'body' => [
                'font' => [
                    'bold' => false,
                    'size' => 10,
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ]
            ],
            'footer' => [
                'font' => [
                    'bold' => false,
                    'size' => 11,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]
        ]
    ],

    'unit' => [
        'code' => [
            'arsitek' => 1,
            'sipil' => 2,
            'tendik' => 3,
        ],
        'desc' => [
            'arsitek' => 'arsitektur',
            'sipil' => 'sipil',
            'tendik' => 'tendik',
        ],
    ],

    'employee' => [
        'columns' => [
            'nik' => 'NIK',
            'unit_id' => 'UNIT',
            'name' => 'NAMA',
            'account' => 'NOMOR REKENING',
        ],
        'regex' => [
            'nik' => "/[^A-Z0-9]/",
            'unit_id' => "/[^A-Z]/",
            'name' => "/[^\s+a-zA-Z0-9.,]/",
            'account' => "/[^0-9]/",
        ],
        'limitter' => [
            'nik' => 20,
            'unit_id' => 100,
            'name' => 100,
            'account' => 20,
        ]
    ],

    
];
