--
-- PostgreSQL database dump
--

-- Started on 2007-12-26 10:32:24 CET

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 1607 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1269 (class 1259 OID 18430)
-- Dependencies: 1601 4
-- Name: pastebin_data; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE pastebin_data (
    data_id bigint NOT NULL,
    data_text text,
    data_timestamp timestamp without time zone DEFAULT now()
);


--
-- TOC entry 1268 (class 1259 OID 18428)
-- Dependencies: 4 1269
-- Name: pastebin_data_data_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE pastebin_data_data_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1609 (class 0 OID 0)
-- Dependencies: 1268
-- Name: pastebin_data_data_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE pastebin_data_data_id_seq OWNED BY pastebin_data.data_id;


--
-- TOC entry 1600 (class 2604 OID 18432)
-- Dependencies: 1269 1268 1269
-- Name: data_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE pastebin_data ALTER COLUMN data_id SET DEFAULT nextval('pastebin_data_data_id_seq'::regclass);


--
-- TOC entry 1603 (class 2606 OID 18438)
-- Dependencies: 1269 1269
-- Name: pastebin_data_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pastebin_data
    ADD CONSTRAINT pastebin_data_id_pk PRIMARY KEY (data_id);


--
-- TOC entry 1608 (class 0 OID 0)
-- Dependencies: 4
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2007-12-26 10:32:25 CET

--
-- PostgreSQL database dump complete
--

