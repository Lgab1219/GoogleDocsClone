<?php

require_once "dbconfig.php";

function registerAccount($pdo, $email, $username, $password, $role, $suspend) {
    $sqlCheck = "SELECT * FROM accounts WHERE email = ?";
    $stmt = $pdo->prepare($sqlCheck);
    $stmt -> execute([$email]);

    if($stmt -> rowCount() == 0) {
        $sqlInsert = "INSERT INTO accounts (email, username, password, role, suspend)
                      VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sqlInsert);
        $query = $stmt -> execute([$email, $username, $password, $role, $suspend]);

        if($query) {
            return true;
        } else {
            return false;
        }
    }
};

function loginAccount($pdo, $email, $password) {
    $sql = "SELECT * FROM accounts WHERE email = ?";
    $stmt = $pdo->prepare($sql);

    if($stmt -> execute([$email]) && $stmt -> rowCount() > 0) {
        $accountInfo = $stmt -> fetch();
        if(password_verify($password, $accountInfo["password"])) {
            return $accountInfo;
        }
    } else {
        return false;
    }
};

function getAllNonAdminAccounts($pdo) {
    $sql = "SELECT * FROM accounts WHERE role = 'user'";
    $stmt = $pdo->prepare($sql);
    $stmt -> execute();
    return $stmt -> fetchAll();
}

function createDocument($pdo, $accountID, $title, $text) {
    $sql = "INSERT INTO documents (accountID, documentTitle, documentText, createdBy)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$accountID, $title, $text, $accountID]);

    if ($result) {
        return $pdo->lastInsertId();
    } else {
        return false;
    }
};

function updateDocument($pdo, $documentID, $title, $text, $accountID) {
    $sql = "UPDATE documents SET documentTitle = ?, documentText = ? WHERE documentID = ? AND accountID = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$title, $text, $documentID, $accountID]);
}

function getDocumentByID($pdo, $documentID, $accountID) {
    $sql = "SELECT documentTitle, documentText FROM documents WHERE documentID = ? AND accountID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documentID, $accountID]);
    return $stmt->fetch();
}

function getAllDocuments($pdo) {
    $sql = "SELECT documentID, documentTitle, createdBy FROM documents";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getAllNonAdminDocuments($pdo) {
    $sql = "SELECT d.documentID, d.documentTitle, a.username
            FROM documents d
            JOIN accounts a ON d.accountID = a.accountID
            WHERE a.role != 'admin'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getAllAdminDocuments($pdo) {
    $sql = "SELECT d.documentID, d.documentTitle, a.username
            FROM documents d
            JOIN accounts a ON d.accountID = a.accountID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}