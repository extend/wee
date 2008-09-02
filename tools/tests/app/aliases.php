<?php

class weeAliasesFile_testResolveAlias extends weeAliasesFile
{
	// We are testing this method so let's expose it.
	public function resolveAlias($sPathInfo)
	{
		return parent::resolveAlias($sPathInfo);
	}
}

$o = new weeAliasesFile_testResolveAlias(dirname(__FILE__) . '/aliases.cnf');

$this->isEqual('/path/info/which/is/not/an/alias', $o->resolveAlias('/path/info/which/is/not/an/alias'),
	'weeAliasesFile thinks the path info is an alias even though it is not.');

$this->isEqual('about_us', $o->resolveAlias('who_are_we'),
	'weeAliasesFile fails to resolve simple aliases.');

$this->isEqual('about_us/nox', $o->resolveAlias('who_are_we/nox'),
	'weeAliasesFile fails to resolve aliases with extra parts in the path info.');

$this->isEqual('download_wee', $o->resolveAlias('download/wee'),
	'weeAliasesFile fails to resolve multi/parts/aliases.');

$this->isEqual('article/post', $o->resolveAlias('new'),
	'weeAliasesFile fails to resolve aliases which leads to named events.');

$this->isEqual('comment/post', $o->resolveAlias('new/comment'),
	'weeAliasesFile fails to check the longest aliases first.');
