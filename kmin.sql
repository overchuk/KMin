
DROP TABLE IF EXISTS `kmin_user`;
CREATE TABLE IF NOT EXISTS `kmin_user` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(64) NOT NULL,
  `norm` varchar(64) NOT NULL,
  `email` varchar(128) character set ascii NOT NULL,
  `sol` varchar(64) character set ascii NOT NULL,
  `pass` varchar(64) character set ascii NOT NULL,
  `role` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `stamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `norm_2` (`norm`),
  KEY `norm` (`norm`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `kmin_user` (`id`, `login`, `norm`, `email`, `sol`, `pass`, `role`, `status`, `stamp`) VALUES 
(1, 'admin', 'admin', 'peter@4-d.su', '4e0dfed83306ce7e298da6361abee2a2', 'f399737e3cf536814bb0c7ff6598c0c7', 99, 1, '2010-10-10 20:29:33');
