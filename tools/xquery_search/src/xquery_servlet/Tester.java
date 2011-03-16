package xquery_servlet;

import com.sleepycat.dbxml.XmlManager;

/**
 * Trida provadi vypis nastaveni a otestovani funkci aplikace
 * @author Tomas
 */
public class Tester {

    public Tester() {
        
    }

    /**
     * Metoda pro provedeni testu spravnosti nastaveni aplikace
     * @param qh
     * @param bh
     * @param mgr
     * @param envDir
     * @param queryDir
     * @param containerName
     * @param useTransformation
     * @param xsltPath
     * @param tempDir
     * @param settingsError
     * @return
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
