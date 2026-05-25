<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        
        if ($number == 0) return 'zero';
        
        $num = (int)$number;
        $result = '';
        
        if ($num >= 1000000) {
            $millions = (int)($num / 1000000);
            $result .= self::convert($millions) . ' million ';
            $num %= 1000000;
        }
        
        if ($num >= 1000) {
            $thousands_num = (int)($num / 1000);
            $result .= self::convert($thousands_num) . ' thousand ';
            $num %= 1000;
        }
        
        if ($num >= 100) {
            $hundreds = (int)($num / 100);
            $result .= $ones[$hundreds] . ' hundred ';
            $num %= 100;
        }
        
        if ($num >= 20) {
            $tens_digit = (int)($num / 10);
            $result .= $tens[$tens_digit] . ' ';
            $num %= 10;
        }
        
        if ($num > 0) {
            $result .= $ones[$num] . ' ';
        }
        
        return trim($result);
    }
}
