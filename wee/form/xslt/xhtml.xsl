<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xhtml" version="1.0">

<xsl:template match="a|abbr|acronym|address|area|b|bdo|big|blockquote
					|br|caption|cite|code|col|colgroup|dd|del
					|div|dfn|dl|dt|em|h1|h2|h3|h4|h5|h6|hr|i
					|img|ins|kbd|li|map|noscript|object|ol|p
					|param|pre|q|samp|small|span|strong|sub|sup
					|table|tbody|td|tfoot|th|thead|tr|tt|ul|var">
	<xsl:copy>
		<xsl:copy-of select="@*"/>
		<xsl:apply-templates/>
	</xsl:copy>
</xsl:template>

</xsl:stylesheet>
