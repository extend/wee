<?php

protected function defaultEvent($aEvent)
{
	runEvent();

	// Replace the HTML for the contained UI frame by the template output
	if ($aEvent['context'] == 'xmlhttprequest') {
		$this->noChildTaconite();
		$this->update('replaceContent', '#' . $this->sId, $this->oTpl); // $this->sId contains the frame identifier
	}
}
