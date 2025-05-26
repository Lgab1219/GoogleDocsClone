<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Registration</title>
</head>
<body>
    <nav class="navbar">
        <h1 class="montserrat">GoogleDocs<span id="logo-span">CLONE</span> </h1>
    </nav>

    <div class="registerContainer">
        <div id="message"></div>
        <h2 class="montserrat">Register</h2>
        <form id="registerForm">
            <label for="registerEmailInput" id="registerEmail">Email</label><br>
            <input type="text" name="email" id="registerEmailInput">
            <br><br>

            <label for="registerUsernameInput" id="registerUsername">Username</label><br>
            <input type="text" name="username" id="registerUsernameInput">
            <br><br>
            
            <label for="registerPasswordInput" id="registerPassword">Password</label><br>
            <input type="password" name="password" id="registerPasswordInput">
            <br><br>

            <label for="registerRoleInput" id="registerRole">Role</label><br>
            <select name="role" id="registerRoleInput">
                <option value=""></option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <br><br>

            <input type="hidden" name="suspend" id="suspendInput" value="0">
            <input type="submit" value="Register" class="registerBtn" name="registerUserBtn">
        </form>
        <br><br>

        <p id="login-link">Already have an account? Log in <a href="login.php">here</a>!</p>
    </div>

    <script src="core/script.js"></script>
</body>
</html>