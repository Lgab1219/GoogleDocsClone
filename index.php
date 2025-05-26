<?php  

session_start();

require_once 'core/models.php';

if (!isset($_SESSION["accountID"])){
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["suspend"]) && $_SESSION["suspend"] == 1) {
    header("Location: login.php");
    exit();
}

$accountID = $_SESSION["accountID"];
$nonAdminDocuments = getAllNonAdminDocuments($pdo, $accountID);

$adminDocuments = getAllAdminDocuments($pdo);

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
    <title>Home</title>
</head>
<body>
    <nav class="navbar">
        <h1 class="montserrat">GoogleDocs<span id="logo-span">CLONE</span></h1>
    </nav>


    <div id="lower-navbar">
        <h2 class="montserrat username">Hello, <?php echo $_SESSION['username']; ?>!</h2>
        <?php if($_SESSION['role'] == "admin"): ?>
            <a href="suspendAccounts.php" id="suspendBtn">Suspend Accounts</a><br><br>
        <?php endif; ?>
        <form action="core/logout.php" method="POST"><button type="submit" id="logoutBtn">Logout</button></form>
    </div>

    <div class="createDocumentBtnContainer">
        <button id="createDocumentBtn">+</button>
    </div>

    <div id="documentsContainer">
        <h2 class="montserrat">Your Documents</h2>
        <?php if($_SESSION['role'] == "user"): ?>
            <ul>
                <?php foreach($nonAdminDocuments as $document): ?>
                    <li class="document-item">
                        <a style="text-decoration: none;" href="editDocument.php?documentID=<?php echo $document['documentID']; ?>">
                            <h2 class="montserrat title-text"><?php echo htmlspecialchars($document['documentTitle']) ?> </h2>
                        </a>
                        <p class="montserrat">Created By: <?php echo htmlspecialchars($document['username']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <ul>
                <?php foreach($adminDocuments as $document): ?>
                    <li class="document-item">
                        <a style="text-decoration: none;" href="editDocument.php?documentID=<?php echo $document['documentID']; ?>">
                        <h2 class="montserrat title-text"><?php echo htmlspecialchars(trim($document['documentTitle']) ?: '(Untitled Document)'); ?> </h2>
                    </a>
                    <p class="montserrat">Created By: <?php echo htmlspecialchars($document['username']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script src="core/script.js"></script>
</body>
</html>