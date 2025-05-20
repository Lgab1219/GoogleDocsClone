<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <title>Registration</title>
</head>
<body>
    <div id="message"></div>
    <div class="registerContainer">
        <h2>Register</h2>
        <form id="registerForm">
            <label for="registerEmailInput" id="registerEmail">Email</label>
            <input type="text" name="email" id="registerEmailInput">
            <br><br>

            <label for="registerUsernameInput" id="registerUsername">Username</label>
            <input type="text" name="username" id="registerUsernameInput">
            <br><br>
            
            <label for="registerPasswordInput" id="registerPassword">Password</label>
            <input type="password" name="password" id="registerPasswordInput">
            <br><br>

            <label for="registerRoleInput" id="registerRole">Role</label>
            <select name="role" id="registerRoleInput">
                <option value=""></option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <br><br>

            <input type="submit" value="Register" class="registerBtn" name="registerUserBtn">
        </form>
        <br><br>

        <p>Already have an account? Log in <a href="login.php">here</a>!</p>
    </div>

    <script src="core/script.js"></script>
</body>
</html>