package xquerysearch;


import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Properties;
import java.util.logging.Logger;
import java.util.regex.Pattern;

import javax.xml.transform.OutputKeys;
import javax.xml.transform.stream.StreamResult;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import net.sf.saxon.Configuration;
import net.sf.saxon.query.DynamicQueryContext;
import net.sf.saxon.query.StaticQueryContext;
import net.sf.saxon.query.XQueryExpression;
import net.sf.saxon.trans.XPathException;

import org.xml.sax.InputSource;

import xquerysearch.db.DbConnectionManager;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;
import com.sleepycat.dbxml.XmlValue;

/**
 * 
 * @author Tomas Marek
 */
public class BDBXMLHandler {
	private Logger logger = CommunicationManager.logger;
	private DbConnectionManager dcm;
	private QueryMaker qm;
	private XmlManager mgr;
	private QueryHandler qh;
    private SettingsManager settings;
    private String containerName;
    boolean useTransformation;
    private String xsltPathPMML;
    private String xsltPathBKEF;
    private Pattern replaceMask = Pattern.compile("[|!@$^* \\//\"\',?ˇ´<>¨;¤×÷§]");
    private String replaceBy = "_";
    
    
    public BDBXMLHandler(SettingsManager settings) {
    	this.dcm = new DbConnectionManager(settings);
    	this.settings = settings;
    	this.containerName = settings.getContainerName();
    	this.useTransformation = settings.isUseTransformation();
    	this.xsltPathPMML = settings.getPmmlTransformationPath();
    	this.xsltPathBKEF = settings.getBkefTransformationPath();
    	this.qm = new QueryMaker(settings);
    	this.qh = new QueryHandler(settings);
    }

    /**
     * Removes document
     * @param id document id
     * @return message
     */
    public String removeDocument (String id) {
        if (dcm.removeDocument(id)) {
        	return "<message>Document with id \"" + id + "\" removed!</message>";
        } else {
            return "<error>Removing document with id \"" + id + "\" failed!</error>";
        }
    }

    /**
     * Gets the document by id
     * @param id document ID
     * @return document as string or message when error occurs
     */
    public String getDocument(String id) {
    	XmlDocument doc = dcm.getDocumentById(id);
    	if (doc != null) {
    		try {
				return doc.getContentAsString();
			} catch (XmlException e) {
				return "<error>Error occured when finding the document with id \"" + id + "\"</error>";
			}
    	} else {
    		return "<error>Cannot find the document with id \"" + id + "\"</error>";
    	}
    }

    /**
     * Cyclic querying (10 times)
     * @param search query content
     * @return time spent and results
     */
    public String query_10(String search) {
        String output = "";
        String output_temp = "";
        InputStream query = new ByteArrayInputStream(qh.queryPrepare(search).toByteArray());
        InputStream query2 = new ByteArrayInputStream(qh.queryPrepare(search).toByteArray());
        String xpath = qm.makeXPath(query)[0];
        for (int i=0; i<10; i++){
            output += "<pokus cislo=\""+ i +"\">";
			double time_start = System.currentTimeMillis();
            output_temp = queryShortened(xpath, false, false, 9999, query2);
            //output_temp = query("", search, 0);
            output += "<time>"+ (System.currentTimeMillis() - time_start) +"</time>";
            if (i == 9){
                output += output_temp;
            }
            output += "</pokus>";
        }
        return output;
    }

   /**
    * Method for querying repository 
    * @param id query id (of saved query)
    * @param search (query content)
    * @param directQuery use query directly
    * @return
    */
    public String query(String id, String search, boolean directQuery) {
        String query = "";
        if (directQuery) {
            query = search;
        } else {
        	query = qh.getQuery(id);	
        	if (query != null) {
        		query += "\nlet $zadani := " + search
        		+ "\nreturn local:mainFunction($zadani)";
        	}
        }
    	query = qh.deleteDeclaration(query);
        
    	XmlResults res = dcm.query(query) ;

        if (res != null) {
            String result = "";
        	try {    
            	XmlValue value = new XmlValue();
                while ((value = res.next()) != null) {
                    result += (value.asString());
                }
                return result;
            } catch (XmlException e) {
            	return "<error>Querying database failed! - XML exception</error>";
            }
        } else {
            return "<error>No results found</error>";
        }
    }

    /**
     * Gets documents names saved in repository
     * @return documents names list / error message
     */
    public String getDocsNames() {
        String query = "let $docs := for $x in collection(\"" + containerName + "\") return $x"
                + "\nreturn"
                + "\n<docs count=\"{count($docs)}\">{for $a in $docs"
                + "\norder by dbxml:metadata(\"dbxml:name\", $a)"
                + "\nreturn  <doc joomlaID=\"{$a/PMML/@joomlaID}\" timestamp=\"{$a/PMML/@creationTime}\" reportUri=\"{$a/PMML/@reportURI}\" database=\"{$a/PMML/@database}\" table=\"{$a/PMML/@table}\">{dbxml:metadata(\"dbxml:name\", $a)}</doc>}</docs>";

        XmlResults res = dcm.query(query);

        String results = "";
        try {
            XmlValue value = new XmlValue();
            while ((value = res.next()) != null) {
                results += value.asString();
            }
            return results;
        } catch (XmlException e) {
        	return "<error>Documents names retrieval failed!</error>";
        }
    }

    /**
     * Saves document into repository
     * @param document document content
     * @param docID document id (is injected into document)
     * @param docName document name (by this name is saved into repository)
     * @param creationTime
     * @param reportUri URI of the report
     * @return message - success/failure
     */
    public String indexDocument(String document, String docID, String docName, String creationTime, String reportUri) {
        String xml_doc = "";
        boolean isValid = false;
        File xsltFile;
            if (useTransformation) {
                if (document.contains("sourceType=\"BKEF\"")) {
                    xsltFile = new File(xsltPathBKEF);
                } else {
                    isValid = DocumentValidator.validate(document, settings.getValidationSchemaPath());
                    xsltFile = new File(xsltPathPMML);
                }
                XSLTTransformer xslt = new XSLTTransformer();
                xml_doc += xslt.xsltTransformation(document, xsltFile, docID, creationTime, reportUri);
            } else {
                xml_doc = document;
            }
        if (isValid){        
            docName = docName.replaceAll(replaceMask.toString(), replaceBy);

            boolean saved = dcm.insertDocument(docName, xml_doc);

            if (saved) {
            	return "<message>Document " + docName + " inserted</message>";
            } else {
            	return "<error>Error occured during document saving occured</error>";
            }
        } else {
            return "<error>Document validation failed</error>";
        }
    }

    /**
     * Saves document into repository
     * @param document document content
     * @param docID document id (is injected into document)
     * @param docName document name (by this name is saved into repository)
     * @param creationTime
     * @param reportUri URI of the report
     * @return message - success/failure
     */
    public String indexDocument(File document, String docID, String docName, String creationTime, String reportUri) {
        String xml_doc = "";
        String output = "";
        long act_time_long = System.currentTimeMillis();

        FileReader rdr = null;
        BufferedReader out = null;
        try {
	        rdr = new FileReader(document);
	        out = new BufferedReader(rdr);
	        String radek = out.readLine();
	        while (radek != null){
	            xml_doc += radek + "\n";
	            radek = out.readLine();
	        }
        } catch (FileNotFoundException e) {
        	return "<error></error>";
        } catch (IOException e) {
        	return "<error></error>";
        }
        boolean isValid = DocumentValidator.validate(xml_doc, settings.getValidationSchemaPath());
        if(isValid){
	        if (useTransformation) {
	            File xsltFile = new File(xsltPathPMML);
	            XSLTTransformer xslt = new XSLTTransformer();
	            xml_doc = xslt.xsltTransformation(xml_doc, xsltFile, docID, creationTime, reportUri);
	            output += "<xslt_time>" + (System.currentTimeMillis() - act_time_long) + "</xslt_time>";
	       }
	
	        docName = docName.replaceAll(replaceMask.toString(), replaceBy);
	
	        dcm.insertDocument(docName, xml_doc);
	        output += "<message>Document " + docName + " inserted</message>";
	        output += "<doc_time>" + (System.currentTimeMillis() - act_time_long) + "</doc_time>";
        } else {
        	return "<error>Document validation failed</error>";
        }
        return output;
    }

    /**
     * Metoda pro nahrani vice dokumentu ze slozky
     * @param folder slozka, ze ktere se maji soubory nahrat
     * @return zprava o ulozeni / chyba
     */
    public String indexDocumentMultiple (String folder) {
        String output = "";
    	File uploadFolder = new File(folder);
        File uploadFiles[] = uploadFolder.listFiles();
        
        for(int i = 0; i < uploadFiles.length; i++){
            output += indexDocument(uploadFiles[i], "", uploadFiles[i].getName(), new Date().toString(), "");
        }
        return output;
    }

    /**
     * Adds index into repository
     * @param index index specified like namespace;node;index type
     * @return message for success/failure
     */
    public String addIndex(String index) {
        boolean saved = dcm.addIndex(index);
        if (saved) {
        	return "<message>Index \"" + index + "\" added!</message>";
        } else {
        	return "<error>Index malspecified: " + index + "!</error>";
        }
    }

    /**
     * Removes index from repository
     * @param index index specified like namespace;node;index type
     * @return message for success/failure
     */
    public String removeIndex (String index){
    	boolean removed = dcm.removeIndex(index);
    	if (removed) {
    		return "<message>Index \"" + index + "\" removed!</message>";
        } else {
        	return "<error>Index malspecified: " + index + "!</error>";
        }
    }

    /**
     * 
     * @return list of indexes or error message
     */
    public String listIndex() {
    	String indexes = dcm.listIndexes();
        if (indexes != null) {
        	return indexes;
        } else {
        	return "<error>Error occured during listing indexes!</error>";
        }
    }
    
    /**
     * Method for retrieve saved data description from repository
     * @return DataDescription / chyba
     */
    public String getDataDescriptionCache() {
        String dataDescription = dcm.getDataDescirption();
        if (dataDescription != null) {
        	return dataDescription;
        } else {
        	return "<error>Getting data description failed!</error>";
        }
    }
    
    /**
     * Saves data description into repository - caching
     * @return message - success/failure
     */
    public String actualizeDataDescriptionCache() {
       boolean saved = dcm.saveDataDescription(getDataDescription());
       if (saved) {
    	   return "<message>Data description successfully saved!</message>";
       } else {
    	   return "<error>Error occured during data description save!</error>";
       }
    }
    
    /**
     * Metoda pro vytvoreni DataDescription dat ulozenych v XML DB
     * @return DataDescription
     * @throws XmlException 
     */
    public String getDataDescription() {
        String output = "";

        String query = "let $distinctNamesCategorical :="
                    + "\nfor $dataField in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@type != \"continuous\"]/@name/string())"
                    + "\nreturn $dataField"
                + "\n"
                + "\nlet $distinctNamesContinuous :="
                    + "\nfor $dataField in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@type = \"continuous\"]/@name/string())"
                    + "\nreturn $dataField"
                + "\n"
                + "\nlet $transDict := "
                    + "\nfor $name in ($distinctNamesCategorical, $distinctNamesContinuous)"
                    + "\nreturn"
                    + "\n<Field name=\"{$name}\">{"
                    + "\nlet $cats :="
                    + "\nfor $cat in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category/text())"
                    + "\nreturn <Category>{$cat}</Category>"
                    + "\nreturn $cats"
                    + "\n}</Field>"
                    + "\n"
                + "\nlet $dataDict := "
                    + "\nfor $name in $distinctNamesContinuous"
                    + "\nreturn"
                    + "\n<Field name=\"{$name}\">{"
                    + "\nfor $catText in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category/text())"
                    + "\nfor $lm in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[text() = $catText]/@leftMargin)"
                    + "\nlet $rm := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@rightMargin)"
                    + "\nlet $clos := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@closure)"
                    + "\nreturn <Interval leftMargin=\"{$lm}\" rightMargin=\"{$rm}\" closure=\"{$clos}\"/>"
                    + "\n}</Field>"
                + "\n"
                + "\nlet $valueMapping := "
                    + "\nfor $name in $distinctNamesContinuous"
                    + "\nreturn"
                    + "\n"
                    + "\nfor $catText in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category/text())"
                    + "\nfor $lm in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[text() = $catText]/@leftMargin)"
                    + "\nlet $rm := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@rightMargin)"
                    + "\nlet $clos := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@closure)"
                    + "\nreturn "
                    + "\n<ValueMapping>"
                    + "\n<Field dictionary=\"DataDictionary\" name=\"{$name}\">"
                    + "\n<Interval leftMargin=\"{$lm}\" rightMargin=\"{$rm}\" closure=\"{$clos}\"/>"
                    + "\n</Field>"
                    + "\n<Field dictionary=\"TransformationDictionary\" name=\"{$name}\">"
                    + "\n<Value>{$catText}</Value>"
                    + "\n</Field>"
                    + "\n</ValueMapping>"
                + "\nreturn"
                + "\n<data:DataDescription xmlns:data=\"http://keg.vse.cz/ns/datadescription0_1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://keg.vse.cz/ns/datadescription0_1 http://sewebar.vse.cz/schemas/DataDescription0_1.xsd\">"
                    + "\n<Dictionary sourceSubType=\"TransformationDictionary\" sourceType = \"PMML\" default=\"true\">"
                    + "\n{$transDict}"
                    + "\n</Dictionary>"
                    + "\n<Dictionary sourceSubType=\"DataDictionary\" sourceType = \"PMML\">"
                    + "\n{$dataDict}"
                    + "\n</Dictionary>"
                    + "\n<DictionaryMapping>"
                    + "\n{$valueMapping}"
                    + "\n</DictionaryMapping>"
                + "\n</data:DataDescription>";

        /*String query = "<dd:DataDescription xmlns:dd=\"http://keg.vse.cz/ns/datadescription0_1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:pmml=\"http://www.dmg.org/PMML-4_0\" xsi:schemaLocation=\"http://keg.vse.cz/ns/datadescription0_1 http://sewebar.vse.cz/schemas/DataDescription0_1.xsd\">"
        	+ "<Dictionary sourceSubType=\"DataDictionary\" sourceType = \"PMML\" default=\"true\">{"
            + "\nfor $field in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField/@name/string()) "
    		+ "\nlet $values :=  for $value in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type != \"continuous\"]/Category/text())"
			+ "\nreturn <Category>{$value}</Category>"
			+ "\nlet $leftMargin := for $LM in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type = \"continuous\"]/Interval/@leftMargin)"
			+ "\nreturn min($LM)"
			+ "\nlet $rightMargin := for $RM in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type = \"continuous\"]/Interval/@rightMargin)"
			+ "\nreturn max($RM)"
			+ "\nlet $int := if (count($leftMargin) > 0 and count($leftMargin) > 0) then <Interval leftMargin = \"{$leftMargin}\" rightMargin = \"{$rightMargin}\" closure = \"closedClosed\"/> else ()"
			+ "\nlet $intCats := distinct-values(for $DF in collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type = \"continuous\"]"
			+ "\nwhere max(count($DF/Interval/Category))"
			+ "\nreturn $DF/Interval/Category)"
            + "\nreturn"
            + "\n<Field name=\"{$field}\">"
            + "\n{$values union $int}"
            + "\n{for $IC in $intCats return <Category>{$IC}</Category>}"
            + "\n</Field>}</Dictionary></dd:DataDescription>";
        */
        /*String query =
                "<DataDescription><Dictionary sourceSubType=\"DataDictionary\" sourceType = \"PMML\" default=\"true\">{"
                + "\nfor $field in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field/@name/string())"
                + "\nlet $values :=  for $value in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field[@name = $field and @type != \"continuous\"]/fieldValue/text())"
                + "\nreturn <Category>{$value}</Category>"
                + "\nlet $ints_from := for $int in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field[@name = $field and @type = \"continuous\"]/fieldValue[1]/@from)"
                + "\nreturn $int"
                + "\nlet $ints_to := for $int in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field[@name = $field and @type = \"continuous\"]/fieldValue[last()]/@to)"
                + "\nreturn $int"
                + "\nlet $ints := if(count($ints_from) > 0 and count($ints_to) > 0) then <Interval closure=\"\" leftMargin=\"{$ints_from}\" rightMargin=\"{$ints_to}\"/> else ()"
                + "\nreturn"
                + "\n<Field name=\"{$field}\">"
                + "\n{$values union $ints}"
                + "\n</Field>}</Dictionary></DataDescription>";*/
        
        try {
            XmlContainer cont = mgr.openContainer(containerName);
            XmlQueryContext qc = mgr.createQueryContext();
            XmlTransaction txn = mgr.createTransaction();
            XmlResults res = mgr.query(query, qc);
    
            XmlValue value = new XmlValue();
            while ((value = res.next()) != null) {
                output += value.asString();
            }
            txn.commit();
            res.delete();
            closeContainer(cont);
        } catch (XmlException e) {
        	logger.warning("Data description retrieval failed!");
        }
        return output;
    }
    
    private String selectByXPath(String xpath, String document) {
    	try {
    		InputSource bais = new InputSource(new ByteArrayInputStream(document.getBytes()));
		    XPathFactory factory = XPathFactory.newInstance();
		    XPath xp = factory.newXPath();
			XPathExpression expr = xp.compile(xpath);
			return expr.evaluate(bais);
    	} catch (XPathExpressionException e) {
    		logger.warning("Error occured during getting max results restriction! - XPath expression exception");
    		return null;
    	}
    }

    /**
     * Metoda pro dotazovani pomoci vytvoreneho XPath dotazu
     * @param XPathRequest XPath dotaz
     * @return vysledky hledani v SearchResult formatu
     */
    public String queryShortened(String XPathRequest, boolean restructure, boolean exception, int maxResults, InputStream xmlQuery) {
        long startTime = System.currentTimeMillis();
        String output = "";
        String schema = "";
        if (restructure) {
            schema = "http://sewebar.vse.cz/schemas/SearchResult0_2.xsd";
        } else {
            schema = "http://sewebar.vse.cz./schemas/SearchResult0_1.xsd";
        }
        String query = "for $ar in subsequence(" + XPathRequest + ", 1, " + maxResults + ")"
            + "\n return"
            + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
                + "\n {$ar/Text}"
                + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
            + "\n </Hit>";
        String queryResult = query("", query, true);
        if (exception && !queryResult.isEmpty()) {
        	String xpath = qm.getExceptionPath(xmlQuery);
        	queryResult = selectByXPath(xpath, queryResult);
        }
        output += "<SearchResult xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\""+ schema +"\">"
                + "<Metadata>"
                    + "<SearchTimestamp>" + getDateTime() + "</SearchTimestamp>"
                    + "<LastIndexUpdate>2011-05-30T09:00:00</LastIndexUpdate>"
                    + "<SearchAlgorithm>xquery</SearchAlgorithm>"
                    + "<SearchAlgorithmVersion>xquery 3/4/2011</SearchAlgorithmVersion>"
                + "</Metadata>"
                + "<Statistics>"
                    + "<ExecutionTime>" + (System.currentTimeMillis() - startTime) + "</ExecutionTime>"
                    + "<DocumentsSearched>" + query("", "count(collection(\""+ containerName +"\")/PMML)", true) + "</DocumentsSearched>"
                    + "<RulesSearched>" + query("", "count(collection(\""+ containerName +"\")/PMML/AssociationRule)", true) + "</RulesSearched>"
                + "</Statistics>";
        if (restructure) {
            output += "<DataDescription>" + dataDescriptionPrepare("<Hits>" + queryResult + "</Hits>") + "</DataDescription>";
        }
        output += "<Hits>";
        if (restructure) {
            output += restructureOutput("<Hits>"+queryResult+"</Hits>");
        } else {
            output += queryResult;
        }
        output += "</Hits></SearchResult>";
        return output;
    }
    
    
    private String dataDescriptionPrepare(String queryOutput) {
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
        	logger.warning("Error occured during data description preparation! - Unsupported encoding exception");
        	return null;
		} catch (XPathException e) {
			logger.warning("Error occured during data description preparation! - XPath expression exception");
			return null;
		}
    }
    /**
     * Metoda pro zmenu struktury vystupu query
     * @param queryOutput puvodni vystup query
     * @return restrukturovana hodnota
     */
    private String restructureOutput (String queryOutput) {
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
        	logger.warning("Error occured during restructuring output! - Unsupported encoding exception");
        	return null;
		} catch (XPathException e) {
			logger.warning("Error occured during restructuring output! - XPath expression exception");
			return null;
		}
    }
    /**
     * Metoda zajistujici uzavreni pouzivaneho kontejneru
     * @param cont instance XmlContainer
     */
    private void closeContainer (XmlContainer cont) {
        if (cont != null) {
            try {
                cont.close();
            } catch (XmlException ex) {
            }
        }
    }

    /**
     * Metoda pro vypsani data a casu v danem formatu
     * @return aktualni datum a cas
     */
    private String getDateTime(){
        DateFormat df1 = new SimpleDateFormat("yyyy-MM-dd");
        DateFormat df2 = new SimpleDateFormat("HH:mm:ss");
        Date date = new Date();
        return df1.format(date)+"T"+df2.format(date);
    }
}