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


$nonAdminDocuments = getAllNonAdminDocuments($pdo);
$adminDocuments = getAllAdminDocuments($pdo);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <title>Home</title>
</head>
<body>
    <h1>Hello, <?php echo $_SESSION['username']; ?>!</h1>
   
    <?php if($_SESSION['role'] == "admin"): ?>
        <a href="suspendAccounts.php">Suspend Accounts</a><br><br>
    <?php endif; ?>

    <button id="createDocumentBtn">Create a new document</button>

    <br><br>

    <div id="documentsContainer">
        <h2>Your Documents</h2>
        <?php if($_SESSION['role'] == "user"): ?>
            <ul>
                <?php foreach($nonAdminDocuments as $document): ?>
                    <li>
                        <a href="editDocument.php?documentID=<?php echo $document['documentID']; ?>">
                            <p><?php echo htmlspecialchars($document['documentTitle']) ?> </p>
                        </a>
                        <p>Created By: <?php echo htmlspecialchars($document['username']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <ul>
                <?php foreach($adminDocuments as $document): ?>
                    <li>
                    <a href="editDocument.php?documentID=<?php echo $document['documentID']; ?>">
                        <p><?php echo htmlspecialchars($document['documentTitle']) ?> </p>
                    </a>
                    <p>Created By: <?php echo htmlspecialchars($document['username']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <br><br>

    <form action="core/logout.php" method="POST"><button type="submit">Logout</button></form>

    <script src="core/script.js"></script>
</body>
</html>