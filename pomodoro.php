<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pomodoro Timer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
            background-color: #f0f0f0;
        }
        #timer {
            font-size: 48px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Pomodoro Timer</h1>
    <div id="timer">25:00</div>
    <button onclick="startTimer()">Start</button>
    <button onclick="pauseTimer()">Pause</button>
    <button onclick="resetTimer()">Reset</button>

    <script>
        let duration = 25 * 60; // 25 menit
        let timer = duration;
        let interval = null;
        let isRunning = false;

        function updateDisplay() {
            let minutes = Math.floor(timer / 60);
            let seconds = timer % 60;
            document.getElementById("timer").innerText =
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function startTimer() {
            if (isRunning) return;
            isRunning = true;
            interval = setInterval(() => {
                if (timer > 0) {
                    timer--;
                    updateDisplay();
                } else {
                    clearInterval(interval);
                    alert("Waktu habis! Istirahat dulu yuk.");
                    isRunning = false;
                }
            }, 1000);
        }

        function pauseTimer() {
            clearInterval(interval);
            isRunning = false;
        }

        function resetTimer() {
            pauseTimer();
            timer = duration;
            updateDisplay();
        }

        updateDisplay(); // initialize
    </script>
</body>
</html>
