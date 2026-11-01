<?php

namespace App\Support;

class SearchTerm
{
    /**
     * Normalise a user supplied search term for use in LIKE queries.
     */
    public static function clean(?string $value): string
    {
        return trim(str_replace(['%', '_'], '', (string) $value));
    }
}
