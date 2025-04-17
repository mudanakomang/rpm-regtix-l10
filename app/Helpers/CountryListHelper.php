<?php

namespace App\Helpers;

class CountryListHelper
{
    public static function get(string $locale = 'en'): array
    {
        $path = base_path("vendor/umpirsky/country-list/data/{$locale}/country.php");

        return file_exists($path) ? include $path : [];
    }

    public static function getName(string $code, string $locale = 'en'): ?string
    {
        $countries = self::get($locale);
        return $countries[$code] ?? null;
    }
}
