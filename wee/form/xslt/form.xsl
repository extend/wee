<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="form">
	<form>
		<xsl:attribute name="action">
			<xsl:value-of select="uri"/>
		</xsl:attribute>

		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="class">
					<xsl:value-of select="class"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>block</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>

		<xsl:if test="enctype">
			<xsl:attribute name="enctype">
				<xsl:value-of select="enctype"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="id">
			<xsl:attribute name="id">
				<xsl:value-of select="id"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:attribute name="method">
			<xsl:value-of select="method"/>
		</xsl:attribute>

		<xsl:if test="$formkey">
			<input type="hidden" name="wee_formkey">
				<xsl:attribute name="value">
					<xsl:value-of select="$formkey"/>
				</xsl:attribute>
			</input>
		</xsl:if>

		<xsl:call-template name="container">
			<xsl:with-param name="select" select="widgets/*"/>
		</xsl:call-template>
	</form>
</xsl:template>

</xsl:stylesheet>
