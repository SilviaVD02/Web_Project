-- ????????? ?? ???? ?????


CREATE DATABASE IF NOT EXISTS tc_db;

-- ?????????? ?? ?????? ???? ?????
USE tc_db;

-- ????????? ?? ???????
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(13) NOT NULL,
    password VARCHAR(50) NOT NULL,
    fn VARCHAR(10) NULL UNIQUE,
    role INT(1) NOT NULL
);

CREATE TABLE IF NOT EXISTS questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    importer_id INT NOT NULL,
    creator_fn VARCHAR(10)NOT NULL,
    goal VARCHAR(100) NOT NULL,
    question VARCHAR(200) NOT NULL,
    opt1 VARCHAR(300) NOT NULL,
    opt2 VARCHAR(300) NOT NULL,
    opt3 VARCHAR(300) NOT NULL,
    opt4 VARCHAR(300) NOT NULL,
    answer VARCHAR(300) NOT NULL,
    difficulty INT NOT NULL,
    react_when_wrong VARCHAR(100) NOT NULL,
    react_when_right VARCHAR(100) NOT NULL,
    date_submitted VARCHAR(50) NOT NULL,
    note VARCHAR(100)NOT NULL,
    FOREIGN KEY (importer_id) REFERENCES users(user_id)

);

CREATE TABLE IF NOT EXISTS reviews (
    date_submitted VARCHAR(50) NOT NULL,
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    creator_fn VARCHAR(10) NOT NULL,
    q_id INT NOT NULL,
    user_id INT NOT NULL,
    review VARCHAR(200) NOT NULL,
    rating INT NOT NULL,
    FOREIGN KEY (q_id) REFERENCES questions(question_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    
);

CREATE TABLE IF NOT EXISTS test_results (
    date_submitted VARCHAR(50) NOT NULL,
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    q_id INT NOT NULL, 
    examinee_id INT NOT NULL,
    answer BIT NOT NULL,
    FOREIGN KEY (q_id) REFERENCES questions(question_id),
    FOREIGN KEY (examinee_id) REFERENCES users(user_id)
   
);

INSERT INTO users (username, password, fn, role) VALUES
('asdfgh', 'asdFGH1234', NULL, 2),
('me2031', 'asdFGH1234', '789456', 1),
('rositsa', '2002Rosi2002', '2002', 1),
('me2032', 'asdFGH1234', '9999', 1),
('??2033', '??????1234', '61505', 1),
('me2035', 'asdFGH1234', '1234', 1),
('asdfgh2', 'asdFGH1234', NULL, 2),
('me5353', 'asdFGH1234', '8MI0600036', 1);

