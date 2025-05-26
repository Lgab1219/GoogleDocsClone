<?php
session_start();

require_once "core/dbconfig.php";

if (!isset($_SESSION["accountID"])) {
    header("Location: login.php");
    exit();
}

// This checks if your account is suspended or not
if (isset($_SESSION["suspend"]) && $_SESSION["suspend"] == 1) {
    header("Location: login.php");
    exit();
}

$documentID = $_GET['documentID'] ?? null;

// This fetches the logs on the activity logs table
$stmt = $pdo->prepare("
    SELECT l.*, a.username
    FROM document_logs l
    JOIN accounts a ON l.accountID = a.accountID
    WHERE l.documentID = ?
    ORDER BY l.timestamp DESC
");
$stmt->execute([$documentID]);
$logs = $stmt->fetchAll();
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
    <title>Document</title>
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
        <input type="submit" value="Save" class="saveDocumentBtn montserrat">
        <form action="core/logout.php" method="POST"><button type="submit" id="logoutBtn">Logout</button></form>
    </div>

    <div class="document-desc">
        <h1 class="montserrat">Edit Document</h1>
        <p id="message"></p>
        <div class="toolbar">
            <button type="button" onclick="formatText('p')">P</button>
            <button type="button" onclick="formatText('h1')">H1</button>
            <button type="button" onclick="formatText('h2')">H2</button>
            <button type="button" onclick="formatText('h3')">H3</button>
        </div>
    </div>

    <div class="documentContainer">
        <form id="documentForm">
            <label for="documentTitleInput" class="montserrat">Title: </label>
            <input type="text" name="title" id="documentTitleInput"><br><br>
            <div id="documentTextInput"
                 contenteditable="true"
                 style="border: 1px solid #ccc; padding: 10px; min-height: 300px; width: 90%; font-family: sans-serif;">
            </div>
        </form>

        <div class="chatContainer">
            <h3 class="montserrat">Document Chat</h3>
            <div id="chatBox" style="border: 1px solid #ccc; padding: 10px; height: 180px; overflow-y: auto;"></div><br>
                <form id="chatForm">
                    <input type="text" id="chatInput" placeholder="Type your message..." style="width: 80%;"><br><br>
                    <button type="submit" class="chatBtn">Send</button>
                </form>
        
                <br>
        
            <div class="userSearchContainer">
                <input type="text" id="userSearch" placeholder="Search users to share with...">
                <input type="hidden" id="docID" value="<?php echo htmlspecialchars($_GET['documentID']); ?>">
                <ul id="searchResults"></ul>
            </div>
        </div>
    </div>

    <div class="recent-activity">
        <h1 class="montserrat">Recent Activity Logs</h1>
        <div id="logContainer">
            <p>Loading logs...</p>
        </div>
    </div>



    <script src="core/script.js"></script>
</body>
</html>
