<?php

session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

require_once 'log.php';
require_once 'db.php';

function getCurrentGame($user_id)
{
    global $db;
    $result = $db->query("SELECT * FROM games WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
    return $db->fetchAssoc($result);
}

function createNewGame($user_id)
{
    ServerLogger::log("Creating a new game");

    global $db;
    $db->query("INSERT INTO games (user_id) VALUES ($user_id)");

    ServerLogger::log("Created new game: ", $db->lastInsertId('games'));

    return $db->lastInsertId('games');
}

function updateGameState($game_id, $board, $status)
{
    global $db;
    $board = $db->escape($board);
    $db->query("UPDATE games SET board = '$board', status = '$status' WHERE id = $game_id");
}

function resetGame(): void
{
    ServerLogger::log("Reseting game state");

    global $db;
    $user_id = $_SESSION['user_id'];
    createNewGame($user_id);
}

function getToken(int $player): string
{
    return $player === 0 ? "X" : "O";
}

// Function to check if the move is valid
function isValidMove(array $board, int $position): bool
{
    ServerLogger::log($board[$position] ?? "null");
    return isset($board) && $board[$position] === '-';
}

function getComputerMove(array $board_tokens): int|null
{
    $counter = rand(5, 19);
    $n = 0;

    while ($n < $counter) {
        $l = $n;
        for ($i = 0; $i < count($board_tokens); $i++) {
            if ($board_tokens[$i] === '-') {
                $n++;

                // If we've found n valid moves, return that n'th move
                if ($n >= $counter) {
                    return $i;
                }
            }
        }

        if ($l === $n) {
            return null;
        }
    }

    return null;
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
            $board[$combo[0]] !== '-' &&
            $board[$combo[0]] === $board[$combo[1]] &&
            $board[$combo[1]] === $board[$combo[2]]
        ) {
            return [$board[$combo[0]], $combo[0], $combo[1], $combo[2]];
        }
    }

    return null;
}

function isBoardFull($board)
{
    for ($i = 0; $i < count($board); $i++) {
        if ($board[$i] === "-") {
            return false;
        }
    }

    return true;
}

function computeState($user_id)
{
    global $db;
    $game = getCurrentGame($user_id);

    if (!$game) {
        return ['status' => 'no_game'];
    }

    $board = str_split($game['board']);
    $winner = checkWin($board);

    $leaderboard = getLeaderboard();

    return [
        'status' => $game['status'],
        'winner' => $winner,
        'leaderboard' => $leaderboard,
        'board' => $board,
        'xWins' => getUserWins($user_id),
        'oWins' => getAIWins($user_id)
    ];
}

function getLeaderboard()
{
    global $db;
    $result = $db->query("SELECT u.username, COUNT(*) as wins FROM games g JOIN users u ON g.user_id = u.id WHERE g.status = 'user_won' GROUP BY u.id ORDER BY wins DESC LIMIT 10");
    return $db->fetchAll($result);
}

function getUserWins($user_id)
{
    global $db;
    $result = $db->query("SELECT COUNT(*) as wins FROM games WHERE user_id = $user_id AND status = 'user_won'");
    $row = $db->fetchAssoc($result);
    return $row['wins'];
}

function getAIWins($user_id)
{
    global $db;
    $result = $db->query("SELECT COUNT(*) as wins FROM games WHERE user_id = $user_id AND status = 'ai_won'");
    $row = $db->fetchAssoc($result);
    return $row['wins'];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    ServerLogger::log($_GET);

    $user_id = $_SESSION['user_id'];
    $status = computeState($user_id);

    if ($status['status'] == 'no_game') {
        resetGame();
    }

    $status = computeState($user_id);

    ServerLogger::log("Response: ", $status);
    echo json_encode($status);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ServerLogger::log($_POST);

    $user_id = $_SESSION['user_id'];

    if (isset($_POST['reset']) && isset($_POST['reset'])) {
        resetGame();
        echo json_encode(computeState($user_id));
        return;
    }

    if (isset($_POST['logout']) && isset($_POST['logout'])) {
        logout();
        echo json_encode(['success' => true]);
        return;
    }

    $game = getCurrentGame($user_id);

    if (!$game) {
        $game_id = createNewGame($user_id);
        $game = getCurrentGame($user_id);
    }

    $board = str_split($game['board']);
    $position = intval($_POST['position']);

    if (isValidMove($board, $position) && $game['status'] === 'ongoing') {
        ServerLogger::log("Playing move");

        $board[$position] = 'X';
        $new_board = implode('', $board);

        $winner = checkWin($board);
        ServerLogger::log("Winner: ", $winner);

        if ($winner !== null) {
            ServerLogger::log("User won!");
            ServerLogger::log(computeState($user_id));
            updateGameState($game['id'], $new_board, 'user_won');
            ServerLogger::log(computeState($user_id));

        } else if (isBoardFull($board)) {
            ServerLogger::log("Board is full");
            updateGameState($game['id'], $new_board, 'draw');
        } else {
            // AI move
            ServerLogger::log("Computing AI move!");
            $move = getComputerMove($board);
            ServerLogger::log("AI move: ", $move);

            $board[$move] = 'O';
            $new_board = implode('', $board);

            $winner = checkWin($board);
            if ($winner !== null) {
                updateGameState($game['id'], $new_board, 'ai_won');
            } else if (isBoardFull($board)) {
                updateGameState($game['id'], $new_board, 'draw');
            } else {
                updateGameState($game['id'], $new_board, 'ongoing');
            }
        }

        $response = computeState($user_id);
    } else {
        ServerLogger::log("Invalid move received!");
        $response = computeState($user_id);
        $response['status'] = 'invalid';
    }

    ServerLogger::log($response);
    echo json_encode($response);
}
?>