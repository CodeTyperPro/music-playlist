<?php
session_start();

if (!$_GET) {
    header('location: index.php');
    exit();
}

$track_id = $_GET['track_id'] ?? -1;
$playlist_id = $_GET['playlist_id'] ?? -1;

if ($track_id == -1 || $playlist_id == -1){
    header('location: detail_myplaylist.php?failure=false');
    exit();
}

include_once('storage.php');

$stor = new Storage(new JsonIO('data/playlists.json'));
$new_data = $stor->findById($playlist_id);
$new_data['tracks'] = array_diff($new_data['tracks'], [$track_id]);
$stor->update($playlist_id, $new_data);

header('location: detail_myplaylist.php?playlist_id='.$playlist_id.'&success=true');
exit();

?>