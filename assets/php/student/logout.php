<?php
session_start();
session_unset();
session_destroy();

// Redirect with JavaScript to avoid ERR_CACHE_MISS
echo "<script>
    window.location.href = '../student_login.php';
</script>";
exit();
?>