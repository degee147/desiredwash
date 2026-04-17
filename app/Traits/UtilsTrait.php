<?php

namespace App\Traits;

use DateTime;
use App\Models\Bot;
use App\Models\Buy;
use App\Models\Game;
use App\Models\Sell;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Outcome;
use App\Models\BBOutcome;
use App\Models\Competition;
use App\Models\OutcomeStat;
use Illuminate\Support\Str;
use App\Models\BBCompetition;
use App\Models\BBOutcomeStat;
use App\Models\BasketballGame;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

trait UtilsTrait
{
    /**
     * Replace last occurrence of search string with replacement
     */
    public function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }



    /**
     * Clean JSON string by removing unwanted characters
     */
    public function cleanJson($json)
    {
        // Remove unwanted characters (ASCII 0-31 and 127)
        for ($i = 0; $i <= 31; ++$i) {
            $json = str_replace(chr($i), "", $json);
        }
        $json = str_replace(chr(127), "", $json);

        // Remove BOM (Byte Order Mark) if present
        if (0 === strpos(bin2hex($json), 'efbbbf')) {
            $json = substr($json, 3);
        }

        $json = mb_convert_encoding($json, "UTF-8");
        $json = preg_replace('/[^\x20-\x7E]/', '', $json);
        $json = html_entity_decode($json);
        $json = stripslashes($json);

        return $json;
    }

    /**
     * Get shorter version of a string (first word or first two words)
     */
    public function shorterVersion($string)
    {
        $first_word = strtok($string, " ");
        $second_word = strtok(" ");

        if ((strlen($first_word) + strlen($second_word)) < 7) {
            return $first_word . " " . $second_word;
        }

        if (strlen($first_word) < 3) {
            return $second_word;
        }

        return $first_word;
    }

    /**
     * Add URL parameter to existing URL
     */
    public function addUrlParam($url, $param, $value)
    {
        $urlParts = parse_url($url);
        $queryParams = [];

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $queryParams);
        }

        $queryParams[$param] = $value;

        $newQuery = http_build_query($queryParams);
        $baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'];

        if (isset($urlParts['port'])) {
            $baseUrl .= ':' . $urlParts['port'];
        }

        if (isset($urlParts['path'])) {
            $baseUrl .= $urlParts['path'];
        }

        return $baseUrl . '?' . $newQuery;
    }

    /**
     * Remove URL parameter from existing URL
     */
    public function removeUrlParam($url, $paramToRemove)
    {
        $urlParts = parse_url($url);
        $queryParams = [];

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $queryParams);
        }

        if (isset($queryParams[$paramToRemove])) {
            unset($queryParams[$paramToRemove]);
        }

        $baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'];

        if (isset($urlParts['port'])) {
            $baseUrl .= ':' . $urlParts['port'];
        }

        if (isset($urlParts['path'])) {
            $baseUrl .= $urlParts['path'];
        }

        if (!empty($queryParams)) {
            $newQuery = http_build_query($queryParams);
            return $baseUrl . '?' . $newQuery;
        }

        return $baseUrl;
    }

    /**
     * Update URL by removing one parameter and adding another
     */
    public function updateUrl($url, $paramToRemove, $newParam, $newValue)
    {
        $urlParts = parse_url($url);
        $queryParams = [];

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $queryParams);
        }

        // Remove existing parameter
        if (isset($queryParams[$paramToRemove])) {
            unset($queryParams[$paramToRemove]);
        }

        // Add new parameter
        $queryParams[$newParam] = $newValue;

        $baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'];

        if (isset($urlParts['port'])) {
            $baseUrl .= ':' . $urlParts['port'];
        }

        if (isset($urlParts['path'])) {
            $baseUrl .= $urlParts['path'];
        }

        $newQuery = http_build_query($queryParams);
        return $baseUrl . '?' . $newQuery;
    }

    /**
     * Find array index by value
     */
    public function findIndexByValue($array, $value)
    {
        foreach ($array as $key => $val) {
            if ($val === $value) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Clean query parameters by removing unwanted characters
     */
    public function cleanQuery($data)
    {
        $arr = [];
        foreach ($data as $key => $value) {
            $newKey = str_replace("amp;", "", $key);
            $newKey = str_replace("__", "", $newKey);
            $arr[$newKey] = $value;
        }
        return $arr;
    }

    /**
     * Get readable date from formatted date string
     */
    public function getReadableFromDate($txt)
    {
        $d = DateTime::createFromFormat('d/m/y', $txt);
        if ($d === false) {
            return $txt;
        } else {
            return $this->readableTimestamp($d->getTimestamp());
        }
    }

    /**
     * Get ordinal number (1st, 2nd, 3rd, etc.)
     */
    public function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }

    /**
     * Strip HTML tags and clean text
     */
    public function stripHTML($str)
    {
        $str = str_replace("&nbsp;", " ", $str);
        $str = str_replace("<br>", "\n", $str);
        $str = strip_tags($str);
        return $str;
    }

    /**
     * Calculate success rate percentage
     */
    public function successRate($wins, $fails)
    {
        $total = $wins + $fails;
        $success_rate = !empty($total) ? round(($wins / $total) * 100) : 0;
        return $success_rate;
    }

    /**
     * Validate email address
     */
    public function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Split string into two parts based on delimiters
     */
    public function getNameParts($string, $delimiter1 = ",", $delimiter2 = " ")
    {
        $delimiters = array($delimiter1, $delimiter2);

        $minPos = false;
        $chosenDelimiter = '';

        foreach ($delimiters as $delimiter) {
            $pos = strpos($string, $delimiter);
            if ($pos !== false && ($minPos === false || $pos < $minPos)) {
                $minPos = $pos;
                $chosenDelimiter = $delimiter;
            }
        }

        if ($minPos !== false) {
            $part1 = substr($string, 0, $minPos);
            $part2 = substr($string, $minPos + strlen($chosenDelimiter));
        } else {
            $part1 = $string;
            $part2 = '';
        }

        $part1 = str_replace(array('<', ';', '>', '-'), '', $part1);
        $part2 = str_replace(array('<', ';', '>', '-'), '', $part2);

        return [$part1, $part2];
    }

    /**
     * Truncate string to specified length
     */
    public function truncate($string, $length = 100, $ellipsis = false)
    {
        $truncated = Str::limit($string, $length, '');
        return $ellipsis ? $truncated . '...' : $truncated;
    }

    /**
     * Search for string in multidimensional array
     */
    public function searchArray($array, $searchString)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $this->searchArray($value, $searchString);
                if ($result !== false) {
                    return $result;
                }
            } elseif (strpos($value, $searchString) !== false) {
                return $value;
            }
        }
        return false;
    }

    public function showUserRoles($user)
    {
        $roles = [];
        if ($user->sa) {
            $roles[] = "sa";
        }
        if ($user->admin) {
            $roles[] = "admin";
        }
        if ($user->autopilot) {
            $roles[] = "autopilot";
        }
        if ($user->editor) {
            $roles[] = "editor";
        }
        if ($user->elite) {
            $roles[] = "elite";
        }
        return implode(', ', $roles);

    }

    public function removeCommaEntries(array $inputArray): array
    {
        return array_filter($inputArray, function ($value) {
            return strpos($value, ',') === false;
        });
    }

    public function sortLeaguesByKeyAsc($stats, $key)
    {
        // Custom comparison function to sort by 'success_rate' in descending order
        uasort($stats, function ($a, $b) use ($key) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($a[$key] < $b[$key]) ? -1 : 1;
        });

        return $stats;
    }
    public function sortLeaguesByKeyDesc($stats, $key)
    {
        // Custom comparison function to sort by 'success_rate' in descending order
        uasort($stats, function ($a, $b) use ($key) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($a[$key] > $b[$key]) ? -1 : 1;
        });

        return $stats;
    }
    public function displayROI($roi, $dec = 3, $p = true)
    {
        $roi = (float) number_format((float) $roi, $dec, '.', '');
        return '<' . ($p ? 'p' : 'span') . ' style="color:' . ($roi == 0.000 ? 'black' : ($roi < 0.000 ? 'black' : "white")) . ';margin-bottom:0;" ' . $roi . ' class="' . ($roi > 0 ? "gradient-green-tea" : ($roi == 0.00 ? 'bg-info' : "bg-warning")) . '">&nbsp;$' . $roi . '&nbsp;</' . ($p ? 'p' : 'span') . '>';
    }

    public function doLog($filename, $data = [], $write = false, $attach = "")
    {
        $path = storage_path('logs/' . $filename);

        if (!empty($data)) {
            if (is_array($data)) {
                $data = json_encode($data);
            }

            if ($write) {
                file_put_contents($path, $data);
            } else {
                file_put_contents($path, $data . $attach, FILE_APPEND);
            }
        } else {
            $query = request()->query();
            file_put_contents($path, json_encode($query), FILE_APPEND);
        }

        // Optional: add a separator if needed
        // if (!$write) {
        //     file_put_contents($path, str_repeat('-', 80) . PHP_EOL, FILE_APPEND);
        // }
    }

    public function getUser($id)
    {
        return User::where('id', $id)
            ->first();
    }

    public function escapeString(string $str): string
    {
        $connection = DB::connection()->getPdo();
        return $connection->quote($str);
    }



    public function displayColoredStopLoss($trade)
    {
        return '<span ' . ((float) $trade->buy->price > (float) $trade->stop_loss ? "" : 'class="bg-primary" style="color:white"') . '">&nbsp;$' . number_format($trade->stop_loss, 6) . '&nbsp;</span>';
    }


    public function userFullTextQuery($search_term, $model = "Users")
    {
        return [
            // ["MATCH(" . $model . ".username) AGAINST('{$search_term}'IN BOOLEAN MODE)"],
            // ['LOWER(' . $model . '.username) LIKE' => "%" . $search_term . "%"],
            // ["MATCH(" . $model . ".email) AGAINST('{$search_term}'IN NATURAL LANGUAGE MODE)"],
            // ["MATCH(" . $model . ".firstname) AGAINST('{$search_term}'IN NATURAL LANGUAGE MODE)"],
            // ["MATCH(" . $model . ".lastname) AGAINST('{$search_term}'IN NATURAL LANGUAGE MODE)"],
            ['LOWER(' . $model . '.name) LIKE' => "%" . strtolower($search_term) . "%"],
            ['LOWER(' . $model . '.email) LIKE' => "%" . strtolower($search_term) . "%"],
            ['LOWER(' . $model . '.phone) LIKE' => "%" . strtolower($search_term) . "%"],
            // ['LOWER(' . $model . '.refcode) LIKE' => "%" . strtolower($search_term) . "%"],

        ];
    }


    public function getUserBalance($user_id)
    {
        $balance = [
            "available" => "0.00000000",
            "onOrder" => "0.00000000",
            "btcValue" => 0.0,
            "btcTotal" => 0.0
        ];

        return $this->arrayChangeKeyCaseRecursive($balance, CASE_LOWER);
    }

    public function arrayChangeKeyCaseRecursive($arr, $case = CASE_LOWER)
    {
        return array_map(function ($item) use ($case) {
            if (is_array($item))
                $item = $this->arrayChangeKeyCaseRecursive($item, $case);
            return $item;
        }, array_change_key_case($arr, $case));
    }

    /*
   Converts any variable to its best array representation
   */
    public function toArray($variable)
    {

        $snapshot = null;
        if (gettype($variable) == 'string') {
            $snapshot = json_decode($variable, true);
        } elseif (gettype($variable) == 'object') {

            try {
                $snapshot = $variable->toArray();
            } catch (\Error $e) {
                // $snapshot = $variable;
                $reflectionClass = new \ReflectionClass(get_class($variable));
                $array = array();
                foreach ($reflectionClass->getProperties() as $property) {
                    $property->setAccessible(true);
                    $array[$property->getName()] = $property->getValue($variable);
                    $property->setAccessible(false);
                }
                return $array;
            }
        } else {
            $snapshot = $variable;
        }


        //dd(gettype($order_snapshot));
        // if (gettype($order_snapshot) == 'object') {
        //     $snapshot = (array) $order_snapshot;
        // }

        return json_decode(json_encode($snapshot), true);
        //return (array) $snapshot;
    }

    public function numberTowords($num)
    {
        $ones = array(
            0 => "ZERO",
            1 => "ONE",
            2 => "TWO",
            3 => "THREE",
            4 => "FOUR",
            5 => "FIVE",
            6 => "SIX",
            7 => "SEVEN",
            8 => "EIGHT",
            9 => "NINE",
            10 => "TEN",
            11 => "ELEVEN",
            12 => "TWELVE",
            13 => "THIRTEEN",
            14 => "FOURTEEN",
            15 => "FIFTEEN",
            16 => "SIXTEEN",
            17 => "SEVENTEEN",
            18 => "EIGHTEEN",
            19 => "NINETEEN",
            "014" => "FOURTEEN"
        );
        $tens = array(
            0 => "ZERO",
            1 => "TEN",
            2 => "TWENTY",
            3 => "THIRTY",
            4 => "FORTY",
            5 => "FIFTY",
            6 => "SIXTY",
            7 => "SEVENTY",
            8 => "EIGHTY",
            9 => "NINETY"
        );
        $hundreds = array(
            "HUNDRED",
            "THOUSAND",
            "MILLION",
            "BILLION",
            "TRILLION",
            "QUARDRILLION"
        ); /*limit t quadrillion */
        $num = number_format($num, 2, ".", ",");
        $num_arr = explode(".", $num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",", $wholenum));
        krsort($whole_arr, 1);
        $rettxt = "";
        foreach ($whole_arr as $key => $i) {

            while (substr($i, 0, 1) == "0")
                $i = substr($i, 1, 5);
            if ($i < 20) {
                /* echo "getting:".$i; */
                $rettxt .= $ones[$i];
            } elseif ($i < 100) {
                if (substr($i, 0, 1) != "0")
                    $rettxt .= $tens[substr($i, 0, 1)];
                if (substr($i, 1, 1) != "0")
                    $rettxt .= " " . $ones[substr($i, 1, 1)];
            } else {
                if (substr($i, 0, 1) != "0")
                    $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                if (substr($i, 1, 1) != "0")
                    $rettxt .= " " . $tens[substr($i, 1, 1)];
                if (substr($i, 2, 1) != "0")
                    $rettxt .= " " . $ones[substr($i, 2, 1)];
            }
            if ($key > 0) {
                $rettxt .= " " . $hundreds[$key] . " ";
            }
        }
        if ($decnum > 0) {
            $rettxt .= " and ";
            if ($decnum < 20) {
                $rettxt .= $ones[$decnum];
            } elseif ($decnum < 100) {
                $rettxt .= $tens[substr($decnum, 0, 1)];
                $rettxt .= " " . $ones[substr($decnum, 1, 1)];
            }
        }
        return $rettxt;
    }


}
