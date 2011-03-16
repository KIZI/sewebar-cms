<?xml version="1.0" encoding="UTF-8"?>
<!--
    BKEF 1.0 ~ XSLT 1.0
    Background knowledge - visualisation of data matrix, particular attributes and their relationships
    
    Author: Daniel Stastny
            daniel@realmind.org
    Date:   06/2009
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns="http://www.w3.org/1999/xhtml" xmlns:ds="http://keg.vse.cz/bkef" version="1.0">

    <!-- **** IMPORTANT SETTINGS **** -->

    <!-- Webpage shall be in XHTML 1.0 Strict -->
    <xsl:output method="xml" encoding="UTF-8"/>

    <!-- URL to images -->
    <xsl:param name="image-url" select="'components/com_bkef/influences/img/'"/>
    <xsl:param name="lang">en</xsl:param>
    <!-- INFLUENCES -->
    <!-- Specific realisations of attribute influences  -->
    <xsl:variable name="someInfluence" select="'Some-influence'"/>
    <xsl:variable name="positiveInfluence" select="'Positive-growth'"/>
    <xsl:variable name="negativeInfluence" select="'Negative-growth'"/>
    <xsl:variable name="positiveFrequency" select="'Positive-bool-growth'"/>
    <xsl:variable name="negativeFrequency" select="'Negative-bool-growth'"/>
    <xsl:variable name="positiveBoolean" select="'Double-bool-growth'"/>
    <xsl:variable name="negativeBoolean" select="'Negative boolean'"/>
    <xsl:variable name="functional" select="'Functional'"/>
    <xsl:variable name="none" select="'None'"/>
    <xsl:variable name="doNotCare" select="'Uninteresting'"/>
    <xsl:variable name="unknown" select="'Unknown'"/>
    <xsl:variable name="notSet" select="'Not Set'"/>

    <!--
        ******************************
              Mutual Influences
        ******************************
    -->

    <xsl:template match="/">
        <table class="matrix">
            <form id="formularInfluences"
                action="index.php?option=com_bkef&amp;task=zpracujInfluences" method="post">
                <tr>
                    <td class="attr">
                        <em>
                            <xsl:value-of
                                select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Attributes']"
                            />
                        </em>
                    </td>

                    <!-- 1st ROW of matrix // Generating of matrix with use of the list of meta-attributes -->
                    <xsl:for-each
                        select="//ds:MetaAttribute[@name and @id and not(@role) and @level = 0]">
                        <xsl:choose>
                            <xsl:when
                                test="current()/parent::*/ds:MetaAttribute[@level = 1]/ds:ChildMetaAttribute[current()/@id = @id]/parent::*/@name">
                                <xsl:variable name="metaAttribute2"
                                    select="current()/parent::*/ds:MetaAttribute[@level = 1]/ds:ChildMetaAttribute[current()/@id = @id]/parent::*/@name"/>
                                <td class="attr" name="{$metaAttribute2}">
                                    <xsl:value-of select="current()/@name"/>
                                    <br/>
                                </td>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:variable name="metaAttribute3s" select="current()/@name"/>
                                <td class="attr" name="{$metaAttribute3s}">
                                    <xsl:value-of select="current()/@name"/>
                                    <br/>
                                </td>
                            </xsl:otherwise>
                        </xsl:choose>

                    </xsl:for-each>
                </tr>
                <!-- OUTER ITERATION -->
                <xsl:for-each
                    select="//ds:MetaAttribute[@name and @id and not(@role) and @level = 0]">
                    <xsl:choose>
                        <xsl:when
                            test="current()/parent::*/ds:MetaAttribute[@level = 1]/ds:ChildMetaAttribute[current()/@id = @id]/parent::*/@name">
                            <xsl:variable name="metaAttribute11"
                                select="current()/parent::*/ds:MetaAttribute[@level = 1]/ds:ChildMetaAttribute[current()/@id = @id]/parent::*/@name"/>
                            <tr name="{$metaAttribute11}">
                                <td class="attr">
                                    <xsl:value-of select="current()/@name"/>

                                </td>

                                <!-- 1st VAR // Actual value from 1st iteration block -->
                                <xsl:variable name="attribute" select="current()/@name"/>

                                <!-- INNER ITERATION -->
                                <xsl:for-each
                                    select="//ds:MetaAttribute[@name and @id and not(@role) and @level = 0]">

                                    <!-- 2nd VAR // Actual value from 2nd iteration block -->
                                    <xsl:variable name="attributeII" select="current()/@name"/>

                                    <xsl:choose>
                                        <xsl:when
                                            test="//ds:Influence/ds:MetaAttribute[@role = 'A' and @name = $attribute] and //ds:Influence/ds:MetaAttribute[@role = 'B' and @name = $attributeII]">
                                            <xsl:variable name="selectAttribute"
                                                select="//ds:Influence
                                    [child::ds:MetaAttribute[@role = 'A' and @name = $attribute] and child::ds:MetaAttribute[@role = 'B' and @name = $attributeII]]
                                    /@type"/>

                                            <xsl:choose>

                                                <!-- THERE CAN BE SOME UNDEFINED INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $someInfluence">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'someInfluence'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- POSITIVE GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $positiveInfluence">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'positiveInfluence'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NEGATIVE GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $negativeInfluence">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'negativeInfluence'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- POSITIVE BOOLEAN GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $positiveFrequency">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'positiveFrequency'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NEGATIVE BOOLEAN GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $negativeFrequency">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'negativeFrequency'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- POSITIVE DOUBLE BOOLEAN -->
                                                <xsl:when test="$selectAttribute = $positiveBoolean">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'positiveBoolean'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NEGATIVE BOOLEAN - JUST PREPARED FOR USAGE -->
                                                <xsl:when test="$selectAttribute = $negativeBoolean">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'negativeBoolean'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- FUNCTIONAL INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $functional">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'functional'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NONE INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $none">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName" select="'none'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- WE ARE NOT INTERESTED ABOUT INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $doNotCare">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'doNotCare'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- UNKNOWN -->
                                                <xsl:when test="$selectAttribute = $unknown">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName" select="'unknown'"
                                                  />
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- "Gray" field - intersection of same attributes -->
                                                <xsl:when test="$attribute = $attributeII">
                                                  <td class="cannotMatch" name="{$attributeII}">
                                                  <p class="displayNone">XXX</p>
                                                  </td>
                                                </xsl:when>

                                                <!-- Not set -->
                                                <xsl:otherwise>
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="imgName" select="'notSet'"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  </xsl:call-template>
                                                </xsl:otherwise>

                                            </xsl:choose>
                                        </xsl:when>

                                        <!-- Intersection of the same attributes -->
                                        <xsl:when test="$attribute = $attributeII">
                                            <td class="cannotMatch">
                                                <p class="displayNone">XXX</p>
                                            </td>
                                        </xsl:when>

                                        <!-- The cell value is not set -->
                                        <xsl:otherwise>
                                            <xsl:call-template name="ds:Cell">
                                                <xsl:with-param name="attribute" select="$attribute"/>
                                                <xsl:with-param name="selectAttribute"
                                                  select="'Not Set'"/>
                                                <xsl:with-param name="imgName" select="'notSet'"/>
                                                <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                            </xsl:call-template>
                                        </xsl:otherwise>

                                    </xsl:choose>
                                </xsl:for-each>
                            </tr>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:variable name="metaAttribute111" select="current()/@name"/>
                            <tr name="{$metaAttribute111}">
                                <td class="attr">
                                    <xsl:value-of select="current()/@name"/>

                                </td>

                                <!-- 1st VAR // Actual value from 1st iteration block -->
                                <xsl:variable name="attribute" select="current()/@name"/>

                                <!-- INNER ITERATION -->
                                <xsl:for-each
                                    select="//ds:MetaAttribute[@name and @id and not(@role) and @level = 0]">

                                    <!-- 2nd VAR // Actual value from 2nd iteration block -->
                                    <xsl:variable name="attributeII" select="current()/@name"/>

                                    <xsl:choose>
                                        <xsl:when
                                            test="//ds:Influence/ds:MetaAttribute[@role = 'A' and @name = $attribute] and //ds:Influence/ds:MetaAttribute[@role = 'B' and @name = $attributeII]">
                                            <xsl:variable name="selectAttribute"
                                                select="//ds:Influence
                                    [child::ds:MetaAttribute[@role = 'A' and @name = $attribute] and child::ds:MetaAttribute[@role = 'B' and @name = $attributeII]]
                                    /@type"/>

                                            <xsl:choose>

                                                <!-- THERE CAN BE SOME UNDEFINED INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $someInfluence">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'someInfluence'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- POSITIVE GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $positiveInfluence">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'positiveInfluence'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NEGATIVE GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $negativeInfluence">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'negativeInfluence'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- POSITIVE BOOLEAN GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $positiveFrequency">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'positiveFrequency'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NEGATIVE BOOLEAN GROWTH -->
                                                <xsl:when
                                                  test="$selectAttribute = $negativeFrequency">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'negativeFrequency'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- POSITIVE DOUBLE BOOLEAN -->
                                                <xsl:when test="$selectAttribute = $positiveBoolean">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'positiveBoolean'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NEGATIVE BOOLEAN - JUST PREPARED FOR USAGE -->
                                                <xsl:when test="$selectAttribute = $negativeBoolean">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'negativeBoolean'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- FUNCTIONAL INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $functional">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'functional'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- NONE INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $none">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName" select="'none'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- WE ARE NOT INTERESTED ABOUT INFLUENCE -->
                                                <xsl:when test="$selectAttribute = $doNotCare">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName"
                                                  select="'doNotCare'"/>
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- UNKNOWN -->
                                                <xsl:when test="$selectAttribute = $unknown">
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  <xsl:with-param name="imgName" select="'unknown'"
                                                  />
                                                  </xsl:call-template>
                                                </xsl:when>

                                                <!-- "Gray" field - intersection of same attributes -->
                                                <xsl:when test="$attribute = $attributeII">
                                                  <td class="cannotMatch" name="{$attributeII}">
                                                  <p class="displayNone">XXX</p>
                                                  </td>
                                                </xsl:when>

                                                <!-- Not set -->
                                                <xsl:otherwise>
                                                  <xsl:call-template name="ds:Cell">
                                                  <xsl:with-param name="selectAttribute"
                                                  select="$selectAttribute"/>
                                                  <xsl:with-param name="attribute"
                                                  select="$attribute"/>
                                                  <xsl:with-param name="imgName" select="'notSet'"/>
                                                  <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                                  </xsl:call-template>
                                                </xsl:otherwise>

                                            </xsl:choose>
                                        </xsl:when>

                                        <!-- Intersection of the same attributes -->
                                        <xsl:when test="$attribute = $attributeII">
                                            <td class="cannotMatch">
                                                <p class="displayNone">XXX</p>
                                            </td>
                                        </xsl:when>

                                        <!-- The cell value is not set -->
                                        <xsl:otherwise>
                                            <xsl:call-template name="ds:Cell">
                                                <xsl:with-param name="attribute" select="$attribute"/>
                                                <xsl:with-param name="selectAttribute"
                                                  select="'Not Set'"/>
                                                <xsl:with-param name="imgName" select="'notSet'"/>
                                                <xsl:with-param name="attributeII"
                                                  select="$attributeII"/>
                                            </xsl:call-template>
                                        </xsl:otherwise>

                                    </xsl:choose>
                                </xsl:for-each>
                            </tr>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:variable name="metaAttribute1" select="current()/@name"/>

                </xsl:for-each>
            </form>
        </table>

    </xsl:template>


    <!-- **** CELL **** -->
    <!-- Cell is valid only for interstection of two attributes. Therefore it has only two dimensions -->
    <xsl:template name="ds:Cell">
        <xsl:param name="selectAttribute"/>
        <xsl:param name="attribute"/>
        <xsl:param name="attributeII"/>
        <xsl:param name="imgName"/>
        <xsl:param name="idBordel"/>
        <td>
            <img src="{$image-url}{$imgName}.png" alt="{$selectAttribute}"
                metaAttributeI="{$attribute}" metaAttributeII="{$attributeII}" name="obrazekObr"
                onclick="spust(this,'content')"/>
        </td>
    </xsl:template>


</xsl:stylesheet>
