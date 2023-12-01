<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Domain\FileStorage\IFileStorageService;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Requests\UserRequest;

class CompanyController extends Controller
{
    public function list(Request $request)
    {
        $companies = Company::select('id', 'company_name', 'company_code', 'company_icon_path', 'is_active', 'created_at', 'updated_at');

        $searchWord = $request->input('name');

        if ($searchWord) {
            $companies = $companies->where(
                'company_name',
                'LIKE',
                '%' . $searchWord . '%'
            );
        }

        if ($request->input('isActive') !== null) {
            $isActive = $request->input('isActive');
            $companies = $companies->where('is_active', $isActive);
        }

        $paginatedCompanies = $companies->paginate(10);
        $convertedData = convertColumnsToCamelCase($paginatedCompanies);

        return $paginatedCompanies->withQueryString()->setCollection($convertedData);
    }

    public function detail(Request $request)
    {
        $companyId = $request->company_id;
        $company = Company::find($companyId);
        $convertedData = convertKeysToCamelCase($company->toArray());

        if (!$company) {
            return $this->errorJsonResponse(404, '該当する法人が存在しません。', null);
        }

        return $convertedData;
    }

    public function create(UserRequest $request, IFileStorageService $service)
    {
        try {
            DB::beginTransaction();

            $formData = $request->all();

            // S3アップロード処理
            if (!empty($formData['iconFile'])) {
                $formData['companyIconPath'] = $service->uploadFile($formData['iconFile']);
            }

            //法人作成
            $companyCode = Company::createCompany($formData);

            // 初期ユーザー作成
            User::createInitialUser($companyCode, $formData);

            DB::commit();

            return $this->successJsonResponse(201, '法人を作成しました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, '法人の作成に失敗しました。');
        }
    }

    public function update(Request $request, IFileStorageService $service)
    {
        try {
            DB::beginTransaction();

            $formData = $request->all();
            $company = Company::findOrFail($formData['companyId']);
            $originalIconPath = $company->company_icon_path;

            // 既存のアイコンファイルを削除
            if ($service->hasFile($originalIconPath)) {
                $service->deleteFile($originalIconPath);
            }

            // S3アップロード処理
            if (!empty($formData['iconFile'])) {
                $formData['companyIconPath'] = $service->uploadFile($formData['iconFile']);
            }

            //法人更新
            $company->updateCompany($formData);

            DB::commit();

            return $this->successJsonResponse(201, '法人を更新しました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, '法人の更新に失敗しました。');
        }
    }
}
