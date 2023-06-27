<?php
include_once('storage.php');

session_start();

$user = [];

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
}

if ($_POST) {
    $playlist_id            = $_POST['playlist_id']  ?? -1;
    $track_id               = $_POST['track_id'] ?? -1;
    $current_playlist_id    = $_POST['current_playlist_id'] ?? -1;

    var_dump($playlist_id);
    var_dump($track_id);

    if (intval($playlist_id) === -1) {
        if ($current_playlist_id === -1) {
            header('location: index.php?success=true');
        } else {
            header('location: detail_playlist.php?playlist_id=' . $current_playlist_id . '&failure=true');
        }
        exit();
    }

    $stor = new Storage(new JsonIO('data/playlists.json'));
    $old_playlist = $stor->findById($playlist_id);
    var_dump($playlist_id);

    var_dump($old_playlist);
    $old_playlist['tracks'] = array_merge($old_playlist['tracks'], [$track_id]) ?? [$track_id];
    var_dump($old_playlist);

    // don't forget to do your validations here on the exam :)
    $stor->update($playlist_id, $old_playlist);

    if (intval($current_playlist_id) === -1) {
        header('location: index.php?success=true');
    } else {
        header('location: detail_playlist.php?playlist_id=' . $current_playlist_id . '&success=true');
    }
    exit();
}
