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
    <title>Suspend Accounts</title>
</head>
<body>
    <h1>Hello, <?php echo $_SESSION['username']; ?>!</h1>

    <h3>Suspend an account?</h3>

    <div id="accountsContainer">
        <ul>
            <?php foreach($userAccounts as $account): ?>
                <li style="list-style-type: none;">
                    <input type="checkbox" name="account" class="account" 
                    value="<?php echo htmlspecialchars($account['username']); ?>" <?php echo ($account['suspend']) ? 'checked' : ''; ?>>
                    <?php echo htmlspecialchars($account['username']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <a href="index.php">Return</a>

    <script src="core/script.js"></script>
</body>
</html>