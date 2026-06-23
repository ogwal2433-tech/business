<?php

if (!function_exists('businessCurrency')) {
    function businessCurrency(): string
    {
        $user = auth()->user();
        if (!$user) return 'UGX';
        if ($user->isEmployee()) {
            return optional($user->admin)->currency ?: 'UGX';
        }
        return $user->currency ?: 'UGX';
    }
}

if (!function_exists('currency_label')) {
    function currency_label(string $key, array $replace = [], ?string $locale = null): string
    {
        return str_replace('UGX', businessCurrency(), __($key, $replace, $locale));
    }
}
