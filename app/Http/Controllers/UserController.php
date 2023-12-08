<?php

namespace App\Http\Controllers;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use App\Models\Organization;
use App\Models\InHouseSystem;
use App\Models\User;
use App\Models\SystemRole;
use App\Models\Company;
use App\Domain\Human\IHumanService;
use App\Domain\User\UserService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Storage;
use App\Domain\FileStorage\IFileStorageService;
use App\Models\Diary;

class UserController extends Controller
{
    public function show(Request $request)
    {
        // humanIDを取得しユーザー情報を取得する。
        $user = User::with('organization')
            ->select(
                'id',
                'user_name',
                'mailadd',
                'is_active',
                'division_cd',
                'system_role_id',
                'is_initial_password'
            )
            ->where(
                'human_id',
                $request->session()->get('ha_time_session')['human_id']
            )
            ->first();

        return response()->json($user);
    }

    // TODO => ユーザー全件取得する関数を記述（アカウント別CSVで選択可能にする為）
    public function userlist(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $user_all_list = User::select(
            'id',
            'user_name',
        )->where('company_code', $userCompanyCode)->get();

        return response()->json($user_all_list);
    }

    public function list(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $user = User::with('Organization')
            ->select(
                'id',
                'user_name',
                'mailadd',
                'is_active',
                'division_cd',
                'system_role_id',
                'is_initial_user'
            )->where('company_code', $userCompanyCode); // 必要なカラム値のみ取得

        if ($request->input('name')) {
            $serch_name = $request->input('name');
            $user = $user->where(function ($q) use ($serch_name) {
                $q->where(
                    'user_name',
                    'LIKE',
                    '%' . $serch_name . '%'
                )->orWhere('mailadd', 'LIKE', '%' . $serch_name . '%');
            });
        }

        if ($request->input('division')) {
            $division_name = $request->input('division');
            $user = $user->whereHas('Organization', function ($q) use ($division_name) {
                $q->where('organization_name', $division_name);
            });
        }

        if ($request->input('isActive') !== null) {
            $isActive = $request->input('isActive');
            $user = $user->where('is_active', $isActive);
        }

        $paginatedAccounts = $user->paginate(10);
        $convertedData = convertColumnsToCamelCase($paginatedAccounts);

        $orgList = Organization::select([
            'id',
            'organization_name',
            'division_cd',
        ])->where('company_code', $userCompanyCode)->get();

        return $this->successJsonResponse(200, '', [
            'account' => $paginatedAccounts->withQueryString()->setCollection($convertedData),
            'organization' => $orgList,
        ]);
    }

    public function updatePermission(Request $request)
    {
        $user_id = $request->user_id;
        $target = User::with('SystemRole')->find($user_id);
        if (empty($target)) {
            return $this->errorJsonResponse(
                404,
                '該当するユーザーが存在しません。'
            );
        }
        // 2022/12/09時点 1: 一般ユーザー, 2: 管理者ユーザー
        // 1であれば2に変更して2を返却, 2であれば1に変更して1を返却
        $changeRoleId = 0;
        if ($target->SystemRole->IsAdmin()) {
            $changeRoleId = SystemRole::GetGeneralId();
        } else {
            $changeRoleId = SystemRole::GetAdminId();
        }

        if ($changeRoleId === 0) {
            return $this->errorJsonResponse(
                500,
                'ロール権限のDB設定に誤りがあります。お手数ですが管理者までお問い合わせください。'
            );
        }
        $target->system_role_id = $changeRoleId;
        $result = $target->update();
        if (!$result) {
            return $this->errorJsonResponse(
                500,
                '更新処理に失敗しました。再度リトライし、解決しない場合はお手数ですが管理者までお問い合わせください。'
            );
        }

        return $this->successJsonResponse(200, '', $changeRoleId);
    }

    public function getEmployeeProfile(Request $request)
    {
        // humanIDを取得しユーザー情報を取得する。
        // $client = new \GuzzleHttp\Client([
        //     'headers' => ['Content-Type' => 'application/json', 'x-apigw-api-id' => '2zjvw8lhoi'],
        // ]);
        // $res = $client->get(
        //     'https://vpce-054d41069cac19ced-jn5ydm5o.execute-api.ap-northeast-1.vpce.amazonaws.com/stg/api/integration/divisions'
        // );
        // return $res;

        // $client = new \GuzzleHttp\Client([
        //     'headers' => ['Content-Type' => 'application/json', 'x-apigw-api-id' => '2zjvw8lhoi'],
        // ]);
        // $res = $client->get(
        //     'https://vpce-054d41069cac19ced-jn5ydm5o.execute-api.ap-northeast-1.vpce.amazonaws.com/stg/api/core/get-teacher-employee-profile?teacher_employee_id=2000111',
        // );

        // $res = $client->request('GET', 'https://vpce-054d41069cac19ced-jn5ydm5o.execute-api.ap-northeast-1.vpce.amazonaws.com/stg/api/core/get-teacher-employee-profile', [
        //     'teacher_employee_id' => 2000111
        // ]);

        // return $res;

        $humanObject = request()->getHumanObject();

        $AuthInfo = $this->authPfGetRequest(
            config('app.ha_auth_pf_url') .
                '/member_info/' .
                $humanObject->human_id,
            [],
            $humanObject->token
        );

        $teacher_employee_id =
            $AuthInfo[0]['member_info']['teacher_employee_id'];

        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'x-apigw-api-id' => config('app.db_api_key'),
            ],
        ]);
        $res = $client->get(
            config('app.db_api_base') .
                '/api/core/get-teacher-employee-profile?teacher_employee_id=' .
                $teacher_employee_id
        );

        return $res;
    }

    public function mentionUser(Request $request)
    {
        $userQuery = User::with('Organization');
        if ($request->has('search_name')) {
            $userQuery->where(
                'user_name',
                'LIKE',
                '%' . $request->get('search_name') . '%'
            );
        }
        if ($request->has('division_cd')) {
            $userQuery->where('division_cd', '=', $request->get('division_cd'));
        }
        $users = $userQuery
            ->where('company_code', UserService::getUserCompanyCode($request))
            ->orderBy('user_name', 'asc')
            ->limit(20)
            ->get();

        return response()->json($users);
    }

    public function divisions(Request $request)
    {
        // humanIDを取得しユーザー情報を取得する。
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'x-apigw-api-id' => config('app.db_api_key'),
            ],
        ]);
        $res = $client->get(
            config('app.db_api_base') . '/api/integration/divisions'
        );

        $res = json_decode($res->getBody(), true);

        // return $res['success'];

        foreach ($res['data'] as $div) {
            Organization::create([
                'division_cd' => $div['division_cd'],
                'organization_name' => $div['division_name'],
            ]);
            InHouseSystem::create([
                'division_cd' => $div['division_cd'],
                'system_name' => $div['division_name'],
            ]);
        }

        return $res;
    }

    public function getProfileIcon(Request $request, IHumanService $service, IFileStorageService $fileStorageService)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);
        $isHumanAcademy = Company::isHumanAcademy($userCompanyCode);

        if (!$isHumanAcademy) {
            //法人のコーポレートアイコンを表示
            $company = Company::where('company_code', $userCompanyCode)->first();
            $iconPath = $company->company_icon_path;

            //開発環境の場合はpublic_pathを付与
            if (app()->isLocal()) {
                $fullIconPath = public_path($iconPath);
                return response()->file($fullIconPath);
            } else {
                return $fileStorageService->getFile($iconPath);
            }
        }

        // プロフィール画像URLを取得
        return $service->getProfileIcon($request);
    }

    private function authPfGetRequest($url, $params, $token)
    {
        if (!empty($params)) {
            $url = $url . '?' . http_build_query($params);
        }

        $res = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get($url);

        if ($res->ok()) {
            return [$res->json(), $res->status()];
        }
        // httpステータスが成功していない
        return [false, $res->status()];
    }

    public function getUserName(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);
        $user = new User();

        $users = $user->select('id', 'user_name')->where('company_code', $userCompanyCode)->get();
        return $users;
    }

    public function detail(Request $request)
    {
        $userId = $request->user_id;
        $user = User::select(
            'id',
            'user_name',
            'mailadd',
            'division_cd',
            'is_active',
            'system_role_id',
            'is_initial_user'
        )->find($userId);

        $convertedData = convertKeysToCamelCase($user->toArray());

        if (!$user) {
            return $this->errorJsonResponse(404, '該当するアカウントが存在しません。', null);
        }

        return $convertedData;
    }

    public function create(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $userCompanyCode = UserService::getUserCompanyCode($request);
            $formData = $request->all();

            User::createUser($userCompanyCode, $formData);

            DB::commit();

            return $this->successJsonResponse(201, 'アカウントを作成しました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, 'アカウントの作成に失敗しました。');
        }
    }

    public function update(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $formData = $request->all();

            $user = User::findOrFail($formData['userId']);
            $user->updateUser($formData);

            DB::commit();

            return $this->successJsonResponse(201, 'アカウントを更新しました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, 'アカウントの更新に失敗しました。');
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            DB::beginTransaction();

            $formData = $request->all();

            $user = User::findOrFail($formData['userId']);
            $user->updatePassword($formData);

            DB::commit();

            return $this->successJsonResponse(201, 'パスワードを更新しました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, 'パスワードの更新に失敗しました。');
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            DB::beginTransaction();

            $userId = $request->user_id;

            User::resetPassword($userId);

            DB::commit();

            return $this->successJsonResponse(201, 'パスワードをリセットしました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, 'パスワードのリセットに失敗しました。');
        }
    }

    public function isHumanAcademy(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);
        $isHumanAcademy = Company::isHumanAcademy($userCompanyCode);

        return response()->json($isHumanAcademy);
    }
}
