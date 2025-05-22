<?php
session_start();

if (!isset($_SESSION["accountID"])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["suspend"]) && $_SESSION["suspend"] == 1) {
    header("Location: login.php");
    exit();
}

$documentID = $_GET['documentID'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <title>Document</title>
</head>
<body>

    <style>
    .toolbar button {
        margin-right: 5px;
        padding: 5px 10px;
        font-size: 14px;
        cursor: pointer;
    }
    #documentTextInput h1, h2, h3, p {
        margin: 10px 0;
    }
    </style>

    <h1>Edit Document #<?php echo htmlspecialchars($documentID); ?></h1>

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
    </div>

    <div>
        <input type="text" id="userSearch" placeholder="Search users to share with...">
        <input type="hidden" id="docID" value="<?php echo htmlspecialchars($_GET['documentID']); ?>">
        <ul id="searchResults"></ul>
    </div>

    <script src="core/script.js"></script>
</body>
</html>
