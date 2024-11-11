-- Create User table with Barangay and Role
CREATE TABLE User (
    userID INT PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(255),
    lastName VARCHAR(255),
    contactInfo VARCHAR(255),
    email VARCHAR(255),
    address VARCHAR(255),
    barangay VARCHAR(255),
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(255) DEFAULT 'user' CHECK (role IN ('user', 'employee', 'admin'))
);

-- Create Request table with updated definition, including handledBy column
CREATE TABLE Request (
    requestID INT PRIMARY KEY AUTO_INCREMENT,
    userID INT,
    serviceType VARCHAR(255),
    requestDateTime DATETIME,
    barangay VARCHAR(255),
    status VARCHAR(255) DEFAULT 'pending',
    role VARCHAR(255) DEFAULT 'user',
    weight DECIMAL(10, 2),
    typeOfClothes VARCHAR(255),
    detergent VARCHAR(255),
    foldingStyle VARCHAR(255),
    pickupDateTime DATETIME,
    deliveryDateTime DATETIME,
    deliveryFee DECIMAL(10, 2),
    address VARCHAR(255),
    totalCost DECIMAL(10, 2),
    handledBy INT,
    FOREIGN KEY (userID) REFERENCES User(userID),
    FOREIGN KEY (handledBy) REFERENCES User(userID)
);
