<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;

class SyncOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncOrganization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '組織情報を取得';

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
        $this->info('sync organization start.');
        $organizations = null;
        if (config('app.env') == 'local') {
            // 内部で用意したJSONを取得
            $stubRes = json_decode($this->getStubData());
            $organizations = $stubRes->data;
        } else {
            // 統合DB APIへリクエスト
            $client = new \GuzzleHttp\Client([
                'headers' => ['Content-Type' => 'application/json', 'x-apigw-api-id' => config('app.db_api_key')],
            ]);
            $res = $client->get(
                config('app.db_api_base').'/api/hat/organization',
            );
            $body = json_decode($res->getBody());
            $organizations = $body->data;
        }

        foreach ($organizations as $organization) {
            $this->info($organization->division_cd);
            Organization::updateOrCreate(
                [
                    'division_cd' => intval(trim($organization->division_cd))
                ],
                [
                    'organization_name' => trim($organization->organization_name),
                    'level' => intval(trim($organization->level)),
                    'display_order' => intval(trim($organization->display_order)),
                    'parent_division_cd'  => (empty($organization->parent_division_cd))? null: intval(trim($organization->parent_division_cd)),
                    'is_last_level' => intval(trim($organization->is_last_level)),
                    'in_house_system_id' => 1,
                    'synced_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        $this->info('sync organization finish.');
        return 0;
    }

    public function getStubData()
    {
        return <<<EOJ
            {
                "success": true,
                "message": "",
                "data": [
                    {
                        "display_order": "34",
                        "division_cd": "273460    ",
                        "organization_name": "行政大宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "35",
                        "division_cd": "273480    ",
                        "organization_name": "行政天王寺",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "273420    ",
                        "organization_name": "旧法人梅田",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "273212    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "273210    ",
                        "organization_name": "旧法人大阪",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "273212    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "273350    ",
                        "organization_name": "旧法人福岡",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "273312    "
                    },
                    {
                        "display_order": "33",
                        "division_cd": "273470    ",
                        "organization_name": "行政銀座",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "102",
                        "division_cd": "202010    ",
                        "organization_name": "ＨＡＡ新宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "201610    ",
                        "organization_name": "ＨＡＡ銀座",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "106",
                        "division_cd": "201830    ",
                        "organization_name": "ＨＡＡ大井町",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "108",
                        "division_cd": "201418    ",
                        "organization_name": "ＨＡＡ柏",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "110",
                        "division_cd": "202110    ",
                        "organization_name": "ＨＡＡ横浜",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "112",
                        "division_cd": "202210    ",
                        "organization_name": "ＨＡＡ立川",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "114",
                        "division_cd": "201510    ",
                        "organization_name": "ＨＡＡ千葉",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "116",
                        "division_cd": "203110    ",
                        "organization_name": "ＨＡＡ静岡駅前",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "118",
                        "division_cd": "203210    ",
                        "organization_name": "ＨＡＡ浜松駅前",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "120",
                        "division_cd": "203410    ",
                        "organization_name": "ＨＡＡ名駅",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "122",
                        "division_cd": "204710    ",
                        "organization_name": "ＨＡＡ天王寺",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "124",
                        "division_cd": "204310    ",
                        "organization_name": "ＨＡＡ西大寺",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "126",
                        "division_cd": "205010    ",
                        "organization_name": "ＨＡＡ和歌山",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "128",
                        "division_cd": "204110    ",
                        "organization_name": "ＨＡＡ京都",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "130",
                        "division_cd": "204410    ",
                        "organization_name": "ＨＡＡ大阪梅田",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "132",
                        "division_cd": "205210    ",
                        "organization_name": "ＨＡＡ心斎橋",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "134",
                        "division_cd": "204810    ",
                        "organization_name": "ＨＡＡ三宮",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "52",
                        "division_cd": "223000    ",
                        "organization_name": "学童営業部",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "240070    ",
                        "organization_name": "北日本営業課",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "240010    ",
                        "organization_name": "首都圏営業課",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "240020    ",
                        "organization_name": "中部営業課",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "240030    ",
                        "organization_name": "近畿営業課",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "240040    ",
                        "organization_name": "西日本営業課",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "273450    ",
                        "organization_name": "渉外梅田",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "270000    "
                    },
                    {
                        "display_order": "27",
                        "division_cd": "222130    ",
                        "organization_name": "学校営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "270000    "
                    },
                    {
                        "display_order": "29",
                        "division_cd": "273132    ",
                        "organization_name": "法人東日本エリア",
                        "level": "4",
                        "is_last_level": "0",
                        "parent_division_cd": "270000    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "273212    ",
                        "organization_name": "旧法人中日本エリア",
                        "level": "4",
                        "is_last_level": "0",
                        "parent_division_cd": "270000    "
                    },
                    {
                        "display_order": "33",
                        "division_cd": "273312    ",
                        "organization_name": "旧法人西日本エリア",
                        "level": "4",
                        "is_last_level": "0",
                        "parent_division_cd": "270000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "270001    ",
                        "organization_name": "HA行政営業部",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "131710    ",
                        "organization_name": "ＨＬ札幌募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "131730    ",
                        "organization_name": "ＨＬ札幌学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "22",
                        "division_cd": "131750    ",
                        "organization_name": "ＨＬ札幌夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "131610    ",
                        "organization_name": "ＨＬ仙台募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "26",
                        "division_cd": "131630    ",
                        "organization_name": "ＨＬ仙台学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "131650    ",
                        "organization_name": "ＨＬ仙台夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "30",
                        "division_cd": "133210    ",
                        "organization_name": "ＨＬ大宮募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "133230    ",
                        "organization_name": "ＨＬ大宮学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "32",
                        "division_cd": "133250    ",
                        "organization_name": "ＨＬ大宮夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "33",
                        "division_cd": "132810    ",
                        "organization_name": "ＨＬ柏募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "132830    ",
                        "organization_name": "ＨＬ柏学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "36",
                        "division_cd": "132850    ",
                        "organization_name": "ＨＬ柏夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "38",
                        "division_cd": "132210    ",
                        "organization_name": "ＨＬ新宿募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "40",
                        "division_cd": "132230    ",
                        "organization_name": "ＨＬ新宿学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "132250    ",
                        "organization_name": "ＨＬ新宿夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "44",
                        "division_cd": "132610    ",
                        "organization_name": "ＨＬ原宿募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "46",
                        "division_cd": "132630    ",
                        "organization_name": "ＨＬ原宿学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "47",
                        "division_cd": "132650    ",
                        "organization_name": "ＨＬ原宿夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "49",
                        "division_cd": "131310    ",
                        "organization_name": "ＨＬ東京募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "50",
                        "division_cd": "131330    ",
                        "organization_name": "ＨＬ東京学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "52",
                        "division_cd": "131350    ",
                        "organization_name": "ＨＬ東京夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "53",
                        "division_cd": "133410    ",
                        "organization_name": "HL秋葉原募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "55",
                        "division_cd": "133430    ",
                        "organization_name": "HL秋葉原学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "133450    ",
                        "organization_name": "HL秋葉原夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "59",
                        "division_cd": "132310    ",
                        "organization_name": "ＨＬ横浜募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "61",
                        "division_cd": "132330    ",
                        "organization_name": "ＨＬ横浜学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "63",
                        "division_cd": "132350    ",
                        "organization_name": "ＨＬ横浜夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "65",
                        "division_cd": "133310    ",
                        "organization_name": "ＨＬ静岡募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "68",
                        "division_cd": "133330    ",
                        "organization_name": "ＨＬ静岡学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "71",
                        "division_cd": "133350    ",
                        "organization_name": "ＨＬ静岡夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "74",
                        "division_cd": "132130    ",
                        "organization_name": "ＨＬ河口湖学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "76",
                        "division_cd": "131410    ",
                        "organization_name": "ＨＬ名古募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "78",
                        "division_cd": "131430    ",
                        "organization_name": "ＨＬ名古学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "80",
                        "division_cd": "131450    ",
                        "organization_name": "ＨＬ名古夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "82",
                        "division_cd": "132910    ",
                        "organization_name": "ＨＬ京都募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "84",
                        "division_cd": "132930    ",
                        "organization_name": "ＨＬ京都学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "86",
                        "division_cd": "132950    ",
                        "organization_name": "ＨＬ京都夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "273230    ",
                        "organization_name": "行政札幌",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "273240    ",
                        "organization_name": "行政仙台",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "273260    ",
                        "organization_name": "行政宇都宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "10",
                        "division_cd": "273130    ",
                        "organization_name": "行政埼玉",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "273390    ",
                        "organization_name": "行政新宿",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "273160    ",
                        "organization_name": "行政立川",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "16",
                        "division_cd": "273150    ",
                        "organization_name": "行政横浜",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "273400    ",
                        "organization_name": "行政名古屋",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "273370    ",
                        "organization_name": "行政鹿児島",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "019909    ",
                        "organization_name": "ＨＡ本社",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "011004    ",
                        "organization_name": "検定室",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "011002    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "012006    ",
                        "organization_name": "CSｺﾝﾌﾟﾗｲｱﾝｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "019924    ",
                        "organization_name": "ＩＴ企画推進室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "019922    ",
                        "organization_name": "ＨＡ人材開発室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "019920    ",
                        "organization_name": "HA戦略本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "13",
                        "division_cd": "191310    ",
                        "organization_name": "サービス",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "013110    ",
                        "organization_name": "HA管理本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "019916    ",
                        "organization_name": "HA運営本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "43",
                        "division_cd": "100000    ",
                        "organization_name": "全日制事業本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "49",
                        "division_cd": "140000    ",
                        "organization_name": "日本語教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "019914    ",
                        "organization_name": "ＦＣ・海外事業推進室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "019920    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "012002    ",
                        "organization_name": "HA教材・映像制作課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "019920    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "100010    ",
                        "organization_name": "全日制事業戦略",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "100014    ",
                        "organization_name": "全日制教学室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "13",
                        "division_cd": "222110    ",
                        "organization_name": "短期留学課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "17",
                        "division_cd": "240005    ",
                        "organization_name": "ＦＣ教務課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "88",
                        "division_cd": "131110    ",
                        "organization_name": "ＨＬ大阪募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "90",
                        "division_cd": "131130    ",
                        "organization_name": "ＨＬ大阪学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "92",
                        "division_cd": "131150    ",
                        "organization_name": "ＨＬ大阪夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "94",
                        "division_cd": "131210    ",
                        "organization_name": "ＨＬ神戸募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "96",
                        "division_cd": "131230    ",
                        "organization_name": "ＨＬ神戸学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "98",
                        "division_cd": "131250    ",
                        "organization_name": "ＨＬ神戸夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "100",
                        "division_cd": "132510    ",
                        "organization_name": "ＨＬ広島募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "102",
                        "division_cd": "132530    ",
                        "organization_name": "ＨＬ広島学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "132550    ",
                        "organization_name": "ＨＬ広島夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "106",
                        "division_cd": "133110    ",
                        "organization_name": "ＨＬ北九州募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "108",
                        "division_cd": "133130    ",
                        "organization_name": "ＨＬ北九州学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "110",
                        "division_cd": "133150    ",
                        "organization_name": "ＨＬ北九州夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "112",
                        "division_cd": "131510    ",
                        "organization_name": "ＨＬ福岡募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "114",
                        "division_cd": "131530    ",
                        "organization_name": "ＨＬ福岡学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "116",
                        "division_cd": "131550    ",
                        "organization_name": "ＨＬ福岡夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "118",
                        "division_cd": "132410    ",
                        "organization_name": "ＨＬ那覇募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "120",
                        "division_cd": "132430    ",
                        "organization_name": "ＨＬ那覇学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "122",
                        "division_cd": "132450    ",
                        "organization_name": "ＨＬ那覇夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "124",
                        "division_cd": "133510    ",
                        "organization_name": "ＨＬ鹿児島募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "126",
                        "division_cd": "133530    ",
                        "organization_name": "ＨＬ鹿児島学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "128",
                        "division_cd": "133550    ",
                        "organization_name": "ＨＬ鹿児島夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "130",
                        "division_cd": "141110    ",
                        "organization_name": "HIUC東京募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "132",
                        "division_cd": "141130    ",
                        "organization_name": "HIUC東京学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "134",
                        "division_cd": "141210    ",
                        "organization_name": "HIUC大阪募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "136",
                        "division_cd": "141230    ",
                        "organization_name": "HIUC大阪学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "138",
                        "division_cd": "223110    ",
                        "organization_name": "学童保育大井町",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "270",
                        "division_cd": "223140    ",
                        "organization_name": "ひらくきっず横浜みなとみらい",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "271",
                        "division_cd": "144130    ",
                        "organization_name": "ＭＢＡ東京",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "274",
                        "division_cd": "136520    ",
                        "organization_name": "ｅ－Ｓｐｏｒｔｓ営業",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "207020    ",
                        "organization_name": "日本語講師ＰＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "136",
                        "division_cd": "206110    ",
                        "organization_name": "ＨＡＡ岡山",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "138",
                        "division_cd": "206410    ",
                        "organization_name": "ＨＡＡ広島",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "140",
                        "division_cd": "206210    ",
                        "organization_name": "ＨＡＡ高松",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "142",
                        "division_cd": "206610    ",
                        "organization_name": "ＨＡＡ福岡",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "144",
                        "division_cd": "206710    ",
                        "organization_name": "ＨＡＡ北九州",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "147",
                        "division_cd": "206810    ",
                        "organization_name": "ＨＡＡ熊本",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "155",
                        "division_cd": "201034    ",
                        "organization_name": "東日本ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "191320    ",
                        "organization_name": "保険ｻｰﾋﾞｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "191310    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "191330    ",
                        "organization_name": "ﾄﾗﾍﾞﾙｻｰﾋﾞｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "191310    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "207230    ",
                        "organization_name": "HA別所教室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "41",
                        "division_cd": "207270    ",
                        "organization_name": "HA茨木教室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "50",
                        "division_cd": "273000    ",
                        "organization_name": "ＨＡＡ行政",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "270000    ",
                        "organization_name": "ＨＡＡ渉外本部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "92",
                        "division_cd": "201110    ",
                        "organization_name": "ＨＡＡ札幌",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "94",
                        "division_cd": "201210    ",
                        "organization_name": "ＨＡＡ仙台",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "96",
                        "division_cd": "201819    ",
                        "organization_name": "ＨＡＡ宇都宮",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "241",
                        "division_cd": "207200    ",
                        "organization_name": "カルチャー営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "200044    ",
                        "organization_name": "HA通信営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "012003    ",
                        "organization_name": "HA通信運営課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "011003    ",
                        "organization_name": "通信物販課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "10",
                        "division_cd": "200102    ",
                        "organization_name": "ＨＡＡ広告宣伝課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200105    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "125010    ",
                        "organization_name": "HA 幼児教室",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "207200    "
                    },
                    {
                        "display_order": "30",
                        "division_cd": "207250    ",
                        "organization_name": "ｶﾙﾁｬｰ成増校",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "207200    "
                    },
                    {
                        "display_order": "11",
                        "division_cd": "240090    ",
                        "organization_name": "算数準備室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "242",
                        "division_cd": "200051    ",
                        "organization_name": "通信営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "207290    ",
                        "organization_name": "ｶﾙﾁｬｰ長津田校",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "207200    "
                    },
                    {
                        "display_order": "48",
                        "division_cd": "207300    ",
                        "organization_name": "ｶﾙﾁｬｰ名古屋校",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "207200    "
                    },
                    {
                        "display_order": "55",
                        "division_cd": "207280    ",
                        "organization_name": "ｶﾙﾁｬｰ碑文谷校",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "207200    "
                    },
                    {
                        "display_order": "61",
                        "division_cd": "207310    ",
                        "organization_name": "GAS東戸塚校",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "207200    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "222120    ",
                        "organization_name": "×企業営業課HAES",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "222121    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "240004    ",
                        "organization_name": "ＨＡＫコンテンツ課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "17",
                        "division_cd": "240001    ",
                        "organization_name": "ＲＯＢＯＴ営業部",
                        "level": "4",
                        "is_last_level": "0",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "240080    ",
                        "organization_name": "SG営業部",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "47",
                        "division_cd": "240060    ",
                        "organization_name": "ＧＥ営業部",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "48",
                        "division_cd": "200000    ",
                        "organization_name": "社会人事業本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "273440    ",
                        "organization_name": "渉外新宿",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "270000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "200105    ",
                        "organization_name": "社会人事業戦略",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "200106    ",
                        "organization_name": "社会人教学室",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "012003    ",
                        "organization_name": "HA通信運営課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200105    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "011002    ",
                        "organization_name": "ＨＡＡ商品開発",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200105    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "011004    ",
                        "organization_name": "検定室",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200106    "
                    },
                    {
                        "display_order": "149",
                        "division_cd": "206910    ",
                        "organization_name": "ＨＡＡ鹿児島",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "151",
                        "division_cd": "207110    ",
                        "organization_name": "ＨＡＡ那覇",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "273",
                        "division_cd": "240000    ",
                        "organization_name": "児童教育営業本部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "21",
                        "division_cd": "273200    ",
                        "organization_name": "行政京都",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "98",
                        "division_cd": "201719    ",
                        "organization_name": "ＨＡＡ新潟",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "100",
                        "division_cd": "201710    ",
                        "organization_name": "ＨＡＡ大宮",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "161",
                        "division_cd": "206032    ",
                        "organization_name": "中四国ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "239",
                        "division_cd": "206030    ",
                        "organization_name": "九州ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "153",
                        "division_cd": "201030    ",
                        "organization_name": "首都圏ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "157",
                        "division_cd": "203030    ",
                        "organization_name": "中部東海ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "159",
                        "division_cd": "204030    ",
                        "organization_name": "近畿ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "275",
                        "division_cd": "136510    ",
                        "organization_name": "ＨＬ渉外営業",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "151110    ",
                        "organization_name": "ＨＡＪＬ東京",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "151210    ",
                        "organization_name": "ＨＡＪＬ大阪",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "32",
                        "division_cd": "151220    ",
                        "organization_name": "ＨＡＪＬ佐賀",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "151230    ",
                        "organization_name": "ＨＡＪＬ渉外",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "36",
                        "division_cd": "222121    ",
                        "organization_name": "ｱｳﾄﾊﾞｳﾝﾄﾞ営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "40",
                        "division_cd": "889909    ",
                        "organization_name": "ＬＩＫ本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "559909    ",
                        "organization_name": "ＨＰ本社",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "550030    ",
                        "organization_name": "HP大阪ｴｳﾞｪｯｻ（ｽﾎﾟﾝｻ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "550020    ",
                        "organization_name": "HP大阪ｴｳﾞｪｯｻ（ﾁｹｯﾄ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "15",
                        "division_cd": "550160    ",
                        "organization_name": "HP舞洲アリーナ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "550152    ",
                        "organization_name": "HP新規事業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "550130    ",
                        "organization_name": "HP 映像ｻｰﾋﾞｽ課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "550140    ",
                        "organization_name": "HP広告事業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "550150    ",
                        "organization_name": "HP広告課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "550170    ",
                        "organization_name": "HP購買事業課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "27",
                        "division_cd": "550180    ",
                        "organization_name": "HP飲食部門",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "550190    ",
                        "organization_name": "HP MD事業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "449909    ",
                        "organization_name": "HDC本社事業部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "441120    ",
                        "organization_name": "営業本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "441132    ",
                        "organization_name": "ＨＩ本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "589909    ",
                        "organization_name": "HT本社事業部",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "589920    ",
                        "organization_name": "HT首都圏紹介営業本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "589909    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "581020    ",
                        "organization_name": "HT近畿紹介営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "589909    "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "581060    ",
                        "organization_name": "HT新規事業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "589909    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "589910    ",
                        "organization_name": "HT経営管理室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "589909    "
                    },
                    {
                        "display_order": "26",
                        "division_cd": "589950    ",
                        "organization_name": "HTﾏｰｹﾃｨﾝｸﾞ部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "589909    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "581010    ",
                        "organization_name": "HT首都圏紹介第１営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "589920    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "581030    ",
                        "organization_name": "HT首都圏紹介第２営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "589920    "
                    },
                    {
                        "display_order": "36",
                        "division_cd": "273490    ",
                        "organization_name": "行政心斎橋",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "13",
                        "division_cd": "273140    ",
                        "organization_name": "旧法人東京",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "273132    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "273190    ",
                        "organization_name": "旧法人名古屋",
                        "level": "5",
                        "is_last_level": "1",
                        "parent_division_cd": "273212    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "273410    ",
                        "organization_name": "行政梅田",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "273220    ",
                        "organization_name": "行政三宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "27",
                        "division_cd": "273320    ",
                        "organization_name": "行政広島",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "29",
                        "division_cd": "273310    ",
                        "organization_name": "行政岡山",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "273430    ",
                        "organization_name": "行政福岡",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "32",
                        "division_cd": "273340    ",
                        "organization_name": "行政北九州",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "219",
                        "division_cd": "364021    ",
                        "organization_name": "八尾の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "221",
                        "division_cd": "364046    ",
                        "organization_name": "大蓮の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "223",
                        "division_cd": "364014    ",
                        "organization_name": "あべのDS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "225",
                        "division_cd": "399017    ",
                        "organization_name": "あべの（居）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "227",
                        "division_cd": "364042    ",
                        "organization_name": "ひしき乃湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "229",
                        "division_cd": "364043    ",
                        "organization_name": "岸和田の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "231",
                        "division_cd": "364035    ",
                        "organization_name": "泉大津の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "233",
                        "division_cd": "364047    ",
                        "organization_name": "日根野湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "235",
                        "division_cd": "364044    ",
                        "organization_name": "河内長野の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "237",
                        "division_cd": "364026    ",
                        "organization_name": "なにわ乃湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "239",
                        "division_cd": "354311    ",
                        "organization_name": "東住吉の湯DST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "241",
                        "division_cd": "364028    ",
                        "organization_name": "南花田の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "243",
                        "division_cd": "364013    ",
                        "organization_name": "ひらお湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "245",
                        "division_cd": "354012    ",
                        "organization_name": "よどがわDST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "247",
                        "division_cd": "399005    ",
                        "organization_name": "よどがわ（居）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "249",
                        "division_cd": "364024    ",
                        "organization_name": "豊里湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "251",
                        "division_cd": "364033    ",
                        "organization_name": "守口の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "253",
                        "division_cd": "364017    ",
                        "organization_name": "みのお乃湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "255",
                        "division_cd": "364999    ",
                        "organization_name": "みのお乃宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "257",
                        "division_cd": "364029    ",
                        "organization_name": "城東の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "259",
                        "division_cd": "394011    ",
                        "organization_name": "城東の湯GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "261",
                        "division_cd": "364019    ",
                        "organization_name": "甲子園の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "263",
                        "division_cd": "364040    ",
                        "organization_name": "宝塚の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "265",
                        "division_cd": "364025    ",
                        "organization_name": "豊中の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "267",
                        "division_cd": "364050    ",
                        "organization_name": "山科の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "269",
                        "division_cd": "394013    ",
                        "organization_name": "山科GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "271",
                        "division_cd": "364049    ",
                        "organization_name": "伏見の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "569909    ",
                        "organization_name": "ＯＥＳＣ本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "550010    ",
                        "organization_name": "HP大阪ｴｳﾞｪｯｻ（チーム）",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "124",
                        "division_cd": "361048    ",
                        "organization_name": "宮前の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "126",
                        "division_cd": "392014    ",
                        "organization_name": "麻生GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "128",
                        "division_cd": "361042    ",
                        "organization_name": "麻生の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "130",
                        "division_cd": "392024    ",
                        "organization_name": "菅仙谷ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "132",
                        "division_cd": "361051    ",
                        "organization_name": "菅仙谷の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "134",
                        "division_cd": "392019    ",
                        "organization_name": "府中GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "136",
                        "division_cd": "392023    ",
                        "organization_name": "大野台GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "138",
                        "division_cd": "361050    ",
                        "organization_name": "大野台の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "140",
                        "division_cd": "392017    ",
                        "organization_name": "八王子GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "142",
                        "division_cd": "361046    ",
                        "organization_name": "八王子の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "144",
                        "division_cd": "392026    ",
                        "organization_name": "麻生GH２号",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "146",
                        "division_cd": "351411    ",
                        "organization_name": "南与野の湯ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "148",
                        "division_cd": "399001    ",
                        "organization_name": "南与野の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "150",
                        "division_cd": "399002    ",
                        "organization_name": "南与野の湯（居",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "152",
                        "division_cd": "361052    ",
                        "organization_name": "高幡不動の湯ＤＳＴ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "154",
                        "division_cd": "361028    ",
                        "organization_name": "武南DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "156",
                        "division_cd": "392015    ",
                        "organization_name": "見沼GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "158",
                        "division_cd": "361043    ",
                        "organization_name": "見沼の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "160",
                        "division_cd": "361053    ",
                        "organization_name": "大泉学園の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "162",
                        "division_cd": "392027    ",
                        "organization_name": "大泉学園GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "164",
                        "division_cd": "392021    ",
                        "organization_name": "入間GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "166",
                        "division_cd": "381012    ",
                        "organization_name": "鳩ヶ谷の郷",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "168",
                        "division_cd": "361022    ",
                        "organization_name": "鳩ヶ谷の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "170",
                        "division_cd": "392018    ",
                        "organization_name": "浦和の樹",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "172",
                        "division_cd": "361047    ",
                        "organization_name": "浦和の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "174",
                        "division_cd": "381011    ",
                        "organization_name": "千葉院内の郷",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "176",
                        "division_cd": "363019    ",
                        "organization_name": "高針の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "178",
                        "division_cd": "363021    ",
                        "organization_name": "もりやまの湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "180",
                        "division_cd": "363011    ",
                        "organization_name": "滝子乃湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "182",
                        "division_cd": "363013    ",
                        "organization_name": "豊橋の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "184",
                        "division_cd": "363022    ",
                        "organization_name": "岡崎の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "186",
                        "division_cd": "363012    ",
                        "organization_name": "なるこの湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "019909    ",
                        "organization_name": "ＨＡ本社",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "191310    ",
                        "organization_name": "サービス",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "188",
                        "division_cd": "363014    ",
                        "organization_name": "川名の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "190",
                        "division_cd": "363020    ",
                        "organization_name": "本郷の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "192",
                        "division_cd": "363015    ",
                        "organization_name": "千種の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "194",
                        "division_cd": "363016    ",
                        "organization_name": "荒子川DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "196",
                        "division_cd": "363017    ",
                        "organization_name": "堀越DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "198",
                        "division_cd": "363018    ",
                        "organization_name": "太閤の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "200",
                        "division_cd": "363024    ",
                        "organization_name": "平安の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "202",
                        "division_cd": "364011    ",
                        "organization_name": "京都西院DST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "205",
                        "division_cd": "399015    ",
                        "organization_name": "京都西院DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "207",
                        "division_cd": "364031    ",
                        "organization_name": "奈良DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "209",
                        "division_cd": "364041    ",
                        "organization_name": "大東の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "211",
                        "division_cd": "364027    ",
                        "organization_name": "寝屋川の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "213",
                        "division_cd": "364034    ",
                        "organization_name": "釈尊寺の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "215",
                        "division_cd": "364012    ",
                        "organization_name": "たつみ湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "217",
                        "division_cd": "364030    ",
                        "organization_name": "東大阪の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "452160    ",
                        "organization_name": "DDI南大沢店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "422",
                        "division_cd": "361064    ",
                        "organization_name": "20年度新規宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "421",
                        "division_cd": "392045    ",
                        "organization_name": "20年度新規ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "420",
                        "division_cd": "361068    ",
                        "organization_name": "江戸川の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "419",
                        "division_cd": "392044    ",
                        "organization_name": "今宿ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "418",
                        "division_cd": "361067    ",
                        "organization_name": "船橋の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "417",
                        "division_cd": "392043    ",
                        "organization_name": "船橋ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "416",
                        "division_cd": "361066    ",
                        "organization_name": "川崎ＨＰ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "415",
                        "division_cd": "361063    ",
                        "organization_name": "さいたま根岸の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "414",
                        "division_cd": "392042    ",
                        "organization_name": "さいたま根岸ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "413",
                        "division_cd": "392041    ",
                        "organization_name": "川口東本郷ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "412",
                        "division_cd": "392040    ",
                        "organization_name": "さいたま今羽ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "411",
                        "division_cd": "392039    ",
                        "organization_name": "江戸川ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "459909    ",
                        "organization_name": "DDI本社管理部",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "452120    ",
                        "organization_name": "DDI大宮ﾙﾐﾈ店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "452130    ",
                        "organization_name": "DDI浦和パルコ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "452220    ",
                        "organization_name": "DDI宇都宮ﾊﾟｾｵ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "10",
                        "division_cd": "452080    ",
                        "organization_name": "DDIｾﾚｵ八王子店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "455020    ",
                        "organization_name": "DDI名鉄本店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "16",
                        "division_cd": "452020    ",
                        "organization_name": "DDIｱﾄﾚ大井町店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "452030    ",
                        "organization_name": "DDI汐留ｼﾃｨｾﾝﾀｰ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "452150    ",
                        "organization_name": "DDIアトレ川崎店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "22",
                        "division_cd": "452210    ",
                        "organization_name": "DDI相模大野店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "452110    ",
                        "organization_name": "DDI東戸塚店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "359909    ",
                        "organization_name": "HLC本社事業部",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "380050    ",
                        "organization_name": "ﾗｲﾌｹｱｶﾚｯｼﾞ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "361030    ",
                        "organization_name": "厚別DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "361014    ",
                        "organization_name": "大倉湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "359999    ",
                        "organization_name": "大倉湯栄町ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "15",
                        "division_cd": "361019    ",
                        "organization_name": "大倉湯栄町DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "399012    ",
                        "organization_name": "大倉湯栄町（居",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "21",
                        "division_cd": "391011    ",
                        "organization_name": "大倉湯GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "391012    ",
                        "organization_name": "大倉湯GH2号",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "27",
                        "division_cd": "361013    ",
                        "organization_name": "鶴の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "30",
                        "division_cd": "399007    ",
                        "organization_name": "鶴の湯ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "33",
                        "division_cd": "399008    ",
                        "organization_name": "鶴の湯（居宅）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "36",
                        "division_cd": "361035    ",
                        "organization_name": "西新井DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "39",
                        "division_cd": "361033    ",
                        "organization_name": "しらひげ乃湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "410",
                        "division_cd": "364054    ",
                        "organization_name": "今宿の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "409",
                        "division_cd": "361062    ",
                        "organization_name": "王禅寺の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "408",
                        "division_cd": "392038    ",
                        "organization_name": "王禅寺ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "26",
                        "division_cd": "452200    ",
                        "organization_name": "DDI平塚ラスカ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "452240    ",
                        "organization_name": "DDIアズ熊谷店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "29",
                        "division_cd": "457010    ",
                        "organization_name": "DDIルクア大阪",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "457020    ",
                        "organization_name": "DDIｵﾄｶﾘﾃ千里",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "33",
                        "division_cd": "457030    ",
                        "organization_name": "DDIあべのﾊﾙｶｽ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "35",
                        "division_cd": "452170    ",
                        "organization_name": "DDI溝の口店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "37",
                        "division_cd": "452060    ",
                        "organization_name": "DDIｽｶｲﾂﾘｰｿﾗﾏﾁ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "39",
                        "division_cd": "452090    ",
                        "organization_name": "DDIラクーア店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "43",
                        "division_cd": "452140    ",
                        "organization_name": "DDIひばりが丘",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "46",
                        "division_cd": "452050    ",
                        "organization_name": "DDI錦糸町パルコ店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "49",
                        "division_cd": "452310    ",
                        "organization_name": "DDI銀座店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "53",
                        "division_cd": "459010    ",
                        "organization_name": "DDI ﾈｲﾙｶﾚｯｼﾞ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "113",
                        "division_cd": "452180    ",
                        "organization_name": "DDIｼｬﾎﾟｰ本八幡",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "351511    ",
                        "organization_name": "千葉院内の湯ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "45",
                        "division_cd": "399003    ",
                        "organization_name": "千葉院内DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "48",
                        "division_cd": "361020    ",
                        "organization_name": "あじさいの湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "361016    ",
                        "organization_name": "風鈴の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "54",
                        "division_cd": "392013    ",
                        "organization_name": "墨田GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "392012    ",
                        "organization_name": "市川GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "361026    ",
                        "organization_name": "清瀬DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "63",
                        "division_cd": "361017    ",
                        "organization_name": "花水木の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "66",
                        "division_cd": "361037    ",
                        "organization_name": "栄の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "69",
                        "division_cd": "361018    ",
                        "organization_name": "あいぞめの湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "72",
                        "division_cd": "351013    ",
                        "organization_name": "練馬ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "75",
                        "division_cd": "399011    ",
                        "organization_name": "練馬（居宅）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "78",
                        "division_cd": "361012    ",
                        "organization_name": "小松湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "81",
                        "division_cd": "361015    ",
                        "organization_name": "薬師の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "84",
                        "division_cd": "361023    ",
                        "organization_name": "新横浜DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "87",
                        "division_cd": "361029    ",
                        "organization_name": "望地DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "90",
                        "division_cd": "361040    ",
                        "organization_name": "南町田DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "93",
                        "division_cd": "361041    ",
                        "organization_name": "武蔵の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "96",
                        "division_cd": "361027    ",
                        "organization_name": "上溝DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "98",
                        "division_cd": "361025    ",
                        "organization_name": "めじろ台DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "100",
                        "division_cd": "361044    ",
                        "organization_name": "府中DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "102",
                        "division_cd": "361032    ",
                        "organization_name": "稲城の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "361034    ",
                        "organization_name": "永山の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "106",
                        "division_cd": "351311    ",
                        "organization_name": "鶴見ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "108",
                        "division_cd": "361024    ",
                        "organization_name": "平塚DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "110",
                        "division_cd": "361036    ",
                        "organization_name": "星川DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "112",
                        "division_cd": "392011    ",
                        "organization_name": "高津GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "114",
                        "division_cd": "392022    ",
                        "organization_name": "宇奈根ＧＨ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "116",
                        "division_cd": "361049    ",
                        "organization_name": "宇奈根の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "118",
                        "division_cd": "392016    ",
                        "organization_name": "多摩GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "120",
                        "division_cd": "361045    ",
                        "organization_name": "多摩の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "122",
                        "division_cd": "392020    ",
                        "organization_name": "宮前GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "115",
                        "division_cd": "452340    ",
                        "organization_name": "DDIグランデュオ蒲田店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "117",
                        "division_cd": "452350    ",
                        "organization_name": "DDI茗荷谷店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "118",
                        "division_cd": "452330    ",
                        "organization_name": "DDI調布店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "119",
                        "division_cd": "452360    ",
                        "organization_name": "DDI津田沼パルコ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "120",
                        "division_cd": "452370    ",
                        "organization_name": "DDI中野マルイ店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "122",
                        "division_cd": "452380    ",
                        "organization_name": "DDI武蔵浦和ﾏｰﾚ",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "123",
                        "division_cd": "452390    ",
                        "organization_name": "DDI船橋フェイス店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "124",
                        "division_cd": "452400    ",
                        "organization_name": "DDI吉祥寺店",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "459909    "
                    },
                    {
                        "display_order": "29",
                        "division_cd": "550110    ",
                        "organization_name": "HPスクール",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "26",
                        "division_cd": "550200    ",
                        "organization_name": "HPｲﾝﾊﾞｳﾝﾄﾞｻｰﾋﾞｽ課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "001000    ",
                        "organization_name": "HHC会長社長室",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "004000    ",
                        "organization_name": "ＨＨＣ監査役",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "002000    ",
                        "organization_name": "HHC監査室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "003300    ",
                        "organization_name": "財務本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "003600    ",
                        "organization_name": "管理本部（役員",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "003500    ",
                        "organization_name": "経営企画（役員",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "003700    ",
                        "organization_name": "総合戦略（役員",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "17",
                        "division_cd": "009500    ",
                        "organization_name": "HHC経営企画",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "009520    ",
                        "organization_name": "HHCＩＴ戦略",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "21",
                        "division_cd": "009000    ",
                        "organization_name": "HHC本社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "009510    ",
                        "organization_name": "ＨＨＣ総合戦略",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "009300    ",
                        "organization_name": "海外事業本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "32",
                        "division_cd": "600901    ",
                        "organization_name": "HHC総務部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "600201    ",
                        "organization_name": "HHC人事部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "66",
                        "division_cd": "600301    ",
                        "organization_name": "HHC経理部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "68",
                        "division_cd": "600701    ",
                        "organization_name": "HHC情報ｼｽﾃﾑ本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "69",
                        "division_cd": "600401    ",
                        "organization_name": "HHC財務部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "70",
                        "division_cd": "009310    ",
                        "organization_name": "HHC新規事業準備室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "001000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "009100    ",
                        "organization_name": "HHC新規事業",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "009500    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "461130    ",
                        "organization_name": "ＨＤＳ開発・制作本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "29",
                        "division_cd": "301051    ",
                        "organization_name": "HR新規営業（東京１",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "30",
                        "division_cd": "301410    ",
                        "organization_name": "ＨＲ神奈川支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "32",
                        "division_cd": "301460    ",
                        "organization_name": "HR銀座営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "301470    ",
                        "organization_name": "HR銀座第２営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "37",
                        "division_cd": "301052    ",
                        "organization_name": "HRＤＸ営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "38",
                        "division_cd": "302410    ",
                        "organization_name": "ＨＲ千葉支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "40",
                        "division_cd": "301300    ",
                        "organization_name": "ＨＲ茨城支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "301250    ",
                        "organization_name": "HR通信法人営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "43",
                        "division_cd": "301110    ",
                        "organization_name": "東京Ｃ第１営業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "45",
                        "division_cd": "301040    ",
                        "organization_name": "東京Ｃ第２営業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "47",
                        "division_cd": "301810    ",
                        "organization_name": "ＨＲ静岡支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "49",
                        "division_cd": "302610    ",
                        "organization_name": "ＨＲ浜松支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "301310    ",
                        "organization_name": "ＨＲ愛知第１営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "53",
                        "division_cd": "301490    ",
                        "organization_name": "ＨＲ愛知第２営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "55",
                        "division_cd": "301400    ",
                        "organization_name": "HR名古屋ｺﾝｽﾄﾗｸｼｮﾝ課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "56",
                        "division_cd": "332310    ",
                        "organization_name": "HR名古屋ﾒﾃﾞｨｶﾙ課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "58",
                        "division_cd": "305010    ",
                        "organization_name": "ＨＲ豊田支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "304410    ",
                        "organization_name": "HR大阪営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "62",
                        "division_cd": "304420    ",
                        "organization_name": "HR大阪第２営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "64",
                        "division_cd": "304430    ",
                        "organization_name": "HR大阪ｺﾝｽﾄﾗｸｼｮﾝ営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "66",
                        "division_cd": "308040    ",
                        "organization_name": "HR大阪GIT営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "68",
                        "division_cd": "332430    ",
                        "organization_name": "HR京都ﾒﾃﾞｨｶﾙ営業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "70",
                        "division_cd": "301053    ",
                        "organization_name": "HR新規営業（大阪",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "72",
                        "division_cd": "302510    ",
                        "organization_name": "ＨＲ神戸支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "74",
                        "division_cd": "302210    ",
                        "organization_name": "ＨＲ京都支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "76",
                        "division_cd": "304910    ",
                        "organization_name": "ＨＲ金沢支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "78",
                        "division_cd": "301910    ",
                        "organization_name": "ＨＲ広島支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "80",
                        "division_cd": "303310    ",
                        "organization_name": "ＨＲ岡山支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "82",
                        "division_cd": "301510    ",
                        "organization_name": "ＨＲ福岡支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "83",
                        "division_cd": "303710    ",
                        "organization_name": "ＨＲ熊本支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "85",
                        "division_cd": "303910    ",
                        "organization_name": "ＨＲ鹿児島支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "87",
                        "division_cd": "304310    ",
                        "organization_name": "ＨＲ那覇支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "89",
                        "division_cd": "304440    ",
                        "organization_name": "HR東京エリア営",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "90",
                        "division_cd": "304450    ",
                        "organization_name": "HR大阪エリア営",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "92",
                        "division_cd": "304460    ",
                        "organization_name": "京都エリア営業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "94",
                        "division_cd": "304470    ",
                        "organization_name": "神戸エリア営業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "97",
                        "division_cd": "321132    ",
                        "organization_name": "HR人材紹介事業本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "98",
                        "division_cd": "321133    ",
                        "organization_name": "HR ＲＣＡ部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "100",
                        "division_cd": "321130    ",
                        "organization_name": "HR東京人材紹介第1営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "102",
                        "division_cd": "301360    ",
                        "organization_name": "HR東京人材紹介第2営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "321410    ",
                        "organization_name": "HR大阪人材紹介営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "105",
                        "division_cd": "321150    ",
                        "organization_name": "HRﾌﾞﾘｯｼﾞCA営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "107",
                        "division_cd": "308111    ",
                        "organization_name": "HRＧＩＴ統括",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "109",
                        "division_cd": "308110    ",
                        "organization_name": "HR東京GIT第1営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "111",
                        "division_cd": "301450    ",
                        "organization_name": "HR東京GIT第2営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "112",
                        "division_cd": "310050    ",
                        "organization_name": "HR海外リクルーティング部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "114",
                        "division_cd": "301101    ",
                        "organization_name": "HR 北日本C",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "116",
                        "division_cd": "301102    ",
                        "organization_name": "HR 北関東C",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "118",
                        "division_cd": "301100    ",
                        "organization_name": "HR 首都圏C",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "119",
                        "division_cd": "301320    ",
                        "organization_name": "HR 中日本C",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "121",
                        "division_cd": "301200    ",
                        "organization_name": "HR 近畿C",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "123",
                        "division_cd": "301201    ",
                        "organization_name": "HR 中四国C",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "125",
                        "division_cd": "303001    ",
                        "organization_name": "HR 九州C",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "126",
                        "division_cd": "301130    ",
                        "organization_name": "HR首都圏SS",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "128",
                        "division_cd": "301340    ",
                        "organization_name": "HR中日本SS",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "469909    ",
                        "organization_name": "ＨＤＳ本社事業部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "461120    ",
                        "organization_name": "ＨＤＳ東京営業部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "461110    ",
                        "organization_name": "ＨＤＳ大阪営業部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "191320    ",
                        "organization_name": "保険ｻｰﾋﾞｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "191310    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "191330    ",
                        "organization_name": "ﾄﾗﾍﾞﾙｻｰﾋﾞｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "191310    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "549909    ",
                        "organization_name": "大朝アド本社",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "309909    ",
                        "organization_name": "HR本社",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "300100    ",
                        "organization_name": "ＨＲ業務１課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "300000    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "300200    ",
                        "organization_name": "ＨＲ業務２課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "300000    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "300300    ",
                        "organization_name": "ＨＲ業務３課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "300000    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "300210    ",
                        "organization_name": "ＨＲ業務管理課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "300000    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "310080    ",
                        "organization_name": "ﾁｬﾚﾝｼﾞﾄﾞｵﾌｨｽ",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "300000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "310000    ",
                        "organization_name": "経営企画本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "301160    ",
                        "organization_name": "ＭＭ推進室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "310010    ",
                        "organization_name": "DM推進・RCM本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "309912    ",
                        "organization_name": "ﾏｯﾁﾝｸﾞ本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "310004    ",
                        "organization_name": "管理統括本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "11",
                        "division_cd": "301610    ",
                        "organization_name": "ＨＲ札幌支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "302110    ",
                        "organization_name": "ＨＲ仙台支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "301620    ",
                        "organization_name": "ＨＲ盛岡支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "16",
                        "division_cd": "301710    ",
                        "organization_name": "HRさいたま支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "304810    ",
                        "organization_name": "ＨＲ宇都宮支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "305280    ",
                        "organization_name": "ＨＲ前橋支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "21",
                        "division_cd": "305230    ",
                        "organization_name": "ＨＲ新潟支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "301291    ",
                        "organization_name": "ＨＲ首都圏事業本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "301290    ",
                        "organization_name": "HR新宿第1営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "27",
                        "division_cd": "301480    ",
                        "organization_name": "HR新宿第2営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "571030    ",
                        "organization_name": "HMC近畿営業課",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "579909    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "571080    ",
                        "organization_name": "HMC東京第１営",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "579909    "
                    },
                    {
                        "display_order": "11",
                        "division_cd": "322190    ",
                        "organization_name": "HGT事業推進室（開発）",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "519909    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "322180    ",
                        "organization_name": "HGT事業推進室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "519909    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "322111    ",
                        "organization_name": "HGT営業本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "519909    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "519909    ",
                        "organization_name": "HGT本社事業部",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "439909    ",
                        "organization_name": "ゑ美寿本社事業部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "430010    ",
                        "organization_name": "ゑ美寿　大阪店",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "571010    ",
                        "organization_name": "HMC東京第２営",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "579909    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "571012    ",
                        "organization_name": "HMC RCM首都圏",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "571010    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "571013    ",
                        "organization_name": "HMC運営首都圏",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "571080    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "571032    ",
                        "organization_name": "HMC RCM近畿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "571030    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "571033    ",
                        "organization_name": "HMC運営近畿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "571030    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "571040    ",
                        "organization_name": "HMC紹介課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "571010    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "571050    ",
                        "organization_name": "HMC京都支社",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "571030    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "579909    ",
                        "organization_name": "HMC本社事業部",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "579910    ",
                        "organization_name": "HMC経営管理",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "579909    "
                    },
                    {
                        "display_order": "13",
                        "division_cd": "441150    ",
                        "organization_name": "HDC新規営業第1本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "11",
                        "division_cd": "441140    ",
                        "organization_name": "HDC広告プランニング室",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "441110    ",
                        "organization_name": "HDC東京本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "16",
                        "division_cd": "441160    ",
                        "organization_name": "HDC新規営業第2本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "441130    ",
                        "organization_name": "ﾌﾟﾛｼﾞｪｸﾄ・ｻｰﾋﾞｽ管理本部",
                        "level": "1",
                        "is_last_level": "1",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "10",
                        "division_cd": "550040    ",
                        "organization_name": "HP大阪ｴｳﾞｪｯｻ（興行運営）",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "130",
                        "division_cd": "301230    ",
                        "organization_name": "HR大阪SS",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "132",
                        "division_cd": "323252    ",
                        "organization_name": "ＨＲＲＰＡ事業本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "133",
                        "division_cd": "305000    ",
                        "organization_name": "HR RPA事業本部管理部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "135",
                        "division_cd": "323251    ",
                        "organization_name": "HRRPA商品企画部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "137",
                        "division_cd": "323210    ",
                        "organization_name": "HR東日本RPA営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "139",
                        "division_cd": "323220    ",
                        "organization_name": "HR中日本RPA課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "141",
                        "division_cd": "323230    ",
                        "organization_name": "HR西日本RPA営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "143",
                        "division_cd": "323250    ",
                        "organization_name": "HRRPA技術部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "144",
                        "division_cd": "323253    ",
                        "organization_name": "ＨＲＳａａＳ営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "146",
                        "division_cd": "332110    ",
                        "organization_name": "HR東京ﾒﾃﾞｨｶﾙ営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "149",
                        "division_cd": "332410    ",
                        "organization_name": "HR大阪ﾒﾃﾞｨｶﾙ営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "151",
                        "division_cd": "305002    ",
                        "organization_name": "HRメディカル企画推進",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "153",
                        "division_cd": "310090    ",
                        "organization_name": "HRメディカル企画・運営推進部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "154",
                        "division_cd": "305004    ",
                        "organization_name": "HRBPO運営管理課",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "156",
                        "division_cd": "311110    ",
                        "organization_name": "HRBPO営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "453",
                        "division_cd": "311112    ",
                        "organization_name": "HRPSS事業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "454",
                        "division_cd": "301292    ",
                        "organization_name": "ＨＲ ＤＸ営業部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "309909    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "310030    ",
                        "organization_name": "人事総務部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "310004    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "310040    ",
                        "organization_name": "ｺﾝﾌﾟﾗｲｱﾝｽ・CS室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "310004    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "310020    ",
                        "organization_name": "システム運用管理部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "310004    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "300000    ",
                        "organization_name": "ＨＲ業務部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "310004    "
                    },
                    {
                        "display_order": "308",
                        "division_cd": "366019    ",
                        "organization_name": "いまDS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "600601    ",
                        "organization_name": "HHC国際課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "600401    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "600501    ",
                        "organization_name": "ＨＨＣ株式課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "600901    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "600208    ",
                        "organization_name": "HHC西日本管理",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "600901    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "601510    ",
                        "organization_name": "HHC施設管理課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "600901    "
                    },
                    {
                        "display_order": "274",
                        "division_cd": "394012    ",
                        "organization_name": "伏見GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "276",
                        "division_cd": "364051    ",
                        "organization_name": "生野の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "278",
                        "division_cd": "394014    ",
                        "organization_name": "生野GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "280",
                        "division_cd": "364038    ",
                        "organization_name": "きしべ乃湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "282",
                        "division_cd": "364037    ",
                        "organization_name": "利倉DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "284",
                        "division_cd": "354413    ",
                        "organization_name": "板宿ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "286",
                        "division_cd": "364023    ",
                        "organization_name": "板宿の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "288",
                        "division_cd": "364022    ",
                        "organization_name": "灘の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "290",
                        "division_cd": "354412    ",
                        "organization_name": "灘ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "292",
                        "division_cd": "364018    ",
                        "organization_name": "やまのまち湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "294",
                        "division_cd": "354411    ",
                        "organization_name": "鈴蘭台ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "296",
                        "division_cd": "364015    ",
                        "organization_name": "くいせ湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "298",
                        "division_cd": "364016    ",
                        "organization_name": "かみのしま湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "300",
                        "division_cd": "364020    ",
                        "organization_name": "たるみ乃湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "302",
                        "division_cd": "364039    ",
                        "organization_name": "明石の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "13",
                        "division_cd": "550050    ",
                        "organization_name": "HP大阪ｴｳﾞｪｯｻ（広告・販促）",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "559909    "
                    },
                    {
                        "display_order": "304",
                        "division_cd": "364045    ",
                        "organization_name": "加古川の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "306",
                        "division_cd": "392025    ",
                        "organization_name": "高砂GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "310",
                        "division_cd": "366023    ",
                        "organization_name": "かすがDS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "312",
                        "division_cd": "356311    ",
                        "organization_name": "広島ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "314",
                        "division_cd": "366011    ",
                        "organization_name": "金亀湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "316",
                        "division_cd": "396011    ",
                        "organization_name": "大野城の宿GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "318",
                        "division_cd": "396999    ",
                        "organization_name": "大野城の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "320",
                        "division_cd": "366024    ",
                        "organization_name": "土井の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "322",
                        "division_cd": "366020    ",
                        "organization_name": "東平尾DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "324",
                        "division_cd": "366022    ",
                        "organization_name": "長住DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "326",
                        "division_cd": "366015    ",
                        "organization_name": "西公園の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "328",
                        "division_cd": "366017    ",
                        "organization_name": "細工町の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "330",
                        "division_cd": "399030    ",
                        "organization_name": "熊本（居）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "332",
                        "division_cd": "366021    ",
                        "organization_name": "あきつDS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "334",
                        "division_cd": "366018    ",
                        "organization_name": "ふくはまDS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "336",
                        "division_cd": "356513    ",
                        "organization_name": "大濠公園ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "338",
                        "division_cd": "399023    ",
                        "organization_name": "大濠公園（居）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "340",
                        "division_cd": "366013    ",
                        "organization_name": "大橋温泉DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "342",
                        "division_cd": "356512    ",
                        "organization_name": "福岡ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "345",
                        "division_cd": "399022    ",
                        "organization_name": "福岡（居）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "347",
                        "division_cd": "366016    ",
                        "organization_name": "むらさきばるDS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "350",
                        "division_cd": "366012    ",
                        "organization_name": "玉の湯DS",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "352",
                        "division_cd": "356811    ",
                        "organization_name": "鹿児島ST",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "354",
                        "division_cd": "399021    ",
                        "organization_name": "鹿児島（居）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "365",
                        "division_cd": "361054    ",
                        "organization_name": "立川の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "367",
                        "division_cd": "361055    ",
                        "organization_name": "下永谷の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "369",
                        "division_cd": "375210    ",
                        "organization_name": "ﾎｰﾑｻｰﾋﾞｽ札幌",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "371",
                        "division_cd": "380250    ",
                        "organization_name": "HLC紹介事業",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "373",
                        "division_cd": "392028    ",
                        "organization_name": "下永谷GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "375",
                        "division_cd": "392029    ",
                        "organization_name": "新百合ヶ丘GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "377",
                        "division_cd": "392030    ",
                        "organization_name": "川口GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "379",
                        "division_cd": "392031    ",
                        "organization_name": "立川GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "381",
                        "division_cd": "375120    ",
                        "organization_name": "ホームサービス",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "375130    ",
                        "organization_name": "HA西調布保育園",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "375170    ",
                        "organization_name": "HA上石原保育園",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "375140    ",
                        "organization_name": "HA中河原保育園",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "375150    ",
                        "organization_name": "HA大倉山保育園",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "10",
                        "division_cd": "375160    ",
                        "organization_name": "HA印西牧保育園",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "375180    ",
                        "organization_name": "HA多摩川保育園",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "375190    ",
                        "organization_name": "HA蒲田保育園",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "16",
                        "division_cd": "375200    ",
                        "organization_name": "ﾅｰｻﾘｰ受託東工大",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "375110    ",
                        "organization_name": "ﾅｰｻﾘｰ受託八尾",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "375000    "
                    },
                    {
                        "display_order": "383",
                        "division_cd": "392032    ",
                        "organization_name": "葛西GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "385",
                        "division_cd": "392033    ",
                        "organization_name": "市川曽谷GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "387",
                        "division_cd": "392034    ",
                        "organization_name": "市川真間GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "390",
                        "division_cd": "392035    ",
                        "organization_name": "中村橋GH",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "392",
                        "division_cd": "392036    ",
                        "organization_name": "包括新規GH1",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "394",
                        "division_cd": "392037    ",
                        "organization_name": "包括新規GH2",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "397",
                        "division_cd": "361056    ",
                        "organization_name": "中村橋の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "399",
                        "division_cd": "361057    ",
                        "organization_name": "市川真間の宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "401",
                        "division_cd": "361058    ",
                        "organization_name": "包括新規宿1",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "403",
                        "division_cd": "361059    ",
                        "organization_name": "包括新規宿2",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "404",
                        "division_cd": "361060    ",
                        "organization_name": "HLC20年度新規DS1",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "405",
                        "division_cd": "361061    ",
                        "organization_name": "HLC20年度新規DS2",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "406",
                        "division_cd": "364052    ",
                        "organization_name": "HLC20年度新規DS3",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "407",
                        "division_cd": "364053    ",
                        "organization_name": "HLC20年度新規DS4",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "304000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "350010    ",
                        "organization_name": "HLC事業本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "304001    ",
                        "organization_name": "HLC事業戦略室・経営管理室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "11",
                        "division_cd": "350020    ",
                        "organization_name": "HLC管理本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "350012    ",
                        "organization_name": "HLC関東支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "17",
                        "division_cd": "350011    ",
                        "organization_name": "HLC近畿支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "20",
                        "division_cd": "350015    ",
                        "organization_name": "HLC中部東海",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "350014    ",
                        "organization_name": "HLC九州支社",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "26",
                        "division_cd": "380210    ",
                        "organization_name": "ＭＳ鷺沼",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "350030    ",
                        "organization_name": "HLC営業部門",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "30",
                        "division_cd": "380070    ",
                        "organization_name": "HLC国内教育事業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "380110    ",
                        "organization_name": "HLC流山研修センター",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "33",
                        "division_cd": "380080    ",
                        "organization_name": "HLC海外教育事業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "35",
                        "division_cd": "380090    ",
                        "organization_name": "HLC就業促進事業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "37",
                        "division_cd": "380010    ",
                        "organization_name": "HLC関東FC係",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "38",
                        "division_cd": "304000    ",
                        "organization_name": "HLC本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "39",
                        "division_cd": "375000    ",
                        "organization_name": "HLC保育運営本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "359909    "
                    },
                    {
                        "display_order": "55",
                        "division_cd": "273460    ",
                        "organization_name": "行政大宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "273480    ",
                        "organization_name": "行政天王寺",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "59",
                        "division_cd": "273490    ",
                        "organization_name": "行政心斎橋",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "10",
                        "division_cd": "100010    ",
                        "organization_name": "全日制事業戦略",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "13",
                        "division_cd": "100014    ",
                        "organization_name": "全日制教学室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "131710    ",
                        "organization_name": "ＨＬ札幌募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "131730    ",
                        "organization_name": "ＨＬ札幌学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "131750    ",
                        "organization_name": "ＨＬ札幌夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "131610    ",
                        "organization_name": "ＨＬ仙台募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "37",
                        "division_cd": "131630    ",
                        "organization_name": "ＨＬ仙台学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "40",
                        "division_cd": "131650    ",
                        "organization_name": "ＨＬ仙台夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "133210    ",
                        "organization_name": "ＨＬ大宮募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "44",
                        "division_cd": "133230    ",
                        "organization_name": "ＨＬ大宮学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "240000    ",
                        "organization_name": "児童教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "52",
                        "division_cd": "100000    ",
                        "organization_name": "全日制教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "53",
                        "division_cd": "200000    ",
                        "organization_name": "社会人教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "117",
                        "division_cd": "131430    ",
                        "organization_name": "ＨＬ名古学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "120",
                        "division_cd": "131450    ",
                        "organization_name": "ＨＬ名古夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "123",
                        "division_cd": "132910    ",
                        "organization_name": "ＨＬ京都募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "126",
                        "division_cd": "132930    ",
                        "organization_name": "ＨＬ京都学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "129",
                        "division_cd": "132950    ",
                        "organization_name": "ＨＬ京都夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "132",
                        "division_cd": "131110    ",
                        "organization_name": "ＨＬ大阪募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "135",
                        "division_cd": "131130    ",
                        "organization_name": "ＨＬ大阪学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "138",
                        "division_cd": "131150    ",
                        "organization_name": "ＨＬ大阪夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "141",
                        "division_cd": "131210    ",
                        "organization_name": "ＨＬ神戸募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "144",
                        "division_cd": "131230    ",
                        "organization_name": "ＨＬ神戸学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "147",
                        "division_cd": "131250    ",
                        "organization_name": "ＨＬ神戸夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "150",
                        "division_cd": "132510    ",
                        "organization_name": "ＨＬ広島募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "153",
                        "division_cd": "132530    ",
                        "organization_name": "ＨＬ広島学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "156",
                        "division_cd": "132550    ",
                        "organization_name": "ＨＬ広島夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "159",
                        "division_cd": "133110    ",
                        "organization_name": "ＨＬ北九州募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "162",
                        "division_cd": "133130    ",
                        "organization_name": "ＨＬ北九州学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "165",
                        "division_cd": "133150    ",
                        "organization_name": "ＨＬ北九州夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "168",
                        "division_cd": "131510    ",
                        "organization_name": "ＨＬ福岡募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "171",
                        "division_cd": "131530    ",
                        "organization_name": "ＨＬ福岡学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "174",
                        "division_cd": "131550    ",
                        "organization_name": "ＨＬ福岡夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "177",
                        "division_cd": "132410    ",
                        "organization_name": "ＨＬ那覇募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "180",
                        "division_cd": "132430    ",
                        "organization_name": "ＨＬ那覇学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "183",
                        "division_cd": "132450    ",
                        "organization_name": "ＨＬ那覇夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "186",
                        "division_cd": "133510    ",
                        "organization_name": "ＨＬ鹿児島募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "189",
                        "division_cd": "133530    ",
                        "organization_name": "ＨＬ鹿児島学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "192",
                        "division_cd": "133550    ",
                        "organization_name": "ＨＬ鹿児島夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "341",
                        "division_cd": "136520    ",
                        "organization_name": "ｅ－Ｓｐｏｒｔｓ営業",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "200106    ",
                        "organization_name": "社会人教学室",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "36",
                        "division_cd": "207230    ",
                        "organization_name": "HA別所教室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "50",
                        "division_cd": "207270    ",
                        "organization_name": "HA茨木教室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "273000    ",
                        "organization_name": "ＨＡＡ行政",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "201110    ",
                        "organization_name": "ＨＡＡ札幌",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "107",
                        "division_cd": "201210    ",
                        "organization_name": "ＨＡＡ仙台",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "116",
                        "division_cd": "201710    ",
                        "organization_name": "ＨＡＡ大宮",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "119",
                        "division_cd": "202010    ",
                        "organization_name": "ＨＡＡ新宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "122",
                        "division_cd": "201610    ",
                        "organization_name": "ＨＡＡ銀座",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "128",
                        "division_cd": "201418    ",
                        "organization_name": "ＨＡＡ柏",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "131",
                        "division_cd": "202110    ",
                        "organization_name": "ＨＡＡ横浜",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "137",
                        "division_cd": "201510    ",
                        "organization_name": "ＨＡＡ千葉",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "140",
                        "division_cd": "203110    ",
                        "organization_name": "ＨＡＡ静岡駅前",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "143",
                        "division_cd": "203210    ",
                        "organization_name": "ＨＡＡ浜松駅前",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "146",
                        "division_cd": "203410    ",
                        "organization_name": "ＨＡＡ名駅",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "149",
                        "division_cd": "204710    ",
                        "organization_name": "ＨＡＡ天王寺",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "158",
                        "division_cd": "204110    ",
                        "organization_name": "ＨＡＡ京都",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "161",
                        "division_cd": "204410    ",
                        "organization_name": "ＨＡＡ大阪梅田",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "164",
                        "division_cd": "205210    ",
                        "organization_name": "ＨＡＡ心斎橋",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "167",
                        "division_cd": "204810    ",
                        "organization_name": "ＨＡＡ三宮",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "170",
                        "division_cd": "206110    ",
                        "organization_name": "ＨＡＡ岡山",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "173",
                        "division_cd": "206410    ",
                        "organization_name": "ＨＡＡ広島",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "176",
                        "division_cd": "206210    ",
                        "organization_name": "ＨＡＡ高松",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "179",
                        "division_cd": "206610    ",
                        "organization_name": "ＨＡＡ福岡",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "186",
                        "division_cd": "206810    ",
                        "organization_name": "ＨＡＡ熊本",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "189",
                        "division_cd": "206910    ",
                        "organization_name": "ＨＡＡ鹿児島",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "192",
                        "division_cd": "207110    ",
                        "organization_name": "ＨＡＡ那覇",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "195",
                        "division_cd": "201030    ",
                        "organization_name": "首都圏ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "198",
                        "division_cd": "201034    ",
                        "organization_name": "東日本ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "201",
                        "division_cd": "203030    ",
                        "organization_name": "中部東海ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "204",
                        "division_cd": "204030    ",
                        "organization_name": "近畿ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "207",
                        "division_cd": "206032    ",
                        "organization_name": "中四国ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "286",
                        "division_cd": "206030    ",
                        "organization_name": "九州ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "288",
                        "division_cd": "200051    ",
                        "organization_name": "通信直販営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "290",
                        "division_cd": "144130    ",
                        "organization_name": "ＭＢＡ営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "200044    ",
                        "organization_name": "HA通信営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "011003    ",
                        "organization_name": "通信物販課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "011002    ",
                        "organization_name": "ＨＡＡ商品開発",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200106    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "240001    ",
                        "organization_name": "ＲＯＢＯＴ営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "240080    ",
                        "organization_name": "SG営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "223000    ",
                        "organization_name": "学童営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "59",
                        "division_cd": "240110    ",
                        "organization_name": "海外営業本部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "240070    ",
                        "organization_name": "北日本営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "240010    ",
                        "organization_name": "首都圏営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "240020    ",
                        "organization_name": "中部営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "16",
                        "division_cd": "240030    ",
                        "organization_name": "近畿営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "223110    ",
                        "organization_name": "学童保育大井町",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "270001    ",
                        "organization_name": "HA行政営業部",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "273230    ",
                        "organization_name": "行政札幌",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "273240    ",
                        "organization_name": "行政仙台",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "273250    ",
                        "organization_name": "行政新潟",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "15",
                        "division_cd": "273260    ",
                        "organization_name": "行政宇都宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "273130    ",
                        "organization_name": "行政埼玉",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "21",
                        "division_cd": "273390    ",
                        "organization_name": "行政東京",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "273160    ",
                        "organization_name": "行政立川",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "27",
                        "division_cd": "273150    ",
                        "organization_name": "行政横浜",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "29",
                        "division_cd": "273400    ",
                        "organization_name": "行政名古屋",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "32",
                        "division_cd": "273370    ",
                        "organization_name": "行政鹿児島",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "35",
                        "division_cd": "273200    ",
                        "organization_name": "行政京都",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "38",
                        "division_cd": "273410    ",
                        "organization_name": "行政近畿",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "41",
                        "division_cd": "273220    ",
                        "organization_name": "行政三宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "44",
                        "division_cd": "273320    ",
                        "organization_name": "行政広島",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "47",
                        "division_cd": "273310    ",
                        "organization_name": "行政岡山",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "49",
                        "division_cd": "273430    ",
                        "organization_name": "行政福岡",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "273340    ",
                        "organization_name": "行政北九州",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "53",
                        "division_cd": "273470    ",
                        "organization_name": "行政銀座",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "46",
                        "division_cd": "133250    ",
                        "organization_name": "ＨＬ大宮夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "48",
                        "division_cd": "132810    ",
                        "organization_name": "ＨＬ柏募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "132830    ",
                        "organization_name": "ＨＬ柏学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "54",
                        "division_cd": "132850    ",
                        "organization_name": "ＨＬ柏夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "132210    ",
                        "organization_name": "ＨＬ新宿募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "132230    ",
                        "organization_name": "ＨＬ新宿学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "63",
                        "division_cd": "132250    ",
                        "organization_name": "ＨＬ新宿夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "66",
                        "division_cd": "132610    ",
                        "organization_name": "ＨＬ原宿募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "68",
                        "division_cd": "132630    ",
                        "organization_name": "ＨＬ原宿学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "71",
                        "division_cd": "132650    ",
                        "organization_name": "ＨＬ原宿夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "73",
                        "division_cd": "131310    ",
                        "organization_name": "ＨＬ東京募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "76",
                        "division_cd": "131330    ",
                        "organization_name": "ＨＬ東京学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "78",
                        "division_cd": "131350    ",
                        "organization_name": "ＨＬ東京夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "81",
                        "division_cd": "133410    ",
                        "organization_name": "HL秋葉原募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "84",
                        "division_cd": "133430    ",
                        "organization_name": "HL秋葉原学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "87",
                        "division_cd": "133450    ",
                        "organization_name": "HL秋葉原夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "90",
                        "division_cd": "132310    ",
                        "organization_name": "ＨＬ横浜募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "93",
                        "division_cd": "132330    ",
                        "organization_name": "ＨＬ横浜学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "96",
                        "division_cd": "132350    ",
                        "organization_name": "ＨＬ横浜夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "100",
                        "division_cd": "133310    ",
                        "organization_name": "ＨＬ静岡募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "133330    ",
                        "organization_name": "ＨＬ静岡学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "108",
                        "division_cd": "133350    ",
                        "organization_name": "ＨＬ静岡夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "111",
                        "division_cd": "132130    ",
                        "organization_name": "ＨＬ河口湖学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "114",
                        "division_cd": "131410    ",
                        "organization_name": "ＨＬ名古募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "11",
                        "division_cd": "200102    ",
                        "organization_name": "ＨＡＡ広告宣伝室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "240040    ",
                        "organization_name": "九州営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "240100    ",
                        "organization_name": "ＨＭ営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "240090    ",
                        "organization_name": "算数開発課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240100    "
                    },
                    {
                        "display_order": "61",
                        "division_cd": "273380    ",
                        "organization_name": "行政那覇",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "37",
                        "division_cd": "141110    ",
                        "organization_name": "HIUC東京募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "38",
                        "division_cd": "141130    ",
                        "organization_name": "HIUC東京学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "39",
                        "division_cd": "141210    ",
                        "organization_name": "HIUC大阪募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "40",
                        "division_cd": "141230    ",
                        "organization_name": "HIUC大阪学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "136510    ",
                        "organization_name": "児童学校営業課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "011004    ",
                        "organization_name": "検定課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "019920    "
                    },
                    {
                        "display_order": "293",
                        "division_cd": "125010    ",
                        "organization_name": "HA 幼児教室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "019924    ",
                        "organization_name": "ＤＸ戦略本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "240060    ",
                        "organization_name": "児童ＦＣ営業課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "273770    ",
                        "organization_name": "行政日本語",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "013110    ",
                        "organization_name": "HA経営戦略本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "012006    ",
                        "organization_name": "CSｺﾝﾌﾟﾗｲｱﾝｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "019922    ",
                        "organization_name": "ＨＡ人材開発室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "15",
                        "division_cd": "019920    ",
                        "organization_name": "HA経営戦略室",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "43",
                        "division_cd": "019914    ",
                        "organization_name": "ＨＡ海外事業",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "012002    ",
                        "organization_name": "HA教材・映像制作課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "019920    "
                    },
                    {
                        "display_order": "54",
                        "division_cd": "140000    ",
                        "organization_name": "国際教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "23",
                        "division_cd": "151110    ",
                        "organization_name": "ＨＡＪＬ東京",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "151210    ",
                        "organization_name": "ＨＡＪＬ大阪",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "292",
                        "division_cd": "151230    ",
                        "organization_name": "ＨＡ社会人渉外営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "41",
                        "division_cd": "222110    ",
                        "organization_name": "短期留学課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "200105    ",
                        "organization_name": "社会人営業戦略",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "012003    ",
                        "organization_name": "HA通信運営課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "31",
                        "division_cd": "131730    ",
                        "organization_name": "ＨＬ札幌学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "28",
                        "division_cd": "131710    ",
                        "organization_name": "ＨＬ札幌募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "76",
                        "division_cd": "132610    ",
                        "organization_name": "ＨＬ原宿募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "128",
                        "division_cd": "201610    ",
                        "organization_name": "ＨＡＡ銀座",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "158",
                        "division_cd": "204710    ",
                        "organization_name": "ＨＡＡ天王寺",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "37",
                        "division_cd": "131610    ",
                        "organization_name": "ＨＬ仙台募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "40",
                        "division_cd": "131630    ",
                        "organization_name": "ＨＬ仙台学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "131650    ",
                        "organization_name": "ＨＬ仙台夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "131750    ",
                        "organization_name": "ＨＬ札幌夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "78",
                        "division_cd": "132630    ",
                        "organization_name": "ＨＬ原宿学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "81",
                        "division_cd": "132650    ",
                        "organization_name": "ＨＬ原宿夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "117",
                        "division_cd": "133330    ",
                        "organization_name": "ＨＬ静岡学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "120",
                        "division_cd": "133350    ",
                        "organization_name": "ＨＬ静岡夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "123",
                        "division_cd": "132130    ",
                        "organization_name": "ＨＬ河口湖学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "192",
                        "division_cd": "132430    ",
                        "organization_name": "ＨＬ那覇学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "191320    ",
                        "organization_name": "保険ｻｰﾋﾞｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "191310    "
                    },
                    {
                        "display_order": "286",
                        "division_cd": "206032    ",
                        "organization_name": "中四国ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "41",
                        "division_cd": "273220    ",
                        "organization_name": "行政三宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "44",
                        "division_cd": "273320    ",
                        "organization_name": "行政広島",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "47",
                        "division_cd": "273310    ",
                        "organization_name": "行政岡山",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "49",
                        "division_cd": "273430    ",
                        "organization_name": "行政福岡",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "32",
                        "division_cd": "273370    ",
                        "organization_name": "行政鹿児島",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "35",
                        "division_cd": "273200    ",
                        "organization_name": "行政京都",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "38",
                        "division_cd": "273410    ",
                        "organization_name": "行政近畿",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "10",
                        "division_cd": "100010    ",
                        "organization_name": "全日制事業戦略",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "13",
                        "division_cd": "100014    ",
                        "organization_name": "全日制教学室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "273000    ",
                        "organization_name": "ＨＡＡ行政",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "90",
                        "division_cd": "131350    ",
                        "organization_name": "ＨＬ東京夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "93",
                        "division_cd": "133410    ",
                        "organization_name": "HL秋葉原募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "96",
                        "division_cd": "133430    ",
                        "organization_name": "HL秋葉原学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "108",
                        "division_cd": "132330    ",
                        "organization_name": "ＨＬ横浜学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "111",
                        "division_cd": "132350    ",
                        "organization_name": "ＨＬ横浜夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "114",
                        "division_cd": "133310    ",
                        "organization_name": "ＨＬ静岡募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "156",
                        "division_cd": "131230    ",
                        "organization_name": "ＨＬ神戸学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "159",
                        "division_cd": "131250    ",
                        "organization_name": "ＨＬ神戸夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "164",
                        "division_cd": "204410    ",
                        "organization_name": "ＨＡＡ大阪梅田",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "161",
                        "division_cd": "204110    ",
                        "organization_name": "ＨＡＡ京都",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "167",
                        "division_cd": "205210    ",
                        "organization_name": "ＨＡＡ心斎橋",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "170",
                        "division_cd": "204810    ",
                        "organization_name": "ＨＡＡ三宮",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "189",
                        "division_cd": "206810    ",
                        "organization_name": "ＨＡＡ熊本",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "44",
                        "division_cd": "133210    ",
                        "organization_name": "ＨＬ大宮募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "46",
                        "division_cd": "133230    ",
                        "organization_name": "ＨＬ大宮学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "48",
                        "division_cd": "133250    ",
                        "organization_name": "ＨＬ大宮夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "132810    ",
                        "organization_name": "ＨＬ柏募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "171",
                        "division_cd": "133110    ",
                        "organization_name": "ＨＬ北九州募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "174",
                        "division_cd": "133130    ",
                        "organization_name": "ＨＬ北九州学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "177",
                        "division_cd": "133150    ",
                        "organization_name": "ＨＬ北九州夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "126",
                        "division_cd": "131410    ",
                        "organization_name": "ＨＬ名古募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "129",
                        "division_cd": "131430    ",
                        "organization_name": "ＨＬ名古学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "153",
                        "division_cd": "131210    ",
                        "organization_name": "ＨＬ神戸募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "186",
                        "division_cd": "131550    ",
                        "organization_name": "ＨＬ福岡夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "189",
                        "division_cd": "132410    ",
                        "organization_name": "ＨＬ那覇募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "192",
                        "division_cd": "206910    ",
                        "organization_name": "ＨＡＡ鹿児島",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "195",
                        "division_cd": "207110    ",
                        "organization_name": "ＨＡＡ那覇",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "198",
                        "division_cd": "201030    ",
                        "organization_name": "首都圏ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "191330    ",
                        "organization_name": "ﾄﾗﾍﾞﾙｻｰﾋﾞｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "191310    "
                    },
                    {
                        "display_order": "5",
                        "division_cd": "200106    ",
                        "organization_name": "社会人教学室",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "11",
                        "division_cd": "200102    ",
                        "organization_name": "ＨＡＡ広告宣伝室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "107",
                        "division_cd": "201110    ",
                        "organization_name": "ＨＡＡ札幌",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "116",
                        "division_cd": "201210    ",
                        "organization_name": "ＨＡＡ仙台",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "119",
                        "division_cd": "201710    ",
                        "organization_name": "ＨＡＡ大宮",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "200044    ",
                        "organization_name": "HA通信営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "011003    ",
                        "organization_name": "通信物販課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "240001    ",
                        "organization_name": "ＲＯＢＯＴ営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "34",
                        "division_cd": "240080    ",
                        "organization_name": "SG営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "54",
                        "division_cd": "132830    ",
                        "organization_name": "ＨＬ柏学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "132850    ",
                        "organization_name": "ＨＬ柏夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "180",
                        "division_cd": "131510    ",
                        "organization_name": "ＨＬ福岡募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "183",
                        "division_cd": "131530    ",
                        "organization_name": "ＨＬ福岡学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "68",
                        "division_cd": "132210    ",
                        "organization_name": "ＨＬ新宿募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "71",
                        "division_cd": "132230    ",
                        "organization_name": "ＨＬ新宿学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "173",
                        "division_cd": "206110    ",
                        "organization_name": "ＨＡＡ岡山",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "176",
                        "division_cd": "206410    ",
                        "organization_name": "ＨＡＡ広島",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "179",
                        "division_cd": "206210    ",
                        "organization_name": "ＨＡＡ高松",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "186",
                        "division_cd": "206610    ",
                        "organization_name": "ＨＡＡ福岡",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "201",
                        "division_cd": "201034    ",
                        "organization_name": "東日本ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "240100    ",
                        "organization_name": "ＨＭ営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "240090    ",
                        "organization_name": "算数開発課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240100    "
                    },
                    {
                        "display_order": "122",
                        "division_cd": "202010    ",
                        "organization_name": "ＨＡＡ新宿",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "27",
                        "division_cd": "273150    ",
                        "organization_name": "行政横浜",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "29",
                        "division_cd": "273400    ",
                        "organization_name": "行政名古屋",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "43",
                        "division_cd": "019924    ",
                        "organization_name": "ＤＸ戦略本部",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "273770    ",
                        "organization_name": "行政日本語",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "15",
                        "division_cd": "019920    ",
                        "organization_name": "HA経営戦略室",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "273340    ",
                        "organization_name": "行政北九州",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "53",
                        "division_cd": "273470    ",
                        "organization_name": "行政銀座",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "55",
                        "division_cd": "273460    ",
                        "organization_name": "行政大宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "57",
                        "division_cd": "273480    ",
                        "organization_name": "行政天王寺",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "59",
                        "division_cd": "273490    ",
                        "organization_name": "行政心斎橋",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "7",
                        "division_cd": "191310    ",
                        "organization_name": "サービス",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "53",
                        "division_cd": "240000    ",
                        "organization_name": "児童教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "54",
                        "division_cd": "100000    ",
                        "organization_name": "全日制教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "87",
                        "division_cd": "131330    ",
                        "organization_name": "ＨＬ東京学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "42",
                        "division_cd": "222110    ",
                        "organization_name": "短期留学課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "200105    ",
                        "organization_name": "社会人営業戦略",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "012003    ",
                        "organization_name": "HA通信運営課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200051    "
                    },
                    {
                        "display_order": "61",
                        "division_cd": "273380    ",
                        "organization_name": "行政那覇",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "38",
                        "division_cd": "141110    ",
                        "organization_name": "HIUC東京募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "39",
                        "division_cd": "141130    ",
                        "organization_name": "HIUC東京学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "40",
                        "division_cd": "141210    ",
                        "organization_name": "HIUC大阪募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "41",
                        "division_cd": "141230    ",
                        "organization_name": "HIUC大阪学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "294",
                        "division_cd": "125010    ",
                        "organization_name": "HA 幼児教室",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "73",
                        "division_cd": "132250    ",
                        "organization_name": "ＨＬ新宿夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "138",
                        "division_cd": "132930    ",
                        "organization_name": "ＨＬ京都学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "141",
                        "division_cd": "132950    ",
                        "organization_name": "ＨＬ京都夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "132",
                        "division_cd": "131450    ",
                        "organization_name": "ＨＬ名古夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "135",
                        "division_cd": "132910    ",
                        "organization_name": "ＨＬ京都募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "84",
                        "division_cd": "131310    ",
                        "organization_name": "ＨＬ東京募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "204",
                        "division_cd": "203030    ",
                        "organization_name": "中部東海ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "207",
                        "division_cd": "204030    ",
                        "organization_name": "近畿ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "293",
                        "division_cd": "200051    ",
                        "organization_name": "通信直販営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "14",
                        "division_cd": "013110    ",
                        "organization_name": "HA経営戦略本部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "16",
                        "division_cd": "240030    ",
                        "organization_name": "近畿営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "59",
                        "division_cd": "240110    ",
                        "organization_name": "海外営業本部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "240070    ",
                        "organization_name": "北日本営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "8",
                        "division_cd": "240010    ",
                        "organization_name": "首都圏営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "012006    ",
                        "organization_name": "CSｺﾝﾌﾟﾗｲｱﾝｽ課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "013110    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "240020    ",
                        "organization_name": "中部営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "56",
                        "division_cd": "140000    ",
                        "organization_name": "国際教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "55",
                        "division_cd": "200000    ",
                        "organization_name": "社会人教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "100",
                        "division_cd": "133450    ",
                        "organization_name": "HL秋葉原夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "104",
                        "division_cd": "132310    ",
                        "organization_name": "ＨＬ横浜募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "144",
                        "division_cd": "131110    ",
                        "organization_name": "ＨＬ大阪募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "147",
                        "division_cd": "131130    ",
                        "organization_name": "ＨＬ大阪学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "150",
                        "division_cd": "131150    ",
                        "organization_name": "ＨＬ大阪夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "162",
                        "division_cd": "132510    ",
                        "organization_name": "ＨＬ広島募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "341",
                        "division_cd": "132450    ",
                        "organization_name": "ＨＬ那覇夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "165",
                        "division_cd": "132530    ",
                        "organization_name": "ＨＬ広島学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "168",
                        "division_cd": "132550    ",
                        "organization_name": "ＨＬ広島夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "140",
                        "division_cd": "201510    ",
                        "organization_name": "ＨＡＡ千葉",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "143",
                        "division_cd": "203110    ",
                        "organization_name": "ＨＡＡ静岡駅前",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "146",
                        "division_cd": "203210    ",
                        "organization_name": "ＨＡＡ浜松駅前",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "149",
                        "division_cd": "203410    ",
                        "organization_name": "ＨＡＡ名駅",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "131",
                        "division_cd": "201418    ",
                        "organization_name": "ＨＡＡ柏",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "137",
                        "division_cd": "202110    ",
                        "organization_name": "ＨＡＡ横浜",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "288",
                        "division_cd": "206030    ",
                        "organization_name": "九州ＡＣＳ",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "011002    ",
                        "organization_name": "ＨＡＡ商品開発",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "200106    "
                    },
                    {
                        "display_order": "350",
                        "division_cd": "136520    ",
                        "organization_name": "ｅ－Ｓｐｏｒｔｓ営業",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "344",
                        "division_cd": "133510    ",
                        "organization_name": "ＨＬ鹿児島募集",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "345",
                        "division_cd": "133530    ",
                        "organization_name": "ＨＬ鹿児島学務",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "346",
                        "division_cd": "133550    ",
                        "organization_name": "ＨＬ鹿児島夜間",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "12",
                        "division_cd": "273250    ",
                        "organization_name": "行政新潟",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "15",
                        "division_cd": "273260    ",
                        "organization_name": "行政宇都宮",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "18",
                        "division_cd": "273130    ",
                        "organization_name": "行政埼玉",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "21",
                        "division_cd": "273390    ",
                        "organization_name": "行政東京",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "24",
                        "division_cd": "273160    ",
                        "organization_name": "行政立川",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "270001    ",
                        "organization_name": "HA行政営業部",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "6",
                        "division_cd": "273230    ",
                        "organization_name": "行政札幌",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "9",
                        "division_cd": "273240    ",
                        "organization_name": "行政仙台",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "273000    "
                    },
                    {
                        "display_order": "19",
                        "division_cd": "240040    ",
                        "organization_name": "九州営業課",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240001    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "019909    ",
                        "organization_name": "ＨＡ本社",
                        "level": "1",
                        "is_last_level": "0",
                        "parent_division_cd": "          "
                    },
                    {
                        "display_order": "61",
                        "division_cd": "240008    ",
                        "organization_name": "直営営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "223110    ",
                        "organization_name": "学童保育大井町",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240008    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "207230    ",
                        "organization_name": "HA別所教室",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240008    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "207270    ",
                        "organization_name": "HA茨木教室",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "240008    "
                    },
                    {
                        "display_order": "52",
                        "division_cd": "019908    ",
                        "organization_name": "クリエイティブ推進室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "019922    ",
                        "organization_name": "ＨＡ人材開発室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "019914    ",
                        "organization_name": "ＨＡ海外推進室",
                        "level": "2",
                        "is_last_level": "1",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "51",
                        "division_cd": "240060    ",
                        "organization_name": "ＨＡＬＳ営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "240000    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "136510    ",
                        "organization_name": "学校営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "151231    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "151230    ",
                        "organization_name": "渉外営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "151231    "
                    },
                    {
                        "display_order": "3",
                        "division_cd": "144130    ",
                        "organization_name": "ＭＢＡ営業部",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "151231    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "012002    ",
                        "organization_name": "HA教材・映像制作課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "019908    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "012002    ",
                        "organization_name": "HA教材・映像制作課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "019908    "
                    },
                    {
                        "display_order": "25",
                        "division_cd": "100101    ",
                        "organization_name": "商品開発室（全日海外）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "100000    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "200009    ",
                        "organization_name": "内部営業（社会人）",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "200000    "
                    },
                    {
                        "display_order": "60",
                        "division_cd": "151231    ",
                        "organization_name": "ビジネス教育事業部",
                        "level": "2",
                        "is_last_level": "0",
                        "parent_division_cd": "019909    "
                    },
                    {
                        "display_order": "37",
                        "division_cd": "150001    ",
                        "organization_name": "ＨＡＪＬ営業部",
                        "level": "3",
                        "is_last_level": "0",
                        "parent_division_cd": "140000    "
                    },
                    {
                        "display_order": "1",
                        "division_cd": "151110    ",
                        "organization_name": "ＨＡＪＬ東京",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "150001    "
                    },
                    {
                        "display_order": "2",
                        "division_cd": "151210    ",
                        "organization_name": "ＨＡＪＬ大阪",
                        "level": "4",
                        "is_last_level": "1",
                        "parent_division_cd": "150001    "
                    },
                    {
                        "display_order": "4",
                        "division_cd": "011004    ",
                        "organization_name": "検定課",
                        "level": "3",
                        "is_last_level": "1",
                        "parent_division_cd": "151231    "
                    }
                ]
            }
        EOJ;
    }
}
