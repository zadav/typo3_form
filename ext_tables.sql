#
# Table structure for table 'tx_feuserstat_sessions'
#
CREATE TABLE tx_feuserstat_sessions (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fe_user int(11) DEFAULT '0' NOT NULL,
	session_start tinytext,
	session_end tinytext,
	hits int(11) DEFAULT '0' NOT NULL,
	first_page int(11) DEFAULT '0' NOT NULL,
	last_page int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=InnoDB;



#
# Table structure for table 'tx_feuserstat_tx_feuserstat_pagestats'
#
CREATE TABLE tx_feuserstat_tx_feuserstat_pagestats (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	fe_user int(11) DEFAULT '0' NOT NULL,
	sesstat_uid int(11) DEFAULT '0' NOT NULL,
	page_uid int(11) DEFAULT '0' NOT NULL,
	hits int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=InnoDB;