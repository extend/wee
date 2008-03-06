<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template name="weeFormRadioBoxItem">
	<xsl:param name="radioboxid"/>
	<xsl:param name="radioboxname"/>

	<li><label>
		<xsl:attribute name="for">
			<xsl:value-of select="concat($radioboxid, '_', position())"/>
		</xsl:attribute>

		<xsl:if test="@help">
			<xsl:attribute name="title">
				<xsl:value-of select="@help"/>
			</xsl:attribute>
		</xsl:if>

		<input type="radio">
			<xsl:if test="@selected">
				<xsl:attribute name="checked">
					<xsl:text>checked</xsl:text>
				</xsl:attribute>
			</xsl:if>

			<xsl:if test="@disabled">
				<xsl:attribute name="disabled">
					<xsl:text>disabled</xsl:text>
				</xsl:attribute>
			</xsl:if>

			<xsl:attribute name="id">
				<xsl:value-of select="concat($radioboxid, '_', position())"/>
			</xsl:attribute>

			<xsl:attribute name="name">
				<xsl:value-of select="$radioboxname"/>
			</xsl:attribute>

			<xsl:attribute name="value">
				<xsl:value-of select="@value"/>
			</xsl:attribute>
		</input>

		<xsl:value-of select="@label"/>
	</label></li>
</xsl:template>

<xsl:template name="weeFormRadioBoxGroup">
	<xsl:param name="radioboxid"/>
	<xsl:param name="radioboxname"/>

	<li class="group">
		<label>
			<xsl:value-of select="@label"/>
		</label>

		<ol>
			<xsl:for-each select="*">
				<xsl:call-template name="weeFormRadioBoxOptions">
					<xsl:with-param name="radioboxid" select="concat($radioboxid, '_', position())"/>
					<xsl:with-param name="radioboxname" select="$radioboxname"/>
				</xsl:call-template>
			</xsl:for-each>
		</ol>
	</li>
</xsl:template>

<xsl:template name="weeFormRadioBoxOptions">
	<xsl:param name="radioboxid"/>
	<xsl:param name="radioboxname"/>

	<xsl:choose>
		<xsl:when test="self::group">
			<xsl:call-template name="weeFormRadioBoxGroup">
				<xsl:with-param name="radioboxid" select="$radioboxid"/>
				<xsl:with-param name="radioboxname" select="$radioboxname"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="weeFormRadioBoxItem">
				<xsl:with-param name="radioboxid" select="$radioboxid"/>
				<xsl:with-param name="radioboxname" select="$radioboxname"/>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="widget[@type='weeFormRadioBox']">
	<xsl:variable name="radioboxid" select="concat($formidprefix, name, $formidsuffix)"/>

	<fieldset>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="class">
					<xsl:value-of select="class"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>radiobox</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>

		<xsl:attribute name="id">
			<xsl:value-of select="concat($formidprefix, name, $formidsuffix)"/>
		</xsl:attribute>

		<legend>
			<xsl:value-of select="label"/>
		</legend>

		<ol>
			<xsl:for-each select="options/*">
				<xsl:call-template name="weeFormRadioBoxOptions">
					<xsl:with-param name="radioboxid" select="$radioboxid"/>
					<xsl:with-param name="radioboxname" select="../../name"/>
				</xsl:call-template>
			</xsl:for-each>
		</ol>
	</fieldset>
</xsl:template>

</xsl:stylesheet>
