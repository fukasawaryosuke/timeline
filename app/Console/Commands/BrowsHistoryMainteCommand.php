<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DiaryBrowsHistory;
use App\Models\User;

class BrowsHistoryMainteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:browsHistoryMainte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add user name to brows_history';

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
        echo "start.".PHP_EOL;
        DiaryBrowsHistory::all()->each(function ($item) {
            echo "diary_id: ".$item->diary_id.PHP_EOL;
            $history = $item->history;
            $cnt = count($history);
            for ($i=0; $i<$cnt; $i++) {
                // foreach($item->history as $history) {
                echo "human id: ".$history[$i]["human_id"];
                if (isset($history[$i]["name"])) {
                    echo ", name: ".$history[$i]["name"].PHP_EOL;
                } else {
                    echo ", name: has no name.";
                    $user = User::where("human_id", $history[$i]["human_id"])->first();
                    if ($user) {
                        $history[$i]["name"] = $user->user_name;
                        echo ", name set: ".$user->user_name.PHP_EOL;
                    } else {
                        echo ", user not found".PHP_EOL;
                    }
                }
            }
            $item->history = $history;
            $item->save();
        });
        return 0;
    }
}
