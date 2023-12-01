<?php

use Illuminate\Support\Str;

/**
 * キャメルケース変換
 * change camel or snake
 * $method string
 * camel or snake
 **/

if (!function_exists('convertColumnsToCamelCase')) {

  function convertColumnsToCamelCase($data): object
  {
    return $data->map(function ($item) {
      return collect($item)->mapWithKeys(function ($value, $key) {
        return [Str::camel($key) => $value];
      });
    });
  }
}

if (!function_exists('convertKeysToCamelCase')) {

  function convertKeysToCamelCase($array): object
  {
    return collect($array)->mapWithKeys(function ($value, $key) {
      return [Str::camel($key) => $value];
    });
  }
}
