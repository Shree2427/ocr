<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Path to your Python script
$pythonScript = 'C:\\Users\\SERVER\\Desktop\\OCR\\ocr_proj\\python_scripts\\ocr_api.py'; // Update this path

// Path to the image you want to process
$imagePath = 'C:\\Users\\SERVER\\Downloads\\shi1.png'; // Update this path

// Command to run the Python script
$command = escapeshellcmd("python $pythonScript $imagePath");

// Execute the command
$output = shell_exec($command);

// Print output for debugging
echo "<pre>$output</pre>";
?>
