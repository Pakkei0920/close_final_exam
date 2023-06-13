#include <WiFi.h>
#include <HTTPClient.h>

const char* ssid = "7412";
const char* password = "20020920";
const char* ledUrl = "http://192.168.137.1/led.txt";
const char* sensorUrl = "http://192.168.137.1/save_data.php";
const int ledPin = 13;  // 将LED连接到ESP32的引脚13
const int analogInPin = 34;  // 定义可变电阻连接到的引脚

unsigned long previousPostTime = 0;
const unsigned long postInterval = 12;  // 发送POST请求的时间间隔为1秒

void setup() {
  Serial.begin(115200);

  pinMode(ledPin, OUTPUT);

  // 连接到Wi-Fi网络
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }

  Serial.println("Connected to WiFi");
}

void loop() {
  // 发起HTTP GET请求以获取LED状态
  String ledState = getLedState();

  if (ledState == "1") {
    // 打开LED
    digitalWrite(ledPin, HIGH);

  } else if (ledState == "0") {
    // 关闭LED
    digitalWrite(ledPin, LOW);
  }

  // 检查是否满足发送POST请求的时间间隔
  unsigned long currentMillis = millis();
  if (currentMillis - previousPostTime >= postInterval) {
    // 读取可变电阻的值
    int sensorValue = analogRead(analogInPin);
    Serial.println(String(sensorValue));

    // 创建HTTPClient对象
    HTTPClient http;

    // 添加要发送的数据
    String postData = "value=" + String(sensorValue);

    // 发送POST请求
    http.begin(sensorUrl);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      String response = http.getString();
    
    }
    http.end();
    previousPostTime = currentMillis;  // 更新上一次发送POST请求的时间
  }

}

String getLedState() {
  String ledState;

  // 创建HTTPClient对象
  HTTPClient http;

  // 发起GET请求
  http.begin(ledUrl);

  // 获取响应
  int httpResponseCode = http.GET();
  if (httpResponseCode == 200) {
    ledState = http.getString();
    Serial.print("LED State: ");
    Serial.println(ledState);
  } else {
    Serial.print("HTTP GET request failed, error code: ");
    Serial.println(httpResponseCode);
  }

  // 关闭连接
  http.end();

  return ledState;
}
