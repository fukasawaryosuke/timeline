<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Diary;
use Illuminate\Console\Command;

class SyncDiaryDivision extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncDiaryDivision';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '日報の部署を投稿者の部署と同期する';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('sync diary division start.');
        Diary::with('created_user')->get()->each(function ($d) {
            $this->info('diary: id=' . $d->id . ', diary division=' . $d->division_cd . ', user division='.$d->created_user->division_cd);
            if ($d->division_cd != $d->created_user->division_cd) {
                $d->division_cd = $d->created_user->division_cd;
                $d->save();
            }
        });
        $this->info('sync diary division finish.');
        return 0;
    }
}
