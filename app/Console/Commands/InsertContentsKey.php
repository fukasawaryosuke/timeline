<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Diary;

class InsertContentsKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:insertContentsKey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '既存の日報のcontentsに統計用のkeyを付与';

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
        echo 'command start';
        Diary::all()->each(function ($d) {
            if (is_null($d->diary)) {
                return true;
            }
            $newData = ['contents' => []];
            echo "diary id: {$d->id}" . PHP_EOL;
            foreach ($d->diary['contents'] as $i => $c) {
                if ($c['title'] === '在籍部署（例：HAA〇〇校）') {
                    $c['key'] = 1;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '〇/〇日報（日付）') {
                    $c['key'] = 2;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '今月の個人テーマ') {
                    $c['key'] = 3;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '日計（計画／実績）') {
                    $c['key'] = 4;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '月間（計画／実績）') {
                    $c['key'] = 5;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '累計（計画／実績）') {
                    $c['key'] = 6;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '今日のマインド天気図') {
                    $c['key'] = 7;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '本日やったこと') {
                    $c['key'] = 8;
                    $c['reportVisible'] = true;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '今日の気付き') {
                    $c['key'] = 9;
                    $c['reportVisible'] = true;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '明日やること') {
                    $c['key'] = 10;
                    $c['reportVisible'] = false;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '自由コメント') {
                    $c['key'] = 11;
                    $c['reportVisible'] = true;
                    $newData['contents'][$i] = $c;
                } elseif ($c['title'] === '本日の写真') {
                    $c['key'] = 12;
                    $c['reportVisible'] = true;
                    $newData['contents'][$i] = $c;
                }
            }
            $d->diary = $newData;
            $d->save();
            echo "id: {$d->id} save" . PHP_EOL;
        });

        echo 'command end';
        return 0;
    }
}
