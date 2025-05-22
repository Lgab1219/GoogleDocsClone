<?php

session_start();

require_once "dbconfig.php";
require_once "models.php";

header("Content-Type: application/json");

if (isset($_POST["registerAccount"])) {
    $email = $_POST["registerEmailInput"];
    $username = $_POST["registerUsernameInput"];
    $password = $_POST["registerPasswordInput"];
    $role = $_POST["registerRoleInput"];
    $suspend = isset($_POST["suspendInput"]) ? (int) $_POST["suspendInput"] : 0;


    // Validate empty fields
    if (empty($email) || empty($username) || empty($password || empty($role))) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit();
    }

    // Password validation
    if (
        strlen($password) < 5 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^a-zA-Z0-9\s]/', $password)
    ) {
        // JSON for password validation
        echo json_encode([
            'status' => 'error',
            'message' => 'Password must be at least 5 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
        ]);
        exit();
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $insertQuery = registerAccount($pdo, $email, $username, $password, $role, $suspend);

    if ($insertQuery) {
        echo json_encode([
            'status' => 'success',
            'redirect' => 'login.php'
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred during registration. Please try again.'
        ]);
        exit();
    }
}

if(isset($_POST['loginAccount'])) {
    $email = $_POST['loginEmailInput'];
    $password = $_POST['loginPasswordInput'];

    if(!empty($email) || !empty($password)) {
        $loginQuery = loginAccount($pdo, $email, $password);

        if($loginQuery){
            $usernameDB = $loginQuery['username'];
            $accountIDDB = $loginQuery['accountID'];
            $roleDB = $loginQuery['role'];
            $suspend = $loginQuery['suspend'];
            $_SESSION['username'] = $usernameDB;
            $_SESSION['accountID'] = $accountIDDB;
            $_SESSION['role'] = $roleDB;
            $_SESSION['suspend'] = $suspend;

            echo json_encode([
                'status' => 'success',
                'redirect' => 'index.php'
            ]);
            exit();
        } else {
            echo json_encode([
                'status'=> 'error',
                'message'=> 'Incorrect email or password!'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit();
    }
}

if (isset($_GET['getAllNonAdminAccounts'])) {

    if(!$_SESSION['accountID']){
        echo json_encode([
            'status'=> 'error',
            'message'=> 'No accounts logged in!'
        ]);
        exit();
    }

    $accounts = getAllNonAdminAccounts($pdo);

    echo json_encode([
        'status' => 'success',
        'accounts' => $accounts
    ]);
    exit();
}

if (isset($_POST["toggleSuspend"])) {
    $username = $_POST["username"];
    $suspend = $_POST["suspend"];

    $sql = "UPDATE accounts SET suspend = ? WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $updated = $stmt->execute([$suspend, $username]);

    if ($updated) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Suspend status updated.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update suspend status.'
        ]);
    }
    exit();
}



if (isset($_POST['createDocument'])) {

    $accountID = $_SESSION['accountID'] ?? null;

    if (!$accountID) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Not logged in.'
        ]);
        exit();
    }

    $title = "";
    $text = "";

    $documentID = createDocument($pdo, $accountID, $title, $text);

    if ($documentID) {
        echo json_encode([
            'status' => 'success',
            'documentID' => $documentID
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create document.'
        ]);
    }

    exit();
}

if (isset($_POST['updateDocument'])) {
    $documentID = $_POST['documentID'] ?? null;
    $title = $_POST['title'] ?? '';
    $text = $_POST['text'] ?? '';
    $accountID = $_SESSION['accountID'] ?? null;

    if (!$accountID || !$documentID) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid session or document ID.'
        ]);
        exit();
    }

    $updateSuccess = updateDocument($pdo, $documentID, $title, $text, $accountID);

    if ($updateSuccess) {
        echo json_encode([
            'status' => 'success'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update document.'
        ]);
    }

    exit();
}

if (isset($_GET['getDocument'])) {
    $documentID = $_GET['documentID'] ?? null;
    $accountID = $_SESSION['accountID'] ?? null;

    if (!$documentID || !$accountID) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid document ID or not logged in.'
        ]);
        exit();
    }

    $document = getDocumentByID($pdo, $documentID, $accountID);

    if ($document) {
        echo json_encode([
            'status' => 'success',
            'document' => $document
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Document not found.',
            'redirect' => 'index.php'
        ]);
    }

    exit();
}

if (isset($_GET['getAllDocuments'])) {

    if(!$_SESSION['accountID']){
        echo json_encode([
            'status'=> 'error',
            'message'=> 'No accounts logged in!'
        ]);
        exit();
    }

    $documents = getAllDocuments($pdo);

    echo json_encode([
        'status' => 'success',
        'documents' => $documents
    ]);
    exit();
}

if (isset($_GET['searchUsers'])) {
    $query = $_GET['query'];
    
    $sql = "SELECT accountID, username FROM accounts 
            WHERE username LIKE ? AND role = 'user' AND suspend = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$query%"]);
    $results = $stmt->fetchAll();

    echo json_encode($results);
    exit();
}

if (isset($_POST['grantAccess'])) {
    $documentID = $_POST['documentID'];
    $accountID = $_POST['accountID'];

    $sql = "INSERT IGNORE INTO document_access (documentID, accountID) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$documentID, $accountID]);

    echo json_encode([
        'status' => $success ? 'success' : 'error'
    ]);
    exit();
}
