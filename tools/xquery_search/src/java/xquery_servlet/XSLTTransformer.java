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
     * @param args
     */
    public String XSLT_transformation (File xmlFile, File xsltFile)
    {
    	String output = "";
        ByteArrayOutputStream baos = new ByteArrayOutputStream();

        //File xml_output = new File(xmlFile.getAbsolutePath().toString() + "transformed.xml");

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
        trans.transform(xmlSource, result);

        
        } catch (TransformerConfigurationException ex) {
                output += "<err>" + ex.toString() + "</err>";
                //ex.printStackTrace();
        }
        catch (TransformerException ex) {
                output += "<err>" + ex.toString() + "</err>";
                //ex.printStackTrace();
        }
        //output += xmlFile.getAbsolutePath().toString() + "\n";
        //output += xsltFile.getAbsolutePath().toString() + "\n";
        output += baos.toString();
        return output;
   }

    public String XSLT_transformation (String xmlString, File xsltFile)
    {
    	String output = "";
        ByteArrayOutputStream baos = new ByteArrayOutputStream();

        byte[] bytes = xmlString.getBytes();
        ByteArrayInputStream bais = new ByteArrayInputStream(bytes);
        
        //File xml_output = new File(xmlFile.getAbsolutePath().toString() + "transformed.xml");

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
        trans.transform(xmlSource, result);


        } catch (TransformerConfigurationException ex) {
                output += "<err>" + ex.toString() + "</err>";
                //ex.printStackTrace();
        }
        catch (TransformerException ex) {
                output += "<err>" + ex.toString() + "</err>";
                //ex.printStackTrace();
        }
        //output += xmlFile.getAbsolutePath().toString() + "\n";
        //output += xsltFile.getAbsolutePath().toString() + "\n";
        output += baos.toString();
        return output;
   }

}
