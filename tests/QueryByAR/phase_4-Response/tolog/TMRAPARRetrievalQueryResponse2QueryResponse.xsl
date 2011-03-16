<?xml version="1.0" encoding="UTF-8"?>
<!-- Transformation for presenting the result of the OKS query -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xd="http://www.oxygenxml.com/ns/doc/xsl" version="1.0"
    xmlns:tm="http://psi.ontopia.net/xml/tm-xml/" xmlns:www.dmg.org="http://www.dmg.org/PMML-4_0#">
    <xsl:template match="/">
        <SearchResult xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="http://sewebar.vse.cz./schemas/SearchResult0_1.xsd">
            <Metadata>
                <SearchTimestamp/>
                <LastIndexUpdate/>
                <SearchAlgorithm>Ontopia</SearchAlgorithm>
                <SearchAlgorithmVersion>tolog.xsl 1/1/2011</SearchAlgorithmVersion>
            </Metadata>
            <Statistics>
                <ExecutionTime/>
                <DocumentsSearched/>
                <RulesSearched/>
            </Statistics>
            <Hits>
                <xsl:apply-templates/>
            </Hits>
        </SearchResult>
    </xsl:template>
    <xsl:template match="tm:value">
        <Hit docID="1">
            <Text><xsl:value-of select="."/></Text>
            <Details/>
        </Hit>
    </xsl:template>


</xsl:stylesheet>
