<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Tic Tac Toe</title>
    <link type="text/css" href="styles.css" rel="stylesheet" />
    <script src="game.js"></script>
</head>

<body>
    <nav>
        <a style="margin-right: 1em" href="../index.html">Return Home</a>

        <?php if (isset($_SERVER['PHP_AUTH_USER'])): ?>
            <?php
            $name = $_SERVER['PHP_AUTH_USER'];
            echo "<a>$name</a>";
            ?>
            <button onclick="javascript:logout()">Logout</button>
        <?php endif; ?>
    </nav>
    <div class="center-grid">
        <div class="side"></div>
        <div class="center">
            <div class="header">
                <h1>Score : X-<b id="x_score">0</b>&emsp; O-<b id="o_score">0</b></h1>
            </div>
            <div>
                <div class="game-grid">
                    <div data-slot="0"></div>
                    <div data-slot="1"></div>
                    <div data-slot="2"></div>
                    <div data-slot="3"></div>
                    <div data-slot="4"></div>
                    <div data-slot="5"></div>
                    <div data-slot="6"></div>
                    <div data-slot="7"></div>
                    <div data-slot="8"></div>
                </div>
            </div>
            <div class="center">
                <button id="restart-button">Restart</button>
            </div>
        </div>
        <div class="side">
            <div class="leaderboard-list">
                <b>10 best wins in a row</b>
                <ol>
                    <div data-leadSlot="0"></div>
                    <div data-leadSlot="1"></div>
                    <div data-leadSlot="2"></div>
                    <div data-leadSlot="3"></div>
                    <div data-leadSlot="4"></div>
                    <div data-leadSlot="5"></div>
                    <div data-leadSlot="6"></div>
                    <div data-leadSlot="7"></div>
                    <div data-leadSlot="8"></div>
                    <div data-leadSlot="9"></div>
                </ol>
            </div>
        </div>
    </div>
</body>

</html>