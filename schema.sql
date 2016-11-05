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
SET default_tablespace = '';

SET default_with_oids = false;

-- Name: ss_stuff_sid_seq; Type: SEQUENCE; Schema: public; Owner: stuffsharers
--

CREATE SEQUENCE ss_stuff_sid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ss_stuff_sid_seq OWNER TO stuffsharers;

--
-- Name: ss_stuff; Type: TABLE; Schema: public; Owner: stuffsharers
--

CREATE TABLE ss_stuff (
    sid integer DEFAULT nextval('ss_stuff_sid_seq'::regclass) NOT NULL,
    uid integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    is_available boolean DEFAULT true NOT NULL,
    pref_price money DEFAULT 0 NOT NULL,
    pickup_date timestamp with time zone DEFAULT now() NOT NULL,
    return_date timestamp with time zone DEFAULT now() NOT NULL,
    pickup_locn character varying(255) NOT NULL,
    return_locn character varying(255) NOT NULL,
    CONSTRAINT ss_stuff_check CHECK ((return_date > pickup_date))
);


ALTER TABLE ss_stuff OWNER TO stuffsharers;

--
-- Name: available_stuff; Type: VIEW; Schema: public; Owner: stuffsharers
--

CREATE VIEW available_stuff AS
 SELECT ss_stuff.sid,
    ss_stuff.uid,
    ss_stuff.name,
    ss_stuff.description,
    ss_stuff.is_available,
    ss_stuff.pref_price,
    ss_stuff.pickup_date,
    ss_stuff.return_date,
    ss_stuff.pickup_locn,
    ss_stuff.return_locn
   FROM ss_stuff
  WHERE (ss_stuff.is_available = true);


ALTER TABLE available_stuff OWNER TO stuffsharers;

--
-- Name: ss_bid; Type: TABLE; Schema: public; Owner: stuffsharers
--

CREATE TABLE ss_bid (
    sid integer NOT NULL,
    uid integer NOT NULL,
    bid_amount money NOT NULL,
    bid_date timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT ss_bid_bid_amount_check CHECK ((bid_amount >= (0)::money))
);


ALTER TABLE ss_bid OWNER TO stuffsharers;

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
-- Data for Name: ss_bid; Type: TABLE DATA; Schema: public; Owner: stuffsharers
--

COPY ss_bid (sid, uid, bid_amount, bid_date) FROM stdin;
\.


--
-- Data for Name: ss_stuff; Type: TABLE DATA; Schema: public; Owner: stuffsharers
--

COPY ss_stuff (sid, uid, name, description, is_available, pref_price, pickup_date, return_date, pickup_locn, return_locn) FROM stdin;
1	1	Google Bottle	Red; from Orbital 2015	t	0	2016-09-30 15:30:00	2016-10-10 15:30:00	NUS SoC	NUS SoC
2	1	Striped Red Shirt	Medium size	t	0	2016-09-30 15:30:00	2016-10-10 15:30:00	NUS SoC	NUS SoC
3	1	CS2102 Notes	Made with love	t	0	2016-09-30 15:30:00	2016-10-10 15:30:00	NUS SoC	NUS SoC
\.


--
-- Name: ss_stuff_sid_seq; Type: SEQUENCE SET; Schema: public; Owner: stuffsharers
--

SELECT pg_catalog.setval('ss_stuff_sid_seq', 3, true);


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
-- Name: ss_bid_pkey; Type: CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_bid
    ADD CONSTRAINT ss_bid_pkey PRIMARY KEY (sid, uid);


--
-- Name: ss_bid_sid_bid_amount_key; Type: CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_bid
    ADD CONSTRAINT ss_bid_sid_bid_amount_key UNIQUE (sid, bid_amount);


--
-- Name: ss_stuff_pkey; Type: CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_stuff
    ADD CONSTRAINT ss_stuff_pkey PRIMARY KEY (sid);


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
-- Name: ss_bid_sid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_bid
    ADD CONSTRAINT ss_bid_sid_fkey FOREIGN KEY (sid) REFERENCES ss_stuff(sid) ON DELETE CASCADE;


--
-- Name: ss_bid_uid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_bid
    ADD CONSTRAINT ss_bid_uid_fkey FOREIGN KEY (uid) REFERENCES ss_user(uid) ON DELETE CASCADE;


--
-- Name: ss_stuff_uid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: stuffsharers
--

ALTER TABLE ONLY ss_stuff
    ADD CONSTRAINT ss_stuff_uid_fkey FOREIGN KEY (uid) REFERENCES ss_user(uid) ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: stuffsharers
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM stuffsharers;
GRANT ALL ON SCHEMA public TO stuffsharers;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

