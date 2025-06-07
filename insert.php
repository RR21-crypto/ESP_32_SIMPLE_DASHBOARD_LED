<?php
// Include your database connection file
require "db.php"; // This provides the $koneksi variable

// Set header for JSON response
header('Content-Type: application/json');

// Get the JSON data sent from the ESP32
$input_data = json_decode(file_get_contents("php://input"), true);

// Extract temperature and humidity from the input data.
// Use null coalescing operator (??) to provide null if the key doesn't exist.
$suhu = $input_data['esp_suhu'] ?? null;
$kelembapan = $input_data['esp_kelembapan'] ?? null;

// --- Determine the current lamp state to insert ---
$current_lamp_state = 'off'; // Default if no previous records or if fetching fails

// Query to get the lamp_state from the most recent record
$sql_get_last_lamp_state = "SELECT lamp_state FROM datasensor ORDER BY id DESC LIMIT 1";
$result_last_state = $koneksi->query($sql_get_last_lamp_state);

if ($result_last_state && $result_last_state->num_rows > 0) {
    $last_row = $result_last_state->fetch_assoc();
    // Use the lamp_state from the last record
    $current_lamp_state = $last_row['lamp_state'];
}
// If the table was empty, $current_lamp_state remains 'off',
// which matches the default you set for the lamp_state column in your database.

// --- Prepare the INSERT statement ---
// Using prepared statements is crucial for security (prevents SQL injection)
// The 'waktu' column will use its database default (CURRENT_TIMESTAMP)
$stmt = $koneksi->prepare("INSERT INTO datasensor (esp_suhu, esp_kelembapan, lamp_state) VALUES (?, ?, ?)");

// Check if the statement was prepared successfully
if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to prepare SQL statement: " . $koneksi->error
    ]);
    exit; // Stop script execution
}

// Bind the PHP variables to the placeholders in the SQL statement
// "sss" means all three parameters are strings.
// Your esp_suhu, esp_kelembapan, and lamp_state are TINYTEXT, which are treated as strings here.
$stmt->bind_param("sss", $suhu, $kelembapan, $current_lamp_state);

// Execute the prepared statement
if ($stmt->execute()) {
    // If execution is successful
    echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully.",
        "inserted_data" => [
            "suhu" => $suhu,
            "kelembapan" => $kelembapan,
            "lamp_state" => $current_lamp_state
        ]
    ]);
} else {
    // If execution fails
    echo json_encode([
        "status" => "error",
        "message" => "Failed to insert data: " . $stmt->error
    ]);
}

// Close the statement
$stmt->close();

// The database connection ($koneksi) is typically closed automatically by PHP
// when the script finishes, or you can explicitly close it if needed,
// but be careful if db.php is included in multiple places or long-running scripts.
// mysqli_close($koneksi);
?>