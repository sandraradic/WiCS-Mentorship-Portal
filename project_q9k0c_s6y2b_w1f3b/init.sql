-- DROPPING TABLES AT BEGINNING OF EVERY INIT SO THERE IS NO REDUNDANT INFO
DROP TABLE Match;
DROP TABLE WorkExperienceDuration;
DROP TABLE BasedIn;
DROP TABLE Attends;
DROP TABLE WithinField;
DROP TABLE InterestedIn;
DROP TABLE WorkPay;
DROP TABLE Mentor;
DROP TABLE Mentee;
DROP TABLE Person;
DROP TABLE PotentialCareer;
DROP TABLE Major;
DROP TABLE Country;
DROP TABLE Industry;
DROP TABLE WorkPlace;
DROP TABLE PostalCode;
DROP TABLE SponsoredEvent;
DROP TABLE Room;
DROP TABLE Sponsor;
-- DROP TABLE 


-- NECESSARY TABLES (ADAPTED FROM M2)
CREATE TABLE Person(
    pid INTEGER PRIMARY KEY,
    email CHAR(40), 
    firstName CHAR(20), 
    lastName CHAR(20), 
    year INTEGER, 
    genderPreference CHAR(10), 
    gender CHAR(10), 
    degree CHAR(10)
);

CREATE TABLE Mentor(
	pid INTEGER PRIMARY KEY,
    major CHAR(20),
    FOREIGN KEY (pid) REFERENCES Person(pid)
        ON DELETE CASCADE
);

CREATE TABLE Mentee(
	pid INTEGER PRIMARY KEY,
    FOREIGN KEY (pid) REFERENCES Person(pid) 
        ON DELETE CASCADE
);

CREATE TABLE WorkExperienceDuration ( 
    pid INTEGER,
	company CHAR(40),
	duration INTEGER, 
	PRIMARY KEY(pid, company),
	FOREIGN KEY (pid) REFERENCES Mentor
        -- ON UPDATE CASCADE -- commented out cause it gives error in sqlplus
		ON DELETE CASCADE
);

CREATE TABLE WorkPay (
    pid INTEGER, 
	company CHAR(40),
	jobTitle CHAR(40), 
	salary INTEGER, 
	PRIMARY KEY(pid, company, jobTitle),
	FOREIGN KEY (pid) REFERENCES Mentor
    -- ON UPDATE CASCADE 
		ON DELETE CASCADE
);

CREATE TABLE Match(
    mentorID INTEGER NOT NULL, 
    menteeID INTEGER NOT NULL,
    PRIMARY KEY(mentorID, menteeID), 
    FOREIGN KEY (menteeID) REFERENCES Mentee(pid)
        -- ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY(mentorID) REFERENCES Mentor(pid)
    --     -- ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE Major(
    faculty CHAR(40),
    majorName CHAR(40),
    PRIMARY KEY(faculty, majorName)
);

CREATE TABLE Country(
	country CHAR(30),
	city CHAR(30),
    province CHAR(30),
    PRIMARY KEY(city, province)
);

CREATE TABLE PostalCode (
	postalCode CHAR(6) PRIMARY KEY,
    city CHAR(30),
    province CHAR(30)
);

CREATE TABLE WorkPlace (
	postalCode CHAR(6) PRIMARY KEY,
	platform CHAR(30) -- changed from mode to platform
); 

CREATE TABLE Industry(
    industryName CHAR(40) PRIMARY KEY
);

CREATE TABLE Room(
    roomNumber INTEGER,
    capacity INTEGER,
    addressMain CHAR(40), 
    floorNumber INTEGER,
    PRIMARY KEY(roomNumber, addressMain)
);

CREATE TABLE Sponsor(
	sponsorName CHAR(30) PRIMARY KEY,
	repName CHAR(30),
	repEmail CHAR(30)
);

CREATE TABLE PotentialCareer (
	jobTitle CHAR(20) PRIMARY KEY
);


CREATE TABLE InterestedIn(
    pid INTEGER, 
    faculty CHAR(40),
    majorName CHAR(40),
    jobTitle  CHAR(20),
    PRIMARY KEY (pid, faculty, majorName),
    FOREIGN KEY (pid) REFERENCES Person (pid)
        -- ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY(faculty, majorName) REFERENCES Major(faculty, majorName),
        -- ON UPDATE CASCADE
        -- ON DELETE CASCADE,
    FOREIGN KEY(jobTitle) REFERENCES PotentialCareer(jobTitle)
        -- ON DELETE CASCADE
       -- ON UPDATE CASCADE
);

CREATE TABLE WithinField(
	industryName CHAR(40),
    pid INTEGER,
	company CHAR(40),
	jobTitle CHAR(40),
    PRIMARY KEY(industryName, pid, company, jobTitle),
    FOREIGN KEY (industryName) REFERENCES Industry(industryName)
        ON DELETE CASCADE,
    --ON UPDATE CASCADE,
    FOREIGN KEY (pid, company, jobTitle) REFERENCES WorkPay(pid, company, jobTitle)
	    ON DELETE CASCADE
);

CREATE TABLE SponsoredEvent ( 
    eventName CHAR(40),
    eventDate CHAR(40),
	sponsorName CHAR(30),
	addressMain CHAR(40),
	roomNumber INTEGER,
    PRIMARY KEY(eventName, eventDate),
    FOREIGN KEY (sponsorName) REFERENCES Sponsor(sponsorName)
        ON DELETE CASCADE,
        --ON UPDATE CASCADE,
    FOREIGN KEY (addressMain, roomNumber) REFERENCES Room(addressMain, roomNumber)
        ON DELETE CASCADE
        --ON UPDATE CASCADE
);


CREATE TABLE Attends(
    pid INTEGER,
    eventName CHAR(40),
    eventDate CHAR(40),
    PRIMARY KEY (pid, eventName, eventDate),
    FOREIGN KEY (eventName, eventDate) REFERENCES SponsoredEvent(eventName, eventDate)
        --ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (pid) REFERENCES Person(pid)
    --ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- fix
CREATE TABLE BasedIn(
	postalCode CHAR(6),
    platform CHAR(10),
    pid INTEGER,
    company CHAR(40),
	jobTitle CHAR(40),
	PRIMARY KEY (postalCode, platform, company, jobTitle, pid),
	-- FOREIGN KEY (postalCode) REFERENCES WorkPlace(postalCode)
	-- 	ON DELETE CASCADE,
        -- ON UPDATE CASCADE,
	-- FOREIGN KEY (company, jobTitle, pid) REFERENCES WorkPay (company, jobTitle, pid)
    --     ON DELETE CASCADE
    FOREIGN KEY (pid, company, jobTitle) REFERENCES WorkPay (pid, company, jobTitle)
        ON DELETE CASCADE
        -- ON UPDATE CASCADE
);


-- PERSON INSERT
INSERT INTO Person(pid, email, firstName, lastName, year, genderPreference, gender, degree)
    VALUES (1, 'billsmith@student.com','bill', 'smith', 1, 'male', 'male', 'BComm');
INSERT INTO Person VALUES (2, 'carlysmith@student.com','carly', 'smith', 2, 'female', 'male', 'BA');
INSERT INTO Person VALUES (3, 'sandrabullock@student.com','sandra', 'bullock', 3, 'female', 'female', 'BSc');
INSERT INTO Person VALUES (4, 'sarahpaulson@student.com','sarah', 'paulson', 3, 'female', 'female', 'BSc');
INSERT INTO Person VALUES (5, 'carinacostanza@student.com','carina', 'costanza', 4, 'female', 'female', 'BSc');
INSERT INTO Person VALUES (6, 'temp@student.com','stella', 'costanza', 4, 'female', 'female', 'BSc');
INSERT INTO Person VALUES (7, 'anon@student.com','bella', 'costanza', 4, 'female', 'female', 'BSc');
INSERT INTO Person VALUES (8, 'athenacostanza@student.com','athena', 'costanza', 4, 'female', 'female', 'BSc');
INSERT INTO Person VALUES (9, 'violetcostanza@student.com','violet', 'costanza', 4, 'female', 'female', 'BSc');
INSERT INTO Person VALUES (10, 'olacostanza@student.com','violet', 'costanza', 4, 'female', 'female', 'BSc');

-- MENTOR INSERT
INSERT INTO Mentor(pid, major)
    VALUES(1, 'BUCS');
INSERT INTO Mentor VALUES (2, 'Cognitive Sciences');
INSERT INTO Mentor VALUES (3, 'Computer Science');
INSERT INTO Mentor VALUES (4, 'Computer Science');
INSERT INTO Mentor VALUES (5, 'Computer Science');

-- MENTEE INSERT

INSERT INTO Mentee(pid)
    VALUES(6);
INSERT INTO Mentee VALUES (7);
INSERT INTO Mentee VALUES (8);
INSERT INTO Mentee VALUES (9);
INSERT INTO Mentee VALUES (10);

-- WORKEXP DURATION INSERT

INSERT INTO WorkExperienceDuration(pid, company, duration)
    VALUES(1, 'SAP', 8);
INSERT INTO WorkExperienceDuration VALUES (2, 'Stripe',10);
INSERT INTO WorkExperienceDuration VALUES (3, 'Teck', 4);
INSERT INTO WorkExperienceDuration VALUES (4, 'Amazon', 16);
INSERT INTO WorkExperienceDuration VALUES (5, 'Google', 8);

-- WORKPAY INSERT
INSERT INTO WorkPay(pid, company, jobTitle, salary)
    VALUES(1, 'SAP', 'Business Analyst', 100000);
INSERT INTO WorkPay VALUES (2, 'Stripe', 'Recruiter', 98000);
INSERT INTO WorkPay VALUES (3, 'Teck', 'Data Analyst', 270000);
INSERT INTO WorkPay VALUES  (4, 'Amazon', 'Project Manager', 345000);
INSERT INTO WorkPay VALUES (5,  'Google', 'Software Engineer', 400000); 

-- MATCH INSERT
INSERT INTO Match(mentorID, menteeID)
    VALUES(1,6);
INSERT INTO Match VALUES (2,7);
INSERT INTO Match VALUES (3,8);
INSERT INTO Match VALUES (4,9);
INSERT INTO Match VALUES(5,10);

-- MAJOR INSERT
INSERT INTO Major(faculty, majorName)
    VALUES('Science', 'Computer Science');
INSERT INTO Major VALUES('Science', 'Statistics');
INSERT INTO Major VALUES('Arts', 'Computer Science');
INSERT INTO Major VALUES('Science', 'Math');
INSERT INTO Major VALUES('Business', 'Computer Science');

-- COUNTRY INSERT
INSERT INTO Country(country, city, province)
    VALUES('Canada', 'British Columbia', 'Vancouver');
INSERT INTO Country VALUES('Canada', 'British Columbia', 'Victoria');
INSERT INTO Country VALUES('Canada', 'Alberta', 'Edmonton');
INSERT INTO Country VALUES('USA', 'Washington', 'Seattle');
INSERT INTO Country VALUES('USA', 'California', 'Los Angeles');

-- POSTAL CODE INSERT
INSERT INTO PostalCode(postalCode, city, province)
    VALUES('V5H6U3', 'Vancouver', 'British Columbia');
INSERT INTO PostalCode VALUES('V4K8K9', 'Vancouver', 'British Columbia');
INSERT INTO PostalCode VALUES('V8H9I0', 'Vancouver', 'British Columbia');
INSERT INTO PostalCode VALUES('C95H63', 'Edmonton', 'Alberta');
INSERT INTO PostalCode VALUES('B7H8J9', 'Victoria', 'British Columbia');

-- WORKPLACE INSERT

INSERT INTO WorkPlace(postalCode, platform)
    VALUES('V5H6U3', 'online');
INSERT INTO WorkPlace VALUES('V4K8K9', 'in person');
INSERT INTO WorkPlace VALUES('V8H9I0', 'in person');
INSERT INTO WorkPlace VALUES('V5H8U3', 'in person');
INSERT INTO WorkPlace VALUES('C95H63', 'online');


-- INDUSTRY INSERT
INSERT INTO Industry(industryName)
    VALUES('IT/Technology');
INSERT INTO Industry VALUES('Data Science');
INSERT INTO Industry VALUES('HR');
INSERT INTO Industry VALUES('Engineering');
INSERT INTO Industry VALUES('UX Design');


-- SPONSOR INSERT

INSERT INTO Sponsor(sponsorName, repName, repEmail)
    VALUES('Stripe', 'Mary Lamb', 'marylamb@stripe.com');
INSERT INTO Sponsor VALUES('Amazon', 'Jeff Bezos', 'jeff@amazon.ca');
INSERT INTO Sponsor VALUES('Apple', 'Violet James', 'violet@apple.com');
INSERT INTO Sponsor VALUES('Splunk', 'Boris Kovacevic', 'boris@splunk.ca');
INSERT INTO Sponsor VALUES('Unity', 'Lola Evans', 'lola@unity.com');


-- ROOM INSERT
INSERT INTO Room(roomNumber, capacity, addressMain, floorNumber)
    VALUES(236, 30, '1111 Main Mall', 2);
INSERT INTO Room VALUES(304, 50, '1111 Main Mall', 3);
INSERT INTO Room VALUES(144, 25, '75 Agronomy Road',  1);
INSERT INTO Room VALUES(100, 30, '81 West Mall', 1);
INSERT INTO Room VALUES(423, 23, '1111 Main Mall', 4);

-- POTENTIAL CAREER INSERT
INSERT INTO PotentialCareer(jobTitle)
    VALUES('Software Engineer');
INSERT INTO PotentialCareer VALUES('Web Developer');
INSERT INTO PotentialCareer VALUES('Data Scientist');
INSERT INTO PotentialCareer VALUES('Data Engineer');
INSERT INTO PotentialCareer VALUES('Data Analyst');

-- INTERESTEDIN INSERT

INSERT INTO InterestedIn(pid, faculty, majorName, jobTitle) VALUES(10, 'Science', 'Computer Science', 'Web Developer');
INSERT INTO InterestedIn VALUES(6, 'Science', 'Statistics', 'Data Scientist');
INSERT INTO InterestedIn VALUES(7, 'Arts', 'Computer Science',  'Web Developer');
INSERT INTO InterestedIn VALUES(8, 'Science', 'Math', 'Data Engineer');
INSERT INTO InterestedIn VALUES(9, 'Business', 'Computer Science', 'Data Analyst');

-- BASEDIN INSERT
INSERT INTO BasedIn(postalCode, platform, pid, company, jobTitle) VALUES('V5H8U3', 'online', 1, 'SAP', 'Business Analyst');
INSERT INTO BasedIn VALUES('V4K8K9', 'in person', 2, 'Stripe', 'Recruiter');
INSERT INTO BasedIn VALUES('V5H6U3', 'in person', 3, 'Teck', 'Data Analyst');
INSERT INTO BasedIn VALUES('V8H9I0', 'in person', 4, 'Amazon', 'Project Manager');
INSERT INTO BasedIn VALUES('C95H63', 'online', 5, 'Google', 'Software Engineer');

-- WITHINFIELD INSERT
INSERT INTO WithinField(industryName, pid, company, jobTitle)
    VALUES('IT/Technology', 1, 'SAP', 'Business Analyst'); 
INSERT INTO WithinField VALUES('HR', 2, 'Stripe', 'Recruiter');
INSERT INTO WithinField VALUES ('Data Science', 3, 'Teck', 'Data Analyst');
INSERT INTO WithinField VALUES ('Engineering', 4, 'Amazon', 'Project Manager');
INSERT INTO WithinField VALUES('Engineering', 5, 'Google', 'Software Engineer');

-- SPONSOREDEVENT INSERT
INSERT INTO SponsoredEvent(eventName, eventDate, sponsorName, addressMain, roomNumber)
    VALUES('Coffee Chats', 'January 5', 'Stripe', '1111 Main Mall', 236);
INSERT INTO SponsoredEvent VALUES('Ice Breakers', 'November 15', 'Amazon', '1111 Main Mall', 304);
INSERT INTO SponsoredEvent VALUES('Banquet', 'May 5', 'Apple', '75 Agronomy Road', 144);
INSERT INTO SponsoredEvent VALUES('Info Session', 'June 30', 'Splunk', '81 West Mall', 100);
INSERT INTO SponsoredEvent VALUES('Resume Workshop', 'July 5', 'Unity', '1111 Main Mall', 423);

-- ATTENDS INSERT
INSERT INTO Attends(pid, eventName, eventDate)
    VALUES(1, 'Coffee Chats', 'January 5');
INSERT INTO Attends VALUES(3, 'Info Session', 'June 30');
INSERT INTO Attends VALUES(4, 'Resume Workshop', 'July 5');
INSERT INTO Attends VALUES(2, 'Ice Breakers', 'November 15');
INSERT INTO Attends VALUES(5, 'Banquet', 'May 5');

COMMIT;

