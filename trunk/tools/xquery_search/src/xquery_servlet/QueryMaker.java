package xquery_servlet;

import java.io.IOException;
import java.io.InputStream;
import java.io.StringWriter;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

/**
 * Trida obstaravajici sestaveni XPath dotazu podle vstupniho zadani (ARBuilder)
 * @author Tomas Marek
 */
public class QueryMaker {

    public QueryMaker() {
    }

    public String makeXPath(InputStream xmlQuery){
        String output = "";
        try {
                DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
                DocumentBuilder db = dbf.newDocumentBuilder();
                Document doc = db.parse(xmlQuery);
                doc.getDocumentElement().normalize();

                NodeList bbaList = doc.getElementsByTagName("BBA");
                for (int i = 0; i < bbaList.getLength(); i++) {
                    Element bbaElement = (Element)bbaList.item(i).getChildNodes();
                    NodeList bbaChildList = bbaElement.getChildNodes();
                    for (int j = 0; j < bbaChildList.getLength() ; j++) {
                        Node bbaChild = bbaChildList.item(j);
                        NamedNodeMap bbaAttributes = bbaChild.getAttributes();
                        if (bbaAttributes.getNamedItem("dictionary").toString().equals("DataDictionary")) {
                            output += "<child>" + bbaChild.getNodeName() + "</child>";
                        }
                    }
                }
        } catch (SAXException ex) {
            //Logger.getLogger(QueryMaker.class.getName()).log(Level.SEVERE, null, ex);
        } catch (IOException ex) {
            //Logger.getLogger(QueryMaker.class.getName()).log(Level.SEVERE, null, ex);
        } catch (ParserConfigurationException ex) {
            //Logger.getLogger(QueryMaker.class.getName()).log(Level.SEVERE, null, ex);
        }
        return output;
    }
}
