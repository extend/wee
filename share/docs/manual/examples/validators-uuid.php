<?php

class UUIDValidator extends weeValidator
{
	protected function isValidInput($mValue)
	{
		return is_string($mValue) && is_valid_uuid($mValue);
	}
}
