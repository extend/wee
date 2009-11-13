CREATE TABLE sessions (
	session_id CHAR(32) NOT NULL,
	session_path CHAR(64) NOT NULL,
	session_name CHAR(32) NOT NULL,
	session_time INTEGER UNSIGNED NOT NULL,
	session_data TEXT,
	PRIMARY KEY (session_id, session_path, session_name)
);
