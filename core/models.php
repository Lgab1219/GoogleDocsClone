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
    // Fetch current document first
    $sql = "SELECT documentTitle, documentText FROM documents WHERE documentID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documentID]);
    $doc = $stmt->fetch();

    if (!$doc) return false;

    // Update document if allowed
    $sql = "
        UPDATE documents
        SET documentTitle = ?, documentText = ?
        WHERE documentID = ?
          AND (
            accountID = ? OR
            EXISTS (
                SELECT 1 FROM document_access
                WHERE documentID = ? AND accountID = ?
            )
          )
    ";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$title, $text, $documentID, $accountID, $documentID, $accountID]);

    if ($success) {
        // Log title change
        if ($doc['documentTitle'] !== $title) {
            logDocumentChange($pdo, $documentID, $accountID, 'edited_title', $doc['documentTitle'], $title);
        }

        // Log text change
        if ($doc['documentText'] !== $text) {
            logDocumentChange($pdo, $documentID, $accountID, 'edited_text', $doc['documentText'], $text);
        }
    }

    return $success;
}

function logDocumentChange($pdo, $documentID, $accountID, $action, $oldValue, $newValue) {
    $sql = "INSERT INTO document_logs (documentID, accountID, action, oldValue, newValue) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documentID, $accountID, $action, $oldValue, $newValue]);
}



function getDocumentByID($pdo, $documentID, $accountID) {
    $sql = "
        SELECT d.documentTitle, d.documentText
        FROM documents d
        LEFT JOIN document_access da ON d.documentID = da.documentID
        WHERE d.documentID = ?
          AND (d.accountID = ? OR da.accountID = ?)
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documentID, $accountID, $accountID]);
    return $stmt->fetch();
}

function getAllDocuments($pdo) {
    $sql = "SELECT documentID, documentTitle, createdBy FROM documents";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getAllNonAdminDocuments($pdo, $accountID) {
    $sql = "
        SELECT d.documentID, d.documentTitle, a.username
        FROM documents d
        JOIN accounts a ON d.accountID = a.accountID
        WHERE a.role != 'admin'

        UNION

        SELECT d.documentID, d.documentTitle, a.username
        FROM document_access da
        JOIN documents d ON da.documentID = d.documentID
        JOIN accounts a ON d.accountID = a.accountID
        WHERE da.accountID = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$accountID]);
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