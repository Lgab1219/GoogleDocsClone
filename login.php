<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <title>Login</title>
</head>
<body>
    <div id="messageLogin"></div>
    <div class="loginContainer">
        <h2>Login</h2>
        <form id="loginForm">
            <label for="loginEmailInput" id="loginEmail">Email</label>
            <input type="text" name="email" id="loginEmailInput">
            <br><br>
            
            <label for="loginPasswordInput" id="loginPassword">Password</label>
            <input type="password" name="password" id="loginPasswordInput">
            <br><br>

            <input type="submit" value="Login" class="loginBtn" name="loginUserBtn">
        </form>
        <br><br>

        <p>Don't have an account yet? Register <a href="register.php">here</a>!</p>
    </div>

    <script src="core/script.js"></script>
</body>
</html>