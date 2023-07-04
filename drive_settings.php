<?php
include 'validate_login.php';

// Function to update config.php file with the provided backup file name template
function updateConfig($backupFileName, $uploadToFolder, $folderName)
{
    $config = include 'config.php';
    $config['backup_file_name'] = $backupFileName;
    $config['upload_to_folder'] = $uploadToFolder;
    $config['folder_name'] = $folderName;

    $content = "<?php\n\n";
    $content .= "return " . var_export($config, true) . ";";

    file_put_contents('config.php', $content);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $backupFileName = $_POST['backup_file_name'];
    $uploadToFolder = isset($_POST['upload_to_folder']) ? true : false;
    $folderName = $uploadToFolder ? $_POST['folder_name'] : '';
    updateConfig($backupFileName, $uploadToFolder, $folderName);
    $_SESSION['success_message'] = 'Backup file name template saved successfully.';
} else {
    $config = include 'config.php';
    if (isset($config['backup_file_name'])) {
        $backupFileName = $config['backup_file_name'];
    } else {
        $backupFileName = '';
    }
    $uploadToFolder = isset($config['upload_to_folder']) ? $config['upload_to_folder'] : false;
    $folderName = isset($config['folder_name']) ? $config['folder_name'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <title>Backup File Name Configuration</title>
    <script>
        function updatePreview() {
            const currentDate = (new Date()).toISOString().slice(0, 10);
            const currentTime = (new Date()).toLocaleTimeString().replace(/:/g, '-').replace(/\s/g, '');
            const databaseName = "<?php $config = include 'config.php'; echo $config['db_name']; ?>";
            const template = document.getElementById('backup_file_name').value;
            const folderName = document.getElementById('folder_name').value;

            let preview = template
                .replace('{date}', currentDate)
                .replace('{time}', currentTime)
                .replace('{database_name}', databaseName);

            if (folderName.trim() !== '' && document.getElementById('upload_to_folder').checked) {
                preview = folderName + '/' + preview;
            }

            const previewBox = document.getElementById('preview');
            const previewLabel = document.getElementById('preview_label');

            if (template.trim() === '') {
                previewBox.style.display = 'none';
                previewLabel.style.display = 'none';
            } else {
                previewBox.style.display = 'block';
                previewLabel.style.display = 'block';
                previewBox.textContent = preview + '.zip';
            }
        }

        function toggleFolderInput() {
            const folderNameInput = document.getElementById('folder_name');
            const folderNameLabel = document.getElementById('folder_name_label');
            const uploadToFolderCheckbox = document.getElementById('upload_to_folder');

            folderNameInput.style.display = uploadToFolderCheckbox.checked ? 'block' : 'none';
            folderNameLabel.style.display = uploadToFolderCheckbox.checked ? 'block' : 'none';
            updatePreview();
        }
    </script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto my-10 px-4 py-8 bg-white rounded shadow-xl max-w-xl">
        <a href="dashboard.php" class="mb-4 block text-blue-500 font-semibold">Back to Dashboard</a>
        <form method="POST">
            <div class="mb-4">
                <label for="backup_file_name" class="block font-bold mb-1">Backup File Name Template:</label>
                <input type="text" name="backup_file_name" id="backup_file_name" class="w-full px-3 py-2 border rounded" placeholder="Enter the backup file name template with placeholders like {date}, {time}, {database_name}" required value="<?php echo $backupFileName; ?>" oninput="updatePreview()">
            </div>
            <div class="mb-2">
                <span class="text-sm font-semibold">Available Placeholders:</span>
                <ul class="list-disc ml-6 mt-1">
                    <li><span class="text-xs text-gray-600">{date}</span> - Current date in the format YYYY-MM-DD</li>
                    <li><span class="text-xs text-gray-600">{time}</span> - Current time in the format HH-MM-SS</li>
                    <li><span class="text-xs text-gray-600">{database_name}</span> - Placeholder for the database name</li>
                </ul>
            </div>
            <div class="mb-4">
                <label for="upload_to_folder" class="inline-flex items-center">
                    <input type="checkbox" name="upload_to_folder" id="upload_to_folder" class="mr-2" onchange="toggleFolderInput()" <?php echo $uploadToFolder ? 'checked' : ''; ?>>
                    <span class="font-semibold">Upload to a Folder</span>
                </label>
            </div>
            <div class="mb-4">
                <label for="folder_name" id="folder_name_label" class="block font-bold mb-1 <?php echo $uploadToFolder ? '' : 'hidden'; ?>">Folder Name:</label>
                <input type="text" name="folder_name" id="folder_name" class="w-full px-3 py-2 border rounded <?php echo $uploadToFolder ? '' : 'hidden'; ?>" placeholder="Enter the folder name" value="<?php echo $folderName; ?>" onchange="updatePreview()">
            </div>
            <div class="mb-4">
                <span class="text-sm font-semibold" id="preview_label" <?php echo empty($backupFileName) ? 'style="display: none;"' : ''; ?>>Backup File Preview:</span>
                <div id="preview" class="bg-gray-300 border border-gray-400 px-3 py-2 mt-1 rounded <?php echo empty($backupFileName) ? 'hidden' : ''; ?>"></div>
            </div>
            <?php if(isset($_SESSION['success_message'])): ?>
            <div class="mb-4">
                <div class="text-green-500"><?php echo $_SESSION['success_message']; ?></div>
            </div>
            <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            <div class="mb-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save Backup File Name Template</button>
            </div>
        </form>
    </div>

    <script>
        updatePreview();
    </script>
</body>

</html>