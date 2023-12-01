<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Models\User;
use Illuminate\Http\Request;

class DraftsController extends Controller
{
    public function create(Request $request)
    {
        $contents = $request->input('diaryTemplate');
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first()->id;
        $ihsId = $request->ihs_id;
        $bool = Draft::where([
            ['user_id', $user],
        ])->exists();
        try{
            if($bool){
                Draft::where([
                    ['user_id', $user],
                ])->update([
                    'draft' => $contents,
                    'in_house_systems_id' => $ihsId,
                ]);
                return $this->successJsonResponse(200, '', '更新しました。');
            }else{
                Draft::create([
                    'user_id' => $user,
                    'in_house_systems_id' => $ihsId,
                    'draft' => $contents,
                ]);
                return $this->successJsonResponse(200, '', '保存しました。');
            }
        }catch(\Exception $e){
            return $this->successJsonResponse(500, '', $e->getMessage());
        }

    }
}
