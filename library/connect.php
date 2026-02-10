<?php
require_once('../include/db_define.php');

mysql_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die ("Connect Error");
mysql_select_db(DB_NAME);
mysql_query("set names 'utf8'");
?>