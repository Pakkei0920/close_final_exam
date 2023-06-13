<?php
// 獲取POST請求中的數值
$value = $_POST['value'];

// 保存數值到檔
$file = fopen('data.txt', 'w');
fwrite($file, $value);
fclose($file);

?>
