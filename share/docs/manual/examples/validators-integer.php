<?php

class IntegerValidator extends weeNumberValidator
{
	protected $aArgs = array(
		'prime'		=> false
	);

	protected $aErrors = array(
		'invalid'	=> 'Input is not an integer.',
		'prime'		=> 'Input is not prime.',
	);

	public function __construct(array $aArgs = array())
	{
		array_key_exists('prime', $aArgs) && is_bool($aArgs['prime'])
			or burn('DomainException', 'The `prime` argument should be a boolean.');

		parent::__construct($aArgs);
	}

	protected function isValidInput($mValue)
	{
		return is_int($mValue);
	}

	protected function validate()
	{
		if ($this->isValidInput($this->mValue))
			return $this->setError('invalid');

		if ($this->aArgs['prime'])
			if (!is_prime($this->mValue))
				return $this->setError('prime');
	}
}
