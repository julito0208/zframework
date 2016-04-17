
DROP TABLE IF EXISTS `zf_location_zone`;
create table IF NOT EXISTS zf_permissions (
	id_permission varchar(255) primary key,
	`name` varchar(255)
);

DROP TABLE IF EXISTS `zf_roles`;
create table IF NOT EXISTS zf_roles (
	id_role varchar(255) primary key,
	is_admin tinyint(1) unsigned default 0,
	`name` varchar(255)
);

DROP TABLE IF EXISTS `zf_roles_permissions`;
CREATE TABLE IF NOT EXISTS zf_roles_permissions (
	id_role varchar(255),
	id_permission varchar(255),
	primary key (id_role, id_permission),
	FOREIGN KEY (id_role)
		REFERENCES zf_roles(id_role)
		ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (id_permission)
		REFERENCES zf_permissions(id_permission)
		ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS `zf_users`;
CREATE TABLE IF NOT EXISTS `zf_users` (
	id_user BIGINT UNSIGNED auto_increment primary key,
	username varchar(400) unique not null,
	password varchar(400),
	date_added timestamp default current_timestamp,
	last_login timestamp null default null,
	is_active tinyint(1) unsigned default 1
);

DROP TABLE IF EXISTS `zf_users_roles`;
CREATE TABLE IF NOT EXISTS zf_users_roles (
	id_user bigint unsigned,
	id_role varchar(255),
	primary key (id_user, id_role),
	FOREIGN KEY (id_user)
		REFERENCES `zf_users`(id_user)
		ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (id_role)
		REFERENCES zf_roles(id_role)
		ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO `zf_roles` VALUES ('ADMIN', 1, 'Admin');
