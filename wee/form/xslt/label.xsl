<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template name="label">
	<xsl:param name="name"/>
	<xsl:param name="help"/>
	<xsl:param name="required"/>

	<label>
		<xsl:attribute name="for">
			<xsl:value-of select="concat($formidprefix, name, $formidsuffix)"/>
		</xsl:attribute>

		<xsl:if test="$help">
			<xsl:attribute name="title">
				<xsl:value-of select="$help"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:value-of select="label"/>

		<xsl:if test="$required">
			<em class="required"> *</em>
		</xsl:if>
	</label>
</xsl:template>

</xsl:stylesheet>
