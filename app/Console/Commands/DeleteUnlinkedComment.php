<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comment;
use App\Models\CommentNotifications;

class DeleteUnlinkedComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deleteUnlinkedComment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '日報に紐づかないコメントとコメントに紐づかない通知を削除';

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
        echo "comment start.".PHP_EOL;
        Comment::with(['diary'])->get()->each(function($item) {
            echo "comment id: ".$item->id.PHP_EOL;
            if (is_null($item->diary)) {
                echo " not related diary.".PHP_EOL;
                $item->delete();
                echo " delete.".PHP_EOL;
            }
        });
        echo "comment end.".PHP_EOL;

        echo "notification start.".PHP_EOL;
        CommentNotifications::with(['comment'])->get()->each(function($item) {
            echo "notification id: ".$item->id.PHP_EOL;
            if (is_null($item->comment)) {
                echo " not related comment.".PHP_EOL;
                $item->delete();
                echo " delete.".PHP_EOL;
            }
        });
        echo "notification end.".PHP_EOL;
        return 0;
    }
}
