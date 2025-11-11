<?php

class DateUtils
{
    public static function daysFromNow(string $pastDate): int
    {
        $past = new DateTime($pastDate);
        $now = new DateTime();

        if ($past > $now) {
            return 0;
        }

        return $now->diff($past)->days;
    }

    public static function formatPrettyDate(string $datetime): string
    {
        $d = new DateTime($datetime);
        return $d->format('l, F j, Y');
    }

}
