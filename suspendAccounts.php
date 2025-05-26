<?php  

session_start();

require_once 'core/models.php';

if (!isset($_SESSION["accountID"]) && $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["suspend"]) && $_SESSION["suspend"] == 1) {
    header("Location: login.php");
    exit();
}

$userAccounts = getAllNonAdminAccounts($pdo);

?>

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
    <title>Suspend Accounts</title>
</head>
<body>
    <nav class="navbar">
        <h1 class="montserrat">GoogleDocs<span id="logo-span">CLONE</span></h1>
    </nav>

    <div id="lower-navbar">
        <h2 class="montserrat username">Hello, <?php echo $_SESSION['username']; ?>!</h2>
        <?php if($_SESSION['role'] == "admin"): ?>
            <a href="index.php" id="suspendBtn">Return</a><br><br>
        <?php endif; ?>
        <form action="core/logout.php" method="POST"><button type="submit" id="logoutBtn">Logout</button></form>
    </div>

    
    <div id="accountsContainer">
        <h1 class="montserrat">Suspend an account?</h1>
        <ul>
            <?php foreach($userAccounts as $account): ?>
                <li style="list-style-type: none;" class="montserrat">
                    <input type="checkbox" name="account" class="account" 
                    value="<?php echo htmlspecialchars($account['username']); ?>" <?php echo ($account['suspend']) ? 'checked' : ''; ?>>
                    <?php echo htmlspecialchars($account['username']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="core/script.js"></script>
</body>
</html>