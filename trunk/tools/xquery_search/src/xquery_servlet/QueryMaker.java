package xquery_servlet;

import java.io.IOException;
import java.io.InputStream;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
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
        String output_xml = "";
        try {
                DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
                DocumentBuilder db = dbf.newDocumentBuilder();
                Document doc = db.parse(xmlQuery);
                doc.getDocumentElement().normalize();

                NodeList fieldList = doc.getElementsByTagName("Field");
                /*for (int i = 0; i < bbaList.getLength(); i++) {
                    Element bbaElement = (Element)bbaList.item(i);
                    NodeList bbaChildList = bbaElement.getChildNodes();*/
                    for (int j = 0; j < fieldList.getLength() ; j++) {
                        Element fieldChildElement = (Element)fieldList.item(j);
                        if (fieldChildElement.getAttribute("dictionary").equals("DataDictionary")) {
                            output_xml += "<Field>";
                            NodeList namesList = fieldChildElement.getElementsByTagName("Name");
                            NodeList typesList = fieldChildElement.getElementsByTagName("Type");
                            NodeList catsList = fieldChildElement.getElementsByTagName("Category");
                            for (int k = 0; k < namesList.getLength(); k++) {
                                Element nameElement = (Element)namesList.item(k);
                                NodeList names = nameElement.getChildNodes();
                                Node name = names.item(0);
                                output_xml += "<Name>" + name.getNodeValue() + "</Name>";
                            }
                            for (int k = 0; k < catsList.getLength(); k++) {
                                Element catElement = (Element)catsList.item(k);
                                NodeList cats = catElement.getChildNodes();
                                Node cat = cats.item(0);
                                output_xml += "<Category>" + cat.getNodeValue() + "</Category>";
                            }
                            output_xml += "</Field>";
                        }
                    }
                output += output_xml;
                //}
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
