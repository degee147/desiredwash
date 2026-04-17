<?php

use App\Models\Option;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

// app/Helpers/CustomHelpers.php

if (!function_exists('format_view_count')) {
    function format_view_count($views)
    {
        if ($views >= 1000000) {
            return number_format($views / 1000000, 1) . 'M';
        } elseif ($views >= 1000) {
            return number_format($views / 1000, 1) . 'k';
        } else {
            return $views;
        }

    }
}
if (!function_exists('custom_format_date')) {
    function custom_format_date($date)
    {
        return \Carbon\Carbon::parse($date)->format('D, M d, Y, h:i A');
    }
}


if (!function_exists('short_date')) {
    function short_date($date)
    {
        if (empty($date)) {
            return '';
        }
        return date('M j, Y', strtotime($date));

    }
}

if (!function_exists('show_ticket_status')) {
    function show_ticket_status($status)
    {
        if ($status == "pending") {
            return '<span class="md-bg-cyan" style="padding: 5px;">Pending</span>';
            // return '<span class="badge bg-info white">Pending</span>';
        } else {
            return '<span class="md-bg-green-900 uk-text-contrast" style="padding: 5px;">Closed</span>';
            // return '<span class="badge bg-success white">Closed</span>';
            // return '<span class="label label-success bg-success white">Closed</span>';
        }
    }
}
if (!function_exists('time_ago_in_words')) {
    function time_ago_in_words($datetime)
    {
        $time = \Carbon\Carbon::parse($datetime);
        return $time->diffForHumans();
    }
}
if (!function_exists('get_option')) {
    function get_option($key, $default = null)
    {
        // $option = Option::where('key', $key)->first();
        // return $option ? $option->value : $default;
    }
}

if (!function_exists('add_vat')) {
    function add_vat($amount, $vatPercentage)
    {
        $vatAmount = ($amount * $vatPercentage) / 100;
        $totalAmount = $amount + $vatAmount;
        return $totalAmount;
    }
}
if (!function_exists('get_vat_amount')) {
    function get_vat_amount($amount, $percentage)
    {
        return ($amount * $percentage) / 100;
    }
}


if (!function_exists('get_future_timestamp')) {
    function get_future_timestamp($days)
    {
        return Carbon::now()->addDays((int) $days);
    }
}

if (!function_exists('points_to_cordinates')) {
    function points_to_cordinates($pointsString)
    {
        // Split the string into an array of coordinates
        $pointsArray = explode(' ', $pointsString);

        // Initialize an empty array to hold the coordinates
        $coordinates = [];

        // Iterate over the points array two elements at a time
        for ($i = 0; $i < count($pointsArray); $i += 2) {
            // Add each pair of points as an array of [x, y] to the coordinates array
            $coordinates[] = [(float) $pointsArray[$i], (float) $pointsArray[$i + 1]];
        }

        // Return the coordinates array as a JSON string
        return $coordinates;
    }
}

if (!function_exists('our_public_path')) {
    function our_public_path(string $dir, $file = null, string $format = 'png')
    {
        // Determine the public directory based on the environment
        $publicDir = app()->environment('production')
            ? '/home/taporczr/staging.tapolgroup.com/'
            : public_path();

        return $publicDir;
    }
}
if (!function_exists('upload_file')) {
    function upload_file(string $dir, $file = null, string $format = 'png', $custom_file_name = null, $thumbnail = false)
    {
        $publicDir = public_path();
        $uploadsDir = $publicDir . '/uploads/' . $dir;

        $fileName = 'no-file.png';
        $extension = null;
        $size = 0;
        $thumbFileName = null;

        if ($file !== null) {
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();

            $fileName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!empty($custom_file_name)) {
                $fileName = $custom_file_name;
            }

            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }

            $destinationPath = $uploadsDir . '/' . $fileName;

            // ✅ Use Intervention Image instead of move()
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // Resize if width > 500px (maintain aspect ratio)
            if ($image->width() > 500) {
                $image->scale(width: 500);
            }

            // Save compressed image (loop quality until <500KB if needed)
            $quality = 90; // start quality
            do {
                $image->encodeByExtension($format, $quality)->save($destinationPath);
                $currentSize = filesize($destinationPath);
                $quality -= 5;
            } while ($currentSize > 500 * 1024 && $quality > 10);

            $size = filesize($destinationPath); // final saved size

            if ($thumbnail) {
                try {
                    $thumbFileName = 'thumb_' . $fileName;
                    $thumbPath = $uploadsDir . '/' . $thumbFileName;

                    $thumb = $manager->read($destinationPath);
                    $thumb->scale(width: 300)->encodeByExtension($format, 80)->save($thumbPath);
                } catch (\Exception $e) {
                    // logger()->error('Thumbnail creation failed: ' . $e->getMessage());
                }
            }
        }

        return [
            "name" => $fileName,
            "ext" => $extension,
            "size" => $size,
            "date" => now(),
            "thumbnail" => $thumbFileName,
        ];
    }
}
if (!function_exists(function: 'remove_file')) {
    function remove_file(string $dir, $fileName = "")
    {
        if (empty($fileName)) {
            return false;
        }
        $filePath = public_path('uploads/' . $dir . '/' . $fileName);

        if (file_exists($filePath)) {
            unlink($filePath); // Deletes the file
            return true; // Return true if file was successfully removed
        }

        return false; // Return false if file was not found
    }
}
if (!function_exists(function: 'get_initials')) {
    function get_initials($name)
    {
        $initials = collect(explode(' ', $name))->map(fn($part) => strtoupper($part[0]))->join('');
        return $initials;
    }
}
if (!function_exists(function: 'display_picture')) {
    function display_picture(string $dir, $fileName = "", $business = false)
    {
        if (filter_var($fileName, FILTER_VALIDATE_URL)) {
            // $fileName is a link, return it
            return $fileName;
        }

        if (empty($fileName)) {
            if ($business) {
                return asset('assets/img/icon/1.png');
            }
            return asset('images/avatars/avatar_22.jpg');
        }
        if (filter_var($fileName, FILTER_VALIDATE_URL)) {
            // $fileName is a link, return it
            return $fileName;
        }

        return asset('uploads/' . $dir . '/' . $fileName);
    }
}
if (!function_exists(function: 'course_duration_text')) {
    function course_duration_text($course)
    {
        // Get the duration in days
        $durationInDays = $course->duration;

        // Calculate the number of years, months, and remaining days
        $years = floor($durationInDays / 365); // 1 year = 365 days
        $months = floor(($durationInDays % 365) / 30); // 1 month = 30 days
        $days = $durationInDays % 30; // Remaining days

        // Prepare a readable text based on the values
        $durationText = '';

        if ($years > 0) {
            $durationText .= $years . ' year' . ($years > 1 ? 's' : '') . ' ';
        }
        if ($months > 0 and $years < 1) {
            $durationText .= $months . ' month' . ($months > 1 ? 's' : '') . ' ';
        }
        if ($days > 0 and $years < 1) {
            $durationText .= $days . ' day' . ($days > 1 ? 's' : '');
        }

        // Return the formatted duration
        return $durationText ?: '0 days'; // In case the duration is 0
    }
}

if (!function_exists(function: 'course_level')) {
    function course_level($course)
    {

        if ($course->category_id == 1) {
            return "Beginner";
        }
        if ($course->category_id == 2) {
            return "Intermediate";
        }
        if ($course->category_id >= 3) {
            return "Advanced";
        }
    }
}

if (!function_exists(function: 'increase_by_percentage')) {
    function increase_by_percentage($amount, $percentage = 1.25)
    {
        $increasedNumber = $amount * $percentage;
        return round($increasedNumber / 5000) * 5000;

    }
}

if (!function_exists('user_role')) {
    function user_role($user)
    {
        if ($user->sa or $user->admin) {
            return 'Admin';
        }
        if ($user->instructor) {
            return 'Instructor';
        }
        if ($user->student) {
            return 'Student';
        }
        return '';

    }
}


if (!function_exists(function: 'clean_time_for_db')) {
    function clean_time_for_db($str, $default = "")
    {
        if (!empty($str)) {
            if (strlen($str) > 5) { // Check if time already has seconds (H:i:s format)
                $str = Carbon::parse($str)->format('H:i:s');
            } else {
                $str = Carbon::createFromFormat('H:i', $str)->format('H:i:s');
            }
            return $str;
        }

        return $default;
    }
}

if (!function_exists('get_forum_user_role')) {
    function get_forum_user_role($topic, $user)
    {
        $txt = "";
        if ($topic->user_id == $user->id) {
            $txt .= 'original-poster';
        }
        if ($user->moderator) {
            $txt .= ' moderator';
        }
        if ($user->staff) {
            $txt .= ' staff';
        }
        return $txt;
    }
}
if (!function_exists('random_class')) {
    function random_class()
    {
        $classes = [
            'bt-3 border-primary',
            'bs-3 border-info',
            // 'be-3 border-danger',
            // 'bb-3 border-warning',
        ];

        return $classes[array_rand($classes)];
    }
}

if (!function_exists('random_color')) {
    function random_color()
    {
        $classes = [
            'warning',
            'danger',
            'success',
            'info',
            'primary',
        ];

        return $classes[array_rand($classes)];
    }
}
if (!function_exists('highlight')) {
    /**
     * Highlight search term inside a string by wrapping it in a <span> with a highlight class.
     *
     * @param string $text The full text.
     * @param string $term The search term to highlight.
     * @return string The text with highlighted term.
     */
    function highlight(string $text, string $term): string
    {
        if (!$term)
            return $text;

        $escapedTerm = preg_quote($term, '/');
        return preg_replace(
            "/($escapedTerm)/i",
            '<span class="highlight">$1</span>',
            e($text)  // escape text to prevent XSS
        );
    }
}

if (!function_exists('random_color2')) {
    function random_color2()
    {
        $classes = [
            'blue',
            'pink',
            'red',
            'black',
            'green',
            'violet',
            'yellow',
            'cyan',
        ];

        return $classes[array_rand($classes)];
    }
}
if (!function_exists('random_class_color')) {
    function random_class_color()
    {
        $classes = [
            'blue_text',
            'pink_text',
            'orange_text',
            'black_text',
            'green_text',
            'purple_text',
        ];

        return $classes[array_rand($classes)];
    }
}

if (!function_exists('get_embed_video')) {
    function get_embed_video($video)
    {
        $videoId = null;
        if (preg_match('/v=([^&]+)/', $video, $matches)) {
            $videoId = $matches[1];
        }
        $embedUrl = $videoId ? "https://www.youtube.com/embed/$videoId" : $video;

        return $embedUrl;
    }
}

if (!function_exists('get_first_word')) {
    function get_first_word($string)
    {
        $words = explode(' ', $string);
        return $words[0];

    }
}


if (!function_exists('getFirstSentence')) {
    function getFirstSentence($paragraph)
    {
        $paragraph = strip_tags($paragraph); // remove HTML tags
        $paragraph = html_entity_decode($paragraph); // decode HTML entities
        $sentences = preg_split('/[.!?]/', $paragraph, 2); // split into sentences
        return trim($sentences[0]) . '.';


    }
}

if (!function_exists('generateSession')) {
    function generateSession()
    {
        $year = date('Y');
        $month = date('n'); // Get current month (1-12)

        // Determine the quarter
        if ($month >= 1 && $month <= 3) {
            $quarter = 'A';
        } elseif ($month >= 4 && $month <= 6) {
            $quarter = 'B';
        } elseif ($month >= 7 && $month <= 9) {
            $quarter = 'C';
        } else {
            $quarter = 'D';
        }

        return "{$year}-{$quarter}";
    }
}

if (!function_exists('lastSeen')) {
    function lastSeen($dateTime)
    {
        if (empty($dateTime)) {
            return 'never';
        }
        $currentTime = new DateTime();
        $lastSeenTime = new DateTime($dateTime);
        $interval = $currentTime->diff($lastSeenTime);

        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        } elseif ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        } elseif ($interval->d > 0) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        } elseif ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'just now';
        }

    }
}


if (!function_exists('error_processor')) {
    function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;

    }
}


if (!function_exists('is_checked')) {
    function is_checked($category)
    {
        if (in_array($category->id, explode(',', request('category', '')))) {
            return 'checked';
        }
        return '';

    }
}
if (!function_exists('title_breaker')) {
    function title_breaker($title)
    {
        $words = explode(" ", trim($title));
        $result = "";
        $currentLine = "";

        foreach ($words as $word) {
            if (strlen($word) < 6 && strlen($currentLine) > 0) {
                $currentLine .= " " . $word;
            } else {
                if (strlen($currentLine) > 0) {
                    $result .= $currentLine . "<br>";
                }
                $currentLine = $word;
            }
        }

        $result .= $currentLine;

        return $result;


    }
}


if (!function_exists('first_name')) {
    function first_name($name)
    {
        return explode(" ", $name)[0] ?? '';
    }
}

if (!function_exists('status_icon')) {
    function status_icon($status)
    {
        if ($status == 'paid') {
            return "<span class='badge badge-success'>Paid</span>";
        } elseif ($status == 'pending') {
            return "<span class='badge badge-warning'>Pending</span>";
        } else {
            return "<span class='badge badge-danger'>Declined</span>";
        }
    }
}

if (!function_exists('truncate_text')) {
    function truncate_text($text, $chars = 120)
    {
        return Str::limit(strip_tags($text), $chars);
    }
}

if (!function_exists('sanitize_filename')) {
    function sanitize_filename($filename)
    {
        return preg_replace('/[^a-zA-Z0-9-_\.]/', '', $filename);
    }
}

if (!function_exists('extract_youtube_id')) {
    function extract_youtube_id($url)
    {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }
}

if (!function_exists('is_json')) {
    function is_json($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('minify_html')) {
    function minify_html($html)
    {
        return preg_replace('/\s+/', ' ', trim($html));
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('generate_slug')) {
    function generate_slug($string)
    {
        return Str::slug($string);
    }
}

if (!function_exists('generate_random_string')) {
    function generate_random_string($length = 10)
    {
        return Str::random($length);
    }
}

if (!function_exists('get_current_timestamp')) {
    function get_current_timestamp()
    {
        return Carbon::now()->timestamp;
    }
}

if (!function_exists('human_date')) {
    function human_date($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp)->diffForHumans();
    }
}

if (!function_exists('full_date')) {
    function full_date($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp)->toDayDateTimeString();
    }
}

if (!function_exists('send_http_post')) {
    function send_http_post($url, $data = [])
    {
        return Http::post($url, $data)->body();
    }
}

if (!function_exists('get_mime_type')) {
    function get_mime_type($filePath)
    {
        return Storage::mimeType($filePath);
    }
}

if (!function_exists('read_json_file')) {
    function read_json_file($path)
    {
        if (Storage::exists($path)) {
            return json_decode(Storage::get($path), true);
        }
        return [];
    }
}

if (!function_exists('write_json_file')) {
    function write_json_file($path, $data)
    {
        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
    }
}

if (!function_exists('remove_special_chars')) {
    function remove_special_chars($text)
    {
        return preg_replace('/[^A-Za-z0-9\- ]/', '', $text);
    }
}

if (!function_exists('format_phone')) {
    function format_phone($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}

if (!function_exists('generate_otp')) {
    function generate_otp($length = 6)
    {
        return rand(pow(10, $length - 1), pow(10, $length) - 1);
    }
}

if (!function_exists('days_ago')) {
    function days_ago($date)
    {
        return Carbon::parse($date)->diffInDays(Carbon::now());
    }
}

if (!function_exists('short_number')) {
    function short_number($n)
    {
        if ($n < 1000)
            return $n;
        $suffix = ['K', 'M', 'B', 'T'];
        $i = floor(log($n, 1000));
        return round($n / pow(1000, $i), 1) . $suffix[$i - 1];
    }
}

if (!function_exists('parse_domain')) {
    function parse_domain($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        return preg_replace('/^www\./', '', $host);
    }
}

if (!function_exists('nice_time')) {
    function nice_time($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp)->format('g:i A');
    }
}

if (!function_exists('nice_date')) {
    function nice_date($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp)->format('F j, Y');
    }
}
if (!function_exists('cakeTime')) {
    function cakeTime($time, $replaceAgo = false)
    {
        $carbonTime = Carbon::parse($time);
        $timeAgo = $carbonTime->diffForHumans();

        return $replaceAgo ? trim(str_replace("ago", "", $timeAgo)) : $timeAgo;
    }
}
if (!function_exists('getNotificationBackground')) {
    function getNotificationBackground($notification_type_id)
    {
        if ($notification_type_id == 1) { //info
            return 'bg-info';
        }
        if ($notification_type_id == 2) { //success
            return 'bg-green';
        }
        if ($notification_type_id == 3) { //warning
            return 'bg-orange';
        }
        if ($notification_type_id == 4) { //error
            return 'bg-red';
        }

        return 'bg-info';
    }
}

if (!function_exists('getNotificationIcon')) {
    function getNotificationIcon($notification_type_id)
    {
        if ($notification_type_id == 1) { //info
            return '<i class="fa fa-info-circle"></i>';
        }
        if ($notification_type_id == 2) { //success
            return '<i class="fa fa-check"></i>';
        }
        if ($notification_type_id == 3) { //warning
            return '<i class="fa fa-exclamation-triangle"></i>';
        }
        if ($notification_type_id == 4) { //error
            return '<i class="fa fa-exclamation-triangle"></i>';
        }

        return '<i class="fa fa-info-circle"></i>';
    }

}

if (!function_exists('days_between')) {
    function days_between($start, $end)
    {
        return Carbon::parse($start)->diffInDays(Carbon::parse($end));
    }
}
if (!function_exists('arrayChangeKeyCaseRecursive')) {
    function arrayChangeKeyCaseRecursive($arr, $case = CASE_LOWER)
    {
        return array_map(function ($item) use ($case) {
            if (is_array($item))
                $item = arrayChangeKeyCaseRecursive($item, $case);
            return $item;
        }, array_change_key_case($arr, $case));
    }
}
if (!function_exists('getUserText')) {
    function getUserText($currentUser, $activeSub = null)
    {
        return !empty($activeSub) ? $activeSub->plan->name : ($currentUser->elite ? "Elite" : "Free");
    }
}
if (!function_exists('endsWith')) {
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return $length === 0 || (substr($haystack, -$length) === $needle);
    }
}

if (!function_exists('singularise')) {
    function singularise($word, $count = 1)
    {
        if ($count < 2) {
            if (endsWith($word, "ies")) {
                $word = str_lreplace("ies", "y", $word);
            } elseif (endsWith($word, "s")) {
                $word = str_lreplace("s", "", $word);
            }
        }
        return $word;
    }
}




if (!function_exists('isJson')) {
    function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;

    }
}



if (!function_exists('getBadge')) {
    function getBadge($currentUser, $activeSub = null)
    {
        if ($currentUser->elite) {
            return "diamond.png";
        }

        if (!empty($activeSub)) {
            if ($activeSub->plan_id == 1) {
                return "gold.png";
            }
            if ($activeSub->plan_id == 2) {
                return "diamond.png";
            }
        }
        return "user.png";
    }

}
