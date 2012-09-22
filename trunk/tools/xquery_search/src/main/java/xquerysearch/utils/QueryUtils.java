package xquerysearch.utils;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;
import java.util.logging.Logger;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.stream.StreamResult;

import net.sf.saxon.Configuration;
import net.sf.saxon.query.DynamicQueryContext;
import net.sf.saxon.query.StaticQueryContext;
import net.sf.saxon.query.XQueryExpression;
import net.sf.saxon.trans.XPathException;

import org.w3c.dom.DOMException;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

/**
 * Utilities for querying.
 * 
 * @author Tomas Marek
 * 
 */

public class QueryUtils {
	private static final Logger logger = Logger.getLogger("QueryUtils");
	private static final String DICTIONARY = "TransformationDictionary";

	private static final boolean shorter = true;
	/**
	 * Provides removal of XML file declaration and oxygen declaration.
	 * 
	 * @param queryBody
	 *            query body to process
	 * @return processed query body when successful, otherwise <code>null</code>
	 */
	public static String deleteDeclaration(String queryBody) {
		String output = "";
		String splitXMLBegin[] = queryBody.split("([<][?][x][m][l])|([<][?][o][x][y][g][e][n])");
		if (splitXMLBegin.length == 1) {
			output = queryBody;
		} else {
			for (int i = 0; i <= (splitXMLBegin.length - 1); i++) {
				if (i == 0) {
					output += splitXMLBegin[i];
				} else {
					String splitXMLEnd[] = splitXMLBegin[i].split("[?][>]");
					if (splitXMLEnd.length > 1) {
						String splitXMLBack = splitXMLEnd[1];
						output += splitXMLBack;
					}
				}
			}
		}
		return output;
	}

	/**
	 * Converts query from input ARBuilder structure into internal structure
	 * 
	 * @param query
	 *            in input structure
	 * @return restructured query (into internal structure)
	 */
	public static ByteArrayOutputStream queryPrepare(String request) {
		String query = "declare function local:processRequest($request as node()) {"
				+ "\n let $generalSet := $request/ARQuery/GeneralSetting"
				+ "\n let $output := if (count($generalSet) > 0) then ("
				+ "\n let $attribs := for $MBA in $generalSet/MandatoryPresenceConstraint/MandatoryBA/text() return "
				+ "\n for $DBA in $request//DBASetting[@id = $MBA] return local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")"
				+ "\n return <AR_query><Scope>{$generalSet/Scope/node()}</Scope>{$attribs}"
				+ "\n <IMs>{for $im in $request/ARQuery/InterestMeasureSetting/InterestMeasureThreshold return if (contains(lower-case($im/InterestMeasure/text()), \"any interest measure\"))"
				+ "\n then <IM id=\"{$im/@id}\"><InterestMeasure>Any Interest Measure</InterestMeasure></IM>"
				+ "\n else <IM id=\"{$im/@id}\"><InterestMeasure>{$im/InterestMeasure/text()}</InterestMeasure><Threshold>{$im/Threshold/text()}</Threshold><CompareType>{$im/CompareType/text()}</CompareType></IM>}</IMs>"
				+ "\n <MaxResults>{$request/ARQuery/MaxResults/text()}</MaxResults></AR_query>"
				+ ") else ("
				+ "\n let $antecedent := for $ante in $request//AntecedentSetting/text() return if (count($request//DBASetting[@id = $ante]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $ante], $request)}</DBA>"
				+ "\n else for $DBA in $request//DBASetting[@id = $ante] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")}</DBA>"
				+ "\n let $consequent := for $cons in $request//ConsequentSetting let $exception := if ($cons/@exception=\"true\") then true() else false()  return if (count($request//DBASetting[@id = $cons/text()]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $cons], $request)}</DBA>"
				+ "\n else for $DBA in $request//DBASetting[@id = $cons/text()] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")}</DBA>"
				+ "\n let $condition := for $cond in $request//ConditionSetting/text() return if (count($request//DBASetting[@id = $cond]) = 0) then <DBA connective=\"AnyConnective\">{local:getBBAs($request//BBASetting[@id = $cond], $request)}</DBA>"
				+ "\n else for $DBA in $request//DBASetting[@id = $cond] return <DBA connective=\"{$DBA/@type}\" match=\"{$DBA/@match}\">{local:DBAtoBBARecursion($DBA//BASettingRef, $request, \"\", \"\")}</DBA>"
				+ "\nreturn"
				+ "\n <AR_query> {if (count($request/ARQuery/AntecedentSetting) > 0) then (<Antecedent>{$antecedent}</Antecedent>) else ()}"
				+ "\n {if (count($request/ARQuery/ConsequentSetting) > 0) then if($request/ARQuery/ConsequentSetting/@exception=\"true\") then <Consequent exception=\"true\">{$consequent}</Consequent> else (<Consequent>{$consequent}</Consequent>) else ()}"
				+ "\n {if (count($request/ARQuery/ConditionSetting) > 0) then (<Condition>{$condition}</Condition>) else ()}"
				+ "\n <IMs>{for $im in $request/ARQuery/InterestMeasureSetting/InterestMeasureThreshold return if (contains(lower-case($im/InterestMeasure/text()), \"any interest measure\"))"
				+ "\n then <IM id=\"{$im/@id}\"><InterestMeasure>Any Interest Measure</InterestMeasure></IM>"
				+ "\n else <IM id=\"{$im/@id}\"><InterestMeasure>{$im/InterestMeasure/text()}</InterestMeasure><Threshold>{$im/Threshold/text()}</Threshold><CompareType>{$im/CompareType/text()}</CompareType></IM>}</IMs>"
				+ "\n <MaxResults>{$request/ARQuery/MaxResults/text()}</MaxResults></AR_query>)"
				+ "\nreturn $output};"
				+ "\n declare function local:getBBAs($BBAs as node()*, $request as node()) as node()*{"
				+ "\n for $BBA in $BBAs return local:BBABuild($BBA, $request//DictionaryMapping)};"
				+ "\n declare function local:DBAtoBBARecursion($BARefs as node()*, $request as node(), $literal as xs:string*, $inference as xs:string*){"
				+ "\n for $odkaz in $BARefs let $liter := if ($literal = \"\" or empty($literal)) then \"Both\" else $literal let $infer := if ($inference = \"\" or empty($inference)) then \"false\" else $inference return "
				+ "\n if (count($request//BBASetting[@id = $odkaz/text()])>0) then <DBA connective=\"{$liter}\" inference=\"{$infer}\">{local:BBABuild($request//BBASetting[@id = $odkaz/text()], $request//DictionaryMapping)}</DBA> else local:DBAtoBBARecursion($request//DBASetting[@id = $odkaz/text()]//BASettingRef, $request, $request//DBASetting[@id = $odkaz/text()]/LiteralSign/text(), $request//DBASetting[@id = $odkaz/text()]/LiteralSign/@inference)};"
				+ "\n declare function local:BBABuild($BBA as node(), $mapping) as node(){"
				+ "\n let $dictionary := $BBA/FieldRef/@dictionary/string()"
				+ "\n let $field := $BBA/FieldRef/text()"
				+ "\n let $coefficient := $BBA/Coefficient"
				+ "\n let $category := for $cat in $coefficient//Category return "
				+ "\n if ($cat/name() = \"Category\") then <Category>{$cat/text()}</Category> else"
				+ "\n if ($cat/name() = \"Interval\") then <Interval closure=\"{$cat/@closure}\" left=\"{$cat/@leftMargin}\" right=\"{$cat/@rightMargin}\"/> else $cat"
				+ "\n return if (count($mapping/ValueMapping/Field[@name = $field]) = 0) then "
				+ "\n <BBA id=\"{$BBA/@id}\">"
				+ "\n <Field dictionary=\"TransformationDictionary\"><Name>{$field}</Name><Type>{$coefficient/Type/text()}</Type>{$category}</Field>"
				+ "\n <Field dictionary=\"DataDictionary\"><Name>{$field}</Name><Type>{$coefficient/Type/text()}</Type>{$category}</Field>"
				+ "\n </BBA> else"
				+ "\n <BBA id=\"{$BBA/@id}\"><Field dictionary=\"{$dictionary}\"><Name>{$field}</Name><Type>{$coefficient/Type/text()}</Type>{$category}</Field>{local:DictionarySwitch($dictionary, $field, $coefficient, $mapping)}</BBA>};"
				+ "\n declare function local:DictionarySwitch($dict, $field, $coeff, $mapping){"
				+ "\n let $valueMapping := $mapping//Field[@name = $field and @dictionary = $dict]/parent::node()"
				+ "\n let $fieldTrans := $valueMapping/Field[@dictionary != $dict]"
				+ "\n let $category := let $catTrans := $fieldTrans/child::node() for $everyCat in $catTrans return "
				+ "\n if ($everyCat/name() = \"Value\") then <Category>{$everyCat/text()}</Category> else "
				+ "\n if ($everyCat/name() = \"Interval\") then <Interval closure=\"{$everyCat/@closure}\" left=\"{$everyCat/@leftMargin}\" right=\"{$everyCat/@rightMargin}\"/> else $everyCat"
				+ "\n return if (count($fieldTrans) > 0) then "
				+ "\n <Field dictionary=\"{distinct-values($fieldTrans[1]/@dictionary)}\"><Name>{distinct-values($fieldTrans[1]/@name/string())}</Name><Type>{$coeff/Type/text()}</Type>{$category}</Field> else ()};"
				+ "\n let $vstup := " + deleteDeclaration(request) + "\n return local:processRequest($vstup)";

		try {
			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			Configuration config = new Configuration();
			StaticQueryContext sqc = config.newStaticQueryContext();
			XQueryExpression xqe = sqc.compileQuery(query);
			DynamicQueryContext dqc = new DynamicQueryContext(config);
			Properties props = new Properties();
			props.setProperty(OutputKeys.METHOD, "html");
			props.setProperty(OutputKeys.INDENT, "no");
			xqe.run(dqc, new StreamResult(baos), props);
			return baos;
		} catch (XPathException e) {
			logger.warning("Query preparation failed! - XPath exception");
			return null;
		}
	}

	/**
	 * Metoda provadejici prevedeni query na XPath dotaz
	 * 
	 * @param xmlQuery
	 *            query ve formatu XML
	 * @return XPath dotaz
	 */
	public static String makeXPath(InputStream xmlQuery, boolean isException, String containerName) {
		String output = "";
		try {
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(xmlQuery);
			doc.getDocumentElement().normalize();

			output += "collection(\"" + containerName + "\")/PMML/AssociationRule[";
			NodeList scope = doc.getElementsByTagName("Scope");
			if (scope.getLength() == 0) {
				NodeList anteList = doc.getElementsByTagName("Antecedent");
				NodeList consList = doc.getElementsByTagName("Consequent");
				NodeList condList = doc.getElementsByTagName("Condition");

				if (anteList.getLength() > 0) {
					output += "(" + prepareCedent(anteList, isException, shorter) + ")";
				}
				if (anteList.getLength() > 0 && consList.getLength() > 0) {
					output += " and ";
				}
				if (consList.getLength() > 0) {
					if (isException == false) {
						String consCedent = prepareCedent(consList, false, shorter);
						output += "(" + consCedent + ")";
					} else {
						String consCedent = getExceptionQuery(consList);
						output += "(" + consCedent + ")";
					}
				}
				if ((anteList.getLength() > 0 && condList.getLength() > 0)
						|| (consList.getLength() > 0 && condList.getLength() > 0)) {
					output += " and ";
				}
				if (condList.getLength() > 0) {
					output += "(" + prepareCedent(condList, false, shorter) + ")";
				}

			} else {
				int x = 0;
				NodeList BBAList = doc.getElementsByTagName("BBA");
				for (int i = 0; i < BBAList.getLength(); i++) {
					Element BBAElement = (Element) BBAList.item(i);
					NodeList fieldList = BBAElement.getElementsByTagName("Field");
					if (x > 0) {
						output += " and ";
					}
					output += "(";
					output += bbaMake(fieldList, "./", false);
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
				if (thList.item(0) != null) {
					Element thresholdElement = (Element) thList.item(0);
					NodeList thresholdList = thresholdElement.getChildNodes();
					Node threshold = thresholdList.item(0);
					output += " and IMValue[@name=\"" + interestMeasure.getNodeValue() + "\"]/text() >= "
							+ threshold.getNodeValue();
				}
			}
			output += "]";
			return output;
		} catch (DOMException e) {
			logger.warning("Making of XPath expression failed! - DOM exception");
			return null;
		} catch (ParserConfigurationException e) {
			logger.warning("Making of XPath expression failed! - Parser configuration exception");
			return null;
		} catch (IOException e) {
			logger.warning("Making of XPath expression failed! - IO exception");
			return null;
		} catch (SAXException e) {
			logger.warning("Making of XPath expression failed! - SAX exception");
			return null;
		}
	}

	private static String bbaMake(NodeList fieldList, String axis, boolean catSignNotEqual) {
		String output = "";
		for (int j = 0; j < fieldList.getLength(); j++) {
			Element fieldChildElement = (Element) fieldList.item(j);
			if (fieldChildElement.getAttribute("dictionary").toLowerCase().equals(DICTIONARY.toLowerCase())) {
				NodeList namesList = fieldChildElement.getElementsByTagName("Name");
				NodeList typesList = fieldChildElement.getElementsByTagName("Type");
				NodeList catsList = fieldChildElement.getElementsByTagName("Category");
				NodeList intsList = fieldChildElement.getElementsByTagName("Interval");

				Element nameElement = (Element) namesList.item(0);
				NodeList names = nameElement.getChildNodes();
				Node name = names.item(0);
				Element typeElement = (Element) typesList.item(0);
				NodeList types = typeElement.getChildNodes();
				Node type = types.item(0);
				if (catsList.getLength() > 0 && intsList.getLength() == 0) {
					for (int k = 0; k < (catsList.getLength()); k++) {
						Element catElement = (Element) catsList.item(k);
						NodeList cats = catElement.getChildNodes();
						Node cat = cats.item(0);
						String catString = "";
						boolean isSubset = false;
						if (cat != null) {
							catString = cat.getNodeValue();
						}
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
										catString = "(count(CatName) >= " + min + " and count(CatName) <= "
												+ max + ")";
									}
								}
							}
						}
						String connective = "";
						if (k > 0) {
							if (type.getNodeValue().equals("At least one from listed")) {
								connective = " or ";
							} else {
								connective = " and ";
							}
						}
						String sign = "";
						if (!catSignNotEqual) {
							sign = "=";
						} else {
							sign = "!=";
						}
						if (isSubset) {
							output += connective + axis + "/BBA/TransformationDictionary[FieldName=\""
									+ name.getNodeValue() + "\" and " + catString + "]";
						} else {
							output += connective + axis + "/BBA/TransformationDictionary[FieldName=\""
									+ name.getNodeValue() + "\"";
						}
						if (catString.length() > 0 && !isSubset) {
							output += " and CatName" + sign + "\"" + catString + "\"]";
						}
					}
				} else if (catsList.getLength() == 0 && intsList.getLength() > 0) {
					Element intElement = (Element) intsList.item(0);
					output += axis + "/BBA/TransformationDictionary[FieldName=\"" + name.getNodeValue()
							+ "\" and Interval/@left <= " + intElement.getAttribute("right")
							+ " and Interval/@right >= " + intElement.getAttribute("left") + "]";
				}
			}
		}
		return output;
	}

	private static String prepareCedent(NodeList cedentList, boolean exception, boolean shorter) {
		String queryHasCategory = "";
    	String queryHasNotCategory = "";
    	String countCondition = "";
    	
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
              
                    if (cedentDBA1Element.getAttribute("connective").equals("AnyConnective") || cedentDBA1Element.getAttribute("connective").equals("Any")) {
                        axisDBA1 += "/DBA";
                    } else {
                        axisDBA1 += "/DBA[@connective=\""+cedentDBA1Element.getAttribute("connective").toString() +"\"]";
                    }
                    
                    NodeList cedentDBA2 = cedentDBA1Element.getChildNodes();
                    
                    
                    for (int l = 0; l < cedentDBA2.getLength(); l++){
                        Element cedentBBAElement = (Element)cedentDBA2.item(l);
                        NodeList cedentBBA = cedentBBAElement.getChildNodes();
                    
                        for (int m = 0; m < cedentBBA.getLength(); m++){
                            Element bbaElement = (Element)cedentBBA.item(m);
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
                            
                            String bbaConnection = "and";
                            
                            if (exception == true || shorter == true) {
                            	bbaConnection = "or";
                            }
                            
                            NodeList fieldList = bbaElement.getElementsByTagName("Field");
                            
                            if (l > 0) { queryHasCategory += " "+ bbaConnection +" "; }
                            /*if (cedentDBA2.getLength() > 1) 
                            {*/
                            
                            
                            if (inference == true && shorter == false) {
                            	
                            	if (connective.equals("both")){
                            		queryHasCategory += "(" + bbaMake(fieldList, axisCedent+axisDBA1+axisDBA2, false) + " or " + bbaMake(fieldList, axisCedent+axisDBA1+"/DBA", true) + ")";
                            	} else {
                            		queryHasCategory += "(" + bbaMake(fieldList, axisCedent+axisDBA1+axisDBA2, false) + " or " + bbaMake(fieldList, axisCedent+axisDBA1+"/DBA[@connective!=\"" + connective + "\"]", true) + ")";
                            	}
                            	
                            }                          
                            
                            if (shorter == true && inference == false) {
                            	
                            	queryHasCategory += "count(" + bbaMake(fieldList, axisCedent+axisDBA1+axisDBA2, false) + ") > 0";
                            	
                            	if (l > 0) {
                            		queryHasNotCategory += " and ";
                            		countCondition += " and "; 
                            	}
                            	
                            	queryHasNotCategory += "count(" + bbaMake(fieldList, axisCedent+axisDBA1+axisDBA2, true) + ") = 0";
                            	
                            	countCondition +=  "FieldName != \"" + getBbaName(fieldList) + "\"";
                            	
                            }
                            /*} else {
                                output += bbaMake(fieldList, axisCedent+axisDBA1+axisDBA2, false);
                            }*/
                        }
                    }
                }
            }
    	}
        String length = "";
        if (countCondition.length() > 0) {
        	length = " and count(" + axisCedent + "/DBA/DBA/BBA/" + DICTIONARY + "[" + countCondition + "]) = 0";
        }
    	return "(" + queryHasCategory + ") and (" + queryHasNotCategory + ")" + length;
    }
    
//    String output = "";
//    output = "";
//    String axisCedent = "";
//    String axisDBA1 = "";
//    String axisDBA2 = "";
//    if (cedentList.getLength() > 0) {
//        axisCedent = "";
//        axisCedent += cedentList.item(0).getNodeName();
//        for (int j = 0; j < cedentList.getLength(); j++){
//            Element cedentElement = (Element)cedentList.item(j);
//            NodeList cedentDBA1 = cedentElement.getChildNodes();
//            for (int k = 0; k < cedentDBA1.getLength(); k++){
//                Element cedentDBA1Element = (Element) cedentDBA1.item(k);
//                axisDBA1 = "";
//                if (cedentDBA1Element.getAttribute("connective").equals("AnyConnective") || cedentDBA1Element.getAttribute("connective").equals("Any")) {
//                    axisDBA1 += "/DBA";
//                } else {
//                    axisDBA1 += "/DBA[@connective=\""+cedentDBA1Element.getAttribute("connective").toString() +"\"]";
//                }
//                NodeList cedentDBA2 = cedentDBA1Element.getChildNodes();
//                for (int l = 0; l < cedentDBA2.getLength(); l++){
//                    Element cedentBBAElement = (Element)cedentDBA2.item(l);
//                    NodeList cedentBBA = cedentBBAElement.getChildNodes();
//                    for (int m = 0; m < cedentBBA.getLength(); m++){
//                        Element bbaElement = (Element)cedentBBA.item(m);
//                        axisDBA2 = "";
//                        boolean inference = false;
//                        String connective = cedentBBAElement.getAttribute("connective").toString();
//                        if (connective.toLowerCase().equals("positive")) { connective = "Conjunction"; }
//                        if (cedentBBAElement.getAttribute("inference").toLowerCase().equals("true")) { inference = true; }
//                        if (connective.toLowerCase().equals("both")) {
//                            axisDBA2 += "/DBA";
//                        } else {
//                            axisDBA2 += "/DBA[@connective=\""+ connective +"\"]";
//                        }
//                        String bbaConnection = "and";
//                        if (exception == true) {
//                        	bbaConnection = "or";
//                        }
//                        NodeList fieldList = bbaElement.getElementsByTagName("Field");
//                        if (l > 0) { output += " "+ bbaConnection +" "; }
//                        /*if (cedentDBA2.getLength() > 1) 
//                        {*/
//                            output += "(" + BBAMake(fieldList, axisCedent+axisDBA1+axisDBA2, false);
//                            if (inference) {
//                                if (connective.equals("both")){
//                                    output += " or " + BBAMake(fieldList, axisCedent+axisDBA1+"/DBA", true);
//                                } else {
//                                    output += " or " + BBAMake(fieldList, axisCedent+axisDBA1+"/DBA[@connective!=\"" + connective + "\"]", true);
//                                }
//                            }
//                            output  += ")";
//                        /*} else {
//                            output += BBAMake(fieldList, axisCedent+axisDBA1+axisDBA2, false);
//                        }*/
//                    }
//                }
//            }
//        }
//     }
//    return output;

	/**
	 * Metoda pro ziskani omezeni poctu vysledku
	 * 
	 * @param xmlQuery
	 *            vstupni XML dotaz
	 * @return maximalni pocet vysledku
	 */
	public static Integer getMaxResults(InputStream xmlQuery) {
		int maxResInt = 200;
		try {
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();

			Document doc = db.parse(xmlQuery);
			doc.getDocumentElement().normalize();

			NodeList maxResultsList = doc.getElementsByTagName("MaxResults");
			Element maxResultsElement = (Element) maxResultsList.item(0);
			NodeList maxResultsList2 = maxResultsElement.getChildNodes();
			if (maxResultsList2.item(0) != null) {
				Node maxResults = maxResultsList2.item(0);
				maxResInt = Integer.parseInt(maxResults.getNodeValue());
			}
			return maxResInt;
		} catch (DOMException e) {
			logger.warning("Error occured during getting max results restriction! - DOM exception");
			return null;
		} catch (ParserConfigurationException e) {
			logger.warning("Error occured during getting max results restriction! - Parser configuration exception");
			return null;
		} catch (IOException e) {
			logger.warning("Error occured during getting max results restriction! - IO exception");
			return null;
		} catch (SAXException e) {
			logger.warning("Error occured during getting max results restriction! - SAX exception");
			return null;
		}
	}

	/**
	    * Metoda pro vytvoreni XPath dotazu, ktery pomaha vybrat pravidla u exception query 
	    * @param xmlQuery vstupni XML dotaz
	    * @return XPath dotaz
	    */
	   public static String getExceptionQuery(NodeList consList) {
		   String output = "";
	       Element consElement = (Element) consList.item(0);
	       NodeList dbas = consElement.getChildNodes();
	       for (int a = 0; a < dbas.getLength(); a++) {
	    	   Element dba = (Element) dbas.item(a);
	    	   NodeList dbas2 = dba.getChildNodes();
	    	   for (int b = 0; b < dbas2.getLength(); b++) {
	    		   if (b > 0) {
	    			   output += " and (";
	    		   } else {
	    			   output += "(";
	    		   }
	    		   Element dba2 = (Element) dbas2.item(b);
	    		   String connectiveAttribute = dba2.getAttribute("connective").toString();
	    		   int goTo = 1;
	    		   if (connectiveAttribute.toLowerCase().equals("positive") || connectiveAttribute.toLowerCase().equals("negative")) {
	    			   goTo = 2;
	    		   }
	    		   for (int z = 0; z < goTo; z++) {
	    			   String connective = "";
	        		   if (connectiveAttribute.toLowerCase().equals("positive") && z > 0) {
	        			   connective = "[@connective=\"Negative\"]";
	        		   } else if (connectiveAttribute.toLowerCase().equals("negative") && z > 0) {
	        			   connective = "[@connective=\"Positive\"]";
	        		   } else {
	        			   connective = "";
	        		   }
	        		   String connect = "";
	        		   if (z == 1) { connect = " or "; }
	        		   output += connect + "Consequent/DBA/DBA" + connective;

	        		   NodeList bbas = dba2.getChildNodes();
	        		   for (int c = 0; c < bbas.getLength(); c++) {
	        			   Element bbaElement = (Element) bbas.item(c);

	        			   NodeList fields = bbaElement.getChildNodes();
	        			   for (int d = 0; d < fields.getLength(); d++) {
	        				   Element fieldElement = (Element) fields.item(d);
	        				   if (fieldElement.getAttribute("dictionary").toLowerCase().equals(DICTIONARY.toLowerCase())) {
	        					   NodeList nameList = fieldElement.getElementsByTagName("Name");
		    		               NodeList catsList = fieldElement.getElementsByTagName("Category");
		    		               
		    		               Element nameElement = (Element) nameList.item(0);
		    		               Node name = nameElement.getChildNodes().item(0);
		    		               String fieldName = name.getNodeValue();
		    		               output += "/BBA/" + DICTIONARY + "[";
		    		               for (int e = 0; e < catsList.getLength(); e++) {
		    		            	   Element catElement = (Element) catsList.item(e);
		    		            	   Node cat = catElement.getChildNodes().item(0);
		    		            	   String catName = cat.getNodeValue();
		    		            	   String catNameCondition = "CatName=\"" + catName + "\"";
		    		            	   if (z == 0) {
		    		            		   catNameCondition = "CatName!=\"" + catName + "\"";
		    		            	   }
		    		            	   output += "FieldName=\"" + fieldName + "\" and " + catNameCondition;
		    		               }
		    		               output += "]";
	        				   }
	        			   }
	        		   }
	    		   }
	    		   output += ")";
	    	   }
	    	   output += " and count(Consequent//BBA)=" + dbas2.getLength();
		   }
		   return output;
	   }

	public boolean isException(InputStream inputQuery) {
		boolean isException = false;
		try {
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();

			Document doc = db.parse(inputQuery);
			doc.getDocumentElement().normalize();

			NodeList consSettingList = doc.getElementsByTagName("Consequent");
			Element consSettingElement = (Element) consSettingList.item(0);
			String exceptionString = consSettingElement.getAttribute("exception");
			if (exceptionString != null && exceptionString != "") {
				isException = Boolean.parseBoolean(exceptionString);
			}
		} catch (ParserConfigurationException e) {
			return isException;
		} catch (SAXException e) {
			return isException;
		} catch (IOException e) {
			return isException;
		} catch (NullPointerException e) {
			return isException;
		}
		return isException;
	}
	   
	   private static String getBbaName(NodeList fieldList) {
	       for (int j = 0; j < fieldList.getLength(); j++) {
	           Element fieldChildElement = (Element)fieldList.item(j);
	           if (fieldChildElement.getAttribute("dictionary").toLowerCase().equals(DICTIONARY.toLowerCase())) {
	               NodeList namesList = fieldChildElement.getElementsByTagName("Name");

	               Element nameElement = (Element)namesList.item(0);
	               NodeList names = nameElement.getChildNodes();
	               Node name = names.item(0);
	               return name.getNodeValue();
	           }
	       }
	       return null;
	   }

}
