<?php

define('STDOUT', fopen('php://stdout', 'w'));

/**
 * This is for development purpose ONLY !
 */
final class ServerLogger
{

    /**
     * send a log message to the STDOUT stream.
     *
     * @param array<int, mixed> $args
     *
     * @return void
     */
    public static function log(...$args): void
    {
        foreach ($args as $arg) {
            if (is_object($arg) || is_array($arg) || is_resource($arg)) {
                $output = print_r($arg, true);
            } else {
                $output = (string) $arg;
            }

            fwrite(STDOUT, $output . "\n");
        }
    }
}

// pg_connect("host=localhost dbname=tictacoe user=admin password=password");

session_start();

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}

function logout()
{
    ServerLogger::log('Logout: ', $_SERVER['PHP_AUTH_USER']);
    if (isset($_SERVER['PHP_AUTH_USER'])) {
        $_SERVER['PHP_AUTH_USER'] = "";
        unset($_SERVER['PHP_AUTH_USER']);
    }

    if (isset($_SERVER['PHP_AUTH_PW'])) {
        $_SERVER['PHP_AUTH_PW'] = "";
        unset($_SERVER['PHP_AUTH_PW']);
    }
    ServerLogger::log("Logged out: ", $_SERVER['PHP_AUTH_USER']);

    session_destroy();

    header('WWW-Authenticate: Basic realm="My Realm"');
}

function resetGame(): void
{
    $_SESSION['board'] = array_fill(0, 9, null);
    $_SESSION['currentPlayer'] = 0;
    $_SESSION['gameOver'] = false;
    $_SESSION['counter'] = 0;
}

// Initialize game state if not set
if (!isset($_SESSION['board']) || !isset($_SESSION['currentPlayer']) || !isset($_SESSION['gameOver']) || !isset($_SESSION['counter']) || !isset($_SESSION['oWins']) || !isset($_SESSION['xWins'])) {
    $_SESSION['oWins'] = 0;
    $_SESSION['xWins'] = 0;
    $_SESSION['pointer'] = 0;
    $_SESSION['scores'] = array(
        array('score' => 0, 'player' => 0),
        array('score' => 0, 'player' => 1),
        array('score' => 0, 'player' => 0),
        array('score' => 0, 'player' => 1),
        array('score' => 0, 'player' => 0),
        array('score' => 0, 'player' => 1),
        array('score' => 0, 'player' => 0),
        array('score' => 0, 'player' => 1),
        array('score' => 0, 'player' => 0),
        array('score' => 0, 'player' => 1)
    );
    resetGame();
}

function getToken(int $player): string
{
    return $player === 0 ? "X" : "O";
}

// Function to check if the move is valid
function isValidMove(array $board, int $position): bool
{
    ServerLogger::log($board[$position] ?? null);
    return isset($board) && $board[$position] === null;
}

function getComputerMove(): int
{
    return rand(0, 8);
}

// Function to check for a win
function checkWin(array $board): ?array
{
    $winningCombos = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8], // rows
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8], // columns
        [0, 4, 8],
        [2, 4, 6]  // diagonals
    ];

    foreach ($winningCombos as $combo) {
        if (
            $board[$combo[0]] !== null &&
            $board[$combo[0]] === $board[$combo[1]] &&
            $board[$combo[1]] === $board[$combo[2]]
        ) {
            return [$board[$combo[0]], $combo[0], $combo[1], $combo[2]];
        }
    }

    return null;
}


function updateScores(int $win, int $switch)
{

    if ($switch == 1) {
        $_SESSION['pointer']++;
        $_SESSION['scores'][$_SESSION['pointer']] = ['score' => 1, 'player' => $win];
    } else {
        $_SESSION['scores'][$_SESSION['pointer']]['score']++;
    }
}

function compareScores($a, $b)
{
    return $a[1] - $b[1];
}

function computeState()
{

    //resetGame();
    $winner = checkWin($_SESSION['board']);

    $leaderboard = $_SESSION['scores'];
    rsort($leaderboard);
    $leaderboard = array_slice($leaderboard, 0, 10);

    if ($winner !== null) {
        $_SESSION['gameOver'] = true;
    }

    $draw = $winner !== null ? false : $_SESSION['counter'] >= 9;

    if ($draw) {
        $_SESSION['gameOver'] = true;
    }

    $status = $winner !== null ? 'win' : ($draw ? 'draw' : 'continue');

    return ['status' => $status, 'winner' => $winner, 'leaderboard' => $leaderboard, 'currentPlayer' => $_SESSION['currentPlayer'], 'board' => $_SESSION['board'], 'oWins' => $_SESSION['oWins'], 'xWins' => $_SESSION['xWins']];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    ServerLogger::log($_GET);

    $status = computeState();

    ServerLogger::log("Response: ", $status);
    echo json_encode($status);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ServerLogger::log($_POST);

    if (isset($_POST['logout']) && $_POST['logout'] == true) {
        logout();

        echo json_encode(['success' => true]);
        exit;
    }

    if (isset($_POST['reset'])) {
        resetGame();

        $status = computeState();
        $status['status'] = 'reset';

        echo json_encode($status);
        exit;
    }

    $position = intval($_POST['position']);
    if (isValidMove($_SESSION['board'], $position) && !$_SESSION['gameOver']) {
        $_SESSION['board'][$position] = 0;
        $_SESSION['counter']++;


        $response = computeState();

        if ($response['status'] === 'win') {
            if ($response['winner'][0] === 0) {
                $_SESSION['xWins'] = $_SESSION['xWins'] + 1;
            } else {
                $_SESSION['oWins'] = $_SESSION['oWins'] + 1;
            }

            $switch = 0;
            if ($_SESSION['scores'][$_SESSION['pointer']]['player'] !== $response['winner'][0]) {
                $switch = 1;
            }
            updateScores($response['winner'][0], $switch);

            $response = computeState();
        }
    } else {
        $response = computeState();
        $response['status'] = 'invalid';
    }

    if (!$_SESSION['gameOver']) {

        do {
            $move = getComputerMove();
        } while (!isValidMove($_SESSION['board'], $move));

        $_SESSION['board'][$move] = 1;
        $_SESSION['counter']++;

        $response = computeState();

        if ($response['status'] === 'win') {
            if ($response['winner'][0] === 0) {
                $_SESSION['xWins'] = $_SESSION['xWins'] + 1;
            } else {
                $_SESSION['oWins'] = $_SESSION['oWins'] + 1;
            }

            $switch = 0;
            if ($_SESSION['scores'][$_SESSION['pointer']]['player'] !== $response['winner'][0]) {
                $switch = 1;
            }
            updateScores($response['winner'][0], $switch);

            $response = computeState();
        }

    }


    ServerLogger::log("Response: ", $response);

    echo json_encode($response);
}
?>