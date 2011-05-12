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

            output += "collection(\""+containerName+"\")/PMML/AssociationRule[";
            NodeList scope = doc.getElementsByTagName("Scope");
            if (scope.getLength() == 0) {
                 NodeList anteList = doc.getElementsByTagName("Antecedent");
                 NodeList consList = doc.getElementsByTagName("Consequent");
                 NodeList condList = doc.getElementsByTagName("Condition");
                 
                 if (anteList.getLength() > 0) { output += cedentPrepare(anteList); }
                 if (anteList.getLength() > 0 && consList.getLength() > 0) { output += " and "; }
                 if (consList.getLength() > 0) { output += cedentPrepare(consList); }
                 if ((anteList.getLength() > 0 && condList.getLength() > 0) || (consList.getLength() > 0 && condList.getLength() > 0)) { output += " and "; }
                 if (condList.getLength() > 0) { output += cedentPrepare(condList); }
                     
            } else {
                int x = 0;
                NodeList BBAList = doc.getElementsByTagName("BBA");
                for (int i = 0; i < BBAList.getLength(); i++){
                    Element BBAElement = (Element)BBAList.item(i);
                    NodeList fieldList = BBAElement.getElementsByTagName("Field");
                    if (x > 0) {
                        output += " and ";
                    }
                    output += "(";
                    output += BBAMake(fieldList, "./");    
                    output += ")";
                    x++;
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
    
    private String BBAMake(NodeList fieldList, String axis) {
        String output = "";
        for (int j = 0; j < fieldList.getLength() ; j++) {
            Element fieldChildElement = (Element)fieldList.item(j);
            if (fieldChildElement.getAttribute("dictionary").equals("DataDictionary")) {
                NodeList namesList = fieldChildElement.getElementsByTagName("Name");
                NodeList typesList = fieldChildElement.getElementsByTagName("Type");
                NodeList catsList = fieldChildElement.getElementsByTagName("Category");
                NodeList intsList = fieldChildElement.getElementsByTagName("Interval");

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
                        output += connective+axis+"/BBA/DataDictionary[FieldName=\""+name.getNodeValue()+"\"]/CatName=\""+cat.getNodeValue()+"\"";
                    }
                } else if (catsList.getLength() == 0 && intsList.getLength() > 0) {
                    Element intElement = (Element)intsList.item(0);
                    output += axis+"/BBA/DataDictionary[FieldName=\""+name.getNodeValue()+"\" and Interval/@left <= "+intElement.getAttribute("right")+"  and Interval/@right >= "+intElement.getAttribute("left")+"]";
                }
            }
        }
        return output;
    }
    private String cedentPrepare(NodeList cedentList){
        String output = "";
        String axisCedent = "";
        String axisDBA1 = "";
        String axisDBA2 = "";
        if (cedentList.getLength() > 0) {
            axisCedent = "";
            axisCedent += cedentList.item(0).getNodeName();
            for (int j = 0; j < cedentList.getLength(); j++){
                Element cedentElement = (Element)cedentList.item(j);
                NodeList cedentDBA1 = cedentElement.getChildNodes();
                for (int k = 0; k < cedentDBA1.getLength(); k++){
                    Element cedentDBA1Element = (Element) cedentDBA1.item(k);
                    axisDBA1 = "";
                    if (cedentDBA1Element.getAttribute("connective").equals("AnyConnective")) {
                        axisDBA1 += "/DBA";
                    } else {
                        axisDBA1 += "/DBA[@connective="+cedentDBA1Element.getAttribute("connective").toString() +"]";
                    }
                    NodeList cedentDBA2 = cedentDBA1Element.getChildNodes();
                    for (int l = 0; l < cedentDBA2.getLength(); l++){
                        Element cedentBBAElement = (Element)cedentDBA2.item(l);
                        NodeList cedentBBA = cedentBBAElement.getChildNodes();
                        for (int m = 0; m < cedentBBA.getLength(); m++){
                            Element BBAElement = (Element)cedentBBA.item(m);
                            axisDBA2 = "";
                            if (cedentBBAElement.getAttribute("connective").equals("Both")) {
                                axisDBA2 += "/DBA";
                            } else {
                                axisDBA2 += "/DBA[@connective="+cedentBBAElement.getAttribute("connective").toString() +"]";
                            }
                            NodeList fieldList = BBAElement.getElementsByTagName("Field");
                            output += BBAMake(fieldList, axisCedent+axisDBA1+axisDBA2);
                        }
                    }
                }
            }
         }
        return output;
    }    
}
