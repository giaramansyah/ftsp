<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateTableNote extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_note')->where('status', config('global.status.code.finished'))->update([
            'application_status' => config('global.application_status.code.approve')
        ]);

        $notes = DB::table('ms_note')->where('status', config('global.status.code.finished'))->get(['id', DB::raw('DATE(updated_at) as updated')]);
        foreach($notes as $note) {
            DB::table('ms_note')->where('id', $note->id)->update([
                'disbursed_date' => $note->updated
            ]);
        }
    }
}
