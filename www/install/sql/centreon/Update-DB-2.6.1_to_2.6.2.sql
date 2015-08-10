-- Add default socket path for Centreon Broker
INSERT INTO `options` (`key`, `value`) VALUES ('broker_socket_path', '@CENTREONBROKER_VARLIB@/command');

-- Add new configuration information for synchronize database with Centreon Broker
INSERT INTO `cb_module` (`cb_module_id`, `name`, `libname`, `loading_pos`, `is_bundle`, `is_activated`) VALUES
(17, 'Dumper', 'dumper.so', 20, 0, 1);

INSERT INTO `cb_type` (`cb_type_id`, `type_name`, `type_shortname`, `cb_module_id`) VALUES
(28, 'Database configuration reader', 'db_cfg_reader', 17),
(29, 'Database configuration writer', 'db_cfg_writer', 17);

INSERT INTO `cb_type_field_relation` (`cb_type_id`, `cb_field_id`, `is_required`, `order_display`) VALUES
(28, 15, 1, 1),
(28, 7, 1, 2),
(28, 18, 0, 3),
(28, 8, 0, 4),
(28, 9, 0, 5),
(28, 10, 1, 6),
(29, 15, 1, 1),
(29, 7, 1, 2),
(29, 18, 0, 3),
(29, 8, 0, 4),
(29, 9, 0, 5),
(29, 10, 1, 6);

INSERT INTO `cb_tag_type_relation` (`cb_tag_id`, `cb_type_id`, `cb_type_uniq`) VALUES
(1, 28, 1),
(1, 29, 1);

ALTER TABLE cfg_centreonbroker ADD COLUMN command_file VARCHAR(255);

INSERT INTO `cb_list_values` (`cb_list_id`, `value_name`, `value_value`) VALUES
(6, 'Dumper', 'dumper');

-- Change version of Centreon
UPDATE `informations` SET `value` = '2.6.2' WHERE CONVERT( `informations`.`key` USING utf8 )  = 'version' AND CONVERT ( `informations`.`value` USING utf8 ) = '2.6.1' LIMIT 1;