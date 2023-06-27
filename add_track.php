<?php
include_once('storage.php');
$error = false;
$errors = [];
if ($_POST) {
    $title    = $_POST['title']  ?? '';
    $artist   = $_POST['artist'] ?? '';
    $length   = $_POST['length'] ?? '';
    $year     = $_POST['year']   ?? '';
    $genres   = $_POST['genres'] ?? '';

    if($title === '') {
        $errors['title'] = "Playlist name is required!";
        $error = true;
    }

    if($artist === '') {
        $errors['artist'] = "Artist name is required!";
        $error = true;
    }

    if($length === '' || false === filter_var(intval($length), FILTER_VALIDATE_INT)) {
        $errors['length'] = "Length is required!";
        $error = true;
    }

    if($year === '' || false === filter_var(intval($year), FILTER_VALIDATE_INT)) {
        $error = true;
    }

    if($genres === '') {
        $error = true;
    }

    if ($error === true) {
        header('location: admin.php?failure=true');
        exit();
    }

    function splitStringByComma($string) {
        $array = explode(',', $string);
        return $array;
    }

    $stor = new Storage(new JsonIo('data/tracks.json'));

    // don't forget to do your validations here on the exam :)
    $stor->add([
        'title'  => $title,
        'artist' => $artist,
        'length' => intval($length),
        'year'   => intval($year),
        'genres' => splitStringByComma($genres)
    ]);

    header('location: admin.php?success=true');
    exit();
}
