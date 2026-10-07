<?php

namespace App\Support;

class ContentModeration
{
    private const HARSH_WORDS = [
        // Filipino/Tagalog
        'bobo', 'tanga', 'gago', 'puta', 'putangina', 'peste', 'piste', 'ukinam', 'bwisit', 'bwisit',
        'lintik', 'hayop', 'tarantado', 'leche', 'ulol', 'inutil', 'siraulo', 'hudas', 'yawa', 'amaw',
        'buang', 'kolera', 'hampaslupa', 'walang kwenta', 'patayin', 'mamatay',
        'atay', 'kablaaw', 'pukaw', 'ukininam', 'kayat', 'agpayso', 'pisti', 'bogo', 'gahi', 'animal',
        // Ilocano
        'bassit', 'basit', 'talbog', 'bukod', 'dakes', 'awang', 'balaklaw', 'kamengking', 'kaldit', 'ulitaoan',
        'walat-ayat', 'walat ayat', 'agkaskaso', 'aggapu', 'annak ti biag', 'pukpukaw', 'mayaw', 'asiento',
        'kamumurang', 'gulgulang', 'dakilon', 'narugis', 'malaw-law', 'sipat', 'usisa', 'pagastoso',
        // English
        'stupid', 'idiot', 'dumb', 'moron', 'hate', 'kill', 'trash', 'useless', 'disgusting', 'sh*t', 'shit', 'fuck', 'f*ck',
    ];

    public static function normalize(string $text): string
    {
        $normalized = strtolower($text);
        $normalized = strtr($normalized, ['@' => 'a', '0' => 'o', '1' => 'i', '$' => 's', '3' => 'e', '4' => 'a', '5' => 's', '6' => 'g', '7' => 't', '8' => 'b', '9' => 'g']);
        $normalized = preg_replace('/[^\pL\pN]+/u', ' ', $normalized) ?? $normalized;

        return trim(preg_replace('/\s+/u', ' ', $normalized) ?? $normalized);
    }

    public static function containsHarshLanguage(string $text): bool
    {
        $normalized = self::normalize($text);
        $compactNormalized = preg_replace('/(.)\1+/u', '$1', str_replace(' ', '', $normalized)) ?? $normalized;

        foreach (self::HARSH_WORDS as $word) {
            $normalizedWord = self::normalize($word);
            $compactWord = preg_replace('/(.)\1+/u', '$1', str_replace(' ', '', $normalizedWord)) ?? $normalizedWord;

            if (preg_match('/\b' . preg_quote($normalizedWord, '/') . '\b/u', $normalized) === 1
                || ($compactWord !== '' && str_contains($compactNormalized, $compactWord))) {
                return true;
            }
        }

        return false;
    }
}
