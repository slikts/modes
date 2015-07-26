CREATE TABLE autologin (
	id serial,
	userid integer REFERENCES users,
	created timestamp default current_timestamp,
	used timestamp default current_timestamp,
	ip text NOT NULL,
	loginkey text NOT NULL
);