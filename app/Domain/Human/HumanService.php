<?php

namespace App\Domain\Human;

use App\Models\User;
use App\Domain\Human\IHumanService;
use App\Domain\Human\BaseHumanService;
use Exception;
use Illuminate\Http\Request;

/**
 * ヒューマンオブジェクトハンドリングサービス
 */
class HumanService extends BaseHumanService implements IHumanService
{
    public function getProfileIcon(Request $request)
    {
        // プロフィール画像URLを取得
        $user = User::where("human_id", $request->humanId)->first();
        if ($user == null) {
            return response()->json([
                "code" => 404,
                "message" => "not found user",
            ]);
        }
        $humanId = $user->human_id;
        $client = new \GuzzleHttp\Client([
            "headers" => [
                "Content-Type" => "application/json",
                "x-apigw-api-id" => config("app.db_api_key"),
            ],
        ]);
        $res = $client->get(
            config("app.db_api_base") .
                "/api/member/profile?human_id=" .
                $humanId .
                "&member_kind=mk_emp"
        );
        $body = [];
        if ($res->getStatusCode() == 200) {
            $body = json_decode(
                $res->getBody()->getContents(),
                true
            );
        }

        $memberSubjectSection = $this->getMemberSubjectSection($body);
        if ($this->existsArray($memberSubjectSection)) {
            return response()->json([
                "code" => 404,
                "message" => "not found member_subject_section",
            ]);
        }
        $memberItems = $this->getMemberItems($memberSubjectSection);
        if ($this->existsArray($memberItems)) {
            return response()->json([
                "code" => 404,
                "message" => "not found member_item_input",
            ]);
        }
        $iconPath = $this->getIconPath($memberItems);
        $iconUrl = config("app.profile_icon_url") . $iconPath;
        $iconImage = $client->get($iconUrl);
        return $iconImage->getBody();
    }
}
