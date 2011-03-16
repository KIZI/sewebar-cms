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
    <xsl:variable name="attribute" select="''"/>
    <xsl:variable name="attributeII" select="''"/>

    <!--
        ******************************
              Mutual Influences
        ******************************
    -->

    <!-- **** CELL **** -->
    <!-- Cell is valid only for interstection of two attributes. Therefore it has only two dimensions -->
    <xsl:template match="/">
        <xsl:variable name="knowledge" select="//ds:KnowledgeValidity"/>
        <xsl:variable name="scope" select="//ds:InfluenceScope"/>

        <xsl:variable name="ids" select="generate-id()"/>
        <xsl:variable name="zavislostibkefa" select="$attribute"/>
        <xsl:variable name="zavislostibkefb" select="$attributeII"/>
        <xsl:variable name="save">
            <xsl:value-of
                select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Save']"
            />
        </xsl:variable>
        <xsl:variable name="addAnnotation">
            <xsl:value-of
                select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Add Annotation']"
            />
        </xsl:variable>


        <div id="BKEF_mainObject">
            <div id="BKEF_formatInfo">
                <xsl:for-each
                    select="//ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:RestrictedTo/ds:Format">
                    <xsl:value-of select="current()/@name" />ŧ
                </xsl:for-each>    
            </div>
            <table>
                <tr>
                    <td>
                        <img id="Not Set" src="{$image-url}notSet.png" alt="Not set" border="3"
                            onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Do Not Care" src="{$image-url}doNotCare.png" alt="Do Not Care"
                            border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Functional" src="{$image-url}functional.png" alt="Functional"
                            border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Negative boolean" src="{$image-url}negativeBoolean.png"
                            alt="Negative boolean" border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Negative Frequency" src="{$image-url}negativeFrequency.png"
                            alt="Negative Frequency" border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Negative Influence" src="{$image-url}negativeInfluence.png"
                            alt="Negative Influence" border="3" onclick="prebarvi(this);"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <img id="None" src="{$image-url}none.png" alt="None" border="3"
                            onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Positive Boolean" src="{$image-url}positiveBoolean.png"
                            alt="Positive Boolean" border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Positive Frequency" src="{$image-url}positiveFrequency.png"
                            alt="Positive Frequency" border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Positive Influence" src="{$image-url}positiveInfluence.png"
                            alt="Positive Influence" border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Some Influence" src="{$image-url}someInfluence.png"
                            alt="Some Influence" border="3" onclick="prebarvi(this);"/>
                    </td>
                    <td>
                        <img id="Unknown" src="{$image-url}unknown.png" alt="Unknown" border="3"
                            onclick="prebarvi(this);"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" id="BKEF_doplnkovytext"/>
                </tr>
                <!-- Button for saving the document and then solution for formats  -->
                <tr id="BKEF_firstButton">
                    <td colspan="6">
                        <input type="button" value="{$save}" onclick="save();"/>
                    </td>
                </tr>
                <!-- Annotations are solved here -->
                <xsl:for-each
                    select="//ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:Annotations/ds:Annotation">
                    <tr>
                        <td>Autor: </td>
                        <td colspan="5">
                            <xsl:if test="not(current()/ds:Author)">
                                <input type="text" name="annotationsAuthors" value=""/>
                            </xsl:if>
                            <xsl:if test="current()/ds:Author">
                                <xsl:variable name="autors" select="current()/ds:Author"/>
                                <input type="text" name="annotationsAuthors" value="{$autors}"/>
                            </xsl:if>
                        </td>
                    </tr>
                    <tr>
                        <td><xsl:value-of
                                select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Annotation']"
                            />: </td>
                        <td colspan="5">
                            <textarea cols="50" rows="5" name="annotationsTexts">
                                <xsl:if test="not(current()/ds:Text)"> Zde můžete zadat doplňující
                                    informace k této závislosti. </xsl:if>
                                <xsl:if test="current()/ds:Text">
                                    <xsl:value-of select="current()/ds:Text"/>
                                </xsl:if>
                            </textarea>
                        </td>
                    </tr>
                </xsl:for-each>
                <tr id="pridejAnotaci°{$ids}">
                    <td colspan="6">
                        <input type="button" value="{$addAnnotation}"
                            onclick="pridejAnotaci(this,this.getAttribute('id'));"/>
                    </td>
                </tr>
                <tr style="font-size: 25px;font-weight: bold;">
                    <td colspan="3"> A: </td>
                    <td colspan="3">
                        <xsl:value-of select="$attribute"/>
                    </td>
                </tr>
                <xsl:for-each
                    select="//ds:MetaAttributes/ds:MetaAttribute[@name = $attribute]/ds:Formats/ds:Format">
                    <xsl:variable name="ted" select="current()/@name"/>
                    <tr>
                        <td>
                            <xsl:value-of select="current()/@name"/>
                        </td>
                        <td>
                            <input type="checkbox" name="formatsA" format="{$ted}" checked="checked"
                            />
                        </td>
                        <td colspan="4"> </td>
                    </tr>
                </xsl:for-each>
                <tr style="font-size: 25px;font-weight: bold;">
                    <td colspan="3"> B: </td>
                    <td colspan="3">
                        <xsl:value-of select="$attributeII"/>
                    </td>
                </tr>
                <xsl:for-each
                    select="//ds:MetaAttributes/ds:MetaAttribute[@name = $attributeII]/ds:Formats/ds:Format">
                    <xsl:variable name="ted" select="current()/@name"/>
                    <tr>
                        <td>
                            <xsl:value-of select="current()/@name"/>
                        </td>
                        <td>
                            <input type="checkbox" name="formatsB" format="{$ted}" checked="checked"
                            />
                        </td>
                        <td colspan="4">
                            <xsl:if test="current()/ds:AllowedRange/ds:Interval">
                                <xsl:for-each select="current()/ds:AllowedRange/ds:Interval">
                                    <xsl:if test="current()/ds:LeftBound/@type = 'closed'">
                                        <![CDATA[ < ]]>
                                    </xsl:if>
                                    <xsl:if test="current()/ds:LeftBound/@type = 'open'"> ( </xsl:if>
                                    <xsl:value-of select="current()/ds:LeftBound/@value"/> ;
                                        <xsl:value-of select="current()/ds:RightBound/@value"/>
                                    <xsl:if test="current()/ds:RightBound/@type = 'closed'">
                                        <![CDATA[ > ]]>
                                    </xsl:if>
                                    <xsl:if test="current()/ds:RightBound/@type = 'open'"> ) </xsl:if>
                                    <xsl:if
                                        test="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/ds:RestrictedTo/ds:Format/ds:Intervals">
                                        <div name="BKEF_formatBPosVal" typ="interval"
                                            format="{$ted}"><table
                                                style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;">
                                                <xsl:for-each
                                                  select="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/ds:RestrictedTo/ds:Format/ds:Intervals/ds:Interval">
                                                  <tr
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;">
                                                  <xsl:if
                                                  test="current()/ds:LeftBound/@type = 'closed'">
                                                  <td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;">
                                                  <select name="{$ted}0">
                                                  <option type="close">&lt;</option>
                                                  <option type="open">(</option>
                                                  </select>
                                                  </td>
                                                  </xsl:if>
                                                  <xsl:if
                                                  test="current()/ds:LeftBound/@type = 'open'">
                                                  <td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><select name="{$ted}0">
                                                  <option type="open">(</option>
                                                  <option type="close">&lt;</option>
                                                  </select></td>
                                                  </xsl:if>
                                                  <xsl:variable name="leva"
                                                  select="current()/ds:LeftBound/@value"/>
                                                  <xsl:variable name="prav"
                                                  select="current()/ds:RightBound/@value"/>
                                                  <td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><input type="text" size="10" name="{$ted}"
                                                  value="{$leva}"/></td><td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  > ; </td>
                                                  <td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><input type="text" size="10" name="{$ted}1"
                                                  value="{$prav}"/></td>
                                                  <xsl:if
                                                  test="current()/ds:RightBound/@type = 'closed'">
                                                  <td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><select name="{$ted}2">
                                                  <option type="close">&gt;</option>
                                                  <option type="open">)</option>
                                                  </select></td>
                                                  </xsl:if>
                                                  <xsl:if
                                                  test="current()/ds:RightBound/@type = 'open'">
                                                  <td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><select name="{$ted}2">
                                                  <option type="open">)</option>
                                                  <option type="close">&gt;</option>
                                                  </select></td>
                                                  </xsl:if>
                                                  </tr>
                                                </xsl:for-each>
                                            </table></div>
                                    </xsl:if>
                                    <xsl:if
                                        test="not(//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/ds:RestrictedTo/ds:Format/ds:Intervals/ds:Interval)">
                                        <br/> <div name="BKEF_formatBPosVal"
                                            typ="interval" format="{$ted}">Interval: <br /><table
                                                style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><tr
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;">
                                                  <td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><select name="{$ted}0">
                                                  <option type="close">&lt;</option>
                                                  <option type="open">(</option>
                                                  </select></td><td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><input type="text" size="5" name="{$ted}"
                                                  /></td><td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  > ; </td><td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><input type="text" size="5" name="{$ted}1"
                                                  /></td><td
                                                  style="margin: 0px; padding: 0px; border: 0px; min-width:0px; min-height: 0px;"
                                                  ><select name="{$ted}2">
                                                  <option type="close">&gt;</option>
                                                  <option type="open">)</option>
                                                  </select></td>
                                                </tr></table></div>
                                    </xsl:if>
                                </xsl:for-each>
                            </xsl:if>
                            <xsl:if test="current()/ds:AllowedRange/ds:Enumeration">
                                <xsl:if test="current()/ds:AllowedRange/ds:Enumeration">
                                    <select size="4" multiple="multiple" name="BKEF_formatBPosVal"
                                        format="{$ted}">
                                        <option selected="selected"> - <xsl:value-of
                                                select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Choose']"
                                            /> - </option>
                                        <xsl:for-each
                                            select="current()/ds:AllowedRange/ds:Enumeration/ds:Value">
                                            <option>
                                                <xsl:value-of select="current()"/>
                                            </option>
                                        </xsl:for-each>
                                        <option>
                                            <xsl:for-each select="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/ds:RestrictedTo/ds:Format/ds:Value[@format = $ted]">
                                            <xsl:value-of select="current()" />Ł
                                        </xsl:for-each>
                                        </option>
                                    </select>
                                </xsl:if>
                            </xsl:if>
                            <xsl:if test="current()/ds:ValueAnnotations">
                                <select size="4" multiple="multiple" name="BKEF_formatBPosVal"
                                    format="{$ted}">
                                    <option selected="selected"> - <xsl:value-of
                                            select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Choose']"
                                        /> - </option>
                                    <xsl:for-each
                                        select="current()/ds:ValueAnnotations/ds:ValueAnnotation">
                                        <option>
                                            <xsl:value-of select="current()/ds:Value"/>
                                        </option>
                                    </xsl:for-each>
                                    <option>
                                        <xsl:for-each select="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/ds:RestrictedTo/ds:Format/ds:Value[@format = $ted]">
                                            <xsl:value-of select="current()" />Ł
                                        </xsl:for-each>
                                    </option>
                                </select>
                            </xsl:if>
                        </td>
                    </tr>
                </xsl:for-each>
                <tr id="BKEF_secondButton" style="display: none;">
                    <td colspan="6">
                        <input type="button" value="{$save}" onclick="save();"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <xsl:value-of
                            select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Knowledge validity']"
                        />: </td>
                    <td colspan="3">
                        <select id="BKEF_bgKnow" size="4">
                            <xsl:if
                                test="not(//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:KnowledgeValidity)">
                                <option  selected="selected" vychozi=""> - <xsl:value-of
                                        select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Choose']"
                                    /> - </option>
                            </xsl:if>
                            <xsl:if
                                test="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:KnowledgeValidity">
                                <xsl:variable name="pomocna111" select="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:KnowledgeValidity"/>
                                <option vychozi="{$pomocna111}" selected="selected"> - <xsl:value-of
                                    select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Choose']"
                                /> - </option>
                            </xsl:if>
                            <option type="Proven">
                                <xsl:value-of
                                    select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Proven']"
                                />
                            </option>
                            <option type="Rejected">
                                <xsl:value-of
                                    select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Rejected']"
                                />
                            </option>
                            <option type="Unknown">
                                <xsl:value-of
                                    select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Unknown']"
                                />
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <xsl:value-of
                            select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Influence scope']"
                        />: </td>
                    <td colspan="3">
                        <select id="BKEF_inflScope" size="2">
                            <xsl:if
                                test="not(//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:InfluenceScope)">
                                <option vychozi="" selected="selected"> - <xsl:value-of
                                        select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Choose']"
                                    /> - </option>
                            </xsl:if>
                            <xsl:if
                                test="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:InfluenceScope">
                                <xsl:variable name="pomocna11" select="//ds:MetaAttribute[@name = $attribute and @role = 'A']/parent::*/ds:MetaAttribute[@name = $attributeII and @role = 'B']/parent::*/ds:InfluenceScope"/>
                                <option vychozi="{$pomocna11}" selected="selected"> - <xsl:value-of
                                    select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Choose']"
                                /> - </option>
                            </xsl:if>
                            <option>
                                <xsl:value-of
                                    select="document('dictionary.xml')/BKEFDictionary/Strings/str[@lang=$lang and @name='Background Knowledge']"
                                />
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="button" value="{$save}" onclick="save();"/>
                    </td>
                </tr>
            </table>
        </div>



    </xsl:template>

    <!-- **** RESTRICTION **** -->
    <xsl:template name="ds:Restriction">
        <xsl:param name="path"/>
        <xsl:param name="ids"/>
        <xsl:param name="ktery"/>

        <!-- The name of the restricting format -->

        <div name="bkefrestrikce°{$ids}" id="bkefrestrikce°{$ids}" style="display: none;"
            ktery="{$ktery}">
            <xsl:for-each select="$path/ds:Format">
                <xsl:if test="current()/@name">
                    <span id="bkefrformatname°{$ids}">
                        <xsl:value-of select="current()/@name"/>
                    </span>
                </xsl:if>
                <xsl:choose>
                    <!-- Enumeration -->
                    <xsl:when test="current()/ds:Value">
                        <span id="enumeration°{$ids}" name="enumeration">
                            <xsl:for-each select="current()/ds:Value">
                                <xsl:value-of select="current()"/>
                                <xsl:text>°</xsl:text>
                            </xsl:for-each>
                        </span>
                    </xsl:when>

                    <xsl:when test="current()/ds:Intervals">
                        <!-- ITEREATION - SELECTING INTERVALS -->
                        <span id="enumeration°{$ids}" name="enumeration">
                            <xsl:for-each select="$path/ds:Format/ds:Intervals/ds:Interval">
                                <xsl:choose>
                                    <xsl:when test="current()/ds:LeftBound/@type = 'closed'">
                                        <xsl:text>&lt;</xsl:text>
                                        <xsl:value-of select="current()/ds:LeftBound/@value"/>
                                    </xsl:when>
                                    <xsl:when test="current()/ds:LeftBound/@type = 'open'">
                                        <xsl:text>(</xsl:text>
                                        <xsl:value-of select="current()/ds:LeftBound/@value"/>
                                    </xsl:when>
                                </xsl:choose>
                                <xsl:text>;</xsl:text>
                                <xsl:choose>
                                    <xsl:when test="current()/ds:RightBound/@type = 'closed'">
                                        <xsl:value-of select="current()/ds:RightBound/@value"/>
                                        <xsl:text>&gt;</xsl:text>
                                    </xsl:when>
                                    <xsl:when test="current()/ds:RightBound/@type = 'open'">
                                        <xsl:value-of select="current()/ds:RightBound/@value"/>
                                        <xsl:text>)</xsl:text>
                                    </xsl:when>
                                </xsl:choose>
                            </xsl:for-each>
                        </span>
                    </xsl:when>

                    <xsl:when test="not(current()/ds:Value) and not(current()/ds:Intervals)">
                        <span id="enumeration°{$ids}" name="enumeration">
                            <xsl:for-each select="current()/ds:Value">
                                <xsl:value-of select="current()"/>
                                <xsl:text>°</xsl:text>
                            </xsl:for-each>
                        </span>
                    </xsl:when>

                </xsl:choose>
            </xsl:for-each>
        </div>

    </xsl:template>
</xsl:stylesheet>
