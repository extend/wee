<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="widget[@type='weeFormSubmitButton']">
	<input type="submit">
		<xsl:if test="class">
			<xsl:attribute name="class">
				<xsl:value-of select="class"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="name">
			<xsl:attribute name="name">
				<xsl:value-of select="name"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="label">
			<xsl:attribute name="value">
				<xsl:value-of select="label"/>
			</xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

</xsl:stylesheet>
