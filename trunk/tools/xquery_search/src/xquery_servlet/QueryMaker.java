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
	final String dictionary = "TransformationDictionary";
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
    public String[] makeXPath(InputStream xmlQuery){
    	String output[] = new String[2];
    	output[0] = "";
        try {
            DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
            DocumentBuilder db = dbf.newDocumentBuilder();
            Document doc = db.parse(xmlQuery);
            doc.getDocumentElement().normalize();

            output[0] += "collection(\""+containerName+"\")/PMML/AssociationRule[";
            NodeList scope = doc.getElementsByTagName("Scope");
            if (scope.getLength() == 0) {
                 NodeList anteList = doc.getElementsByTagName("Antecedent");
                 NodeList consList = doc.getElementsByTagName("Consequent");
                 NodeList condList = doc.getElementsByTagName("Condition");
                 
                 output[1] = "false";
                 
                 if (anteList.getLength() > 0) { output[0] += "(" + cedentPrepare(anteList)[0] + ")"; }
                 if (anteList.getLength() > 0 && consList.getLength() > 0) { output[0] += " and "; }
                 if (consList.getLength() > 0) {
                	 String consCedent[] = cedentPrepare(consList); 
                	 output[0] += "(" + consCedent[0] + ")";
                	 output[1] = consCedent[1];
         		 }
                 if ((anteList.getLength() > 0 && condList.getLength() > 0) || (consList.getLength() > 0 && condList.getLength() > 0)) { output[0] += " and "; }
                 if (condList.getLength() > 0) { output[0] += "(" + cedentPrepare(condList)[0] + ")"; }
                 
                 
            } else {
                int x = 0;
                NodeList BBAList = doc.getElementsByTagName("BBA");
                for (int i = 0; i < BBAList.getLength(); i++){
                    Element BBAElement = (Element)BBAList.item(i);
                    NodeList fieldList = BBAElement.getElementsByTagName("Field");
                    if (x > 0) {
                        output[0] += " and ";
                    }
                    output[0] += "(";
                    output[0] += BBAMake(fieldList, "./", false, false);    
                    output[0] += ")";
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
            	if (thList.item(0) != null) {
	            	Element thresholdElement = (Element) thList.item(0);
	            	NodeList thresholdList = thresholdElement.getChildNodes();
	            	Node threshold = thresholdList.item(0);
	            	output[0] += " and IMValue[@name=\"" + interestMeasure.getNodeValue() + "\"]/text() >= " + threshold.getNodeValue();
            	}
            }
            output[0] += "]";
        } catch (SAXException ex) {
            
        } catch (IOException ex) {
            
        } catch (ParserConfigurationException ex) {
            
        }
        return output;
    }
    
    private String BBAMake(NodeList fieldList, String axis, boolean inference, boolean exception) {
    	String output = "";
        for (int j = 0; j < fieldList.getLength() ; j++) {
            Element fieldChildElement = (Element)fieldList.item(j);
            if (fieldChildElement.getAttribute("dictionary").toLowerCase().equals(dictionary.toLowerCase())) {
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
                        boolean isSubset = false;
                        if (cat != null) {catString = cat.getNodeValue();}
                        if (catString.toLowerCase().contains("subset")) {
                        	String[] catSplit = catString.split(" ");
                        	if (catSplit.length > 2) {
                        		catString = "";
                        	} else {
                        		String[] boundsSplit = catSplit[1].split("-");
                        		if (boundsSplit.length > 2) {
                        			catString = "";
                        		} else {
                        			int min = Integer.parseInt(boundsSplit[0]);
                        			int max = Integer.parseInt(boundsSplit[1]);
                        			if (min == max) {
                        				isSubset = true;
                            			catString = "(count(CatName) = " + min + ")";
                            		} else {
                            			isSubset = true;
                            			catString = "(count(CatName) >= " + min + " and count(CatName) <= " + max + ")";
                        			}
                				}
                    		}
                    	}
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
                        if (exception) {
                        	output += connective+axis+"/BBA/TransformationDictionary[FieldName=\"" + name.getNodeValue() + "\"]";
                        } else {
	                        if (isSubset) {
	                        	output += connective+axis+"/BBA/TransformationDictionary[FieldName=\""+name.getNodeValue()+"\" and " + catString + "]";
	                        } else {
	                        	output += connective+axis+"/BBA/TransformationDictionary[FieldName=\""+name.getNodeValue()+"\"]";
	                        }
	                        if (catString.length() > 0 && !isSubset) {
	                        	output += "/CatName"+ sign +"\""+catString+"\"";
	                        }
                        }
                    }
                } else if (catsList.getLength() == 0 && intsList.getLength() > 0) {
                    Element intElement = (Element)intsList.item(0);
                    output += axis+"/BBA/TransformationDictionary[FieldName=\""+name.getNodeValue()+"\" and Interval/@left <= "+intElement.getAttribute("right")+" and Interval/@right >= "+intElement.getAttribute("left")+"]";
                }
            }
        }
        return output;
    }
    private String[] cedentPrepare(NodeList cedentList){
        String output[] = new String[2];
        output[0] = "";
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
                output[1] = String.valueOf(exception);
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
                                if (exception) { axisDBA2 = "/DBA"; } else { axisDBA2 += "/DBA[@connective=\""+ connective +"\"]"; }
                            }
                            String BBAConnection = "and";
                            NodeList fieldList = BBAElement.getElementsByTagName("Field");
                            if (l > 0) { output[0] += " "+ BBAConnection +" "; }
                            /*if (cedentDBA2.getLength() > 1) 
                            {*/
                                output[0] += "(" + BBAMake(fieldList, axisCedent+axisDBA1+axisDBA2, false, exception);
                                if (inference) {
                                    if (connective.equals("both")){
                                        output[0] += " or " + BBAMake(fieldList, axisCedent+axisDBA1+"/DBA", true, exception);
                                    } else {
                                        output[0] += " or " + BBAMake(fieldList, axisCedent+axisDBA1+"/DBA[@connective!=\"" + connective + "\"]", true, exception);
                                    }
                                }
                                output[0]  += ")";
                            /*} else {
                                output[0] += BBAMake(fieldList, axisCedent+axisDBA1+axisDBA2, false);
                            }*/
                        }
                    }
                }
            }
         }
        return output;
    }
    
    /**
     * Metoda pro ziskani omezeni poctu vysledku
     * @param xmlQuery vstupni XML dotaz
     * @return maximalni pocet vysledku
     */
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
			if (maxResultsList2.item(0) != null) {
				Node maxResults = maxResultsList2.item(0);
				maxResInt = Integer.parseInt(maxResults.getNodeValue());
			}
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return maxResInt;
    }
   
   /**
    * Metoda pro vytvoreni XPath dotazu, ktery pomaha vybrat pravidla u exception query 
    * @param xmlQuery vstupni XML dotaz
    * @return XPath dotaz
    */
   public String getExceptionPath(InputStream xmlQuery) {
	   String output = "/AssociationRule[";
	   try {
           DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
           DocumentBuilder db = dbf.newDocumentBuilder();
           Document doc = db.parse(xmlQuery);
           doc.getDocumentElement().normalize();
           
           NodeList consList = doc.getElementsByTagName("Consequent");
           Element consElement = (Element) consList.item(0);
           NodeList dbas = consElement.getChildNodes();
           for (int a = 0; a < dbas.getLength(); a++) {
        	   Element dba = (Element) dbas.item(a);
        	   NodeList dbas2 = dba.getChildNodes();
        	   for (int b = 0; b < dbas2.getLength(); b++) {
        		   for (int z = 0; z < 2; z++) {
	        		   Element dba2 = (Element) dbas2.item(b);
	        		   String connective = dba2.getAttribute("connective").toString();
	        		   if (connective.toLowerCase().equals("positive") && z < 1) {
	        			   connective = "Negative";
	        		   } else if (connective.toLowerCase().equals("negative") && z < 1) {
	        			   connective = "Positive";
	        		   }
	        		   String connect = "";
	        		   if (z == 1) { connect = " or "; }
	        		   output += connect + "Consequent/DBA/DBA[@connective=\"" + connective + "\"]";

	        		   NodeList bbas = dba2.getChildNodes();
	        		   for (int c = 0; c < bbas.getLength(); c++) {
	        			   Element bbaElement = (Element) bbas.item(c);
	
	        			   NodeList fields = bbaElement.getChildNodes();
	        			   for (int d = 0; d < fields.getLength(); d++) {
	        				   Element fieldElement = (Element) fields.item(d);
	        				   if (fieldElement.getAttribute("dictionary").toLowerCase().equals(dictionary.toLowerCase())) {
	        					   NodeList nameList = fieldElement.getElementsByTagName("Name");
		    		               NodeList catsList = fieldElement.getElementsByTagName("Category");
		    		               
		    		               Element nameElement = (Element) nameList.item(0);
		    		               Node name = nameElement.getChildNodes().item(0);
		    		               String fieldName = name.getNodeValue();
		    		               output += "/BBA[";
		    		               for (int e = 0; e < catsList.getLength(); e++) {
		    		            	   Element catElement = (Element) catsList.item(e);
		    		            	   Node cat = catElement.getChildNodes().item(0);
		    		            	   String catName = cat.getNodeValue();
		    		            	   String catNameCondition = "CatName=\"" + catName + "\"";
		    		            	   if (z == 1) {
		    		            		   catNameCondition = "CatName!=\"" + catName + "\"";
		    		            	   }
		    		            	   output += "FieldName=\"" + fieldName + "\" and " + catNameCondition;
		    		               }
		    		               output += "]";
	        				   }
	        			   }
	        		   }
        		   }
        	   }
           }
	   } catch (ParserConfigurationException e) {
		   e.printStackTrace();
	   } catch (SAXException e) {
		e.printStackTrace();
	} catch (IOException e) {
		e.printStackTrace();
	}
	   output += "]";
	   return output;
   }
   
}
