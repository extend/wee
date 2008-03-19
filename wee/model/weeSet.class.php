<?php

/**
	Base class for handling a set of elements.
	Elements are defined by the model associated with the set.
*/

abstract class weeSet extends weeDataSource
{
	/**
		Model associated with this set of elements.
		This set will always return elements according to this model.
	*/

	protected $sModel;

	/**
		Returns all the elements in the set.
	*/

	abstract public function fetchAll();
}
