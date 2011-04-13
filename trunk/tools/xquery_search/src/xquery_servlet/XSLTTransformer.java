package xquery_servlet;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;

import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;

/**
 * Trida XSLT transformace
 * @author Tomas Marek
 */
public class XSLTTransformer {

    /**
     * Metoda pro pouziti XSLT transformace (vstup typu File)
     * @param xmlFile vstupni soubor
     * @param xsltFile soubor s xslt transformaci
     * @return prevedeny soubor ve forme Stringu
     */
    public String xsltTransformation (File xmlFile, File xsltFile, String docID, String creationTime, String reportUri)
    {
    	String output = "";
        ByteArrayOutputStream baos = new ByteArrayOutputStream();

        try {
        javax.xml.transform.Source xmlSource =
         new javax.xml.transform.stream.StreamSource(xmlFile);
        javax.xml.transform.Source xsltSource =
         new javax.xml.transform.stream.StreamSource(xsltFile);
        javax.xml.transform.Result result =
         new javax.xml.transform.stream.StreamResult(baos);

        // create an instance of TransformerFactory
        javax.xml.transform.TransformerFactory transFact =
         javax.xml.transform.TransformerFactory.newInstance(  );

        javax.xml.transform.Transformer trans;
        
        trans = transFact.newTransformer(xsltSource);
        trans.setParameter("joomlaID", docID);
        trans.setParameter("creationTime", creationTime);
        trans.setParameter("reportURI", reportUri);
        trans.transform(xmlSource, result);

        
        } catch (TransformerConfigurationException ex) {
                output += "<err>" + ex.toString() + "</err>";
        }
        catch (TransformerException ex) {
                output += "<err>" + ex.toString() + "</err>";
        }
        output += baos.toString();
        return output;
   }
    
    /**
     * Metoda pro pouziti XSLT transformace (vstup typu String)
     * @param xmlString String obsahujici dokument k prevedeni
     * @param xsltFile soubor s xslt transformaci
     * @return prevedeny soubor ve forme Stringu
     */
    public String xsltTransformation (String xmlString, File xsltFile, String docID, String creationTime, String reportUri)
    {
    	String output = "";
        ByteArrayOutputStream baos = new ByteArrayOutputStream();

        byte[] bytes = xmlString.getBytes();
        ByteArrayInputStream bais = new ByteArrayInputStream(bytes);

        try {
        javax.xml.transform.Source xmlSource =
         new javax.xml.transform.stream.StreamSource(bais);
        javax.xml.transform.Source xsltSource =
         new javax.xml.transform.stream.StreamSource(xsltFile);
        javax.xml.transform.Result result =
         new javax.xml.transform.stream.StreamResult(baos);

        // create an instance of TransformerFactory
        javax.xml.transform.TransformerFactory transFact =
         javax.xml.transform.TransformerFactory.newInstance(  );

        javax.xml.transform.Transformer trans;

        trans = transFact.newTransformer(xsltSource);
        trans.setParameter("joomlaID", docID);
        trans.setParameter("creationTime", creationTime);
        trans.setParameter("reportURI", reportUri);
        trans.transform(xmlSource, result);


        } catch (TransformerConfigurationException ex) {
                output += "<err>" + ex.toString() + "</err>";
        }
        catch (TransformerException ex) {
                output += "<err>" + ex.toString() + "</err>";
        }
        output += baos.toString();
        return output;
   }

}
