<?php
session_start();
session_unset();
session_destroy();

// Also clear cookies
setcookie("user_id", "", time() - 3600, "/");
setcookie("fullname", "", time() - 3600, "/");
setcookie("is_admin", "", time() - 3600, "/");

header("Location: index.php");
exit;
