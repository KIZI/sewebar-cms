package xquery_servlet;

import com.sleepycat.dbxml.XmlManager;

/**
 * Trida provadi vypis nastaveni a otestovani funkci aplikace
 * @author Tomas Marek
 */
public class Tester {

	/**
	 * Konstruktor
	 */
    public Tester() {
    }

    /**
     * Metoda pro provedeni testu spravnosti nastaveni aplikace
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
     * @return vypis testu
     */
    public String runTest(QueryHandler qh, BDBXMLHandler bh, XmlManager mgr, String envDir, String queryDir, String containerName, String useTransformation, String xsltPath, String tempDir, String settingsError){
        String output = "<testOutput>";
            output +=
                "<settings>" +
                    "<envDir>" + envDir + "</envDir>" +
                    "<queryDir>" + queryDir + "</queryDir>" +
                    "<contName>" + containerName + "</contName>" +
                    "<useTrans>" + useTransformation + "</useTrans>" +
                    "<xsltPath>" + xsltPath + "</xsltPath>" +
                    "<tempDir>" + tempDir + "</tempDir>" +
                    "<err>" + settingsError + "</err>" +
                "</settings>";
            output += "<addQuery>" + qh.addQuery("Pokusná query určená k testu", "pokusTest") + "</addQuery>";
            output += "<getQuery>" + qh.getQuery("pokusTest")[1] + "</getQuery>";
            output += "<deleteQuery>" + qh.deleteQuery("pokusTest") + "</deleteQuery>";
            output += "<indexDocument>" + bh.indexDocument("<doc>pokusný dokument</doc>", "pokusTest") + "</indexDocument>";
            output += "<getDocument>" + bh.getDocument("pokusTest") + "</getDocument>";
            output += "<removeDocument>" + bh.removeDocument("pokusTest") + "</removeDocument>";
        output += "</testOutput>";
        return output;
    }
}
