Not included is the DB structure, or cleanup scripts.  This will
be uploaded shortly.

SIMPLE sturcture is:

attend.cur_month
	FIELD		TYPE		NULL	DEFAULT
	username	varchar(124)
	dstamp		date			0000-00-00
	ipaddress	varchar(124)
	tab		varchar(124)	YES
	tstamp		timestamp	YES	CURRENT_TIMESTAMP

Primary Key is a dual field, username+dstamp

	KEY NAME	FIELD		TYPE
	PRIMARY		username	PRIMARY
			dstamp

