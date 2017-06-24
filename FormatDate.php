<?php

class FormatDate {
    function parseDate($str) {
        if (empty($str)) return false;
        return strToTime($str);
    }
    
    function dbDate($date = '') {
        $date = $this->parseDate($date);
        if (!$date) $date = time();
        return date('Y-m-d h:i:s', $date);
    }
    
    function defaultDate($date = '') {
        $date = $this->parseDate($date);
        if (!$date) $date = time();
        return date('d.m.Y h:i:s', $date);
    }
    
    function humanDate($date = '') {
        $date = $this->parseDate($date);
        if (!$date) $date = time();
        $diff = time() - $date;
        switch (true) {
            case ($diff<3*60):           return 'gerade eben';
            case ($diff<25*60):          return 'vor '.round($diff/60).' Minuten';
            case ($diff<35*60):          return 'vor einer halben Stunde';
            case ($diff<55*60):          return 'vor einer Stunde';
            case ($diff<85*60):          return 'vor eineinhalb Stunden';
            case ($diff<23*60*60):       return 'vor '.round($diff/60/60).' Stunden';
            case ($diff<2*24*60*60):     return 'vor einem Tag';
            case ($diff<7*24*60*60):     return 'vor einer Woche';
            case ($diff<30*24*60*60):    return 'vor '.round($diff/60/60/24/7).' Wochen';
            case ($diff<2*30*24*60*60):  return 'vor einem Monat';
            case ($diff<12*30*24*60*60): return 'vor '.round($diff/60/60/30).' Monaten';
            default:                     return 'vor mehr als einem Jahr';
        }
    }
    
}

?>