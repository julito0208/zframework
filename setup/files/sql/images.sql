
DROP TABLE IF EXISTS `zf_image_type`;
CREATE TABLE IF NOT EXISTS `zf_image_type` (
	`id_image_type` varchar(255) primary key,
	`extension` varchar(255),
	`name` varchar(255)
);


DROP TABLE IF EXISTS `zf_image_thumb_type`;
CREATE TABLE IF NOT EXISTS `zf_image_thumb_type` (
	`id_image_thumb_type` varchar(255) NOT NULL DEFAULT '' PRIMARY KEY,
	`id_image_type` varchar(255),
	`thumb_width` int(10) unsigned default NULL,
	`thumb_height` int(10) unsigned default NULL,
	`use_image_crop` tinyint(1) unsigned default 0,
  FOREIGN KEY (id_image_type)
      REFERENCES zf_image_type(id_image_type)
      ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS `zf_image_group`;
CREATE TABLE IF NOT EXISTS `zf_image_group` (
  `id_group` varchar(255) NOT NULL DEFAULT '',
  `editable` tinyint(3) unsigned DEFAULT '1',
  `title` varchar(255) DEFAULT NULL,
  `page` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id_group`)
);


DROP TABLE IF EXISTS `zf_image_file`;
CREATE TABLE IF NOT EXISTS `zf_image_file` (
  `id_image_file` varchar(255) NOT NULL DEFAULT '' PRIMARY KEY,
  `id_group` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `pos` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `crop_x` int(10) unsigned default null,
  `crop_y` int(10) unsigned default null,
  `crop_width` int(10) unsigned default null,
  `crop_height` int(10) unsigned default null,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `temporal` tinyint(1) unsigned default 0,
  FOREIGN KEY (id_group)
    REFERENCES zf_image_group(id_group)
    ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS `zf_image_file_thumb`;
CREATE TABLE IF NOT EXISTS `zf_image_file_thumb` (
  `id_image_file` VARCHAR(255) NOT NULL,
  `id_image_thumb_type` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_image_file, id_image_thumb_type),
  FOREIGN KEY (id_image_file)
    REFERENCES zf_image_file(id_image_file)
    ON UPDATE CASCADE ON DELETE CASCADE
);


INSERT INTO `zf_image_group` VALUES ('default', 1, 'Default', 0);
INSERT INTO `zf_image_type` VALUES ('jpg', 'jpg', 'JPG'), ('png', 'png', 'PNG'), ('gif', 'gif', 'GIF');
INSERT INTO `zf_image_thumb_type` VALUES ('default', 'png', NULL, NULL, 0), ('crop', 'png', NULL, NULL, 1);

