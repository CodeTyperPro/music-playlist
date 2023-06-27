<?php
session_start();

$success = $_GET['success'] ?? false;
$failure = $_GET['failure'] ?? false;

if (!$_GET) {
    header('location: index.php');
    exit();
}

$playlist_id = $_GET['playlist_id'] ?? -1;

$user = [];
if (!isset($_SESSION['count']))
    $_SESSION['count'] = 0;
$_SESSION['count']++;

if (!isset($_SESSION['user_id'])) {
    //header('location: login.php');
    //exit();
    include_once('storage.php');

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

    $total_minute = array_reduce($output, fn ($carry, $item) => $carry + $item['length']);

    $tracks = array_map(function ($item, $index) {
        $item['index'] = $index;
        return $item;
    }, $output, array_keys($output));
} else {
    include_once('storage.php');

    $stor = new Storage(new JsonIO('data/users.json'));
    $user = $stor->findById($_SESSION['user_id']);

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

    $total_minute = array_reduce($output, fn ($carry, $item) => $carry + $item['length']);

    $tracks = array_map(function ($item, $index) {
        $item['index'] = $index;
        return $item;
    }, $output, array_keys($output));

    // === List Playlist === //
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

function formatArrayToString($array)
{
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
            <span>Track added to playlist.</span>
        </div>
    <?php endif; ?>

    <?php if ($failure) : ?>
        <div class="alert alert-danger" role="alert">
            <span>Track not added to the given playlist.</span>
        </div>
    <?php endif; ?>

    <section>

        <section class="ps-4">
            <h5>Playlist</h5>

            <div class="row">
                <div class="col ">
                    <h2 class="d-flex justify-content-start"><?= $playlist['name'] ?? 'Unknown' ?></h2>
                    <span>Total playing time = <?= $total_minute ?? 0 ?></span>
                </div>
                <div class="col">
                    <label for="selectPlaylist">Select Playlist to add track</label>
                    <select id="selectPlaylist" class="form-select" aria-label="Default select example" name="playlist_id">
                        <?php foreach ($mappedPlaylists as $id => $d) : ?>
                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

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
                            <th scope="row"><?= intval($d['index']) + 1 ?></th>
                            <td><?= $d['title'] ?></td>
                            <td><?= $d['artist'] ?></td>
                            <td><?= $d['length'] ?></td>
                            <td><?= $d['year'] ?></td>
                            <td><?= formatArrayToString($d['genres']) ?></td>
                            <td>
                                <form method="POST" action="add_to_playlist.php" novalidate>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                        <ul class="dropdown-menu">
                                            <input type="hidden" id="selectedId" name="playlist_id" value="-1">
                                            <input type="hidden" name="track_id" value="<?= $d['id'] ?>">
                                            <input type="hidden" name="current_playlist_id" value="<?= $playlist_id ?>">
                                            <li>
                                                <button type="submit" class="dropdown-item btn btn-light">Add to my playlist</button>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item" href="#">Separated link</a></li>
                                        </ul>
                                    </div>

                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
            </form>

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
        var selectElement = document.getElementById("selectPlaylist");
        var destElement = document.querySelectorAll('input[id=selectedId]');

        destElement.forEach(function(input) {
                input.value = selectElement.options[selectElement.selectedIndex].value;
        });

        selectElement.addEventListener("change", function() {

            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var selectedValue = selectedOption.value;
            var selectedText = selectedOption.text;

            // Perform desired actions with the selected value or text
            console.log("Selected value: " + selectedValue);
            console.log("Selected text: " + selectedText);

            destElement.forEach(function(input) {
                input.value = selectedValue;
            });

        });
    </script>
</body>

</html>