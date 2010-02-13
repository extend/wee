<?php

class myClassName // [extends myParentClass[, ...]] [implements myInterface[, ...]]
{
	/**
		DocComment
	*/

	const MY_CONSTANT = 42;

	// more constants...

	/**
		DocComment
	*/

	public /* OR protected OR private */ $iMyInt = 42;

	// more properties...

	/**
		DocComment
	*/

	public /* OR protected OR private [static] */ function myMethod(/* [parameters] */)
	{
		return 42;
	}

	// more methods...
}
