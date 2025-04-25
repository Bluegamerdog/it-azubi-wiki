<?php

require_once 'database.php';


function loadEnv($path)
{
    if (!file_exists($path))
        return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#'))
            continue; // Skip comments

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Strip optional surrounding quotes
        $value = trim($value, '"\'');

        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

function time_ago($timestamp)
{
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;

    // Break down the difference into smaller units
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);  // Roughly 30 days
    $years = round($seconds / 31553280); // Roughly 365.25 days

    // Return the human-readable format based on the time difference
    if ($seconds <= 60) {
        return ($seconds == 1) ? "Vor einer Sekunde" : "Vor $seconds Sekunden";
    } else if ($minutes <= 60) {
        return ($minutes == 1) ? "Vor einer Minute" : "Vor $minutes Minuten";
    } else if ($hours <= 24) {
        return ($hours == 1) ? "Vor einer Stunde" : "Vor $hours Stunden";
    } else if ($days <= 7) {
        return ($days == 1) ? "Gestern" : "Vor $days Tagen";
    } else if ($weeks <= 4.3) {  // 4.3 weeks = 30 days
        return ($weeks == 1) ? "Vor einer Woche" : "Vor $weeks Wochen";
    } else if ($months <= 12) {
        return ($months == 1) ? "Vor einem Monat" : "Vor $months Monaten";
    } else {
        return ($years == 1) ? "Vor einem Jahr" : "Vor $years Jahren";
    }
}

