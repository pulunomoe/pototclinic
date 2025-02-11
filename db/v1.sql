CREATE TABLE users
(
    id       VARCHAR(255) NOT NULL PRIMARY KEY,
    name     VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    level    VARCHAR(255) NOT NULL
);

CREATE TABLE doctors
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE nurses
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE patients
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    race        VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE tests
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE results
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    test_id     VARCHAR(255) NOT NULL,
    value       VARCHAR(255) NOT NULL,
    color       CHAR(8)      NOT NULL,
    description TEXT,
    FOREIGN KEY (test_id) REFERENCES tests (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE VIEW results_view AS
SELECT r.*,
       t.name        AS test_name,
       t.description AS test_description
FROM results r
         INNER JOIN tests t on r.test_id = t.id;

CREATE TABLE reports
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    patient_id  VARCHAR(255) NOT NULL,
    doctor_id   VARCHAR(255) NOT NULL,
    nurse_id    VARCHAR(255) NOT NULL,
    created     TIMESTAMP    NOT NULL,
    summary     VARCHAR(255) NOT NULL,
    color       CHAR(8)      NOT NULL,
    description TEXT
);

CREATE VIEW reports_view AS
SELECT r.*,
       p.name        AS patient_name,
       p.race        AS patient_race,
       p.description AS patient_description,
       d.name        AS doctor_name,
       n.name        AS nurse_name
FROM reports r
         INNER JOIN patients p on r.patient_id = p.id
         INNER JOIN doctors d on r.doctor_id = d.id
         INNER JOIN nurses n ON r.nurse_id = n.id;

CREATE TABLE report_details
(
    id        VARCHAR(255) NOT NULL PRIMARY KEY,
    report_id VARCHAR(255) NOT NULL,
    test_id   VARCHAR(255) NOT NULL,
    result_id VARCHAR(255) NOT NULL
);

CREATE VIEW report_details_view AS
SELECT rd.*,
       rv.summary,
       rv.color,
       rv.description,
       t.name        AS test_name,
       t.description AS test_description,
       r.value       AS result_value,
       r.color       AS result_color,
       r.description AS result_description
FROM report_details rd
         INNER JOIN reports_view rv ON rd.report_id = rv.id
         INNER JOIN tests t on rd.test_id = t.id
         INNER JOIN results r on rd.result_id = r.id

CREATE TABLE certificates
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    patient_id  VARCHAR(255) NOT NULL,
    doctor_id   VARCHAR(255) NOT NULL,
    created     TIMESTAMP    NOT NULL,
    description TEXT
);

CREATE VIEW certificates_view AS
SELECT c.*,
       p.name        AS patient_name,
       p.race        AS patient_race,
       p.description AS patient_description,
       d.name        AS doctor_name,
       d.description AS doctor_description
FROM certificates c
         INNER JOIN patients p on c.patient_id = p.id
         INNER JOIN doctors d on c.doctor_id = d.id;
