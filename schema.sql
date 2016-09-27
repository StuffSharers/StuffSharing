--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.4
-- Dumped by pg_dump version 9.5.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: ss_user; Type: TABLE; Schema: public; Owner: stuffsharers
--

CREATE TABLE ss_user (
    uid integer NOT NULL,
    username character varying(20) NOT NULL,
    password character(40) NOT NULL,
    email character varying(255) NOT NULL,
    contact numeric(8,0),
    join_date timestamp with time zone DEFAULT now() NOT NULL,
    is_admin boolean DEFAULT false NOT NULL
);


ALTER TABLE ss_user OWNER TO stuffsharers;

--
-- Name: ss_user_uid_seq; Type: SEQUENCE; Schema: public; Owner: stuffsharers
--

CREATE SEQUENCE ss_user_uid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ss_user_uid_seq OWNER TO stuffsharers;

--
-- Name: ss_user_uid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: stuffsharers
--

ALTER SEQUENCE ss_user_uid_seq OWNED BY ss_user.uid;


--
-- Name: uid; Type: DEFAULT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_user ALTER COLUMN uid SET DEFAULT nextval('ss_user_uid_seq'::regclass);


--
-- Data for Name: ss_user; Type: TABLE DATA; Schema: public; Owner: stuffsharers
--

COPY ss_user (uid, username, password, email, contact, join_date, is_admin) FROM stdin;
1	admin	5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8	admin@stuffsharing.com	\N	2016-09-27 16:48:47.586184+08	t
2	stuffsharer	5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8	stuffsharer@stuffsharing.com	\N	2016-09-27 17:03:39.897454+08	f
\.


--
-- Name: ss_user_uid_seq; Type: SEQUENCE SET; Schema: public; Owner: stuffsharers
--

SELECT pg_catalog.setval('ss_user_uid_seq', 2, true);


--
-- Name: ss_user_email_key; Type: CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_user
    ADD CONSTRAINT ss_user_email_key UNIQUE (email);


--
-- Name: ss_user_pkey; Type: CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_user
    ADD CONSTRAINT ss_user_pkey PRIMARY KEY (uid);


--
-- Name: ss_user_username_key; Type: CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_user
    ADD CONSTRAINT ss_user_username_key UNIQUE (username);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

