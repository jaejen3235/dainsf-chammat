<?php
$pwd = 'dainlab';
$pwd = password_hash($pwd, PASSWORD_DEFAULT);
echo $pwd;
?>