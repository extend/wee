<?php

require(ROOT_PATH . 'tools/tests/db/mysql/connect.php.inc');

if (!class_exists('myDbScaffoldSetProfile')) {
	class myDbScaffoldSetProfile extends weeDbSetScaffold
	{
		protected $sTableName = 'dbscaffoldjoinprofile';
	}

	class myDbScaffoldSetRank extends weeDbSetScaffold
	{
		protected $sTableName = 'dbscaffoldjoinrank';
	}

	class myDbScaffoldSetCountry extends weeDbSetScaffold
	{
		protected $sTableName = 'dbscaffoldjoincountry';
	}

	class myDbScaffoldModelJoin extends weeDbModelScaffold
	{
		protected $sSet = 'myDbScaffoldSetJoin';
	}

	class myDbScaffoldSetJoin extends weeDbSetScaffold
	{
		protected $sModel = 'myDbScaffoldModelJoin';
		protected $aRefSets = array(
			'myDbScaffoldSetProfile',
			array('set' => 'myDbScaffoldSetRank'),
			array('set' => 'myDbScaffoldSetCountry', 'key' => array('country' => 'country_id', 'year' => 'country_year')),
		);
		protected $sTableName = 'dbscaffoldjoin';

		// Making a few things public for testing

		public $sJoinType = 'LEFT OUTER JOIN';
		public $sOrderBy;

		public function buildJoin($aMeta)
		{
			return parent::buildJoin($aMeta);
		}
	}
}

try {
	$oDb->query('CREATE TABLE IF NOT EXISTS dbscaffoldjoin (pkey integer, profile_id integer, rank_id integer, rank_type integer, country integer, year integer, PRIMARY KEY (pkey))');
	$oDb->query('CREATE TABLE IF NOT EXISTS dbscaffoldjoinprofile (profile_id integer, profile_label varchar(50), PRIMARY KEY (profile_id))');
	$oDb->query('CREATE TABLE IF NOT EXISTS dbscaffoldjoinrank (rank_id integer, rank_type integer, rank_label varchar(50), PRIMARY KEY (rank_id, rank_type))');
	$oDb->query('CREATE TABLE IF NOT EXISTS dbscaffoldjoincountry (country_id integer, country_year integer, country_label varchar(50), PRIMARY KEY (country_id, country_year))');

	$oDb->query('INSERT INTO dbscaffoldjoin VALUES (1, 1, 2, 1, 3, 2008)');
	$oDb->query('INSERT INTO dbscaffoldjoin VALUES (2, 2, 1, 1, 6, 2008)');
	$oDb->query('INSERT INTO dbscaffoldjoin VALUES (3, 1, 2, 1, 3, 2007)');
	$oDb->query('INSERT INTO dbscaffoldjoin VALUES (4, 4, 3, 2, 6, 2007)');
	$oDb->query('INSERT INTO dbscaffoldjoin VALUES (5, 4, 1, 2, 2, 2008)');
	$oDb->query('INSERT INTO dbscaffoldjoin VALUES (6, NULL, NULL, NULL, NULL, NULL)');

	$oDb->query("INSERT INTO dbscaffoldjoinprofile VALUES (1, 'Administrator')");
	$oDb->query("INSERT INTO dbscaffoldjoinprofile VALUES (2, 'Moderator')");
	$oDb->query("INSERT INTO dbscaffoldjoinprofile VALUES (3, 'User')");
	$oDb->query("INSERT INTO dbscaffoldjoinprofile VALUES (4, 'Guest')");

	$oDb->query("INSERT INTO dbscaffoldjoinrank VALUES (1, 1, 'Rank 1-1')");
	$oDb->query("INSERT INTO dbscaffoldjoinrank VALUES (2, 1, 'Rank 2-1')");
	$oDb->query("INSERT INTO dbscaffoldjoinrank VALUES (3, 1, 'Rank 3-1')");
	$oDb->query("INSERT INTO dbscaffoldjoinrank VALUES (1, 2, 'Rank 1-2')");
	$oDb->query("INSERT INTO dbscaffoldjoinrank VALUES (2, 2, 'Rank 2-2')");
	$oDb->query("INSERT INTO dbscaffoldjoinrank VALUES (3, 2, 'Rank 3-2')");

	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (1, 2007, 'France')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (2, 2007, 'Japan')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (3, 2007, 'Moon')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (4, 2007, 'Mars')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (5, 2007, 'Neptune')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (6, 2007, 'Other')");

	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (1, 2008, 'France')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (2, 2008, 'Japan')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (3, 2008, 'Moon')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (4, 2008, 'Mars')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (5, 2008, 'Neptune')");
	$oDb->query("INSERT INTO dbscaffoldjoincountry VALUES (6, 2008, 'Other')");

	$oSet			= new myDbScaffoldSetJoin;
	$oProfileSet	= new myDbScaffoldSetProfile;
	$oRankSet		= new myDbScaffoldSetRank;
	$oCountrySet	= new myDbScaffoldSetCountry;

	$oSet->setDb($oDb);
	$oProfileSet->setDb($oDb);
	$oRankSet->setDb($oDb);
	$oCountrySet->setDb($oDb);

	$aMeta			= $oSet->getMeta();
	$aProfileMeta	= $oProfileSet->getMeta();
	$aRankMeta		= $oRankSet->getMeta();
	$aCountryMeta	= $oCountrySet->getMeta();

	// weeDbSetScaffold::buildJoin

	$this->isEqual(
		' LEFT OUTER JOIN ' . $aProfileMeta['table'] . ' ON (' . $aMeta['table'] . '.`profile_id`=' . $aProfileMeta['table'] . '.`profile_id`)' . 
		' LEFT OUTER JOIN ' . $aRankMeta['table'] . ' ON (' . $aMeta['table'] . '.`rank_id`=' . $aRankMeta['table'] . '.`rank_id` AND ' . $aMeta['table'] . '.`rank_type`=' . $aRankMeta['table'] . '.`rank_type`)' . 
		' LEFT OUTER JOIN ' . $aCountryMeta['table'] . ' ON (' . $aMeta['table'] . '.`country`=' . $aCountryMeta['table'] . '.`country_id` AND ' . $aMeta['table'] . '.`year`=' . $aCountryMeta['table'] . '.`country_year`)',
		$oSet->buildJoin($oSet->getMeta()),
		_WT('weeDbSetScaffold::buildJoin failed to build the correct statement.')
	);

	// weeDbSetScaffold::count

	$oSet->sJoinType = 'INNER JOIN';
	$this->isEqual(5, count($oSet), _WT('weeDbSetScaffold::count does not return the correct number of queries for INNER JOIN.'));

	$oSet->sJoinType = 'LEFT OUTER JOIN';
	$this->isEqual(6, count($oSet), _WT('weeDbSetScaffold::count does not return the correct number of queries for LEFT OUTER JOIN.'));

	// weeDbSetScaffold::fetch

	$oResult = $oSet->fetch(1);
	$this->isEqual(array(
		'pkey'			=> 1,
		'profile_id'	=> 1,
		'rank_id'		=> 2,
		'rank_type'		=> 1,
		'country'		=> 3,
		'year'			=> 2008,
		'profile_label'	=> 'Administrator',
		'rank_label'	=> 'Rank 2-1',
		'country_id'	=> 3,
		'country_year'	=> 2008,
		'country_label'	=> 'Moon',
	), $oResult->toArray(), _WT('weeDbSetScaffold::fetch returned bad data when also fetching reference tables.'));

	$oResult = $oSet->fetch(6);
	$this->isEqual(array(
		'pkey'			=> 6,
		'profile_id'	=> null,
		'rank_id'		=> null,
		'rank_type'		=> null,
		'country'		=> null,
		'year'			=> null,
		'profile_label'	=> null,
		'rank_label'	=> null,
		'country_id'	=> null,
		'country_year'	=> null,
		'country_label'	=> null,
	), $oResult->toArray(), _WT('weeDbSetScaffold::fetch returned bad data when the keys to reference tables are NULL.'));

	// weeDbSetScaffold's subsets

	$oSubset = new myDbScaffoldSetJoin(array('profile_label' => array('LIKE', '%tor')));
	$oSubset->setDb($oDb);
	$this->isEqual(3, count($oSubset),
		_WT('Using subsets with a reference table returned the wrong number of results.'));

	$oSubset = new myDbScaffoldSetJoin(array('country_label' => array('=', 'Moon')));
	$oSubset->setDb($oDb);
	$this->isEqual(2, count($oSubset),
		_WT('Using subsets with a reference table returned the wrong number of results.'));

	$oSubset = new myDbScaffoldSetJoin(array('profile_label' => array('LIKE', '%tor')));
	$oSubset->setDb($oDb);
	$oSubset = $oSubset->subsetComplementOf(array('country_label' => array('=', 'Other')));
	$this->isEqual(2, count($oSubset), _WT('The subset count method returned a wrong number of results.'));

} catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbscaffoldjoin');
$oDb->query('DROP TABLE IF EXISTS dbscaffoldjoinprofile');
$oDb->query('DROP TABLE IF EXISTS dbscaffoldjoinrank');
$oDb->query('DROP TABLE IF EXISTS dbscaffoldjoincountry');

if (isset($oException))
	throw $oException;
