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
	Accumulator helper to generate totals in templates.

	Allows you to store the sum of a list of numbers accessed in the template, typically in loops.
	Those sums can be stacked to create totals and subtotals as needed.

	@todo Allow other numerical values than integers.
*/

class weeAccumulator
{
	/**
		The stack of values that have been pushed so far.
	*/

	protected $aStack;

	/**
		The current values accumulated.
	*/

	protected $aCurrent;

	/**
		Add the given numeric value to an accumulator.
		If the accumulator doesn't exist, it is initialized with a value of 0.

		@param $sName Name of the accumulator.
		@param $mValue Numerical value to add. Defaults to 1.
	*/

	public function add($sName, $mValue = 1)
	{
		if (isset($this->aCurrent[$sName]))
			$this->aCurrent[$sName] += $mValue;
		else
			$this->aCurrent[$sName] = $mValue;
	}

	/**
		Return the value of an accumulator, or 0 if it doesn't exist.

		@param $sName Name of the accumulator.
		@return integer Value of the accumulator.
	*/

	public function get($sName)
	{
		if (isset($this->aCurrent[$sName]))
			return $this->aCurrent[$sName];
		return 0;
	}

	/**
		Push the current accumulators onto the stack.

		An optional stack name can be given for later use in the method "total".
		After pushing to the stack, the current accumulators are reset to 0.

		@param $sStackName Optional stack name.
	*/

	public function push($sStackName = null)
	{
		if ($sStackName === null)
			$this->aStack[] = $this->aCurrent;
		else {
			isset($this->aStack[$sStackName]) and burn('IllegalStateException',
				sprintf(_WT('The stack named "%s" already exists.'), $sStackName));
			$this->aStack[$sStackName] = $this->aCurrent;
		}

		$this->aCurrent = array();
	}

	/**
		Return the totals of the accumulators of the given stack names.
		When no argument is given, the total of all the stacked accumulators is performed.

		@param ... A list of stack names to get the total for.
		@return array The totals of all the accumulators used for the given stack names.
	*/

	public function total(/* ... */)
	{
		empty($this->aCurrent) or burn('IllegalStateException',
			_WT('You should push the current data before trying to calculate a total.'));

		if (func_num_args() != 0)
			$aStackNames = func_get_args();
		else {
			empty($this->aStack) and burn('IllegalStateException',
				_WT('The stack is empty, there is nothing to get the total for.'));

			$aStackNames = array_keys($this->aStack);
		}

		$aTotal = $this->aStack[array_pop($aStackNames)];
		foreach ($aStackNames as $sStackName)
			foreach ($this->aStack[$sStackName] as $sName => $iValue)
				$aTotal[$sName] = array_value($aTotal, $sName, 0) + $iValue;

		return $aTotal;
	}
}
