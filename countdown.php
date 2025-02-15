<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Countdown</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #9370db;
            color: white;
            font-family: "Lalezar", system-ui;
            font-size: 3rem;
            font-weight: 1000;
        }
        #countdown {
            font-size: 5rem;
        }
    </style>
</head>
<body>
    <div id="countdown">3</div>

    <script>
        let countdown = 3;

        function updateCountdown() {
            document.getElementById('countdown').textContent = countdown;
            if (countdown > 1) {
                countdown--;
                setTimeout(updateCountdown, 1000);
            } else {
                setTimeout(() => {
                    document.getElementById('countdown').textContent = "Name That Tune !!!";
                    setTimeout(() => {
                        const urlParams = new URLSearchParams(window.location.search);
                        const quizId = urlParams.get('quizId');
                        window.location.href = `user_question_page_new.php?quizId=${quizId}`;
                    }, 1000);
                }, 1000);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateCountdown();
        });
    </script>
</body>
</html>