<?php namespace Moregold\Infrastructure\Helpers;

trait DataCleaningTrait
{
    protected static function makeUrlSlug($string = '', $length_limit = 50, $filter_words = [])
    {
        $string = trim($string, "-");
        $filtered_string = implode('-', array_diff(explode('-', $string), $filter_words));
        $current_length = strlen($filtered_string);

        if ($current_length > $length_limit) {
            $new_length = 0;
            $words = explode('-', $filtered_string);
            $new_words = [];
            foreach ($words as $word) {
                $word_length = strlen($word) + 1;
                if (($new_length + $word_length) <= $length_limit) {
                    array_push($new_words, $word);
                    $new_length += $word_length;
                } else {
                    break;
                }
            }
            $filtered_string = implode('-', $new_words);
        }
        return strtolower($filtered_string);
    }

    protected static function slugifyText($string = '')
    {
        return preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
    }

    protected static function stripHtml($string = '')
    {
        return strip_tags($string);
    }

    protected static function cleanCurrency($currency = '')
    {
        $currency = preg_replace('/[^\d.]/', '', $currency);
        if (is_string($currency) || is_int($currency)) {
            $currency = (float) $currency;
        }
        return $currency;
    }

    protected static function cleanUrl($url = '')
    {
        if(strpos($url, "http://") === false && strpos($url, "https://") === false) {
            $url = 'http://'.$url;
        }
        return $url;
    }
}
