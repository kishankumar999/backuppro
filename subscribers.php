<?php
        // Function to read subscribers from JSON file
        function readSubscribers() {
            $file = 'subscribers.json';
            if (file_exists($file)) {
                $jsonContent = file_get_contents($file);
                $subscribers = json_decode($jsonContent, true);
                if ($subscribers !== null) {
                    return $subscribers;
                }
            }
            return [];
        }

        // Function to save subscribers to JSON file
        function saveSubscribers($subscribers) {
            $file = 'subscribers.json';
            $jsonContent = json_encode($subscribers, JSON_PRETTY_PRINT);
            file_put_contents($file, $jsonContent);
        }

        // Function to validate an email address
        function validateEmail($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';

            if (!empty($name) && !empty($email) && validateEmail($email)) {
                $subscribers = readSubscribers();
                $subscriber = ['name' => $name, 'email' => $email];
                $subscribers[] = $subscriber;
                saveSubscribers($subscribers);
            }
        }

        // Handle delete action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
            $deleteIndex = $_POST['delete'];

            $subscribers = readSubscribers();
            if (isset($subscribers[$deleteIndex])) {
                unset($subscribers[$deleteIndex]);
                $subscribers = array_values($subscribers); // Reset array keys
                saveSubscribers($subscribers);
            }
        }

        // Handle update action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $updateIndex = $_POST['update'];
            $newName = $_POST['newName'] ?? '';
            $newEmail = $_POST['newEmail'] ?? '';

            if (!empty($newName) && !empty($newEmail) && validateEmail($newEmail)) {
                $subscribers = readSubscribers();
                if (isset($subscribers[$updateIndex])) {
                    $subscribers[$updateIndex]['name'] = $newName;
                    $subscribers[$updateIndex]['email'] = $newEmail;
                    saveSubscribers($subscribers);
                }
            }
        }

        // Display subscribers
        $subscribers = readSubscribers();
        ?>
<!DOCTYPE html>
<html lang="en">
<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscriber Management</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>
<body class="bg-gray-50">
    <?php include 'email_notification_tabs.php'; ?>
    <div class="container mx-auto max-w-xl bg-white px-10 pt-10 pb-8 shadow-xl my-10 rounded-lg ring-1 ring-gray-900/5 ">
      
        <!--  include email tabs -->
        <h1 class="text-xl font-bold mb-8">Email Confirmation Subscribers</h1>
  <!-- Subscriber List -->
  <?php
        if (count($subscribers) != 0) {
            
        
        ?>
        
        
        <table class="w-full my-10 table-auto">
            <thead>
                <tr class="border-b border-gray-500 ">
                    <th class="text-left font-semibold p-3 ">Subscriber</th>
        
                    <th class="text-left font-semibold p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $index => $subscriber): ?>
                    <tr class="border-b border-gray-200 ">
                        <td class="p-3">
                        <div class="flex flex-col gap-1">
                            <div class="text-xl font-semibold"><?php echo ucfirst( $subscriber['name']); ?></div>
                            <?php echo $subscriber['email']; ?>
                        </div>    
                        </td>

                       
                        <td class="p-3">
                            <form method="POST" class="inline-block">
                                <input type="hidden" name="delete" value="<?php echo $index; ?>">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-2 py-1 rounded">Delete</button>
                            </form>
                            <button onclick="openUpdateModal('<?php echo $index; ?>', '<?php echo $subscriber['name']; ?>', '<?php echo $subscriber['email']; ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-2 py-1 rounded">Update</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php } ?>
        
        <div class="text-lg font-bold">Add a new Subscriber</div>
        <!-- Subscriber Form -->
        <form method="POST" class="my-10">
            <div class="grid gap-5 mb-2 grid-cols-4">
                <label for="name" class="text-lg font-semibold">Name</label>
                <input type="text" name="name" id="name" class="flex-1 col-span-3 border border-gray-300 p-2">
           
                <label for="email" class="text-lg font-semibold">Email</label>
                <input type="email" name="email" id="email" class="col-span-3  flex-1 border border-gray-300 p-2">
            </div>
            <button type="submit" class="w-full mt-5 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold px-4 py-2 rounded">Add Subscriber</button>
        </form>

      

        <!-- Update Modal -->
        <div id="updateModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white w-1/3 rounded shadow-lg p-4">
                    <h2 class="text-xl font-semibold mb-4">Update Subscriber</h2>
                    <form method="POST">
                        <input type="hidden" id="updateIndex" name="update" value="">
                        <div class="flex flex-col mb-2">
                            <label for="newName" class="text-lg font-semibold">New Name</label>
                            <input type="text" id="newName" name="newName" class="border border-gray-300 p-2">
                        </div>
                        <div class="flex flex-col mb-2">
                            <label for="newEmail" class="text-lg font-semibold">New Email</label>
                            <input type="email" id="newEmail" name="newEmail" class="border border-gray-300 p-2">
                        </div>
                        <div class="flex gap-2 my-10">
                            <button type="submit" class=" bg-indigo-500 hover:bg-indigo-600 text-white font-semibold px-4 py-2 rounded">Update</button>
                            <button type="button" onclick="closeUpdateModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        // Function to open the update modal with pre-filled values
        function openUpdateModal(index, name, email) {
            document.getElementById('updateIndex').value = index;
            document.getElementById('newName').value = name;
            document.getElementById('newEmail').value = email;
            document.getElementById('updateModal').classList.remove('hidden');
        }

        // Function to close the update modal
        function closeUpdateModal() {
            document.getElementById('updateModal').classList.add('hidden');
        }
        </script>
    </div>
</body>
</html>
