<?php
session_start();

$success = $_GET['success'] ?? false;
$failure = $_GET['failure'] ?? false;

$errors = $_GET['errors'] ?? false;

if (!$_GET) {
    header('location: index.php');
    exit();
}

$playlist_id = $_GET['playlist_id'] ?? -1;
$errors = [];

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

    // 
    $stor = new Storage(new JsonIO('data/playlists.json'));
    $playlist = $stor->findById($playlist_id);

    $stor_tracks = new Storage(new JsonIO('data/tracks.json'));
    $output = [];
    $tracks = $playlist['tracks'] ?? [];
    foreach ($tracks as $track_id) {
        $track = $stor_tracks->findById($track_id);
        if (!$track)
            continue;
        $output[] = $track;
    }

    $total_minute = array_reduce($output, fn($carry, $item) => $carry + $item['length']);

    $tracks = array_map(function ($item, $index) {
        $item['index'] = $index;
        return $item;
    }, $output, array_keys($output));
}

function formatArrayToString($array) {
    $separator = " | ";
    return implode($separator, $array);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <a class="navbar-brand custom-lg-bg" href="index.php">
                    <img src="src/svg_logo.svg" width="250" alt="SongTyper">
                </a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="myplaylists.php">My Playlist</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                    </li>
                </ul>
                <div class="d-flex justify-content-end pl-10 ml-10">
                    <ul class="navbar-light bg-light navbar-nav me-auto mb-2 mb-lg-0 ">

                        <?php if ($user) : ?>
                            <li class="nav-item">
                            <a class="nav-link active" href="#"><?= $user['username']?></a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>

                        <?php if ($user) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php"> Logout</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <?php if ($success) : ?>
        <div class="alert alert-success" role="alert">
            <span>Track removed successfully.</span>
        </div>
    <?php endif; ?>

    <?php if ($failure) : ?>
        <div class="alert alert-danger" role="alert">
            <span>Error removing the track.</span>
        </div>
    <?php endif; ?>

    <?php foreach($errors as $x):  ?>
        <?= $x ?? '' ?>
    <?php endforeach; ?>

    <section>
        <section class="ps-4">
            <h2><?= $playlist['name'] ?? 'Unknown'?></h2>

            <span>Total playing time = <?= $total_minute ?? 0 ?></span>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Title</th>
                        <th scope="col">Artist</th>
                        <th scope="col">Length</th>
                        <th scope="col">Year</th>
                        <th scope="col">Genres</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tracks as $id => $d) : ?>
                    <tr>
                            <th scope="row"><?= intval($d['index']) + 1?></th>
                            <td><?= $d['title'] ?></td>
                            <td><?= $d['artist'] ?></td>
                            <td><?= $d['length'] ?></td>
                            <td><?= $d['year'] ?></td>
                            <td><?= formatArrayToString($d['genres']) ?></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="remove_track_playlist.php?playlist_id=<?= $playlist_id ?>&track_id=<?= $d['id']?>">Remove</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </section>
    </section>


    <footer>
        <div class="container">
            <div class="row">
                <div class="col">
                    <p class="text-center">SongTyper &copy; 2023 ELTE, Hungary - All rights reserved</p>
                </div>
            </div>
        </div>
    </footer>



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>