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
        header('location: admin.php?failure=true');
        exit();
    }

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
    }
}      

function splitStringByComma($string)
{
    $array = explode(',', $string);
    return $array;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.0/lottie.min.js"> </script>
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
            <span>Track updated successfully.</span>
        </div>
    <?php endif; ?>

    <section>
        <section class="ps-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex justify-content-start">
                    <h2>Edit track</h2>
                </div>
            </div>

            <div class="modal-body">

                <!-- Form starts here -->
                <form method="POST" action="edit_track.php" class="row g-4 modal-content p-3" novalidate>
                    <div class="container">
                            <div class="col">
                                <div>
                                    <label for="InputTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="InputTitle" name="title" aria-describedby="InputTitleHelp" placeholder="Title" value="<?= $title ?>">
                                </div>

                                <div>
                                    <label for="InputArtist" class="form-label">Artist</label>
                                    <input type="text" class="form-control" id="InputArtist" name="artist" aria-describedby="InputArtistHelp" placeholder="Artist" value="<?= $artist ?>">
                                </div>

                                <div>
                                    <label for="InputLength" class="form-label">Length</label>
                                    <input type="text" class="form-control" id="InputLength" name="length" aria-describedby="InputLengthHelp" placeholder="Length" value="<?= $length ?>">
                                </div>

                                <div>
                                    <label for="InputGenres" class="form-label">Genres</label>
                                    <input type="text" class="form-control" id="InputGenres" name="genres" aria-describedby="InputGenresHelp" placeholder="Genres" value="<?= $genres ?>">
                                </div>

                                <div>
                                    <label for="InputYear" class="form-label">Year</label>
                                    <input type="text" class="form-control" id="InputYear" aria-describedby="InputYearHelp" name="year" placeholder="Year" value="<?= $year ?>">
                                </div>

                                <input type="hidden" name="track_id" value="<?= $track_id ?>">

                            </div>
                    </div>

                    <div class="d-flex justify-content-center w-40 pl-100 pr-100">
                        <button type="submit" class="btn btn-primary w-50">Edit track</button>
                    </div>

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
        var animation = bodymovin.loadAnimation({
            container: document.getElementById('animation-container'),
            path: 'src/listening-music.json',
            renderer: 'svg',
            loop: true,
            autoplay: true,
            name: "Demo Animation"
        });

        var myModal = document.getElementById('editModal');
        var myInput = document.getElementById('myInput');

        myModal.addEventListener('shown.bs.modal', function() {
            myModal.focus();
        });
    </script>
</body>

</html>