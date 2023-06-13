# final_exam
使用XAMPP軟件，local/IP作為遊戲界面；
//apple.png及banana.png為遊戲圖示；
//save_data.php -> 接收ESP32的Post可變電阻的數值 -> 保存在data.txt;
//get_data.php -> 獲取data.txt * 3.3 / 4095.0;
//write_data.php -> 接收index.php碰到Line後的輸出"0" "1"-> 保存在led.txt;

ESP32中
發送Post可變電阻的數值 及 接收Get led.txt

程序有經過大量的優化及設計，
例如：
LED write_data.php，只會顯示一秒
球隨機完整開始等等
