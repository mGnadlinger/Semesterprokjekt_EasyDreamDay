-- 1. User-Tabelle
CREATE TABLE IF NOT EXISTS User (
                                    UserId INT AUTO_INCREMENT PRIMARY KEY,
                                    Username VARCHAR(255) NOT NULL UNIQUE,
    Phone VARCHAR(15),
    Email VARCHAR(255) NOT NULL UNIQUE,
    Role ENUM('Planner', 'Guest') NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL
    );

-- 2. Guest-Tabelle
CREATE TABLE IF NOT EXISTS Guest (
                                     GuestId INT AUTO_INCREMENT PRIMARY KEY,
                                     WeddingId INT,
                                     UserId INT,
                                     FirstName VARCHAR(255) NOT NULL,
    LastName VARCHAR(255) NOT NULL,
    Phone VARCHAR(15),
    Email VARCHAR(255),
    AdditionalText TEXT,
    RSVP ENUM('Yes', 'No', 'Pending') DEFAULT 'Pending',
    GuestGroupId INT,
    FOREIGN KEY (UserId) REFERENCES User(UserId)
    );

-- 3. Wedding-Tabelle
CREATE TABLE IF NOT EXISTS Wedding (
                                       WeddingId INT AUTO_INCREMENT PRIMARY KEY,
                                       Date DATE NOT NULL,
                                       Time TIME NOT NULL,
                                       Location1 VARCHAR(255) NOT NULL,
    Location2 VARCHAR(255) NOT NULL,
    CeremonyType ENUM('standesamtlich', 'kirchlich', 'frei') NOT NULL,
    Dresscode VARCHAR(255) NOT NULL,
    Partner1Id INT,
    Partner2Id INT,
    PlannerId INT,
    FOREIGN KEY (Partner1Id) REFERENCES Guest(GuestId),
    FOREIGN KEY (Partner2Id) REFERENCES Guest(GuestId),
    FOREIGN KEY (PlannerId) REFERENCES User(UserId)
    );

-- 4. GuestGroup-Tabelle
CREATE TABLE IF NOT EXISTS GuestGroup (
                                          GuestGroupId INT AUTO_INCREMENT PRIMARY KEY,
                                          WeddingId INT NOT NULL,
                                          Name VARCHAR(255) NOT NULL,
    FOREIGN KEY (WeddingId) REFERENCES Wedding(WeddingId)
    );

-- 5. Jetzt: Guest vervollständigen mit FOREIGN KEYS auf Wedding & GuestGroup
ALTER TABLE Guest
    ADD FOREIGN KEY (WeddingId) REFERENCES Wedding(WeddingId),
    ADD FOREIGN KEY (GuestGroupId) REFERENCES GuestGroup(GuestGroupId);

-- 6. GuestGroup_Member
CREATE TABLE IF NOT EXISTS GuestGroup_Member (
                                                 GuestGroupId INT NOT NULL,
                                                 GuestId INT NOT NULL,
                                                 PRIMARY KEY (GuestGroupId, GuestId),
    FOREIGN KEY (GuestGroupId) REFERENCES GuestGroup(GuestGroupId),
    FOREIGN KEY (GuestId) REFERENCES Guest(GuestId)
    );

-- 7. Schedule
CREATE TABLE IF NOT EXISTS Schedule (
                                        ScheduleId INT AUTO_INCREMENT PRIMARY KEY,
                                        WeddingId INT NOT NULL,
                                        Time TIME NOT NULL,
                                        EventName VARCHAR(255) NOT NULL,
    MeetingPoint VARCHAR(255),
    FOREIGN KEY (WeddingId) REFERENCES Wedding(WeddingId)
    );

-- 8. ToDo
CREATE TABLE IF NOT EXISTS ToDo (
                                    ToDoId INT AUTO_INCREMENT PRIMARY KEY,
                                    WeddingId INT NOT NULL,
                                    Name VARCHAR(255) NOT NULL,
    Date DATE NOT NULL,
    Time TIME NOT NULL,
    Done TINYINT(1) DEFAULT 0,
    FOREIGN KEY (WeddingId) REFERENCES Wedding(WeddingId)
    );

-- 9. Gift
CREATE TABLE IF NOT EXISTS Gift (
                                    GiftId INT AUTO_INCREMENT PRIMARY KEY,
                                    WeddingId INT NOT NULL,
                                    Name VARCHAR(255) NOT NULL,
    FOREIGN KEY (WeddingId) REFERENCES Wedding(WeddingId)
    );

-- 10. GiftReservation
CREATE TABLE IF NOT EXISTS GiftReservation (
                                               ReservationId INT AUTO_INCREMENT PRIMARY KEY,
                                               GiftId INT NOT NULL,
                                               GuestId INT NOT NULL,
                                               FOREIGN KEY (GiftId) REFERENCES Gift(GiftId),
    FOREIGN KEY (GuestId) REFERENCES Guest(GuestId)
    );

-- 11. Photo
CREATE TABLE IF NOT EXISTS Photo (
                                     PhotoId INT AUTO_INCREMENT PRIMARY KEY,
                                     WeddingId INT NOT NULL,
                                     Link VARCHAR(255) NOT NULL,
    Description TEXT,
    FOREIGN KEY (WeddingId) REFERENCES Wedding(WeddingId)
    );
