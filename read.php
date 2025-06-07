<?php
// Set headers for JSON response and CORS (Cross-Origin Resource Sharing)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allows all origins. For production, you might want to restrict this.

// Include your database connection file
require "db.php"; // or include "db.php";

// SQL query to select the latest sensor data including the lamp_state
// We order by 'id' in descending order and limit to 1 to get the most recent entry.
$sql_query = "SELECT esp_suhu, esp_kelembapan, waktu, lamp_state FROM datasensor ORDER BY id DESC LIMIT 1";

// Execute the query
$result = mysqli_query($koneksi, $sql_query);

// Check if the query was successful and if any rows were returned
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the data as an associative array
    $data = mysqli_fetch_assoc($result);

    // Prepare the data for JSON output
    // The dashboard JavaScript expects 'lampState' (camelCase)
    $output = [
        'temperature' => number_format((float)$data['esp_suhu'], 1) . '°C',
        'humidity' => number_format((float)$data['esp_kelembapan'], 0) . '%',
        'timestamp' => $data['waktu'], // Assuming 'waktu' is already in a suitable format from the DB
        'lampState' => $data['lamp_state']  // Add the lamp state here
    ];

    // Encode the array to JSON and output it
    echo json_encode($output);

} else {
    // If no data is found or there's an error, return an error message
    // It's also good practice to return the expected structure with default values
    // so the frontend doesn't break if it expects these keys.
    if (!$result) {
        // Specifically handle query execution errors
        echo json_encode([
            'error' => 'Database query failed: ' . mysqli_error($koneksi),
            'temperature' => '--°C',
            'humidity' => '--%',
            'timestamp' => 'N/A',
            'lampState' => 'off' // Default lamp state if there's an error
        ]);
    } else {
        // Handle the case where no data rows are found
        echo json_encode([
            'error' => 'No data found in datasensor table',
            'temperature' => '--°C',
            'humidity' => '--%',
            'timestamp' => 'N/A',
            'lampState' => 'off' // Default lamp state if no data
        ]);
    }
}

// It's good practice to close the database connection,
// though PHP often handles this automatically at the end of script execution.
// mysqli_close($koneksi);
?>