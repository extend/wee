<?php

$oBreadcrumbs = new weeBreadcrumbsUI;
$oBreadcrumbs->setPath(array(
	'home'				=> 'Home',
	'home/news'			=> 'News',
	'home/news/today'	=> "Today's News",
));
$this->addFrame('breadcrumbs', $oBreadcrumbs);
