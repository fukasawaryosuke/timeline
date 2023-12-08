<?php

namespace App\Domain\User;

use App\Models\User;
use Illuminate\Http\Request;

class UserService
{
  private static function getSessionUser(Request $request)
  {
    return $request->session()->get('ha_time_session');
  }

  public static function getUserCompanyCode(Request $request)
  {
    $sessionUser = self::getSessionUser($request);

    if ($sessionUser) {
      $user =  User::select('company_code')
        ->where('human_id', $sessionUser['human_id'])
        ->first();
      if ($user) {
        return $user->company_code;
      } else {
        return null;
      }
    }

    return null;
  }

  public static function getUserDivisionCd(Request $request)
  {
    $sessionUser = self::getSessionUser($request);

    if ($sessionUser) {
      $user =  User::select('division_cd')
        ->where('human_id', $sessionUser['human_id'])
        ->first();

      return $user->division_cd;
    }

    return null;
  }

  public static function getUserParentDivisionCd(Request $request)
  {
    $sessionUser = self::getSessionUser($request);

    if ($sessionUser) {
      $user =  User::with('organization')
        ->where('human_id', $sessionUser['human_id'])
        ->first();

      return $user->organization->parent_division_cd;
    }

    return null;
  }
}
