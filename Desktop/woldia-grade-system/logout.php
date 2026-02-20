<?php
session_start();

// Destroy all session data
session_destroy();

// Go back to login with message
header("Location: index.php?logout=1");
exit();
?>