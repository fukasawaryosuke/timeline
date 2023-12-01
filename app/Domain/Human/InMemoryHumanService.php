<?php

namespace App\Domain\Human;

use App\Domain\Human\IHumanService;
use App\Domain\Human\BaseHumanService;
use Exception;
use Illuminate\Http\Request;

/**
 * ローカル用ヒューマンオブジェクトハンドリングサービス
 */
class InMemoryHumanService extends BaseHumanService implements IHumanService
{
    public function getProfileIcon(Request $request)
    {
        if ($request->humanId == 'undefined') {
            return '';
        }
        $userId = mt_rand(1, 5); // 暫定で5未満の数字のみ対応
        $responseObject = [
            [
                'member_subject_id' => 21,
                'member_subject_name' => 'プロフィール',
                'member_subject_section' => 'top',
                'children' => [],
                'member_items' => [
                    [
                        'member_item_id' => 132,
                        'member_subject_id' => 21,
                        'member_item_name' => 'プロフィール写真',
                        'member_item_input' => 'top_image',
                        'member_is_public' => 0,
                        'member_kind' => 'mk_emp',
                        'is_required' => 0,
                        'is_public' => 0,
                        'item_value' => "/assets/img/profile/avatar_0$userId.png",
                    ],
                ],
            ],
        ];
        $memberSubjectSection = $this->getMemberSubjectSection($responseObject);
        if ($this->existsArray($memberSubjectSection)) {
            throw new Exception('syntax error memberSubjectSection');
        }
        $memberItems = $this->getMemberItems($memberSubjectSection);
        if ($this->existsArray($memberItems)) {
            throw new Exception('syntax error memberItems');
        }
        $iconPath = $this->getIconPath($memberItems);
        $iconUrl = 'https://stg-edu-mye.athuman.com';
        $client = new \GuzzleHttp\Client();
        $iconImage = $client->get($iconUrl . $iconPath);
        return $iconImage->getBody();
    }
}
