-- Tables for the pastebin example.

CREATE TABLE pastebin (
	pastebin_id			int NOT NULL,
	pastebin_text		text,
	pastebin_timestamp	timestamp without time zone DEFAULT NOW()
);

CREATE SEQUENCE pastebin_id_seq
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

ALTER SEQUENCE pastebin_id_seq OWNED BY pastebin.pastebin_id;
ALTER TABLE pastebin ALTER COLUMN pastebin_id SET DEFAULT nextval('pastebin_id_seq'::regclass);
ALTER TABLE ONLY pastebin ADD CONSTRAINT pastebin_id_pk PRIMARY KEY (pastebin_id);
