DROP TABLE IF EXISTS aiml;
DROP TABLE IF EXISTS aiml_userdefined;
DROP TABLE IF EXISTS botpersonality;
DROP TABLE IF EXISTS bots;
DROP TABLE IF EXISTS client_properties;
DROP TABLE IF EXISTS conversation_log;
DROP TABLE IF EXISTS myprogramo;
DROP TABLE IF EXISTS spellcheck;
DROP TABLE IF EXISTS srai_lookup;
DROP TABLE IF EXISTS undefined_defaults;
DROP TABLE IF EXISTS unknown_inputs;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS wordcensor;

DROP TYPE IF EXISTS save_state_type RESTRICT;

CREATE TYPE save_state_type AS ENUM('session','database');

CREATE TABLE IF NOT EXISTS aiml ( id serial, bot_id integer NOT NULL DEFAULT '1', pattern varchar(256) NOT NULL, thatpattern varchar(256) NOT NULL, template text NOT NULL, topic varchar(256) NOT NULL, filename varchar(256) NOT NULL, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS aiml_userdefined ( id serial, pattern varchar(256) NOT NULL, thatpattern varchar(256) NOT NULL, template text NOT NULL, user_id varchar(256) NOT NULL, bot_id integer NOT NULL, date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS botpersonality ( id serial, bot_id integer NOT NULL DEFAULT '0', name varchar(256) NOT NULL DEFAULT '', value text NOT NULL, PRIMARY KEY (id));

CREATE TABLE IF NOT EXISTS bots ( bot_id serial, bot_name varchar(256) NOT NULL, bot_desc varchar(256) NOT NULL, bot_active integer NOT NULL DEFAULT '1', bot_parent_id integer NOT NULL DEFAULT '0', format varchar(10) NOT NULL DEFAULT 'html', save_state save_state_type NOT NULL DEFAULT 'session', conversation_lines integer NOT NULL DEFAULT '7', remember_up_to integer NOT NULL DEFAULT '10', debugemail text NOT NULL, debugshow integer NOT NULL DEFAULT '1', debugmode integer NOT NULL DEFAULT '1', error_response text NOT NULL, default_aiml_pattern varchar(256) NOT NULL DEFAULT 'RANDOM PICKUP LINE', unknown_user varchar(256) NOT NULL DEFAULT 'Seeker', PRIMARY KEY (bot_id));
CREATE TABLE IF NOT EXISTS client_properties ( id serial, user_id integer NOT NULL, bot_id integer NOT NULL, name text NOT NULL, value text NOT NULL, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS conversation_log ( id serial, input text NOT NULL, response text NOT NULL, user_id integer NOT NULL, convo_id text NOT NULL, bot_id integer NOT NULL, timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS myprogramo ( id serial, user_name varchar(256) NOT NULL, password varchar(256) NOT NULL, last_ip varchar(25) NOT NULL, last_login timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE (user_name));
CREATE TABLE IF NOT EXISTS spellcheck ( id serial, missspelling varchar(100) NOT NULL, correction varchar(100) NOT NULL, PRIMARY KEY (id));

CREATE TABLE IF NOT EXISTS srai_lookup ( id serial, bot_id integer NOT NULL, pattern text NOT NULL, template_id integer NOT NULL, PRIMARY KEY (id));

CREATE TABLE IF NOT EXISTS undefined_defaults ( id serial, bot_id integer NOT NULL, user_id integer NOT NULL DEFAULT '0', pattern text NOT NULL, template varchar(256) NOT NULL, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS unknown_inputs ( id serial, bot_id integer NOT NULL, input text NOT NULL, user_id integer NOT NULL, timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS users ( id serial, user_name text NOT NULL, session_id varchar(256) NOT NULL, bot_id integer NOT NULL, chatlines integer NOT NULL, ip varchar(100) NOT NULL, referer text NOT NULL, browser text NOT NULL, date_logged_on timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, last_update timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, state text NOT NULL, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS wordcensor ( censor_id serial, word_to_censor varchar(50) NOT NULL, replace_with varchar(50) NOT NULL DEFAULT '****', bot_exclude varchar(256) NOT NULL, PRIMARY KEY (censor_id));

CREATE INDEX aiml_topic ON aiml USING btree (topic);
CREATE INDEX aiml_thatpattern ON aiml USING btree (thatpattern);
CREATE INDEX aiml_pattern ON aiml USING btree (pattern);
CREATE INDEX aiml_bot_id ON aiml USING btree (bot_id);
CREATE INDEX aiml_userdefined_user_id ON aiml_userdefined USING btree (user_id);
CREATE INDEX aiml_userdefined_thatpattern ON aiml_userdefined USING btree (thatpattern);
CREATE INDEX aiml_userdefined_pattern ON aiml_userdefined USING btree (pattern);
CREATE INDEX aiml_userdefined_bot_id ON aiml_userdefined USING btree (bot_id);
CREATE INDEX botpersonality_botname ON botpersonality USING btree (bot_id, name);
CREATE INDEX srai_lookup_template_id ON srai_lookup USING btree (template_id);
CREATE INDEX srai_lookup_pattern ON srai_lookup USING btree (bot_id, pattern);


INSERT INTO bots (bot_id, bot_name, bot_desc, bot_active, bot_parent_id, format, save_state, conversation_lines, remember_up_to, debugemail, debugshow, debugmode, error_response, default_aiml_pattern, unknown_user) VALUES (1, 'newTest', '', 1, 1, 'json', 'session', 1, 10, 'dmorton@geekcavecreations.com', 4, 1, '', 'RANDOM PICKUP LINE', 'Seeker');
INSERT INTO myprogramo (id, user_name, password, last_ip, last_login) VALUES (0, 'dmorton@geekcavecreations.com', '986fb4494b455629e27ba1d1ad8cfdc8', '127.0.0.1', '2017-06-18 14:49:02');
INSERT INTO spellcheck (id, missspelling, correction) VALUES (1, 'shakespear', 'shakespeare'), (2, 'shakesper', 'shakespeare'), (3, 'ws', 'william shakespeare'), (4, 'shakespaer', 'shakespeare'), (5, 'shakespere', 'shakespeare'), (6, 'shakepeare', 'shakespeare'), (7, 'shakeper', 'shakespeare'), (8, 'willam', 'william'), (9, 'willaim', 'william'), (10, 'romoe', 'romeo'), (11, 'julet', 'juliet'), (12, 'juleit', 'juliet'), (13, 'thats', 'that is'), (89, 'Youa aare', 'you are'), (88, 'that s', 'that is'), (87, 'wot s', 'what is'), (17, 'whats', 'what is'), (18, 'wot', 'what'), (19, 'wots', 'what is'), (86, 'what s', 'what is'), (21, 'lool', 'lol'), (27, 'pogram', 'program'), (23, 'progam', 'program'), (26, 'progam', 'program'), (28, 'r', 'are'), (29, 'u', 'you'), (30, 'ur', 'your'), (31, 'v', 'very'), (32, 'k', 'ok'), (33, 'np', 'no problem'), (34, 'ta', 'thank you'), (35, 'ty', 'thank you'), (36, 'omg', 'oh my god'), (37, 'letts', 'lets'), (38, 'yeah', 'yes'), (39, 'yeh', 'yes'), (40, 'portugues', 'portuguese'), (41, 'hehe', 'lol'), (42, 'ha', 'lol'), (43, 'intersting', 'interesting'), (44, 'qestion', 'question'), (45, 'elrond hubbard', 'l.ron hubbard'), (46, 'programm', 'program'), (47, 'c''mon', 'come on'), (48, 'ye', 'yes'), (49, 'im', 'i am'), (50, 'fuckahh', 'fucker'), (51, 'shakespeare bot', 'shakespearebot'), (52, 'goodf', 'good'), (53, 'dont', 'do not'), (54, 'cos', 'because'), (55, 'cus', 'because'), (56, 'coz', 'because'), (57, 'cuz', 'because'), (58, 'isnt', 'is not'), (59, 'isn''t', 'is not'), (60, 'i''m', 'i am'), (61, 'ima', 'i am a'), (62, 'chheese', 'cheese'), (63, 'watsup', 'what is up'), (64, 'let s', 'let us'), (65, 'he s', 'he is'), (66, 'she''s', 'she is'), (67, 'i ll', 'i will'), (68, 'they ll', 'they will'), (69, 'you re', 'you are'), (70, 'you ve', 'you have'), (71, 'hy', 'hey'), (72, 'msician', 'musician'), (74, 'don t', 'do not'), (75, 'can t', 'cannot'), (76, 'favourite', 'favorite'), (77, 'colour', 'color'), (78, 'won t', 'will not'), (79, 'a/s/l', 'asl'), (80, 'haven t', 'have not'), (81, 'doesn t', 'does not'), (82, 'a/s/l/', 'asl'), (83, 'wht', 'what'), (84, 'It s been', 'It has been'), (85, 'its been', 'it has been'), (90, 'you re', 'you are'), (91, 'theres', 'there is'), (92, 'youa re', 'you are'), (93, 'youa aare', 'you are'), (94, 'wath', 'what'), (95, 'waths', 'what is'), (96, 'hy', 'hey'), (97, 'oke', 'ok'), (98, 'okay', 'ok'), (99, 'errm', 'erm'), (100, 'aare', 'are'), (101, 'shakespeare bot', 'william shakespeare'), (102, 'shakespearebot', 'william shakespeare'), (103, 'werwerwer', 'war'), (109, 'program o', 'programo'), (106, 'ddddddddd', 'ddddddddd'), (107, 'ddddddddd', 'ddddddddd'), (108, 'fgfgfgfg', 'fgfgfgfg'), (110, 'program-o', 'programo'), (111, 'fav', 'favorite'), (112, 'FUCK', 'FUCK'), (113, 'U', 'YOU');
INSERT INTO wordcensor (censor_id, word_to_censor, replace_with, bot_exclude) VALUES (1, 'SHIT', 'S***', ''), (2, 'fuck', 'f***', '');
