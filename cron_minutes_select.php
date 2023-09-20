<?php
// Define the PHP array with values and labels
$options = array(
    "--" => "-- Common Settings --",
    "*" => "Once Per Minute(*)",
    "*/2" => "Once Per Two Minutes(*/2)",
    "*/5" => "Once Per Five Minutes(*/5)",
    "*/10" => "Once Per Ten Minutes(*/10)",
    "*/15" => "Once Per Fifteen Minutes(*/15)",
    "0,30" => "Once Per Thirty Minutes(0,30)",
    "--" => "-- Minutes --"
);

for ($i = 0; $i < 60; $i++) {
    $label = sprintf(":%02d (%d)", $i, $i);
    $options[$i] = $label;
}

// if not isset $minute
if (!isset($minute)) {
    $minute = '--';
}

// Generate the HTML select element
echo '<select  id="minute"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " >';
foreach ($options as $value => $label) {
    $selected = ($value == $minute) ? 'selected' : '';
    echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
}
echo '</select>';
?>