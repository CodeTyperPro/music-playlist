<?php
include_once('storage.php');

session_start();

$user = [];
$errors = [];
$error = false;

if (!isset($_SESSION['count']))
    $_SESSION['count'] = 0;
$_SESSION['count']++;

if (!isset($_SESSION['user_id'])) {
    // header('location: login.php');
} else {
    include_once('storage.php');

    $stor = new Storage(new JsonIO('data/users.json'));
    $user = $stor->findById($_SESSION['user_id']);
}


if ($_POST) {
    $playlist_name    = $_POST['playlist_name']  ?? '';
    $is_public        = $_POST['is_public'] ?? false;
    $is_public        = filter_var($is_public, FILTER_VALIDATE_BOOLEAN);

    var_dump($is_public);
    
    if($playlist_name === '') {
        $errors['play_list_name'] =  "Playlist name is required!";
        $error = true;
    }

    $errors = array_map(fn ($e) => 
    "<div class='alert alert-danger' role='alert'>
            <span>$e</span>
        </div>
    ", $errors);

    if ($error === true) {
        header('location: myplaylists.php?errors='.$errors);
        exit();
    }

    $stor = new Storage(new JsonIO('data/playlists.json'));

    // don't forget to do your validations here on the exam :)
    $stor->add([
        'name'  => $playlist_name,
        'public' => $is_public ? true : false,
        'created_by' => $user['username'],
        'tracks'   => []
    ]);

    header('location: myplaylists.php?success=true');
    exit();
}
