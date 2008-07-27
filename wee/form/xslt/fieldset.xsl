<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<!--
	these are reserved and will get matched by the container template
	matching them here means not displaying their contents
-->

<xsl:template match="class"/>
<xsl:template match="label"/>

<xsl:template match="widget[@type='fieldset']">
	<fieldset>
		<xsl:if test="class">
			<xsl:attribute name="class">
				<xsl:value-of select="class"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="label">
			<legend><xsl:value-of select="label"/></legend>
		</xsl:if>

		<xsl:call-template name="container">
			<xsl:with-param name="select" select="*"/>
		</xsl:call-template>
	</fieldset>
</xsl:template>

</xsl:stylesheet>
