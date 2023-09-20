<?php
// Define the PHP array with values and labels
$options = array(
    "--" => "-- Common Settings --",
    "*" => "Every Month (*)",
    "*/2" => "Every Other Month (*/2)",
    "*/4" => "Every Third Month (*/4)",
    "1,7" => "Every Six Months (1,7)",
    "--" => "-- Months --"
);

$months = array(
    1 => "January",
    2 => "February",
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "August",
    9 => "September",
    10 => "October",
    11 => "November",
    12 => "December"
);

// Define the selected month value

// if not isset $month
if (!isset($month)) {
    $month = '--';
}

// Generate the HTML select element
echo '<select id="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " >';
foreach ($options as $value => $label) {
    $selected = ($value == $month) ? 'selected' : '';
    echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
}
foreach ($months as $value => $label) {
    $selected = ($value == $month) ? 'selected' : '';
    echo '<option value="' . $value . '" ' . $selected . '>' . $label . ' (' . $value . ')</option>';
}
echo '</select>';
?>
