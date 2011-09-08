package xquery_servlet;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexDeclaration;
import com.sleepycat.dbxml.XmlIndexSpecification;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;
import com.sleepycat.dbxml.XmlValue;
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
import java.util.regex.Pattern;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.stream.StreamResult;
import net.sf.saxon.Configuration;
import net.sf.saxon.query.DynamicQueryContext;
import net.sf.saxon.query.StaticQueryContext;
import net.sf.saxon.query.XQueryExpression;
import net.sf.saxon.trans.XPathException;

/**
 * Trida pro ovladani a komunikaci s Berkeley XML DB
 * @author Tomas Marek
 */
public class BDBXMLHandler {
    XmlManager mgr;
    QueryHandler qh;
    SchemaChecker sc;
    String containerName;
    String useTransformation;
    String xsltPathPMML;
    String xsltPathBKEF;
    Pattern replaceMask = Pattern.compile("[|!@$^* \\//\"\',?ˇ´<>¨;¤×÷§]");
    String replaceBy = "_";
    
    /**
     * Konstruktor
     * @param mgr instance XmlManager
     * @param qh instance tridy QueryHandler
     * @param containerName nazev pouzivaneho kontejneru
     * @param useTransformation pouzit transformaci - true/false
     * @param xsltPath cesta k souboru s xslt transformaci
     */
    public BDBXMLHandler(XmlManager mgr, QueryHandler qh, SchemaChecker sc, String containerName, String useTransformation, String xsltPathPMML, String xsltPathBKEF) {
        this.mgr = mgr;
        this.qh = qh;
        this.sc = sc;
        this.containerName = containerName;
        this.useTransformation = useTransformation;
        this.xsltPathPMML = xsltPathPMML;
        this.xsltPathBKEF = xsltPathBKEF;
    }

    /**
     * Metoda pro vymazani dokumentu z XML DB
     * @param id ID dokumentu v DB
     * @return Zprava - splneno/chyba
     */
    public String removeDocument (String id){
        String output = "";
        try {

            XmlContainer cont = mgr.openContainer(containerName);
            XmlTransaction txn = mgr.createTransaction();

            if (cont.getDocument(id) != null) {
                cont.deleteDocument(id);
                output += "<message>Dokument " + id + " smazan!</message>";
            } else {
                output += "<error>Dokument nenalezen!</error>";
            }
            txn.commit();
            closeContainer(cont);
        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /*public String removeAllDocuments(){
        String output = "";
        try {
            XmlContainer cont = mgr.openContainer(containerName);
            XmlTransaction txn = mgr.createTransaction();
            XmlResults allDocs = cont.getAllDocuments(XmlDocumentConfig.DEFAULT);
            int pocitadlo = 0;
            while (allDocs.hasNext()) {
                if (!allDocs.next().isNull()) {
                    XmlDocument doc = allDocs.next().asDocument();
                    output += "<doc id=\""+ pocitadlo +"\">" + doc.getName().toString() + "</doc>";
                    pocitadlo++;
                }
            }
            txn.commit();
            closeContainer(cont);
        } catch (XmlException ex) {
            output += "<error>" + ex.toString() + "</error>";
        }
        return output;
    }*/
    
    /**
     * Metoda pro zobrazeni dokumentu z XML DB
     * @param id ID dokumentu v DB
     * @return Zobrazeni dokumentu/chyba
     */
    public String getDocument(String id){
        String output = "";
        try {
            XmlContainer cont = mgr.openContainer(containerName);
            XmlTransaction txn = mgr.createTransaction();

            if (cont.getDocument(id) != null) {
                XmlDocument doc = cont.getDocument(id);
                output += doc.getContentAsString();
            } else {
                output += "<error>Dokument nenalezen!</error>";
            }
            txn.commit();
            closeContainer(cont);
        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Cyklycke dotazovani - 10x za sebou stejny dotaz na XML DB (pouzito pri testovani)
     * @param search XQuery dotaz
     * @return cas a vysledky dotazovani 
     */
    public String query_10(String search) {
        String output = "";
        String output_temp = "";
        QueryMaker qm = new QueryMaker(containerName);
        InputStream is = new ByteArrayInputStream(qh.queryPrepare(search).toByteArray());
        String xpath = qm.makeXPath(is);
        for (int i=0; i<10; i++){
            output += "<pokus cislo=\""+ i +"\">";
            double time_start = System.currentTimeMillis();
            output_temp = queryShortened(xpath, false);
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
     * Metoda slouzici k vyhledavani v BDB XML pomoci XQuery
     * Moznosti zadani - prima XQuery/pouziti ulozene XQuery a pridani vstupniho dotazu
     * @param id ID ulozene XQuery
     * @param search vstupni dotaz pro XQuery
     * @param type typ pouzite XQuery - 0 pro primou, 1 pro ulozenou
     * @return vysledek vyhledavani
    */
    public String query(String id, String search, int type){
        String output = "";
        int chyba = 0;
        
        try {
            XmlContainer cont = mgr.openContainer(containerName);
            String query = "";
            if (type == 0) {
                query = search;
            } else {
                    if (qh.getQuery(id)[0].toString().equals("1")) {
                        output = qh.getQuery(id)[1].toString();
                        chyba = 1;
                    } else {
                        query = qh.getQuery(id)[1].toString();
                        query += "\nlet $zadani := " + search
                                + "\nreturn local:mainFunction($zadani)";
                    }
            }
            if (chyba != 1) {
            	query = qh.deleteDeclaration(query);
	            XmlQueryContext qc = mgr.createQueryContext();
	            XmlTransaction txn = mgr.createTransaction();
	            XmlResults res = mgr.query(query, qc);

	            if (res != null) {
	            // Process results -- just print them
	                    XmlValue value = new XmlValue();
	                    while ((value = res.next()) != null) {
	                        output += (value.asString());
	                    }
	            } else {
	                output = "<error>Zadny vysledek</error>";
	            }
	            txn.commit();
	            res.delete();
	            closeContainer(cont);
            }
	        } catch (XmlException e) {
	                output += "<error>"+e.toString()+"</error>";
	        } catch (Throwable e) {
	                output += "<error>"+e.toString()+"</error>";
	        }
        return output;
    }

    /**
     * !!! NEPOUZITO !!!
     * Metoda pro vlozeni vice dokumentu najednou,
     * dokumenty rozdeleny sekvenci znaku ;;;NEXTPMML;;;
     * @param docs vsechny tela vkladanych dokumentu oddelene danou sekvenci znaku
     * @param names vsechny ID vkladanych dokumentu oddelene danou sekvenci znaku
     * @return Zprava pro kazdy dokument - vlozeno/chyba
     */

    /*public String[] moreDocuments(String docs, String names){
    String output[] = new String[2];
        output[1] = "";
        long cas_zacatek = System.currentTimeMillis();
        long cas_konec = 0;
        String[] dokumenty = docs.split(";;;NEXTPMML;;;");
        String[] jmena = names.split(";;;NEXTPMML;;;");
        if (dokumenty.length != jmena.length){
                output[1] = "<error>Nastala chyba!!!</error>";
        } else {
                for (int i = 0; i < dokumenty.length; i++){
                    output[1] += "\n" + indexDocument(dokumenty[i], jmena[i], mgr);
                }
        cas_konec = System.currentTimeMillis();
        output[0] = "" + ((cas_konec - cas_zacatek)/1000);
        }
        return output;
    }*/

    /**
     * Metoda pro ziskani nazvu dokumentu ulozenych v XML DB
     * @return seznam ulozenych dokumentu
     */
    public String getDocsNames(){
        String output = "";
        try {
            String query = "let $docs := for $x in collection(\""+containerName+"\") return $x"
                    + "\nreturn"
                    + "\n<docs count=\"{count($docs)}\">{for $a in $docs"
                    + "\norder by dbxml:metadata(\"dbxml:name\", $a)"
                    + "\nreturn  <doc joomlaID=\"{$a/PMML/@joomlaID}\" timestamp=\"{$a/PMML/@creationTime}\" reportUri=\"{$a/PMML/@reportURI}\" database=\"{$a/PMML/@database}\" table=\"{$a/PMML/@table}\">{dbxml:metadata(\"dbxml:name\", $a)}</doc>}</docs>";

            XmlContainer cont = mgr.openContainer(containerName);
            XmlQueryContext qc = mgr.createQueryContext();
            XmlTransaction txn = mgr.createTransaction();
            XmlResults res = mgr.query(query, qc);

            XmlValue value = new XmlValue();
            while ((value = res.next()) != null) {
                output += value.asString();
            }
            txn.commit();
            closeContainer(cont);
        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro vlozeni dokumentu do XML DB
     * @param document telo dokumentu (String)
     * @param docID id dokumentu (joomlaID)
     * @param docName nazev doumentu (pro ulozeni v XMLDB)
     * @param creationTime datum a cas vytvoreni dokumentu
     * @param reportUri url adresa reportu
     * @return informace o ulozeni/chybe
     */
    public String indexDocument(String document, String docID, String docName, String creationTime, String reportUri) throws IOException{
        String output = "";
        String xml_doc = "";
        String validation[] = null;
        File xsltFile;
            try {
                if (useTransformation.equals("true")) {
                    if (document.contains("sourceType=\"BKEF\"")) {
                        xsltFile = new File(xsltPathBKEF);
                    } else {
                        validation = sc.validate(document);
                        xsltFile = new File(xsltPathPMML);
                    }
                    XSLTTransformer xslt = new XSLTTransformer();
                    xml_doc += xslt.xsltTransformation(document, xsltFile, docID, creationTime, reportUri);
                } else {
                    xml_doc = document;
                }
            if(validation == null || (validation != null && validation[0].equals("1"))){        
                XmlContainer cont = mgr.openContainer(containerName);
                XmlTransaction txn = mgr.createTransaction();

                docName = docName.replaceAll(replaceMask.toString(), replaceBy);

                cont.putDocument(docName, xml_doc);
                output += "<message>Dokument " + docName + " vlozen</message>";

                txn.commit();
                closeContainer(cont);
            } else {
                output += "<error>"+validation[1]+"</error>";
            }
            } catch (XmlException e) {
                    output += "<error>"+e.toString()+"</error>";
            } catch (Throwable e) {
                    output += "<error>"+e.toString()+"</error>";
            }
        return output;
    }

    /**
     * Metoda pro vlozeni dokumentu do XML DB
     * @param document telo dokumentu (File)
     * @param docID id dokumentu (joomlaID)
     * @param docName nazev doumentu (pro ulozeni v XMLDB)
     * @param creationTime datum a cas vytvoreni dokumentu
     * @param reportUri url adresa reportu
     * @return zprava - ulozeno/chyba
     */
    public String indexDocument(File document, String docID, String docName, String creationTime, String reportUri) throws FileNotFoundException, IOException{
        String xml_doc = "";
        String output = "";
        long act_time_long = System.currentTimeMillis();

        FileReader rdr = null;
        BufferedReader out = null;
        rdr = new FileReader(document);
        out = new BufferedReader(rdr);
        String radek = out.readLine();
        while (radek != null){
            xml_doc += radek + "\n";
            radek = out.readLine();
        }

        String validation[] = sc.validate(xml_doc);
        if(validation[0].equals("1")){
            try {
                if (useTransformation.equals("true")) {
                    File xsltFile = new File(xsltPathPMML);
                    XSLTTransformer xslt = new XSLTTransformer();
                    xml_doc = xslt.xsltTransformation(xml_doc, xsltFile, docID, creationTime, reportUri);
                    output += "<xslt_time>" + (System.currentTimeMillis() - act_time_long) + "</xslt_time>";
               }

                XmlContainer cont = mgr.openContainer(containerName);
                XmlTransaction txn = mgr.createTransaction();

                docName = docName.replaceAll(replaceMask.toString(), replaceBy);

                cont.putDocument(docName, xml_doc);
                output += "<message>Dokument " + docName + " vlozen</message>";
                output += "<doc_time>" + (System.currentTimeMillis() - act_time_long) + "</doc_time>";
                txn.commit();
                closeContainer(cont);
                } catch (XmlException e) {
                    output += "<error>"+e.toString()+"</error>";
            } catch (Throwable e) {
                    output += "<error>"+e.toString()+"</error>";
            }
        } else {
            output += "<error>"+validation[1]+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro nahrani vice dokumentu ze slozky
     * @param folder slozka, ze ktere se maji soubory nahrat
     * @return zprava o ulozeni / chyba
     */
    public String indexDocumentMultiple (String folder) throws FileNotFoundException, IOException {
        String output = "";
        File uploadFolder = new File(folder);
        File uploadFiles[] = uploadFolder.listFiles();
        
        for(int i = 0; i < uploadFiles.length; i++){
            output += indexDocument(uploadFiles[i], "", uploadFiles[i].getName(), new Date().toString(), "");
        }
        return output;
    }

    /**
     * Metoda pro pridani indexu XML DB
     * @param index zadani indexu - namespace;node;index type
     * @return zprava o pridani indexu / chybe
     */
    public String addIndex(String index) {
        String output = "";

        try {
            XmlContainer cont = mgr.openContainer(containerName);

            XmlTransaction txn = mgr.createTransaction();
            String[] indexPole = index.split(";");
            if (indexPole.length == 3) {
                XmlIndexSpecification indexSpec = cont.getIndexSpecification();
                indexSpec.addIndex(indexPole[0], indexPole[1], indexPole[2]);
                cont.setIndexSpecification(indexSpec);
                output = "<message>Index " + index + " pridan</message>";

                txn.commit();
                indexSpec.delete();
                closeContainer(cont);
            } else {
                output = "<error>Spatne zadany index</error>";
            }
        } catch (XmlException e) {
                output += "<error>"+e.toString()+"</error>";
        }catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda zajistujici smazani indexu
     * @param index zadani indexu - namespace;node;index type
     * @return zprava o smazani indexu / chybe
     */
    public String delIndex (String index){
        String output = "";

        try {
            XmlContainer cont = mgr.openContainer(containerName);
            XmlTransaction txn = mgr.createTransaction();
            String[] indexPole = index.split(";");

            if (indexPole.length == 3) {
                XmlIndexSpecification indexSpec = cont.getIndexSpecification();
                indexSpec.deleteIndex(indexPole[0], indexPole[1], indexPole[2]);
                cont.setIndexSpecification(indexSpec);
                output = "<message>Index " + index + " odebran</message>";

                txn.commit();
                indexSpec.delete();
                closeContainer(cont);
            } else {
                output = "<error>Spatne zadany index</error>";
            }
        } catch (XmlException ex) {
            //Logger.getLogger(BDBXMLHandler.class.getName()).log(Level.SEVERE, null, ex);
            output += "<error>"+ex.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro zobrazeni indexu v XML DB
     * @return vypis pouzivanych indexu v XML DB
     */
    public String listIndex() {
        String output = "";
        String outputEnd = "";
        try {
            XmlContainer cont = mgr.openContainer(containerName);
            XmlIndexSpecification indexSpec = cont.getIndexSpecification();

            int count = 0;
            XmlIndexDeclaration indexDecl = null;
            while ((indexDecl = (indexSpec.next())) != null) {
                outputEnd += "<index>"
                            + "<nodeName>" + indexDecl.name + "</nodeName>"
                            + "<indexType>" + indexDecl.index + "</indexType>"
                        + "</index>";
                count++;
            }
            output += "<indexCount>" + count + "</indexCount>" + outputEnd;
            indexSpec.delete();
            closeContainer(cont);
        } catch (XmlException ex) {
            //Logger.getLogger(BDBXMLHandler.class.getName()).log(Level.SEVERE, null, ex);
            output += "<error>"+ex.toString()+"</error>";
        }
        return output;
    }
    
    /**
     * Metoda pro ziskani cachovaneho DataDescription
     * @return DataDescription / chyba
     */
    public String getDataDescriptionCache() {
        String output = "";
        try {
            XmlContainer cont = mgr.openContainer("__DataDescriptionCacheContainer");
            XmlTransaction txn = mgr.createTransaction();
            XmlDocument doc = cont.getDocument("__DataDescriptionCacheDocument");
            output += doc.getContentAsString();
            txn.commit();
            closeContainer(cont);
            } catch (XmlException ex) {
                if (ex.getErrorCode() == XmlException.DOCUMENT_NOT_FOUND) {
                    output += "<error>Chyba cache - Document not found</error>";
                } else if (ex.getErrorCode() == XmlException.CONTAINER_NOT_FOUND) {
                    output += "<error>Chyba cache - Container not found</error>";
                }
                output += "<error>"+ex.toString()+"</error>";
            }
        return output;
    }
    
    /**
     * Metoda pro ulozeni aktualniho DataDescription do cache
     * @return Provedeno / chyba
     */
    public String actualizeDataDescriptionCache() {
        String output = "";
        XmlContainer cont = null;
        try {
            cont = mgr.openContainer("__DataDescriptionCacheContainer");
            XmlTransaction txn = mgr.createTransaction();
            String dataDescription = getDataDescription();
            cont.putDocument("__DataDescriptionCacheDocument", dataDescription);
            output += "<message>DataDescription cache aktualizovan</message>";
            
            txn.commit();
            closeContainer(cont);
            } catch (XmlException ex) {
                if (ex.getErrorCode() == XmlException.CONTAINER_NOT_FOUND) {
                    try {
                        cont = mgr.createContainer("__DataDescriptionCacheContainer");
                        cont.setAutoIndexing(false);
                        actualizeDataDescriptionCache();
                    } catch (XmlException ex1) {
                        output += "<error>"+ex1.toString()+"</error>";
                    }
                } else if (ex.getErrorCode() == XmlException.UNIQUE_ERROR) {
                    try {
                        cont.deleteDocument("__DataDescriptionCacheDocument");
                        actualizeDataDescriptionCache();
                    } catch (XmlException ex1) {
                        output += "<error>"+ex1.toString()+"</error>";
                    }
                } else {
                    output += "<error>"+ex.toString()+"</error>";
                }
            }
        return output;
    }
    /**
     * Metoda pro vytvoreni DataDescription dat ulozenych v XML DB
     * @return DataDescription
     */
    public String getDataDescription(){
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
            } catch (XmlException ex) {
                //Logger.getLogger(BDBXMLHandler.class.getName()).log(Level.SEVERE, null, ex);
                output += "<error>"+ex.toString()+"</error>";
            }
        return output;
    }

    /**
     * Metoda pro dotazovani pomoci vytvoreneho XPath dotazu
     * @param XPathRequest XPath dotaz
     * @return vysledky hledani v SearchResult formatu
     */
    public String queryShortened(String XPathRequest, boolean restructure){
        long startTime = System.currentTimeMillis();
        String output = "";
        String schema = "";
        if (restructure) {
            schema = "http://sewebar.vse.cz/schemas/SearchResult0_2.xsd";
        } else {
            schema = "http://sewebar.vse.cz./schemas/SearchResult0_1.xsd";
        }
        String query = "for $ar in " + XPathRequest
            + "\n return"
            + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
                + "\n {$ar/Text}"
                + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
            + "\n </Hit>";
        String queryResult = query("", query, 0);
        output += "<SearchResult xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
                + "xsi:noNamespaceSchemaLocation=\""+ schema +"\">"
                + "<Metadata>"
                    + "<SearchTimestamp>" + getDateTime() + "</SearchTimestamp>"
                    + "<LastIndexUpdate>2011-05-30T09:00:00</LastIndexUpdate>"
                    + "<SearchAlgorithm>xquery</SearchAlgorithm>"
                    + "<SearchAlgorithmVersion>xquery 3/4/2011</SearchAlgorithmVersion>"
                + "</Metadata>"
                + "<Statistics>"
                    + "<ExecutionTime>" + (System.currentTimeMillis() - startTime) + "</ExecutionTime>"
                    + "<DocumentsSearched>" + query("", "count(collection(\""+ containerName +"\")/PMML)", 0) + "</DocumentsSearched>"
                    + "<RulesSearched>" + query("", "count(collection(\""+ containerName +"\")/PMML/AssociationRule)", 0) + "</RulesSearched>"
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
        String output = "";
        String ddPrepareQuery = "declare function local:descriptionTransform($inputData) {"
                + "let $dataDictOutput := <Dictionary sourceDictType=\"DataDictionary\" sourceFormat=\"PMML\" default=\"true\" completeness=\"ReferencedFromPatterns\" id=\"DataDictionary\">"
                + "            { for $bbaName in distinct-values($inputData/DataDictionary/FieldName)"
                + "                let $cats := for $cat in distinct-values($inputData/DataDictionary[FieldName=$bbaName]/CatName) return <Category>{$cat}</Category>"
                + "                let $intsAll := for $int in $inputData/DataDictionary[FieldName=$bbaName]/Interval return $int"
                + "                let $ints := for $left in distinct-values($intsAll/@left) return for $right in distinct-values($intsAll[@left = $left]/@right) return <Interval leftMargin=\"{$left}\" rightMargin=\"{$right}\" closure=\"{distinct-values($intsAll[@left = $left and @right = $right]/@type)}\"/>"
                + "            return <Field id=\"{concat(\"f\",index-of($inputData//DataDictionary/FieldName, $bbaName)[1])}\"><Name>{$bbaName}</Name>{$cats, $ints}</Field>}"
                + "            </Dictionary>"
                + "let $transDictOutput := <Dictionary sourceDictType=\"DiscretizationHint\" sourceFormat=\"PMML\" default=\"true\" completeness=\"ReferencedFromPatterns\" id=\"TransformationDictionary\">"
                + "            {for $bbaName in distinct-values($inputData/TransformationDictionary/FieldName)"
                + "                let $cats := for $cat in distinct-values($inputData/TransformationDictionary[FieldName=$bbaName]/CatName) return <Category>{$cat}</Category>"
                + "                let $intsAll := for $int in $inputData/TransformationDictionary[FieldName=$bbaName]/Interval return $int"
                + "                let $ints := for $left in distinct-values($intsAll/@left) return for $right in distinct-values($intsAll[@left = $left]/@right) return <Interval leftMargin=\"{$left}\" rightMargin=\"{$right}\" closure=\"{distinct-values($intsAll[@left = $left and @right = $right]/@type)}\"/>"
                + "            return <Field id=\"{concat(\"f\",index-of($inputData//TransformationDictionary/FieldName, $bbaName)[1])}\"><Name>{$bbaName}</Name>{$cats, $ints}</Field>}"
                + "            </Dictionary>"
                + "let $mappingOutput := <DictionaryMapping>"
                + "            {for $ddName in distinct-values($inputData/DataDictionary/FieldName)"
                + "                let $id := $dataDictOutput/Field[index-of($dataDictOutput/Field/Name, $ddName)]/@id"
                + "                let $tdNames := for $tdName in distinct-values($inputData[DataDictionary/FieldName=$ddName]/TransformationDictionary/FieldName) return $tdName"
                + "                let $valueMappings := if (count($inputData/DataDictionary[FieldName=$ddName]/Interval) > 0) then"
                + "                    for $intervalLeft in distinct-values($inputData/DataDictionary[FieldName=$ddName]/Interval/@left)"
                + "                        return for $intervalRight in distinct-values($inputData/DataDictionary[FieldName=$ddName and Interval/@left = $intervalLeft]/Interval/@right)"
                + "                        return for $intervalClosure in distinct-values($inputData/DataDictionary[FieldName=$ddName and Interval/@left = $intervalLeft and Interval/@right = $intervalRight]/Interval/@type)"
                + "                        let $tdValues := for $tdValue in distinct-values($inputData[DataDictionary/FieldName=$ddName and DataDictionary/Interval/@left = $intervalLeft and DataDictionary/Interval/@right = $intervalRight and DataDictionary/Interval/@type = $intervalClosure]/TransformationDictionary/CatName)"
                + "                        return $tdValue"
                + "                    return <IntervalMapping><Field><Interval leftMargin=\"{$intervalLeft}\" rightMargin=\"{$intervalRight}\" closure=\"{$intervalClosure}\" /></Field><Field>{for $tdValueOut in $tdValues return <CatRef>{$tdValueOut}</CatRef>}</Field></IntervalMapping>"
                + "                else"
                + "                    for $ddValue in distinct-values($inputData/DataDictionary[FieldName=$ddName]/CatName)"
                + "                        let $tdValues := for $tdValue in distinct-values($inputData[DataDictionary/FieldName = $ddName and DataDictionary/CatName = $ddValue]/TransformationDictionary/CatName) return $tdValue"
                + "                    return <ValueMapping><Field><CatRef>{$ddValue}</CatRef></Field><Field>{for $tdValueOut in $tdValues return <CatRef>{$tdValueOut}</CatRef>}</Field></ValueMapping>"
                + "            return <FieldMapping><AppliesTo>"
                + "            <FieldRef id=\"{$id}\" dictID=\"DataDictionary\"><Name>{$ddName}</Name></FieldRef>"
                + "            <FieldRef id=\"{$id}\" dictID=\"TransformationDictionary\">{for $tdNameOut in $tdNames return <Name>{$tdNameOut}</Name>}</FieldRef>"
                + "            </AppliesTo>{$valueMappings}</FieldMapping>}"
                + "            </DictionaryMapping>"
                + "return $dataDictOutput union $transDictOutput union $mappingOutput"
                + "};"
                + "let $dd := " + queryOutput
                + "return local:descriptionTransform($dd//BBA)";
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        try {
            Configuration config = new Configuration();
            StaticQueryContext sqc = config.newStaticQueryContext();
            XQueryExpression xqe = sqc.compileQuery(ddPrepareQuery);
            DynamicQueryContext dqc = new DynamicQueryContext(config);
            Properties props = new Properties();
            props.setProperty(OutputKeys.METHOD, "html");
            props.setProperty(OutputKeys.INDENT, "no");
            xqe.run(dqc, new StreamResult(baos), props);
            output += baos.toString("UTF-8");
        } catch (UnsupportedEncodingException ex) {
            
        } catch (XPathException ex) {
            //output += "<error>" + ex.toString() + "</error>";
        }
        return output;
    }
    /**
     * Metoda pro zmenu struktury vystupu query
     * @param queryOutput puvodni vystup query
     * @return restrukturovana hodnota
     */
    private String restructureOutput (String queryOutput) {
        String output = "";
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
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        try {
            Configuration config = new Configuration();
            StaticQueryContext sqc = config.newStaticQueryContext();
            XQueryExpression xqe = sqc.compileQuery(restructureQuery);
            DynamicQueryContext dqc = new DynamicQueryContext(config);
            Properties props = new Properties();
            props.setProperty(OutputKeys.METHOD, "html");
            props.setProperty(OutputKeys.INDENT, "no");
            xqe.run(dqc, new StreamResult(baos), props);
            output += baos.toString("UTF-8");
        } catch (UnsupportedEncodingException ex) {
            
        } catch (XPathException ex) {
            //output += "<error>" + ex.toString() + "</error>";
        }
        return output;
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