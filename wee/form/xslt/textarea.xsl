<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="widget[@type='textarea']">
	<xsl:call-template name="label">
		<xsl:with-param name="name" select="name"/>
		<xsl:with-param name="help" select="help"/>
		<xsl:with-param name="required" select="@required"/>
	</xsl:call-template>

	<xsl:text> </xsl:text>

	<textarea>
		<xsl:if test="class">
			<xsl:attribute name="class">
				<xsl:value-of select="class"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:attribute name="cols">
			<xsl:choose>
				<xsl:when test="cols">
					<xsl:value-of select="cols"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>35</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>

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

		<xsl:if test="name">
			<xsl:attribute name="name">
				<xsl:value-of select="name"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:attribute name="rows">
			<xsl:choose>
				<xsl:when test="rows">
					<xsl:value-of select="rows"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>4</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>

		<xsl:comment/>
		<xsl:value-of select="value"/>
	</textarea>

	<xsl:if test="errors">
		<xsl:apply-templates select="errors"/>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
