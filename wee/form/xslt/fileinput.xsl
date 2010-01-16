<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="widget[@type='fileinput']">
	<xsl:call-template name="label">
		<xsl:with-param name="name" select="name"/>
		<xsl:with-param name="help" select="help"/>
		<xsl:with-param name="required" select="@required"/>
	</xsl:call-template>

	<xsl:text> </xsl:text>

	<input type="file">
		<xsl:if test="accept">
			<xsl:attribute name="accept">
				<xsl:value-of select="accept"/>
			</xsl:attribute>
		</xsl:if>

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

		<xsl:attribute name="name">
			<xsl:value-of select="name"/>
		</xsl:attribute>
	</input>

	<xsl:if test="errors">
		<xsl:apply-templates select="errors"/>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
