<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template name="container">
	<xsl:param name="select"/>

	<ol>
		<xsl:for-each select="$select">
			<xsl:choose>
				<xsl:when test="@type='hidden'">
					<li class="invisible"><xsl:apply-templates select="."/></li>
				</xsl:when>
				<xsl:otherwise>
					<li><xsl:apply-templates select="."/></li>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</ol>
</xsl:template>

</xsl:stylesheet>
