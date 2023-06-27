<?php

session_start();

$success = $_GET['success'] ?? false;
$failure = $_GET['failure'] ?? false;

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// For testing reasons
/*echo "<h1>Hi!</h1>";*/
//echo password_hash("normal_userpwd", PASSWORD_DEFAULT)."\n";
//echo '';
$errors = [];

$error = false;
if ($_POST) {

    if ($username === '')
        $errors['username'] =  "The name is required!";

    if ($password === '')
        $errors['password'] =  "Password is required!";

    include_once('storage.php');
    $stor = new Storage(new JsonIO('data/users.json'));

    $user = $stor->findOne(['username' => $username]);
    if (!$user) {
        // error: no such user
        $error = true;
    } else {
        //echo print_r($user);
        if (!password_verify($password, $user['password'])) {
            // error: password mismatch
            $error = true;
        } else {
            // sucessfull login
            //echo "In!";
            $_SESSION['user_id'] = $user['id'];
            header('location: index.php');
            exit();
        }
    }

    $errors = array_map(fn ($e) => 
    "<div class='alert alert-danger' role='alert'>
            <span>$e</span>
        </div>
    ", $errors);
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

                </ul>

                <div class="d-flex justify-content-end pl-10 ml-10">
                    <ul class="navbar-light bg-light navbar-nav me-auto mb-2 mb-lg-0 ">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <?php if ($error) : ?>
        <div class="alert alert-danger" role="alert">
            <span>Invalid username or password.</span>
        </div>
    <?php endif; ?>

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

    <?php foreach($errors as $x):  ?>
        <?= $x ?? '' ?>
    <?php endforeach; ?>

    <!-- Modal -->
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">Login to SongTyper</h5>
            </div>
            <div class="modal-body">

                <!-- Form starts here -->
                <form action="login.php" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" aria-describedby="UsernameHelp" placeholder="Username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="check">
                        <label class="form-check-label" for="check">Remember me</label>
                    </div>
                    <div class="mb-3 d-flex flex-column align-items-center">
                        <button type="submit" class="btn btn-primary">Log In</button>
                        <a class="pt-3" href="forgot_password.php">Forgot your password?</a>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <small class="mx-1">Don't have an account?</small> <a href="register.php">Sign up for SongTyper</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


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