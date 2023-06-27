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

    $stor = new Storage(new JsonIO('data/tracks.json'));
    $track = $stor->findById($track_id);

    if ($track_id == -1) {
        header('location: index.php');
        exit();
    } else if ($_POST) {
        $title    = $_POST['title']  ?? '';
        $artist   = $_POST['artist'] ?? '';
        $length   = $_POST['length'] ?? '';
        $year     = $_POST['year']   ?? '';
        $genres   = $_POST['genres'] ?? '';
        $track_id = $_POST['track_id'] ?? '';

        $stor->update($track_id, [
            'title'  => $title,
            'artist' => $artist,
            'length' => intval($length),
            'year'   => intval($year),
            'genres' => splitStringByComma($genres),
            'id'     => $track_id
        ]);
        
        header('location: admin.php?success=true');
    }
}      

function splitStringByComma($string) {
    $array = explode(' | ', $string);
    return $array;
}

?>