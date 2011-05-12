package xquery_servlet;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.Properties;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.stream.StreamResult;
import net.sf.saxon.Configuration;
import net.sf.saxon.query.DynamicQueryContext;
import net.sf.saxon.query.StaticQueryContext;
import net.sf.saxon.query.XQueryExpression;
import net.sf.saxon.trans.XPathException;

/**
 * Trida umoznuje praci s dotazy - ukladani, mazani atd.
 * @author Tomas Marek
 */
public class QueryHandler {
    String queryDir;

    /**
     * Konstruktor instance tridy
     * @param queryDir slozka obsahujici ulozene query
     */
    public QueryHandler(String queryDir) {
        this.queryDir = queryDir;
    }

    /**
     * Metoda pro ulozeni query
     * @param query ukladana query
     * @param id nazev ukladane query
     * @return zprava o ulozeni / chybe
     */
    public String addQuery(String query, String id){
        File file = new File(queryDir + id + ".txt");
        String output = "";
        try {
                if (file.exists()) {
                output = "<error>Query jiz existuje!</error>";
                }
                else {
                        FileOutputStream fos = new FileOutputStream(file);
                        OutputStreamWriter osw = new OutputStreamWriter(fos);
                        osw.write(query);
                        osw.close();
                output = "<message>Query " + id + " ulozena!</message>";
                }
        } catch (IOException e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro ziskani nazvu ulozenych XQuery
     * @return seznam ulozenych XQuery
     */
    public String getQueriesNames(){
        String output = "";
        File uploadFolder = new File(queryDir);
        File uploadFiles[] = uploadFolder.listFiles();

        for(int i = 0; i < uploadFiles.length; i++){
            if (uploadFiles[i].isFile()) {
                String fileName = uploadFiles[i].getName();
                String nameParts[] = fileName.split("\\.");
                String outputName = "";
                if (nameParts[nameParts.length-1].toLowerCase().equals("txt")){
                    for (int a = 0; a < nameParts.length-1; a++){
                        outputName += nameParts[a];
                    }
                    output += "<query>" + outputName + "</query>";
                }
            }
        }
        return output;
    }

    /**
     * Metoda pro vymazani ulozene XQuery
     * @param id ID ulozene XQuery
     * @return zprava - vymazana/nenalezena
     */
    public String deleteQuery (String id) {
        String output = "";
        File file = new File(queryDir + id + ".txt");

        if (file.exists()) {
                file.delete();
                output = "<message>Query " + id + " smazana!</message>";
        }
        else {
                output = "<error>Query neexistuje!</error>";
        }
        return output;
    }

    /**
     * Metoda pro ziskani ulozene XQuery
     * @param id ID ulozene XQuery
     * @return vracena XQuery/Zprava - nenalezena
     */
    public String[] getQuery(String id){
        FileReader rdr = null;
        BufferedReader out = null;
        File file = new File(queryDir + id + ".txt");
        String output[] = new String[2];
        output[1] = "";
        try {
                if (file.exists()) {
                        rdr = new FileReader(file);
                        out = new BufferedReader(rdr);
                        String radek = out.readLine();
                        while (radek != null){
                                output[1] += radek + "\n";
                                radek = out.readLine();
                        }
                        output[0] = "0";
                        out.close();
                }
                else {
                        output[1] = "<error>Query neexistuje!</error>";
                        output[0] = "1";
                }
        } catch (IOException e) {
                output[1] = "<error>"+e.toString()+"</error>";
                output[0] = "1";
        }
        return output;
    }

    /**
     * Metoda zajistujici odstraneni XML deklarace z XQuery dotazu
     * @param query vstupni XQuery dotaz
     * @return vraceny dotaz bez XML deklarace / chyba
     */
    public String deleteDeclaration(String query) {
        String output = "";
        String splitXMLBegin[] = query.split("([<][?][x][m][l])|([<][?][o][x][y][g][e][n])");
        if (splitXMLBegin.length == 1) {
            output = query;
        } else {
            for (int i = 0; i <= (splitXMLBegin.length - 1); i++) {
                if (i == 0) {
                    output += splitXMLBegin[i];
                } else {
                    String splitXMLEnd[] = splitXMLBegin[i].split("[?][>]");
                    if (splitXMLEnd.length > 1) {
                        String splitXMLBack = splitXMLEnd[1];
                        output += splitXMLBack;
                    }
                }
            }
        }
        return output;
    }

    /**
     * Metoda prevadi query ze vstupniho ARBuilder formatu do formatu pro dalsi zpracovani
     * @param request vstupni dotaz (ve formatu ARBuilder)
     * @return query v novem formatu
     */
    public ByteArrayOutputStream queryPrepare(String request){
        String query =
                "declare function local:processRequest($request as node()) {"
                    + "\n let $generalSet := $request/ARQuery/GeneralSetting"
                    + "\n let $output := if (count($generalSet) > 0) then ("
                    + "\n let $attribs := for $MBA in $generalSet/MandatoryPresenceConstraint/MandatoryBA/text() return "
                        + "\n for $DBA in $request//DBASetting[@id = $MBA] return local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\")"
                    + "\n return <AR_query><IMSetting>{$request/ARQuery/InterestMeasureSetting/InterestMeasureThreshold[1]/InterestMeasure/text()}</IMSetting><Scope>{$generalSet/Scope/node()}</Scope>{$attribs}</AR_query>"
                    + ") else ("
                    + "\n let $antecedent := for $ante in $request//AntecedentSetting/text() return if (count($request//DBASetting[@id = $ante]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $ante], $request)}</DBA>"
                    + "\n else for $DBA in $request//DBASetting[@id = $ante] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\")}</DBA>"
                    + "\n let $consequent := for $cons in $request//ConsequentSetting/text() return if (count($request//DBASetting[@id = $cons]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $cons], $request)}</DBA>"
                    + "\n else for $DBA in $request//DBASetting[@id = $cons] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\")}</DBA>"
                    + "\n let $condition := for $cond in $request//ConditionSetting/text() return if (count($request//DBASetting[@id = $cond]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $cond], $request)}</DBA>"
                    + "\n else for $DBA in $request//DBASetting[@id = $cond] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\")}</DBA>"
                    + "\nreturn"
                    + "\n <AR_query> {if (count($request/ARQuery/AntecedentSetting) > 0) then (<Antecedent>{$antecedent}</Antecedent>) else ()}"
                    + "\n {if (count($request/ARQuery/ConsequentSetting) > 0) then (<Consequent>{$consequent}</Consequent>) else ()}"
                    + "\n {if (count($request/ARQuery/ConditionSetting) > 0) then (<Condition>{$condition}</Condition>) else ()}</AR_query>)"
                + "\nreturn $output};"
                + "\n declare function local:getBBAs($BBAs as node()*, $request as node()) as node()*{"
                    + "\n for $BBA in $BBAs return local:BBABuild($BBA, $request//DictionaryMapping)};"
                + "\n declare function local:DBAtoBBARecursion($BARefs as node()*, $request as node(), $literal as xs:string*){"
                    + "\n for $odkaz in $BARefs let $liter := if ($literal = \"\" or empty($literal)) then \"Both\" else $literal return "
                        + "\n if (count($request//BBASetting[@id = $odkaz/text()])>0) then <DBA connective=\"{$liter}\">{local:BBABuild($request//BBASetting[@id = $odkaz/text()], $request//DictionaryMapping)}</DBA> else local:DBAtoBBARecursion($request//DBASetting[@id = $odkaz/text()]//BASettingRef, $request, $request//DBASetting[@id = $odkaz/text()]/LiteralSign/text())};"
                + "\n declare function local:BBABuild($BBA as node(), $mapping) as node(){"
                    + "\n let $dictionary := $BBA/FieldRef/@dictionary/string()"
                    + "\n let $field := $BBA/FieldRef/text()"
                    + "\n let $coefficient := $BBA/Coefficient"
                    + "\n let $category := for $cat in $coefficient//Category return "
                        + "\n if ($cat/name() = \"Category\") then <Category>{$cat/text()}</Category> else"
                            + "\n if ($cat/name() = \"Interval\") then <Interval closure=\"{$cat/@closure}\" left=\"{$cat/@leftMargin}\" right=\"{$cat/@rightMargin}\"/> else $cat"
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
                + "\n let $vstup := "+deleteDeclaration(request)
                + "\n return local:processRequest($vstup)";
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        try {
            Configuration config = new Configuration();
            StaticQueryContext sqc = config.newStaticQueryContext();
            XQueryExpression xqe = sqc.compileQuery(query);
            DynamicQueryContext dqc = new DynamicQueryContext(config);
            Properties props = new Properties();
            props.setProperty(OutputKeys.METHOD, "html");
            props.setProperty(OutputKeys.INDENT, "no");
            xqe.run(dqc, new StreamResult(baos), props);
        } catch (XPathException ex) {
            //output += "<error>" + ex.toString() + "</error>";
        }
        return baos;
    }
}
