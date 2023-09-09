<!DOCTYPE html>
<html>
<head>
    <title>Timezone Dropdown</title>
</head>
<body>

<select id="timezoneDropdown">
    <?php
    function generateTimezoneOptions() {
        $timezones = timezone_identifiers_list();
        $output = '';
        foreach ($timezones as $timezone) {
            $output .= "<option value=\"$timezone\">$timezone</option>";
        }
        return $output;
    }
    
    echo generateTimezoneOptions();
    ?>
</select>

<script>
// Function to automatically select the user's timezone
function autoSelectUserTimezone() {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const timezoneMapping = {
        'Asia/Calcutta': 'Asia/Kolkata', // Map 'Asia/Calcutta' to 'Asia/Kolkata'
        // Add more mappings as needed
    };

    const mappedTimezone = timezoneMapping[userTimezone] || userTimezone;

    const dropdown = document.getElementById('timezoneDropdown');
    for (let i = 0; i < dropdown.options.length; i++) {
        if (dropdown.options[i].value === mappedTimezone) {
            dropdown.selectedIndex = i;
            break;
        }
    }
}

// Call the function to auto-select the user's timezone on page load
window.onload = autoSelectUserTimezone;
</script>

</body>
</html>
