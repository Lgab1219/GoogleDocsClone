CREATE TABLE accounts (
    accountID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    role VARCHAR(255),
    suspend BOOLEAN
);

CREATE TABLE documents (
	documentID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT,
    documentTitle VARCHAR(255),
    documentText TEXT,
    createdBy INT
);

CREATE TABLE document_access (
    accessID INT AUTO_INCREMENT PRIMARY KEY,
    documentID INT,
    accountID INT,
    FOREIGN KEY (documentID) REFERENCES documents(documentID) ON DELETE CASCADE,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE
);
