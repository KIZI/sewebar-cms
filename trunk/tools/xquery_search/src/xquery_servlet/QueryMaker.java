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
    String containerName;
    /**
     * Konstruktor instance tridy QueryMaker
     * @param containerName
     */
    public QueryMaker(String containerName) {
        this.containerName = containerName;
    }

    /**
     * Metoda provadejici prevedeni query na XPath dotaz
     * @param xmlQuery query ve formatu XML
     * @return XPath dotaz
     */
    public String makeXPath(InputStream xmlQuery){
        String output = "";
        try {
            DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
            DocumentBuilder db = dbf.newDocumentBuilder();
            Document doc = db.parse(xmlQuery);
            doc.getDocumentElement().normalize();

            int i = 0;
            output += "collection(\""+containerName+"\")/PMML/AssociationRule[";
            NodeList fieldList = doc.getElementsByTagName("Field");
            for (int j = 0; j < fieldList.getLength() ; j++) {
                Element fieldChildElement = (Element)fieldList.item(j);
                if (fieldChildElement.getAttribute("dictionary").equals("DataDictionary")) {
                    NodeList namesList = fieldChildElement.getElementsByTagName("Name");
                    NodeList typesList = fieldChildElement.getElementsByTagName("Type");
                    NodeList catsList = fieldChildElement.getElementsByTagName("Category");
                    NodeList intsList = fieldChildElement.getElementsByTagName("Interval");
                    if (i > 0) {
                        output += " and ";
                    }
                    output += "(";

                    Element nameElement = (Element)namesList.item(0);
                    NodeList names = nameElement.getChildNodes();
                    Node name = names.item(0);
                    Element typeElement = (Element)typesList.item(0);
                    NodeList types = typeElement.getChildNodes();
                    Node type = types.item(0);
                    if (catsList.getLength() > 0 && intsList.getLength() == 0) {
                        for (int k = 0; k < (catsList.getLength()); k++) {
                            Element catElement = (Element)catsList.item(k);
                            NodeList cats = catElement.getChildNodes();
                            Node cat = cats.item(0);
                            String connective = "";
                            if(k > 0) {
                                if (type.getNodeValue().equals("At least one from listed")){
                                    connective = " or ";
                                } else {
                                    connective = " and ";
                                }
                            }
                            output += connective+".//BBA/DataDictionary[FieldName=\""+name.getNodeValue()+"\"]/CatName=\""+cat.getNodeValue()+"\"";
                        }
                    } else if (catsList.getLength() == 0 && intsList.getLength() > 0) {
                        Element intElement = (Element)intsList.item(0);
                        output += ".//BBA/DataDictionary[FieldName=\""+name.getNodeValue()+"\" and Interval/@left <= "+intElement.getAttribute("right")+"  and Interval/@right >= "+intElement.getAttribute("left")+"]";
                    }
                    output += ")";
                    i++;
                }
            }
            output += "]";
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
