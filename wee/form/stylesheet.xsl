<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<?php foreach ($oWeeStylesheets as $oFile): if ($oFile->isFile()):?>
	<xsl:import href="<?php echo $oFile->getPathname()?>"/>
<?php endif; endforeach?>

<!-- TODO: include user stylesheet to override defaults -->

<xsl:output method="xml" omit-xml-declaration="yes"/>

<xsl:variable name="formidprefix" select="'form_'"/>
<xsl:variable name="formidsuffix"/>
<xsl:variable name="formkey"<?php if (!empty($sFormKey)):?> select="'<?php echo $sFormKey?>'"<?php endif?>/>

</xsl:stylesheet>
