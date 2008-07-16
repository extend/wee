<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template name="checklistitem">
	<xsl:param name="checklistid"/>
	<xsl:param name="checklistname"/>

	<label>
		<xsl:attribute name="for">
			<xsl:value-of select="concat($checklistid, '_', position())"/>
		</xsl:attribute>

		<xsl:if test="@help">
			<xsl:attribute name="title">
				<xsl:value-of select="@help"/>
			</xsl:attribute>
		</xsl:if>

		<input type="checkbox">
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
				<xsl:value-of select="concat($checklistid, '_', position())"/>
			</xsl:attribute>

			<xsl:attribute name="name">
				<xsl:value-of select="$checklistname"/>
				<xsl:text>[]</xsl:text>
			</xsl:attribute>

			<xsl:attribute name="value">
				<xsl:value-of select="@value"/>
			</xsl:attribute>
		</input>

		<xsl:text> </xsl:text>
		<xsl:value-of select="@label"/>
	</label>
</xsl:template>

<xsl:template match="widget[@type='checklist']">
	<xsl:variable name="checklistid" select="concat($formidprefix, name, $formidsuffix)"/>

	<fieldset>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="class">
					<xsl:value-of select="class"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>checklist</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>

		<xsl:attribute name="id">
			<xsl:value-of select="$checklistid"/>
		</xsl:attribute>

		<legend>
			<xsl:value-of select="label"/>
		</legend>

		<ol>
			<xsl:for-each select="options/*">
				<li>
					<xsl:call-template name="checklistitem">
						<xsl:with-param name="checklistid" select="$checklistid"/>
						<xsl:with-param name="checklistname" select="../../name"/>
					</xsl:call-template>
				</li>
			</xsl:for-each>
		</ol>
	</fieldset>
</xsl:template>

</xsl:stylesheet>
