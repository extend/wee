<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ALLOW_INCLUSION')) die;

/**
	Class for Oracle prepared statements handling.

	Instances of this class are returned by weeOracleDatabase's prepare method and
	should not be instantiated manually.
*/

class weeOracleStatement extends weeDatabaseStatement
{
	/**
		The oracle statement resource.
	*/

	protected $rStatement;

	/**
		Number of affected rows for the previous query.
		Stocked here to prevent errors if getPKId is called.
	*/

	protected $iNumAffectedRows;

	/**
		The parameters of the statement.
		The keys are the names of the parameters.
		Used to filter the input of the doBind method.
	*/

	protected $aParameters = array();

	/**
		Initialise an oracle prepared statement.

		@param	$rLink	The oracle link resource.
		@param	$sQuery	The query to prepare.
		@throw	InvalidArgumentException	The resource is not a valid oracle statement.
	*/

	public function __construct($rLink, $sQuery)
	{
		is_resource($rLink) && get_resource_type($rLink) == 'oci8 connection' or burn('InvalidArgumentException',
			sprintf(_WT('The given variable must be a resource of type "%s".'), 'oci8 connection'));

		preg_match_all('/:([\w_]+)/', $sQuery, $aMatches);
		foreach ($aMatches[1] as $sParam)
			$this->aParameters[$sParam] = true;

		$this->rStatement = oci_parse($rLink, $sQuery);
		$this->rStatement !== false or burn('DatabaseException',
			sprintf(_WT("Failed to parse the query with the following error:\n%s"), array_value(oci_error($rLink), 'message')));
	}

	/**
		Does the oracle-dependent work to bind the parameters to the statement.

		@param	$aParameters	The parameters to bind.
	*/

	protected function doBind($aParameters)
	{
		foreach ($aParameters as $sName => $mValue)
			if (isset($this->aParameters[$sName]))
				// Don't use $mValue here because oci_bind_by_name binds by reference.
				oci_bind_by_name($this->rStatement, ':' . $sName, $aParameters[$sName]) or burn('DatabaseException',
					sprintf(_WT("Failed to bind the parameter \"%s\" with the following error:\n%s"), $sName, array_value(oci_error($this->rStatement), 'message')));
	}

	/**
		Executes the prepared statement.

		@return	mixed	An instance of weeDatabaseDummyResult if the query returned rows or null.
	*/

	public function execute()
	{
		// oci_execute triggers a warning when the statement could not be executed.
		@oci_execute($this->rStatement, OCI_DEFAULT) or burn('DatabaseException',
			sprintf(_WT("Failed to execute the query with the following error:\n%s"), array_value(oci_error($this->rStatement), 'message')));

		$this->iNumAffectedRows = oci_num_rows($this->rStatement);
		if (oci_num_fields($this->rStatement) > 0) {
			// TODO: Check whether the silence operator is really required here.
			@oci_fetch_all($this->rStatement, $aRows, 0, -1, OCI_ASSOC | OCI_FETCHSTATEMENT_BY_ROW);
			return new weeDatabaseDummyResult($aRows);
		}
	}

	/**
		Returns the number of affected rows in the last INSERT, UPDATE or DELETE query.
		You can't use this method safely to check if your UPDATE executed successfully,
		since the UPDATE statement does not always update rows that are already up-to-date.

		@return	int	The number of affected rows in the last query.
	*/

	public function numAffectedRows()
	{
		return $this->iNumAffectedRows;
	}
}
