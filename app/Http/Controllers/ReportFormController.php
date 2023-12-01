<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReportForm;
use App\Models\InHouseSystem;
use App\Models\User;
use Exception;
use Throwable;
use App\Domain\User\UserService;

class ReportFormController extends Controller
{
    public function show(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $query = ReportForm::query();
        $query = $query->select('id', 'name', 'created_at', 'updated_at')->where('company_code', $userCompanyCode); // 必要なカラム値のみ取得
        if ($request->input('name')) {
            $query = $query->where(
                'name',
                'LIKE',
                '%' . $request->input('name') . '%'
            );
        }
        return $query->paginate(10)->withQueryString();
    }
    public function detail(Request $request)
    {
        $format_id = $request->format_id;
        $target = ReportForm::find($format_id);
        if (!$target) {
            return $this->errorJsonResponse(
                404,
                '該当するフォームが存在しません。',
                null
            );
        }
        $first_report_form_name = ReportForm::first()->name;
        return $this->successJsonResponse(200, '', [
            'form' => $target,
            'first_report_form' => $first_report_form_name,
        ]);
    }
    public function get_form_list(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $form_list = ReportForm::select([
            'id',
            'name',
            'diary',
            'required',
        ])->where('company_code', $userCompanyCode)->get(); // 必要なカラム値のみ取得
        return $this->successJsonResponse(200, '', $form_list);
    }
    public function get_form(Request $request)
    {
        $humanId = request()->getHumanObject()->human_id;
        $user_id = User::where('human_id', $humanId)->first()->id;
        $bool = Draft::where([
            ['user_id', $user_id],
            ['in_house_systems_id', $request->ihs_id],
        ])->exists();

        // 下書きが存在する場合は下書きを取得
        if ($bool) {
            $draft =  Draft::select('draft')
                ->where([
                    ['user_id', $user_id],
                    ['in_house_systems_id', $request->ihs_id],
                ])->first();

            $target = InHouseSystem::with('report_form')
                ->where('id', $request->ihs_id)
                ->first(); // 必要なカラム値のみ取得

            $target->report_form->diary = $draft->draft;
        } else {
            $target = InHouseSystem::with('report_form')
                ->where('id', $request->ihs_id)
                ->first(); // 必要なカラム値のみ取得
        }

        return $this->successJsonResponse(200, '', $target);
    }
    public function create(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        if ($user == null) {
            return $this->successJsonResponse(
                404,
                'ユーザー情報が存在しません。'
            );
        }
        $contents = $request->input();
        $result = ReportForm::create([
            'name' => $contents['name'],
            'required' => $contents['required'],
            'diary' => $contents['diary'],
            'company_code' => $userCompanyCode,
            'create_by' => $user->id,
            // 'updated_by' => $user->id,
        ]);
        if ($result) {
            return $this->successJsonResponse(
                201,
                '報告フォームを作成しました。'
            );
        } else {
            return $this->errorJsonResponse(
                500,
                '報告フォームの作成に失敗しました。'
            );
        }
    }
    public function update(Request $request)
    {
        $format_id = $request->format_id;
        $target = ReportForm::where('id', $format_id)->first();
        if ($target == null) {
            return $this->errorJsonResponse(
                404,
                '該当の報告フォームが存在しません。'
            );
        }

        $contents = $request->input();
        $target->name = $contents['name'];
        $target->required = $contents['required'];
        $target->diary = $contents['diary'];
        $result = $target->update();
        if ($result) {
            return $this->successJsonResponse(
                200,
                '報告フォームを更新しました。'
            );
        } else {
            return $this->errorJsonResponse(
                500,
                '報告フォームの更新に失敗しました。'
            );
        }
    }
    public function delete(Request $request)
    {
        $format_id = $request->format_id;
        $target = ReportForm::with('in_house_system')
            ->where('id', $format_id)
            ->first();
        if ($target == null) {
            return $this->errorJsonResponse(
                404,
                '該当の報告フォームが存在しません。'
            );
        }
        DB::beginTransaction();
        try {
            $result = $target->delete();
            if ($result) {
                // 関連制度のフォームをデフォルトのフォームに変更
                $target->in_house_system->each(function ($row) {
                    $row->report_form_id = ReportForm::first()->id;
                    $row->save();
                });
                DB::commit();
                return $this->successJsonResponse(
                    200,
                    '報告フォームを削除しました。'
                );
            } else {
                throw new Exception('報告フォームの削除に失敗しました。');
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return $this->errorJsonResponse(500, $th->getMessage());
        }
    }
}
