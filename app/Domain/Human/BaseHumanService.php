<?php

namespace App\Domain\Human;

/**
 * ヒューマンデータのベースクラス
 */
class BaseHumanService {
    // 外部APIから'member_subject_section'の値が'top'のものを取得
    public function getMemberSubjectSection($array) {
        return array_filter($array, function($val) {
            return $val['member_subject_section'] == 'top';
        });
    }
    // 外部APIから'member_items'内の'member_item_input'の値が'top_image'のものを取得
    public function getMemberItems($array) {
        return array_filter($array[0]['member_items'], function($val) {
            return $val['member_item_input'] == 'top_image';
        });
    }
    // 配列数を確認
    public function existsArray($array) {
        return count($array) <= 0;
    }
    // iconのパスを取得, なければデフォルトのパスを返却
    public function getIconPath($memberItems) {
        $path = $memberItems[0]['item_value'] != '' ? $memberItems[0]['item_value'] : '/assets/img/profile/avatar_04.png';
        $path = str_replace('https://edu-mye.athuman.com', '', $path);
        return $path;
    }
}