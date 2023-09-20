<?php
// Define the PHP array with values and labels
$options = array(
    "--" => "-- Common Settings --",
    "*" => "Every Hour (*)",
    "*/2" => "Every Other Hour (*/2)",
    "*/3" => "Every Third Hour (*/3)",
    "*/4" => "Every Fourth Hour (*/4)",
    "*/6" => "Every Sixth Hour (*/6)",
    "*/8" => "Every Eighth Hour (*/8)",
    "0,12" => "Every Twelve Hours (0,12)",
    "--" => "-- Hours --"
);

for ($i = 0; $i < 24; $i++) {
    $label = sprintf("%d:00 %s (%d)", ($i % 12 == 0) ? 12 : $i % 12, ($i < 12) ? 'a.m.' : 'p.m.', $i);
    $options[$i] = $label;
}

// Define the selected hour value

// if  not isset $hour
if (!isset($hour)) {
    $hour = '--';
}

// Generate the HTML select element
echo '<select id="hour" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " >';
foreach ($options as $value => $label) {
    $selected = ($value == $hour) ? 'selected' : '';
    echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
}
echo '</select>';
?>
