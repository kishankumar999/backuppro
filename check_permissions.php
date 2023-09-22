<?php
// File and folder paths
$files_and_folders = [
    'backup.php' => 'backup.php',
    'backup_drive.php' => 'backup_drive.php',
    'upload folder' => 'uploads/',
    'primary folder' => __DIR__,
];

// Function to check and display permission messages
function checkPermissions($path, $label)
{
    return is_writable($path);
}


// Function to check if any permission is not okay
function anyPermissionNotOk()
{
    global $files_and_folders;
    foreach ($files_and_folders as $label => $path) {
        if (!checkPermissions($path, $label)) {
            return true;
        }
    }
    return false;
}
// Function to display permission check results and instructions
function displayPermissionCheck()
{
    global $files_and_folders;
?>

    <div class="bg-white mb-8 p-8">
        <h1 class="text-2xl font-bold mb-2">File Permissions Check Failed</h1>
        <p class="mb-7">Please fix the following File Permissions before you proceed</p>
        <table class="table-auto">
            <thead>
                <tr>
                    <th class="px-4 py-2">File/Folder</th>
                    <th class="px-4 py-2">Permission Check</th>
                    <th class="px-4 py-2">Instructions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files_and_folders as $label => $path) : ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo $label; ?></td>
                        <td class="border px-4 py-2">
                            <?php
                            if (checkPermissions($path, $label)) {
                                echo '<span class="text-green-500">OK</span>';
                            } else {
                                echo '<span class="text-red-500">Warning</span>';
                            }
                            ?>
                        </td>
                        <td class="border px-4 py-2">
                            <?php
                            if (is_dir($path)) {
                                echo 'To set permissions, use the following command:<br>';
                                echo '<code>chmod -R 755 ' . $path . '</code>';
                            } else {
                                echo 'To set permissions, use the following command:<br>';
                                echo '<code>chmod 644 ' . $path . '</code>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php
}
if (anyPermissionNotOk()) {

    displayPermissionCheck();
}
?>