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
    <title>Login</title>
</head>
<body>
    <nav class="navbar">
        <h1 class="montserrat">GoogleDocs<span id="logo-span">CLONE</span> </h1>
    </nav>

    <div class="loginContainer">
        <div id="messageLogin"></div>
        <h2 class="montserrat">Login</h2>
        <form id="loginForm">
            <div class="loginRow">
                <label for="loginEmailInput" id="loginEmail">Email</label><br>
                <input type="text" name="email" id="loginEmailInput">
                <br><br>
            </div>
            
            <div class="loginRow">
                <label for="loginPasswordInput" id="loginPassword">Password</label><br>
                <input type="password" name="password" id="loginPasswordInput">
                <br><br>
            </div>

            <input type="submit" value="Login" class="loginBtn" name="loginUserBtn">
        </form>
        <br><br>

        <p id="register-link">Don't have an account yet? Register <a href="register.php">here</a>!</p>
    </div>

    <script src="core/script.js"></script>
</body>
</html>