<?php
// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allows all origins. For production, restrict this.

// Include your database connection file
require "db.php"; // This provides the $koneksi variable

// Get the desired lamp state from the GET request (e.g., lamp.php?state=on)
$requested_state = $_GET['state'] ?? 'off'; // Default to 'off' if not provided

// Validate the state: it should only be 'on' or 'off'
if ($requested_state !== 'on' && $requested_state !== 'off') {
    $requested_state = 'off'; // Force to 'off' if invalid input
}

// --- Update the lamp_state in the database ---
// We want to update the lamp_state for ALL future records until it's changed again.
// A common approach is to update the lamp_state of the MOST RECENT record.
// Your insert.php will then pick up this state for new entries.
// Alternatively, you could have a separate table just for device states,
// but for simplicity with your current 'datasensor' table, updating the last record works.

// Using prepared statements for security
$stmt = $koneksi->prepare("UPDATE datasensor SET lamp_state = ? ORDER BY id DESC LIMIT 1");

// Check if the statement was prepared successfully
if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to prepare SQL statement: " . $koneksi->error
    ]);
    exit;
}

// Bind the parameter
// "s" means the parameter is a string
$stmt->bind_param("s", $requested_state);

// Execute the prepared statement
if ($stmt->execute()) {
    // Check if any rows were actually affected.
    // If the table is empty, no rows will be affected.
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Lamp state updated to " . $requested_state,
            "new_state" => $requested_state
        ]);
    } else {
        // This can happen if the datasensor table is empty.
        // In this scenario, the default 'off' in the table definition
        // and in insert.php will handle the initial state.
        echo json_encode([
            "status" => "notice",
            "message" => "Lamp state command received (" . $requested_state . "). No existing records to update, new entries will reflect this state if applicable.",
            "new_state" => $requested_state
        ]);
    }
} else {
    // If execution fails
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update lamp state: " . $stmt->error
    ]);
}

// Close the statement
$stmt->close();

// mysqli_close($koneksi); // Connection usually closed automatically
?>