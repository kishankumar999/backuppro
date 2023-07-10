<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <style>
        .bg-gray-50 {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'email_notification_tabs.php'; ?>
    <div class="container mx-auto px-4 py-8 max-w-xl bg-white">
        <h1 class="text-2xl font-bold mb-4">Email Template</h1>

        <?php
        $templateFile = 'email.html';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject = $_POST['subject'];
            $template = $_POST['template'];
            // add subject line to the template
            $template = "<subject>$subject</subject>\n\n" . $template;

            if (!file_exists($templateFile)) {
                file_put_contents($templateFile, $template);
                echo '<div class="bg-green-100 text-green-900 py-2 px-4 rounded mb-4">Template saved successfully!</div>';
            } else {
                // Save/update the template
                file_put_contents($templateFile, $template);
                echo '<div class="bg-green-100 text-green-900 py-2 px-4 rounded mb-4">Template saved successfully!</div>';
            }
        }

        if (!file_exists($templateFile)) {
            // Create a sample email template if it doesn't exist
            $defaultSubject = 'Backup Notification';
            $defaultTemplate = '<p>Dear {name},</p>
<p>We are pleased to inform you that a backup of your files has been created.</p>
<p>Backup details:</p>
<ul>
    <li>Email: {email}</li>
    <li>Backup Time: {backup-time}</li>
    <li>Backup Location: {backup-location}</li>
    <li>Backup Size: {backup-size}</li>
</ul>
<p>Thank you,</p>
<p>Your Company</p>';
            // add subject line to the template
            $defaultTemplate = "<subject>$defaultSubject</subject>\n\n" . $defaultTemplate;

            file_put_contents($templateFile, $defaultTemplate);
        } else {
            // Read the subject line from the template file
            $templateContent = file_get_contents($templateFile);
            preg_match('/<subject>(.*?)<\/subject>/s', $templateContent, $matches);
            $defaultSubject = isset($matches[1]) ? $matches[1] : 'Backup Notification';

            // Remove the subject line from the template
        }
        
        $templateContent = file_get_contents($templateFile);
        $templateContent = preg_replace('/<subject>(.*?)<\/subject>/s', '', $templateContent);

        // remove empty lines
        $templateContent = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $templateContent);
        ?>

        <form method="POST">
            <label for="subject" class="block mb-2 font-bold">Subject Line</label>
            <input type="text" id="subject" name="subject" class="w-full border-gray-300 rounded-md p-2 mb-4" value="<?php echo htmlspecialchars($defaultSubject); ?>" placeholder="Enter the subject line..." required>

            <label for="template" class="block mb-2 font-bold">Email HTML Template</label>
            <textarea id="template" name="template" class="w-full h-64 border-gray-300 rounded-md p-2 mb-4" placeholder="Enter your email template..."><?php echo htmlspecialchars($templateContent); ?></textarea>
            <details class="mt-4">
            <summary class="font-bold cursor-pointer py-1 px-2 bg-green-100 mb-5">Placeholders you can use</summary>
            <ul class="ml-4 mt-2 list-disc">
                <li>{name}</li>
                <li>{email}</li>
                <li>{backup-time}</li>
                <li>{backup-location}</li>
                <li>{backup-size}</li>
            </ul>
        </details>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        </form>

      
    </div>
</body>
</html>
