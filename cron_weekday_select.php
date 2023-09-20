<?php
// Define the PHP array with values and labels
$options = array(
    "--" => "-- Common Settings --",
    "*" => "Every Day (*)",
    "1-5" => "Every Weekday (1-5)",
    "0,6" => "Every Weekend Day (6,0)",
    "1,3,5" => "Every Monday, Wednesday, and Friday (1,3,5)",
    "2,4" => "Every Tuesday and Thursday (2,4)",
    "--" => "-- Weekdays --"
);

$days = array(
    0 => "Sunday",
    1 => "Monday",
    2 => "Tuesday",
    3 => "Wednesday",
    4 => "Thursday",
    5 => "Friday",
    6 => "Saturday"
);

// Define the selected weekday value


// if not isset $weekday
if (!isset($dayOfWeek)) {
    $dayOfWeek = '--';
}
// Generate the HTML select element
echo '<select id="dayOfWeek" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">';
foreach ($options as $value => $label) {
    $selected = ($value ==  $dayOfWeek) ? 'selected' : '';
    echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
}
foreach ($days as $value => $label) {
    $selected = ($value ==  $dayOfWeek) ? 'selected' : '';
    echo '<option value="' . $value . '" ' . $selected . '>' . $label . ' (' . $value . ')</option>';
}
echo '</select>';
?>
