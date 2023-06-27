<?php
session_start();

$success = $_GET['success'] ?? false;
$failure = $_GET['failure'] ?? false;

$user = [];

if (!isset($_SESSION['count']))
    $_SESSION['count'] = 0;
$_SESSION['count']++;

if (!isset($_SESSION['user_id'])) {
    // header('location: login.php');
    include_once('storage.php');

    // List all the public playlists
    $stor = new Storage(new JsonIO('data/playlists.json'));
    // $playlists = $stor->findAll();
    $playlists = $stor->findMany(function ($data) use ($user) {
        return $data['public'] == true;
    }) ?? [];

    $mappedPlaylists = array_map(function ($item, $index) {
        $item['index'] = $index;
        $item['tracks'] = count($item['tracks']);
        return $item;
    }, $playlists, array_keys($playlists)) ?? [];
} else {
    include_once('storage.php');

    $stor = new Storage(new JsonIO('data/users.json'));
    $user = $stor->findById($_SESSION['user_id']);

    // List all the public playlists
    $stor = new Storage(new JsonIO('data/playlists.json'));
    // $playlists = $stor->findAll();
    $playlists = $stor->findMany(function ($data) use ($user) {
        return $data['public'] == true;
    }) ?? [];

    $mappedPlaylists = array_map(function ($item, $index) {
        $item['index'] = $index;
        $item['tracks'] = count($item['tracks']);
        return $item;
    }, $playlists, array_keys($playlists)) ?? [];

    // My playlists
    $stor = new Storage(new JsonIO('data/playlists.json'));
    // $playlists = $stor->findAll();
    $playlists = $stor->findMany(function ($data) use ($user) {
        return strcmp($user['username'], $data['created_by']) == 0;
    });

    $mappedMyPlaylists = array_map(function ($item, $index) {
        $item['index'] = $index;
        $item['tracks'] = count($item['tracks']);
        return $item;
    }, $playlists, array_keys($playlists));
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

                    <?php if (isset($user['id'])) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="myplaylists.php">My Playlist</a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#">My Playlist</a>
                        </li>

                    <?php endif; ?>

                    <?php if ($user && $user['isAdmin']) : ?>

                        <li class="nav-item">
                            <a class="nav-link" href="admin.php" tabindex="-1" aria-disabled="true">Admin</a>
                        </li>

                    <?php endif; ?>
                </ul>


                <div class="d-flex justify-content-end pl-10 ml-10">
                    <ul class="navbar-light bg-light navbar-nav me-auto mb-2 mb-lg-0 ">

                        <?php if ($user) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><?= $user['username'] ?></a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
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
            <span>Success!</span>
        </div>
    <?php endif; ?>

    <?php if ($failure) : ?>
        <div class="alert alert-danger" role="alert">
            <span>Something went wrong.</span>
        </div>
    <?php endif; ?>

    <!--
        Modal login
    -->

    <section>
        <div>
            <!-- Username on the page -->
            <?php if ($user) : ?>
                <h1>Welcome to SongTyper :), <?= $user['username'] ?>! </h1>
            <?php else : ?>
                <h1>Welcome to SongTyper :) ! </h1>
            <?php endif; ?>

            <h5 class="fw-normal ps-3">Unlock the Rhythm of Your Perfect Playlist ... </h5>
            <p class="ps-4">Where the magic of music comes alive through your fingertips. Discover, create, and immerse yourself in the rhythm of your perfect playlist. With SongTyper, every keystroke unlocks a world of melodies, allowing you to curate a unique musical journey tailored to your tastes. Whether you're searching for upbeat tunes to energize your day or mellow melodies to soothe your soul, SongTyper has you covered. Get ready to embark on a musical adventure like never before, as you type your way to a symphony of sounds. Let the rhythm flow through your fingertips and unlock the beat of your perfect playlist on SongTyper!</p>
        </div>


        <section>
            <h2>Public playlist</h2>

            <div class="list-group">

                <?php foreach ($mappedPlaylists as $id => $d) : ?>

                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start align-items-center">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <h6 class="mb-1"><?= $d['name'] ?></h6>
                            </div>
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <?php if ($d['tracks'] > 1) : ?>
                                    <h8><?= $d['tracks'] ?> tracks</h8>
                                <?php else : ?>
                                    <h8><?= $d['tracks'] ?> track</h8>
                                <?php endif; ?>
                                <small class="text-muted text-center">Created by <?= $d['created_by'] ?></small>
                            </div>
                            <form class="d-flex flex-column align-items-center justify-content-center" method="GET" action="detail_playlist.php?" novalidate>
                                <input type="hidden" name="playlist_id" value="<?= $id ?>">
                                <button class="btn btn-outline-success me-5" type="submit">Details</button>
                            </form>
                        </div>
                    </a>

                <?php endforeach; ?>

            </div>

            <hr>

            <h2>Tracks</h2>
            <div class="row">
                <div class="d-flex md-6">
                    <input id="search_input" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success me-6" type="submit">Search</button>
                </div>
                <div class="col-md-6">
                    <label for="selectPlaylist">Select Playlist to add track</label>
                    <select id="selectPlaylist" class="form-select" aria-label="Default select example" name="playlist_id">
                        <option active value="-1">Select</option>
                        <?php foreach ($mappedMyPlaylists as $id => $d) : ?>
                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <table id="table_search" class="table">
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

                </tbody>
            </table>

            <script src="ajax.js"></script>

            <hr>

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
    <script>

    </script>
</body>

</html>