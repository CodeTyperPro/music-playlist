<?php
session_start();
$user = [];

$success = $_GET['success'] ?? false;
$failure = $_GET['failure'] ?? false;

if (!isset($_SESSION['count']))
    $_SESSION['count'] = 0;

$_SESSION['count']++;

if (!isset($_SESSION['user_id'])) {
    // header('location: login.php');
} else {
    include_once('storage.php');

    $stor = new Storage(new JsonIO('data/users.json'));
    $user = $stor->findById($_SESSION['user_id']);

    //echo print_r($user);
    //var_dump($user);

    $stor = new Storage(new JsonIO('data/playlists.json'));
    // $playlists = $stor->findAll();
    $playlists = $stor->findMany(function ($data) use ($user) {
        return strcmp($user['username'], $data['created_by']) == 0;
    });

    $mappedPlaylists = array_map(function ($item, $index) {
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
                        <a class="nav-link" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">My Playlist</a>
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
            <span>Playlist inserted successfully.</span>
        </div>
    <?php endif; ?>

    <?php if ($failure) : ?>
        <div class="alert alert-danger" role="alert">
            <span>Error adding new playlist.</span>
        </div>
    <?php endif; ?>

    <section>
        <section>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex justify-content-start">
                    <h2>My playlists</h2>
                </div>

                <form class="collumn d-flex col-md-5 justify-content-end justify-content-between" method="POST" action="add_playlist.php" novalidate>
                    <input type="text" class="form-control" placeholder="New playlist name" name="playlist_name">
                    <div class="form-check d-flex justify-content-between align-items-center mx-3">
                        <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked" name="is_public" checked>
                        <label class="form-check-label mx-1" for="flexCheckChecked">Public</label>
                    </div>
                    <button type="submit" class="btn btn-primary col-md-2">Add</button>
                </form>
            </div>

            <div class="list-group">
                <?php foreach ($mappedPlaylists as $id => $d) : ?>
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start align-items-center">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <h6 class="mb-1"><?= $d['name'] ?></h6>
                            </div>
                            
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <h6 class="mb-1">
                                    <?= $d['public'] ? 'Public' : 'Private' ?>
                                </h6>
                            </div>

                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <?php if ($d['tracks'] > 1) : ?>
                                    <h8><?= $d['tracks'] ?> tracks</h8>
                                <?php else : ?>
                                    <h8><?= $d['tracks'] ?> track</h8>
                                <?php endif; ?>
                                <small class="text-muted text-center"><?= $d['created_by'] ?></small>
                            </div>
                            <form class="d-flex flex-column align-items-center justify-content-center" method="POST" action="detail_myplaylist.php?playlist_id=<?=$d['id']?>" novalidate>
                                <button class="btn btn-outline-success me-5" type="submit">Details</button>
                            </form>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

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