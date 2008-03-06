<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="widget[@type='weeFormHidden']">
	<input type="hidden">
		<xsl:if test="class">
			<xsl:attribute name="class">
				<xsl:value-of select="class"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="help">
			<xsl:attribute name="title">
				<xsl:value-of select="help"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:attribute name="id">
			<xsl:value-of select="concat($formidprefix, name, $formidsuffix)"/>
		</xsl:attribute>

		<xsl:if test="name">
			<xsl:attribute name="name">
				<xsl:value-of select="name"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="value">
			<xsl:attribute name="value">
				<xsl:value-of select="value"/>
			</xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

</xsl:stylesheet>
