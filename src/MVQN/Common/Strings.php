<?php
declare(strict_types=1);

namespace MVQN\Common;
//require __DIR__."/../../vendor/autoload.php";

final class Strings
{




    public static function pascal_to_snake(string $string)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    public static function snake_to_pascal(string $string): string
    {
        return str_replace('_', '', ucwords($string, '_'));
    }

    public static function snake_to_camel(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }


    public static function startsWithUpper(string $word): bool
    {
        return (preg_match('/[A-Z]$/',$word{0}) == true);
    }

    public static function contains(string $haystack, string $needle): bool
    {
        return (strpos($haystack, $needle) !== false);
    }

    public static function splitLast(string $haystack, string $delimiter): string
    {
        $parts = explode("\\", $haystack);
        return array_pop($parts);
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return $length == 0 ? true : (substr($haystack, -$length) === $needle);
    }







}