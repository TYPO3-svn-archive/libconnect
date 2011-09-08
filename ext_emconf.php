<?php

########################################################################
# Extension Manager/Repository config file for ext "libconnect".
#
# Auto generated 08-09-2011 14:22
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extension zur Anbindung von EZB und DBIS',
	'description' => 'Diese Extension ist von Avonis im Auftrag der Staats- und Universitaetsbibliothek Hamburg entwickelt worden. Mit ihr lassen sich Ergebnisse aus den Informationssystemen EZB und DBIS der Universitaet Regensburg direkt in das TYPO3-System einbinden.',
	'category' => 'plugin',
	'author' => 'Avonis New Media / SUB Hamburg',
	'author_email' => 'agency@avonis.com',
	'shy' => '',
	'dependencies' => 'cms,lib,div,smarty',
	'conflicts' => 'avonis_sub',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '2.2.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lib' => '',
			'div' => '',
			'smarty' => '',
		),
		'conflicts' => array(
			'avonis_sub' => '0.0.1',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:53:{s:9:"ChangeLog";s:4:"1f5c";s:12:"ext_icon.gif";s:4:"b57c";s:17:"ext_localconf.php";s:4:"101f";s:14:"ext_tables.php";s:4:"03fd";s:14:"ext_tables.sql";s:4:"5ec1";s:25:"ext_tables_static+adt.sql";s:4:"c1a0";s:13:"locallang.xml";s:4:"41dd";s:16:"locallang_db.xml";s:4:"539b";s:7:"tca.php";s:4:"11a3";s:31:"configurations/TS/constants.txt";s:4:"06c3";s:27:"configurations/TS/setup.txt";s:4:"dfa7";s:56:"configurations/dbis/class.tx_libconnect_dbis_wizicon.php";s:4:"55ec";s:32:"configurations/dbis/flexform.xml";s:4:"e8f1";s:54:"configurations/ezb/class.tx_libconnect_ezb_wizicon.php";s:4:"5c5d";s:31:"configurations/ezb/flexform.xml";s:4:"1af9";s:52:"controllers/class.tx_libconnect_controllers_dbis.php";s:4:"c46a";s:51:"controllers/class.tx_libconnect_controllers_ezb.php";s:4:"9069";s:14:"doc/manual.sxw";s:4:"9e8b";s:22:"icons/icon_subject.png";s:4:"0ba8";s:18:"icons/wiz_icon.gif";s:4:"38d5";s:35:"lib/ezb_dbis/classes/class_DBIS.php";s:4:"3da6";s:34:"lib/ezb_dbis/classes/class_EZB.php";s:4:"f97c";s:42:"models/class.tx_libconnect_models_dbis.php";s:4:"5e2d";s:41:"models/class.tx_libconnect_models_ezb.php";s:4:"b857";s:25:"templates/dbis_detail.tpl";s:4:"b9de";s:23:"templates/dbis_form.tpl";s:4:"134f";s:23:"templates/dbis_list.tpl";s:4:"51d9";s:27:"templates/dbis_miniform.tpl";s:4:"6700";s:27:"templates/dbis_overview.tpl";s:4:"0f25";s:25:"templates/dbis_search.tpl";s:4:"e79c";s:26:"templates/dbis_toplist.tpl";s:4:"7b25";s:24:"templates/ezb_detail.tpl";s:4:"2b69";s:22:"templates/ezb_form.tpl";s:4:"4618";s:22:"templates/ezb_list.tpl";s:4:"744a";s:26:"templates/ezb_miniform.tpl";s:4:"6e32";s:26:"templates/ezb_overview.tpl";s:4:"d9cb";s:24:"templates/ezb_search.tpl";s:4:"3f34";s:29:"templates/img/dbis-list_1.png";s:4:"4ca5";s:29:"templates/img/dbis-list_2.png";s:4:"fffe";s:29:"templates/img/dbis-list_3.png";s:4:"efff";s:29:"templates/img/dbis-list_4.png";s:4:"2000";s:29:"templates/img/dbis-list_5.png";s:4:"1a6a";s:29:"templates/img/dbis-list_6.png";s:4:"5b3e";s:29:"templates/img/dbis-list_7.png";s:4:"8ff8";s:35:"templates/img/dbis-list_germany.png";s:4:"5f7e";s:28:"templates/img/ezb-list_1.png";s:4:"4ca5";s:28:"templates/img/ezb-list_2.png";s:4:"fffe";s:28:"templates/img/ezb-list_4.png";s:4:"5b3e";s:28:"templates/img/ezb-list_6.png";s:4:"9401";s:31:"templates/img/ezb-list_euro.png";s:4:"76f7";s:25:"templates/styles/dbis.css";s:4:"f33d";s:24:"templates/styles/ezb.css";s:4:"a4b8";s:42:"views/class.tx_libconnect_views_smarty.php";s:4:"9048";}',
	'suggests' => array(
	),
);

?>