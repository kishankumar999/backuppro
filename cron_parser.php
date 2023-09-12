<?php

function modifyMinutes($currentMinute, $minutesToModify, $operation) {
    // Ensure $currentMinute is in the range [0-59]
    if ($currentMinute < 0 || $currentMinute > 59) {
        throw new InvalidArgumentException("Current minute must be in the range [0-59]");
    }
    
    if ($operation === '+') {
        // Add minutes
        $newMinute = ($currentMinute + $minutesToModify) % 60;
    } elseif ($operation === '-') {
        // Subtract minutes
        $newMinute = ($currentMinute - $minutesToModify + 60) % 60;
    } else {
        throw new InvalidArgumentException("Invalid operation. Use '+' or '-'");
    }
    
    return $newMinute;
}

function modifyHour($currentHour, $hoursToModify, $operation) {
    // Ensure $currentHour is in the range [0-23]
    if ($currentHour < 0 || $currentHour > 23) {
        throw new InvalidArgumentException("Current hour must be in the range [0-23]");
    }
    
    if ($operation === '+') {
        // Add hours
        $newHour = ($currentHour + $hoursToModify) % 24;
    } elseif ($operation === '-') {
        // Subtract hours
        $newHour = ($currentHour - $hoursToModify + 24) % 24;
    } else {
        throw new InvalidArgumentException("Invalid operation. Use '+' or '-'");
    }
    
    return $newHour;
}



class CronScheduler {
    private $currentDate;
    
    public function __construct() {
        $this->currentDate = new DateTime();
    }
    
    private function parseCronComponent($component, $maxValue) {
        if ($component === "*") {
            return range(0, $maxValue);
        }
        return array_map('intval', explode(",", $component));
    }
    
    public function generateNextTimes($minute, $hour, $dayOfMonth, $month, $dayOfWeek, $count) {
        $minutes = $this->parseCronComponent($minute, 59);
        $hours = $this->parseCronComponent($hour, 23);
        $daysOfMonth = $this->parseCronComponent($dayOfMonth, 31);
        $months = $this->parseCronComponent($month, 12);
        $daysOfWeek = $this->parseCronComponent($dayOfWeek, 7);

        $nextTimes = [];
        $nextTimeCandidate = new DateTime($this->currentDate->format('Y-m-d H:i:s'));

        while (count($nextTimes) < $count) {
            $nextTimeCandidate->setTime($nextTimeCandidate->format('H'), $nextTimeCandidate->format('i'), 0);

            while (
                !in_array((int)$nextTimeCandidate->format('i'), $minutes) ||
                !in_array((int)$nextTimeCandidate->format('H'), $hours) ||
                !in_array((int)$nextTimeCandidate->format('d'), $daysOfMonth) ||
                !in_array((int)$nextTimeCandidate->format('n'), $months) ||
                !in_array((int)$nextTimeCandidate->format('w'), $daysOfWeek)
            ) {
                $nextTimeCandidate->modify('+1 minute');
            }

            //

            $nextTimes[] = new DateTime($nextTimeCandidate->format('Y-m-d H:i:s '));
            $nextTimeCandidate->modify('+1 minute');
        }

        return $nextTimes;
    }

  
}

function getRelativeTime($originalDatetimeString) {
    // Convert the input datetime string to a DateTime object
    $originalDatetime = new DateTime($originalDatetimeString);

    // Current datetime
    $currentDatetime = new DateTime();

    // Check if the original datetime is in the future
    if ($originalDatetime > $currentDatetime) {
        $interval = $currentDatetime->diff($originalDatetime);

        // Format the relative time for the future
        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' from now';
        } elseif ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' from now';
        } elseif ($interval->d > 1) { // Check if more than 1 day difference
            return $interval->d . ' days from now';
        } elseif ($interval->d == 1) { // Check if exactly 1 day difference
            return 'Tomorrow';
        } elseif ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' from now';
        } elseif ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' from now';
        } else {
            return 'Just now';
        }
    } else {
        // Calculate the difference for the past
        $interval = $currentDatetime->diff($originalDatetime);

        // Format the relative time for the past
        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        } elseif ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        } elseif ($interval->d > 1) { // Check if more than 1 day difference
            return $interval->d . ' days ago';
        } elseif ($interval->d == 1) { // Check if exactly 1 day difference
            return 'Yesterday';
        } elseif ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }
}

// Example usage:
// $originalDatetimeString = '2023-09-10 14:30:00'; // Future date
// echo getRelativeTime($originalDatetimeString);

// Example usage:
// $originalDatetimeString = '2023-09-08 14:30:00';


// Example usage:

/*
$cronScheduler = new CronScheduler();
$nextTimes = $cronScheduler->generateNextTimes("0", "15", "*", "*", "*", 5);s
foreach($nextTimes as $nextTime) {
    echo date("l F j, Y, h:i A", $nextTime->getTimestamp()) . "\n";
    echo getRelativeTime($nextTime->format('Y-m-d H:i:s')) . "\n";
}
*/


// echo date("Y-m-d H:i:s", $nextTimes[0]->getTimestamp());
// $cronScheduler->renderNextTimes($nextTimes);
