CREATE TABLE accounts (
    accountID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    role VARCHAR(255)
);

CREATE TABLE documents (
	documentID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT,
    documentTitle VARCHAR(255),
    documentText TEXT,
    createdBy INT
);