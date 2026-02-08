<?php

use App\Models\ApprovalsTracker;
use App\Models\SettingsMenu;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

function formatDate($date)
{
    if ($date) {
        $date = Carbon::parse($date)->format('M d, Y');
        return $date;
    }
}

function formatDateTime($dateTime)
{
    if ($dateTime) {
        $dateTime = Carbon::parse($dateTime)->format('d/m/Y h:i:s');
        return $dateTime;
    }
}

function Convert_amount_to_words($amount)
{
    // Separate dollars and cents
    list($dollars, $cents) = explode('.', $amount);

    // Set default values for dollars and cents
    $dollars = isset($parts[0]) ? $parts[0] : '0';
    $cents = isset($parts[1]) ? $parts[1] : '00';

    // Convert dollars to words
    $dollarsInWords = convertNumberToWords($dollars);

    // Convert cents to words
    $centsInWords = convertNumberToWords($cents);

    // Prepare the final result
    $result = ucfirst($dollarsInWords) . ' shillings';
    if ($cents > 0) {
        $result .= ' and ' . $centsInWords . ' cents';
    }

    return $result;
}

function convertNumberToWords($number)
{
    // Array of words for numbers
    $words = [
        '',
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'eleven',
        'twelve',
        'thirteen',
        'fourteen',
        'fifteen',
        'sixteen',
        'seventeen',
        'eighteen',
        'nineteen',
        'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
    ];

    // Define units
    $units = ['', 'thousand', 'million', 'billion'];

    $wordsArray = [];

    // Process each 3-digit group
    for ($i = 0, $len = strlen($number); $i < $len; $i += 3) {
        $chunk = substr($number, $len - $i - 3, 3);
        $chunk = (int)$chunk;

        if ($chunk) {
            // Extract hundreds, tens, and ones
            $hundreds = floor($chunk / 100);
            $tensUnits = $chunk % 100;

            // Convert hundreds to words
            if ($hundreds) {
                $wordsArray[] = $words[$hundreds] . ' hundred';
            }

            // Convert tens and units to words
            if ($tensUnits) {
                if ($tensUnits < 20 || ($tensUnits % 10 == 0)) {
                    $wordsArray[] = $words[$tensUnits];
                } else {
                    $tens = (int)($tensUnits / 10) * 10;
                    $units = $tensUnits % 10;
                    $wordsArray[] = $words[$tens] . '-' . $words[$units];
                }
            }

            // Add the corresponding unit
            if ($i / 3 > 0 && $chunk) {
                $wordsArray[] = $units[$i / 3];
            }
        }
    }

    // Reverse the array to maintain correct order
    $wordsArray = array_reverse($wordsArray);

    // Combine words into a single string
    return implode(' ', $wordsArray);
}

function firstUpper($string)
{
    $str = ucwords(Str::lower($string));
    return $str;
}

// function company(){
//     $company = Company::where('company_id', 1)->first();
//     return $company;
// }



function settingsMenus()
{
    return SettingsMenu::whereNull('parent_id')->with('children')->get();
}

function convert_to_words($num = false)
{
    $num = str_replace(array(',', ' '), '', trim($num));
    if (!$num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array(
        '',
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'eleven',
        'twelve',
        'thirteen',
        'fourteen',
        'fifteen',
        'sixteen',
        'seventeen',
        'eighteen',
        'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array(
        '',
        'thousand',
        'million',
        'billion',
        'trillion',
        'quadrillion',
        'quintillion',
        'sextillion',
        'septillion',
        'octillion',
        'nonillion',
        'decillion',
        'undecillion',
        'duodecillion',
        'tredecillion',
        'quattuordecillion',
        'quindecillion',
        'sexdecillion',
        'septendecillion',
        'octodecillion',
        'novemdecillion',
        'vigintillion'
    );
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ($tens < 20) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return implode(' ', $words);
}

function pendingApprovalsCount()
{
    $userId = Auth::user()->id;
    return  ApprovalsTracker::where('approver', $userId)
        ->where('status', 'P')
        ->count();
}

function formatCurrency($amount, $currency = 'KES', $decimals = 2)
{
    if (is_null($amount)) {
        return $currency . ' 0.00';
    }

    $formatted = number_format($amount, $decimals, '.', ',');
    return $currency . ' ' . $formatted;
}
