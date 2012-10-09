package xquerysearch.utils;

import java.io.ByteArrayOutputStream;
import java.io.UnsupportedEncodingException;
import java.util.Properties;

import javax.xml.transform.OutputKeys;
import javax.xml.transform.stream.StreamResult;

import org.apache.log4j.Logger;

import net.sf.saxon.Configuration;
import net.sf.saxon.query.DynamicQueryContext;
import net.sf.saxon.query.StaticQueryContext;
import net.sf.saxon.query.XQueryExpression;
import net.sf.saxon.trans.XPathException;

/**
 * Help class for outputting.
 * 
 * @author Tomas Marek
 *
 */
public class OutputUtils {
	
	private static final Logger logger = Logger.getLogger("OutputUtils");
	
	/**
	 * Prepares data description for adding into query output. 
	 * @param queryOutput
	 * @return prepared data description
	 */
	public static String prepareDataDescription(String queryOutput) {
        String ddPrepareQuery = "declare function local:descriptionTransform($inputData) {"
                + "\nlet $dataDictOutput := <Dictionary sourceDictType=\"DataDictionary\" sourceFormat=\"PMML\" default=\"true\" completeness=\"ReferencedFromPatterns\" id=\"DataDictionary\">"
                + "\n            { for $bbaName in distinct-values($inputData/DataDictionary/FieldName)"
                + "\n                let $cats := for $cat in distinct-values($inputData/DataDictionary[FieldName=$bbaName]/CatName) return <Category>{$cat}</Category>"
                + "\n                let $intsAll := for $int in $inputData/DataDictionary[FieldName=$bbaName]/Interval return $int"
                + "\n                let $ints := for $left in distinct-values($intsAll/@left) return for $right in distinct-values($intsAll[@left = $left]/@right) return <Interval leftMargin=\"{$left}\" rightMargin=\"{$right}\" closure=\"{distinct-values($intsAll[@left = $left and @right = $right]/@type)}\"/>"
                + "\n            return <Field id=\"{concat(\"f\",index-of($inputData//DataDictionary/FieldName, $bbaName)[1])}\"><Name>{$bbaName}</Name>{$cats, $ints}</Field>}"
                + "\n            </Dictionary>"
                + "\nlet $transDictOutput := <Dictionary sourceDictType=\"DiscretizationHint\" sourceFormat=\"PMML\" default=\"true\" completeness=\"ReferencedFromPatterns\" id=\"TransformationDictionary\">"
                + "\n            {for $bbaName in distinct-values($inputData/TransformationDictionary/FieldName)"
                + "\n                let $cats := for $cat in distinct-values($inputData/TransformationDictionary[FieldName=$bbaName]/CatName) return <Category>{$cat}</Category>"
                + "\n                let $intsAll := for $int in $inputData/TransformationDictionary[FieldName=$bbaName]/Interval return $int"
                + "\n                let $ints := for $left in distinct-values($intsAll/@left) return for $right in distinct-values($intsAll[@left = $left]/@right) return <Interval leftMargin=\"{$left}\" rightMargin=\"{$right}\" closure=\"{distinct-values($intsAll[@left = $left and @right = $right]/@type)}\"/>"
                + "\n            return <Field id=\"{concat(\"f\",index-of($inputData//TransformationDictionary/FieldName, $bbaName)[1])}\"><Name>{$bbaName}</Name>{$cats, $ints}</Field>}"
                + "\n            </Dictionary>"
                + "\nlet $mappingOutput := <DictionaryMapping>"
                + "\n            {for $ddName in distinct-values($inputData/DataDictionary/FieldName)"
                + "\n                let $id := $dataDictOutput/Field[index-of($dataDictOutput/Field/Name, $ddName)]/@id"
                + "\n                let $tdNames := for $tdName in distinct-values($inputData[DataDictionary/FieldName=$ddName]/TransformationDictionary/FieldName) return $tdName"
                + "\n                let $valueMappings := if (count($inputData/DataDictionary[FieldName=$ddName]/Interval) > 0) then"
                + "\n                    for $intervalLeft in distinct-values($inputData/DataDictionary[FieldName=$ddName]/Interval/@left)"
                + "\n                        return for $intervalRight in distinct-values($inputData/DataDictionary[FieldName=$ddName and Interval/@left = $intervalLeft]/Interval/@right)"
                + "\n                        return for $intervalClosure in distinct-values($inputData/DataDictionary[FieldName=$ddName and Interval/@left = $intervalLeft and Interval/@right = $intervalRight]/Interval/@type)"
                + "\n                        let $tdValues := for $tdValue in distinct-values($inputData[DataDictionary/FieldName=$ddName and DataDictionary/Interval/@left = $intervalLeft and DataDictionary/Interval/@right = $intervalRight and DataDictionary/Interval/@type = $intervalClosure]/TransformationDictionary/CatName)"
                + "\n                        return $tdValue"
                + "\n                    return <IntervalMapping><Field><Interval leftMargin=\"{$intervalLeft}\" rightMargin=\"{$intervalRight}\" closure=\"{$intervalClosure}\" /></Field><Field>{for $tdValueOut in $tdValues return <CatRef>{$tdValueOut}</CatRef>}</Field></IntervalMapping>"
                + "\n                else" 
                + "\n                    <ValueMappings>{for $ddValue in distinct-values($inputData/DataDictionary[FieldName=$ddName]/CatName)"
                + "\n                        let $tdValues := for $tdValue in distinct-values($inputData[DataDictionary/FieldName = $ddName and DataDictionary/CatName = $ddValue]/TransformationDictionary/CatName) return $tdValue"
                + "\n                    return <ValueMapping><Field id=\"{$id}\" dictID=\"DataDictionary\"><CatRef>{$ddValue}</CatRef></Field><Field id=\"{$id}\" dictID=\"TransformationDictionary\">{for $tdValueOut in $tdValues return <CatRef>{$tdValueOut}</CatRef>}</Field></ValueMapping>}</ValueMappings>"
                + "\n            return <FieldMapping><AppliesTo>"
                + "\n            <FieldRef id=\"{$id}\" dictID=\"DataDictionary\"/>"
                + "\n            <FieldRef id=\"{$id}\" dictID=\"TransformationDictionary\"/>"
                + "\n            </AppliesTo>{$valueMappings}</FieldMapping>}"
                + "\n            </DictionaryMapping>"
                + "\nreturn $dataDictOutput union $transDictOutput union $mappingOutput"
                + "\n};"
                + "\nlet $dd := " + queryOutput
                + "\nreturn local:descriptionTransform($dd//BBA)";
        try {
	        ByteArrayOutputStream baos = new ByteArrayOutputStream();
	        Configuration config = new Configuration();
	        StaticQueryContext sqc = config.newStaticQueryContext();
	        XQueryExpression xqe = sqc.compileQuery(ddPrepareQuery);
	        DynamicQueryContext dqc = new DynamicQueryContext(config);
	        Properties props = new Properties();
	        props.setProperty(OutputKeys.METHOD, "html");
	        props.setProperty(OutputKeys.INDENT, "no");
	        xqe.run(dqc, new StreamResult(baos), props);
	        return baos.toString("UTF-8");
        } catch (UnsupportedEncodingException e) {
        	logger.warn("Error occured during data description preparation! - Unsupported encoding exception");
        	return null;
		} catch (XPathException e) {
			logger.warn("Error occured during data description preparation! - XPath expression exception");
			return null;
		}
    }
    /**
     * Method for changing query output structure.
     * @param queryOutput
     * @return restructured output
     */
    public static String restructureOutput (String queryOutput) {
        String restructureQuery = 
                "declare function local:restructure($queryOutput) {"
                + "\nlet $BBAs := for $bba in $queryOutput//BBA let $fieldRef := $bba/TransformationDictionary/FieldName/string() let $catName := $bba/TransformationDictionary/CatName/string() return <BBA id=\"{$bba/@id}\"><Text>{concat($fieldRef, \"(\", $catName, \")\")}</Text><FieldRef>{$fieldRef}</FieldRef><CatRef>{$catName}</CatRef></BBA>"
                + "\nlet $ARs := let $positions := $queryOutput/Hit/position()"
                    + "\nfor $position in $positions return for $hit in $queryOutput/Hit[$position]"
                    + "\nlet $ARAntePointer := if(count($hit/Detail/Antecedent)>0) then concat(\"ante_00\", $position) else ()"
                    + "\nlet $ARConsPointer := if(count($hit/Detail/Consequent)>0) then concat(\"cons_00\", $position) else ()"
                    + "\nlet $ARCondPointer := if(count($hit/Detail/Condition)>0) then concat(\"cond_00\", $position) else ()"
                    + "\nreturn <Hit docID=\"{$hit/@docID}\" ruleID=\"{$hit/@ruleID}\" docName=\"{$hit/@docName}\" database=\"{$hit/@database}\" reportURI=\"{$hit/@reportURI}\">"
                        + "{if (count($ARCondPointer) > 0) then"
                        + "<AssociationRule antecedent=\"{$ARAntePointer}\" consequent=\"{$ARConsPointer}\" condition=\"{count($ARCondPointer)}\">{$hit/Text}{$hit/Detail/IMValue}</AssociationRule>"
                        + "else"
                        + "<AssociationRule antecedent=\"{$ARAntePointer}\" consequent=\"{$ARConsPointer}\">{$hit/Text}{$hit/Detail/IMValue}</AssociationRule>}"
                    + "</Hit>"
                + "\nlet $DBAs := let $positions := $queryOutput/Hit/position() for $position in $positions return for $hit in $queryOutput/Hit[$position]"
                    + "\nlet $ante := local:getDBAs(concat('ante_00', $position), $hit/Detail/Antecedent)"
                    + "\nlet $cons := local:getDBAs(concat('cons_00', $position), $hit/Detail/Consequent)"
                    + "\nlet $cond := local:getDBAs(concat('cond_00', $position), $hit/Detail/Condition)"
                    + "\nreturn $ante union $cons union $cond"
                + "\nreturn $BBAs union $DBAs union $ARs};"
                + "\ndeclare function local:getDBAs($ID, $DBAs){"
                    + "\nlet $dba1Positions := $DBAs/position() for $dba1Position in $dba1Positions return for $dba1 in $DBAs[$dba1Position]"
                    + "\nlet $dba1ID := $ID"
                    + "\nlet $childsDBA1 := let $dba2Positions := $dba1/DBA/position() for $dba2Position in $dba2Positions return for $dba2 in $dba1/DBA[$dba2Position] return concat($dba1ID, '_00', $dba2Position)"
                    + "\nlet $dba1Output := <DBA id=\"{$dba1ID}\" connective=\"{if(count($dba1/@connective)>0) then $dba1/@connective else 'Conjunction'}\">"
                        + "\n{for $child in $childsDBA1 return <BARef>{$child}</BARef>} </DBA>"
                    + "\nlet $dba2Output := let $dba2Positions := $dba1/DBA/position() for $dba2Position in $dba2Positions return for $dba2 in $dba1/DBA[$dba2Position]"
                        + "\nlet $dba2Name := concat($dba1ID, '_00', $dba2Position) return local:getDBAs2($dba2Name, $dba2) "
                    + "\nreturn $dba1Output union $dba2Output};"
                + "\ndeclare function local:getDBAs2 ($ID, $DBAs) { let $dba2Positions := $DBAs/position() for $dba2Position in $dba2Positions return for $dba2 in $DBAs[$dba2Position]"
                    + "\nlet $dba2ID := $ID let $childsDBA2 := "
                    + "\nlet $dba3Positions := $dba2/DBA/position() for $dba3Position in $dba3Positions return for $dba3 in $dba2/DBA[$dba3Position] return concat($dba2ID, '_00', $dba3Position)"
                    + "\nlet $dba2Output := <DBA id=\"{$dba2ID}\" connective=\"{if(count($dba2/@connective)>0) then $dba2/@connective else 'Conjunction'}\">"
                        + "\n{for $child in $childsDBA2 return <BARef>{$child}</BARef>} </DBA>"
                    + "\nlet $dba3Output := let $dba3Positions := $dba2/DBA/position() for $dba3Position in $dba3Positions return for $dba3 in $dba2/DBA[$dba3Position]"
                        + "\nlet $dba3Name :=  concat($dba2ID, '_00', $dba3Position) return local:getBBAs($dba3Name, $dba3)"
                    + "\nreturn $dba2Output union $dba3Output};"
                + "\ndeclare function local:getBBAs ($ID, $DBAs) { let $dba3Positions := $DBAs/position() for $dba3Position in $dba3Positions return for $dba3 in $DBAs[$dba3Position]"
                    + "\nlet $dba3ID := $ID"
                    + "\nlet $childsDBA3 := let $bbaPositions := $dba3/BBA/position() for $bbaPosition in $bbaPositions return for $bba in $dba3/BBA[$bbaPosition] return $bba/@id/string()"
                    + "\nlet $dba3Output := <DBA id=\"{$dba3ID}\" connective=\"{if(count($dba3/@connective)>0) then $dba3/@connective else 'Conjunction'}\">"
                        + "\n{for $child in $childsDBA3 return <BARef>{$child}</BARef>} </DBA>"
                    + "\nreturn $dba3Output};"
                + "\nlet $queryOutput := " + queryOutput + "\n"
                + "\nreturn local:restructure($queryOutput)";
        try {
	        ByteArrayOutputStream baos = new ByteArrayOutputStream();
	        Configuration config = new Configuration();
	        StaticQueryContext sqc = config.newStaticQueryContext();
	        XQueryExpression xqe = sqc.compileQuery(restructureQuery);
	        DynamicQueryContext dqc = new DynamicQueryContext(config);
	        Properties props = new Properties();
	        props.setProperty(OutputKeys.METHOD, "html");
	        props.setProperty(OutputKeys.INDENT, "no");
	        xqe.run(dqc, new StreamResult(baos), props);
	        return baos.toString("UTF-8");
        } catch (UnsupportedEncodingException e) {
        	logger.warn("Error occured during restructuring output! - Unsupported encoding exception");
        	return null;
		} catch (XPathException e) {
			logger.warn("Error occured during restructuring output! - XPath expression exception");
			return null;
		}
    }
}
