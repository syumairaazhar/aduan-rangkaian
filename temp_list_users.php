<?php
include('db.php');
$res = $conn->query('SELECT id_number, role, name, status FROM users');
echo "<pre>";
while($r = $res->fetch_assoc()) {
    print_r($r);
}
echo "</pre>";
?>
