#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// --- WHAT YOU NEED TO CHANGE ---
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* serverUrl = "http://YOUR_COMPUTER_IP/lampDashBoard/";
const int LED_PIN = 2; // Change if you use a different pin // GPIO2 is the built-in LED on most ESP32s
// ---------------------------------

// Non-blocking timers using millis()
unsigned long lastPostTime = 0;
unsigned long lastCheckTime = 0;
const long postInterval = 2000;  // Post sensor data every 30 seconds
const long checkInterval = 1000;   // Check for lamp commands every 5 seconds

void setup() {
  Serial.begin(115200);
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(LED_PIN, LOW); // Start with LED off

  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    attempts++;
    if (attempts > 20) {
      Serial.println("\nFailed to connect to WiFi. Check SSID/Password and restart.");
      return;
    }
  }

  Serial.println("\nWiFi connected!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi disconnected. Reconnecting...");
    WiFi.reconnect();
    delay(1000);
    return;
  }

  unsigned long currentTime = millis();

  if (currentTime - lastPostTime >= postInterval) {
    lastPostTime = currentTime;
    postSensorData();
  }

  if (currentTime - lastCheckTime >= checkInterval) {
    lastCheckTime = currentTime;
    checkLampState();
  }
}

void postSensorData() {
  HTTPClient http;
  StaticJsonDocument<128> doc;
  doc["esp_suhu"] = random(200, 400) / 10.0;
  doc["esp_kelembapan"] = random(400, 901) / 10.0;

  String jsonString;
  serializeJson(doc, jsonString);

  String postUrl = String(serverUrl) + "insert.php";
  http.begin(postUrl);
  http.addHeader("Content-Type", "application/json");

  Serial.println("--- Sending Sensor Data ---");
  Serial.print("POST to: ");
  Serial.println(postUrl);
  Serial.print("Payload: ");
  Serial.println(jsonString);

  int httpCode = http.POST(jsonString);

  if (httpCode > 0) {
    String response = http.getString();
    Serial.print("HTTP Response Code: ");
    Serial.println(httpCode);
    Serial.print("Server Response: ");
    Serial.println(response);
  } else {
    Serial.print("HTTP POST failed, error: ");
    Serial.println(http.errorToString(httpCode));
  }
  http.end();
}

void checkLampState() {
  HTTPClient http;
  String getUrl = String(serverUrl) + "read.php";
  http.begin(getUrl);

  Serial.println("--- Checking Lamp State ---");
  Serial.print("GET from: ");
  Serial.println(getUrl);

  int httpCode = http.GET();

  if (httpCode > 0) {
    String payload = http.getString();
    Serial.print("Server Response: ");
    Serial.println(payload);

    StaticJsonDocument<256> doc;
    DeserializationError error = deserializeJson(doc, payload);

    if (error) {
      Serial.print("deserializeJson() failed: ");
      Serial.println(error.c_str());
      return;
    }

    const char* lampState = doc["lampState"];

    if (lampState) {
      Serial.print("Received Lamp State: ");
      Serial.println(lampState);
      if (strcmp(lampState, "on") == 0) {
        digitalWrite(LED_PIN, HIGH);
        Serial.println("Action: Turning LED ON");
      } else {
        digitalWrite(LED_PIN, LOW);
        Serial.println("Action: Turning LED OFF");
      }
    } else {
      Serial.println("Error: 'lampState' key not found in JSON response.");
    }
  } else {
    Serial.print("HTTP GET failed, error: ");
    Serial.println(http.errorToString(httpCode));
  }
  http.end();
}