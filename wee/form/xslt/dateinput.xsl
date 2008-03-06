<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="php xhtml" version="1.0">

<xsl:template match="widget[@type='weeFormDateInput']">
	<!-- validate node -->

	<xsl:if test="date-format and string-length(date-format) != 4">
		<xsl:message terminate="yes">
			weeFormDateInput's property date-format must be a 4 character long string.
		</xsl:message>
	</xsl:if>

	<!-- transform node -->

	<xsl:call-template name="label">
		<xsl:with-param name="name" select="name"/>
		<xsl:with-param name="help" select="help"/>
	</xsl:call-template>

	<xsl:text> </xsl:text>

	<input type="text">
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="class">
					<xsl:value-of select="class"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>dateinput</xsl:text>
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

		<xsl:attribute name="maxlength">
			<xsl:value-of select="10"/>
		</xsl:attribute>

		<xsl:if test="name">
			<xsl:attribute name="name">
				<xsl:value-of select="name"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="@readonly">
			<xsl:attribute name="readonly">
				<xsl:text>readonly</xsl:text>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="value">
			<xsl:attribute name="value">
				<!-- If no date-format is defined, only MDY/ will be passed;
					 else a 8 character long string will be passed.
					 Its 4 last characters, MDY/, will be ignored by the function. -->
				<xsl:value-of select="php:function('getLocalizedDate', concat(date-format, 'DMY/'), string(value))"/>
			</xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

</xsl:stylesheet>
