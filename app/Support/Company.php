<?php

namespace App\Support;

class Company
{
    public static function whatsappNumber(): string
    {
        $number = preg_replace('/\D+/', '', (string) (config('company.whatsapp') ?: config('company.phone')));

        if ($number === '') {
            return '';
        }

        if (str_starts_with($number, '0')) {
            return '234'.substr($number, 1);
        }

        return str_starts_with($number, '234') ? $number : '234'.$number;
    }

    public static function whatsappUrl(?string $message = null): string
    {
        $url = 'https://wa.me/'.self::whatsappNumber();

        return $message ? $url.'?text='.rawurlencode($message) : $url;
    }
}
