<?php
// Define the PHP array with values and labels
$options = array(
    "--" => "-- Common Settings --",
    "*" => "Every Day (*)",
    "*/2" => "Every Other Day (*/2)",
    "*/3" => "Every Third Day (*/3)",
    "1,15" => "On the 1st and 15th of the Month (1,15)",
    "--" => "-- Days --"
);

for ($i = 1; $i <= 31; $i++) {
    $ordinal = $i . 'th';
    if ($i == 1) {
        $ordinal = $i . 'st';
    } elseif ($i == 2) {
        $ordinal = $i . 'nd';
    } elseif ($i == 3) {
        $ordinal = $i . 'rd';
    }
    $label = $ordinal . ' (' . $i . ')';
    $options[$i] = $label;
}

// Define the selected day value

// if not  isset $dayOfMonth
if (!isset($dayOfMonth)) {
    $dayOfMonth = '--';
}

// Generate the HTML select element
echo '<select id="dayOfMonth" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">';
foreach ($options as $value => $label) {
    $selected = ($value == $dayOfMonth) ? 'selected' : '';
    echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
}
echo '</select>';
?>
