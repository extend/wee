<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template name="weeFormChoiceItem">
	<option>
		<xsl:if test="@disabled">
			<xsl:attribute name="disabled">
				<xsl:text>disabled</xsl:text>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="@selected">
			<xsl:attribute name="selected">
				<xsl:text>selected</xsl:text>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="@value">
			<xsl:attribute name="value">
				<xsl:value-of select="@value"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:value-of select="@label"/>
	</option>
</xsl:template>

<xsl:template name="weeFormChoiceGroup">
	<optgroup>
		<xsl:if test="@disabled">
			<xsl:attribute name="disabled">
				<xsl:text>disabled</xsl:text>
			</xsl:attribute>
		</xsl:if>

		<xsl:attribute name="label">
			<xsl:value-of select="@label"/>
		</xsl:attribute>

		<xsl:for-each select="item">
			<xsl:call-template name="weeFormChoiceItem"/>
		</xsl:for-each>
	</optgroup>
</xsl:template>

<xsl:template match="widget[@type='weeFormChoice']">
	<xsl:call-template name="label">
		<xsl:with-param name="name" select="name"/>
		<xsl:with-param name="help" select="help"/>
	</xsl:call-template>

	<xsl:text> </xsl:text>

	<select>
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

		<xsl:if test="name">
			<xsl:attribute name="name">
				<xsl:value-of select="name"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="size">
			<xsl:attribute name="size">
				<xsl:value-of select="size"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:for-each select="options/*">
			<xsl:choose>
				<xsl:when test="self::group">
					<xsl:call-template name="weeFormChoiceGroup"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="weeFormChoiceItem"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</select>
</xsl:template>

</xsl:stylesheet>
