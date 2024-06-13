<?php
if (isset($_COOKIE['UserEmail'])) {
    echo json_encode('true');
} else {
    echo json_encode('false');
}
?>
