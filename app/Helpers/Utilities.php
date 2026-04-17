<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

if (!function_exists('last_seen')) {
    function last_seen($user)
    {
        if (!empty($user->last_active_time)) {
            $time = Carbon::createFromTimestamp((int) $user->last_active_time);
            if ($time->greaterThanOrEqualTo(Carbon::now()->subMinutes(3))) {
                return "Online";
            } else {
                return $time->diffForHumans();
            }
        }
        return "Offline";
    }
}

if (!function_exists('get_yearly_price')) {
    function get_yearly_price($price, $getDiscount = false, $voucher = null, $discount = 25)
    {
        $yearly = (float) $price * 12;
        $calculated_discount = $yearly - (($yearly / 100) * $discount);
        $monthly = number_format(($calculated_discount / 12));
        return $getDiscount ? $calculated_discount : $monthly;
    }
}

if (!function_exists('ip_info')) {
    function ip_info($ip = null, $purpose = "location", $deep_detect = true)
    {
        $output = null;
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = request()->ip();
        }
        $purpose = str_replace(["name", "\n", "\t", " ", "-", "_"], '', strtolower(trim($purpose)));
        $support = ["country", "countrycode", "state", "region", "city", "location", "address"];
        $continents = [
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        ];
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (!empty($ipdat) && $ipdat->geoplugin_status == 200 && @strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = [
                            "city" => @$ipdat->geoplugin_city,
                            "state" => @$ipdat->geoplugin_regionName,
                            "country" => @$ipdat->geoplugin_countryName,
                            "country_code" => @$ipdat->geoplugin_countryCode,
                            "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        ];
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }
}

if (!function_exists('generate_random_alphanumeric')) {
    function generate_random_alphanumeric($size)
    {
        $alpha_key = '';
        $keys = range('a', 'z');
        for ($i = 0; $i < 2; $i++) {
            $alpha_key .= $keys[array_rand($keys)];
        }
        $length = $size - 2;
        $key = '';
        $keys = range(0, 9);
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $alpha_key . $key;
    }
}

if (!function_exists('contains_any')) {
    function contains_any($str, array $arr)
    {
        foreach ($arr as $a) {
            if (stripos(strtolower($str), strtolower($a)) !== false)
                return true;
        }
        return false;
    }
}

if (!function_exists('str_lreplace')) {
    function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }
}

if (!function_exists('add_position_suffix')) {
    function add_position_suffix($position)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if (empty($position)) {
            return '';
        }
        if ((($position % 100) >= 11) && (($position % 100) <= 13)) {
            return 'th';
        } else {
            return $ends[$position % 10];
        }
    }
}

if (!function_exists('random_string')) {
    function random_string($length = 6, $upper = false)
    {
        $str = "";
        $characters = $upper ? range('A', 'Z') : range('a', 'z');
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
}

if (!function_exists('csv_to_array')) {
    function csv_to_array($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }
        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    if (count($header) == count($row)) {
                        $data[] = array_combine($header, $row);
                    }
                }
            }
            fclose($handle);
        }
        return $data;
    }
}

if (!function_exists('array_change_key_case_recursive')) {
    function array_change_key_case_recursive($arr, $case = CASE_LOWER)
    {
        return array_map(function ($item) use ($case) {
            if (is_array($item))
                $item = array_change_key_case_recursive($item, $case);
            return $item;
        }, array_change_key_case($arr, $case));
    }
}

if (!function_exists('display_roi')) {
    function display_roi($roi, $dec = 3, $p = true)
    {
        $roi = (float) number_format((float) $roi, $dec, '.', '');
        return '<' . ($p ? 'p' : 'span') . ' style="color:' . ($roi == 0.000 ? 'black' : ($roi < 0.000 ? 'black' : "white")) . ';margin-bottom:0;" ' . $roi . ' class="' . ($roi > 0 ? "gradient-green-tea" : ($roi == 0.00 ? 'bg-info' : "bg-warning")) . '">&nbsp;$' . $roi . '&nbsp;</' . ($p ? 'p' : 'span') . '>';
    }
}

if (!function_exists('remove_all_white_spaces')) {
    function remove_all_white_spaces($string)
    {
        return strtolower(trim(preg_replace('/\s+/', '', $string)));
    }
}

if (!function_exists('get_dp')) {
    function get_dp($image_url, $folder = null, $size = null)
    {
        return dp_url($image_url, $folder, $size);
    }
}

if (!function_exists('remote_file_exists')) {
    function remote_file_exists($url)
    {
        $headers = @get_headers($url);
        return $headers && strpos($headers[0], '404') === false;
    }
}

if (!function_exists('hours_and_mins')) {
    function hours_and_mins($time, $format = '%02d:%02d')
    {
        $format = '%02d hour(s) %02d minute(s)';
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }
}

if (!function_exists('access_protected')) {
    function access_protected($obj, $prop)
    {
        try {
            $reflection = new \ReflectionClass($obj);
            $property = $reflection->getProperty($prop);
            $property->setAccessible(true);
            return $property->getValue($obj);
        } catch (\Exception $e) {
        }
        return false;
    }
}

if (!function_exists('to_array')) {
    function to_array($variable)
    {
        $snapshot = null;

        if (is_string($variable)) {
            $snapshot = json_decode($variable, true);
        } elseif (is_object($variable)) {
            if (method_exists($variable, 'toArray')) {
                $snapshot = $variable->toArray();
            } else {
                $snapshot = $variable;
                // dd("reflexion");
                //this section not working as expected
                // $reflectionClass = new \ReflectionClass(get_class($variable));
                // $array = [];
                // foreach ($reflectionClass->getProperties() as $property) {
                //     $property->setAccessible(true);
                //     $array[$property->getName()] = $property->getValue($variable);
                //     $property->setAccessible(false);
                // }
                // return $array;
            }
        } else {
            $snapshot = $variable;
        }

        return json_decode(json_encode($snapshot), true);
    }
}

if (!function_exists('dp_url')) {
    function dp_url($image_url, $folder = null, $size = null)
    {
        if (!empty($image_url)) {
            if (strpos($image_url, 'https://') !== false) {
                return $image_url;
            }
            $path = public_path('uploads/' . ($folder ? $folder . '/' : '') . $image_url);
            if (File::exists($path)) {
                $url = url('uploads/' . ($folder ? $folder . '/' : '') . $image_url);
                return $url;
            }
        }
        return 'https://www.placehold.it/' . (!empty($size) ? $size : '150x150') . '/EFEFEF/AAAAAA&amp;text=no+image';
    }
}

if (!function_exists('make_links')) {
    function make_links($str)
    {
        $reg_exUrl = "/(((http|https|ftp|ftps)\:\/\/)|(www\.))[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/";
        $urls = [];
        $urlsToReplace = [];
        if (preg_match_all($reg_exUrl, $str, $urls)) {
            $numOfMatches = count($urls[0]);
            $numOfUrlsToReplace = 0;
            for ($i = 0; $i < $numOfMatches; $i++) {
                $alreadyAdded = false;
                $numOfUrlsToReplace = count($urlsToReplace);
                for ($j = 0; $j < $numOfUrlsToReplace; $j++) {
                    if ($urlsToReplace[$j] == $urls[0][$i]) {
                        $alreadyAdded = true;
                    }
                }
                if (!$alreadyAdded) {
                    array_push($urlsToReplace, $urls[0][$i]);
                }
            }
            $numOfUrlsToReplace = count($urlsToReplace);
            for ($i = 0; $i < $numOfUrlsToReplace; $i++) {
                $mystring = $urlsToReplace[$i];
                if (!(strpos($mystring, 'http://') !== false || strpos($mystring, 'https://') !== false)) {
                    $mystring = "http://" . $urlsToReplace[$i];
                }
                $str = str_replace($urlsToReplace[$i], "<a class=\"msglink\" href=\"" . $mystring . "\">" . $urlsToReplace[$i] . "</a> ", $str);
            }
            return $str;
        } else {
            return $str;
        }
    }
}

if (!function_exists('show_array_items_as_string')) {
    function show_array_items_as_string($array)
    {
        $string = "";
        if (!empty($array)) {
            foreach ($array as $array_item) {
                $string .= $array_item->name . ", ";
            }
        } else {
            return "N/A";
        }
        return str_lreplace(",", "", $string);
    }
}

if (!function_exists('truncate_by_words')) {
    function truncate_by_words($string, $limit = 2)
    {
        $words = str_word_count($string, 2);
        $positions = array_keys($words);
        if ($limit > 0 && count($words) > $limit) {
            $last_pos = $positions[$limit - 1];
            $string = substr($string, 0, $last_pos);
        }
        return $string;
    }
}

if (!function_exists('custom_truncate')) {
    function custom_truncate($string, $length = 50, $ellipsis = false)
    {
        $options = [
            'ellipsis' => $ellipsis ? '...' : '',
            'exact' => true,
            'html' => false
        ];
        return Str::limit($string, $length, $options['ellipsis']);
    }
}

if (!function_exists('make_username')) {
    function make_username($firstname, $lastname, $random_username = false)
    {
        if ($lastname == "N/A") {
            $lastname = random_string(4);
        }
        return trim(strtolower(str_replace(" ", "", $firstname) . "." . str_replace(" ", "", $lastname)
            . ($random_username ? '.' . random_string(6) : '')));
    }
}

if (!function_exists('seconds_to_days')) {
    function seconds_to_days($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a day(s), %h hour(s), %i minute(s)');
    }
}

if (!function_exists('user_has_role')) {
    function user_has_role($user_roles, $action_roles)
    {
        return count(array_intersect($user_roles, $action_roles)) > 0;
    }
}

if (!function_exists('get_sum_of_products')) {
    function get_sum_of_products($products, $notes, $config_quantity = null)
    {
        $price = 0;
        $array_variable = json_decode($notes, true);

        if (!empty($products)) {
            foreach ($products as $value) {
                $value = (object) $value;
                $product_price = 0;
                if (!empty($array_variable) && array_key_exists('products_quantity', $array_variable)) {
                    if (array_key_exists($value->id, $array_variable['products_quantity'])) {
                        $product_price += ($value->price * $array_variable['products_quantity'][$value->id]);
                    } else {
                        $product_price += $value->price;
                    }
                } else {
                    $product_price += $value->price;
                }
                if (!empty($config_quantity['products']) && array_key_exists($value->id, $config_quantity['products'])) {
                    $product_price = $product_price * $config_quantity['products'][$value->id];
                }
                if (!empty($config_quantity['product']) && array_key_exists($value->id, $config_quantity['product'])) {
                    $product_price = $product_price * $config_quantity['product'][$value->id];
                }
                $price += $product_price;
            }
        }
        return $price;
    }
}

if (!function_exists('show_ticket_status')) {
    function show_ticket_status($status)
    {
        if ($status == "pending") {
            return '<span class="badge bg-info white">Pending</span>';
        } else {
            return '<span class="badge bg-success white">Closed</span>';
        }
    }
}

if (!function_exists('get_day')) {
    function get_day($index)
    {
        $days = [
            1 => "monday",
            2 => "tuesday",
            3 => "wednesday",
            4 => "thursday",
            5 => "friday"
        ];
        return $days[$index] ?? null;
    }
}

if (!function_exists('get_meal_type')) {
    function get_meal_type($index)
    {
        $meals = [
            1 => "breakfast",
            2 => "lunch",
            3 => "dinner"
        ];
        return $meals[$index] ?? null;
    }
}

if (!function_exists('get_day_reversed')) {
    function get_day_reversed($day)
    {
        $days = [
            "monday" => 1,
            "tuesday" => 2,
            "wednesday" => 3,
            "thursday" => 4,
            "friday" => 5
        ];
        return $days[$day] ?? null;
    }
}

if (!function_exists('get_meal_type_reversed')) {
    function get_meal_type_reversed($meal)
    {
        $meals = [
            "breakfast" => 1,
            "lunch" => 2,
            "dinner" => 3
        ];
        return $meals[$meal] ?? null;
    }
}

if (!function_exists('number_to_words')) {
    function number_to_words($num)
    {
        $ones = [
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
        ];
        $tens = [
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
        ];
        $hundreds = [
            "HUNDRED",
            "THOUSAND",
            "MILLION",
            "BILLION",
            "TRILLION",
            "QUARDRILLION"
        ];
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

if (!function_exists('get_browser_name')) {
    function get_browser_name($user_agent)
    {
        $arr_browsers = ["Opera", "Edge", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];
        $agent = $user_agent;
        $user_browser = '';
        foreach ($arr_browsers as $browser) {
            if (strpos($agent, $browser) !== false) {
                $user_browser = $browser;
                break;
            }
        }
        switch ($user_browser) {
            case 'MSIE':
            case 'Trident':
                $user_browser = 'Internet Explorer';
                break;
            case 'Edge':
                $user_browser = 'Microsoft Edge';
                break;
        }
        return $user_browser;
    }
}

if (!function_exists('get_percentage_of_amount')) {
    function get_percentage_of_amount($amount, $percentage)
    {
        return (($amount / 100) * $percentage);
    }
}
if (!function_exists('get_pricing_params')) {
    function get_pricing_params($voucher = null)
    {
        // $voucher can be passed as an object, array, or null
        if (!empty($voucher) && !empty($voucher->code)) {
            return ['voucher' => $voucher->code];
        }
        return [];
    }
}
