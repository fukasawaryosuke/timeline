<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InHouseSystem;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use DateTimeInterface;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_name',
        'level',
        'company_code',
        'display_order',
        'division_cd',
        'parent_division_cd',
        'is_last_level',
        'in_house_system_id',
        'is_active',
        'synced_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function InHouseSystem()
    {
        return $this->belongsTo(InHouseSystem::class);
    }

    public function rightUnderOrganisations()
    {
        return $this->hasMany(Organization::class, 'parent_division_cd', 'division_cd')
            ->orderBy('display_order', 'asc');
    }

    public function underOrganisations()
    {
        return $this->rightUnderOrganisations()->with('underOrganisations');
    }

    public function rightUpperOrganisation()
    {
        return $this->hasOne(Organization::class, 'division_cd', 'parent_division_cd')
            ->orderBy('display_order', 'asc');
    }

    public function upperOrganisation()
    {
        return $this->rightUpperOrganisation()->with('upperOrganisation');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y/m/d');
    }

    public function getDivisionCodesWithUnderOrganizations($divisionCodes)
    {
        $result = [];
        $orgzList = $this->whereIn('division_cd', $divisionCodes)
            ->with('underOrganisations')
            ->get();
        function getUnderOrganizations($orgz, &$result)
        {
            if ($orgz->underOrganisations) {
                foreach ($orgz->underOrganisations as $underOrgz) {
                    getUnderOrganizations($underOrgz, $result);
                }
            }
            $result[] = $orgz->division_cd;
        }
        foreach ($orgzList as $orgz) {
            getUnderOrganizations($orgz, $result);
        }
        return $result;
    }

    public static function createOrganization($userCompanyCode, $level, $formData)
    {
        $divisionCd = self::generateDivisionCd();

        return self::create([
            'organization_name' => $formData['organizationName'],
            'company_code' => $userCompanyCode,
            'division_cd' => $divisionCd,
            'parent_division_cd' => $formData['parentDivisionCd'],
            'level' => $level,
            'is_last_level' => 1,
            'is_active' => $formData['isActive'],
        ]);
    }

    public function updateOrganization($formData)
    {
        return $this->update([
                'organization_name' => $formData['organizationName'],
                'parent_division_cd' => $formData['parentDivisionCd'],
            ]);
    }

    public function updateIsLastLevel($isLastLevel)
    {
        return self::update([
            'is_last_level' => $isLastLevel,
        ]);
    }

    public function updateLevelRecursively($level)
    {
        $this->update(['level' => $level]);

        foreach ($this->underOrganisations as $underOrganization) {
            $underOrganization->updateLevelRecursively($level + 1);
        }
    }

    public function updateIsActiveRecursively($isActive)
    {
        $this->update(['is_active' => $isActive]);

        foreach ($this->underOrganisations as $underOrganization) {
            $underOrganization->updateIsActiveRecursively($isActive);
        }
    }

    private static function generateDivisionCd()
    {
        do {
            $divisionCd = mt_rand(100000, 999999);
            $organization = self::where('division_cd', $divisionCd)->first();
        } while ($organization);

        return $divisionCd;
    }
}
