/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package PMML2XTM;

import java.io.IOException;
import java.util.HashMap;
import java.util.List;
import javax.xml.parsers.ParserConfigurationException;
import org.dom4j.Document;
import org.dom4j.DocumentException;
import org.dom4j.Element;
import org.dom4j.io.SAXReader;
import org.jaxen.JaxenException;
import org.jaxen.SimpleNamespaceContext;
import org.jaxen.dom4j.Dom4jXPath;
import org.xml.sax.SAXException;
/**
 *
 * @author Marek
 */
public class PMMLReader{

  private Document doc;
  private Element PMML;
  private Element AModel;

  public static void PMMLReader() {
  }

  public void openfile(String file) throws ParserConfigurationException, SAXException, IOException, DocumentException, JaxenException{
    SAXReader reader = new SAXReader();
    Document document = reader.read(file);
    this.doc = document;
    setPMMLNamespace();
    setGUHANamespace();
  }

  private void setPMMLNamespace() throws JaxenException{
      HashMap map = new HashMap();
      map.put( "pmml", "http://www.dmg.org/PMML-4_0");
      Dom4jXPath xpath = new Dom4jXPath("//pmml:PMML");
      xpath.setNamespaceContext( new SimpleNamespaceContext(map));
      PMML = (Element) xpath.selectSingleNode(doc);
      System.out.println(PMML);
  }

  private void setGUHANamespace() throws JaxenException{
      HashMap map = new HashMap();
      map.put("guha", "http://keg.vse.cz/ns/GUHA0.1rev1");
      Dom4jXPath xpath = new Dom4jXPath("//guha:AssociationModel");
      xpath.setNamespaceContext( new SimpleNamespaceContext(map));
      AModel = (Element) xpath.selectSingleNode(doc);
      System.out.println(PMML);
  }

  public Element getHeader() throws JaxenException{

  Element polozky = (Element) PMML.selectSingleNode("./pmml:Header");
  System.out.println(PMML);
  System.out.println(PMML.attributeValue("version") );
  System.out.println("Header: "+polozky);
  return polozky;
  }

  public Element getDataDictionary(){
    Element polozka = (Element) PMML.selectSingleNode("./pmml:DataDictionary");
    return polozka;
  }

  public List getDataField(){
    
    List polozky = PMML.selectNodes("./pmml:DataDictionary/pmml:DataField");
    System.out.println("DataField: "+polozky);
    return polozky;
  }
  
  public List getDerivedField(){
    
    List polozky = PMML.selectNodes("./pmml:TransformationDictionary/pmml:DerivedField");
    System.out.println("DerivedField: "+polozky);
    return polozky;
  }

  public Element getAssociationModel() {
    
    
    System.out.println("AssociationModel: "+AModel);
    return AModel;
  }

}