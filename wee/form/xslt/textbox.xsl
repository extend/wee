<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="widget[@type='weeFormTextBox']">
	<xsl:call-template name="label">
		<xsl:with-param name="name" select="name"/>
		<xsl:with-param name="help" select="help"/>
	</xsl:call-template>

	<xsl:text> </xsl:text>

	<input type="text">
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

		<xsl:if test="@disabled">
			<xsl:attribute name="disabled">
				<xsl:text>disabled</xsl:text>
			</xsl:attribute>
		</xsl:if>

		<xsl:attribute name="id">
			<xsl:value-of select="concat($formidprefix, name, $formidsuffix)"/>
		</xsl:attribute>

		<xsl:if test="validator[@type='weeStringValidator']/@max">
			<xsl:attribute name="maxlength">
				<xsl:value-of select="validator[@type='weeStringValidator']/@max"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:attribute name="name">
			<xsl:value-of select="name"/>
		</xsl:attribute>

		<xsl:if test="@readonly">
			<xsl:attribute name="readonly">
				<xsl:text>readonly</xsl:text>
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
