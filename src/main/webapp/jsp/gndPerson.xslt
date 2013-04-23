<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:xs="http://www.w3.org/2001/XMLSchema" 
version="2.0">

  <xsl:template match="/">
    <html>
    <head>
    <script src="http://alkyoneus.hbz-nrw.de/dev/portal_javascripts/DiPPThemeNG/jquery.js" type="text/javascript"></script>
    <script src="http://alkyoneus.hbz-nrw.de/dev/gndrequest.js" type="text/javascript"></script>
    </head>
      <body>
	<xsl:choose>
	<xsl:when test="//resultSize=0"> 
		<h1>Die Suche in der GND ergab keine Treffer</h1>
		<p>Bitte versuchen Sie eine Suche direkt bei der DNB. 
		Verwenden Sie dazu diesen Link: <br />
		<xsl:element name="a">
		  <xsl:attribute name="href">
		    http://www.d-nb.de/<xsl:value-of select="prefferedName"/> 
		  </xsl:attribute>
		  <xsl:attribute name="alt">
		    Person direkt auf den Seiten der DNB suchen  
		  </xsl:attribute>
		  <xsl:attribute name="title">
		    Person direkt auf den Seiten der DNB suchen  
		  </xsl:attribute>
		Person direkt in der PND suchen
		</xsl:element>
		</p>
	</xsl:when>
	<xsl:otherwise>
	<h1><xsl:value-of select="//resultSize"/> Einträge in der GND gefunden</h1>
	  <xsl:for-each select="//result">
	  <xsl:element name="div">
	    <xsl:variable name="count"><xsl:value-of select="position()" /></xsl:variable>
	    <xsl:variable name="forename"><xsl:value-of select="substring-before(prefferedName,',')" /></xsl:variable>
	    <xsl:variable name="lastname"><xsl:value-of select="substring-after(prefferedName,', ')" /></xsl:variable>
	    <xsl:attribute name="id">Result<xsl:value-of select="position()" /></xsl:attribute>
	    <h3>
		<xsl:element name="a">
		  <xsl:attribute name="href">
		    javascript:void(null);
		  </xsl:attribute>
		  <xsl:attribute name="id">apkn</xsl:attribute>
		  <xsl:attribute name="onClick">
		    sendBackToParentWindow(document.getElementById('Result<xsl:value-of select="$count" />' ));
		  </xsl:attribute>
		  <xsl:attribute name="alt">
		    Identifier und Daten dieser Person in das Formular einfügen  
		  </xsl:attribute>
		  <xsl:attribute name="title">
		    Identifier und Daten dieser Person in das Formular einfügen  
		  </xsl:attribute>
		<xsl:value-of select="prefferedName"/>
		</xsl:element>
	      </h3>
	      <div style="display:none;">
	        <ul>
		    <li><xsl:value-of select="$forename" /></li>
		    <li><xsl:value-of select="$lastname" /></li>
		    <li><xsl:value-of select="pndID" /></li>
   		    <xsl:if test="acadTitle">
		    	<li><xsl:value-of select="acadTitle" /></li>
		    </xsl:if>
	        </ul>
	      </div>
	    <ul>
	      <li><em>GND Personen Identifier:</em> 
		<xsl:element name="a">
		  <xsl:attribute name="href">
		    javascript:void(null);
		  </xsl:attribute>
		  <xsl:attribute name="id">apkn</xsl:attribute>
		  <xsl:attribute name="onClick">
		    sendBackToParentWindow(document.getElementById('Result<xsl:value-of select="$count" />' ));
		  </xsl:attribute>
		  <xsl:attribute name="alt">
		    Identifier und Daten dieser Person in das Formular einfügen  
		  </xsl:attribute>
		  <xsl:attribute name="title">
		    Identifier und Daten dieser Person in das Formular einfügen  
		  </xsl:attribute>
		<xsl:value-of select="pndID"/>
		</xsl:element>
	      </li>
	      <li><em>PND Eintrag bei der DNB: </em> 
		<xsl:element name="a">
		  <xsl:attribute name="href">
		    <xsl:value-of select="pndUri"/>
		  </xsl:attribute>
		  <xsl:attribute name="target">
		    _blank
		  </xsl:attribute>
		<xsl:value-of select="prefferedName"/>
		</xsl:element>
	      </li>
	      <xsl:if test="yearOfBirth">
		<li><em>Geburtsjahr:</em> <xsl:value-of select="yearOfBirth"/></li>
	      </xsl:if>
	      <xsl:if test="biograficData">
	      <li><em>Biografische Informationen:</em> <xsl:value-of select="biograficData"/></li>
	      </xsl:if>
	      <xsl:if test="wpUrl">
		<li><em>Wikipedia Eintrag:</em> 
		  <xsl:element name="a">
		    <xsl:attribute name="href">
		      <xsl:value-of select="wpUrl"/>
		    </xsl:attribute>
		    <xsl:attribute name="target">
		    _blank
		    </xsl:attribute>
		  <xsl:value-of select="wpUrl"/>
		</xsl:element>
	      </li>
	      </xsl:if>
	     </ul>
	  </xsl:element>
	</xsl:for-each>
	</xsl:otherwise>
	</xsl:choose>
      </body>
    </html>
  </xsl:template>

 
</xsl:stylesheet>
