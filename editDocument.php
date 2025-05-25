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
    <title>Document</title>
</head>
<body>

    <h1>Edit Document</h1>

    <a href="index.php">Return</a>
    <br><br>

    <div class="documentContainer">
        <p id="message"></p>
        <div class="toolbar">
            <button type="button" onclick="formatText('p')">P</button>
            <button type="button" onclick="formatText('h1')">H1</button>
            <button type="button" onclick="formatText('h2')">H2</button>
            <button type="button" onclick="formatText('h3')">H3</button>
        </div>

        <form id="documentForm">
            <input type="submit" value="Save" class="saveDocumentBtn">
            <br><br>

            <label for="documentTitleInput">Title: </label>
            <input type="text" name="title" id="documentTitleInput">
            <br><br>

        <div id="documentTextInput"
             contenteditable="true"
             style="border: 1px solid #ccc; padding: 10px; min-height: 300px; width: 90%; font-family: sans-serif;">
        </div>

        </form>

        <div class="chatContainer">
            <h3>Document Chat</h3>
            <div id="chatBox" style="border: 1px solid #ccc; padding: 10px; height: 200px; overflow-y: auto;"></div>
                <form id="chatForm">
                    <input type="text" id="chatInput" placeholder="Type your message..." style="width: 80%;">
                    <button type="submit">Send</button>
                </form>
            </div>
    </div>
    
    <div class="userSearchContainer">
        <input type="text" id="userSearch" placeholder="Search users to share with...">
        <input type="hidden" id="docID" value="<?php echo htmlspecialchars($_GET['documentID']); ?>">
        <ul id="searchResults"></ul>
    </div>

    <h3>Recent Activity Logs</h3>
    <div id="logContainer">
        <p>Loading logs...</p>
    </div>



    <script src="core/script.js"></script>
</body>
</html>
