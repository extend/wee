<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template name="container">
	<xsl:param name="select"/>

	<ol>
		<xsl:for-each select="$select">
			<li>
				<xsl:apply-templates select="."/>
			</li>
		</xsl:for-each>
	</ol>
</xsl:template>

</xsl:stylesheet>
