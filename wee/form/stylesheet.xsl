<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<?php foreach ($oWeeStylesheets as $oFile): if ($oFile->isFile() && substr($oFile->getFilename(), 0, 1) != '.'):?> 
	<xsl:import href="<?php echo str_replace('\\', '/', $oFile->getPathname())?>"/>
<?php endif; endforeach?> 

<?php foreach ($oUserStylesheets as $oFile): if ($oFile->isFile() && substr($oFile->getFilename(), 0, 1) != '.'):?> 
	<xsl:include href="<?php echo str_replace('\\', '/', $oFile->getPathname())?>"/>
<?php endif; endforeach?> 

<xsl:output method="xml" omit-xml-declaration="yes" encoding="<?php echo $this->sOutputEncoding?>"/>

<xsl:variable name="formidprefix" select="'form_'"/>
<xsl:variable name="formidsuffix"/>
<xsl:variable name="formkey"<?php if (!empty($sFormKey)):?> select="'<?php echo $sFormKey?>'"<?php endif?>/>

</xsl:stylesheet>
