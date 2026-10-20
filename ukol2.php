<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Prague');

function format_youtube_url($url){
    return str_replace('watch?v=', 'embed/', $url);
}

function get_working_days($from, $to) {
    $workingDays = [];
    $lastDay = strtotime($to);

    for ($day = strtotime($from); $day <= $lastDay; $day = strtotime('+1 day', $day)) {
        // Pondělí je 1, neděle 7
        if (date('N', $day) <= 5) {
            $workingDays[] = date('Y-m-d', $day);
        }
    }

    return $workingDays;
}

$youtubeUrls = [
    'https://www.youtube.com/watch?v=Gl_Dnf_aGfQ',
    'https://www.youtube.com/watch?v=jNQXAC9IVRw',
    'https://www.youtube.com/watch?v=9bZkp7q19f0',
];

$intervals = [
    ['2026-10-15', '2026-10-22'],
    ['2026-10-01', '2026-10-30'],
    ['2026-11-15', '2026-12-04'],
    ['2026-10-17', '2026-10-18'],
];
?>
<html lang="cs">
<head>
<meta charset="utf-8">
<title>Úkol 2</title>
</head>
<body>

<h1>Úkol 2</h1>

<?php
echo '<h2>YouTube URL</h2>';

foreach ($youtubeUrls as $url) {
    echo $url . '<br>';
    echo format_youtube_url($url) . '<br><br>';
}

echo '<h2>Pracovní dny</h2>';

foreach ($intervals as $interval) {
    echo '<h3>' . $interval[0] . ' - ' . $interval[1] . '</h3>';
    echo '<pre>';
    print_r(get_working_days($interval[0], $interval[1]));
    echo '</pre>';
}
?>

</body>
</html>
