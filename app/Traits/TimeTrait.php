<?php

namespace App\Traits;

use DateTime;
use Carbon\Carbon;


trait TimeTrait
{

    // public function timeFilters()
    // {
    //     $today = (int) date('w'); // 0 (for Sunday) through 6 (for Saturday)
    //     if ($today == 6) {
    //         // Saturday → end tomorrow (Sunday)
    //         $end = strtotime('tomorrow 11:15 PM');
    //     } elseif ($today == 0) {
    //         // Sunday → end next Sunday
    //         $end = strtotime('next Sunday 11:15 PM');
    //     } else {
    //         // Any other day → end this coming Sunday
    //         $end = strtotime('Sunday 11:15 PM');
    //     }

    //     return [
    //         'all' => ['start_time >' => strtotime("-3 weeks")],
    //         'analyze' => ['start_time >' => strtotime("-3 weeks"), 'start_time <' => time()],
    //         'past' => ['start_time >' => strtotime("-3 weeks"), 'start_time <' => time()],
    //         'today' => ['start_time >' => strtotime('+2 minutes'), 'start_time <' => strtotime("11:59 p.m")],
    //         'this_week' => ['start_time >' => strtotime('+2 minutes'), 'start_time <' => $end],
    //         'tomorrow' => ['start_time >=' => strtotime('tomorrow 12 am'), 'start_time <' => strtotime('tomorrow 11:59 pm')],
    //         'today_and_tomorrow' => ['start_time >=' => strtotime('+2 minutes'), 'start_time <' => strtotime('tomorrow 11:59 pm')],
    //         'afternow' => ['start_time >' => strtotime('+1 hours')],
    //         'upcoming' => ['start_time >' => strtotime('+2 minutes'), 'start_time <' => strtotime("+1 week")],
    //     ];
    // }

    public function timeFilters()
    {
        $today = (int) date('w'); // 0 (for Sunday) through 6 (for Saturday)
        if ($today == 6) {
            // Saturday → end tomorrow (Sunday)
            $end = strtotime('tomorrow 11:15 PM');
        } elseif ($today == 0) {
            // Sunday → end next Sunday
            $end = strtotime('next Sunday 11:15 PM');
        } else {
            // Any other day → end this coming Sunday
            $end = strtotime('Sunday 11:15 PM');
        }

        return [
            'all' => ['min' => strtotime("-3 weeks")],
            'analyze' => ['min' => strtotime("-3 weeks"), 'max' => time()],
            'past' => ['min' => strtotime("-3 weeks"), 'max' => time()],
            'today' => ['min' => strtotime('+2 minutes'), 'max' => strtotime("11:59 p.m")],
            'this_week' => ['min' => strtotime('+2 minutes'), 'max' => $end],
            'tomorrow' => ['min' => strtotime('tomorrow 12 am'), 'max' => strtotime('tomorrow 11:59 pm'), 'minInclusive' => true],
            // 'today_and_tomorrow' => ['min' => strtotime('+2 minutes'), 'max' => strtotime('tomorrow 11:59 pm'), 'minInclusive' => true],
            'today_and_tomorrow' => [
                'min' => strtotime('+2 minutes'),
                'max' => strtotime('+2 days 11:59 pm'),
                'minInclusive' => true
            ],
            'afternow' => ['min' => strtotime('+1 hours')],
            'upcoming' => ['min' => strtotime('+2 minutes'), 'max' => strtotime("+1 week")],
        ];
    }

    /**
     * Apply time filter to query
     */
    public function applyTimeFilter($query, $filterConfig)
    {
        if (isset($filterConfig['min'])) {
            $operator = isset($filterConfig['minInclusive']) && $filterConfig['minInclusive'] ? '>=' : '>';
            $query->where('start_time', $operator, $filterConfig['min']);
        }

        if (isset($filterConfig['max'])) {
            $query->where('start_time', '<', $filterConfig['max']);
        }

        return $query;
    }

    public function simpleTimeFilters()
    {
        return [
            "Today",
            "Tomorrow",
            "Today and Tomorrow",
            "This Week",
            "Upcoming",
        ];
    }


    public function timeStampToTime($timestamp)
    {
        // $timestamp = 1333699439;
        if (empty($timestamp)) {
            return "";
        }
        $timestamp = $timestamp + date("Z");
        // return gmdate("D, jS M Y", $timestamp);
        return gmdate("h:i a, d/m", $timestamp);
    }


    public function convertToTimestamp($date = null)
    {
        if (!empty($date)) {
            $ts_date = str_replace('/', '-', $date);
            return strtotime($ts_date);
        }
        return "";
    }

    // public function niceTime($time = null, bool $replaceAgo = false): string
    // {
    //     if (empty($time)) {
    //         return '';
    //     }

    //     // Expecting Unix timestamp (seconds)
    //     $time = Carbon::createFromTimestamp((int) $time);
    //     $now = Carbon::now();

    //     $diff = (float) number_format((float) $now->diffInMinutes($time));

    //     if ($diff < 60) {
    //         $timeAgo = $diff . ' minute' . ($diff !== 1 ? 's' : '') . ' ago';
    //     } elseif ($diff < 1440) { // < 24 hours
    //         $hours = intdiv($diff, 60);
    //         $minutes = $diff % 60;

    //         $timeAgo =
    //             $hours . ' hour' . ($hours !== 1 ? 's' : '') .
    //             ($minutes > 0
    //                 ? ', ' . $minutes . ' minute' . ($minutes !== 1 ? 's' : '')
    //                 : ''
    //             ) . ' ago';

    //     } elseif ($diff < 43200) { // < 30 days
    //         $days = intdiv($diff, 1440);
    //         $hours = intdiv($diff % 1440, 60);

    //         $timeAgo =
    //             $days . ' day' . ($days !== 1 ? 's' : '') .
    //             ($hours > 0
    //                 ? ', ' . $hours . ' hour' . ($hours !== 1 ? 's' : '')
    //                 : ''
    //             ) . ' ago';

    //     } else {
    //         $months = intdiv($diff, 43200);
    //         $days = intdiv($diff % 43200, 1440);

    //         $timeAgo =
    //             $months . ' month' . ($months !== 1 ? 's' : '') .
    //             ($days > 0
    //                 ? ', ' . $days . ' day' . ($days !== 1 ? 's' : '')
    //                 : ''
    //             ) . ' ago';
    //     }

    //     // return $timeAgo;
    //     return $replaceAgo
    //         ? trim(str_replace('ago', '', $timeAgo))
    //         : $timeAgo;
    // }

    public function niceTime($time = null, bool $replaceAgo = false): string
    {
        if (empty($time)) {
            return '';
        }

        // Expecting Unix timestamp (seconds)
        $time = Carbon::createFromTimestamp((int) $time);
        $now = Carbon::now();

        $isFuture = $time->greaterThan($now);

        $diff = abs($now->diffInMinutes($time));
        $diff = number_format($diff, 0, '.', '');

        if ($diff < 60) {
            $text = $diff . ' minute' . ($diff !== 1 ? 's' : '');
        } elseif ($diff < 1440) { // < 24 hours
            $hours = intdiv($diff, 60);
            $minutes = $diff % 60;

            $text =
                $hours . ' hour' . ($hours !== 1 ? 's' : '') .
                ($minutes > 0
                    ? ', ' . $minutes . ' minute' . ($minutes !== 1 ? 's' : '')
                    : ''
                );

        } elseif ($diff < 43200) { // < 30 days
            $days = intdiv($diff, 1440);
            $hours = intdiv($diff % 1440, 60);

            $text =
                $days . ' day' . ($days !== 1 ? 's' : '') .
                ($hours > 0
                    ? ', ' . $hours . ' hour' . ($hours !== 1 ? 's' : '')
                    : ''
                );

        } else {
            $months = intdiv($diff, 43200);
            $days = intdiv($diff % 43200, 1440);

            $text =
                $months . ' month' . ($months !== 1 ? 's' : '') .
                ($days > 0
                    ? ', ' . $days . ' day' . ($days !== 1 ? 's' : '')
                    : ''
                );
        }

        if ($isFuture) {
            return 'Starts in ' . $text;
        }

        return $replaceAgo
            ? $text
            : $text . ' ago';
    }


    public function cakeTime($time, bool $replaceAgo = false): string
    {
        if (empty($time)) {
            return '';
        }

        $timeAgo = Carbon::parse($time)->diffForHumans();

        return $replaceAgo
            ? trim(str_replace('ago', '', $timeAgo))
            : $timeAgo;
    }

    public function readableTimestamp($timestamp, $showTime = false)
    {
        // $timestamp = 1333699439;
        if (empty($timestamp)) {
            return "";
        }
        $timestamp = $timestamp + date("Z");
        if ($showTime) {
            return gmdate("D, jS M Y h:i A", $timestamp);
            // return gmdate("l, jS \of F Y h:i A", $timestamp);
        }
        return gmdate("D, jS M Y", $timestamp);
    }

    public function day($dob)
    {
        $test = new DateTime($dob);
        return date_format($test, 'd');
    }
    public function month($dob)
    {
        $test = new DateTime($dob);
        return date_format($test, 'M');
    }
    public function year($dob)
    {
        $test = new DateTime($dob);
        return date_format($test, 'Y');
    }
    public function niceDateMonthDayYear($dob)
    {
        $test = new DateTime($dob);
        return date_format($test, 'M d, Y');
        // return date_format($test, 'F Y');
    }
    public function formReadableTimestamp($timestamp)
    {
        return !empty($timestamp) ? gmdate("d-m-Y H:i", $timestamp) : "";
    }

}
