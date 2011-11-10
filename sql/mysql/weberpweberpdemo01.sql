-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2009 年 07 月 09 日 05:16
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `weberpdemo`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `accountgroups`
-- 

CREATE TABLE `accountgroups` (
  `groupname` char(30) NOT NULL default '',
  `sectioninaccounts` int(11) NOT NULL default '0',
  `pandl` tinyint(4) NOT NULL default '1',
  `sequenceintb` smallint(6) NOT NULL default '0',
  `parentgroupname` varchar(30) NOT NULL,
  PRIMARY KEY  (`groupname`),
  KEY `SequenceInTB` (`sequenceintb`),
  KEY `sectioninaccounts` (`sectioninaccounts`),
  KEY `parentgroupname` (`parentgroupname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `accountgroups`
-- 

INSERT INTO `accountgroups` (`groupname`, `sectioninaccounts`, `pandl`, `sequenceintb`, `parentgroupname`) VALUES 
('BBQs', 5, 1, 6000, 'Promotions'),
('Cost of Goods Sold', 2, 1, 5000, ''),
('Current Assets', 20, 0, 1000, ''),
('Equity', 50, 0, 3000, ''),
('Fixed Assets', 10, 0, 500, ''),
('Giveaways', 5, 1, 6000, 'Promotions'),
('Income Tax', 5, 1, 9000, ''),
('Liabilities', 30, 0, 2000, ''),
('Marketing Expenses', 5, 1, 6000, ''),
('Operating Expenses', 5, 1, 7000, ''),
('Other Revenue and Expenses', 5, 1, 8000, ''),
('Outward Freight', 2, 1, 5000, 'Cost of Goods Sold'),
('Promotions', 5, 1, 6000, 'Marketing Expenses'),
('Revenue', 1, 1, 4000, ''),
('Sales', 1, 1, 10, '');

-- --------------------------------------------------------

-- 
-- 表的结构 `accountsection`
-- 

CREATE TABLE `accountsection` (
  `sectionid` int(11) NOT NULL default '0',
  `sectionname` text NOT NULL,
  PRIMARY KEY  (`sectionid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `accountsection`
-- 

INSERT INTO `accountsection` (`sectionid`, `sectionname`) VALUES 
(1, 'Income'),
(2, 'Cost Of Sales'),
(5, 'Overheads'),
(10, 'Fixed Assets'),
(20, 'Amounts Receivable'),
(30, 'Amounts Payable'),
(50, 'Financed By');

-- --------------------------------------------------------

-- 
-- 表的结构 `areas`
-- 

CREATE TABLE `areas` (
  `areacode` char(3) NOT NULL,
  `areadescription` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`areacode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `areas`
-- 

INSERT INTO `areas` (`areacode`, `areadescription`) VALUES 
('01', 'China');

-- --------------------------------------------------------

-- 
-- 表的结构 `assetmanager`
-- 

CREATE TABLE `assetmanager` (
  `id` int(11) NOT NULL auto_increment,
  `serialno` varchar(30) NOT NULL default '',
  `assetglcode` int(11) NOT NULL default '0',
  `depnglcode` int(11) NOT NULL default '0',
  `description` varchar(30) NOT NULL default '',
  `lifetime` int(11) NOT NULL default '0',
  `location` varchar(15) NOT NULL default '',
  `cost` double NOT NULL default '0',
  `depn` double NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `assetmanager`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `audittrail`
-- 

CREATE TABLE `audittrail` (
  `transactiondate` datetime NOT NULL default '0000-00-00 00:00:00',
  `userid` varchar(20) NOT NULL default '',
  `querystring` text,
  KEY `UserID` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `audittrail`
-- 

INSERT INTO `audittrail` (`transactiondate`, `userid`, `querystring`) VALUES 
('2009-07-07 06:26:59', 'admin', 'UPDATE config\n				SET confvalue=''2009-07-07''\n				WHERE confname=''DB_Maintenance_LastRun'''),
('2009-07-07 06:26:59', 'admin', 'DELETE FROM audittrail\n						WHERE  transactiondate &lt;= ''2009-06-07'''),
('2009-07-07 06:30:41', 'admin', 'UPDATE www_users\n				SET displayrecordsmax=50,\n					theme=''professional'',\n					language=''zh_CN'',\n					email=''''\n				WHERE userid = ''admin'''),
('2009-07-07 06:31:01', 'admin', 'UPDATE www_users\n				SET displayrecordsmax=50,\n					theme=''professional'',\n					language=''zh_CN'',\n					email=''''\n				WHERE userid = ''admin'''),
('2009-07-07 06:31:15', 'admin', 'UPDATE www_users\n				SET displayrecordsmax=50,\n					theme=''professional'',\n					language=''zh_CN'',\n					email=''''\n				WHERE userid = ''admin'''),
('2009-07-07 06:38:24', 'admin', 'INSERT INTO salestypes\n						(typeabbrev,\n			 			 sales_type)\n				VALUES (''01'',\n					''ÄÚÏú'')'),
('2009-07-07 06:38:24', 'admin', 'UPDATE config\n					SET confvalue=''01''\n					WHERE confname=''DefaultPriceList'''),
('2009-07-07 06:38:42', 'admin', 'INSERT INTO salestypes\n						(typeabbrev,\n			 			 sales_type)\n				VALUES (''02'',\n					''³ö¿Ú'')'),
('2009-07-07 06:39:56', 'admin', 'INSERT INTO debtortype\n						(typename)\n					VALUES (''´úÀí'')'),
('2009-07-07 06:40:00', 'admin', 'DELETE FROM debtortype WHERE typeid=''2'''),
('2009-07-07 07:29:05', 'admin', 'INSERT INTO currencies (currency,\n    					currabrev,\n						country,\n						hundredsname,\n						rate)\n				VALUES (''China Renminbi'',\n					''RMB'',\n					''China'',\n					'''',\n					5.44)'),
('2009-07-07 07:29:36', 'admin', 'INSERT INTO taxprovinces (\n							taxprovincename )\n				VALUES (\n					''RPC''\n					)'),
('2009-07-07 07:29:36', 'admin', 'INSERT INTO taxauthrates (taxauthority, dispatchtaxprovince, taxcatid)\n					SELECT taxauthorities.taxid, 2, taxcategories.taxcatid\n						FROM taxauthorities CROSS JOIN taxcategories'),
('2009-07-07 07:31:12', 'admin', 'UPDATE config SET confvalue = ''RMB'' WHERE confname = ''CountryOfOperation'''),
('2009-07-07 07:31:12', 'admin', 'UPDATE config SET confvalue = ''companies/weberpdemo/part_pics'' WHERE confname = ''part_pics_dir'''),
('2009-07-07 07:31:12', 'admin', 'UPDATE config SET confvalue = ''companies/weberpdemo/reportwriter'' WHERE confname = ''reports_dir'''),
('2009-07-07 07:31:12', 'admin', 'UPDATE config SET confvalue = ''2009-08-31'' WHERE confname = ''ProhibitPostingsBefore'''),
('2009-07-07 07:33:25', 'admin', 'INSERT INTO stockcategory (categoryid,\n                                       stocktype,\n                                       categorydescription,\n                                       stockact,\n                                       adjglact,\n                                       purchpricevaract,\n                                       materialuseagevarac,\n                                       wipact)\n                                       VALUES (\n                                       ''01'',\n                                       ''F'',\n                                       ''WalkieTalkie Chips'',\n                                       1,\n                                       1,\n                                       1,\n                                       1,\n                                       1010)'),
('2009-07-07 07:33:54', 'admin', 'INSERT INTO unitsofmeasure (\n						unitname )\n				VALUES (\n					''¿Å''\n					)'),
('2009-07-07 07:34:49', 'admin', 'INSERT INTO locations (\n					loccode,\n					locationname,\n					deladd1,\n					deladd2,\n					deladd3,\n					deladd4,\n					deladd5,\n					deladd6,\n					tel,\n					fax,\n					email,\n					contact,\n					taxprovinceid,\n					managed\n					)\n			VALUES (\n				''HZ'',\n				''º¼ÖÝ²Ö¿â'',\n				'' '',\n				'''',\n				'''',\n				'''',\n				'''',\n				'''',\n				'''',\n				'''',\n				'''',\n				'''',\n				1,\n				0\n			)'),
('2009-07-07 07:34:49', 'admin', 'INSERT INTO locstock (\n					loccode,\n					stockid,\n					quantity,\n					reorderlevel)\n			SELECT ''HZ'',\n				stockmaster.stockid,\n				0,\n				0\n			FROM stockmaster'),
('2009-07-07 07:34:56', 'admin', 'DELETE FROM locstock WHERE loccode =''TOR'''),
('2009-07-07 07:34:56', 'admin', 'DELETE FROM locations WHERE loccode=''TOR'''),
('2009-07-07 07:39:07', 'admin', 'INSERT INTO debtorsmaster (\n							debtorno,\n							name,\n							address1,\n							address2,\n							address3,\n							address4,\n							address5,\n							address6,\n							currcode,\n							clientsince,\n							holdreason,\n							paymentterms,\n							discount,\n							discountcode,\n							pymtdiscount,\n							creditlimit,\n							salestype,\n							invaddrbranch,\n							taxref,\n							customerpoline,\n							typeid)\n				VALUES (''HYT'',\n					''ÉîÛÚºÃÒ×Í¨'',\n					''ÉîÛÚÊÐÄÏÉ½Çø'',\n					'''',\n					'''',\n					'''',\n					'''',\n					'''',\n					''AUD'',\n					''2008/01/01'',\n					1,\n					''20'',\n					0,\n					'''',\n					0,\n					1000,\n					''01'',\n					''0'',\n					'''',\n					''0'',\n					''1''\n					)'),
('2009-07-07 07:40:18', 'admin', 'INSERT INTO salesman (salesmancode,\n						salesmanname,\n						commissionrate1,\n						commissionrate2,\n						breakpoint,\n						smantel,\n						smanfax)\n				VALUES (''01'',\n					''qinkunsong'',\n					0,\n					0,\n					0,\n					'''',\n					''''\n					)'),
('2009-07-07 07:50:14', 'admin', 'INSERT INTO custcontacts (debtorno,contactname,role,phoneno,notes)\n				VALUES (\n					''HYT'',\n					''ÎÅÐ¡Áá'',\n					''²É¹º¹¤³ÌÊ¦'',\n					'''',\n					''''\n					)'),
('2009-07-07 07:51:05', 'admin', 'UPDATE debtorsmaster SET\n					name=''ÉîÛÚºÃÒ×Í¨'',\n					address1=''ÉîÛÚÊÐÄÏÉ½Çø'',\n					address2='''',\n					address3='''',\n					address4='''',\n					address5='''',\n					address6='''',\n					currcode=''RMB'',\n					clientsince=''2008/01/01'',\n					holdreason=''1'',\n					paymentterms=''20'',\n					discount=0,\n					discountcode='''',\n					pymtdiscount=0,\n					creditlimit=1000,\n					salestype = ''01'',\n					invaddrbranch=''0'',\n					taxref='''',\n					customerpoline=''0'',\n					typeid=''1''\n				  WHERE debtorno = ''HYT'''),
('2009-07-07 07:52:25', 'admin', 'INSERT INTO debtorsmaster (\n							debtorno,\n							name,\n							address1,\n							address2,\n							address3,\n							address4,\n							address5,\n							address6,\n							currcode,\n							clientsince,\n							holdreason,\n							paymentterms,\n							discount,\n							discountcode,\n							pymtdiscount,\n							creditlimit,\n							salestype,\n							invaddrbranch,\n							taxref,\n							customerpoline,\n							typeid)\n				VALUES (''0002'',\n					''°²ÔÃ'',\n					''ÉîÛÚ±¦É½ÊÐ'',\n					'''',\n					'''',\n					'''',\n					'''',\n					'''',\n					''RMB'',\n					''2008/01/01'',\n					1,\n					''20'',\n					0,\n					'''',\n					0,\n					1000,\n					''02'',\n					''0'',\n					'''',\n					''0'',\n					''1''\n					)'),
('2009-07-07 07:52:46', 'admin', 'INSERT INTO areas (areacode,\n						areadescription)\n				VALUES (\n					''01'',\n					''China''\n					)'),
('2009-07-07 07:52:59', 'admin', 'INSERT INTO custbranch (branchcode,\n						debtorno,\n						brname,\n						braddress1,\n						braddress2,\n						braddress3,\n						braddress4,\n						braddress5,\n						braddress6,\n						lat,\n						lng,\n 						specialinstructions,\n						estdeliverydays,\n						fwddate,\n						salesman,\n						phoneno,\n						faxno,\n						contactname,\n						area,\n						email,\n						taxgroupid,\n						defaultlocation,\n						brpostaddr1,\n						brpostaddr2,\n						brpostaddr3,\n						brpostaddr4,\n						disabletrans,\n						defaultshipvia,\n						custbranchcode,\n                       			        deliverblind)\n				VALUES (''0002'',\n					''0002'',\n					''°²ÔÃ'',\n					''ÉîÛÚ±¦É½ÊÐ'',\n					'''',\n					'''',\n					'''',\n					'''',\n					'''',\n					0,\n					0,\n					'''',\n					0,\n					0,\n					''01'',\n					'''',\n					'''',\n					'''',\n					''01'',\n					'''',\n					1,\n					''HZ'',\n					'''',\n					'''',\n					'''',\n					'''',\n					0,\n					1,\n					'''',\n					1\n					)'),
('2009-07-07 08:00:23', 'admin', 'INSERT INTO stockmaster (\n							stockid,\n							description,\n							longdescription,\n							categoryid,\n							units,\n							mbflag,\n							eoq,\n							discontinued,\n							controlled,\n							serialised,\n							perishable,\n							volume,\n							kgs,\n							barcode,\n							discountcategory,\n							taxcatid,\n							decimalplaces,\n							appendfile)\n						VALUES (''SRT3210'',\n							''SRT3210 Chips'',\n							''Walkie Talkie Baseband chips'',\n							''01'',\n							''¿Å'',\n							''B'',\n							90,\n							0,\n							0,\n							0,\n							0,\n							0,\n							0,\n							'''',\n							'''',\n							1,\n							0,\n							''0''\n							)'),
('2009-07-07 08:00:23', 'admin', 'INSERT INTO locstock (loccode,\n													stockid)\n										SELECT locations.loccode,\n										''SRT3210''\n										FROM locations'),
('2009-07-07 08:01:27', 'admin', 'INSERT INTO stockmaster (\n							stockid,\n							description,\n							longdescription,\n							categoryid,\n							units,\n							mbflag,\n							eoq,\n							discontinued,\n							controlled,\n							serialised,\n							perishable,\n							volume,\n							kgs,\n							barcode,\n							discountcategory,\n							taxcatid,\n							decimalplaces,\n							appendfile)\n						VALUES (''SRT3210DICE'',\n							''SRT3210 Dice'',\n							''WT SRT3210 Dice'',\n							''01'',\n							''each'',\n							''B'',\n							0,\n							0,\n							0,\n							0,\n							0,\n							0,\n							0,\n							'''',\n							'''',\n							1,\n							0,\n							''0''\n							)'),
('2009-07-07 08:01:27', 'admin', 'INSERT INTO locstock (loccode,\n													stockid)\n										SELECT locations.loccode,\n										''SRT3210DICE''\n										FROM locations'),
('2009-07-07 08:07:36', 'admin', 'INSERT INTO suppliers (supplierid,\n							suppname,\n							address1,\n							address2,\n							address3,\n							address4,\n							currcode,\n							suppliersince,\n							paymentterms,\n							bankpartics,\n							bankref,\n							bankact,\n							remittance,\n							taxgroupid,\n							factorcompanyid,\n							lat,\n							lng,\n							taxref)\n					 VALUES (''HHNEC'',\n					 	''»ªºçNEC'',\n						'''',\n						'''',\n						'''',\n						'''',\n						''AUD'',\n						''2009/07/07'',\n						''20'',\n						'''',\n						''0'',\n						'''',\n                       	0,\n                       	1,\n                       	1,\n                       	''0'',\n                       	''0'',\n                       	'''')'),
('2009-07-07 08:08:35', 'admin', 'INSERT INTO suppliers (supplierid,\n							suppname,\n							address1,\n							address2,\n							address3,\n							address4,\n							currcode,\n							suppliersince,\n							paymentterms,\n							bankpartics,\n							bankref,\n							bankact,\n							remittance,\n							taxgroupid,\n							factorcompanyid,\n							lat,\n							lng,\n							taxref)\n					 VALUES (''HEJIAN'',\n					 	''ºÍ½¢'',\n						'''',\n						'''',\n						'''',\n						'''',\n						''AUD'',\n						''2009/07/07'',\n						''20'',\n						'''',\n						''0'',\n						'''',\n                       	0,\n                       	1,\n                       	1,\n                       	''0'',\n                       	''0'',\n                       	'''')'),
('2009-07-07 08:10:44', 'admin', 'UPDATE companies SET\n				coyname=''Sicomm Technology Ltd'',\n				companynumber = '''',\n				gstno=''not entered yet'',\n				regoffice1=''123 Web Way'',\n				regoffice2=''PO Box 123'',\n				regoffice3=''Queen Street'',\n				regoffice4=''Melbourne'',\n				regoffice5=''Victoria 3043'',\n				regoffice6=''Australia'',\n				telephone=''+61 3 4567 8901'',\n				fax=''+61 3 4567 8902'',\n				email=''weberp@weberpdemo.com'',\n				currencydefault=''AUD'',\n				debtorsact=''1100'',\n				pytdiscountact=''4900'',\n				creditorsact=''2100'',\n				payrollact=''2400'',\n				grnact=''2150'',\n				exchangediffact=''4200'',\n				purchasesexchangediffact=''5200'',\n				retainedearnings=''3500'',\n				gllink_debtors=''1'',\n				gllink_creditors=''1'',\n				gllink_stock=''1'',\n				freightact=''5600''\n			WHERE coycode=1'),
('2009-07-07 08:10:44', 'admin', 'UPDATE currencies SET rate=rate/1'),
('2009-07-07 08:33:18', 'admin', 'INSERT INTO periods (periodno, lastdate_in_period) VALUES (2, ''2009/09/30'')'),
('2009-07-07 08:33:18', 'admin', 'INSERT INTO chartdetails (accountcode, period)\n						SELECT chartmaster.accountcode, periods.periodno\n							FROM chartmaster\n								CROSS  JOIN periods\n						WHERE ( chartmaster.accountcode, periods.periodno ) NOT\n							IN ( SELECT chartdetails.accountcode, chartdetails.period FROM chartdetails )'),
('2009-07-07 08:33:18', 'admin', 'INSERT INTO stockmoves (\n				stockid,\n				type,\n				transno,\n				loccode,\n				trandate,\n				prd,\n				reference,\n				qty,\n				newqoh)\n			VALUES (\n				''SRT3210'',\n				17,\n				1,\n				''HZ'',\n				''2009/07/07'',\n				1,\n				''Æð³õÅÌµã'',\n				3000,\n				3000\n			)'),
('2009-07-07 08:33:18', 'admin', 'UPDATE locstock SET quantity = quantity + 3000\n				WHERE stockid=''SRT3210''\n				AND loccode=''HZ'''),
('2009-07-07 08:40:18', 'admin', 'INSERT INTO custcontacts (debtorno,contactname,role,phoneno,notes)\n				VALUES (\n					''0002'',\n					''ÌÆèaÓ¢'',\n					''²É¹º¹¤³ÌÊ¦'',\n					'''',\n					''''\n					)'),
('2009-07-07 08:41:26', 'admin', 'UPDATE custbranch SET brname = ''Onreal'',\n						braddress1 = ''ÉîÛÚ±¦É½ÊÐ'',\n						braddress2 = '''',\n						braddress3 = '''',\n						braddress4 = '''',\n						braddress5 = '''',\n						braddress6 = '''',\n						lat = 0,\n						lng = 0,\n						specialinstructions = '''',\n						phoneno='''',\n						faxno='''',\n						fwddate= 0,\n						contactname='''',\n						salesman= ''01'',\n						area=''01'',\n						estdeliverydays =0,\n						email='''',\n						taxgroupid=1,\n						defaultlocation=''HZ'',\n						brpostaddr1 = '''',\n						brpostaddr2 = '''',\n						brpostaddr3 = '''',\n						brpostaddr4 = '''',\n						disabletrans=0,\n						defaultshipvia=1,\n						custbranchcode='''',\n						deliverblind=1\n					WHERE branchcode = ''0002'' AND debtorno=''0002'''),
('2009-07-07 08:51:55', 'admin', 'UPDATE stockmaster\n						SET longdescription=''WT SRT3210 Dice'',\n							description=''SRT3210 Dice'',\n							discontinued=0,\n							controlled=0,\n							serialised=0,\n							perishable=0,\n							categoryid=''01'',\n							units=''each'',\n							mbflag=''B'',\n							eoq=0,\n							volume=0.0000,\n							kgs=0.0000,\n							barcode='''',\n							discountcategory='''',\n							taxcatid=1,\n							decimalplaces=0,\n							appendfile=''0''\n					WHERE stockid=''SRT3210DICE'''),
('2009-07-07 08:51:55', 'admin', 'DELETE FROM stockitemproperties\n										WHERE stockid =''SRT3210DICE'''),
('2009-07-07 08:54:53', 'admin', 'INSERT INTO suppliers (supplierid,\n							suppname,\n							address1,\n							address2,\n							address3,\n							address4,\n							currcode,\n							suppliersince,\n							paymentterms,\n							bankpartics,\n							bankref,\n							bankact,\n							remittance,\n							taxgroupid,\n							factorcompanyid,\n							lat,\n							lng,\n							taxref)\n					 VALUES (''SILAN'',\n					 	''Silan'',\n						''º¼ÖÝ'',\n						'''',\n						'''',\n						'''',\n						''AUD'',\n						''2009/07/07'',\n						''20'',\n						'''',\n						''0'',\n						'''',\n                       	0,\n                       	1,\n                       	1,\n                       	''0'',\n                       	''0'',\n                       	'''')'),
('2009-07-07 08:58:35', 'admin', 'INSERT INTO stockmaster (\n							stockid,\n							description,\n							longdescription,\n							categoryid,\n							units,\n							mbflag,\n							eoq,\n							discontinued,\n							controlled,\n							serialised,\n							perishable,\n							volume,\n							kgs,\n							barcode,\n							discountcategory,\n							taxcatid,\n							decimalplaces,\n							appendfile)\n						VALUES (''SRT3210WAFER'',\n							''SRT3210 Wafer'',\n							''SRT3210 Wafer '',\n							''01'',\n							''each'',\n							''B'',\n							0,\n							0,\n							0,\n							0,\n							0,\n							0,\n							0,\n							'''',\n							'''',\n							1,\n							0,\n							''0''\n							)'),
('2009-07-07 08:58:35', 'admin', 'INSERT INTO locstock (loccode,\n													stockid)\n										SELECT locations.loccode,\n										''SRT3210WAFER''\n										FROM locations'),
('2009-07-07 09:02:04', 'admin', 'INSERT INTO suppliers (supplierid,\n							suppname,\n							address1,\n							address2,\n							address3,\n							address4,\n							currcode,\n							suppliersince,\n							paymentterms,\n							bankpartics,\n							bankref,\n							bankact,\n							remittance,\n							taxgroupid,\n							factorcompanyid,\n							lat,\n							lng,\n							taxref)\n					 VALUES (''FUJITSU'',\n					 	''¸»Ê¿Í¨'',\n						''ÄÏÍ¨'',\n						'''',\n						'''',\n						'''',\n						''AUD'',\n						''2009/07/07'',\n						''20'',\n						'''',\n						''0'',\n						'''',\n                       	0,\n                       	1,\n                       	1,\n                       	''0'',\n                       	''0'',\n                       	'''')'),
('2009-07-07 09:15:11', 'admin', 'INSERT INTO workorders (wo,\n                                                     loccode,\n                                                     requiredby,\n                                                     startdate)\n                                     VALUES (1,\n                                            ''MEL'',\n                                            ''2009-07-07'',\n                                            ''2009-07-07'')'),
('2009-07-07 09:15:26', 'admin', 'UPDATE workorders SET requiredby=''2009/07/07'',\n												loccode=''HZ''\n			        	    WHERE wo=1'),
('2009-07-07 09:42:27', 'admin', 'INSERT INTO purchorders (supplierno,\n		     					comments,\n							orddate,\n							rate,\n							initiator,\n							requisitionno,\n							intostocklocation,\n							deladd1,\n							deladd2,\n							deladd3,\n							deladd4,\n							deladd5,\n							deladd6)\n				VALUES(\n				''HHNEC'',\n				'''',\n				''2009-07-07'',\n				1,\n				'''',\n				''0'',\n				''HZ'',\n				''1234 Collins Street'',\n				''Melbourne'',\n				''Victoria 2345'',\n				'''',\n				'''',\n				''Australia''\n				)'),
('2009-07-07 09:42:27', 'admin', 'INSERT INTO purchorderdetails (\n							orderno,\n							itemcode,\n							deliverydate,\n							itemdescription,\n							glcode,\n							unitprice,\n							quantityord,\n							shiptref,\n							jobref\n							)\n						VALUES (\n							1,\n							''SRT3210WAFER'',\n							''2009/07/08'',\n							''SRT3210 Wafer'',\n							1,\n							5000,\n							50,\n							''0'',\n							''''\n						)'),
('2009-07-07 09:46:11', 'admin', 'INSERT INTO periods (periodno, lastdate_in_period) VALUES (3, ''2009/10/31'')'),
('2009-07-07 09:46:11', 'admin', 'INSERT INTO chartdetails (accountcode, period)\n						SELECT chartmaster.accountcode, periods.periodno\n							FROM chartmaster\n								CROSS  JOIN periods\n						WHERE ( chartmaster.accountcode, periods.periodno ) NOT\n							IN ( SELECT chartdetails.accountcode, chartdetails.period FROM chartdetails )'),
('2009-07-07 09:46:11', 'admin', 'UPDATE purchorderdetails SET\n							quantityrecd = quantityrecd + 50,\n							stdcostunit=0,\n							completed=1\n					WHERE podetailitem = 1'),
('2009-07-07 09:46:11', 'admin', 'INSERT INTO grns (grnbatch,\n						podetailitem,\n						itemcode,\n						itemdescription,\n						deliverydate,\n						qtyrecd,\n						supplierid,\n						stdcostunit)\n				VALUES (1,\n					1,\n					''SRT3210WAFER'',\n					''SRT3210 Wafer'',\n					''2009/07/07'',\n					50,\n					''HHNEC'',\n					0.0000)'),
('2009-07-07 09:46:11', 'admin', 'UPDATE locstock\n					SET quantity = locstock.quantity + 50\n					WHERE locstock.stockid = ''SRT3210WAFER''\n					AND loccode = ''HZ'''),
('2009-07-07 09:46:11', 'admin', 'INSERT INTO stockmoves (stockid,\n								type,\n								transno,\n								loccode,\n								trandate,\n								price,\n								prd,\n								reference,\n								qty,\n								standardcost,\n								newqoh)\n					VALUES (''SRT3210WAFER'',\n						25,\n						1, ''HZ'',\n						''2009/07/07'',\n						5000,\n						1,\n						''HHNEC (»ªºçNEC) - 1'',\n						50,\n						0,\n						50)'),
('2009-07-07 09:46:11', 'admin', 'INSERT INTO gltrans (type,\n								typeno,\n								trandate,\n								periodno,\n								account,\n								narrative,\n								amount)\n						VALUES (25,\n							1,\n							''2009/07/07'',\n							1,\n							1,\n							''PO: 1 HHNEC - SRT3210WAFER - SRT3210 Wafer x 50 @ 0.00'',\n							0)'),
('2009-07-07 09:46:11', 'admin', 'INSERT INTO gltrans (type,\n								typeno,\n								trandate,\n								periodno,\n								account,\n								narrative,\n								amount)\n						VALUES (25,\n							1,\n							''2009/07/07'',\n							1,\n							2150, ''²É¹º¶©µ¥: 1 HHNEC - SRT3210WAFER - SRT3210 Wafer x 50 @ 0.00'',\n							0)'),
('2009-07-07 09:50:26', 'admin', 'INSERT INTO purchorders (supplierno,\n		     					comments,\n							orddate,\n							rate,\n							initiator,\n							requisitionno,\n							intostocklocation,\n							deladd1,\n							deladd2,\n							deladd3,\n							deladd4,\n							deladd5,\n							deladd6)\n				VALUES(\n				''HHNEC'',\n				'''',\n				''2009-07-07'',\n				1,\n				'''',\n				''0'',\n				''HZ'',\n				''1234 Collins Street'',\n				''Melbourne'',\n				''Victoria 2345'',\n				'''',\n				'''',\n				''Australia''\n				)'),
('2009-07-07 09:50:26', 'admin', 'INSERT INTO purchorderdetails (\n							orderno,\n							itemcode,\n							deliverydate,\n							itemdescription,\n							glcode,\n							unitprice,\n							quantityord,\n							shiptref,\n							jobref\n							)\n						VALUES (\n							2,\n							''SRT3210WAFER'',\n							''2009/07/08'',\n							''SRT3210 Wafer'',\n							1,\n							5000,\n							51,\n							''0'',\n							''''\n						)'),
('2009-07-07 09:55:06', 'admin', 'INSERT INTO purchorders (supplierno,\n		     					comments,\n							orddate,\n							rate,\n							initiator,\n							requisitionno,\n							intostocklocation,\n							deladd1,\n							deladd2,\n							deladd3,\n							deladd4,\n							deladd5,\n							deladd6)\n				VALUES(\n				''SILAN'',\n				'''',\n				''2009-07-07'',\n				1,\n				'''',\n				''0'',\n				''HZ'',\n				''1234 Collins Street'',\n				''Melbourne'',\n				''Victoria 2345'',\n				'''',\n				'''',\n				''Australia''\n				)'),
('2009-07-07 09:55:06', 'admin', 'INSERT INTO purchorderdetails (\n							orderno,\n							itemcode,\n							deliverydate,\n							itemdescription,\n							glcode,\n							unitprice,\n							quantityord,\n							shiptref,\n							jobref\n							)\n						VALUES (\n							3,\n							''SRT3210'',\n							''2009/07/08'',\n							''SRT3210 Chips'',\n							1,\n							4,\n							30000,\n							''0'',\n							''''\n						)'),
('2009-07-07 09:56:39', 'admin', 'UPDATE purchorderdetails SET\n							quantityrecd = quantityrecd + 30000,\n							stdcostunit=0,\n							completed=1\n					WHERE podetailitem = 3'),
('2009-07-07 09:56:39', 'admin', 'INSERT INTO grns (grnbatch,\n						podetailitem,\n						itemcode,\n						itemdescription,\n						deliverydate,\n						qtyrecd,\n						supplierid,\n						stdcostunit)\n				VALUES (2,\n					3,\n					''SRT3210'',\n					''SRT3210 Chips'',\n					''2009/07/07'',\n					30000,\n					''SILAN'',\n					0.0000)'),
('2009-07-07 09:56:39', 'admin', 'UPDATE locstock\n					SET quantity = locstock.quantity + 30000\n					WHERE locstock.stockid = ''SRT3210''\n					AND loccode = ''HZ'''),
('2009-07-07 09:56:39', 'admin', 'INSERT INTO stockmoves (stockid,\n								type,\n								transno,\n								loccode,\n								trandate,\n								price,\n								prd,\n								reference,\n								qty,\n								standardcost,\n								newqoh)\n					VALUES (''SRT3210'',\n						25,\n						2, ''HZ'',\n						''2009/07/07'',\n						4,\n						1,\n						''SILAN (Silan) - 3'',\n						30000,\n						0,\n						33000)'),
('2009-07-07 09:56:39', 'admin', 'INSERT INTO gltrans (type,\n								typeno,\n								trandate,\n								periodno,\n								account,\n								narrative,\n								amount)\n						VALUES (25,\n							2,\n							''2009/07/07'',\n							1,\n							1,\n							''PO: 3 SILAN - SRT3210 - SRT3210 Chips x 30000 @ 0.00'',\n							0)'),
('2009-07-07 09:56:39', 'admin', 'INSERT INTO gltrans (type,\n								typeno,\n								trandate,\n								periodno,\n								account,\n								narrative,\n								amount)\n						VALUES (25,\n							2,\n							''2009/07/07'',\n							1,\n							2150, ''²É¹º¶©µ¥: 3 SILAN - SRT3210 - SRT3210 Chips x 30000 @ 0.00'',\n							0)'),
('2009-07-07 10:04:15', 'admin', 'INSERT INTO prices (stockid,\n						typeabbrev,\n						currabrev,\n						debtorno,\n						price)\n				VALUES (''SRT3210DICE'',\n					''01'',\n					''AUD'',\n					'''',\n					4.8)'),
('2009-07-07 10:04:30', 'admin', 'INSERT INTO prices (stockid,\n						typeabbrev,\n						currabrev,\n						debtorno,\n						price)\n				VALUES (''SRT3210'',\n					''01'',\n					''AUD'',\n					'''',\n					10)'),
('2009-07-07 13:00:40', 'admin', 'INSERT INTO shipments (shiptref,\n							vessel,\n							voyageref,\n							eta,\n							supplierid)\n					VALUES (1,\n						''sunfeng'',\n						''0001'',\n						''2009/8/9'',\n						''SILAN'')'),
('2009-07-07 13:00:40', 'admin', 'UPDATE purchorderdetails SET shiptref = 1 \n			WHERE podetailitem = 3'),
('2009-07-07 13:00:42', 'admin', 'UPDATE shipments SET vessel=''sunfeng'',\n							voyageref=''0001'',\n							eta=''2009/8/9''\n					WHERE shiptref =1'),
('2009-07-07 13:00:43', 'admin', 'UPDATE purchorderdetails \n						SET deliverydate =''2009/8/9'' \n					WHERE podetailitem=3'),
('2009-07-07 13:00:57', 'admin', 'UPDATE shipments SET vessel=''sunfeng'',\n							voyageref=''0001'',\n							eta=''2009/8/9''\n					WHERE shiptref =1'),
('2009-07-07 13:00:59', 'admin', 'UPDATE shipments SET vessel=''sunfeng'',\n							voyageref=''0001'',\n							eta=''2009/8/9''\n					WHERE shiptref =1'),
('2009-07-07 13:05:54', 'admin', 'INSERT INTO gltrans (type,\n							typeno,\n							trandate,\n							periodno,\n							account,\n							narrative,\n							amount)\n						VALUES (35,\n							1,\n							''2009-07-07'',\n							1,\n							1,\n							''SRT3210 ³É±¾ÊÇ 0 ¸ü¸Äµ½ 6 x ÔÚÊÖÉÏ¶©µ¥ 33000'',\n							-198000)'),
('2009-07-07 13:05:54', 'admin', 'INSERT INTO gltrans (type,\n							typeno,\n							trandate,\n							periodno,\n							account,\n							narrative,\n							amount)\n						VALUES (35,\n							1,\n							''2009-07-07'',\n							1,\n							1,\n							''SRT3210 ³É±¾ÊÇ 0 ¸ü¸Äµ½ 6 x ÔÚÊÖÉÏ¶©µ¥ 33000'',\n							198000)'),
('2009-07-07 13:05:54', 'admin', 'UPDATE stockmaster SET\n					materialcost=6,\n					labourcost=0,\n					overheadcost=0,\n					lastcost=0\n			WHERE stockid=''SRT3210'''),
('2009-07-07 13:07:36', 'admin', 'UPDATE salesorderdetails\n						SET quantity=3000,\n						unitprice=12,\n						discountpercent=0,\n						narrative ='''',\n						itemdue = ''07/07/2009'',\n						poline = ''''\n					WHERE orderno=0\n					AND orderlineno=0'),
('2009-07-07 13:08:05', 'admin', 'INSERT INTO salesorders (\n								orderno,\n								debtorno,\n								branchcode,\n								customerref,\n								comments,\n								orddate,\n								ordertype,\n								shipvia,\n								deliverto,\n								deladd1,\n								deladd2,\n								deladd3,\n								deladd4,\n								deladd5,\n								deladd6,\n								contactphone,\n								contactemail,\n								freightcost,\n								fromstkloc,\n								deliverydate,\n								quotedate,\n								confirmeddate,\n								quotation,\n								deliverblind)\n							VALUES (\n								1,\n								''0002'',\n								''0002'',\n								'''',\n								'''',\n								''2009-07-07 13:08'',\n								''02'',\n								1,\n								''Onreal'',\n								''ÉîÛÚ±¦É½ÊÐ'',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								0,\n								''HZ'',\n								''2009/07/07'',\n								''2009/07/07'',\n								''2009/07/07'',\n								0,\n								1\n								)'),
('2009-07-07 13:08:05', 'admin', 'INSERT INTO salesorderdetails (\n						orderlineno,\n						orderno,\n						stkcode,\n						unitprice,\n						quantity,\n						discountpercent,\n						narrative,\n						poline,\n						itemdue)\n					VALUES (0,\n					1,\n					''SRT3210'',\n					12,\n					3000,\n					0,\n					'''',\n					'''',\n					''2009/07/07''\n				)'),
('2009-07-07 13:12:04', 'admin', 'INSERT INTO purchdata (supplierno,\n					stockid,\n					price,\n					effectivefrom,\n					suppliersuom,\n					conversionfactor,\n					supplierdescription,\n					leadtime,\n					preferred)\n			VALUES (''SILAN'',\n				''SRT3210'',\n				6,\n				''2009/07/07'',\n				''¿Å'',\n				1,\n				'''',\n				8,\n				1)'),
('2009-07-07 13:12:19', 'admin', 'UPDATE purchdata SET\n			    price=6.0000,\n			    effectivefrom=''2009/07/07'',\n				suppliersuom=''¿Å'',\n				conversionfactor=1,\n				supplierdescription='''',\n				leadtime=60,\n				preferred=1\n		WHERE purchdata.stockid=''SRT3210''\n		AND purchdata.supplierno=''SILAN'''),
('2009-07-07 13:21:04', 'admin', 'UPDATE stockmaster\n						SET longdescription=''WT SRT3210 Dice'',\n							description=''SRT3210 Dice'',\n							discontinued=0,\n							controlled=0,\n							serialised=0,\n							perishable=0,\n							categoryid=''01'',\n							units=''each'',\n							mbflag=''M'',\n							eoq=0,\n							volume=0.0000,\n							kgs=0.0000,\n							barcode='''',\n							discountcategory='''',\n							taxcatid=1,\n							decimalplaces=0,\n							appendfile=''0''\n					WHERE stockid=''SRT3210DICE'''),
('2009-07-07 13:21:04', 'admin', 'DELETE FROM stockitemproperties\n										WHERE stockid =''SRT3210DICE'''),
('2009-07-07 13:31:00', 'admin', 'INSERT INTO workorders (wo,\n                                                     loccode,\n                                                     requiredby,\n                                                     startdate)\n                                     VALUES (2,\n                                            ''MEL'',\n                                            ''2009-07-07'',\n                                            ''2009-07-07'')'),
('2009-07-07 13:31:16', 'admin', 'INSERT INTO woitems (wo,\n	                             stockid,\n	                             qtyreqd,\n	                             stdcost)\n	         VALUES ( 2,\n                         ''SRT3210DICE'',\n                         1,\n                          0\n                          )'),
('2009-07-07 13:31:16', 'admin', 'INSERT INTO worequirements (wo,\n                                            parentstockid,\n                                            stockid,\n                                            qtypu,\n                                            stdcost,\n                                            autoissue)\n      	                 SELECT 2,\n        	                           bom.parent,\n                                       bom.component,\n                                       bom.quantity,\n                                       (materialcost+labourcost+overheadcost)*bom.quantity,\n                                       autoissue\n                         FROM bom INNER JOIN stockmaster\n                         ON bom.component=stockmaster.stockid\n                         WHERE parent=''SRT3210DICE''\n                         AND loccode =''MEL'''),
('2009-07-07 13:31:26', 'admin', 'UPDATE workorders SET requiredby=''2009/07/07'',\n												loccode=''MEL''\n			        	    WHERE wo=2'),
('2009-07-07 13:31:26', 'admin', 'UPDATE woitems SET qtyreqd =  1,\n    			                                 nextlotsnref = '''',\n    			                                 stdcost =0\n    			                  WHERE wo=2\n                                  AND stockid=''SRT3210DICE'''),
('2009-07-07 13:32:26', 'admin', 'UPDATE workorders SET requiredby=''2009/07/07'',\n												loccode=''HZ''\n			        	    WHERE wo=2'),
('2009-07-07 13:32:26', 'admin', 'UPDATE woitems SET qtyreqd =  1,\n    			                                 nextlotsnref = '''',\n    			                                 stdcost =0\n    			                  WHERE wo=2\n                                  AND stockid=''SRT3210DICE'''),
('2009-07-07 13:34:04', 'admin', 'UPDATE workorders SET requiredby=''2009/07/07'',\n												loccode=''HZ''\n			        	    WHERE wo=2'),
('2009-07-07 13:34:04', 'admin', 'UPDATE woitems SET qtyreqd =  1,\n    			                                 nextlotsnref = '''',\n    			                                 stdcost =0\n    			                  WHERE wo=2\n                                  AND stockid=''SRT3210DICE'''),
('2009-07-07 13:34:16', 'admin', 'DELETE FROM worequirements WHERE wo=2'),
('2009-07-07 13:34:16', 'admin', 'DELETE FROM woitems WHERE wo=2'),
('2009-07-07 13:34:16', 'admin', 'DELETE FROM workorders WHERE wo=2'),
('2009-07-08 01:21:22', 'admin', 'UPDATE salesorders SET printedpackingslip=1, datepackingslipprinted=''2009-07-08'' WHERE salesorders.orderno=1'),
('2009-07-08 08:15:53', 'admin', 'DELETE FROM salesorderdetails\n							WHERE orderno=0\n							AND orderlineno=0'),
('2009-07-08 09:39:35', 'admin', 'DELETE FROM salesorderdetails\n							WHERE orderno=0\n							AND orderlineno=0'),
('2009-07-08 09:40:44', 'admin', 'UPDATE stockmaster SET\n					materialcost=0.0000,\n					labourcost=8.0000,\n					overheadcost=4.0000,\n					lastcost=0\n			WHERE stockid=''SRT3210DICE'''),
('2009-07-08 09:42:15', 'admin', 'UPDATE salesorderdetails\n						SET quantity=1000,\n						unitprice=10,\n						discountpercent=0.98,\n						narrative ='''',\n						itemdue = ''08/07/2009'',\n						poline = ''''\n					WHERE orderno=0\n					AND orderlineno=0'),
('2009-07-08 09:43:46', 'admin', 'UPDATE salesorderdetails\n						SET quantity=2000,\n						unitprice=10,\n						discountpercent=0.98,\n						narrative ='''',\n						itemdue = ''08/07/2009'',\n						poline = ''''\n					WHERE orderno=0\n					AND orderlineno=1'),
('2009-07-08 09:45:02', 'admin', 'INSERT INTO salesorders (\n								orderno,\n								debtorno,\n								branchcode,\n								customerref,\n								comments,\n								orddate,\n								ordertype,\n								shipvia,\n								deliverto,\n								deladd1,\n								deladd2,\n								deladd3,\n								deladd4,\n								deladd5,\n								deladd6,\n								contactphone,\n								contactemail,\n								freightcost,\n								fromstkloc,\n								deliverydate,\n								quotedate,\n								confirmeddate,\n								quotation,\n								deliverblind)\n							VALUES (\n								2,\n								''0002'',\n								''0002'',\n								'''',\n								'''',\n								''2009-07-08 09:45'',\n								''02'',\n								1,\n								''Onreal'',\n								''ÉîÛÚ±¦É½ÊÐ'',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								0,\n								''HZ'',\n								''2009/07/08'',\n								''2009/07/08'',\n								''2009/07/08'',\n								1,\n								1\n								)'),
('2009-07-08 09:45:02', 'admin', 'INSERT INTO salesorderdetails (\n						orderlineno,\n						orderno,\n						stkcode,\n						unitprice,\n						quantity,\n						discountpercent,\n						narrative,\n						poline,\n						itemdue)\n					VALUES (0,\n					2,\n					''SRT3210'',\n					10,\n					1000,\n					0.98,\n					'''',\n					'''',\n					''2009/07/08''\n				)'),
('2009-07-08 09:45:02', 'admin', 'INSERT INTO salesorderdetails (\n						orderlineno,\n						orderno,\n						stkcode,\n						unitprice,\n						quantity,\n						discountpercent,\n						narrative,\n						poline,\n						itemdue)\n					VALUES (1,\n					2,\n					''SRT3210'',\n					10,\n					2000,\n					0.98,\n					'''',\n					'''',\n					''2009/07/08''\n				)'),
('2009-07-08 09:52:23', 'admin', 'UPDATE salesorders SET comments = CONCAT(comments,'' Inv '',''1'') WHERE orderno= 1'),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO debtortrans (\n			transno,\n			type,\n			debtorno,\n			branchcode,\n			trandate,\n			prd,\n			reference,\n			tpe,\n			order_,\n			ovamount,\n			ovgst,\n			ovfreight,\n			rate,\n			invtext,\n			shipvia,\n			consignment\n			)\n		VALUES (\n			1,\n			10,\n			''0002'',\n			''0002'',\n			''2009/07/08'',\n			1,\n			'''',\n			''02'',\n			1,\n			12000,\n			0,\n			0,\n			5.44,\n			''Í³Ò»¿ªÆ±'',\n			1,\n			''sf1000001''\n		)'),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO orderdeliverydifferenceslog (\n					orderno,\n					invoiceno,\n					stockid,\n					quantitydiff,\n					debtorno,\n					branch,\n					can_or_bo\n				)\n				VALUES (\n					1,\n					1,\n					''SRT3210'',\n					2000,\n					''0002'',\n					''0002'',\n					''BO''\n				)'),
('2009-07-08 09:52:23', 'admin', 'UPDATE salesorderdetails\n					SET qtyinvoiced = qtyinvoiced + 1000,\n					actualdispatchdate = ''2009/07/08''\n					WHERE orderno = 1\n					AND orderlineno = ''0'''),
('2009-07-08 09:52:23', 'admin', 'UPDATE locstock\n					SET quantity = locstock.quantity - 1000\n					WHERE locstock.stockid = ''SRT3210''\n					AND loccode = ''HZ'''),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO stockmoves (\n						stockid,\n						type,\n						transno,\n						loccode,\n						trandate,\n						debtorno,\n						branchcode,\n						price,\n						prd,\n						reference,\n						qty,\n						discountpercent,\n						standardcost,\n						newqoh,\n						narrative )\n					VALUES (''SRT3210'',\n						10,\n						1,\n						''HZ'',\n						''2009/07/08'',\n						''0002'',\n						''0002'',\n						2.20588235294,\n						1,\n						''1'',\n						-1000,\n						0,\n						6.0000,\n						32000,\n						'''' )'),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO salesanalysis (\n						typeabbrev,\n						periodno,\n						amt,\n						cost,\n						cust,\n						custbranch,\n						qty,\n						disc,\n						stockid,\n						area,\n						budgetoractual,\n						salesperson,\n						stkcategory\n						)\n					SELECT ''02'',\n						1,\n						2205.88235294,\n						6000,\n						''0002'',\n						''0002'',\n						1000,\n						0,\n						''SRT3210'',\n						custbranch.area,\n						1,\n						custbranch.salesman,\n						stockmaster.categoryid\n					FROM stockmaster,\n						custbranch\n					WHERE stockmaster.stockid = ''SRT3210''\n					AND custbranch.debtorno = ''0002''\n					AND custbranch.branchcode=''0002'''),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO gltrans (\n							type,\n							typeno,\n							trandate,\n							periodno,\n							account,\n							narrative,\n							amount\n							)\n					VALUES (\n						10,\n						1,\n						''2009/07/08'',\n						1,\n						5000,\n						''0002 - SRT3210 x 1000 @ 6.0000'',\n						6000\n					)'),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO gltrans (\n							type,\n							typeno,\n							trandate,\n							periodno,\n							account,\n							narrative,\n							amount)\n					VALUES (\n						10,\n						1,\n						''2009/07/08'',\n						1,\n						1,\n						''0002 - SRT3210 x 1000 @ 6.0000'',\n						-6000\n					)'),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO gltrans (\n							type,\n							typeno,\n							trandate,\n							periodno,\n							account,\n							narrative,\n							amount\n						)\n					VALUES (\n						10,\n						1,\n						''2009/07/08'',\n						1,\n						4100,\n						''0002 - SRT3210 x 1000 @ 12'',\n						-2205.88235294\n					)'),
('2009-07-08 09:52:23', 'admin', 'INSERT INTO gltrans (\n						type,\n						typeno,\n						trandate,\n						periodno,\n						account,\n						narrative,\n						amount\n						)\n					VALUES (\n						10,\n						1,\n						''2009/07/08'',\n						1,\n						1100,\n						''0002'',\n						2205.88235294\n					)'),
('2009-07-08 10:00:16', 'admin', 'UPDATE debtortrans SET settled=1\n		WHERE ABS(debtortrans.ovamount+debtortrans.ovdiscount+debtortrans.ovfreight+debtortrans.ovgst-debtortrans.alloc)&lt;0.009'),
('2009-07-09 01:08:57', 'admin', 'UPDATE www_users\n				SET displayrecordsmax=50,\n					theme=''professional'',\n					language=''en_GB'',\n					email=''''\n				WHERE userid = ''admin'''),
('2009-07-09 01:11:33', 'admin', 'UPDATE salesorderdetails\n						SET quantity=5000,\n						unitprice=10,\n						discountpercent=0,\n						narrative ='''',\n						itemdue = ''09/07/2009'',\n						poline = ''''\n					WHERE orderno=0\n					AND orderlineno=0'),
('2009-07-09 01:22:36', 'admin', 'UPDATE www_users\n				SET displayrecordsmax=50,\n					theme=''professional'',\n					language=''zh_CN'',\n					email=''''\n				WHERE userid = ''admin'''),
('2009-07-09 01:32:00', 'admin', 'UPDATE salesorderdetails\n						SET quantity=5000,\n						unitprice=10,\n						discountpercent=0,\n						narrative ='''',\n						itemdue = ''09/07/2009'',\n						poline = ''''\n					WHERE orderno=0\n					AND orderlineno=0'),
('2009-07-09 01:32:34', 'admin', 'UPDATE salesorderdetails\n						SET quantity=6000,\n						unitprice=10,\n						discountpercent=0,\n						narrative ='''',\n						itemdue = ''09/07/2009'',\n						poline = ''''\n					WHERE orderno=0\n					AND orderlineno=1'),
('2009-07-09 01:32:34', 'admin', 'UPDATE salesorderdetails\n						SET quantity=7000,\n						unitprice=10,\n						discountpercent=0,\n						narrative ='''',\n						itemdue = ''09/07/2009'',\n						poline = ''''\n					WHERE orderno=0\n					AND orderlineno=2'),
('2009-07-09 01:33:26', 'admin', 'INSERT INTO salesorders (\n								orderno,\n								debtorno,\n								branchcode,\n								customerref,\n								comments,\n								orddate,\n								ordertype,\n								shipvia,\n								deliverto,\n								deladd1,\n								deladd2,\n								deladd3,\n								deladd4,\n								deladd5,\n								deladd6,\n								contactphone,\n								contactemail,\n								freightcost,\n								fromstkloc,\n								deliverydate,\n								quotedate,\n								confirmeddate,\n								quotation,\n								deliverblind)\n							VALUES (\n								3,\n								''0002'',\n								''0002'',\n								'''',\n								'''',\n								''2009-07-09 01:33'',\n								''02'',\n								1,\n								''Onreal'',\n								''ÉîÛÚ±¦É½ÊÐ'',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								'''',\n								0,\n								''HZ'',\n								''2009/07/09'',\n								''2009/07/09'',\n								''2009/07/09'',\n								0,\n								1\n								)'),
('2009-07-09 01:33:26', 'admin', 'INSERT INTO salesorderdetails (\n						orderlineno,\n						orderno,\n						stkcode,\n						unitprice,\n						quantity,\n						discountpercent,\n						narrative,\n						poline,\n						itemdue)\n					VALUES (0,\n					3,\n					''SRT3210'',\n					10,\n					5000,\n					0,\n					'''',\n					'''',\n					''2009/07/09''\n				)'),
('2009-07-09 01:33:26', 'admin', 'INSERT INTO salesorderdetails (\n						orderlineno,\n						orderno,\n						stkcode,\n						unitprice,\n						quantity,\n						discountpercent,\n						narrative,\n						poline,\n						itemdue)\n					VALUES (1,\n					3,\n					''SRT3210'',\n					10,\n					6000,\n					0,\n					'''',\n					'''',\n					''2009/07/09''\n				)'),
('2009-07-09 01:33:26', 'admin', 'INSERT INTO salesorderdetails (\n						orderlineno,\n						orderno,\n						stkcode,\n						unitprice,\n						quantity,\n						discountpercent,\n						narrative,\n						poline,\n						itemdue)\n					VALUES (2,\n					3,\n					''SRT3210'',\n					10,\n					7000,\n					0,\n					'''',\n					'''',\n					''2009/07/09''\n				)'),
('2009-07-09 01:33:32', 'admin', 'UPDATE salesorders SET printedpackingslip=1, datepackingslipprinted=''2009-07-09'' WHERE salesorders.orderno=3'),
('2009-07-09 01:39:50', 'admin', 'UPDATE debtortrans SET settled=1\n		WHERE ABS(debtortrans.ovamount+debtortrans.ovdiscount+debtortrans.ovfreight+debtortrans.ovgst-debtortrans.alloc)&lt;0.009'),
('2009-07-09 02:28:36', 'admin', 'UPDATE www_users\n				SET displayrecordsmax=50,\n					theme=''professional'',\n					language=''en_GB'',\n					email=''''\n				WHERE userid = ''admin''');

-- --------------------------------------------------------

-- 
-- 表的结构 `bankaccounts`
-- 

CREATE TABLE `bankaccounts` (
  `accountcode` int(11) NOT NULL default '0',
  `currcode` char(3) NOT NULL,
  `bankaccountname` char(50) NOT NULL default '',
  `bankaccountnumber` char(50) NOT NULL default '',
  `bankaddress` char(50) default NULL,
  PRIMARY KEY  (`accountcode`),
  KEY `currcode` (`currcode`),
  KEY `BankAccountName` (`bankaccountname`),
  KEY `BankAccountNumber` (`bankaccountnumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `bankaccounts`
-- 

INSERT INTO `bankaccounts` (`accountcode`, `currcode`, `bankaccountname`, `bankaccountnumber`, `bankaddress`) VALUES 
(1030, 'AUD', 'Cheque Account', '', ''),
(1040, 'AUD', 'Savings Account', '', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `banktrans`
-- 

CREATE TABLE `banktrans` (
  `banktransid` bigint(20) NOT NULL auto_increment,
  `type` smallint(6) NOT NULL default '0',
  `transno` bigint(20) NOT NULL default '0',
  `bankact` int(11) NOT NULL default '0',
  `ref` varchar(50) NOT NULL default '',
  `amountcleared` double NOT NULL default '0',
  `exrate` double NOT NULL default '1' COMMENT 'From bank account currency to payment currency',
  `functionalexrate` double NOT NULL default '1' COMMENT 'Account currency to functional currency',
  `transdate` date NOT NULL default '0000-00-00',
  `banktranstype` varchar(30) NOT NULL default '',
  `amount` double NOT NULL default '0',
  `currcode` char(3) NOT NULL default '',
  PRIMARY KEY  (`banktransid`),
  KEY `BankAct` (`bankact`,`ref`),
  KEY `TransDate` (`transdate`),
  KEY `TransType` (`banktranstype`),
  KEY `Type` (`type`,`transno`),
  KEY `CurrCode` (`currcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `banktrans`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `bom`
-- 

CREATE TABLE `bom` (
  `parent` char(20) NOT NULL default '',
  `component` char(20) NOT NULL default '',
  `workcentreadded` char(5) NOT NULL default '',
  `loccode` char(5) NOT NULL default '',
  `effectiveafter` date NOT NULL default '0000-00-00',
  `effectiveto` date NOT NULL default '9999-12-31',
  `quantity` double NOT NULL default '1',
  `autoissue` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`parent`,`component`,`workcentreadded`,`loccode`),
  KEY `Component` (`component`),
  KEY `EffectiveAfter` (`effectiveafter`),
  KEY `EffectiveTo` (`effectiveto`),
  KEY `LocCode` (`loccode`),
  KEY `Parent` (`parent`,`effectiveafter`,`effectiveto`,`loccode`),
  KEY `Parent_2` (`parent`),
  KEY `WorkCentreAdded` (`workcentreadded`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `bom`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `buckets`
-- 

CREATE TABLE `buckets` (
  `workcentre` char(5) NOT NULL default '',
  `availdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `capacity` double NOT NULL default '0',
  PRIMARY KEY  (`workcentre`,`availdate`),
  KEY `WorkCentre` (`workcentre`),
  KEY `AvailDate` (`availdate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `buckets`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `chartdetails`
-- 

CREATE TABLE `chartdetails` (
  `accountcode` int(11) NOT NULL default '0',
  `period` smallint(6) NOT NULL default '0',
  `budget` double NOT NULL default '0',
  `actual` double NOT NULL default '0',
  `bfwd` double NOT NULL default '0',
  `bfwdbudget` double NOT NULL default '0',
  PRIMARY KEY  (`accountcode`,`period`),
  KEY `Period` (`period`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `chartdetails`
-- 

INSERT INTO `chartdetails` (`accountcode`, `period`, `budget`, `actual`, `bfwd`, `bfwdbudget`) VALUES 
(1, 0, 0, 0, 0, 0),
(1, 1, 0, 0, 0, 0),
(1, 2, 0, 0, 0, 0),
(1, 3, 0, 0, 0, 0),
(1010, 0, 0, 0, 0, 0),
(1010, 1, 0, 0, 0, 0),
(1010, 2, 0, 0, 0, 0),
(1010, 3, 0, 0, 0, 0),
(1020, 0, 0, 0, 0, 0),
(1020, 1, 0, 0, 0, 0),
(1020, 2, 0, 0, 0, 0),
(1020, 3, 0, 0, 0, 0),
(1030, 0, 0, 0, 0, 0),
(1030, 1, 0, 0, 0, 0),
(1030, 2, 0, 0, 0, 0),
(1030, 3, 0, 0, 0, 0),
(1040, 0, 0, 0, 0, 0),
(1040, 1, 0, 0, 0, 0),
(1040, 2, 0, 0, 0, 0),
(1040, 3, 0, 0, 0, 0),
(1050, 0, 0, 0, 0, 0),
(1050, 1, 0, 0, 0, 0),
(1050, 2, 0, 0, 0, 0),
(1050, 3, 0, 0, 0, 0),
(1060, 0, 0, 0, 0, 0),
(1060, 1, 0, 0, 0, 0),
(1060, 2, 0, 0, 0, 0),
(1060, 3, 0, 0, 0, 0),
(1070, 0, 0, 0, 0, 0),
(1070, 1, 0, 0, 0, 0),
(1070, 2, 0, 0, 0, 0),
(1070, 3, 0, 0, 0, 0),
(1080, 0, 0, 0, 0, 0),
(1080, 1, 0, 0, 0, 0),
(1080, 2, 0, 0, 0, 0),
(1080, 3, 0, 0, 0, 0),
(1090, 0, 0, 0, 0, 0),
(1090, 1, 0, 0, 0, 0),
(1090, 2, 0, 0, 0, 0),
(1090, 3, 0, 0, 0, 0),
(1100, 0, 0, 0, 0, 0),
(1100, 1, 0, 0, 0, 0),
(1100, 2, 0, 0, 0, 0),
(1100, 3, 0, 0, 0, 0),
(1150, 0, 0, 0, 0, 0),
(1150, 1, 0, 0, 0, 0),
(1150, 2, 0, 0, 0, 0),
(1150, 3, 0, 0, 0, 0),
(1200, 0, 0, 0, 0, 0),
(1200, 1, 0, 0, 0, 0),
(1200, 2, 0, 0, 0, 0),
(1200, 3, 0, 0, 0, 0),
(1250, 0, 0, 0, 0, 0),
(1250, 1, 0, 0, 0, 0),
(1250, 2, 0, 0, 0, 0),
(1250, 3, 0, 0, 0, 0),
(1300, 0, 0, 0, 0, 0),
(1300, 1, 0, 0, 0, 0),
(1300, 2, 0, 0, 0, 0),
(1300, 3, 0, 0, 0, 0),
(1350, 0, 0, 0, 0, 0),
(1350, 1, 0, 0, 0, 0),
(1350, 2, 0, 0, 0, 0),
(1350, 3, 0, 0, 0, 0),
(1400, 0, 0, 0, 0, 0),
(1400, 1, 0, 0, 0, 0),
(1400, 2, 0, 0, 0, 0),
(1400, 3, 0, 0, 0, 0),
(1420, 0, 0, 0, 0, 0),
(1420, 1, 0, 0, 0, 0),
(1420, 2, 0, 0, 0, 0),
(1420, 3, 0, 0, 0, 0),
(1440, 0, 0, 0, 0, 0),
(1440, 1, 0, 0, 0, 0),
(1440, 2, 0, 0, 0, 0),
(1440, 3, 0, 0, 0, 0),
(1460, 0, 0, 0, 0, 0),
(1460, 1, 0, 0, 0, 0),
(1460, 2, 0, 0, 0, 0),
(1460, 3, 0, 0, 0, 0),
(1500, 0, 0, 0, 0, 0),
(1500, 1, 0, 0, 0, 0),
(1500, 2, 0, 0, 0, 0),
(1500, 3, 0, 0, 0, 0),
(1550, 0, 0, 0, 0, 0),
(1550, 1, 0, 0, 0, 0),
(1550, 2, 0, 0, 0, 0),
(1550, 3, 0, 0, 0, 0),
(1600, 0, 0, 0, 0, 0),
(1600, 1, 0, 0, 0, 0),
(1600, 2, 0, 0, 0, 0),
(1600, 3, 0, 0, 0, 0),
(1620, 0, 0, 0, 0, 0),
(1620, 1, 0, 0, 0, 0),
(1620, 2, 0, 0, 0, 0),
(1620, 3, 0, 0, 0, 0),
(1650, 0, 0, 0, 0, 0),
(1650, 1, 0, 0, 0, 0),
(1650, 2, 0, 0, 0, 0),
(1650, 3, 0, 0, 0, 0),
(1670, 0, 0, 0, 0, 0),
(1670, 1, 0, 0, 0, 0),
(1670, 2, 0, 0, 0, 0),
(1670, 3, 0, 0, 0, 0),
(1700, 0, 0, 0, 0, 0),
(1700, 1, 0, 0, 0, 0),
(1700, 2, 0, 0, 0, 0),
(1700, 3, 0, 0, 0, 0),
(1710, 0, 0, 0, 0, 0),
(1710, 1, 0, 0, 0, 0),
(1710, 2, 0, 0, 0, 0),
(1710, 3, 0, 0, 0, 0),
(1720, 0, 0, 0, 0, 0),
(1720, 1, 0, 0, 0, 0),
(1720, 2, 0, 0, 0, 0),
(1720, 3, 0, 0, 0, 0),
(1730, 0, 0, 0, 0, 0),
(1730, 1, 0, 0, 0, 0),
(1730, 2, 0, 0, 0, 0),
(1730, 3, 0, 0, 0, 0),
(1740, 0, 0, 0, 0, 0),
(1740, 1, 0, 0, 0, 0),
(1740, 2, 0, 0, 0, 0),
(1740, 3, 0, 0, 0, 0),
(1750, 0, 0, 0, 0, 0),
(1750, 1, 0, 0, 0, 0),
(1750, 2, 0, 0, 0, 0),
(1750, 3, 0, 0, 0, 0),
(1760, 0, 0, 0, 0, 0),
(1760, 1, 0, 0, 0, 0),
(1760, 2, 0, 0, 0, 0),
(1760, 3, 0, 0, 0, 0),
(1770, 0, 0, 0, 0, 0),
(1770, 1, 0, 0, 0, 0),
(1770, 2, 0, 0, 0, 0),
(1770, 3, 0, 0, 0, 0),
(1780, 0, 0, 0, 0, 0),
(1780, 1, 0, 0, 0, 0),
(1780, 2, 0, 0, 0, 0),
(1780, 3, 0, 0, 0, 0),
(1790, 0, 0, 0, 0, 0),
(1790, 1, 0, 0, 0, 0),
(1790, 2, 0, 0, 0, 0),
(1790, 3, 0, 0, 0, 0),
(1800, 0, 0, 0, 0, 0),
(1800, 1, 0, 0, 0, 0),
(1800, 2, 0, 0, 0, 0),
(1800, 3, 0, 0, 0, 0),
(1850, 0, 0, 0, 0, 0),
(1850, 1, 0, 0, 0, 0),
(1850, 2, 0, 0, 0, 0),
(1850, 3, 0, 0, 0, 0),
(1900, 0, 0, 0, 0, 0),
(1900, 1, 0, 0, 0, 0),
(1900, 2, 0, 0, 0, 0),
(1900, 3, 0, 0, 0, 0),
(2010, 0, 0, 0, 0, 0),
(2010, 1, 0, 0, 0, 0),
(2010, 2, 0, 0, 0, 0),
(2010, 3, 0, 0, 0, 0),
(2020, 0, 0, 0, 0, 0),
(2020, 1, 0, 0, 0, 0),
(2020, 2, 0, 0, 0, 0),
(2020, 3, 0, 0, 0, 0),
(2050, 0, 0, 0, 0, 0),
(2050, 1, 0, 0, 0, 0),
(2050, 2, 0, 0, 0, 0),
(2050, 3, 0, 0, 0, 0),
(2100, 0, 0, 0, 0, 0),
(2100, 1, 0, 0, 0, 0),
(2100, 2, 0, 0, 0, 0),
(2100, 3, 0, 0, 0, 0),
(2150, 0, 0, 0, 0, 0),
(2150, 1, 0, 0, 0, 0),
(2150, 2, 0, 0, 0, 0),
(2150, 3, 0, 0, 0, 0),
(2200, 0, 0, 0, 0, 0),
(2200, 1, 0, 0, 0, 0),
(2200, 2, 0, 0, 0, 0),
(2200, 3, 0, 0, 0, 0),
(2230, 0, 0, 0, 0, 0),
(2230, 1, 0, 0, 0, 0),
(2230, 2, 0, 0, 0, 0),
(2230, 3, 0, 0, 0, 0),
(2250, 0, 0, 0, 0, 0),
(2250, 1, 0, 0, 0, 0),
(2250, 2, 0, 0, 0, 0),
(2250, 3, 0, 0, 0, 0),
(2300, 0, 0, 0, 0, 0),
(2300, 1, 0, 0, 0, 0),
(2300, 2, 0, 0, 0, 0),
(2300, 3, 0, 0, 0, 0),
(2310, 0, 0, 0, 0, 0),
(2310, 1, 0, 0, 0, 0),
(2310, 2, 0, 0, 0, 0),
(2310, 3, 0, 0, 0, 0),
(2320, 0, 0, 0, 0, 0),
(2320, 1, 0, 0, 0, 0),
(2320, 2, 0, 0, 0, 0),
(2320, 3, 0, 0, 0, 0),
(2330, 0, 0, 0, 0, 0),
(2330, 1, 0, 0, 0, 0),
(2330, 2, 0, 0, 0, 0),
(2330, 3, 0, 0, 0, 0),
(2340, 0, 0, 0, 0, 0),
(2340, 1, 0, 0, 0, 0),
(2340, 2, 0, 0, 0, 0),
(2340, 3, 0, 0, 0, 0),
(2350, 0, 0, 0, 0, 0),
(2350, 1, 0, 0, 0, 0),
(2350, 2, 0, 0, 0, 0),
(2350, 3, 0, 0, 0, 0),
(2360, 0, 0, 0, 0, 0),
(2360, 1, 0, 0, 0, 0),
(2360, 2, 0, 0, 0, 0),
(2360, 3, 0, 0, 0, 0),
(2400, 0, 0, 0, 0, 0),
(2400, 1, 0, 0, 0, 0),
(2400, 2, 0, 0, 0, 0),
(2400, 3, 0, 0, 0, 0),
(2410, 0, 0, 0, 0, 0),
(2410, 1, 0, 0, 0, 0),
(2410, 2, 0, 0, 0, 0),
(2410, 3, 0, 0, 0, 0),
(2420, 0, 0, 0, 0, 0),
(2420, 1, 0, 0, 0, 0),
(2420, 2, 0, 0, 0, 0),
(2420, 3, 0, 0, 0, 0),
(2450, 0, 0, 0, 0, 0),
(2450, 1, 0, 0, 0, 0),
(2450, 2, 0, 0, 0, 0),
(2450, 3, 0, 0, 0, 0),
(2460, 0, 0, 0, 0, 0),
(2460, 1, 0, 0, 0, 0),
(2460, 2, 0, 0, 0, 0),
(2460, 3, 0, 0, 0, 0),
(2470, 0, 0, 0, 0, 0),
(2470, 1, 0, 0, 0, 0),
(2470, 2, 0, 0, 0, 0),
(2470, 3, 0, 0, 0, 0),
(2480, 0, 0, 0, 0, 0),
(2480, 1, 0, 0, 0, 0),
(2480, 2, 0, 0, 0, 0),
(2480, 3, 0, 0, 0, 0),
(2500, 0, 0, 0, 0, 0),
(2500, 1, 0, 0, 0, 0),
(2500, 2, 0, 0, 0, 0),
(2500, 3, 0, 0, 0, 0),
(2550, 0, 0, 0, 0, 0),
(2550, 1, 0, 0, 0, 0),
(2550, 2, 0, 0, 0, 0),
(2550, 3, 0, 0, 0, 0),
(2560, 0, 0, 0, 0, 0),
(2560, 1, 0, 0, 0, 0),
(2560, 2, 0, 0, 0, 0),
(2560, 3, 0, 0, 0, 0),
(2600, 0, 0, 0, 0, 0),
(2600, 1, 0, 0, 0, 0),
(2600, 2, 0, 0, 0, 0),
(2600, 3, 0, 0, 0, 0),
(2700, 0, 0, 0, 0, 0),
(2700, 1, 0, 0, 0, 0),
(2700, 2, 0, 0, 0, 0),
(2700, 3, 0, 0, 0, 0),
(2720, 0, 0, 0, 0, 0),
(2720, 1, 0, 0, 0, 0),
(2720, 2, 0, 0, 0, 0),
(2720, 3, 0, 0, 0, 0),
(2740, 0, 0, 0, 0, 0),
(2740, 1, 0, 0, 0, 0),
(2740, 2, 0, 0, 0, 0),
(2740, 3, 0, 0, 0, 0),
(2760, 0, 0, 0, 0, 0),
(2760, 1, 0, 0, 0, 0),
(2760, 2, 0, 0, 0, 0),
(2760, 3, 0, 0, 0, 0),
(2800, 0, 0, 0, 0, 0),
(2800, 1, 0, 0, 0, 0),
(2800, 2, 0, 0, 0, 0),
(2800, 3, 0, 0, 0, 0),
(2900, 0, 0, 0, 0, 0),
(2900, 1, 0, 0, 0, 0),
(2900, 2, 0, 0, 0, 0),
(2900, 3, 0, 0, 0, 0),
(3100, 0, 0, 0, 0, 0),
(3100, 1, 0, 0, 0, 0),
(3100, 2, 0, 0, 0, 0),
(3100, 3, 0, 0, 0, 0),
(3200, 0, 0, 0, 0, 0),
(3200, 1, 0, 0, 0, 0),
(3200, 2, 0, 0, 0, 0),
(3200, 3, 0, 0, 0, 0),
(3300, 0, 0, 0, 0, 0),
(3300, 1, 0, 0, 0, 0),
(3300, 2, 0, 0, 0, 0),
(3300, 3, 0, 0, 0, 0),
(3400, 0, 0, 0, 0, 0),
(3400, 1, 0, 0, 0, 0),
(3400, 2, 0, 0, 0, 0),
(3400, 3, 0, 0, 0, 0),
(3500, 0, 0, 0, 0, 0),
(3500, 1, 0, 0, 0, 0),
(3500, 2, 0, 0, 0, 0),
(3500, 3, 0, 0, 0, 0),
(4100, 0, 0, 0, 0, 0),
(4100, 1, 0, 0, 0, 0),
(4100, 2, 0, 0, 0, 0),
(4100, 3, 0, 0, 0, 0),
(4200, 0, 0, 0, 0, 0),
(4200, 1, 0, 0, 0, 0),
(4200, 2, 0, 0, 0, 0),
(4200, 3, 0, 0, 0, 0),
(4500, 0, 0, 0, 0, 0),
(4500, 1, 0, 0, 0, 0),
(4500, 2, 0, 0, 0, 0),
(4500, 3, 0, 0, 0, 0),
(4600, 0, 0, 0, 0, 0),
(4600, 1, 0, 0, 0, 0),
(4600, 2, 0, 0, 0, 0),
(4600, 3, 0, 0, 0, 0),
(4700, 0, 0, 0, 0, 0),
(4700, 1, 0, 0, 0, 0),
(4700, 2, 0, 0, 0, 0),
(4700, 3, 0, 0, 0, 0),
(4800, 0, 0, 0, 0, 0),
(4800, 1, 0, 0, 0, 0),
(4800, 2, 0, 0, 0, 0),
(4800, 3, 0, 0, 0, 0),
(4900, 0, 0, 0, 0, 0),
(4900, 1, 0, 0, 0, 0),
(4900, 2, 0, 0, 0, 0),
(4900, 3, 0, 0, 0, 0),
(5000, 0, 0, 0, 0, 0),
(5000, 1, 0, 0, 0, 0),
(5000, 2, 0, 0, 0, 0),
(5000, 3, 0, 0, 0, 0),
(5100, 0, 0, 0, 0, 0),
(5100, 1, 0, 0, 0, 0),
(5100, 2, 0, 0, 0, 0),
(5100, 3, 0, 0, 0, 0),
(5200, 0, 0, 0, 0, 0),
(5200, 1, 0, 0, 0, 0),
(5200, 2, 0, 0, 0, 0),
(5200, 3, 0, 0, 0, 0),
(5500, 0, 0, 0, 0, 0),
(5500, 1, 0, 0, 0, 0),
(5500, 2, 0, 0, 0, 0),
(5500, 3, 0, 0, 0, 0),
(5600, 0, 0, 0, 0, 0),
(5600, 1, 0, 0, 0, 0),
(5600, 2, 0, 0, 0, 0),
(5600, 3, 0, 0, 0, 0),
(5700, 0, 0, 0, 0, 0),
(5700, 1, 0, 0, 0, 0),
(5700, 2, 0, 0, 0, 0),
(5700, 3, 0, 0, 0, 0),
(5800, 0, 0, 0, 0, 0),
(5800, 1, 0, 0, 0, 0),
(5800, 2, 0, 0, 0, 0),
(5800, 3, 0, 0, 0, 0),
(5900, 0, 0, 0, 0, 0),
(5900, 1, 0, 0, 0, 0),
(5900, 2, 0, 0, 0, 0),
(5900, 3, 0, 0, 0, 0),
(6100, 0, 0, 0, 0, 0),
(6100, 1, 0, 0, 0, 0),
(6100, 2, 0, 0, 0, 0),
(6100, 3, 0, 0, 0, 0),
(6150, 0, 0, 0, 0, 0),
(6150, 1, 0, 0, 0, 0),
(6150, 2, 0, 0, 0, 0),
(6150, 3, 0, 0, 0, 0),
(6200, 0, 0, 0, 0, 0),
(6200, 1, 0, 0, 0, 0),
(6200, 2, 0, 0, 0, 0),
(6200, 3, 0, 0, 0, 0),
(6250, 0, 0, 0, 0, 0),
(6250, 1, 0, 0, 0, 0),
(6250, 2, 0, 0, 0, 0),
(6250, 3, 0, 0, 0, 0),
(6300, 0, 0, 0, 0, 0),
(6300, 1, 0, 0, 0, 0),
(6300, 2, 0, 0, 0, 0),
(6300, 3, 0, 0, 0, 0),
(6400, 0, 0, 0, 0, 0),
(6400, 1, 0, 0, 0, 0),
(6400, 2, 0, 0, 0, 0),
(6400, 3, 0, 0, 0, 0),
(6500, 0, 0, 0, 0, 0),
(6500, 1, 0, 0, 0, 0),
(6500, 2, 0, 0, 0, 0),
(6500, 3, 0, 0, 0, 0),
(6550, 0, 0, 0, 0, 0),
(6550, 1, 0, 0, 0, 0),
(6550, 2, 0, 0, 0, 0),
(6550, 3, 0, 0, 0, 0),
(6590, 0, 0, 0, 0, 0),
(6590, 1, 0, 0, 0, 0),
(6590, 2, 0, 0, 0, 0),
(6590, 3, 0, 0, 0, 0),
(6600, 0, 0, 0, 0, 0),
(6600, 1, 0, 0, 0, 0),
(6600, 2, 0, 0, 0, 0),
(6600, 3, 0, 0, 0, 0),
(6700, 0, 0, 0, 0, 0),
(6700, 1, 0, 0, 0, 0),
(6700, 2, 0, 0, 0, 0),
(6700, 3, 0, 0, 0, 0),
(6800, 0, 0, 0, 0, 0),
(6800, 1, 0, 0, 0, 0),
(6800, 2, 0, 0, 0, 0),
(6800, 3, 0, 0, 0, 0),
(6900, 0, 0, 0, 0, 0),
(6900, 1, 0, 0, 0, 0),
(6900, 2, 0, 0, 0, 0),
(6900, 3, 0, 0, 0, 0),
(7020, 0, 0, 0, 0, 0),
(7020, 1, 0, 0, 0, 0),
(7020, 2, 0, 0, 0, 0),
(7020, 3, 0, 0, 0, 0),
(7030, 0, 0, 0, 0, 0),
(7030, 1, 0, 0, 0, 0),
(7030, 2, 0, 0, 0, 0),
(7030, 3, 0, 0, 0, 0),
(7040, 0, 0, 0, 0, 0),
(7040, 1, 0, 0, 0, 0),
(7040, 2, 0, 0, 0, 0),
(7040, 3, 0, 0, 0, 0),
(7050, 0, 0, 0, 0, 0),
(7050, 1, 0, 0, 0, 0),
(7050, 2, 0, 0, 0, 0),
(7050, 3, 0, 0, 0, 0),
(7060, 0, 0, 0, 0, 0),
(7060, 1, 0, 0, 0, 0),
(7060, 2, 0, 0, 0, 0),
(7060, 3, 0, 0, 0, 0),
(7070, 0, 0, 0, 0, 0),
(7070, 1, 0, 0, 0, 0),
(7070, 2, 0, 0, 0, 0),
(7070, 3, 0, 0, 0, 0),
(7080, 0, 0, 0, 0, 0),
(7080, 1, 0, 0, 0, 0),
(7080, 2, 0, 0, 0, 0),
(7080, 3, 0, 0, 0, 0),
(7090, 0, 0, 0, 0, 0),
(7090, 1, 0, 0, 0, 0),
(7090, 2, 0, 0, 0, 0),
(7090, 3, 0, 0, 0, 0),
(7100, 0, 0, 0, 0, 0),
(7100, 1, 0, 0, 0, 0),
(7100, 2, 0, 0, 0, 0),
(7100, 3, 0, 0, 0, 0),
(7150, 0, 0, 0, 0, 0),
(7150, 1, 0, 0, 0, 0),
(7150, 2, 0, 0, 0, 0),
(7150, 3, 0, 0, 0, 0),
(7200, 0, 0, 0, 0, 0),
(7200, 1, 0, 0, 0, 0),
(7200, 2, 0, 0, 0, 0),
(7200, 3, 0, 0, 0, 0),
(7210, 0, 0, 0, 0, 0),
(7210, 1, 0, 0, 0, 0),
(7210, 2, 0, 0, 0, 0),
(7210, 3, 0, 0, 0, 0),
(7220, 0, 0, 0, 0, 0),
(7220, 1, 0, 0, 0, 0),
(7220, 2, 0, 0, 0, 0),
(7220, 3, 0, 0, 0, 0),
(7230, 0, 0, 0, 0, 0),
(7230, 1, 0, 0, 0, 0),
(7230, 2, 0, 0, 0, 0),
(7230, 3, 0, 0, 0, 0),
(7240, 0, 0, 0, 0, 0),
(7240, 1, 0, 0, 0, 0),
(7240, 2, 0, 0, 0, 0),
(7240, 3, 0, 0, 0, 0),
(7260, 0, 0, 0, 0, 0),
(7260, 1, 0, 0, 0, 0),
(7260, 2, 0, 0, 0, 0),
(7260, 3, 0, 0, 0, 0),
(7280, 0, 0, 0, 0, 0),
(7280, 1, 0, 0, 0, 0),
(7280, 2, 0, 0, 0, 0),
(7280, 3, 0, 0, 0, 0),
(7300, 0, 0, 0, 0, 0),
(7300, 1, 0, 0, 0, 0),
(7300, 2, 0, 0, 0, 0),
(7300, 3, 0, 0, 0, 0),
(7350, 0, 0, 0, 0, 0),
(7350, 1, 0, 0, 0, 0),
(7350, 2, 0, 0, 0, 0),
(7350, 3, 0, 0, 0, 0),
(7390, 0, 0, 0, 0, 0),
(7390, 1, 0, 0, 0, 0),
(7390, 2, 0, 0, 0, 0),
(7390, 3, 0, 0, 0, 0),
(7400, 0, 0, 0, 0, 0),
(7400, 1, 0, 0, 0, 0),
(7400, 2, 0, 0, 0, 0),
(7400, 3, 0, 0, 0, 0),
(7450, 0, 0, 0, 0, 0),
(7450, 1, 0, 0, 0, 0),
(7450, 2, 0, 0, 0, 0),
(7450, 3, 0, 0, 0, 0),
(7500, 0, 0, 0, 0, 0),
(7500, 1, 0, 0, 0, 0),
(7500, 2, 0, 0, 0, 0),
(7500, 3, 0, 0, 0, 0),
(7550, 0, 0, 0, 0, 0),
(7550, 1, 0, 0, 0, 0),
(7550, 2, 0, 0, 0, 0),
(7550, 3, 0, 0, 0, 0),
(7600, 0, 0, 0, 0, 0),
(7600, 1, 0, 0, 0, 0),
(7600, 2, 0, 0, 0, 0),
(7600, 3, 0, 0, 0, 0),
(7610, 0, 0, 0, 0, 0),
(7610, 1, 0, 0, 0, 0),
(7610, 2, 0, 0, 0, 0),
(7610, 3, 0, 0, 0, 0),
(7620, 0, 0, 0, 0, 0),
(7620, 1, 0, 0, 0, 0),
(7620, 2, 0, 0, 0, 0),
(7620, 3, 0, 0, 0, 0),
(7630, 0, 0, 0, 0, 0),
(7630, 1, 0, 0, 0, 0),
(7630, 2, 0, 0, 0, 0),
(7630, 3, 0, 0, 0, 0),
(7640, 0, 0, 0, 0, 0),
(7640, 1, 0, 0, 0, 0),
(7640, 2, 0, 0, 0, 0),
(7640, 3, 0, 0, 0, 0),
(7650, 0, 0, 0, 0, 0),
(7650, 1, 0, 0, 0, 0),
(7650, 2, 0, 0, 0, 0),
(7650, 3, 0, 0, 0, 0),
(7660, 0, 0, 0, 0, 0),
(7660, 1, 0, 0, 0, 0),
(7660, 2, 0, 0, 0, 0),
(7660, 3, 0, 0, 0, 0),
(7700, 0, 0, 0, 0, 0),
(7700, 1, 0, 0, 0, 0),
(7700, 2, 0, 0, 0, 0),
(7700, 3, 0, 0, 0, 0),
(7750, 0, 0, 0, 0, 0),
(7750, 1, 0, 0, 0, 0),
(7750, 2, 0, 0, 0, 0),
(7750, 3, 0, 0, 0, 0),
(7800, 0, 0, 0, 0, 0),
(7800, 1, 0, 0, 0, 0),
(7800, 2, 0, 0, 0, 0),
(7800, 3, 0, 0, 0, 0),
(7900, 0, 0, 0, 0, 0),
(7900, 1, 0, 0, 0, 0),
(7900, 2, 0, 0, 0, 0),
(7900, 3, 0, 0, 0, 0),
(8100, 0, 0, 0, 0, 0),
(8100, 1, 0, 0, 0, 0),
(8100, 2, 0, 0, 0, 0),
(8100, 3, 0, 0, 0, 0),
(8200, 0, 0, 0, 0, 0),
(8200, 1, 0, 0, 0, 0),
(8200, 2, 0, 0, 0, 0),
(8200, 3, 0, 0, 0, 0),
(8300, 0, 0, 0, 0, 0),
(8300, 1, 0, 0, 0, 0),
(8300, 2, 0, 0, 0, 0),
(8300, 3, 0, 0, 0, 0),
(8400, 0, 0, 0, 0, 0),
(8400, 1, 0, 0, 0, 0),
(8400, 2, 0, 0, 0, 0),
(8400, 3, 0, 0, 0, 0),
(8500, 0, 0, 0, 0, 0),
(8500, 1, 0, 0, 0, 0),
(8500, 2, 0, 0, 0, 0),
(8500, 3, 0, 0, 0, 0),
(8600, 0, 0, 0, 0, 0),
(8600, 1, 0, 0, 0, 0),
(8600, 2, 0, 0, 0, 0),
(8600, 3, 0, 0, 0, 0),
(8900, 0, 0, 0, 0, 0),
(8900, 1, 0, 0, 0, 0),
(8900, 2, 0, 0, 0, 0),
(8900, 3, 0, 0, 0, 0),
(9100, 0, 0, 0, 0, 0),
(9100, 1, 0, 0, 0, 0),
(9100, 2, 0, 0, 0, 0),
(9100, 3, 0, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `chartmaster`
-- 

CREATE TABLE `chartmaster` (
  `accountcode` int(11) NOT NULL default '0',
  `accountname` char(50) NOT NULL default '',
  `group_` char(30) NOT NULL default '',
  PRIMARY KEY  (`accountcode`),
  KEY `AccountCode` (`accountcode`),
  KEY `AccountName` (`accountname`),
  KEY `Group_` (`group_`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `chartmaster`
-- 

INSERT INTO `chartmaster` (`accountcode`, `accountname`, `group_`) VALUES 
(1, 'Default Sales/Discounts', 'Sales'),
(1010, 'Petty Cash', 'Current Assets'),
(1020, 'Cash on Hand', 'Current Assets'),
(1030, 'Cheque Accounts', 'Current Assets'),
(1040, 'Savings Accounts', 'Current Assets'),
(1050, 'Payroll Accounts', 'Current Assets'),
(1060, 'Special Accounts', 'Current Assets'),
(1070, 'Money Market Investments', 'Current Assets'),
(1080, 'Short-Term Investments (< 90 days)', 'Current Assets'),
(1090, 'Interest Receivable', 'Current Assets'),
(1100, 'Accounts Receivable', 'Current Assets'),
(1150, 'Allowance for Doubtful Accounts', 'Current Assets'),
(1200, 'Notes Receivable', 'Current Assets'),
(1250, 'Income Tax Receivable', 'Current Assets'),
(1300, 'Prepaid Expenses', 'Current Assets'),
(1350, 'Advances', 'Current Assets'),
(1400, 'Supplies Inventory', 'Current Assets'),
(1420, 'Raw Material Inventory', 'Current Assets'),
(1440, 'Work in Progress Inventory', 'Current Assets'),
(1460, 'Finished Goods Inventory', 'Current Assets'),
(1500, 'Land', 'Fixed Assets'),
(1550, 'Bonds', 'Fixed Assets'),
(1600, 'Buildings', 'Fixed Assets'),
(1620, 'Accumulated Depreciation of Buildings', 'Fixed Assets'),
(1650, 'Equipment', 'Fixed Assets'),
(1670, 'Accumulated Depreciation of Equipment', 'Fixed Assets'),
(1700, 'Furniture & Fixtures', 'Fixed Assets'),
(1710, 'Accumulated Depreciation of Furniture & Fixtures', 'Fixed Assets'),
(1720, 'Office Equipment', 'Fixed Assets'),
(1730, 'Accumulated Depreciation of Office Equipment', 'Fixed Assets'),
(1740, 'Software', 'Fixed Assets'),
(1750, 'Accumulated Depreciation of Software', 'Fixed Assets'),
(1760, 'Vehicles', 'Fixed Assets'),
(1770, 'Accumulated Depreciation Vehicles', 'Fixed Assets'),
(1780, 'Other Depreciable Property', 'Fixed Assets'),
(1790, 'Accumulated Depreciation of Other Depreciable Prop', 'Fixed Assets'),
(1800, 'Patents', 'Fixed Assets'),
(1850, 'Goodwill', 'Fixed Assets'),
(1900, 'Future Income Tax Receivable', 'Current Assets'),
(2010, 'Bank Indedebtedness (overdraft)', 'Liabilities'),
(2020, 'Retainers or Advances on Work', 'Liabilities'),
(2050, 'Interest Payable', 'Liabilities'),
(2100, 'Accounts Payable', 'Liabilities'),
(2150, 'Goods Received Suspense', 'Liabilities'),
(2200, 'Short-Term Loan Payable', 'Liabilities'),
(2230, 'Current Portion of Long-Term Debt Payable', 'Liabilities'),
(2250, 'Income Tax Payable', 'Liabilities'),
(2300, 'GST Payable', 'Liabilities'),
(2310, 'GST Recoverable', 'Liabilities'),
(2320, 'PST Payable', 'Liabilities'),
(2330, 'PST Recoverable (commission)', 'Liabilities'),
(2340, 'Payroll Tax Payable', 'Liabilities'),
(2350, 'Withholding Income Tax Payable', 'Liabilities'),
(2360, 'Other Taxes Payable', 'Liabilities'),
(2400, 'Employee Salaries Payable', 'Liabilities'),
(2410, 'Management Salaries Payable', 'Liabilities'),
(2420, 'Director / Partner Fees Payable', 'Liabilities'),
(2450, 'Health Benefits Payable', 'Liabilities'),
(2460, 'Pension Benefits Payable', 'Liabilities'),
(2470, 'Canada Pension Plan Payable', 'Liabilities'),
(2480, 'Employment Insurance Premiums Payable', 'Liabilities'),
(2500, 'Land Payable', 'Liabilities'),
(2550, 'Long-Term Bank Loan', 'Liabilities'),
(2560, 'Notes Payable', 'Liabilities'),
(2600, 'Building & Equipment Payable', 'Liabilities'),
(2700, 'Furnishing & Fixture Payable', 'Liabilities'),
(2720, 'Office Equipment Payable', 'Liabilities'),
(2740, 'Vehicle Payable', 'Liabilities'),
(2760, 'Other Property Payable', 'Liabilities'),
(2800, 'Shareholder Loans', 'Liabilities'),
(2900, 'Suspense', 'Liabilities'),
(3100, 'Capital Stock', 'Equity'),
(3200, 'Capital Surplus / Dividends', 'Equity'),
(3300, 'Dividend Taxes Payable', 'Equity'),
(3400, 'Dividend Taxes Refundable', 'Equity'),
(3500, 'Retained Earnings', 'Equity'),
(4100, 'Product / Service Sales', 'Revenue'),
(4200, 'Sales Exchange Gains/Losses', 'Revenue'),
(4500, 'Consulting Services', 'Revenue'),
(4600, 'Rentals', 'Revenue'),
(4700, 'Finance Charge Income', 'Revenue'),
(4800, 'Sales Returns & Allowances', 'Revenue'),
(4900, 'Sales Discounts', 'Revenue'),
(5000, 'Cost of Sales', 'Cost of Goods Sold'),
(5100, 'Production Expenses', 'Cost of Goods Sold'),
(5200, 'Purchases Exchange Gains/Losses', 'Cost of Goods Sold'),
(5500, 'Direct Labour Costs', 'Cost of Goods Sold'),
(5600, 'Freight Charges', 'Outward Freight'),
(5700, 'Inventory Adjustment', 'Cost of Goods Sold'),
(5800, 'Purchase Returns & Allowances', 'Cost of Goods Sold'),
(5900, 'Purchase Discounts', 'Cost of Goods Sold'),
(6100, 'Advertising', 'Marketing Expenses'),
(6150, 'Promotion', 'Promotions'),
(6200, 'Communications', 'Marketing Expenses'),
(6250, 'Meeting Expenses', 'Marketing Expenses'),
(6300, 'Travelling Expenses', 'Marketing Expenses'),
(6400, 'Delivery Expenses', 'Marketing Expenses'),
(6500, 'Sales Salaries & Commission', 'Marketing Expenses'),
(6550, 'Sales Salaries & Commission Deductions', 'Marketing Expenses'),
(6590, 'Benefits', 'Marketing Expenses'),
(6600, 'Other Selling Expenses', 'Marketing Expenses'),
(6700, 'Permits, Licenses & License Fees', 'Marketing Expenses'),
(6800, 'Research & Development', 'Marketing Expenses'),
(6900, 'Professional Services', 'Marketing Expenses'),
(7020, 'Support Salaries & Wages', 'Operating Expenses'),
(7030, 'Support Salary & Wage Deductions', 'Operating Expenses'),
(7040, 'Management Salaries', 'Operating Expenses'),
(7050, 'Management Salary deductions', 'Operating Expenses'),
(7060, 'Director / Partner Fees', 'Operating Expenses'),
(7070, 'Director / Partner Deductions', 'Operating Expenses'),
(7080, 'Payroll Tax', 'Operating Expenses'),
(7090, 'Benefits', 'Operating Expenses'),
(7100, 'Training & Education Expenses', 'Operating Expenses'),
(7150, 'Dues & Subscriptions', 'Operating Expenses'),
(7200, 'Accounting Fees', 'Operating Expenses'),
(7210, 'Audit Fees', 'Operating Expenses'),
(7220, 'Banking Fees', 'Operating Expenses'),
(7230, 'Credit Card Fees', 'Operating Expenses'),
(7240, 'Consulting Fees', 'Operating Expenses'),
(7260, 'Legal Fees', 'Operating Expenses'),
(7280, 'Other Professional Fees', 'Operating Expenses'),
(7300, 'Business Tax', 'Operating Expenses'),
(7350, 'Property Tax', 'Operating Expenses'),
(7390, 'Corporation Capital Tax', 'Operating Expenses'),
(7400, 'Office Rent', 'Operating Expenses'),
(7450, 'Equipment Rental', 'Operating Expenses'),
(7500, 'Office Supplies', 'Operating Expenses'),
(7550, 'Office Repair & Maintenance', 'Operating Expenses'),
(7600, 'Automotive Expenses', 'Operating Expenses'),
(7610, 'Communication Expenses', 'Operating Expenses'),
(7620, 'Insurance Expenses', 'Operating Expenses'),
(7630, 'Postage & Courier Expenses', 'Operating Expenses'),
(7640, 'Miscellaneous Expenses', 'Operating Expenses'),
(7650, 'Travel Expenses', 'Operating Expenses'),
(7660, 'Utilities', 'Operating Expenses'),
(7700, 'Ammortization Expenses', 'Operating Expenses'),
(7750, 'Depreciation Expenses', 'Operating Expenses'),
(7800, 'Interest Expense', 'Operating Expenses'),
(7900, 'Bad Debt Expense', 'Operating Expenses'),
(8100, 'Gain on Sale of Assets', 'Other Revenue and Expenses'),
(8200, 'Interest Income', 'Other Revenue and Expenses'),
(8300, 'Recovery on Bad Debt', 'Other Revenue and Expenses'),
(8400, 'Other Revenue', 'Other Revenue and Expenses'),
(8500, 'Loss on Sale of Assets', 'Other Revenue and Expenses'),
(8600, 'Charitable Contributions', 'Other Revenue and Expenses'),
(8900, 'Other Expenses', 'Other Revenue and Expenses'),
(9100, 'Income Tax Provision', 'Income Tax');

-- --------------------------------------------------------

-- 
-- 表的结构 `cogsglpostings`
-- 

CREATE TABLE `cogsglpostings` (
  `id` int(11) NOT NULL auto_increment,
  `area` char(2) NOT NULL default '',
  `stkcat` varchar(6) NOT NULL default '',
  `glcode` int(11) NOT NULL default '0',
  `salestype` char(2) NOT NULL default 'AN',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Area_StkCat` (`area`,`stkcat`,`salestype`),
  KEY `Area` (`area`),
  KEY `StkCat` (`stkcat`),
  KEY `GLCode` (`glcode`),
  KEY `SalesType` (`salestype`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `cogsglpostings`
-- 

INSERT INTO `cogsglpostings` (`id`, `area`, `stkcat`, `glcode`, `salestype`) VALUES 
(3, 'AN', 'ANY', 5000, 'AN');

-- --------------------------------------------------------

-- 
-- 表的结构 `companies`
-- 

CREATE TABLE `companies` (
  `coycode` int(11) NOT NULL default '1',
  `coyname` varchar(50) NOT NULL default '',
  `gstno` varchar(20) NOT NULL default '',
  `companynumber` varchar(20) NOT NULL default '0',
  `regoffice1` varchar(40) NOT NULL default '',
  `regoffice2` varchar(40) NOT NULL default '',
  `regoffice3` varchar(40) NOT NULL default '',
  `regoffice4` varchar(40) NOT NULL default '',
  `regoffice5` varchar(20) NOT NULL default '',
  `regoffice6` varchar(15) NOT NULL default '',
  `telephone` varchar(25) NOT NULL default '',
  `fax` varchar(25) NOT NULL default '',
  `email` varchar(55) NOT NULL default '',
  `currencydefault` varchar(4) NOT NULL default '',
  `debtorsact` int(11) NOT NULL default '70000',
  `pytdiscountact` int(11) NOT NULL default '55000',
  `creditorsact` int(11) NOT NULL default '80000',
  `payrollact` int(11) NOT NULL default '84000',
  `grnact` int(11) NOT NULL default '72000',
  `exchangediffact` int(11) NOT NULL default '65000',
  `purchasesexchangediffact` int(11) NOT NULL default '0',
  `retainedearnings` int(11) NOT NULL default '90000',
  `gllink_debtors` tinyint(1) default '1',
  `gllink_creditors` tinyint(1) default '1',
  `gllink_stock` tinyint(1) default '1',
  `freightact` int(11) NOT NULL default '0',
  PRIMARY KEY  (`coycode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `companies`
-- 

INSERT INTO `companies` (`coycode`, `coyname`, `gstno`, `companynumber`, `regoffice1`, `regoffice2`, `regoffice3`, `regoffice4`, `regoffice5`, `regoffice6`, `telephone`, `fax`, `email`, `currencydefault`, `debtorsact`, `pytdiscountact`, `creditorsact`, `payrollact`, `grnact`, `exchangediffact`, `purchasesexchangediffact`, `retainedearnings`, `gllink_debtors`, `gllink_creditors`, `gllink_stock`, `freightact`) VALUES 
(1, 'Sicomm Technology Ltd', 'not entered yet', '', '123 Web Way', 'PO Box 123', 'Queen Street', 'Melbourne', 'Victoria 3043', 'Australia', '+61 3 4567 8901', '+61 3 4567 8902', 'weberp@weberpdemo.com', 'AUD', 1100, 4900, 2100, 2400, 2150, 4200, 5200, 3500, 1, 1, 1, 5600);

-- --------------------------------------------------------

-- 
-- 表的结构 `config`
-- 

CREATE TABLE `config` (
  `confname` varchar(35) NOT NULL default '',
  `confvalue` text NOT NULL,
  PRIMARY KEY  (`confname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `config`
-- 

INSERT INTO `config` (`confname`, `confvalue`) VALUES 
('AllowOrderLineItemNarrative', '0'),
('AllowSalesOfZeroCostItems', '0'),
('AutoCreateWOs', '1'),
('AutoDebtorNo', '0'),
('AutoIssue', '1'),
('CheckCreditLimits', '1'),
('Check_Price_Charged_vs_Order_Price', '1'),
('Check_Qty_Charged_vs_Del_Qty', '1'),
('CountryOfOperation', 'RMB'),
('CreditingControlledItems_MustExist', '0'),
('DB_Maintenance', '30'),
('DB_Maintenance_LastRun', '2009-07-07'),
('DefaultBlindPackNote', '1'),
('DefaultCreditLimit', '1000'),
('DefaultCustomerType', '1'),
('DefaultDateFormat', 'd/m/Y'),
('DefaultDisplayRecordsMax', '50'),
('DefaultFactoryLocation', 'MEL'),
('DefaultPriceList', '01'),
('DefaultTaxCategory', '1'),
('DefaultTheme', 'silverwolf'),
('Default_Shipper', '1'),
('DefineControlledOnWOEntry', '1'),
('DispatchCutOffTime', '14'),
('DoFreightCalc', '0'),
('EDIHeaderMsgId', 'D:01B:UN:EAN010'),
('EDIReference', 'WEBERP'),
('EDI_Incoming_Orders', 'companies/weberp/EDI_Incoming_Orders'),
('EDI_MsgPending', 'companies/weberp/EDI_MsgPending'),
('EDI_MsgSent', 'companies/weberp/EDI_Sent'),
('Extended_CustomerInfo', '0'),
('Extended_SupplierInfo', '0'),
('FactoryManagerEmail', 'phil@logicworks.co.nz'),
('FreightChargeAppliesIfLessThan', '1000'),
('FreightTaxCategory', '1'),
('geocode_integration', '0'),
('HTTPS_Only', '0'),
('InvoicePortraitFormat', '0'),
('MaxImageSize', '300'),
('MonthsAuditTrail', '1'),
('NumberOfPeriodsOfStockUsage', '12'),
('OverChargeProportion', '30'),
('OverReceiveProportion', '20'),
('PackNoteFormat', '1'),
('PageLength', '48'),
('part_pics_dir', 'companies/weberpdemo/part_pics'),
('PastDueDays1', '30'),
('PastDueDays2', '60'),
('PO_AllowSameItemMultipleTimes', '1'),
('ProhibitJournalsToControlAccounts', '1'),
('ProhibitNegativeStock', '1'),
('ProhibitPostingsBefore', '2009-08-31'),
('QuickEntries', '10'),
('RadioBeaconFileCounter', '/home/RadioBeacon/FileCounter'),
('RadioBeaconFTP_user_name', 'RadioBeacon ftp server user name'),
('RadioBeaconHomeDir', '/home/RadioBeacon'),
('RadioBeaconStockLocation', 'BL'),
('RadioBraconFTP_server', '192.168.2.2'),
('RadioBreaconFilePrefix', 'ORDXX'),
('RadionBeaconFTP_user_pass', 'Radio Beacon remote ftp server password'),
('reports_dir', 'companies/weberpdemo/reportwriter'),
('RomalpaClause', 'Ownership will not pass to the buyer until the goods have been paid for in full.'),
('Show_Settled_LastMonth', '1'),
('SO_AllowSameItemMultipleTimes', '1'),
('TaxAuthorityReferenceName', 'Tax Ref'),
('UpdateCurrencyRatesDaily', '0'),
('WeightedAverageCosting', '1'),
('WikiApp', 'WackoWiki'),
('WikiPath', 'wiki'),
('YearEnd', '3');

-- --------------------------------------------------------

-- 
-- 表的结构 `contractbom`
-- 

CREATE TABLE `contractbom` (
  `contractref` char(20) NOT NULL default '',
  `component` char(20) NOT NULL default '',
  `workcentreadded` char(5) NOT NULL default '',
  `loccode` char(5) NOT NULL default '',
  `quantity` double NOT NULL default '1',
  PRIMARY KEY  (`contractref`,`component`,`workcentreadded`,`loccode`),
  KEY `Component` (`component`),
  KEY `LocCode` (`loccode`),
  KEY `ContractRef` (`contractref`),
  KEY `WorkCentreAdded` (`workcentreadded`),
  KEY `WorkCentreAdded_2` (`workcentreadded`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `contractbom`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `contractreqts`
-- 

CREATE TABLE `contractreqts` (
  `contractreqid` int(11) NOT NULL auto_increment,
  `contract` char(20) NOT NULL default '',
  `component` char(40) NOT NULL default '',
  `quantity` double NOT NULL default '1',
  `priceperunit` decimal(20,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`contractreqid`),
  KEY `Contract` (`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `contractreqts`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `contracts`
-- 

CREATE TABLE `contracts` (
  `contractref` varchar(20) NOT NULL default '',
  `contractdescription` varchar(50) NOT NULL default '',
  `debtorno` varchar(10) NOT NULL default '',
  `branchcode` varchar(10) NOT NULL default '',
  `status` varchar(10) NOT NULL default 'Quotation',
  `categoryid` varchar(6) NOT NULL default '',
  `typeabbrev` char(2) NOT NULL default '',
  `orderno` int(11) NOT NULL default '0',
  `quotedpricefx` decimal(20,4) NOT NULL default '0.0000',
  `margin` double NOT NULL default '1',
  `woref` varchar(20) NOT NULL default '',
  `requireddate` datetime NOT NULL default '0000-00-00 00:00:00',
  `canceldate` datetime NOT NULL default '0000-00-00 00:00:00',
  `quantityreqd` double NOT NULL default '1',
  `specifications` longblob NOT NULL,
  `datequoted` datetime NOT NULL default '0000-00-00 00:00:00',
  `units` varchar(15) NOT NULL default 'Each',
  `drawing` longblob NOT NULL,
  `rate` double NOT NULL default '1',
  PRIMARY KEY  (`contractref`),
  KEY `OrderNo` (`orderno`),
  KEY `CategoryID` (`categoryid`),
  KEY `Status` (`status`),
  KEY `TypeAbbrev` (`typeabbrev`),
  KEY `WORef` (`woref`),
  KEY `DebtorNo` (`debtorno`,`branchcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `contracts`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `currencies`
-- 

CREATE TABLE `currencies` (
  `currency` char(20) NOT NULL default '',
  `currabrev` char(3) NOT NULL default '',
  `country` char(50) NOT NULL default '',
  `hundredsname` char(15) NOT NULL default 'Cents',
  `rate` double NOT NULL default '1',
  PRIMARY KEY  (`currabrev`),
  KEY `Country` (`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `currencies`
-- 

INSERT INTO `currencies` (`currency`, `currabrev`, `country`, `hundredsname`, `rate`) VALUES 
('Australian Dollars', 'AUD', 'Australia', 'cents', 1),
('Swiss Francs', 'CHF', 'Swizerland', 'centimes', 1),
('Euro', 'EUR', 'Euroland', 'cents', 0.44),
('Pounds', 'GBP', 'England', 'Pence', 0.8),
('China Renminbi', 'RMB', 'China', '', 5.44),
('US Dollars', 'USD', 'United States', 'Cents', 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `custallocns`
-- 

CREATE TABLE `custallocns` (
  `id` int(11) NOT NULL auto_increment,
  `amt` decimal(20,4) NOT NULL default '0.0000',
  `datealloc` date NOT NULL default '0000-00-00',
  `transid_allocfrom` int(11) NOT NULL default '0',
  `transid_allocto` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `DateAlloc` (`datealloc`),
  KEY `TransID_AllocFrom` (`transid_allocfrom`),
  KEY `TransID_AllocTo` (`transid_allocto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `custallocns`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `custbranch`
-- 

CREATE TABLE `custbranch` (
  `branchcode` varchar(10) NOT NULL default '',
  `debtorno` varchar(10) NOT NULL default '',
  `brname` varchar(40) NOT NULL default '',
  `braddress1` varchar(40) NOT NULL default '',
  `braddress2` varchar(40) NOT NULL default '',
  `braddress3` varchar(40) NOT NULL default '',
  `braddress4` varchar(50) NOT NULL default '',
  `braddress5` varchar(20) NOT NULL default '',
  `braddress6` varchar(15) NOT NULL default '',
  `lat` float(10,6) NOT NULL default '0.000000',
  `lng` float(10,6) NOT NULL default '0.000000',
  `estdeliverydays` smallint(6) NOT NULL default '1',
  `area` char(3) NOT NULL,
  `salesman` varchar(4) NOT NULL default '',
  `fwddate` smallint(6) NOT NULL default '0',
  `phoneno` varchar(20) NOT NULL default '',
  `faxno` varchar(20) NOT NULL default '',
  `contactname` varchar(30) NOT NULL default '',
  `email` varchar(55) NOT NULL default '',
  `defaultlocation` varchar(5) NOT NULL default '',
  `taxgroupid` tinyint(4) NOT NULL default '1',
  `defaultshipvia` int(11) NOT NULL default '1',
  `deliverblind` tinyint(1) default '1',
  `disabletrans` tinyint(4) NOT NULL default '0',
  `brpostaddr1` varchar(40) NOT NULL default '',
  `brpostaddr2` varchar(40) NOT NULL default '',
  `brpostaddr3` varchar(30) NOT NULL default '',
  `brpostaddr4` varchar(20) NOT NULL default '',
  `brpostaddr5` varchar(20) NOT NULL default '',
  `brpostaddr6` varchar(15) NOT NULL default '',
  `specialinstructions` text NOT NULL,
  `custbranchcode` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`branchcode`,`debtorno`),
  KEY `BrName` (`brname`),
  KEY `DebtorNo` (`debtorno`),
  KEY `Salesman` (`salesman`),
  KEY `Area` (`area`),
  KEY `DefaultLocation` (`defaultlocation`),
  KEY `DefaultShipVia` (`defaultshipvia`),
  KEY `taxgroupid` (`taxgroupid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `custbranch`
-- 

INSERT INTO `custbranch` (`branchcode`, `debtorno`, `brname`, `braddress1`, `braddress2`, `braddress3`, `braddress4`, `braddress5`, `braddress6`, `lat`, `lng`, `estdeliverydays`, `area`, `salesman`, `fwddate`, `phoneno`, `faxno`, `contactname`, `email`, `defaultlocation`, `taxgroupid`, `defaultshipvia`, `deliverblind`, `disabletrans`, `brpostaddr1`, `brpostaddr2`, `brpostaddr3`, `brpostaddr4`, `brpostaddr5`, `brpostaddr6`, `specialinstructions`, `custbranchcode`) VALUES 
('0002', '0002', 'Onreal', 'ÉîÛÚ±¦É½ÊÐ', '', '', '', '', '', 0.000000, 0.000000, 0, '01', '01', 0, '', '', '', '', 'HZ', 1, 1, 1, 0, '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `custcontacts`
-- 

CREATE TABLE `custcontacts` (
  `contid` int(11) NOT NULL auto_increment,
  `debtorno` varchar(10) NOT NULL,
  `contactname` varchar(40) NOT NULL,
  `role` varchar(40) NOT NULL,
  `phoneno` varchar(20) NOT NULL,
  `notes` varchar(255) NOT NULL,
  PRIMARY KEY  (`contid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `custcontacts`
-- 

INSERT INTO `custcontacts` (`contid`, `debtorno`, `contactname`, `role`, `phoneno`, `notes`) VALUES 
(1, 'HYT', 'ÎÅÐ¡Áá', '²É¹º¹¤³ÌÊ¦', '', ''),
(2, '0002', 'ÌÆèaÓ¢', '²É¹º¹¤³ÌÊ¦', '', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `custnotes`
-- 

CREATE TABLE `custnotes` (
  `noteid` tinyint(4) NOT NULL auto_increment,
  `debtorno` varchar(10) NOT NULL default '0',
  `href` varchar(100) NOT NULL,
  `note` varchar(200) NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `priority` varchar(20) NOT NULL,
  PRIMARY KEY  (`noteid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `custnotes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `debtorsmaster`
-- 

CREATE TABLE `debtorsmaster` (
  `debtorno` varchar(10) NOT NULL default '',
  `name` varchar(40) NOT NULL default '',
  `address1` varchar(40) NOT NULL default '',
  `address2` varchar(40) NOT NULL default '',
  `address3` varchar(40) NOT NULL default '',
  `address4` varchar(50) NOT NULL default '',
  `address5` varchar(20) NOT NULL default '',
  `address6` varchar(15) NOT NULL default '',
  `currcode` char(3) NOT NULL default '',
  `salestype` char(2) NOT NULL default '',
  `clientsince` datetime NOT NULL default '0000-00-00 00:00:00',
  `holdreason` smallint(6) NOT NULL default '0',
  `paymentterms` char(2) NOT NULL default 'f',
  `discount` double NOT NULL default '0',
  `pymtdiscount` double NOT NULL default '0',
  `lastpaid` double NOT NULL default '0',
  `lastpaiddate` datetime default NULL,
  `creditlimit` double NOT NULL default '1000',
  `invaddrbranch` tinyint(4) NOT NULL default '0',
  `discountcode` char(2) NOT NULL default '',
  `ediinvoices` tinyint(4) NOT NULL default '0',
  `ediorders` tinyint(4) NOT NULL default '0',
  `edireference` varchar(20) NOT NULL default '',
  `editransport` varchar(5) NOT NULL default 'email',
  `ediaddress` varchar(50) NOT NULL default '',
  `ediserveruser` varchar(20) NOT NULL default '',
  `ediserverpwd` varchar(20) NOT NULL default '',
  `taxref` varchar(20) NOT NULL default '',
  `customerpoline` tinyint(1) NOT NULL default '0',
  `typeid` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`debtorno`),
  KEY `Currency` (`currcode`),
  KEY `HoldReason` (`holdreason`),
  KEY `Name` (`name`),
  KEY `PaymentTerms` (`paymentterms`),
  KEY `SalesType` (`salestype`),
  KEY `EDIInvoices` (`ediinvoices`),
  KEY `EDIOrders` (`ediorders`),
  KEY `debtorsmaster_ibfk_5` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `debtorsmaster`
-- 

INSERT INTO `debtorsmaster` (`debtorno`, `name`, `address1`, `address2`, `address3`, `address4`, `address5`, `address6`, `currcode`, `salestype`, `clientsince`, `holdreason`, `paymentterms`, `discount`, `pymtdiscount`, `lastpaid`, `lastpaiddate`, `creditlimit`, `invaddrbranch`, `discountcode`, `ediinvoices`, `ediorders`, `edireference`, `editransport`, `ediaddress`, `ediserveruser`, `ediserverpwd`, `taxref`, `customerpoline`, `typeid`) VALUES 
('0002', '°²ÔÃ', 'ÉîÛÚ±¦É½ÊÐ', '', '', '', '', '', 'RMB', '02', '2008-01-01 00:00:00', 1, '20', 0, 0, 0, NULL, 1000, 0, '', 0, 0, '', 'email', '', '', '', '', 0, 1),
('HYT', 'ÉîÛÚºÃÒ×Í¨', 'ÉîÛÚÊÐÄÏÉ½Çø', '', '', '', '', '', 'RMB', '01', '2008-01-01 00:00:00', 1, '20', 0, 0, 0, NULL, 1000, 0, '', 0, 0, '', 'email', '', '', '', '', 0, 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `debtortrans`
-- 

CREATE TABLE `debtortrans` (
  `id` int(11) NOT NULL auto_increment,
  `transno` int(11) NOT NULL default '0',
  `type` smallint(6) NOT NULL default '0',
  `debtorno` varchar(10) NOT NULL default '',
  `branchcode` varchar(10) NOT NULL default '',
  `trandate` datetime NOT NULL default '0000-00-00 00:00:00',
  `prd` smallint(6) NOT NULL default '0',
  `settled` tinyint(4) NOT NULL default '0',
  `reference` varchar(20) NOT NULL default '',
  `tpe` char(2) NOT NULL default '',
  `order_` int(11) NOT NULL default '0',
  `rate` double NOT NULL default '0',
  `ovamount` double NOT NULL default '0',
  `ovgst` double NOT NULL default '0',
  `ovfreight` double NOT NULL default '0',
  `ovdiscount` double NOT NULL default '0',
  `diffonexch` double NOT NULL default '0',
  `alloc` double NOT NULL default '0',
  `invtext` text,
  `shipvia` varchar(10) NOT NULL default '',
  `edisent` tinyint(4) NOT NULL default '0',
  `consignment` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `DebtorNo` (`debtorno`,`branchcode`),
  KEY `Order_` (`order_`),
  KEY `Prd` (`prd`),
  KEY `Tpe` (`tpe`),
  KEY `Type` (`type`),
  KEY `Settled` (`settled`),
  KEY `TranDate` (`trandate`),
  KEY `TransNo` (`transno`),
  KEY `Type_2` (`type`,`transno`),
  KEY `EDISent` (`edisent`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `debtortrans`
-- 

INSERT INTO `debtortrans` (`id`, `transno`, `type`, `debtorno`, `branchcode`, `trandate`, `prd`, `settled`, `reference`, `tpe`, `order_`, `rate`, `ovamount`, `ovgst`, `ovfreight`, `ovdiscount`, `diffonexch`, `alloc`, `invtext`, `shipvia`, `edisent`, `consignment`) VALUES 
(1, 1, 10, '0002', '0002', '2009-07-08 00:00:00', 1, 0, '', '02', 1, 5.44, 12000, 0, 0, 0, 0, 0, 'Í³Ò»¿ªÆ±', '1', 0, 'sf1000001');

-- --------------------------------------------------------

-- 
-- 表的结构 `debtortranstaxes`
-- 

CREATE TABLE `debtortranstaxes` (
  `debtortransid` int(11) NOT NULL default '0',
  `taxauthid` tinyint(4) NOT NULL default '0',
  `taxamount` double NOT NULL default '0',
  PRIMARY KEY  (`debtortransid`,`taxauthid`),
  KEY `taxauthid` (`taxauthid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `debtortranstaxes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `debtortype`
-- 

CREATE TABLE `debtortype` (
  `typeid` tinyint(4) NOT NULL auto_increment,
  `typename` varchar(100) NOT NULL,
  PRIMARY KEY  (`typeid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `debtortype`
-- 

INSERT INTO `debtortype` (`typeid`, `typename`) VALUES 
(1, 'Default');

-- --------------------------------------------------------

-- 
-- 表的结构 `debtortypenotes`
-- 

CREATE TABLE `debtortypenotes` (
  `noteid` tinyint(4) NOT NULL auto_increment,
  `typeid` tinyint(4) NOT NULL default '0',
  `href` varchar(100) NOT NULL,
  `note` varchar(200) NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `priority` varchar(20) NOT NULL,
  PRIMARY KEY  (`noteid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `debtortypenotes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `deliverynotes`
-- 

CREATE TABLE `deliverynotes` (
  `deliverynotenumber` int(11) NOT NULL,
  `deliverynotelineno` tinyint(4) NOT NULL,
  `salesorderno` int(11) NOT NULL,
  `salesorderlineno` int(11) NOT NULL,
  `qtydelivered` double NOT NULL default '0',
  `printed` tinyint(4) NOT NULL default '0',
  `invoiced` tinyint(4) NOT NULL default '0',
  `deliverydate` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`deliverynotenumber`,`deliverynotelineno`),
  KEY `deliverynotes_ibfk_2` (`salesorderno`,`salesorderlineno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `deliverynotes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `discountmatrix`
-- 

CREATE TABLE `discountmatrix` (
  `salestype` char(2) NOT NULL default '',
  `discountcategory` char(2) NOT NULL default '',
  `quantitybreak` int(11) NOT NULL default '1',
  `discountrate` double NOT NULL default '0',
  PRIMARY KEY  (`salestype`,`discountcategory`,`quantitybreak`),
  KEY `QuantityBreak` (`quantitybreak`),
  KEY `DiscountCategory` (`discountcategory`),
  KEY `SalesType` (`salestype`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `discountmatrix`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `ediitemmapping`
-- 

CREATE TABLE `ediitemmapping` (
  `supporcust` varchar(4) NOT NULL default '',
  `partnercode` varchar(10) NOT NULL default '',
  `stockid` varchar(20) NOT NULL default '',
  `partnerstockid` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`supporcust`,`partnercode`,`stockid`),
  KEY `PartnerCode` (`partnercode`),
  KEY `StockID` (`stockid`),
  KEY `PartnerStockID` (`partnerstockid`),
  KEY `SuppOrCust` (`supporcust`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `ediitemmapping`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edimessageformat`
-- 

CREATE TABLE `edimessageformat` (
  `id` int(11) NOT NULL auto_increment,
  `partnercode` varchar(10) NOT NULL default '',
  `messagetype` varchar(6) NOT NULL default '',
  `section` varchar(7) NOT NULL default '',
  `sequenceno` int(11) NOT NULL default '0',
  `linetext` varchar(70) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `PartnerCode` (`partnercode`,`messagetype`,`sequenceno`),
  KEY `Section` (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `edimessageformat`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `edi_orders_segs`
-- 

CREATE TABLE `edi_orders_segs` (
  `id` int(11) NOT NULL auto_increment,
  `segtag` char(3) NOT NULL default '',
  `seggroup` tinyint(4) NOT NULL default '0',
  `maxoccur` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `SegTag` (`segtag`),
  KEY `SegNo` (`seggroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=96 ;

-- 
-- 导出表中的数据 `edi_orders_segs`
-- 

INSERT INTO `edi_orders_segs` (`id`, `segtag`, `seggroup`, `maxoccur`) VALUES 
(1, 'UNB', 0, 1),
(2, 'UNH', 0, 1),
(3, 'BGM', 0, 1),
(4, 'DTM', 0, 35),
(5, 'PAI', 0, 1),
(6, 'ALI', 0, 5),
(7, 'FTX', 0, 99),
(8, 'RFF', 1, 1),
(9, 'DTM', 1, 5),
(10, 'NAD', 2, 1),
(11, 'LOC', 2, 99),
(12, 'FII', 2, 5),
(13, 'RFF', 3, 1),
(14, 'CTA', 5, 1),
(15, 'COM', 5, 5),
(16, 'TAX', 6, 1),
(17, 'MOA', 6, 1),
(18, 'CUX', 7, 1),
(19, 'DTM', 7, 5),
(20, 'PAT', 8, 1),
(21, 'DTM', 8, 5),
(22, 'PCD', 8, 1),
(23, 'MOA', 9, 1),
(24, 'TDT', 10, 1),
(25, 'LOC', 11, 1),
(26, 'DTM', 11, 5),
(27, 'TOD', 12, 1),
(28, 'LOC', 12, 2),
(29, 'PAC', 13, 1),
(30, 'PCI', 14, 1),
(31, 'RFF', 14, 1),
(32, 'DTM', 14, 5),
(33, 'GIN', 14, 10),
(34, 'EQD', 15, 1),
(35, 'ALC', 19, 1),
(36, 'ALI', 19, 5),
(37, 'DTM', 19, 5),
(38, 'QTY', 20, 1),
(39, 'RNG', 20, 1),
(40, 'PCD', 21, 1),
(41, 'RNG', 21, 1),
(42, 'MOA', 22, 1),
(43, 'RNG', 22, 1),
(44, 'RTE', 23, 1),
(45, 'RNG', 23, 1),
(46, 'TAX', 24, 1),
(47, 'MOA', 24, 1),
(48, 'LIN', 28, 1),
(49, 'PIA', 28, 25),
(50, 'IMD', 28, 99),
(51, 'MEA', 28, 99),
(52, 'QTY', 28, 99),
(53, 'ALI', 28, 5),
(54, 'DTM', 28, 35),
(55, 'MOA', 28, 10),
(56, 'GIN', 28, 127),
(57, 'QVR', 28, 1),
(58, 'FTX', 28, 99),
(59, 'PRI', 32, 1),
(60, 'CUX', 32, 1),
(61, 'DTM', 32, 5),
(62, 'RFF', 33, 1),
(63, 'DTM', 33, 5),
(64, 'PAC', 34, 1),
(65, 'QTY', 34, 5),
(66, 'PCI', 36, 1),
(67, 'RFF', 36, 1),
(68, 'DTM', 36, 5),
(69, 'GIN', 36, 10),
(70, 'LOC', 37, 1),
(71, 'QTY', 37, 1),
(72, 'DTM', 37, 5),
(73, 'TAX', 38, 1),
(74, 'MOA', 38, 1),
(75, 'NAD', 39, 1),
(76, 'CTA', 42, 1),
(77, 'COM', 42, 5),
(78, 'ALC', 43, 1),
(79, 'ALI', 43, 5),
(80, 'DTM', 43, 5),
(81, 'QTY', 44, 1),
(82, 'RNG', 44, 1),
(83, 'PCD', 45, 1),
(84, 'RNG', 45, 1),
(85, 'MOA', 46, 1),
(86, 'RNG', 46, 1),
(87, 'RTE', 47, 1),
(88, 'RNG', 47, 1),
(89, 'TAX', 48, 1),
(90, 'MOA', 48, 1),
(91, 'TDT', 49, 1),
(92, 'UNS', 50, 1),
(93, 'MOA', 50, 1),
(94, 'CNT', 50, 1),
(95, 'UNT', 50, 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `edi_orders_seg_groups`
-- 

CREATE TABLE `edi_orders_seg_groups` (
  `seggroupno` tinyint(4) NOT NULL default '0',
  `maxoccur` int(4) NOT NULL default '0',
  `parentseggroup` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`seggroupno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `edi_orders_seg_groups`
-- 

INSERT INTO `edi_orders_seg_groups` (`seggroupno`, `maxoccur`, `parentseggroup`) VALUES 
(0, 1, 0),
(1, 9999, 0),
(2, 99, 0),
(3, 99, 2),
(5, 5, 2),
(6, 5, 0),
(7, 5, 0),
(8, 10, 0),
(9, 9999, 8),
(10, 10, 0),
(11, 10, 10),
(12, 5, 0),
(13, 99, 0),
(14, 5, 13),
(15, 10, 0),
(19, 99, 0),
(20, 1, 19),
(21, 1, 19),
(22, 2, 19),
(23, 1, 19),
(24, 5, 19),
(28, 200000, 0),
(32, 25, 28),
(33, 9999, 28),
(34, 99, 28),
(36, 5, 34),
(37, 9999, 28),
(38, 10, 28),
(39, 999, 28),
(42, 5, 39),
(43, 99, 28),
(44, 1, 43),
(45, 1, 43),
(46, 2, 43),
(47, 1, 43),
(48, 5, 43),
(49, 10, 28),
(50, 1, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `factorcompanies`
-- 

CREATE TABLE `factorcompanies` (
  `id` int(11) NOT NULL auto_increment,
  `coyname` varchar(50) NOT NULL default '',
  `address1` varchar(40) NOT NULL default '',
  `address2` varchar(40) NOT NULL default '',
  `address3` varchar(40) NOT NULL default '',
  `address4` varchar(40) NOT NULL default '',
  `address5` varchar(20) NOT NULL default '',
  `address6` varchar(15) NOT NULL default '',
  `contact` varchar(25) NOT NULL default '',
  `telephone` varchar(25) NOT NULL default '',
  `fax` varchar(25) NOT NULL default '',
  `email` varchar(55) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `factorcompanies`
-- 

INSERT INTO `factorcompanies` (`id`, `coyname`, `address1`, `address2`, `address3`, `address4`, `address5`, `address6`, `contact`, `telephone`, `fax`, `email`) VALUES 
(1, 'None', '', '', '', '', '', '', '', '', '', ''),
(2, 'None', '', '', '', '', '', '', '', '', '', ''),
(3, 'None', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `freightcosts`
-- 

CREATE TABLE `freightcosts` (
  `shipcostfromid` int(11) NOT NULL auto_increment,
  `locationfrom` varchar(5) NOT NULL default '',
  `destination` varchar(40) NOT NULL default '',
  `shipperid` int(11) NOT NULL default '0',
  `cubrate` double NOT NULL default '0',
  `kgrate` double NOT NULL default '0',
  `maxkgs` double NOT NULL default '999999',
  `maxcub` double NOT NULL default '999999',
  `fixedprice` double NOT NULL default '0',
  `minimumchg` double NOT NULL default '0',
  PRIMARY KEY  (`shipcostfromid`),
  KEY `Destination` (`destination`),
  KEY `LocationFrom` (`locationfrom`),
  KEY `ShipperID` (`shipperid`),
  KEY `Destination_2` (`destination`,`locationfrom`,`shipperid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `freightcosts`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `geocode_param`
-- 

CREATE TABLE `geocode_param` (
  `geocodeid` tinyint(4) NOT NULL auto_increment,
  `geocode_key` varchar(200) NOT NULL default '',
  `center_long` varchar(20) NOT NULL default '',
  `center_lat` varchar(20) NOT NULL default '',
  `map_height` varchar(10) NOT NULL default '',
  `map_width` varchar(10) NOT NULL default '',
  `map_host` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`geocodeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `geocode_param`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `gltrans`
-- 

CREATE TABLE `gltrans` (
  `counterindex` int(11) NOT NULL auto_increment,
  `type` smallint(6) NOT NULL default '0',
  `typeno` bigint(16) NOT NULL default '1',
  `chequeno` int(11) NOT NULL default '0',
  `trandate` date NOT NULL default '0000-00-00',
  `periodno` smallint(6) NOT NULL default '0',
  `account` int(11) NOT NULL default '0',
  `narrative` varchar(200) NOT NULL default '',
  `amount` double NOT NULL default '0',
  `posted` tinyint(4) NOT NULL default '0',
  `jobref` varchar(20) NOT NULL default '',
  `tag` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`counterindex`),
  KEY `Account` (`account`),
  KEY `ChequeNo` (`chequeno`),
  KEY `PeriodNo` (`periodno`),
  KEY `Posted` (`posted`),
  KEY `TranDate` (`trandate`),
  KEY `TypeNo` (`typeno`),
  KEY `Type_and_Number` (`type`,`typeno`),
  KEY `JobRef` (`jobref`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- 
-- 导出表中的数据 `gltrans`
-- 

INSERT INTO `gltrans` (`counterindex`, `type`, `typeno`, `chequeno`, `trandate`, `periodno`, `account`, `narrative`, `amount`, `posted`, `jobref`, `tag`) VALUES 
(1, 25, 1, 0, '2009-07-07', 1, 1, 'PO: 1 HHNEC - SRT3210WAFER - SRT3210 Wafer x 50 @ 0.00', 0, 0, '', 0),
(2, 25, 1, 0, '2009-07-07', 1, 2150, '²É¹º¶©µ¥: 1 HHNEC - SRT3210WAFER - SRT3210 Wafer x 50 @ 0.00', 0, 0, '', 0),
(3, 25, 2, 0, '2009-07-07', 1, 1, 'PO: 3 SILAN - SRT3210 - SRT3210 Chips x 30000 @ 0.00', 0, 0, '', 0),
(4, 25, 2, 0, '2009-07-07', 1, 2150, '²É¹º¶©µ¥: 3 SILAN - SRT3210 - SRT3210 Chips x 30000 @ 0.00', 0, 0, '', 0),
(5, 35, 1, 0, '2009-07-07', 1, 1, 'SRT3210 ³É±¾ÊÇ 0 ¸ü¸Äµ½ 6 x ÔÚÊÖÉÏ¶©µ¥ 33000', -198000, 0, '', 0),
(6, 35, 1, 0, '2009-07-07', 1, 1, 'SRT3210 ³É±¾ÊÇ 0 ¸ü¸Äµ½ 6 x ÔÚÊÖÉÏ¶©µ¥ 33000', 198000, 0, '', 0),
(7, 10, 1, 0, '2009-07-08', 1, 5000, '0002 - SRT3210 x 1000 @ 6.0000', 6000, 0, '', 0),
(8, 10, 1, 0, '2009-07-08', 1, 1, '0002 - SRT3210 x 1000 @ 6.0000', -6000, 0, '', 0),
(9, 10, 1, 0, '2009-07-08', 1, 4100, '0002 - SRT3210 x 1000 @ 12', -2205.88235294, 0, '', 0),
(10, 10, 1, 0, '2009-07-08', 1, 1100, '0002', 2205.88235294, 0, '', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `grns`
-- 

CREATE TABLE `grns` (
  `grnbatch` smallint(6) NOT NULL default '0',
  `grnno` int(11) NOT NULL auto_increment,
  `podetailitem` int(11) NOT NULL default '0',
  `itemcode` varchar(20) NOT NULL default '',
  `deliverydate` date NOT NULL default '0000-00-00',
  `itemdescription` varchar(100) NOT NULL default '',
  `qtyrecd` double NOT NULL default '0',
  `quantityinv` double NOT NULL default '0',
  `supplierid` varchar(10) NOT NULL default '',
  `stdcostunit` double NOT NULL default '0',
  PRIMARY KEY  (`grnno`),
  KEY `DeliveryDate` (`deliverydate`),
  KEY `ItemCode` (`itemcode`),
  KEY `PODetailItem` (`podetailitem`),
  KEY `SupplierID` (`supplierid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `grns`
-- 

INSERT INTO `grns` (`grnbatch`, `grnno`, `podetailitem`, `itemcode`, `deliverydate`, `itemdescription`, `qtyrecd`, `quantityinv`, `supplierid`, `stdcostunit`) VALUES 
(1, 1, 1, 'SRT3210WAFER', '2009-07-07', 'SRT3210 Wafer', 50, 0, 'HHNEC', 0),
(2, 2, 3, 'SRT3210', '2009-07-07', 'SRT3210 Chips', 30000, 0, 'SILAN', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `holdreasons`
-- 

CREATE TABLE `holdreasons` (
  `reasoncode` smallint(6) NOT NULL default '1',
  `reasondescription` char(30) NOT NULL default '',
  `dissallowinvoices` tinyint(4) NOT NULL default '-1',
  PRIMARY KEY  (`reasoncode`),
  KEY `ReasonCode` (`reasoncode`),
  KEY `ReasonDescription` (`reasondescription`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `holdreasons`
-- 

INSERT INTO `holdreasons` (`reasoncode`, `reasondescription`, `dissallowinvoices`) VALUES 
(1, 'Good History', 0),
(20, 'Watch', 0),
(51, 'In liquidation', 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `lastcostrollup`
-- 

CREATE TABLE `lastcostrollup` (
  `stockid` char(20) NOT NULL default '',
  `totalonhand` double NOT NULL default '0',
  `matcost` decimal(20,4) NOT NULL default '0.0000',
  `labcost` decimal(20,4) NOT NULL default '0.0000',
  `oheadcost` decimal(20,4) NOT NULL default '0.0000',
  `categoryid` char(6) NOT NULL default '',
  `stockact` int(11) NOT NULL default '0',
  `adjglact` int(11) NOT NULL default '0',
  `newmatcost` decimal(20,4) NOT NULL default '0.0000',
  `newlabcost` decimal(20,4) NOT NULL default '0.0000',
  `newoheadcost` decimal(20,4) NOT NULL default '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `lastcostrollup`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `locations`
-- 

CREATE TABLE `locations` (
  `loccode` varchar(5) NOT NULL default '',
  `locationname` varchar(50) NOT NULL default '',
  `deladd1` varchar(40) NOT NULL default '',
  `deladd2` varchar(40) NOT NULL default '',
  `deladd3` varchar(40) NOT NULL default '',
  `deladd4` varchar(40) NOT NULL default '',
  `deladd5` varchar(20) NOT NULL default '',
  `deladd6` varchar(15) NOT NULL default '',
  `tel` varchar(30) NOT NULL default '',
  `fax` varchar(30) NOT NULL default '',
  `email` varchar(55) NOT NULL default '',
  `contact` varchar(30) NOT NULL default '',
  `taxprovinceid` tinyint(4) NOT NULL default '1',
  `managed` int(11) default '0',
  PRIMARY KEY  (`loccode`),
  KEY `taxprovinceid` (`taxprovinceid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `locations`
-- 

INSERT INTO `locations` (`loccode`, `locationname`, `deladd1`, `deladd2`, `deladd3`, `deladd4`, `deladd5`, `deladd6`, `tel`, `fax`, `email`, `contact`, `taxprovinceid`, `managed`) VALUES 
('HZ', 'º¼ÖÝ²Ö¿â', ' ', '', '', '', '', '', '', '', '', '', 1, 0),
('MEL', 'Melbourne', '1234 Collins Street', 'Melbourne', 'Victoria 2345', '', '', 'Australia', '+61 3 56789012', '+61 3 56789013', 'jacko@webdemo.com', 'Jack Roberts', 1, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `locstock`
-- 

CREATE TABLE `locstock` (
  `loccode` varchar(5) NOT NULL default '',
  `stockid` varchar(20) NOT NULL default '',
  `quantity` double NOT NULL default '0',
  `reorderlevel` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`loccode`,`stockid`),
  KEY `StockID` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `locstock`
-- 

INSERT INTO `locstock` (`loccode`, `stockid`, `quantity`, `reorderlevel`) VALUES 
('HZ', 'SRT3210', 32000, 0),
('HZ', 'SRT3210DICE', 0, 0),
('HZ', 'SRT3210WAFER', 50, 0),
('MEL', 'SRT3210', 0, 0),
('MEL', 'SRT3210DICE', 0, 0),
('MEL', 'SRT3210WAFER', 0, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `loctransfers`
-- 

CREATE TABLE `loctransfers` (
  `reference` int(11) NOT NULL default '0',
  `stockid` varchar(20) NOT NULL default '',
  `shipqty` int(11) NOT NULL default '0',
  `recqty` int(11) NOT NULL default '0',
  `shipdate` date NOT NULL default '0000-00-00',
  `recdate` date NOT NULL default '0000-00-00',
  `shiploc` varchar(7) NOT NULL default '',
  `recloc` varchar(7) NOT NULL default '',
  KEY `Reference` (`reference`,`stockid`),
  KEY `ShipLoc` (`shiploc`),
  KEY `RecLoc` (`recloc`),
  KEY `StockID` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores Shipments To And From Locations';

-- 
-- 导出表中的数据 `loctransfers`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `mrpcalendar`
-- 

CREATE TABLE `mrpcalendar` (
  `calendardate` date NOT NULL,
  `daynumber` int(6) NOT NULL,
  `manufacturingflag` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`calendardate`),
  KEY `daynumber` (`daynumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `mrpcalendar`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `mrpdemands`
-- 

CREATE TABLE `mrpdemands` (
  `demandid` int(11) NOT NULL auto_increment,
  `stockid` varchar(20) NOT NULL default '',
  `mrpdemandtype` varchar(6) NOT NULL default '',
  `quantity` double NOT NULL default '0',
  `duedate` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`demandid`),
  KEY `StockID` (`stockid`),
  KEY `mrpdemands_ibfk_1` (`mrpdemandtype`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `mrpdemands`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `mrpdemandtypes`
-- 

CREATE TABLE `mrpdemandtypes` (
  `mrpdemandtype` varchar(6) NOT NULL default '',
  `description` char(30) NOT NULL default '',
  PRIMARY KEY  (`mrpdemandtype`),
  KEY `mrpdemandtype` (`mrpdemandtype`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `mrpdemandtypes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `orderdeliverydifferenceslog`
-- 

CREATE TABLE `orderdeliverydifferenceslog` (
  `orderno` int(11) NOT NULL default '0',
  `invoiceno` int(11) NOT NULL default '0',
  `stockid` varchar(20) NOT NULL default '',
  `quantitydiff` double NOT NULL default '0',
  `debtorno` varchar(10) NOT NULL default '',
  `branch` varchar(10) NOT NULL default '',
  `can_or_bo` char(3) NOT NULL default 'CAN',
  PRIMARY KEY  (`orderno`,`invoiceno`,`stockid`),
  KEY `StockID` (`stockid`),
  KEY `DebtorNo` (`debtorno`,`branch`),
  KEY `Can_or_BO` (`can_or_bo`),
  KEY `OrderNo` (`orderno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `orderdeliverydifferenceslog`
-- 

INSERT INTO `orderdeliverydifferenceslog` (`orderno`, `invoiceno`, `stockid`, `quantitydiff`, `debtorno`, `branch`, `can_or_bo`) VALUES 
(1, 1, 'SRT3210', 2000, '0002', '0002', 'BO');

-- --------------------------------------------------------

-- 
-- 表的结构 `paymentmethods`
-- 

CREATE TABLE `paymentmethods` (
  `paymentid` tinyint(4) NOT NULL auto_increment,
  `paymentname` varchar(15) NOT NULL default '',
  `paymenttype` int(11) NOT NULL default '1',
  `receipttype` int(11) NOT NULL default '1',
  PRIMARY KEY  (`paymentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `paymentmethods`
-- 

INSERT INTO `paymentmethods` (`paymentid`, `paymentname`, `paymenttype`, `receipttype`) VALUES 
(1, 'Cheque', 1, 1),
(2, 'Cash', 1, 1),
(3, 'Direct Credit', 1, 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `paymentterms`
-- 

CREATE TABLE `paymentterms` (
  `termsindicator` char(2) NOT NULL default '',
  `terms` char(40) NOT NULL default '',
  `daysbeforedue` smallint(6) NOT NULL default '0',
  `dayinfollowingmonth` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`termsindicator`),
  KEY `DaysBeforeDue` (`daysbeforedue`),
  KEY `DayInFollowingMonth` (`dayinfollowingmonth`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `paymentterms`
-- 

INSERT INTO `paymentterms` (`termsindicator`, `terms`, `daysbeforedue`, `dayinfollowingmonth`) VALUES 
('20', 'Due 20th Of the Following Month', 0, 22),
('30', 'Due By End Of The Following Month', 0, 30),
('7', 'Payment due within 7 days', 7, 0),
('CA', 'Cash Only', 2, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `periods`
-- 

CREATE TABLE `periods` (
  `periodno` smallint(6) NOT NULL default '0',
  `lastdate_in_period` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`periodno`),
  KEY `LastDate_in_Period` (`lastdate_in_period`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `periods`
-- 

INSERT INTO `periods` (`periodno`, `lastdate_in_period`) VALUES 
(0, '2009-07-31'),
(1, '2009-08-31'),
(2, '2009-09-30'),
(3, '2009-10-31');

-- --------------------------------------------------------

-- 
-- 表的结构 `prices`
-- 

CREATE TABLE `prices` (
  `stockid` varchar(20) NOT NULL default '',
  `typeabbrev` char(2) NOT NULL default '',
  `currabrev` char(3) NOT NULL default '',
  `debtorno` varchar(10) NOT NULL default '',
  `price` decimal(20,4) NOT NULL default '0.0000',
  `branchcode` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`stockid`,`typeabbrev`,`currabrev`,`debtorno`),
  KEY `CurrAbrev` (`currabrev`),
  KEY `DebtorNo` (`debtorno`),
  KEY `StockID` (`stockid`),
  KEY `TypeAbbrev` (`typeabbrev`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `prices`
-- 

INSERT INTO `prices` (`stockid`, `typeabbrev`, `currabrev`, `debtorno`, `price`, `branchcode`) VALUES 
('SRT3210', '01', 'AUD', '', 10.0000, ''),
('SRT3210DICE', '01', 'AUD', '', 4.8000, '');

-- --------------------------------------------------------

-- 
-- 表的结构 `purchdata`
-- 

CREATE TABLE `purchdata` (
  `supplierno` char(10) NOT NULL default '',
  `stockid` char(20) NOT NULL default '',
  `price` decimal(20,4) NOT NULL default '0.0000',
  `suppliersuom` char(50) NOT NULL default '',
  `conversionfactor` double NOT NULL default '1',
  `supplierdescription` char(50) NOT NULL default '',
  `leadtime` smallint(6) NOT NULL default '1',
  `preferred` tinyint(4) NOT NULL default '0',
  `effectivefrom` date NOT NULL,
  PRIMARY KEY  (`supplierno`,`stockid`,`effectivefrom`),
  KEY `StockID` (`stockid`),
  KEY `SupplierNo` (`supplierno`),
  KEY `Preferred` (`preferred`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `purchdata`
-- 

INSERT INTO `purchdata` (`supplierno`, `stockid`, `price`, `suppliersuom`, `conversionfactor`, `supplierdescription`, `leadtime`, `preferred`, `effectivefrom`) VALUES 
('SILAN', 'SRT3210', 6.0000, '¿Å', 1, '', 60, 1, '2009-07-07');

-- --------------------------------------------------------

-- 
-- 表的结构 `purchorderdetails`
-- 

CREATE TABLE `purchorderdetails` (
  `podetailitem` int(11) NOT NULL auto_increment,
  `orderno` int(11) NOT NULL default '0',
  `itemcode` varchar(20) NOT NULL default '',
  `deliverydate` date NOT NULL default '0000-00-00',
  `itemdescription` varchar(100) NOT NULL default '',
  `glcode` int(11) NOT NULL default '0',
  `qtyinvoiced` double NOT NULL default '0',
  `unitprice` double NOT NULL default '0',
  `actprice` double NOT NULL default '0',
  `stdcostunit` double NOT NULL default '0',
  `quantityord` double NOT NULL default '0',
  `quantityrecd` double NOT NULL default '0',
  `shiptref` int(11) NOT NULL default '0',
  `jobref` varchar(20) NOT NULL default '',
  `completed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`podetailitem`),
  KEY `DeliveryDate` (`deliverydate`),
  KEY `GLCode` (`glcode`),
  KEY `ItemCode` (`itemcode`),
  KEY `JobRef` (`jobref`),
  KEY `OrderNo` (`orderno`),
  KEY `ShiptRef` (`shiptref`),
  KEY `Completed` (`completed`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `purchorderdetails`
-- 

INSERT INTO `purchorderdetails` (`podetailitem`, `orderno`, `itemcode`, `deliverydate`, `itemdescription`, `glcode`, `qtyinvoiced`, `unitprice`, `actprice`, `stdcostunit`, `quantityord`, `quantityrecd`, `shiptref`, `jobref`, `completed`) VALUES 
(1, 1, 'SRT3210WAFER', '2009-07-08', 'SRT3210 Wafer', 1, 0, 5000, 0, 0, 50, 50, 0, '', 1),
(2, 2, 'SRT3210WAFER', '2009-07-08', 'SRT3210 Wafer', 1, 0, 5000, 0, 0, 51, 0, 0, '', 0),
(3, 3, 'SRT3210', '2009-08-09', 'SRT3210 Chips', 1, 0, 4, 0, 0, 30000, 30000, 1, '', 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `purchorders`
-- 

CREATE TABLE `purchorders` (
  `orderno` int(11) NOT NULL auto_increment,
  `supplierno` varchar(10) NOT NULL default '',
  `comments` longblob,
  `orddate` datetime NOT NULL default '0000-00-00 00:00:00',
  `rate` double NOT NULL default '1',
  `dateprinted` datetime default NULL,
  `allowprint` tinyint(4) NOT NULL default '1',
  `initiator` varchar(10) default NULL,
  `requisitionno` varchar(15) default NULL,
  `intostocklocation` varchar(5) NOT NULL default '',
  `deladd1` varchar(40) NOT NULL default '',
  `deladd2` varchar(40) NOT NULL default '',
  `deladd3` varchar(40) NOT NULL default '',
  `deladd4` varchar(40) NOT NULL default '',
  `deladd5` varchar(20) NOT NULL default '',
  `deladd6` varchar(15) NOT NULL default '',
  `contact` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`orderno`),
  KEY `OrdDate` (`orddate`),
  KEY `SupplierNo` (`supplierno`),
  KEY `IntoStockLocation` (`intostocklocation`),
  KEY `AllowPrintPO` (`allowprint`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `purchorders`
-- 

INSERT INTO `purchorders` (`orderno`, `supplierno`, `comments`, `orddate`, `rate`, `dateprinted`, `allowprint`, `initiator`, `requisitionno`, `intostocklocation`, `deladd1`, `deladd2`, `deladd3`, `deladd4`, `deladd5`, `deladd6`, `contact`) VALUES 
(1, 'HHNEC', '', '2009-07-07 00:00:00', 1, NULL, 1, '', '0', 'HZ', '1234 Collins Street', 'Melbourne', 'Victoria 2345', '', '', 'Australia', ''),
(2, 'HHNEC', '', '2009-07-07 00:00:00', 1, NULL, 1, '', '0', 'HZ', '1234 Collins Street', 'Melbourne', 'Victoria 2345', '', '', 'Australia', ''),
(3, 'SILAN', '', '2009-07-07 00:00:00', 1, NULL, 1, '', '0', 'HZ', '1234 Collins Street', 'Melbourne', 'Victoria 2345', '', '', 'Australia', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `recurringsalesorders`
-- 

CREATE TABLE `recurringsalesorders` (
  `recurrorderno` int(11) NOT NULL auto_increment,
  `debtorno` varchar(10) NOT NULL default '',
  `branchcode` varchar(10) NOT NULL default '',
  `customerref` varchar(50) NOT NULL default '',
  `buyername` varchar(50) default NULL,
  `comments` longblob,
  `orddate` date NOT NULL default '0000-00-00',
  `ordertype` char(2) NOT NULL default '',
  `shipvia` int(11) NOT NULL default '0',
  `deladd1` varchar(40) NOT NULL default '',
  `deladd2` varchar(40) NOT NULL default '',
  `deladd3` varchar(40) NOT NULL default '',
  `deladd4` varchar(40) default NULL,
  `deladd5` varchar(20) NOT NULL default '',
  `deladd6` varchar(15) NOT NULL default '',
  `contactphone` varchar(25) default NULL,
  `contactemail` varchar(25) default NULL,
  `deliverto` varchar(40) NOT NULL default '',
  `freightcost` double NOT NULL default '0',
  `fromstkloc` varchar(5) NOT NULL default '',
  `lastrecurrence` date NOT NULL default '0000-00-00',
  `stopdate` date NOT NULL default '0000-00-00',
  `frequency` tinyint(4) NOT NULL default '1',
  `autoinvoice` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`recurrorderno`),
  KEY `debtorno` (`debtorno`),
  KEY `orddate` (`orddate`),
  KEY `ordertype` (`ordertype`),
  KEY `locationindex` (`fromstkloc`),
  KEY `branchcode` (`branchcode`,`debtorno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `recurringsalesorders`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `recurrsalesorderdetails`
-- 

CREATE TABLE `recurrsalesorderdetails` (
  `recurrorderno` int(11) NOT NULL default '0',
  `stkcode` varchar(20) NOT NULL default '',
  `unitprice` double NOT NULL default '0',
  `quantity` double NOT NULL default '0',
  `discountpercent` double NOT NULL default '0',
  `narrative` text NOT NULL,
  KEY `orderno` (`recurrorderno`),
  KEY `stkcode` (`stkcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `recurrsalesorderdetails`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `reportcolumns`
-- 

CREATE TABLE `reportcolumns` (
  `reportid` smallint(6) NOT NULL default '0',
  `colno` smallint(6) NOT NULL default '0',
  `heading1` varchar(15) NOT NULL default '',
  `heading2` varchar(15) default NULL,
  `calculation` tinyint(1) NOT NULL default '0',
  `periodfrom` smallint(6) default NULL,
  `periodto` smallint(6) default NULL,
  `datatype` varchar(15) default NULL,
  `colnumerator` tinyint(4) default NULL,
  `coldenominator` tinyint(4) default NULL,
  `calcoperator` char(1) default NULL,
  `budgetoractual` tinyint(1) NOT NULL default '0',
  `valformat` char(1) NOT NULL default 'N',
  `constant` double NOT NULL default '0',
  PRIMARY KEY  (`reportid`,`colno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `reportcolumns`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `reportfields`
-- 

CREATE TABLE `reportfields` (
  `id` int(8) NOT NULL auto_increment,
  `reportid` int(5) NOT NULL default '0',
  `entrytype` varchar(15) NOT NULL default '',
  `seqnum` int(3) NOT NULL default '0',
  `fieldname` varchar(35) NOT NULL default '',
  `displaydesc` varchar(25) NOT NULL default '',
  `visible` enum('1','0') NOT NULL default '1',
  `columnbreak` enum('1','0') NOT NULL default '1',
  `params` text,
  PRIMARY KEY  (`id`),
  KEY `reportid` (`reportid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `reportfields`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `reportheaders`
-- 

CREATE TABLE `reportheaders` (
  `reportid` smallint(6) NOT NULL auto_increment,
  `reportheading` varchar(80) NOT NULL default '',
  `groupbydata1` varchar(15) NOT NULL default '',
  `newpageafter1` tinyint(1) NOT NULL default '0',
  `lower1` varchar(10) NOT NULL default '',
  `upper1` varchar(10) NOT NULL default '',
  `groupbydata2` varchar(15) default NULL,
  `newpageafter2` tinyint(1) NOT NULL default '0',
  `lower2` varchar(10) default NULL,
  `upper2` varchar(10) default NULL,
  `groupbydata3` varchar(15) default NULL,
  `newpageafter3` tinyint(1) NOT NULL default '0',
  `lower3` varchar(10) default NULL,
  `upper3` varchar(10) default NULL,
  `groupbydata4` varchar(15) NOT NULL default '',
  `newpageafter4` tinyint(1) NOT NULL default '0',
  `upper4` varchar(10) NOT NULL default '',
  `lower4` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`reportid`),
  KEY `ReportHeading` (`reportheading`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `reportheaders`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `reportlinks`
-- 

CREATE TABLE `reportlinks` (
  `table1` varchar(25) NOT NULL default '',
  `table2` varchar(25) NOT NULL default '',
  `equation` varchar(75) NOT NULL default ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `reportlinks`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `reports`
-- 

CREATE TABLE `reports` (
  `id` int(5) NOT NULL auto_increment,
  `reportname` varchar(30) NOT NULL default '',
  `reporttype` char(3) NOT NULL default 'rpt',
  `groupname` varchar(9) NOT NULL default 'misc',
  `defaultreport` enum('1','0') NOT NULL default '0',
  `papersize` varchar(15) NOT NULL default 'A4,210,297',
  `paperorientation` enum('P','L') NOT NULL default 'P',
  `margintop` int(3) NOT NULL default '10',
  `marginbottom` int(3) NOT NULL default '10',
  `marginleft` int(3) NOT NULL default '10',
  `marginright` int(3) NOT NULL default '10',
  `coynamefont` varchar(20) NOT NULL default 'Helvetica',
  `coynamefontsize` int(3) NOT NULL default '12',
  `coynamefontcolor` varchar(11) NOT NULL default '0,0,0',
  `coynamealign` enum('L','C','R') NOT NULL default 'C',
  `coynameshow` enum('1','0') NOT NULL default '1',
  `title1desc` varchar(50) NOT NULL default '%reportname%',
  `title1font` varchar(20) NOT NULL default 'Helvetica',
  `title1fontsize` int(3) NOT NULL default '10',
  `title1fontcolor` varchar(11) NOT NULL default '0,0,0',
  `title1fontalign` enum('L','C','R') NOT NULL default 'C',
  `title1show` enum('1','0') NOT NULL default '1',
  `title2desc` varchar(50) NOT NULL default 'Report Generated %date%',
  `title2font` varchar(20) NOT NULL default 'Helvetica',
  `title2fontsize` int(3) NOT NULL default '10',
  `title2fontcolor` varchar(11) NOT NULL default '0,0,0',
  `title2fontalign` enum('L','C','R') NOT NULL default 'C',
  `title2show` enum('1','0') NOT NULL default '1',
  `filterfont` varchar(10) NOT NULL default 'Helvetica',
  `filterfontsize` int(3) NOT NULL default '8',
  `filterfontcolor` varchar(11) NOT NULL default '0,0,0',
  `filterfontalign` enum('L','C','R') NOT NULL default 'L',
  `datafont` varchar(10) NOT NULL default 'Helvetica',
  `datafontsize` int(3) NOT NULL default '10',
  `datafontcolor` varchar(10) NOT NULL default 'black',
  `datafontalign` enum('L','C','R') NOT NULL default 'L',
  `totalsfont` varchar(10) NOT NULL default 'Helvetica',
  `totalsfontsize` int(3) NOT NULL default '10',
  `totalsfontcolor` varchar(11) NOT NULL default '0,0,0',
  `totalsfontalign` enum('L','C','R') NOT NULL default 'L',
  `col1width` int(3) NOT NULL default '25',
  `col2width` int(3) NOT NULL default '25',
  `col3width` int(3) NOT NULL default '25',
  `col4width` int(3) NOT NULL default '25',
  `col5width` int(3) NOT NULL default '25',
  `col6width` int(3) NOT NULL default '25',
  `col7width` int(3) NOT NULL default '25',
  `col8width` int(3) NOT NULL default '25',
  `table1` varchar(25) NOT NULL default '',
  `table2` varchar(25) default NULL,
  `table2criteria` varchar(75) default NULL,
  `table3` varchar(25) default NULL,
  `table3criteria` varchar(75) default NULL,
  `table4` varchar(25) default NULL,
  `table4criteria` varchar(75) default NULL,
  `table5` varchar(25) default NULL,
  `table5criteria` varchar(75) default NULL,
  `table6` varchar(25) default NULL,
  `table6criteria` varchar(75) default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`reportname`,`groupname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `reports`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `salesanalysis`
-- 

CREATE TABLE `salesanalysis` (
  `typeabbrev` char(2) NOT NULL default '',
  `periodno` smallint(6) NOT NULL default '0',
  `amt` double NOT NULL default '0',
  `cost` double NOT NULL default '0',
  `cust` varchar(10) NOT NULL default '',
  `custbranch` varchar(10) NOT NULL default '',
  `qty` double NOT NULL default '0',
  `disc` double NOT NULL default '0',
  `stockid` varchar(20) NOT NULL default '',
  `area` varchar(3) NOT NULL,
  `budgetoractual` tinyint(1) NOT NULL default '0',
  `salesperson` char(3) NOT NULL default '',
  `stkcategory` varchar(6) NOT NULL default '',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `CustBranch` (`custbranch`),
  KEY `Cust` (`cust`),
  KEY `PeriodNo` (`periodno`),
  KEY `StkCategory` (`stkcategory`),
  KEY `StockID` (`stockid`),
  KEY `TypeAbbrev` (`typeabbrev`),
  KEY `Area` (`area`),
  KEY `BudgetOrActual` (`budgetoractual`),
  KEY `Salesperson` (`salesperson`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `salesanalysis`
-- 

INSERT INTO `salesanalysis` (`typeabbrev`, `periodno`, `amt`, `cost`, `cust`, `custbranch`, `qty`, `disc`, `stockid`, `area`, `budgetoractual`, `salesperson`, `stkcategory`, `id`) VALUES 
('02', 1, 2205.88235294, 6000, '0002', '0002', 1000, 0, 'SRT3210', '01', 1, '01', '01', 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `salescat`
-- 

CREATE TABLE `salescat` (
  `salescatid` tinyint(4) NOT NULL auto_increment,
  `parentcatid` tinyint(4) default NULL,
  `salescatname` varchar(30) default NULL,
  PRIMARY KEY  (`salescatid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `salescat`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `salescatprod`
-- 

CREATE TABLE `salescatprod` (
  `salescatid` tinyint(4) NOT NULL default '0',
  `stockid` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`salescatid`,`stockid`),
  KEY `salescatid` (`salescatid`),
  KEY `stockid` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `salescatprod`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `salesglpostings`
-- 

CREATE TABLE `salesglpostings` (
  `id` int(11) NOT NULL auto_increment,
  `area` varchar(3) NOT NULL,
  `stkcat` varchar(6) NOT NULL default '',
  `discountglcode` int(11) NOT NULL default '0',
  `salesglcode` int(11) NOT NULL default '0',
  `salestype` char(2) NOT NULL default 'AN',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Area_StkCat` (`area`,`stkcat`,`salestype`),
  KEY `Area` (`area`),
  KEY `StkCat` (`stkcat`),
  KEY `SalesType` (`salestype`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `salesglpostings`
-- 

INSERT INTO `salesglpostings` (`id`, `area`, `stkcat`, `discountglcode`, `salesglcode`, `salestype`) VALUES 
(1, 'AN', 'ANY', 4900, 4100, 'AN'),
(2, 'AN', 'AIRCON', 5000, 4800, 'DE');

-- --------------------------------------------------------

-- 
-- 表的结构 `salesman`
-- 

CREATE TABLE `salesman` (
  `salesmancode` char(3) NOT NULL default '',
  `salesmanname` char(30) NOT NULL default '',
  `smantel` char(20) NOT NULL default '',
  `smanfax` char(20) NOT NULL default '',
  `commissionrate1` double NOT NULL default '0',
  `breakpoint` decimal(10,0) NOT NULL default '0',
  `commissionrate2` double NOT NULL default '0',
  PRIMARY KEY  (`salesmancode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `salesman`
-- 

INSERT INTO `salesman` (`salesmancode`, `salesmanname`, `smantel`, `smanfax`, `commissionrate1`, `breakpoint`, `commissionrate2`) VALUES 
('01', 'qinkunsong', '', '', 0, 0, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `salesorderdetails`
-- 

CREATE TABLE `salesorderdetails` (
  `orderlineno` int(11) NOT NULL default '0',
  `orderno` int(11) NOT NULL default '0',
  `stkcode` varchar(20) NOT NULL default '',
  `qtyinvoiced` double NOT NULL default '0',
  `unitprice` double NOT NULL default '0',
  `quantity` double NOT NULL default '0',
  `estimate` tinyint(4) NOT NULL default '0',
  `discountpercent` double NOT NULL default '0',
  `actualdispatchdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `completed` tinyint(1) NOT NULL default '0',
  `narrative` text,
  `itemdue` date default NULL COMMENT 'Due date for line item.  Some customers require \r\nacknowledgements with due dates by line item',
  `poline` varchar(10) default NULL COMMENT 'Some Customers require acknowledgements with a PO line number for each sales line',
  PRIMARY KEY  (`orderlineno`,`orderno`),
  KEY `OrderNo` (`orderno`),
  KEY `StkCode` (`stkcode`),
  KEY `Completed` (`completed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `salesorderdetails`
-- 

INSERT INTO `salesorderdetails` (`orderlineno`, `orderno`, `stkcode`, `qtyinvoiced`, `unitprice`, `quantity`, `estimate`, `discountpercent`, `actualdispatchdate`, `completed`, `narrative`, `itemdue`, `poline`) VALUES 
(0, 1, 'SRT3210', 1000, 12, 3000, 0, 0, '2009-07-08 00:00:00', 0, '', '2009-07-07', ''),
(0, 2, 'SRT3210', 0, 10, 1000, 0, 0.98, '0000-00-00 00:00:00', 0, '', '2009-07-08', ''),
(0, 3, 'SRT3210', 0, 10, 5000, 0, 0, '0000-00-00 00:00:00', 0, '', '2009-07-09', ''),
(1, 2, 'SRT3210', 0, 10, 2000, 0, 0.98, '0000-00-00 00:00:00', 0, '', '2009-07-08', ''),
(1, 3, 'SRT3210', 0, 10, 6000, 0, 0, '0000-00-00 00:00:00', 0, '', '2009-07-09', ''),
(2, 3, 'SRT3210', 0, 10, 7000, 0, 0, '0000-00-00 00:00:00', 0, '', '2009-07-09', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `salesorders`
-- 

CREATE TABLE `salesorders` (
  `orderno` int(11) NOT NULL,
  `debtorno` varchar(10) NOT NULL default '',
  `branchcode` varchar(10) NOT NULL default '',
  `customerref` varchar(50) NOT NULL default '',
  `buyername` varchar(50) default NULL,
  `comments` longblob,
  `orddate` date NOT NULL default '0000-00-00',
  `ordertype` char(2) NOT NULL default '',
  `shipvia` int(11) NOT NULL default '0',
  `deladd1` varchar(40) NOT NULL default '',
  `deladd2` varchar(40) NOT NULL default '',
  `deladd3` varchar(40) NOT NULL default '',
  `deladd4` varchar(40) default NULL,
  `deladd5` varchar(20) NOT NULL default '',
  `deladd6` varchar(15) NOT NULL default '',
  `contactphone` varchar(25) default NULL,
  `contactemail` varchar(40) default NULL,
  `deliverto` varchar(40) NOT NULL default '',
  `deliverblind` tinyint(1) default '1',
  `freightcost` double NOT NULL default '0',
  `fromstkloc` varchar(5) NOT NULL default '',
  `deliverydate` date NOT NULL default '0000-00-00',
  `quotedate` date NOT NULL default '0000-00-00',
  `confirmeddate` date NOT NULL default '0000-00-00',
  `printedpackingslip` tinyint(4) NOT NULL default '0',
  `datepackingslipprinted` date NOT NULL default '0000-00-00',
  `quotation` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`orderno`),
  KEY `DebtorNo` (`debtorno`),
  KEY `OrdDate` (`orddate`),
  KEY `OrderType` (`ordertype`),
  KEY `LocationIndex` (`fromstkloc`),
  KEY `BranchCode` (`branchcode`,`debtorno`),
  KEY `ShipVia` (`shipvia`),
  KEY `quotation` (`quotation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `salesorders`
-- 

INSERT INTO `salesorders` (`orderno`, `debtorno`, `branchcode`, `customerref`, `buyername`, `comments`, `orddate`, `ordertype`, `shipvia`, `deladd1`, `deladd2`, `deladd3`, `deladd4`, `deladd5`, `deladd6`, `contactphone`, `contactemail`, `deliverto`, `deliverblind`, `freightcost`, `fromstkloc`, `deliverydate`, `quotedate`, `confirmeddate`, `printedpackingslip`, `datepackingslipprinted`, `quotation`) VALUES 
(1, '0002', '0002', '', NULL, 0x20496e762031, '2009-07-07', '02', 1, 'ÉîÛÚ±¦É½ÊÐ', '', '', '', '', '', '', '', 'Onreal', 1, 0, 'HZ', '2009-07-07', '2009-07-07', '2009-07-07', 1, '2009-07-08', 0),
(2, '0002', '0002', '', NULL, '', '2009-07-08', '02', 1, 'ÉîÛÚ±¦É½ÊÐ', '', '', '', '', '', '', '', 'Onreal', 1, 0, 'HZ', '2009-07-08', '2009-07-08', '2009-07-08', 0, '0000-00-00', 1),
(3, '0002', '0002', '', NULL, '', '2009-07-09', '02', 1, 'ÉîÛÚ±¦É½ÊÐ', '', '', '', '', '', '', '', 'Onreal', 1, 0, 'HZ', '2009-07-09', '2009-07-09', '2009-07-09', 1, '2009-07-09', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `salestypes`
-- 

CREATE TABLE `salestypes` (
  `typeabbrev` char(2) NOT NULL default '',
  `sales_type` char(20) NOT NULL default '',
  PRIMARY KEY  (`typeabbrev`),
  KEY `Sales_Type` (`sales_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `salestypes`
-- 

INSERT INTO `salestypes` (`typeabbrev`, `sales_type`) VALUES 
('01', 'ÄÚÏú'),
('02', '³ö¿Ú');

-- --------------------------------------------------------

-- 
-- 表的结构 `scripts`
-- 

CREATE TABLE `scripts` (
  `pageid` smallint(4) NOT NULL auto_increment,
  `filename` varchar(50) NOT NULL default '',
  `pagedescription` text NOT NULL,
  PRIMARY KEY  (`pageid`),
  KEY `FileName` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Index of all scripts' AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `scripts`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `securitygroups`
-- 

CREATE TABLE `securitygroups` (
  `secroleid` int(11) NOT NULL default '0',
  `tokenid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`secroleid`,`tokenid`),
  KEY `secroleid` (`secroleid`),
  KEY `tokenid` (`tokenid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `securitygroups`
-- 

INSERT INTO `securitygroups` (`secroleid`, `tokenid`) VALUES 
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(2, 11),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 11),
(4, 1),
(4, 2),
(4, 5),
(5, 1),
(5, 2),
(5, 3),
(5, 11),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(6, 10),
(6, 11),
(7, 1),
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(8, 5),
(8, 6),
(8, 7),
(8, 8),
(8, 9),
(8, 10),
(8, 11),
(8, 12),
(8, 13),
(8, 14),
(8, 15);

-- --------------------------------------------------------

-- 
-- 表的结构 `securityroles`
-- 

CREATE TABLE `securityroles` (
  `secroleid` int(11) NOT NULL auto_increment,
  `secrolename` text NOT NULL,
  PRIMARY KEY  (`secroleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- 导出表中的数据 `securityroles`
-- 

INSERT INTO `securityroles` (`secroleid`, `secrolename`) VALUES 
(1, 'Inquiries/Order Entry'),
(2, 'Manufac/Stock Admin'),
(3, 'Purchasing Officer'),
(4, 'AP Clerk'),
(5, 'AR Clerk'),
(6, 'Accountant'),
(7, 'Customer Log On Only'),
(8, 'System Administrator');

-- --------------------------------------------------------

-- 
-- 表的结构 `securitytokens`
-- 

CREATE TABLE `securitytokens` (
  `tokenid` int(11) NOT NULL default '0',
  `tokenname` text NOT NULL,
  PRIMARY KEY  (`tokenid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `securitytokens`
-- 

INSERT INTO `securitytokens` (`tokenid`, `tokenname`) VALUES 
(1, 'Order Entry/Inquiries customer access only'),
(2, 'Basic Reports and Inquiries with selection options'),
(3, 'Credit notes and AR management'),
(4, 'Purchasing data/PO Entry/Reorder Levels'),
(5, 'Accounts Payable'),
(6, 'Not Used'),
(7, 'Bank Reconciliations'),
(8, 'General ledger reports/inquiries'),
(9, 'Not Used'),
(10, 'General Ledger Maintenance, stock valuation & Configuration'),
(11, 'Inventory Management and Pricing'),
(12, 'Unknown'),
(13, 'Unknown'),
(14, 'Unknown'),
(15, 'User Management and System Administration');

-- --------------------------------------------------------

-- 
-- 表的结构 `shipmentcharges`
-- 

CREATE TABLE `shipmentcharges` (
  `shiptchgid` int(11) NOT NULL auto_increment,
  `shiptref` int(11) NOT NULL default '0',
  `transtype` smallint(6) NOT NULL default '0',
  `transno` int(11) NOT NULL default '0',
  `stockid` varchar(20) NOT NULL default '',
  `value` double NOT NULL default '0',
  PRIMARY KEY  (`shiptchgid`),
  KEY `TransType` (`transtype`,`transno`),
  KEY `ShiptRef` (`shiptref`),
  KEY `StockID` (`stockid`),
  KEY `TransType_2` (`transtype`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `shipmentcharges`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `shipments`
-- 

CREATE TABLE `shipments` (
  `shiptref` int(11) NOT NULL default '0',
  `voyageref` varchar(20) NOT NULL default '0',
  `vessel` varchar(50) NOT NULL default '',
  `eta` datetime NOT NULL default '0000-00-00 00:00:00',
  `accumvalue` double NOT NULL default '0',
  `supplierid` varchar(10) NOT NULL default '',
  `closed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`shiptref`),
  KEY `ETA` (`eta`),
  KEY `SupplierID` (`supplierid`),
  KEY `ShipperRef` (`voyageref`),
  KEY `Vessel` (`vessel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `shipments`
-- 

INSERT INTO `shipments` (`shiptref`, `voyageref`, `vessel`, `eta`, `accumvalue`, `supplierid`, `closed`) VALUES 
(1, '0001', 'sunfeng', '2009-08-09 00:00:00', 0, 'SILAN', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `shippers`
-- 

CREATE TABLE `shippers` (
  `shipper_id` int(11) NOT NULL auto_increment,
  `shippername` char(40) NOT NULL default '',
  `mincharge` double NOT NULL default '0',
  PRIMARY KEY  (`shipper_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `shippers`
-- 

INSERT INTO `shippers` (`shipper_id`, `shippername`, `mincharge`) VALUES 
(1, 'Default Shipper', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `stockcategory`
-- 

CREATE TABLE `stockcategory` (
  `categoryid` char(6) NOT NULL default '',
  `categorydescription` char(20) NOT NULL default '',
  `stocktype` char(1) NOT NULL default 'F',
  `stockact` int(11) NOT NULL default '0',
  `adjglact` int(11) NOT NULL default '0',
  `purchpricevaract` int(11) NOT NULL default '80000',
  `materialuseagevarac` int(11) NOT NULL default '80000',
  `wipact` int(11) NOT NULL default '0',
  PRIMARY KEY  (`categoryid`),
  KEY `CategoryDescription` (`categorydescription`),
  KEY `StockType` (`stocktype`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `stockcategory`
-- 

INSERT INTO `stockcategory` (`categoryid`, `categorydescription`, `stocktype`, `stockact`, `adjglact`, `purchpricevaract`, `materialuseagevarac`, `wipact`) VALUES 
('01', 'WalkieTalkie Chips', 'F', 1, 1, 1, 1, 1010);

-- --------------------------------------------------------

-- 
-- 表的结构 `stockcatproperties`
-- 

CREATE TABLE `stockcatproperties` (
  `stkcatpropid` int(11) NOT NULL auto_increment,
  `categoryid` char(6) NOT NULL,
  `label` text NOT NULL,
  `controltype` tinyint(4) NOT NULL default '0',
  `defaultvalue` varchar(100) NOT NULL default '''''',
  `reqatsalesorder` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`stkcatpropid`),
  KEY `categoryid` (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `stockcatproperties`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `stockcheckfreeze`
-- 

CREATE TABLE `stockcheckfreeze` (
  `stockid` varchar(20) NOT NULL default '',
  `loccode` varchar(5) NOT NULL default '',
  `qoh` double NOT NULL default '0',
  PRIMARY KEY  (`stockid`,`loccode`),
  KEY `LocCode` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `stockcheckfreeze`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `stockcounts`
-- 

CREATE TABLE `stockcounts` (
  `id` int(11) NOT NULL auto_increment,
  `stockid` varchar(20) NOT NULL default '',
  `loccode` varchar(5) NOT NULL default '',
  `qtycounted` double NOT NULL default '0',
  `reference` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `StockID` (`stockid`),
  KEY `LocCode` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `stockcounts`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `stockitemproperties`
-- 

CREATE TABLE `stockitemproperties` (
  `stockid` varchar(20) NOT NULL,
  `stkcatpropid` int(11) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY  (`stockid`,`stkcatpropid`),
  KEY `stockid` (`stockid`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `stockitemproperties`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `stockmaster`
-- 

CREATE TABLE `stockmaster` (
  `stockid` varchar(20) NOT NULL default '',
  `categoryid` varchar(6) NOT NULL default '',
  `description` varchar(50) NOT NULL default '',
  `longdescription` text NOT NULL,
  `units` varchar(20) NOT NULL default 'each',
  `mbflag` char(1) NOT NULL default 'B',
  `lastcurcostdate` date NOT NULL default '1800-01-01',
  `actualcost` decimal(20,4) NOT NULL default '0.0000',
  `lastcost` decimal(20,4) NOT NULL default '0.0000',
  `materialcost` decimal(20,4) NOT NULL default '0.0000',
  `labourcost` decimal(20,4) NOT NULL default '0.0000',
  `overheadcost` decimal(20,4) NOT NULL default '0.0000',
  `lowestlevel` smallint(6) NOT NULL default '0',
  `discontinued` tinyint(4) NOT NULL default '0',
  `controlled` tinyint(4) NOT NULL default '0',
  `eoq` double NOT NULL default '0',
  `volume` decimal(20,4) NOT NULL default '0.0000',
  `kgs` decimal(20,4) NOT NULL default '0.0000',
  `barcode` varchar(50) NOT NULL default '',
  `discountcategory` char(2) NOT NULL default '',
  `taxcatid` tinyint(4) NOT NULL default '1',
  `serialised` tinyint(4) NOT NULL default '0',
  `appendfile` varchar(40) NOT NULL default 'none',
  `perishable` tinyint(1) NOT NULL default '0',
  `decimalplaces` tinyint(4) NOT NULL default '0',
  `nextserialno` bigint(20) NOT NULL default '0',
  `pansize` double NOT NULL default '0',
  `shrinkfactor` double NOT NULL default '0',
  PRIMARY KEY  (`stockid`),
  KEY `CategoryID` (`categoryid`),
  KEY `Description` (`description`),
  KEY `LastCurCostDate` (`lastcurcostdate`),
  KEY `MBflag` (`mbflag`),
  KEY `StockID` (`stockid`,`categoryid`),
  KEY `Controlled` (`controlled`),
  KEY `DiscountCategory` (`discountcategory`),
  KEY `taxcatid` (`taxcatid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `stockmaster`
-- 

INSERT INTO `stockmaster` (`stockid`, `categoryid`, `description`, `longdescription`, `units`, `mbflag`, `lastcurcostdate`, `actualcost`, `lastcost`, `materialcost`, `labourcost`, `overheadcost`, `lowestlevel`, `discontinued`, `controlled`, `eoq`, `volume`, `kgs`, `barcode`, `discountcategory`, `taxcatid`, `serialised`, `appendfile`, `perishable`, `decimalplaces`, `nextserialno`, `pansize`, `shrinkfactor`) VALUES 
('SRT3210', '01', 'SRT3210 Chips', 'Walkie Talkie Baseband chips', '¿Å', 'B', '1800-01-01', 0.0000, 0.0000, 6.0000, 0.0000, 0.0000, 0, 0, 0, 90, 0.0000, 0.0000, '', '', 1, 0, '0', 0, 0, 0, 0, 0),
('SRT3210DICE', '01', 'SRT3210 Dice', 'WT SRT3210 Dice', 'each', 'M', '1800-01-01', 0.0000, 0.0000, 0.0000, 8.0000, 4.0000, 0, 0, 0, 0, 0.0000, 0.0000, '', '', 1, 0, '0', 0, 0, 0, 0, 0),
('SRT3210WAFER', '01', 'SRT3210 Wafer', 'SRT3210 Wafer ', 'each', 'B', '1800-01-01', 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0, 0, 0, 0, 0.0000, 0.0000, '', '', 1, 0, '0', 0, 0, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `stockmoves`
-- 

CREATE TABLE `stockmoves` (
  `stkmoveno` int(11) NOT NULL auto_increment,
  `stockid` varchar(20) NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  `transno` int(11) NOT NULL default '0',
  `loccode` varchar(5) NOT NULL default '',
  `trandate` date NOT NULL default '0000-00-00',
  `debtorno` varchar(10) NOT NULL default '',
  `branchcode` varchar(10) NOT NULL default '',
  `price` decimal(20,4) NOT NULL default '0.0000',
  `prd` smallint(6) NOT NULL default '0',
  `reference` varchar(40) NOT NULL default '',
  `qty` double NOT NULL default '1',
  `discountpercent` double NOT NULL default '0',
  `standardcost` double NOT NULL default '0',
  `show_on_inv_crds` tinyint(4) NOT NULL default '1',
  `newqoh` double NOT NULL default '0',
  `hidemovt` tinyint(4) NOT NULL default '0',
  `narrative` text,
  PRIMARY KEY  (`stkmoveno`),
  KEY `DebtorNo` (`debtorno`),
  KEY `LocCode` (`loccode`),
  KEY `Prd` (`prd`),
  KEY `StockID_2` (`stockid`),
  KEY `TranDate` (`trandate`),
  KEY `TransNo` (`transno`),
  KEY `Type` (`type`),
  KEY `Show_On_Inv_Crds` (`show_on_inv_crds`),
  KEY `Hide` (`hidemovt`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- 导出表中的数据 `stockmoves`
-- 

INSERT INTO `stockmoves` (`stkmoveno`, `stockid`, `type`, `transno`, `loccode`, `trandate`, `debtorno`, `branchcode`, `price`, `prd`, `reference`, `qty`, `discountpercent`, `standardcost`, `show_on_inv_crds`, `newqoh`, `hidemovt`, `narrative`) VALUES 
(1, 'SRT3210', 17, 1, 'HZ', '2009-07-07', '', '', 0.0000, 1, 'Æð³õÅÌµã', 3000, 0, 0, 1, 3000, 0, NULL),
(2, 'SRT3210WAFER', 25, 1, 'HZ', '2009-07-07', '', '', 5000.0000, 1, 'HHNEC (»ªºçNEC) - 1', 50, 0, 0, 1, 50, 0, NULL),
(3, 'SRT3210', 25, 2, 'HZ', '2009-07-07', '', '', 4.0000, 1, 'SILAN (Silan) - 3', 30000, 0, 0, 1, 33000, 0, NULL),
(4, 'SRT3210', 10, 1, 'HZ', '2009-07-08', '0002', '0002', 2.2059, 1, '1', -1000, 0, 6, 1, 32000, 0, '');

-- --------------------------------------------------------

-- 
-- 表的结构 `stockmovestaxes`
-- 

CREATE TABLE `stockmovestaxes` (
  `stkmoveno` int(11) NOT NULL default '0',
  `taxauthid` tinyint(4) NOT NULL default '0',
  `taxrate` double NOT NULL default '0',
  `taxontax` tinyint(4) NOT NULL default '0',
  `taxcalculationorder` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`stkmoveno`,`taxauthid`),
  KEY `taxauthid` (`taxauthid`),
  KEY `calculationorder` (`taxcalculationorder`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `stockmovestaxes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `stockserialitems`
-- 

CREATE TABLE `stockserialitems` (
  `stockid` varchar(20) NOT NULL default '',
  `loccode` varchar(5) NOT NULL default '',
  `serialno` varchar(30) NOT NULL default '',
  `expirationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `quantity` double NOT NULL default '0',
  `qualitytext` text NOT NULL,
  PRIMARY KEY  (`stockid`,`serialno`,`loccode`),
  KEY `StockID` (`stockid`),
  KEY `LocCode` (`loccode`),
  KEY `serialno` (`serialno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `stockserialitems`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `stockserialmoves`
-- 

CREATE TABLE `stockserialmoves` (
  `stkitmmoveno` int(11) NOT NULL auto_increment,
  `stockmoveno` int(11) NOT NULL default '0',
  `stockid` varchar(20) NOT NULL default '',
  `serialno` varchar(30) NOT NULL default '',
  `moveqty` double NOT NULL default '0',
  PRIMARY KEY  (`stkitmmoveno`),
  KEY `StockMoveNo` (`stockmoveno`),
  KEY `StockID_SN` (`stockid`,`serialno`),
  KEY `serialno` (`serialno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `stockserialmoves`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `suppallocs`
-- 

CREATE TABLE `suppallocs` (
  `id` int(11) NOT NULL auto_increment,
  `amt` double NOT NULL default '0',
  `datealloc` date NOT NULL default '0000-00-00',
  `transid_allocfrom` int(11) NOT NULL default '0',
  `transid_allocto` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `TransID_AllocFrom` (`transid_allocfrom`),
  KEY `TransID_AllocTo` (`transid_allocto`),
  KEY `DateAlloc` (`datealloc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `suppallocs`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `suppliercontacts`
-- 

CREATE TABLE `suppliercontacts` (
  `supplierid` varchar(10) NOT NULL default '',
  `contact` varchar(30) NOT NULL default '',
  `position` varchar(30) NOT NULL default '',
  `tel` varchar(30) NOT NULL default '',
  `fax` varchar(30) NOT NULL default '',
  `mobile` varchar(30) NOT NULL default '',
  `email` varchar(55) NOT NULL default '',
  `ordercontact` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`supplierid`,`contact`),
  KEY `Contact` (`contact`),
  KEY `SupplierID` (`supplierid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `suppliercontacts`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `suppliers`
-- 

CREATE TABLE `suppliers` (
  `supplierid` varchar(10) NOT NULL default '',
  `suppname` varchar(40) NOT NULL default '',
  `address1` varchar(40) NOT NULL default '',
  `address2` varchar(40) NOT NULL default '',
  `address3` varchar(40) NOT NULL default '',
  `address4` varchar(50) NOT NULL default '',
  `address5` varchar(20) NOT NULL default '',
  `address6` varchar(15) NOT NULL default '',
  `lat` float(10,6) NOT NULL default '0.000000',
  `lng` float(10,6) NOT NULL default '0.000000',
  `currcode` char(3) NOT NULL default '',
  `suppliersince` date NOT NULL default '0000-00-00',
  `paymentterms` char(2) NOT NULL default '',
  `lastpaid` double NOT NULL default '0',
  `lastpaiddate` datetime default NULL,
  `bankact` varchar(30) NOT NULL default '',
  `bankref` varchar(12) NOT NULL default '',
  `bankpartics` varchar(12) NOT NULL default '',
  `remittance` tinyint(4) NOT NULL default '1',
  `taxgroupid` tinyint(4) NOT NULL default '1',
  `factorcompanyid` int(11) NOT NULL default '1',
  `taxref` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`supplierid`),
  KEY `CurrCode` (`currcode`),
  KEY `PaymentTerms` (`paymentterms`),
  KEY `SupplierID` (`supplierid`),
  KEY `SuppName` (`suppname`),
  KEY `taxgroupid` (`taxgroupid`),
  KEY `suppliers_ibfk_4` (`factorcompanyid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `suppliers`
-- 

INSERT INTO `suppliers` (`supplierid`, `suppname`, `address1`, `address2`, `address3`, `address4`, `address5`, `address6`, `lat`, `lng`, `currcode`, `suppliersince`, `paymentterms`, `lastpaid`, `lastpaiddate`, `bankact`, `bankref`, `bankpartics`, `remittance`, `taxgroupid`, `factorcompanyid`, `taxref`) VALUES 
('FUJITSU', '¸»Ê¿Í¨', 'ÄÏÍ¨', '', '', '', '', '', 0.000000, 0.000000, 'AUD', '2009-07-07', '20', 0, NULL, '', '0', '', 0, 1, 1, ''),
('HEJIAN', 'ºÍ½¢', '', '', '', '', '', '', 0.000000, 0.000000, 'AUD', '2009-07-07', '20', 0, NULL, '', '0', '', 0, 1, 1, ''),
('HHNEC', '»ªºçNEC', '', '', '', '', '', '', 0.000000, 0.000000, 'AUD', '2009-07-07', '20', 0, NULL, '', '0', '', 0, 1, 1, ''),
('SILAN', 'Silan', 'º¼ÖÝ', '', '', '', '', '', 0.000000, 0.000000, 'AUD', '2009-07-07', '20', 0, NULL, '', '0', '', 0, 1, 1, '');

-- --------------------------------------------------------

-- 
-- 表的结构 `supptrans`
-- 

CREATE TABLE `supptrans` (
  `transno` int(11) NOT NULL default '0',
  `type` smallint(6) NOT NULL default '0',
  `supplierno` varchar(10) NOT NULL default '',
  `suppreference` varchar(20) NOT NULL default '',
  `trandate` date NOT NULL default '0000-00-00',
  `duedate` date NOT NULL default '0000-00-00',
  `settled` tinyint(4) NOT NULL default '0',
  `rate` double NOT NULL default '1',
  `ovamount` double NOT NULL default '0',
  `ovgst` double NOT NULL default '0',
  `diffonexch` double NOT NULL default '0',
  `alloc` double NOT NULL default '0',
  `transtext` text,
  `hold` tinyint(4) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `TypeTransNo` (`transno`,`type`),
  KEY `DueDate` (`duedate`),
  KEY `Hold` (`hold`),
  KEY `SupplierNo` (`supplierno`),
  KEY `Settled` (`settled`),
  KEY `SupplierNo_2` (`supplierno`,`suppreference`),
  KEY `SuppReference` (`suppreference`),
  KEY `TranDate` (`trandate`),
  KEY `TransNo` (`transno`),
  KEY `Type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `supptrans`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `supptranstaxes`
-- 

CREATE TABLE `supptranstaxes` (
  `supptransid` int(11) NOT NULL default '0',
  `taxauthid` tinyint(4) NOT NULL default '0',
  `taxamount` double NOT NULL default '0',
  PRIMARY KEY  (`supptransid`,`taxauthid`),
  KEY `taxauthid` (`taxauthid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `supptranstaxes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `systypes`
-- 

CREATE TABLE `systypes` (
  `typeid` smallint(6) NOT NULL default '0',
  `typename` char(50) NOT NULL default '',
  `typeno` int(11) NOT NULL default '1',
  PRIMARY KEY  (`typeid`),
  KEY `TypeNo` (`typeno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `systypes`
-- 

INSERT INTO `systypes` (`typeid`, `typename`, `typeno`) VALUES 
(0, 'Journal - GL', 0),
(1, 'Payment - GL', 0),
(2, 'Receipt - GL', 0),
(3, 'Standing Journal', 0),
(10, 'Sales Invoice', 1),
(11, 'Credit Note', 0),
(12, 'Receipt', 0),
(15, 'Journal - Debtors', 0),
(16, 'Location Transfer', 3),
(17, 'Stock Adjustment', 1),
(18, 'Purchase Order', 3),
(20, 'Purchase Invoice', 0),
(21, 'Debit Note', 0),
(22, 'Creditors Payment', 0),
(23, 'Creditors Journal', 0),
(25, 'Purchase Order Delivery', 2),
(26, 'Work Order Receipt', 0),
(28, 'Work Order Issue', 0),
(29, 'Work Order Variance', 0),
(30, 'Sales Order', 3),
(31, 'Shipment Close', 1),
(35, 'Cost Update', 1),
(36, 'Exchange Difference', 0),
(40, 'Work Order', 2),
(50, 'Opening Balance', 0),
(500, 'Auto Debtor Number', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `tags`
-- 

CREATE TABLE `tags` (
  `tagref` tinyint(4) NOT NULL auto_increment,
  `tagdescription` varchar(50) NOT NULL,
  PRIMARY KEY  (`tagref`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `tags`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `taxauthorities`
-- 

CREATE TABLE `taxauthorities` (
  `taxid` tinyint(4) NOT NULL auto_increment,
  `description` varchar(20) NOT NULL default '',
  `taxglcode` int(11) NOT NULL default '0',
  `purchtaxglaccount` int(11) NOT NULL default '0',
  `bank` varchar(50) NOT NULL default '',
  `bankacctype` varchar(20) NOT NULL default '',
  `bankacc` varchar(50) NOT NULL default '',
  `bankswift` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`taxid`),
  KEY `TaxGLCode` (`taxglcode`),
  KEY `PurchTaxGLAccount` (`purchtaxglaccount`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- 
-- 导出表中的数据 `taxauthorities`
-- 

INSERT INTO `taxauthorities` (`taxid`, `description`, `taxglcode`, `purchtaxglaccount`, `bank`, `bankacctype`, `bankacc`, `bankswift`) VALUES 
(1, 'Australian GST', 2300, 2310, '', '', '', ''),
(5, 'Sales Tax', 2300, 2310, '', '', '', ''),
(11, 'Canadian GST', 2300, 2310, '', '', '', ''),
(12, 'Ontario PST', 2300, 2310, '', '', '', ''),
(13, 'UK VAT', 2300, 2310, '', '', '', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `taxauthrates`
-- 

CREATE TABLE `taxauthrates` (
  `taxauthority` tinyint(4) NOT NULL default '1',
  `dispatchtaxprovince` tinyint(4) NOT NULL default '1',
  `taxcatid` tinyint(4) NOT NULL default '0',
  `taxrate` double NOT NULL default '0',
  PRIMARY KEY  (`taxauthority`,`dispatchtaxprovince`,`taxcatid`),
  KEY `TaxAuthority` (`taxauthority`),
  KEY `dispatchtaxprovince` (`dispatchtaxprovince`),
  KEY `taxcatid` (`taxcatid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `taxauthrates`
-- 

INSERT INTO `taxauthrates` (`taxauthority`, `dispatchtaxprovince`, `taxcatid`, `taxrate`) VALUES 
(1, 1, 1, 0.1),
(1, 1, 2, 0),
(1, 1, 5, 0),
(1, 2, 1, 0),
(1, 2, 2, 0),
(1, 2, 4, 0),
(1, 2, 5, 0),
(5, 1, 1, 0.2),
(5, 1, 2, 0.35),
(5, 1, 5, 0),
(5, 2, 1, 0),
(5, 2, 2, 0),
(5, 2, 4, 0),
(5, 2, 5, 0),
(11, 1, 1, 0.07),
(11, 1, 2, 0.12),
(11, 1, 5, 0),
(11, 2, 1, 0),
(11, 2, 2, 0),
(11, 2, 4, 0),
(11, 2, 5, 0),
(12, 1, 1, 0.05),
(12, 1, 2, 0.075),
(12, 1, 5, 0),
(12, 2, 1, 0),
(12, 2, 2, 0),
(12, 2, 4, 0),
(12, 2, 5, 0),
(13, 1, 1, 0),
(13, 1, 2, 0),
(13, 1, 5, 0),
(13, 2, 1, 0),
(13, 2, 2, 0),
(13, 2, 4, 0),
(13, 2, 5, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `taxcategories`
-- 

CREATE TABLE `taxcategories` (
  `taxcatid` tinyint(4) NOT NULL auto_increment,
  `taxcatname` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`taxcatid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- 导出表中的数据 `taxcategories`
-- 

INSERT INTO `taxcategories` (`taxcatid`, `taxcatname`) VALUES 
(1, 'Taxable supply'),
(2, 'Luxury Items'),
(4, 'Exempt'),
(5, 'Freight');

-- --------------------------------------------------------

-- 
-- 表的结构 `taxgroups`
-- 

CREATE TABLE `taxgroups` (
  `taxgroupid` tinyint(4) NOT NULL auto_increment,
  `taxgroupdescription` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`taxgroupid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `taxgroups`
-- 

INSERT INTO `taxgroups` (`taxgroupid`, `taxgroupdescription`) VALUES 
(1, 'Default tax group'),
(2, 'Ontario'),
(3, 'UK Inland Revenue');

-- --------------------------------------------------------

-- 
-- 表的结构 `taxgrouptaxes`
-- 

CREATE TABLE `taxgrouptaxes` (
  `taxgroupid` tinyint(4) NOT NULL default '0',
  `taxauthid` tinyint(4) NOT NULL default '0',
  `calculationorder` tinyint(4) NOT NULL default '0',
  `taxontax` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`taxgroupid`,`taxauthid`),
  KEY `taxgroupid` (`taxgroupid`),
  KEY `taxauthid` (`taxauthid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `taxgrouptaxes`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `taxprovinces`
-- 

CREATE TABLE `taxprovinces` (
  `taxprovinceid` tinyint(4) NOT NULL auto_increment,
  `taxprovincename` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`taxprovinceid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `taxprovinces`
-- 

INSERT INTO `taxprovinces` (`taxprovinceid`, `taxprovincename`) VALUES 
(1, 'Default Tax province'),
(2, 'RPC');

-- --------------------------------------------------------

-- 
-- 表的结构 `unitsofmeasure`
-- 

CREATE TABLE `unitsofmeasure` (
  `unitid` tinyint(4) NOT NULL auto_increment,
  `unitname` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`unitid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- 导出表中的数据 `unitsofmeasure`
-- 

INSERT INTO `unitsofmeasure` (`unitid`, `unitname`) VALUES 
(1, 'each'),
(2, 'metres'),
(3, 'kgs'),
(4, 'litres'),
(5, 'length'),
(6, 'pack'),
(7, '¿Å');

-- --------------------------------------------------------

-- 
-- 表的结构 `woitems`
-- 

CREATE TABLE `woitems` (
  `wo` int(11) NOT NULL,
  `stockid` char(20) NOT NULL default '',
  `qtyreqd` double NOT NULL default '1',
  `qtyrecd` double NOT NULL default '0',
  `stdcost` double NOT NULL,
  `nextlotsnref` varchar(20) default '',
  PRIMARY KEY  (`wo`,`stockid`),
  KEY `stockid` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `woitems`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `worequirements`
-- 

CREATE TABLE `worequirements` (
  `wo` int(11) NOT NULL,
  `parentstockid` varchar(20) NOT NULL,
  `stockid` varchar(20) NOT NULL,
  `qtypu` double NOT NULL default '1',
  `stdcost` double NOT NULL default '0',
  `autoissue` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`wo`,`parentstockid`,`stockid`),
  KEY `stockid` (`stockid`),
  KEY `worequirements_ibfk_3` (`parentstockid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `worequirements`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `workcentres`
-- 

CREATE TABLE `workcentres` (
  `code` char(5) NOT NULL default '',
  `location` char(5) NOT NULL default '',
  `description` char(20) NOT NULL default '',
  `capacity` double NOT NULL default '1',
  `overheadperhour` decimal(10,0) NOT NULL default '0',
  `overheadrecoveryact` int(11) NOT NULL default '0',
  `setuphrs` decimal(10,0) NOT NULL default '0',
  PRIMARY KEY  (`code`),
  KEY `Description` (`description`),
  KEY `Location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `workcentres`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `workorders`
-- 

CREATE TABLE `workorders` (
  `wo` int(11) NOT NULL,
  `loccode` char(5) NOT NULL default '',
  `requiredby` date NOT NULL default '0000-00-00',
  `startdate` date NOT NULL default '0000-00-00',
  `costissued` double NOT NULL default '0',
  `closed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`wo`),
  KEY `LocCode` (`loccode`),
  KEY `StartDate` (`startdate`),
  KEY `RequiredBy` (`requiredby`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `workorders`
-- 

INSERT INTO `workorders` (`wo`, `loccode`, `requiredby`, `startdate`, `costissued`, `closed`) VALUES 
(1, 'HZ', '2009-07-07', '2009-07-07', 0, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `woserialnos`
-- 

CREATE TABLE `woserialnos` (
  `wo` int(11) NOT NULL,
  `stockid` varchar(20) NOT NULL,
  `serialno` varchar(30) NOT NULL,
  `quantity` double NOT NULL default '1',
  `qualitytext` text NOT NULL,
  PRIMARY KEY  (`wo`,`stockid`,`serialno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `woserialnos`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `www_users`
-- 

CREATE TABLE `www_users` (
  `userid` varchar(20) NOT NULL default '',
  `password` text NOT NULL,
  `realname` varchar(35) NOT NULL default '',
  `customerid` varchar(10) NOT NULL default '',
  `phone` varchar(30) NOT NULL default '',
  `email` varchar(55) default NULL,
  `defaultlocation` varchar(5) NOT NULL default '',
  `fullaccess` int(11) NOT NULL default '1',
  `lastvisitdate` datetime default NULL,
  `branchcode` varchar(10) NOT NULL default '',
  `pagesize` varchar(20) NOT NULL default 'A4',
  `modulesallowed` varchar(20) NOT NULL default '',
  `blocked` tinyint(4) NOT NULL default '0',
  `displayrecordsmax` int(11) NOT NULL default '0',
  `theme` varchar(30) NOT NULL default 'fresh',
  `language` varchar(5) NOT NULL default 'en_GB',
  PRIMARY KEY  (`userid`),
  KEY `CustomerID` (`customerid`),
  KEY `DefaultLocation` (`defaultlocation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `www_users`
-- 

INSERT INTO `www_users` (`userid`, `password`, `realname`, `customerid`, `phone`, `email`, `defaultlocation`, `fullaccess`, `lastvisitdate`, `branchcode`, `pagesize`, `modulesallowed`, `blocked`, `displayrecordsmax`, `theme`, `language`) VALUES 
('admin', 'weberp', 'Demonstration user', '', '', '', 'MEL', 8, '2009-07-09 05:07:14', '', 'A4', '1,1,1,1,1,1,1,1,', 0, 50, 'professional', 'en_GB'),
('tims', '0ddf97e70f0d8e93f5f2dbf28d61c72fd059f221', '', '', '', '', 'MEL', 6, '2009-02-06 08:57:06', '', 'A4', '1,1,1,1,1,1,1,1,', 0, 50, 'jelly', 'en_GB');
