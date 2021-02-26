<?php


namespace App\Helpers;


class TextHelper
{
    /**
     * Convert a camelCase text to SnakCase.
     * Example: camelCase => camel_case
     *
     * @param string $input
     * @return string
     */
    public static function convertCamelCaseToSnakeCase(string $input): string {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);

        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    /**
     * Converts an input value to a text "YES" or "NO
     *
     * @param $value
     * @return string
     */
    public static function yesOrNo($value): string {
        return (bool) $value ? 'SI' : 'NO';
    }
}