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

SET search_path = public, pg_catalog;

--
-- Name: ss_stuff_sid_seq; Type: SEQUENCE; Schema: public; Owner: stuffsharers
--

CREATE SEQUENCE ss_stuff_sid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ss_stuff_sid_seq OWNER TO stuffsharers;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: ss_stuff; Type: TABLE; Schema: public; Owner: stuffsharers
--

CREATE TABLE ss_stuff (
    sid integer DEFAULT nextval('ss_stuff_sid_seq'::regclass) NOT NULL,
    uid integer NOT NULL,
    name character varying(255) NOT NULL,
    availability date NOT NULL,
    pref_price money,
    pickup_date date NOT NULL,
    return_date date NOT NULL,
    pickup_locn character varying(255) NOT NULL,
    return_locn character varying(255) NOT NULL,
    CONSTRAINT ss_stuff_check CHECK ((return_date > pickup_date))
);


ALTER TABLE ss_stuff OWNER TO stuffsharers;

--
-- Data for Name: ss_stuff; Type: TABLE DATA; Schema: public; Owner: stuffsharers
--

COPY ss_stuff (sid, uid, name, availability, pref_price, pickup_date, return_date, pickup_locn, return_locn) FROM stdin;
\.


--
-- Name: ss_stuff_sid_seq; Type: SEQUENCE SET; Schema: public; Owner: stuffsharers
--

SELECT pg_catalog.setval('ss_stuff_sid_seq', 1, false);


--
-- Name: ss_stuff_pkey; Type: CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_stuff
    ADD CONSTRAINT ss_stuff_pkey PRIMARY KEY (sid, uid);


--
-- PostgreSQL database dump complete
--

