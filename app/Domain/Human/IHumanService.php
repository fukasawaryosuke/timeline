<?php

namespace App\Domain\Human;

use Illuminate\Http\Request;

interface IHumanService 
{
    public function getProfileIcon(Request $request);
}