<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Domain\User\UserService;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function list(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $organizations = Organization::with('underOrganisations')
            ->select('id', 'organization_name', 'company_code', 'division_cd', 'parent_division_cd', 'is_active', 'created_at', 'updated_at')
            ->where('company_code', $userCompanyCode);

        $searchWord = $request->input('name');

        if ($searchWord) {
            $organizations = $organizations->where(
                'organization_name',
                'LIKE',
                '%' . $searchWord . '%'
            );
        }

        if ($request->input('isActive') !== null) {
            $isActive = $request->input('isActive');
            $organizations = $organizations->where('is_active', $isActive);
        }

        $paginatedOrganizations = $organizations->paginate(10);
        $convertedData = convertColumnsToCamelCase($paginatedOrganizations);

        return $paginatedOrganizations->withQueryString()->setCollection($convertedData);
    }

    public function detail(Request $request)
    {
        $organizationId = $request->organization_id;
        $organization = Organization::find($organizationId);
        $convertedData = convertKeysToCamelCase($organization->toArray());

        if (!$organization) {
            return $this->errorJsonResponse(404, '該当する法人が存在しません。', null);
        }

        return $convertedData;
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $userCompanyCode = UserService::getUserCompanyCode($request);
            $formData = $request->all();
            $parentDivisionCd = $formData['parentDivisionCd'] ?? null;
            $level = 1;

            //level設定
            if ($parentDivisionCd) {
                // 親のisLastLevelを更新
                $parentOrganization = Organization::where('division_cd', $parentDivisionCd)->where('company_code', $userCompanyCode)->first();
                $parentOrganization->updateIsLastLevel(0);

                $level = $parentOrganization->level + 1;
            }

            //部門作成
            Organization::createOrganization($userCompanyCode, $level, $formData);

            DB::commit();

            return $this->successJsonResponse(201, '部門を作成しました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, '部門の作成に失敗しました。');
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $userCompanyCode = UserService::getUserCompanyCode($request);
            $formData = $request->all();
            $organizationId = $formData['organizationId'];
            $parentDivisionCd = $formData['parentDivisionCd'] ?? null;
            $isActive = $formData['isActive'];
            $level = 1;

            $organization = Organization::with("upperOrganisation")->find($organizationId);
            $originalParentDivisionCd = $organization->parent_division_cd ?? null;
            $originalIsActive = $organization->is_active;

            $isCheangedParent = $originalParentDivisionCd != $parentDivisionCd;
            $isChangedIsActive = $originalIsActive != $isActive;

            //level設定
            if ($isCheangedParent) {
                // 新しい親のisLastLevelを更新
                if ($parentDivisionCd !== null) {
                    $parentOrganization = Organization::where('division_cd', $parentDivisionCd)->where('company_code', $userCompanyCode)->first();
                    $parentOrganization->updateIsLastLevel(0);

                    // 新しい親のlevel+1に設定
                    $level = $parentOrganization->level + 1;
                }

                // 元の親のisLastLevelを更新
                if ($originalParentDivisionCd !== null) {
                    $organization->upperOrganisation->updateIsLastLevel(1);
                }
            }

            //部門更新
            $isCheangedParent ? $organization->updateLevelRecursively($level) : null;
            $isChangedIsActive ? $organization->updateIsActiveRecursively($isActive) : null;
            $organization->updateOrganization($formData);

            DB::commit();

            return $this->successJsonResponse(201, '部門を更新しました。');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorJsonResponse(500, '部門の更新に失敗しました。');
        }
    }

    public function show(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);
        $isHumanAcademy = Company::isHumanAcademy($userCompanyCode);

        if ($isHumanAcademy) {
            // NOTE:19909以降の部門を取得
            $haMainOfficeDivisionCd = 19909;

            $organizations = Organization::with('underOrganisations')
                ->where('parent_division_cd', $haMainOfficeDivisionCd)
                ->where('company_code', $userCompanyCode)
                ->get();
        } else {
            //NOTE:第一階層の部門を取得
            $organizations = Organization::with('underOrganisations')
                ->where('level', 1)
                ->where('company_code', $userCompanyCode)
                ->get();
        }

        // 事業部マスターを取得
        return response()->json($organizations);
    }

    public function getDiv(Request $request)
    {
        $divCd = $request->division_cd;
        $select_divCd = $request->select_division_cd;
        $div = Organization::with('upperOrganisation')->select('organization_name', 'division_cd', 'parent_division_cd')->where('division_cd', $divCd)->first()->toArray();
        $org = $div;
        $decode_select_divCd = [];
        if ($select_divCd) {


            foreach ($select_divCd as $select_div) {
                $select_div = json_decode($select_div, true);
                $decode_select_divCd[] = $select_div;
            }
            //親階層がすでに選択されているかのチェック処理
            while ($org) {
                foreach ($decode_select_divCd as &$select_div) { // &$select_divで参照として扱う
                    if ($select_div['division_cd'] == $org['parent_division_cd']) {
                        $select_div['parents_flg'] = true;
                    }
                }
                $org = Organization::select('organization_name', 'division_cd', 'parent_division_cd')->where('division_cd', $org['parent_division_cd'])->first();
                if ($org) {
                    $org = $org->toArray();
                }
            }

            $test = [];
            //子階層がすでに選択されているかのチェック処理
            foreach ($decode_select_divCd as &$select_div) { // &$select_divで参照として扱う
                if ($select_div['division_cd'] == $divCd) {
                    $div['parents_flg'] = true;
                } else {
                    $nest_org = Organization::select('parent_division_cd')->where('division_cd', $select_div['division_cd'])->first();
                    if ($nest_org) {
                        $nest_org = $nest_org->toArray();
                        $test[] = $nest_org;
                        while ($nest_org) {
                            if ($divCd == $nest_org['parent_division_cd']) {
                                $div['parents_flg'] = true;
                            }
                            $nest_org = Organization::select('parent_division_cd')->where('division_cd', $nest_org['parent_division_cd'])->first();
                            if ($nest_org) {
                                $nest_org = $nest_org->toArray();
                            }
                        }
                    }
                }
            }
        }
        $decode_select_divCd[] = $div;
        return response()->json($decode_select_divCd);
    }

    public function mappingName(Request $request)
    {
        $divCd = $request->division_cd;
        $divName = Organization::select('organization_name')->where('division_cd', $divCd)->first()->toArray();
        return response()->json($divName);
    }
}
