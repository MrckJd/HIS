<?php

namespace App\Filament\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PSGCService
{
    public static function getMunicipalities(string $provinceCode = '112400000'): array
    {
        return Cache::remember("municipalities_{$provinceCode}", now()->addDay(), function() use ($provinceCode) {
            $response = Http::get("https://psgc.gitlab.io/api/provinces/{$provinceCode}/municipalities/");

            if ($response->successful()) {
                return collect($response->json())
                    ->pluck('name', 'code')
                    ->toArray();
            }

            return [];
        });
    }

    public static function getBarangays(string $municipalityCode): array
    {
        return Cache::remember("barangays_{$municipalityCode}", now()->addDay(), function() use ($municipalityCode) {
            $response = Http::get("https://psgc.gitlab.io/api/municipalities/{$municipalityCode}/barangays/");

            if ($response->successful()) {
                return collect($response->json())
                    ->pluck('name', 'code')
                    ->toArray();
            }

            return [];
        });
    }

    public static function getMunicipalityData(string $municipalityCode, string $provinceCode = '112400000'): ?array
    {
        $municipalities = Cache::remember("municipalities_data_{$provinceCode}", now()->addDay(), function() use ($provinceCode) {
            $response = Http::get("https://psgc.gitlab.io/api/provinces/{$provinceCode}/municipalities/");

            if ($response->successful()) {
                return collect($response->json())->keyBy('code')->toArray();
            }

            return [];
        });

        return $municipalities[$municipalityCode] ?? null;
    }

    public static function getBarangayData(string $barangayCode, string $municipalityCode): ?array
    {
        $barangays = Cache::remember("barangays_data_{$municipalityCode}", now()->addDay(), function() use ($municipalityCode) {
            $response = Http::get("https://psgc.gitlab.io/api/municipalities/{$municipalityCode}/barangays/");

            if ($response->successful()) {
                return collect($response->json())->keyBy('code')->toArray();
            }

            return [];
        });

        return $barangays[$barangayCode] ?? null;
    }

    public static function getMunicipalityName(string $municipalityCode, string $provinceCode = '112400000'): string
    {
        $municipalityData = self::getMunicipalityData($municipalityCode, $provinceCode);
        return $municipalityData['name'] ?? $municipalityCode;
    }

    public static function getBarangayName(string $barangayCode, string $municipalityCode): string
    {
        $barangayData = self::getBarangayData($barangayCode, $municipalityCode);
        return $barangayData['name'] ?? $barangayCode;
    }

    public static function clearCache(): void
    {
        Cache::forget('municipalities_112400000');
        Cache::forget('municipalities_data_112400000');
    }

    public static function clearMunicipalityCache(string $municipalityCode): void
    {
        Cache::forget("barangays_{$municipalityCode}");
        Cache::forget("barangays_data_{$municipalityCode}");
    }

    //get municipality code
    public static function getMunicipalityCode(string $municipalityName, string $provinceCode = '112400000'): ?string
    {
        $municipalities = self::getMunicipalities($provinceCode);
        return array_search($municipalityName, $municipalities) ?: null;
    }
}
