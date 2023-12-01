<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'company_code',
        'company_icon_path',
        'use_auth_platform',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y/m/d');
    }

    public static function isHumanAcademy($companyCode)
    {
        return $companyCode == config('app.human_academy_company_code');
    }

    public static function createCompany($formData)
    {
        $companyCode = self::generateCompanyCode();

        self::create([
            'company_name' => $formData['companyName'],
            'company_code' => $companyCode,
            'is_active' => $formData['isActive'],
            'company_icon_path' => $formData['companyIconPath'] ?? "",
        ]);

        return $companyCode;
    }

    public function updateCompany($formData)
    {
        return $this->update([
            'company_name' => $formData['companyName'],
            'is_active' => $formData['isActive'],
            'company_icon_path' => $formData['companyIconPath'] ?? "",
        ]);
    }

    private static function generateCompanyCode()
    {
        do {
            $companyCode = mt_rand(1000, 9999);
            $company = self::where('company_code', $companyCode)->first();
        } while ($company || $companyCode == config('app.human_academy_company_code'));

        return $companyCode;
    }
}
