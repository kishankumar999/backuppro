<a href="dashboard.php" class="m-2 block text-blue-500 font-semibold">
  <!-- back long arrow -->
  <svg class="inline-block w-4 h-4 mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20">
    <path fill-rule="evenodd" d="M7.293 5.293a1 1 0 0 1 0 1.414L4.414 10H16a1 1 0 1 1 0 2H4.414l2.879 2.293a1 1 0 1 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4a1 1 0 0 1 1.414 0z" clip-rule="evenodd"></path>
  </svg>
  Back to Dashboard</a>
<div class="max-w-xl mx-auto">

  <div class="flex justify-center mb-3 gap-2 items-center">
    <div>
      <!-- email icon -->
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-envelope-at" viewBox="0 0 16 16">
  <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2H2Zm3.708 6.208L1 11.105V5.383l4.708 2.825ZM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2Z"/>
  <path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648Zm-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z"/>
</svg>

    </div>
    <div class="text-bold text-2xl text-gray-500">Email Confirmation </div>
  </div>
  <?php
  // JSON structure containing tab data
  $tabs = '[
        {"label": "Enable/Disable ", "page": "enable_disable_email_notification.php"},
          {"label": "Subscribers", "page": "subscribers.php"},
          {"label": "Email Template", "page": "notifications.php"}
        ]';

  // Decoding the JSON string into an array
  $tabsArray = json_decode($tabs, true);

  // Get the current PHP page filename
  $currentPage = basename($_SERVER['PHP_SELF']);

  // Loop through the tab array and create the tab headers
  echo '<div class="text-lg font-medium text-center text-gray-500 border-b border-gray-100 dark:text-gray-500 dark:border-gray-200">';
  echo '<ul class="flex flex-wrap -mb-px justify-center">';
  foreach ($tabsArray as $tab) {
    $isActive = ($currentPage === $tab['page']) ? 'text-blue-600 border-blue-600 active dark:text-blue-600 dark:border-blue-600' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-500';
    $isDisabled = isset($tab['disabled']) && $tab['disabled'] ? 'text-gray-400 cursor-not-allowed dark:text-gray-500' : '';
    echo '<li class="mr-2">';
    echo '<a href="' . $tab['page'] . '" class="inline-block p-4 border-b-4 border-transparent rounded-t-lg ' . $isActive . ' ' . $isDisabled . '" aria-current="page">' . $tab['label'] . '</a>';
    echo '</li>';
  }
  echo '</ul>';
  echo '</div>';
  ?>
</div>