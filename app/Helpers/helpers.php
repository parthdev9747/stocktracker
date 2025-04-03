<?php

use App\Constants\HttpStatusCodes;

if (!function_exists('httpStatusCode')) {
    /**
     * Get HTTP status code constant
     *
     * @param string $code
     * @return int
     */
    function httpStatusCode($code)
    {
        $reflection = new ReflectionClass(HttpStatusCodes::class);
        $constants = $reflection->getConstants();

        return $constants[$code] ?? HttpStatusCodes::SUCCESS;
    }
}

if (!function_exists('getDirection')) {
    function getDirection()
    {
        $rtlLanguages = ['ar', 'he', 'ur', 'fa'];

        return in_array(app()->getLocale(), $rtlLanguages) ? 'ltr' : 'ltr';
    }
}

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        return \App\Models\Setting::first()->value($key) ?? $default;
    }
}

if (!function_exists('darken_color')) {
    /**
     * Darken a hex color by a specified percentage
     *
     * @param string $hex Hex color code
     * @param int $percent Percentage to darken (0-100)
     * @return string Darkened hex color
     */
    function darken_color($hex, $percent = 15)
    {
        $hex = ltrim($hex, '#');

        // Handle 3-digit hex
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // Convert hex to rgb
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Lighten each component
        $r = max(0, min(255, $r + round((255 - $r) * ($percent / 100))));
        $g = max(0, min(255, $g + round((255 - $g) * ($percent / 100))));
        $b = max(0, min(255, $b + round((255 - $b) * ($percent / 100))));

        // Convert back to hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
