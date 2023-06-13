<?php
// Get the text data from the POST request
$text = $_POST['text'];

// Write the data to the led.txt file
$file = fopen("led.txt", "w");
if ($file) {
    fwrite($file, $text);
    fclose($file);

    // Check if the received text is "1"
    if ($text === "1") {
        // Wait for 1 seconds
        sleep(1);

        // Update the text to "0"
        $file = fopen("led.txt", "w");
        if ($file) {
            fwrite($file, "0");
            fclose($file);
        }
    }
}
?>
