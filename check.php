<?php
// Check if the exec function is enabled
if (function_exists('exec')) {
    echo 'exec function is enabled.<br><br>';

    // Possible paths to mysqldump executable
    $possiblePaths = array(
        '/usr/bin/mysqldump',
        '/usr/local/bin/mysqldump',
        'C:\xampp\mysql\bin\mysqldump.exe', // Example path for Windows
        // Add more paths as needed
    );

    // Check each path for mysqldump availability
    foreach ($possiblePaths as $path) {
        $command = $path . ' --version';
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            echo "mysqldump found at: {$path}<br>";
            echo "mysqldump version: {$output[0]}<br><br>";
        } else {
            echo "mysqldump not found at: {$path}<br><br>";
        }
    }
} else {
    echo 'exec function is not enabled.';
}
?>




