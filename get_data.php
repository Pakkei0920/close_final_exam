<?php
$file = 'data.txt';
$data = file_get_contents($file);
$value = floatval($data) * 3.3 / 4095.0;
echo $value;
?>
