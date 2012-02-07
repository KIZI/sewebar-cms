package xquerysearch;

import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlManager;
import java.io.IOException;

import javax.xml.transform.TransformerException;

import org.xml.sax.SAXException;

/**
 * Trida provadi vypis nastaveni a otestovani funkci aplikace
 * @author Tomas Marek
 */
public class Tester {
	QueryHandler qh;
	BDBXMLHandler bh;
	XmlManager mgr;
	String envDir;
	String queryDir;
	String containerName;
	String useTransformation;
	String xsltPathPMML;
        String xsltPathBKEF;
	String tempDir;
	String settingsError;

	/**
	 * Konstruktor
	 * @param qh instance tridy QueryHandler
         * @param bh instance tridy BDBXMLHandler
         * @param mgr instance XmlManager
         * @param envDir umisteni DB
         * @param queryDir slozka pro ukladani query
         * @param containerName nazev pouzivaneho kontejneru
         * @param useTransformation true/false, zda pouzivat xslt transformaci pri ukladani dokumentu
         * @param xsltPath umisteni xslt transformace
         * @param tempDir slozka pro docasne soubory
         * @param settingsError chyby pri nacitani nastaveni
	 */
    public Tester(QueryHandler qh, BDBXMLHandler bh, XmlManager mgr, String envDir, String queryDir, String containerName, String useTransformation, String xsltPathPMML, String xsltPathBKEF, String tempDir, String settingsError) {
    	this.qh = qh;
    	this.bh = bh;
    	this.mgr = mgr;
    	this.envDir = envDir;
    	this.queryDir = queryDir;
    	this.containerName = containerName;
    	this.useTransformation = useTransformation;
    	this.xsltPathPMML = xsltPathPMML;
        this.xsltPathBKEF = xsltPathBKEF;
    	this.tempDir = tempDir;
    	this.settingsError = settingsError;
    }

    /**
     * Metoda pro provedeni testu spravnosti nastaveni aplikace
     * @return vypis testu
     * @throws TransformerException 
     * @throws SAXException 
     * @throws XmlException 
     */
    public String runTest() throws IOException, XmlException, SAXException, TransformerException{
        String output = "<testOutput>";
            output +=
                "<settings>" +
                    "<envDir>" + envDir + "</envDir>" +
                    "<queryDir>" + queryDir + "</queryDir>" +
                    "<contName>" + containerName + "</contName>" +
                    "<useTrans>" + useTransformation + "</useTrans>" +
                    "<xsltPathPMML>" + xsltPathPMML + "</xsltPathPMML>" +
                    "<xsltPathBKEF>" + xsltPathBKEF + "</xsltPathBKEF>" +
                    "<tempDir>" + tempDir + "</tempDir>" +
                    "<err>" + settingsError + "</err>" +
                "</settings>";
            output += "<addQuery>" + qh.addQuery("Pokusná query určená k testu", "pokusTest") + "</addQuery>";
            output += "<getQuery>" + qh.getQuery("pokusTest") + "</getQuery>";
            output += "<deleteQuery>" + qh.deleteQuery("pokusTest") + "</deleteQuery>";
            output += "<indexDocument>" + bh.indexDocument("<doc>pokusný dokument</doc>", "pokus1", "pokusTest", "", "") + "</indexDocument>";
            output += "<getDocument>" + bh.getDocument("pokusTest") + "</getDocument>";
            output += "<removeDocument>" + bh.removeDocument("pokusTest") + "</removeDocument>";
        output += "</testOutput>";
        return output;
    }
}
