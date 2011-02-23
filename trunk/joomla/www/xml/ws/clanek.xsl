<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:template match="/clanek">
  <html>
    <head>
      <title>Clanek</title>
    </head>

    <body>
      <xsl:apply-templates/>
    </body>
  </html>
</xsl:template>
  
</xsl:stylesheet>