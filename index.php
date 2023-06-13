
<!DOCTYPE html>
<html>
<head>
    <title>反彈球遊戲</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            setInterval(function() {
                $.ajax({
                    url: 'get_data.php',
                    type: 'GET',
                    success: function(data) {
                        $('#result').text("藍芽連線正常 | "+ " 電壓: " +data + " V");
                    }
                });
            }, 3000); // 每秒更新一次數據
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        #game-container {
            position: relative;
            height: 600px;
            width: 100%;
            border: 1px solid black;
            overflow: hidden;
        }

        .ball {
            position: absolute;
            width: 40px; /* 根據 banana.png 圖像的尺寸進行調整 */
            height: 40px; /* 根據 banana.png 圖像的尺寸進行調整 */
            top: 0;
            left: 0;
            transform: translate(-50%, -50%);
        }

        .ball img {
            width: 100%;
            height: 100%;
        }

        #line {
            position: absolute;
            width: 100px;
            height: 10px;
            background-color: black;
            bottom: 80px; /* 調整線段的垂直位置，可以根據需要進行調整 */
            left: calc(50% - 50px); /* 調整線段的水準位置，使其居中 */
            transition: left 0.2s linear;
        }
    </style>
</head>
<body>
    <div id="game-container">
        <div class="ball" id="ball1"><img src="banana.png" alt="Banana"></div>
        <div class="ball" id="ball2"><img src="apple.png" alt="Ball"></div>
        <div id="line"></div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var gameContainer = document.getElementById('game-container');
            var balls = document.getElementsByClassName('ball');
            var line = document.getElementById('line');

            var ballRadius = balls[0].offsetWidth / 2;
            var ballSpeedX = [3, -2]; // 每個球的水準速度
            var ballSpeedY = [3, 2]; // 每個球的垂直速度

            var ballPositionsX = []; // 每個球的水準位置
            var ballPositionsY = []; // 每個球的垂直位置

            // 初始化球的位置
            for (var i = 0; i < balls.length; i++) {
                var ball = balls[i];
                var ballPositionX = Math.random() * (gameContainer.offsetWidth - ball.offsetWidth);
                var ballPositionY = ballRadius;
                ballPositionsX.push(ballPositionX);
                ballPositionsY.push(ballPositionY);
                ball.style.left = ballPositionX + 'px';
                ball.style.top = ballPositionY + 'px';
            }

            function updateBallPosition() {
                for (var i = 0; i < balls.length; i++) {
                    var ball = balls[i];
                    var ballPositionX = ballPositionsX[i];
                    var ballPositionY = ballPositionsY[i];
                    var ballSpeedXValue = ballSpeedX[i];
                    var ballSpeedYValue = ballSpeedY[i];

                    ballPositionX += ballSpeedXValue;
                    ballPositionY += ballSpeedYValue;

                    if (ballPositionX + ballRadius >= gameContainer.offsetWidth || ballPositionX - ballRadius <= 0) {
                        ballSpeedXValue = -ballSpeedXValue;
                    }

                    if (ballPositionY + ballRadius >= gameContainer.offsetHeight) {
                        // 球碰到底部時隱藏
                        ball.style.display = 'none';
                    } else if (ballPositionY - ballRadius <= 0) {
                        // 球碰到頂部時顯示
                        ball.style.display = 'block';
                        ballSpeedYValue = -ballSpeedYValue; // 球碰到頂部反彈
                    }

                    if (
                        ballPositionY + ballRadius >= line.offsetTop &&
                        ballPositionX >= line.offsetLeft &&
                        ballPositionX <= line.offsetLeft + line.offsetWidth
                    ) {
                        ballSpeedYValue = -ballSpeedYValue; // 球碰到線段反彈

                        // 在球碰到線段後輸出文字
                        var outputText = (ballPositionY + ballRadius >= gameContainer.offsetHeight) ? "0" : "1";
                        $.ajax({
                            url: 'write_data.php',
                            type: 'POST',
                            data: {text: outputText},
                            success: function(response) {
                                console.log('Data written successfully:', response);
                            },
                            error: function(error) {
                                console.log('Error writing data:', error);
                            }
                        });
                    }

                    ballPositionsX[i] = ballPositionX;
                    ballPositionsY[i] = ballPositionY;
                    ballSpeedX[i] = ballSpeedXValue;
                    ballSpeedY[i] = ballSpeedYValue;

                    ball.style.left = ballPositionX + 'px';
                    ball.style.top = ballPositionY + 'px';
                }
            }

            function updateLinePosition(value) {
                var maxRange = 4095;
                var containerWidth = gameContainer.offsetWidth;
                var lineOffsetWidth = line.offsetWidth;
                var newPosition = Math.floor((value / maxRange) * (containerWidth - lineOffsetWidth));

                if (newPosition < 0) {
                    lineLeft = 0;
                } else if (newPosition > containerWidth - lineOffsetWidth) {
                    lineLeft = containerWidth - lineOffsetWidth;
                } else {
                    lineLeft = newPosition;
                }

                line.style.left = lineLeft + 'px';
            }

            // 從 data.txt 讀取數據
            function readData() {
                fetch('data.txt')
                    .then(response => response.text())
                    .then(data => {
                        var value = parseInt(data.trim());
                        updateLinePosition(value);
                    })
                    .catch(error => {
                        console.log('Error reading data:', error);
                    });
            }

            // 每500毫秒讀取一次資料
            setInterval(readData, 1);

            // 每16毫秒（60 FPS）更新一次球的位置
            setInterval(updateBallPosition, 18);
        });
    </script>
    <h2><div id="result"></div></h2>
</body>
</html>