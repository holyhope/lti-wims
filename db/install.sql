CREATE TABLE lti_users (
  id                    INTEGER PRIMARY KEY,
  sourcedid             VARCHAR(255) DEFAULT NULL,
  name_given            VARCHAR(255) DEFAULT '',
  name_family           VARCHAR(255) DEFAULT '',
  name_full             VARCHAR(255) DEFAULT '',
  contact_email_primary VARCHAR(255) DEFAULT NULL
);

CREATE TABLE tools_provider (
  id                    INTEGER PRIMARY KEY,
  url                   VARCHAR(255) DEFAULT NULL,
  sessions_path         VARCHAR(255) DEFAULT '',
  data_path             VARCHAR(255) DEFAULT ''
);