<?php

include_once 'log.php';

class Database
{
    private $conn;

    public function __construct()
    {
        $this->conn = pg_connect("host=localhost dbname=tictactoe user=admin password=password");
        if (!$this->conn) {
            die("Connection failed: " . pg_last_error());
        }
    }

    public function query($sql)
    {
        $result = pg_query($this->conn, $sql);
        if (!$result) {
            die("Query failed: " . pg_last_error());
        }
        return $result;
    }

    public function escape($value)
    {
        return pg_escape_string($this->conn, $value);
    }

    public function fetchAssoc($result)
    {
        return pg_fetch_assoc($result);
    }

    public function fetchAll($result)
    {
        return pg_fetch_all($result);
    }

    public function lastInsertId($table, $column = 'id')
    {
        $query = "SELECT lastval()";
        $result = $this->query($query);
        $row = $this->fetchAssoc($result);
        return $row['lastval'];
    }
}

$db = new Database();

function login($username, $password)
{
    global $db;
    $u = $db->escape($username);
    $result = $db->query("SELECT id, password, is_admin FROM users WHERE username = '$u'");
    $user = $db->fetchAssoc($result);

    if ($user && $password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $u;
        $_SESSION['is_admin'] = $user['is_admin'] == 't';

        ServerLogger::log("Admin: ", $user);

        return true;
    }
    return false;
}

function logout()
{
    if (isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = null;
        unset($_SESSION['user_id']);
    }

    if (isset($_SESSION['username'])) {
        $_SESSION['username'] = null;
        unset($_SESSION['username']);
    }

    if (isset($_SESSION['is_admin'])) {
        $_SESSION['is_admin'] = null;
        unset($_SESSION['is_admin']);
    }

    session_destroy();

    header('WWW-Authenticate: Basic realm="My Realm"');
}
?>