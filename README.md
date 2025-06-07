# ESP32 IoT Lamp & Sensor Dashboard

A simple yet elegant IoT dashboard to monitor temperature and humidity, and control an LED remotely using an ESP32, PHP, and a MySQL database. This project is designed to be a clear and straightforward starting point for IoT enthusiasts.

![image](https://github.com/user-attachments/assets/042ca866-6413-48c0-9958-278348ac4f22)

link demo : https://youtube.com/shorts/65e8nY5GSzo?si=3L2T2AeS8JtHBmSm

## ✨ Features

-   **Live Data Monitoring:** View real-time temperature and humidity data sent from the ESP32.
-   **Remote Control:** Toggle a physical LED connected to the ESP32 on and off from the web interface.
-   **Clean & Modern UI:** A responsive, minimalist, and curvy user interface built with HTML, CSS, and JavaScript.
-   **Data Logging:** All sensor readings are logged to a MySQL database with timestamps.
-   **Auto-Refreshing Data:** The dashboard automatically fetches the latest data every 5 seconds.

## 🛠️ Tech Stack

-   **Hardware:**
    -   ESP32 Development Board
    -   DHT11/DHT22 Sensor (or random data generation as in the example)
    -   LED
    -   Resistor (e.g., 220Ω)
-   **Firmware (ESP32):**
    -   C++ via Arduino Framework
    -   Libraries: `WiFi`, `HTTPClient`, `ArduinoJson`
-   **Backend:**
    -   XAMPP (Apache Web Server, MySQL Database)
    -   PHP
-   **Frontend:**
    -   HTML5
    -   CSS3
    -   JavaScript

## 📂 Project Structure

Here is the file structure for the web server component located in `xampp/htdocs/lampDashBoard/`.

```
lampDashBoard/
├── db.php           # Database connection configuration
├── index.html       # The main dashboard user interface
├── insert.php       # API endpoint for the ESP32 to insert data
├── lamp.php         # API endpoint to handle lamp control commands
├── read.php         # API endpoint for the dashboard to read data
└── README.md        # This documentation file
```

## 🚀 Getting Started

Follow these steps to get the project up and running on your local machine.

### Part 1: Backend Setup (XAMPP & Database)

1.  **Install XAMPP:** Download and install [XAMPP](https://www.apachefriends.org/index.html).
2.  **Start Services:** Open the XAMPP Control Panel and start the **Apache** and **MySQL** modules.
3.  **Create Database:**
    -   Open your web browser and go to `http://localhost/phpmyadmin/`.
    -   Click on the "Databases" tab.
    -   Create a new database named `iot_data`.
4.  **Create Table:**
    -   Click on the `iot_data` database in the left sidebar.
    -   Go to the "SQL" tab.
    -   Paste the following SQL command and click "Go" to create the `datasensor` table:
    ```sql
    CREATE TABLE `datasensor` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `esp_suhu` tinytext DEFAULT NULL,
      `esp_kelembapan` tinytext DEFAULT NULL,
      `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
      `lamp_state` tinytext NOT NULL DEFAULT 'off',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ```
5.  **Place Project Files:**
    -   Place all the project files (`index.html`, `db.php`, `read.php`, etc.) into a folder inside your XAMPP installation directory. The path should be `C:\xampp\htdocs\lampDashBoard\`.
6.  **Configure `db.php`:**
    -   Open `db.php` and ensure the database credentials are correct for your XAMPP setup (the defaults are usually correct).
    ```php
    $servername = "localhost";
    $username = "root";
    $password = ""; // Default is empty
    $dbname = "iot_data";
    ```

### Part 2: Hardware Setup

1.  Connect the LED to your ESP32. The default pin in the code is **GPIO 2** (the built-in LED on many boards).
2.  **Wiring:**
    -   Connect the **long leg (anode)** of the LED to one end of a 220Ω resistor.
    -   Connect the other end of the resistor to **GPIO 2** on the ESP32.
    -   Connect the **short leg (cathode)** of the LED to a **GND** (Ground) pin on the ESP32.

### Part 3: Firmware Setup (ESP32)

1.  **Setup Arduino IDE:**
    -   Make sure you have the [Arduino IDE](https://www.arduino.cc/en/software) installed.
    -   Add the ESP32 board manager to your IDE. Follow [this guide](https://docs.espressif.com/projects/arduino-esp32/en/latest/installing.html) if you haven't already.
2.  **Install Libraries:**
    -   In the Arduino IDE, go to `Sketch` > `Include Library` > `Manage Libraries...`.
    -   Search for and install `ArduinoJson` by Benoit Blanchon.
3.  **Configure the Code:**
    -   Open the ESP32 code (`.ino` file).
    -   Modify the following configuration variables at the top of the file:
    ```cpp
    // --- WHAT YOU NEED TO CHANGE ---
    const char* ssid = "YOUR_WIFI_SSID";
    const char* password = "YOUR_WIFI_PASSWORD";
    const char* serverUrl = "http://YOUR_COMPUTER_IP/lampDashBoard/";
    const int LED_PIN = 2; // Change if you use a different pin
    ```
    -   **Important:** Replace `YOUR_COMPUTER_IP` with the actual local IP address of the computer running XAMPP (e.g., `192.168.1.10`). You can find this by typing `ipconfig` (Windows) or `ifconfig` (Mac/Linux) in your command prompt/terminal.
4.  **Upload to ESP32:**
    -   Connect your ESP32 to your computer.
    -   Select the correct board (e.g., "ESP32 Dev Module") and COM port from the `Tools` menu.
    -   Click the "Upload" button.

## 💡 How to Use

1.  Ensure your XAMPP services (Apache, MySQL) are running.
2.  Power on your ESP32. You can open the Arduino Serial Monitor at `115200` baud to see debug messages.
3.  Open your web browser and navigate to `http://localhost/lampDashBoard/`.
4.  You should see the dashboard load, and the temperature/humidity values will appear and update automatically.
5.  Click the toggle switch on the "Lamp Control" card to turn your physical LED on and off!

## 📄 API Endpoints

-   `insert.php` [POST]: Receives JSON `{"esp_suhu": value, "esp_kelembapan": value}` from the ESP32 and inserts it into the database.
-   `read.php` [GET]: Returns a JSON object with the latest sensor data and lamp state.
-   `lamp.php` [GET]: Updates the lamp state in the database. Called with a parameter, e.g., `lamp.php?state=on`.

## 📜 License

This project is licensed under the MIT License. See the `LICENSE` file for details.
*(Suggestion: Create a file named `LICENSE` and put the MIT license text in it.)*

---

Create by RAYHAN GIBRANI UHUM
