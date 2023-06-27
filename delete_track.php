<?php
session_start();

$success = $_GET['success'] ?? false;
$failure = $_GET['failure'] ?? false;

if (!isset($_SESSION['count']))
    $_SESSION['count'] = 0;
$_SESSION['count']++;

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
} else {
    include_once('storage.php');

    $stor = new Storage(new JsonIO('data/users.json'));
    $user = $stor->findById($_SESSION['user_id']);

    if (!$user['isAdmin']) {
        header('location: index.php');
        exit();
    }

    if (!$_POST) {
        header('location: admin.php');
        exit();
    }

    $track_id = $_POST['track_id'] ?? -1;

    if ($track_id == -1) {
        header('location: index.php');
        exit();
    }

    $title    = $_POST['title']  ?? '';
    $artist   = $_POST['artist'] ?? '';
    $length   = $_POST['length'] ?? '';
    $year     = $_POST['year']   ?? '';
    $genres   = $_POST['genres'] ?? '';
    $track_id = $_POST['track_id'] ?? '';

    var_dump($_POST);

    $stor = new Storage(new JsonIO('data/tracks.json'));
    $stor->delete($track_id);

    header('location: admin.php?success=true');
    exit();
}

?>