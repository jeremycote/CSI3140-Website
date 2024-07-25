<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

include_once 'db.php';

// Fetch all games
$stmt = $db->query("SELECT g.*, u.username FROM games g LEFT JOIN users u ON g.user_id = u.id ORDER BY g.created_at DESC");
$games = $db->fetchAll($stmt);

// Function to determine winning moves
function getWinningMoves($board)
{
    $winningCombos = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8], // Rows
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8], // Columns
        [0, 4, 8],
        [2, 4, 6]  // Diagonals
    ];

    foreach ($winningCombos as $combo) {
        if (
            $board[$combo[0]] != '-' &&
            $board[$combo[0]] == $board[$combo[1]] &&
            $board[$combo[1]] == $board[$combo[2]]
        ) {
            return $combo;
        }
    }
    return [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic Tac Toe Admin Page</title>
    <link type="text/css" href="global.css" rel="stylesheet" />
    <link type="text/css" href="admin.css" rel="stylesheet" />
</head>

<body>
    <nav>
        <a style="margin-right: 1em" href="../index.html">Return Home</a>
        <a style="margin-right: 1em" href="index.php">Back to Game</a>
    </nav>
    <h1>Admin Page</h1>
    <div class="game-grid">
        <?php foreach ($games as $game): ?>
            <div class="game-card">
                <h3>Game #<?= $game['id'] ?? 'Unknown' ?></h3>
                <p>Player: <?= $game['username'] ?? 'Unknown' ?></p>
                <p>Status: <?= $game['status'] ?? 'Unknown' ?></p>
                <p>Created: <?= $game['created_at'] ?? 'Unknown' ?></p>
                <div class="board">
                    <?php
                    $board = str_split($game['board']);
                    $winningMoves = getWinningMoves($board);
                    foreach ($board as $index => $cell):
                        $class = in_array($index, $winningMoves) ? 'cell winning-move' : 'cell';
                        ?>
                        <div class="<?= $class ?>"><?= $cell != '-' ? $cell : '' ?></div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($winningMoves)): ?>
                    <p>Winning moves: <?= implode(', ', array_map(function ($i) {
                        return $i + 1;
                    }, $winningMoves)) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>