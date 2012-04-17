package xquerysearch.query;

import java.io.ByteArrayOutputStream;
import java.util.Properties;
import java.util.logging.Logger;

import javax.xml.transform.OutputKeys;
import javax.xml.transform.stream.StreamResult;

import net.sf.saxon.Configuration;
import net.sf.saxon.query.DynamicQueryContext;
import net.sf.saxon.query.StaticQueryContext;
import net.sf.saxon.query.XQueryExpression;
import net.sf.saxon.trans.XPathException;
import xquerysearch.controllers.MainController;
import xquerysearch.utils.StoredQueryUtils;

/**
 * Trida umoznuje praci s dotazy - ukladani, mazani atd.
 * @author Tomas Marek
 */
public class QueryHandler {
    private Logger logger = MainController.getLogger();

    /**
     * Metoda prevadi query ze vstupniho ARBuilder formatu do formatu pro dalsi zpracovani
     * @param request vstupni dotaz (ve formatu ARBuilder)
     * @return query v novem formatu
     */
    public ByteArrayOutputStream queryPrepare(String request) {
        String query =
                "declare function local:processRequest($request as node()) {"
                    + "\n let $generalSet := $request/ARQuery/GeneralSetting"
                    + "\n let $output := if (count($generalSet) > 0) then ("
                    + "\n let $attribs := for $MBA in $generalSet/MandatoryPresenceConstraint/MandatoryBA/text() return "
                        + "\n for $DBA in $request//DBASetting[@id = $MBA] return local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")"
                    + "\n return <AR_query><Scope>{$generalSet/Scope/node()}</Scope>{$attribs}"
                    +    "\n <IMs>{for $im in $request/ARQuery/InterestMeasureSetting/InterestMeasureThreshold return if (contains(lower-case($im/InterestMeasure/text()), \"any interest measure\"))"
                    + "\n then <IM id=\"{$im/@id}\"><InterestMeasure>Any Interest Measure</InterestMeasure></IM>"
                    + "\n else <IM id=\"{$im/@id}\"><InterestMeasure>{$im/InterestMeasure/text()}</InterestMeasure><Threshold>{$im/Threshold/text()}</Threshold><CompareType>{$im/CompareType/text()}</CompareType></IM>}</IMs>"
                    + "\n <MaxResults>{$request/ARQuery/MaxResults/text()}</MaxResults></AR_query>"
                    + ") else ("
                    + "\n let $antecedent := for $ante in $request//AntecedentSetting/text() return if (count($request//DBASetting[@id = $ante]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $ante], $request)}</DBA>"
                    + "\n else for $DBA in $request//DBASetting[@id = $ante] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")}</DBA>"
                    + "\n let $consequent := for $cons in $request//ConsequentSetting let $exception := if ($cons/@exception=\"true\") then true() else false()  return if (count($request//DBASetting[@id = $cons/text()]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $cons], $request)}</DBA>"
                    + "\n else for $DBA in $request//DBASetting[@id = $cons/text()] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")}</DBA>"
                    + "\n let $condition := for $cond in $request//ConditionSetting/text() return if (count($request//DBASetting[@id = $cond]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $cond], $request)}</DBA>"
                    + "\n else for $DBA in $request//DBASetting[@id = $cond] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")}</DBA>"
                    + "\nreturn"
                    + "\n <AR_query> {if (count($request/ARQuery/AntecedentSetting) > 0) then (<Antecedent>{$antecedent}</Antecedent>) else ()}"
                    + "\n {if (count($request/ARQuery/ConsequentSetting) > 0) then if($request/ARQuery/ConsequentSetting/@exception=\"true\") then <Consequent exception=\"true\">{$consequent}</Consequent> else (<Consequent>{$consequent}</Consequent>) else ()}"
                    + "\n {if (count($request/ARQuery/ConditionSetting) > 0) then (<Condition>{$condition}</Condition>) else ()}"
                    +    "\n <IMs>{for $im in $request/ARQuery/InterestMeasureSetting/InterestMeasureThreshold return if (contains(lower-case($im/InterestMeasure/text()), \"any interest measure\"))"
                    + "\n then <IM id=\"{$im/@id}\"><InterestMeasure>Any Interest Measure</InterestMeasure></IM>"
                    + "\n else <IM id=\"{$im/@id}\"><InterestMeasure>{$im/InterestMeasure/text()}</InterestMeasure><Threshold>{$im/Threshold/text()}</Threshold><CompareType>{$im/CompareType/text()}</CompareType></IM>}</IMs>"
                    + "\n <MaxResults>{$request/ARQuery/MaxResults/text()}</MaxResults></AR_query>)"
                + "\nreturn $output};"
                + "\n declare function local:getBBAs($BBAs as node()*, $request as node()) as node()*{"
                    + "\n for $BBA in $BBAs return local:BBABuild($BBA, $request//DictionaryMapping)};"
                + "\n declare function local:DBAtoBBARecursion($BARefs as node()*, $request as node(), $literal as xs:string*, $inference as xs:string*){"
                    + "\n for $odkaz in $BARefs let $liter := if ($literal = \"\" or empty($literal)) then \"Both\" else $literal let $infer := if ($inference = \"\" or empty($inference)) then \"false\" else $inference return "
                        + "\n if (count($request//BBASetting[@id = $odkaz/text()])>0) then <DBA connective=\"{$liter}\" inference=\"{$infer}\">{local:BBABuild($request//BBASetting[@id = $odkaz/text()], $request//DictionaryMapping)}</DBA> else local:DBAtoBBARecursion($request//DBASetting[@id = $odkaz/text()]//BASettingRef, $request, $request//DBASetting[@id = $odkaz/text()]/LiteralSign/text(), $request//DBASetting[@id = $odkaz/text()]/LiteralSign/@inference)};"
                + "\n declare function local:BBABuild($BBA as node(), $mapping) as node(){"
                    + "\n let $dictionary := $BBA/FieldRef/@dictionary/string()"
                    + "\n let $field := $BBA/FieldRef/text()"
                    + "\n let $coefficient := $BBA/Coefficient"
                    + "\n let $category := for $cat in $coefficient/child::node()[name() != \"Type\"]"
                    	+ "return if ($cat/name() = \"Category\") then <Category>{$cat/text()}</Category>"
                    	+ "else if ($cat/name() = \"Interval\") then <Interval closure=\"{$cat/@closure}\" left=\"{$cat/@leftMargin}\" right=\"{$cat/@rightMargin}\"/>"
                    	+ "else element {$cat/name()} {$cat/text()}"
                    + "\n return if (count($mapping/ValueMapping/Field[@name = $field]) = 0) then "
                        + "\n <BBA id=\"{$BBA/@id}\">"
                            + "\n <Field dictionary=\"TransformationDictionary\"><Name>{$field}</Name><Type>{$coefficient/Type/text()}</Type>{$category}</Field>"
                            + "\n <Field dictionary=\"DataDictionary\"><Name>{$field}</Name><Type>{$coefficient/Type/text()}</Type>{$category}</Field>"
                        + "\n </BBA> else"
                        + "\n <BBA id=\"{$BBA/@id}\"><Field dictionary=\"{$dictionary}\"><Name>{$field}</Name><Type>{$coefficient/Type/text()}</Type>{$category}</Field>{local:DictionarySwitch($dictionary, $field, $coefficient, $mapping)}</BBA>};"
                + "\n declare function local:DictionarySwitch($dict, $field, $coeff, $mapping){"
                    + "\n let $valueMapping := $mapping//Field[@name = $field and @dictionary = $dict]/parent::node()"
                    + "\n let $fieldTrans := $valueMapping/Field[@dictionary != $dict]"
                    + "\n let $category := let $catTrans := $fieldTrans/child::node() for $everyCat in $catTrans return "
                        + "\n if ($everyCat/name() = \"Value\") then <Category>{$everyCat/text()}</Category> else "
                            + "\n if ($everyCat/name() = \"Interval\") then <Interval closure=\"{$everyCat/@closure}\" left=\"{$everyCat/@leftMargin}\" right=\"{$everyCat/@rightMargin}\"/> else $everyCat"
                    + "\n return if (count($fieldTrans) > 0) then "
                        + "\n <Field dictionary=\"{distinct-values($fieldTrans[1]/@dictionary)}\"><Name>{distinct-values($fieldTrans[1]/@name/string())}</Name><Type>{$coeff/Type/text()}</Type>{$category}</Field> else ()};"
                + "\n let $vstup := " + StoredQueryUtils.deleteDeclaration(request)
                + "\n return local:processRequest($vstup)";
        
        try {
        	ByteArrayOutputStream baos = new ByteArrayOutputStream();
		    Configuration config = new Configuration();
		    StaticQueryContext sqc = config.newStaticQueryContext();
		    XQueryExpression xqe = sqc.compileQuery(query);
		    DynamicQueryContext dqc = new DynamicQueryContext(config);
		    Properties props = new Properties();
		    props.setProperty(OutputKeys.METHOD, "html");
		    props.setProperty(OutputKeys.INDENT, "no");
		    xqe.run(dqc, new StreamResult(baos), props);
		    return baos;
        } catch (XPathException e) {
        	logger.warning("Query preparation failed! - XPath exception");
        	return null;
        }
    }
}
