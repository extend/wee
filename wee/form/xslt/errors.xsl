<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="errors">
	<ol class="errors">
		<xsl:for-each select="error">
			<li><xsl:value-of select="."/></li>
		</xsl:for-each>
	</ol>
</xsl:template>

</xsl:stylesheet>
