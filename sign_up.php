<?php
include_once('storage.php');
if ($_POST) {
    $username       = $_POST['username']  ?? '';
    $email          = $_POST['email'] ?? '';
    $password       = $_POST['password']   ?? '';
    $isAdmin        = $_POST['isAdmin'] ?? false;

    $stor = new Storage(new JsonIo('data/users.json'));

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