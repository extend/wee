<?php

class myFrame extends weeFrame
{
	// ...

	public function defaultEvent($aEvent)
	{
		if (empty($this->oTpl))
			$this->loadTemplate();

		$this->oTpl->addLinkArgs(array(
			'location' => $oUser->getLocation(),
			'year'     => $oUser->getYearActive(),
		));
	}
}
