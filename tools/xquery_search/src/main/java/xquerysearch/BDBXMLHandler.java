//package xquerysearch;
//
//
//import java.io.BufferedReader;
//import java.io.ByteArrayInputStream;
//import java.io.File;
//import java.io.FileNotFoundException;
//import java.io.FileReader;
//import java.io.IOException;
//import java.io.InputStream;
//import java.text.DateFormat;
//import java.text.SimpleDateFormat;
//import java.util.Date;
//import java.util.List;
//import java.util.logging.Logger;
//import java.util.regex.Pattern;
//
//import javax.xml.xpath.XPath;
//import javax.xml.xpath.XPathExpression;
//import javax.xml.xpath.XPathExpressionException;
//import javax.xml.xpath.XPathFactory;
//
//import org.springframework.beans.factory.annotation.Autowired;
//import org.xml.sax.InputSource;
//
//import xquerysearch.controller.MainController;
//import xquerysearch.dao.DocumentDao;
//import xquerysearch.dao.IndexDao;
//import xquerysearch.dao.ResultsDao;
//import xquerysearch.domain.Document;
//import xquerysearch.domain.Query;
//import xquerysearch.domain.Result;
//import xquerysearch.service.StoredQueryService;
//import xquerysearch.transformation.XsltTransformer;
//import xquerysearch.utils.OutputUtils;
//import xquerysearch.utils.QueryUtils;
//import xquerysearch.validation.DocumentValidator;
//
///**
// * 
// * @author Tomas Marek
// */
//public class BDBXMLHandler {
//	private Logger logger = MainController.getLogger();
//	@Autowired
//	private DocumentDao documentDao;
//	
//	@Autowired
//	private ResultsDao resultsDao;
//	
//	@Autowired
//	private IndexDao indexDao;
//    
//	private QueryUtils queryUtils = new QueryUtils();
//	
//    private String containerName;
//    private String validationSchemePath;
//    boolean useTransformation;
//    private String xsltPathPMML;
//    private String xsltPathBKEF;
//    
//    private Pattern replaceMask = Pattern.compile("[|!@$^* \\//\"\',?ˇ´<>¨;¤×÷§]");
//    private String replaceBy = "_";
//    
//    private StoredQueryService storedQueryService;
//    
//    /**
//     * Removes document
//     * @param id document id
//     * @return message
//     */
//    public String removeDocument (String id) {
//        if (documentDao.removeDocument(id)) {
//        	return "<message>Document with id \"" + id + "\" removed!</message>";
//        } else {
//            return "<error>Removing document with id \"" + id + "\" failed!</error>";
//        }
//    }
//
//    /**
//     * Gets the document by id
//     * @param id document ID
//     * @return document as string or message when error occurs
//     */
//    public String getDocument(String id) {
//    	Document doc = documentDao.getDocumentById(id);
//    	if (doc != null) {
//    		return doc.getDocBody();
//    	} else {
//    		return "<error>Cannot find the document with id \"" + id + "\"</error>";
//    	}
//    }
//
//    /**
//     * Cyclic querying (10 times)
//     * @param search query content
//     * @return time spent and results
//     */
//    public String query_10(String search) {
//        String output = "";
//        String output_temp = "";
//        InputStream query = new ByteArrayInputStream(QueryUtils.queryPrepare(search).toByteArray());
//        InputStream query2 = new ByteArrayInputStream(QueryUtils.queryPrepare(search).toByteArray());
//        String xpath = "";//QueryUtils.makeXPath(query, containerName)[0];
//        for (int i=0; i<10; i++){
//            output += "<pokus cislo=\""+ i +"\">";
//			double time_start = System.currentTimeMillis();
//            output_temp = queryShortened(xpath, false, false, 9999, query2);
//            //output_temp = query("", search, 0);
//            output += "<time>"+ (System.currentTimeMillis() - time_start) +"</time>";
//            if (i == 9){
//                output += output_temp;
//            }
//            output += "</pokus>";
//        }
//        return output;
//    }
//
//   /**
//    * Method for querying repository 
//    * @param id query id (of saved query)
//    * @param search (query content)
//    * @param directQuery use query directly
//    * @return
//    */
//    public String query(String id, String search, boolean directQuery) {
//        String query = "";
//        if (directQuery) {
//            query = search;
//        } else {
//        	query = storedQueryService.getQuery(id);	
//        	if (query != null) {
//        		query += "\nlet $zadani := " + search
//        		+ "\nreturn local:mainFunction($zadani)";
//        	}
//        }
//    	query = QueryUtils.deleteDeclaration(query);
//        
//    	List<Result> results = resultsDao.getResultsByQuery(new Query(query));
//
//        if (results != null) {
//            String resultsToPrint = "";
//        	for (Result result : results) {
//        		resultsToPrint += result.getResultBody();
//        	}
//            return resultsToPrint;
//        } else {
//            return "<error>No results found</error>";
//        }
//    }
//
//    /**
//     * Gets documents names saved in repository
//     * @return documents names list / error message
//     */
//    public String getDocsNames() {
//        String query = "let $docs := for $x in collection(\"" + containerName + "\") return $x"
//                + "\nreturn"
//                + "\n<docs count=\"{count($docs)}\">{for $a in $docs"
//                + "\norder by dbxml:metadata(\"dbxml:name\", $a)"
//                + "\nreturn  <doc joomlaID=\"{$a/PMML/@joomlaID}\" timestamp=\"{$a/PMML/@creationTime}\" reportUri=\"{$a/PMML/@reportURI}\" database=\"{$a/PMML/@database}\" table=\"{$a/PMML/@table}\">{dbxml:metadata(\"dbxml:name\", $a)}</doc>}</docs>";
//
//        List<Result> results = resultsDao.getResultsByQuery(new Query(query));
//
//        String resultsToPrint = "";
//        for (Result result : results) {
//        	resultsToPrint += result.getResultBody();
//        }
//        return resultsToPrint;
//    }
//
//    /**
//     * Saves document into repository
//     * @param document document content
//     * @param docID document id (is injected into document)
//     * @param docName document name (by this name is saved into repository)
//     * @param creationTime
//     * @param reportUri URI of the report
//     * @return message - success/failure
//     */
//    public String indexDocument(String document, String docID, String docName, String creationTime, String reportUri) {
//        String xml_doc = "";
//        boolean isValid = false;
//        File xsltFile;
//            if (useTransformation) {
//                if (document.contains("sourceType=\"BKEF\"")) {
//                    xsltFile = new File(xsltPathBKEF);
//                } else {
//                    isValid = DocumentValidator.validate(document, validationSchemePath);
//                    xsltFile = new File(xsltPathPMML);
//                }
//                xml_doc += XsltTransformer.transform(document, xsltFile, docID, creationTime, reportUri);
//            } else {
//                xml_doc = document;
//            }
//        if (isValid){        
//            docName = docName.replaceAll(replaceMask.toString(), replaceBy);
//
//            boolean saved = documentDao.insertDocument(new Document(docName, document));
//
//            if (saved) {
//            	return "<message>Document " + docName + " inserted</message>";
//            } else {
//            	return "<error>Error occured during document saving occured</error>";
//            }
//        } else {
//            return "<error>Document validation failed</error>";
//        }
//    }
//
//    /**
//     * Saves document into repository
//     * @param document document content
//     * @param docID document id (is injected into document)
//     * @param docName document name (by this name is saved into repository)
//     * @param creationTime
//     * @param reportUri URI of the report
//     * @return message - success/failure
//     */
//    public String indexDocument(File document, String docID, String docName, String creationTime, String reportUri) {
//        String xml_doc = "";
//        String output = "";
//        long act_time_long = System.currentTimeMillis();
//
//        FileReader rdr = null;
//        BufferedReader out = null;
//        try {
//	        rdr = new FileReader(document);
//	        out = new BufferedReader(rdr);
//	        String radek = out.readLine();
//	        while (radek != null){
//	            xml_doc += radek + "\n";
//	            radek = out.readLine();
//	        }
//        } catch (FileNotFoundException e) {
//        	return "<error></error>";
//        } catch (IOException e) {
//        	return "<error></error>";
//        }
//        boolean isValid = DocumentValidator.validate(xml_doc, validationSchemePath);
//        if(isValid){
//	        if (useTransformation) {
//	            File xsltFile = new File(xsltPathPMML);
//	            xml_doc = XsltTransformer.transform(xml_doc, xsltFile, docID, creationTime, reportUri);
//	            output += "<xslt_time>" + (System.currentTimeMillis() - act_time_long) + "</xslt_time>";
//	       }
//	
//	        docName = docName.replaceAll(replaceMask.toString(), replaceBy);
//	
////	        documentDao.insertDocument(new PmmlDocument(docName, document));
//	        output += "<message>Document " + docName + " inserted</message>";
//	        output += "<doc_time>" + (System.currentTimeMillis() - act_time_long) + "</doc_time>";
//        } else {
//        	return "<error>Document validation failed</error>";
//        }
//        return output;
//    }
//
//    /**
//     * Allows upload multiple files from folder (local - e.g. server folder)
//     * @param folder folder path
//     * @return result message
//     */
//    public String indexDocumentMultiple (String folder) {
//        String output = "";
//    	File uploadFolder = new File(folder);
//        File uploadFiles[] = uploadFolder.listFiles();
//        
//        for(int i = 0; i < uploadFiles.length; i++){
//            output += indexDocument(uploadFiles[i], "", uploadFiles[i].getName(), new Date().toString(), "");
//        }
//        return output;
//    }
//
//    /**
//     * Adds index into repository
//     * @param index index specified like namespace;node;index type
//     * @return message for success/failure
//     */
//    public String addIndex(String index) {
//        boolean saved = indexDao.insertIndex(index);
//        if (saved) {
//        	return "<message>Index \"" + index + "\" added!</message>";
//        } else {
//        	return "<error>Index malspecified: " + index + "!</error>";
//        }
//    }
//
//    /**
//     * Removes index from repository
//     * @param index index specified like namespace;node;index type
//     * @return message for success/failure
//     */
//    public String removeIndex (String index){
//    	boolean removed = indexDao.removeIndex(index);
//    	if (removed) {
//    		return "<message>Index \"" + index + "\" removed!</message>";
//        } else {
//        	return "<error>Index malspecified: " + index + "!</error>";
//        }
//    }
//
//    /**
//     * 
//     * @return list of indexes or error message
//     */
//    public String listIndex() {
////    	String indexes = indexDao.getAllIndexes();
////        if (indexes != null) {
////        	return indexes;
////        } else {
////        	return "<error>Error occured during listing indexes!</error>";
////        }
//    	return null;
//    }
//    
//    private String selectByXPath(String xpath, String document) {
//    	try {
//    		InputSource bais = new InputSource(new ByteArrayInputStream(document.getBytes()));
//		    XPathFactory factory = XPathFactory.newInstance();
//		    XPath xp = factory.newXPath();
//			XPathExpression expr = xp.compile(xpath);
//			return expr.evaluate(bais);
//    	} catch (XPathExpressionException e) {
//    		logger.warning("Error occured during getting max results restriction! - XPath expression exception");
//    		return null;
//    	}
//    }
//
//    /**
//     * Metoda pro dotazovani pomoci vytvoreneho XPath dotazu
//     * @param XPathRequest XPath dotaz
//     * @return vysledky hledani v SearchResult formatu
//     */
//    public String queryShortened(String XPathRequest, boolean restructure, boolean exception, int maxResults, InputStream xmlQuery) {
//        long startTime = System.currentTimeMillis();
//        String output = "";
//        String schema = "";
//        if (restructure) {
//            schema = "http://sewebar.vse.cz/schemas/SearchResult0_2.xsd";
//        } else {
//            schema = "http://sewebar.vse.cz./schemas/SearchResult0_1.xsd";
//        }
//        String query = "for $ar in subsequence(" + XPathRequest + ", 1, " + maxResults + ")"
//            + "\n return"
//            + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
//                + "\n {$ar/Text}"
//                + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
//            + "\n </Hit>";
//        String queryResult = query("", query, true);
//        if (exception && !queryResult.isEmpty()) {
//        	String xpath = queryUtils.getExceptionPath(xmlQuery);
//        	queryResult = selectByXPath(xpath, queryResult);
//        }
//        output += "<SearchResult xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\""+ schema +"\">"
//                + "<Metadata>"
//                    + "<SearchTimestamp>" + getDateTime() + "</SearchTimestamp>"
//                    + "<LastIndexUpdate>2011-05-30T09:00:00</LastIndexUpdate>"
//                    + "<SearchAlgorithm>xquery</SearchAlgorithm>"
//                    + "<SearchAlgorithmVersion>xquery 3/4/2011</SearchAlgorithmVersion>"
//                + "</Metadata>"
//                + "<Statistics>"
//                    + "<ExecutionTime>" + (System.currentTimeMillis() - startTime) + "</ExecutionTime>"
//                    + "<DocumentsSearched>" + query("", "count(collection(\""+ containerName +"\")/PMML)", true) + "</DocumentsSearched>"
//                    + "<RulesSearched>" + query("", "count(collection(\""+ containerName +"\")/PMML/AssociationRule)", true) + "</RulesSearched>"
//                + "</Statistics>";
//        if (restructure) {
//            output += "<DataDescription>" + OutputUtils.prepareDataDescription("<Hits>" + queryResult + "</Hits>") + "</DataDescription>";
//        }
//        output += "<Hits>";
//        if (restructure) {
//            output += OutputUtils.restructureOutput("<Hits>"+queryResult+"</Hits>");
//        } else {
//            output += queryResult;
//        }
//        output += "</Hits></SearchResult>";
//        return output;
//    }
//
//    /**
//     * Metoda pro vypsani data a casu v danem formatu
//     * @return aktualni datum a cas
//     */
//    private String getDateTime(){
//        DateFormat df1 = new SimpleDateFormat("yyyy-MM-dd");
//        DateFormat df2 = new SimpleDateFormat("HH:mm:ss");
//        Date date = new Date();
//        return df1.format(date)+"T"+df2.format(date);
//    }
//}