package xquery_servlet;

import java.io.BufferedOutputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileOutputStream;
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
                 
                 if (anteList.getLength() > 0) { output += "(" + cedentPrepare(anteList) + ")"; }
                 if (anteList.getLength() > 0 && consList.getLength() > 0) { output += " and "; }
                 if (consList.getLength() > 0) { output += "(" + cedentPrepare(consList) + ")"; }
                 if ((anteList.getLength() > 0 && condList.getLength() > 0) || (consList.getLength() > 0 && condList.getLength() > 0)) { output += " and "; }
                 if (condList.getLength() > 0) { output += "(" + cedentPrepare(condList) + ")"; }
                 
                 
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
                    output += BBAMake(fieldList, "./", false);    
                    output += ")";
                    x++;
                }
            }
            
            NodeList imsList = doc.getElementsByTagName("IMs");
            Element imsElement = (Element) imsList.item(0);
            NodeList imsChilds = imsElement.getChildNodes();
            for (int j = 0; j < imsChilds.getLength(); j++) {
            	Element ims = (Element) imsChilds.item(j);
            	NodeList imList = ims.getElementsByTagName("InterestMeasure");
            	Element interestMeasureElement = (Element) imList.item(0);
            	NodeList interestMeasureList = interestMeasureElement.getChildNodes();
            	Node interestMeasure = interestMeasureList.item(0);
            	NodeList thList = ims.getElementsByTagName("Threshold");
            	Element thresholdElement = (Element) thList.item(0);
            	NodeList thresholdList = thresholdElement.getChildNodes();
            	Node threshold = thresholdList.item(0);
            	output += " and IMValue[@name=\"" + interestMeasure.getNodeValue() + "\"]/text() >= " + threshold.getNodeValue();
            }
            //output += "MaxResults: " + getMaxResults(xmlQuery);
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
    
    private String BBAMake(NodeList fieldList, String axis, boolean inference) {
        String output = "";
        for (int j = 0; j < fieldList.getLength() ; j++) {
            Element fieldChildElement = (Element)fieldList.item(j);
            if (fieldChildElement.getAttribute("dictionary").equals("TransformationDictionary")) {
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
                        String catString = "";
                        if (cat != null) {catString = cat.getNodeValue();}
                        String connective = "";
                        if(k > 0) {
                            if (type.getNodeValue().equals("At least one from listed")){
                                connective = " or ";
                            } else {
                                connective = " and ";
                            }
                        }
                        String sign = "";
                        if (!inference) { sign = "="; } else { sign = "!="; }
                        output += connective+axis+"/BBA/TransformationDictionary[FieldName=\""+name.getNodeValue()+"\"]/CatName"+ sign +"\""+catString+"\"";
                    }
                } else if (catsList.getLength() == 0 && intsList.getLength() > 0) {
                    Element intElement = (Element)intsList.item(0);
                    output += axis+"/BBA/TransformationDictionary[FieldName=\""+name.getNodeValue()+"\" and Interval/@left <= "+intElement.getAttribute("right")+" and Interval/@right >= "+intElement.getAttribute("left")+"]";
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
                boolean exception = false;
                if (cedentElement.getAttribute("exception") != null) {if (cedentElement.getAttribute("exception").toString().toLowerCase().equals("true")) {exception = true;} }
                NodeList cedentDBA1 = cedentElement.getChildNodes();
                for (int k = 0; k < cedentDBA1.getLength(); k++){
                    Element cedentDBA1Element = (Element) cedentDBA1.item(k);
                    axisDBA1 = "";
                    if (cedentDBA1Element.getAttribute("connective").equals("AnyConnective")) {
                        axisDBA1 += "/DBA";
                    } else {
                        axisDBA1 += "/DBA[@connective=\""+cedentDBA1Element.getAttribute("connective").toString() +"\"]";
                    }
                    NodeList cedentDBA2 = cedentDBA1Element.getChildNodes();
                    for (int l = 0; l < cedentDBA2.getLength(); l++){
                        Element cedentBBAElement = (Element)cedentDBA2.item(l);
                        NodeList cedentBBA = cedentBBAElement.getChildNodes();
                        for (int m = 0; m < cedentBBA.getLength(); m++){
                            Element BBAElement = (Element)cedentBBA.item(m);
                            axisDBA2 = "";
                            boolean inference = false;
                            String connective = cedentBBAElement.getAttribute("connective").toString();
                            if (connective.toLowerCase().equals("positive")) { connective = "Conjunction"; }
                            if (cedentBBAElement.getAttribute("inference").toLowerCase().equals("true")) { inference = true; }
                            if (connective.toLowerCase().equals("both")) {
                                axisDBA2 += "/DBA";
                            } else {
                                axisDBA2 += "/DBA[@connective=\""+ connective +"\"]";
                            }
                            String BBAConnection = "and";
                            if (exception) {BBAConnection = "or";}
                            NodeList fieldList = BBAElement.getElementsByTagName("Field");
                            if (l > 0) { output += " "+ BBAConnection +" "; }
                            /*if (cedentDBA2.getLength() > 1) 
                            {*/
                                output += "(" + BBAMake(fieldList, axisCedent+axisDBA1+axisDBA2, false);
                                if (inference) {
                                    if (connective.equals("both")){
                                        output += " or " + BBAMake(fieldList, axisCedent+axisDBA1+"/DBA", true);
                                    } else {
                                        output += " or " + BBAMake(fieldList, axisCedent+axisDBA1+"/DBA[@connective!=\"" + connective + "\"]", true);
                                    }
                                }
                                output  += ")";
                            /*} else {
                                output += BBAMake(fieldList, axisCedent+axisDBA1+axisDBA2, false);
                            }*/
                        }
                    }
                }
            }
         }
        return output;
    }
    
    public int getMaxResults(InputStream xmlQuery) {
		int maxResInt = 200;
		try {
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			 
			Document doc = db.parse(xmlQuery);
			doc.getDocumentElement().normalize();
			
			NodeList maxResultsList = doc.getElementsByTagName("MaxResults");
			Element maxResultsElement = (Element)maxResultsList.item(0);
			NodeList maxResultsList2 = maxResultsElement.getChildNodes();
			Node maxResults = maxResultsList2.item(0);
			maxResInt = Integer.parseInt(maxResults.getNodeValue());
		} catch (ParserConfigurationException e) {
			//maxResInt = 1001;
		} catch (SAXException e) {
			//maxResInt = 1002;
			e.printStackTrace();
		} catch (IOException e) {
			//maxResInt = 1003;
		}
		return maxResInt;
    }
    
    /**
     * Metoda pro zmenu hodnot Interesting Measures mezi vstupni hodnotou a hodnotou ulozenou v DB 
     * @param value Vyhledavana hodnota
     * @param fromDict Vychozi slovnik
     * @param toDict Cilovy slovnik
     * @return Zmenena hodnota
     */
    private String IMValueSwitch(String value, int fromDict, int toDict){
    	String output = "";
    	String [][] dictionary = new String[3][2];
    	//Naplenni hodnot slovniku -> pozice 0 v radku - vstup, pozice 1 v radku - hodnoty v DB
    	dictionary[0][0] = "Confidence";
    	dictionary[0][1] = "Conf";
    	dictionary[1][0] = "Support";
    	dictionary[1][1] = "Supp";
    	for (int i = 0; i < dictionary.length; i++) {
    		if (dictionary[i][fromDict].toLowerCase().equals(value.toLowerCase())) {
    			output = dictionary[i][toDict];
    		}
    	}
    	return output;
    }
}
