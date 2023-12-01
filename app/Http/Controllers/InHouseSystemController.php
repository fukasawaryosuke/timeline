<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\InHouseSystem;
use App\Models\ReportForm;
use App\Models\User;
use App\Domain\FileStorage\IFileStorageService;
use App\Models\NotificationSetting;
use Illuminate\Notifications\Notification;
use App\Domain\User\UserService;

class InHouseSystemController extends Controller
{
    public function show(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $query = InHouseSystem::query();
        $query = $query->select(
            'id',
            'system_name',
            'created_at',
            'updated_at',
            'sequence'
        )->where('company_code', $userCompanyCode);

        if ($request->input('name')) {
            $query = $query->where(
                'system_name',
                'LIKE',
                '%' . $request->input('name') . '%'
            );
        }
        return $query
            ->orderBy('sequence', 'asc')
            ->paginate(10)
            ->withQueryString();
    }
    public function all(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        return $this->successJsonResponse(
            200,
            '',
            InHouseSystem::orderBy('sequence', 'asc')->where('company_code', $userCompanyCode)->get()
        );
    }
    public function sort(Request $request)
    {
        $ihsList = $request->input('ihsList');
        foreach ($ihsList as $ihs) {
            $target = InHouseSystem::find($ihs['id']);
            if (is_null($target)) {
                return $this->errorJsonResponse(
                    404,
                    'No.' . $ihs['id'] . 'の制度が存在しません。'
                );
            }
            $target->sequence = $ihs['sequence'];
            $target->save();
        }
        return $this->successJsonResponse(200, '');
    }
    public function detail(Request $request)
    {
        $ihs_id = $request->ihs_id;
        $target = InHouseSystem::with('notification_setting.organization.upperOrganisation')
            ->find($ihs_id);
        if (!$target) {
            return $this->errorJsonResponse(
                404,
                '該当する制度が存在しません。',
                null
            );
        }

        return $this->successJsonResponse(200, '', $target);
    }
    public function selectList(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $ihsList = InHouseSystem::with('organization')
            ->select(['id', 'system_name', 'description'])
            ->where('release_status', true)
            ->where('company_code', $userCompanyCode)
            ->orderBy('sequence', 'asc')
            ->get();
        return response()->json($ihsList);
    }
    public function create(Request $request, IFileStorageService $service)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        // S3アップロード処理
        $file = request()->file('icon_file');
        $contents = $request->input();
        $contents['organization_div'] = json_decode(
            $contents['organization_div']
        );
        $icon_path =
            $contents['icon_path'] == 'null' ? null : $contents['icon_path'];
        if (!empty($file)) {
            $icon_path = $service->uploadFile($file);
        }
        $inHouseSystem = InHouseSystem::create([
            'system_name' => $contents['system_name'],
            'icon_path' => $icon_path,
            'company_code' => $userCompanyCode,
            'release_status' => intval($contents['release_status']),
            'report_form_id' => intval($contents['report_form_id']),
            'description' => $contents['description'],
            'sequence' => InHouseSystem::max('sequence') + 1,
        ]);

        if (!empty($contents['organization_div']) && is_array($contents['organization_div'])) {
            foreach ($contents['organization_div'] as $organization) {
                if (!property_exists($organization, 'parents_flg') || !$organization->parents_flg) {
                    $result = NotificationSetting::create([
                        'in_house_system_id' => $inHouseSystem->id,
                        'division_cd' => $organization->division_cd,
                    ]);
                }
            }
        } else {
            $result = true;
        }
        if ($result) {
            return $this->successJsonResponse(201, '制度を作成しました。');
        } else {
            return $this->errorJsonResponse(500, '制度の作成に失敗しました。');
        }
    }

    public function update(Request $request, IFileStorageService $service)
    {
        $ihs_id = $request->ihs_id;
        $target = InHouseSystem::where('id', $ihs_id)->first();
        if (is_null($target)) {
            return $this->errorJsonResponse(404, '該当の制度が存在しません。');
        }
        $curretn_icon_path = $target->icon_path;
        // S3アップロード処理
        $file = request()->file('icon_file');
        $contents = $request->input();
        $contents['organization_div'] = json_decode(
            $contents['organization_div']
        );
        $icon_path =
            $contents['icon_path'] == 'null' ? null : $contents['icon_path'];
        if (!empty($file)) {
            $icon_path = $service->uploadFile($file);
        }
        $target->system_name = $contents['system_name'];
        $target->icon_path = $icon_path;
        $target->release_status = $contents['release_status'];
        $target->description = $contents['description'];
        $target->report_form_id = $contents['report_form_id'];
        $result = $target->update();

        NotificationSetting::where('in_house_system_id', $ihs_id)->forceDelete();
        if (!empty($contents['organization_div']) && is_array($contents['organization_div'])) {
            foreach ($contents['organization_div'] as $organization) {
                if (!property_exists($organization, 'parents_flg') || !$organization->parents_flg) {
                    $result = NotificationSetting::create([
                        'in_house_system_id' => $ihs_id,
                        'division_cd' => $organization->division_cd,
                    ]);
                }
            }
        }
        if ($result) {
            // 元の画像が変更、もしくは削除されていた場合はファイルストレージのファイルを削除する
            // 画像が選択されており、元のアイコンが登録済み
            if ($icon_path && $service->hasFile($curretn_icon_path)) {
                $service->deleteFile($curretn_icon_path);
                // 元の画像はあるが、リクエストでは空に設定されている
            } elseif (
                is_null($icon_path) &&
                $service->hasFile($curretn_icon_path)
            ) {
                $service->deleteFile($curretn_icon_path);
            }
            return $this->successJsonResponse(200, '制度を更新しました。');
        } else {
            return $this->errorJsonResponse(500, '制度の更新に失敗しました。');
        }
    }

    public function delete(Request $request, IFileStorageService $service)
    {
        $ihs_id = $request->ihs_id;
        $target = InHouseSystem::where('id', $ihs_id)->first();
        $curretn_icon_path = $target->icon_path;
        if (is_null($target)) {
            return $this->errorJsonResponse(404, '該当の制度が存在しません。');
        }
        $result = $target->delete();
        if ($result) {
            if ($service->hasFile($curretn_icon_path)) {
                $service->deleteFile($curretn_icon_path);
            }
            return $this->successJsonResponse(200, '制度を削除しました。');
        } else {
            return $this->errorJsonResponse(500, '制度の削除に失敗しました。');
        }
    }
}
