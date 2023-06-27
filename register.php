<?php
include_once('storage.php');

$errors = [];
$error = false;

if ($_POST) {
    $username       = $_POST['username']  ?? '';
    $email          = $_POST['email'] ?? '';
    $password       = $_POST['password']   ?? '';
    $confirm_password = $_POST['confirm_password']   ?? '';
    $isAdmin        = $_POST['isAdmin'] ?? false;

    // Validation
    if ($username === '') {
        $errors['username'] =  "The name is required!";
        $error = true;
    }

    if ($password === '') {
        $errors['password'] =  "Password is required!";
        $error = true;
    }
    
    if ($email === '' || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email is required!";
        $error = true;
    }

    if (strcmp($password, $confirm_password) !== 0) {
        $errors['password'] = "Invalid password!";
        $error = true; 
    }

    if ($error === false) {
        $stor = new Storage(new JsonIO('data/users.json'));

        $data['username'] = $username;

        $res = $stor->findAll($data);

        var_dump($res);

        if(count($res) > 0) {
            header('location: login.php?failure=true');
            exit();
        }

        // don't forget to do your validations here on the exam :)
        $stor->add([
            'username'  => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'isAdmin'   => false,
        ]);

        header('location: login.php?success=true');
        exit();        
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
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Register</a>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
    </nav>


    <?php foreach($errors as $x):  ?>
        <?= $x ?? '' ?>
    <?php endforeach; ?>

    <!--
        Modal login
    -->
    <!--
        Registration form
    -->
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">Sign Up for free to SongTyper</h5>
            </div>
            <div class="modal-body">
                <!-- Form starts here -->
                <form method="POST" action="register.php" novalidate><!--
                        <div class="mb-3">
                            <label for="InputName" class="form-label">What's your name?</label>
                            <input type="text" class="form-control" id="InputName" aria-describedby="nameHelp" placeholder="What's your name?" name="name">
                        </div>-->
                    <div class="mb-3">
                        <label for="InputUsername" class="form-label">What's your username?</label>
                        <input type="email" class="form-control" id="InputUsername" aria-describedby="UsernameHelp" placeholder="What's your username?" name="username" value="<?= $username ?? ''?>">
                    </div>

                    <div class="mb-3">
                        <label for="InputEmail" class="form-label">What's your email?</label>
                        <input type="email" class="form-control" id="InputEmail" aria-describedby="emailHelp" placeholder="What's your email?" name="email" value="<?= $email ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="InputPassword1" class="form-label">Create a password</label>
                        <input type="password" class="form-control" id="InputPassword1" placeholder="Create a password" name="password" value="<?= $password ?? ''?>">
                    </div>
                    <div class="mb-3">
                        <label for="InputPassword2" class="form-label">Enter password again</label>
                        <input type="password" class="form-control" id="InputPassword2" placeholder="Enter password again" name="confirm_password">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="CheckTerms">
                        <label class="form-check-label" for="Check1">I agree to the</label> <a href="#">SongTyper terms and conditions of use</a>.
                    </div>
                    <div class="mb-3 d-flex flex-column align-items-center">
                        <button type="submit" id="submitButton" class="btn btn-primary">Sign Up</button>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <small class="mx-1">Have an account?</small> <a href="login.php">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const checkbox = document.querySelector('#CheckTerms');
        const submitButton = document.querySelector('#submitButton');

        submitButton.disabled = true;
        
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>