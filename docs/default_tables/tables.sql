CREATE TABLE `image_file` (
  `id_image_file` varchar(255) NOT NULL DEFAULT '',
  `id_group` varchar(255) DEFAULT NULL,
  `pos` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `crop_x` int(10) unsigned DEFAULT '0',
  `crop_y` int(10) unsigned DEFAULT '0',
  `crop_width` int(10) unsigned DEFAULT '0',
  `crop_height` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id_image_file`)
);

CREATE TABLE `image_file_thumb` (
  `id_thumb` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_image_file` varchar(255) DEFAULT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_thumb`)
);

CREATE TABLE `image_group` (
  `id_group` varchar(255) NOT NULL DEFAULT '',
  `editable` tinyint(3) unsigned DEFAULT '1',
  `title` varchar(255) DEFAULT NULL,
  `page` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id_group`)
);
