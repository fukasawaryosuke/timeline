<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\DiaryNotification;
use App\Models\Diary;
use App\Models\Organization;
use App\Models\User;
use App\Domain\User\UserService;

class MustReadController extends Controller
{
    public function showNotice(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);
        $userDivisionCd = UserService::getUserDivisionCd($request);

        $dairyNotification = DiaryNotification::with(["diary", "diary.in_house_system","diary.in_house_system.notification_setting", "user"])
            ->whereHas('diary.in_house_system', function ($query) use ($userCompanyCode) {
                $query->where('company_code', $userCompanyCode);
            })
            ->latest()
            ->get()
            ->toArray();

        $notifications = [];
        $count = 0;
        foreach ($dairyNotification as $notification) {
            $notification_setting = $notification['diary']['in_house_system']['notification_setting'];
            if(count($notification_setting) !== 0){
                foreach ($notification_setting as $setting) {
                    if($setting['division_cd'] === $userDivisionCd){
                        $notifications[] = $notification;
                    }else{
                        $org = Organization::select('division_cd')->where('parent_division_cd',$setting['division_cd'])->get();
                        if($org !== null){
                            foreach ($org as $orgResult) {
                                if ($userDivisionCd === $orgResult->division_cd) {
                                    $notifications[] = $notification;
                                } else {
                                    $currentOrg = $orgResult;
                                    while ($currentOrg) {
                                        $currentOrg = Organization::select('division_cd')->where('parent_division_cd', $currentOrg->division_cd)->first();
                                        if ($currentOrg !== null && $currentOrg->division_cd === $userDivisionCd) {
                                            $notifications[] = $notification;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $notifications[] = $notification;
            }
            $count++;

            if(count($notifications) === 5){
                return $notifications;
            }
        }
        if(count($notifications) < 5){
            return $notifications;
        }
    }
}
