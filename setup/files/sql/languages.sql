DROP TABLE IF EXISTS `zf_language_code`;
CREATE TABLE IF NOT EXISTS zf_language_code
(
	`id_language_code` varchar(255) primary key,
	`name` varchar(255)
);

DROP TABLE IF EXISTS `zf_language_region`;
CREATE TABLE IF NOT EXISTS zf_language_region
(
	id_language_region varchar(255) primary key,
	`name` varchar(255)
);

DROP TABLE IF EXISTS `zf_language`;
CREATE TABLE IF NOT EXISTS `zf_language`
(
	id_language varchar(255) primary key,
	id_language_code varchar(255),
	id_language_region varchar(255),
	is_default tinyint(1) unsigned default 0,
	`name` varchar(255),
	FOREIGN KEY (id_language_code)
		REFERENCES zf_language_code(id_language_code)
		ON DELETE CASCADE,

	FOREIGN KEY (id_language_region) 
		REFERENCES zf_language_region(id_language_region)
		ON DELETE CASCADE
);

DROP TABLE IF EXISTS `zf_language_section`;
CREATE TABLE IF NOT EXISTS zf_language_section
( 
	id_language_section varchar(255) NOT NULL PRIMARY KEY, 
	`system` tinyint(1) unsigned DEFAULT '0', 
	`user` tinyint(1) unsigned DEFAULT '0'	
);

DROP TABLE IF EXISTS `zf_language_text`;
CREATE TABLE IF NOT EXISTS zf_language_text
(
	id_language_text varchar(255) NOT NULL DEFAULT '',  
	id_language_section varchar(255) NOT NULL DEFAULT '',  
	id_language varchar(255) NOT NULL DEFAULT '',  
	`text` longtext,  
	`javascript` tinyint(1) unsigned DEFAULT '0',  
	PRIMARY KEY (id_language_text,id_language_section,id_language),
	FOREIGN KEY (id_language_section)
		REFERENCES zf_language_section(id_language_section)
		ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (id_language)
		REFERENCES zf_language(id_language)
		ON UPDATE CASCADE ON DELETE CASCADE
);


INSERT INTO zf_language_code VALUES ('es', 'Español');
INSERT INTO zf_language_code VALUES ('en', 'English');
INSERT INTO zf_language_code VALUES ('fr', 'Francais');

INSERT INTO zf_language_region VALUES ('es', 'España');
INSERT INTO zf_language_region VALUES ('ar', 'Argentina');
INSERT INTO zf_language_region VALUES ('us', 'United States');
INSERT INTO zf_language_region VALUES ('uk', 'United Kingdom');
INSERT INTO zf_language_region VALUES ('fr', 'France');

INSERT INTO `zf_language` VALUES ('es-AR', 'es', 'ar', 0, 'Español Argentina');
INSERT INTO `zf_language` VALUES ('en-UK', 'en', 'uk', 0, 'British English');
INSERT INTO `zf_language` VALUES ('fr-FR', 'fr', 'fr', 0, 'Francais');
