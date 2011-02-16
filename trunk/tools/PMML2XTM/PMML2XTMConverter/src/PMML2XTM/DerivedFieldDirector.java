/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package PMML2XTM;

import java.io.IOException;
import java.net.MalformedURLException;
import java.util.Iterator;
import java.util.List;
import net.ontopia.topicmaps.impl.tmapi2.TopicImpl;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapImpl;

import org.dom4j.Element;


/**
 *
 * @author Marek
 */
public class DerivedFieldDirector {
    
    private TopicImpl derivedf;
    private TopicImpl derivedt;
    private TopicImpl derivedo;
    
    private String aHaveDataType = "http://keg.vse.cz/dmo/havedatatype";
    private String aHaveValueType = "http://keg.vse.cz/dmo/havevaluetype";
    private String aHaveBin = "http://keg.vse.cz/dmo/havebin";
    private String aHaveClosure = "http://keg.vse.cz/dmo/haveclosure";
    private String aContainValue = "http://keg.vse.cz/dmo/containvalue";
    private String aConsistOfTransformations = "http://keg.vse.cz/dmo/consistoftransformations";
    private String aDefineBinOrdering = "http://keg.vse.cz/dmo/definebinordering";
    private String aApplyToBin = "http://keg.vse.cz/dmo/applytobin";
    private String aComposedOf = "http://keg.vse.cz/dmo/composeofordereredvalues";

    private String lDerivedField = "http://www.dmg.org/PMML-4_0#DerivedField";
    private String lDataType = "http://keg.vse.cz/dmo/datatype";
    private String lValue = "http://www.dmg.org/PMML-4_0#Value";
    private String lOptype = "http://keg.vse.cz/dmo/optype";
    private String lDiscretizationBin = "http://www.dmg.org/PMML-4_0#DiscretizeBin";
    private String lDiscretize = "http://www.dmg.org/PMML-4_0#Discretize";
    private String lClosureType = "http://keg.vse.cz/dmo/intervaclosuretype";
    private String lLeftMargin = "http://www.dmg.org/PMML-4_0#leftMargin";
    private String lRightMargin = "http://www.dmg.org/PMML-4_0#rightMargin";
    private String lDisplayName = "http://www.dmg.org/PMML-4_0#displayName";
    private String lValueMappingBin = "http://keg.vse.cz/dmo/valuemappingbin";
    private String lMapValues = "http://www.dmg.org/PMML-4_0#MapValues";
    private String lTransformationDictionary = "http://www.dmg.org/PMML-4_0#TransformationDictionary";
    private String lBinOrdering = "http://keg.vse.cz/binordering";
    private String lOrderedBin = "http://keg.vse.cz/dmo/orderedbin";
    private String lOrder = "http://keg.vse.cz/dmo/order";
    private String lIntervalRole = "http://keg.vse.cz/dmo/intervalrole";
    private String lField = "http://keg.vse.cz/dmo/field";
    private String lDerivedFieldContent = "http://keg.vse.cz/dmo/derivedfieldcontent";
    
    private final Storage storage;
    private final TopicMapImpl topicmap;
    private final TMHandler generator;

    DerivedFieldDirector(Storage storage, TopicMapImpl map) {
        this.storage = storage;
        this.topicmap = map;
        this.generator = new TMHandler(storage, topicmap);
    }

    public void direct(List polozkyDF) throws MalformedURLException, IOException{
    Iterator DFList = polozkyDF.iterator();
    
    TopicImpl transDictionary = generator.getTopicTypeInstance(lTransformationDictionary);

     while (DFList.hasNext()){
        String type = null;
        String dfName=null;
        org.dom4j.Element df = (org.dom4j.Element)DFList.next();

        String dfOType = df.attributeValue("optype");
        //System.out.println("OpType: "+dfOType);

        if(dfOType.equals("categorical") ^ dfOType.equals("ordinal")){
        //System.out.println("categorical");
        type=lMapValues;
        //Element mapName = (Element) df.selectSingleNode("./MapValues");
        //dfName = mapName.attributeValue("outputColumn");
        dfName = df.attributeValue("name");
        }

        if(dfOType.equals("continuous")){
        //System.out.println("continuous");
        type=lDiscretize;
        dfName = df.attributeValue("name");
        }

        // Vytvorenie instancie DerivedField
        //System.out.println("Type: "+type);
        //System.out.println("Name: "+dfName);
        derivedf = generator.createInstance(type, dfName, "DerivedField");


        generator.createAssocAB(aConsistOfTransformations, transDictionary, lTransformationDictionary, derivedf, lDerivedField);

        // Vytvorenie instancie Datatype
        String dfDType = df.attributeValue("dataType");
        derivedt = generator.createInstance(lDataType, dfDType, "DataType");
        // Vytvorenie instancie DerivedField - DataType
        generator.createAssocAB(aHaveDataType, derivedf, lDerivedField, derivedt, lDataType);

        // Vytvorenie instancie Optype
        derivedo = generator.createInstance(lOptype, dfOType, "OpType");
        // Vytvorenie instancie DerivedField - Optype
        generator.createAssocAB(aHaveValueType, derivedf, lField, derivedo, lOptype);

        String optypevalue = df.attributeValue("optype");
        if (optypevalue.equals("continuous")){

        TopicImpl binOrdering = generator.createInstance(lBinOrdering, dfName, "ordering");
        generator.createAssocAB(aDefineBinOrdering, derivedf, lDerivedField, binOrdering, lBinOrdering);

            this.Discretize(df, derivedf, binOrdering);
            }
        else if (optypevalue.equals("categorical")){
           this.MapValues(df, derivedf);
            }
        else if (optypevalue.equals("ordinal")){
           this.MapValues(df, derivedf);
            }
    }
    }

    public void Discretize(Element df, TopicImpl dfield, TopicImpl binOrdering) throws MalformedURLException{

    List polozky = df.selectNodes("./pmml:Discretize/pmml:DiscretizeBin");
    int order = 0;
    Iterator it = polozky.iterator();
        while (it.hasNext()){
          order = order + 1;
          //System.out.println("order "+order);
          Element db = (org.dom4j.Element)it.next();
          String dName = db.attributeValue("binValue");
          //System.out.println("vypis: "+db);

          // Vytvorenie instancie DiscretizationBin
          TopicImpl discbin = generator.createInstance(lDiscretizationBin,dName, "discBin");
          // Vytvoreni asociacie derivedfield - discretization bin
          generator.createAssocAB(aHaveBin, discbin, lDerivedFieldContent, dfield, lDerivedField);

          TopicImpl orderedBin = generator.createInstance(lOrderedBin, dName, "ordered");
          generator.createAssocAB(aComposedOf, binOrdering, lBinOrdering, orderedBin, lOrderedBin);
          generator.createAssocAB(aApplyToBin, discbin, lDerivedFieldContent, orderedBin, lOrderedBin);
          generator.createOccurence(orderedBin, lOrder, ""+order+"");

          Element extension = (Element) db.selectSingleNode("./pmml:Extension");

          Element interval = (Element) db.selectSingleNode("./pmml:Interval");

          // Vytvorenie instancie ClosureType
          String closureName = interval.attributeValue("closure");
          TopicImpl closure = generator.createInstance(lClosureType, closureName, "closure");
 
          // Vytvoreni asociacie DiscretizationBin - haveClosure
          generator.createAssocAB(aHaveClosure, discbin, lIntervalRole, closure, lClosureType );

          this.DirectMargins(discbin, interval);
          this.DirectExtension(discbin, extension);
        }
    }

   public void MapValues(Element df, TopicImpl dfield) throws MalformedURLException{

    String dfName = df.attributeValue("name");
    List polozky = df.selectNodes("./pmml:MapValues/pmml:InlineTable/pmml:Extension");
    Element fcp = (Element) df.selectSingleNode("./pmml:MapValues/pmml:FieldColumnPair");
    String columnType = fcp.attributeValue("column");

    //System.out.println("?"+columnType);
    Iterator it = polozky.iterator();
        while (it.hasNext()){
          Element mv = (org.dom4j.Element)it.next();
          //System.out.println("MapValues: "+mv);
          String extensionName = mv.attributeValue("extender");

          // Vytvorenie instancie Mapping Bin

          TopicImpl mapbin = generator.createInstance(lValueMappingBin, extensionName,"vmBin");
          //System.out.println("DISC : "+ mapbin);
          // Vytvoreni asociacie derivedfield - discretization bin
          generator.createAssocAB(aHaveBin, mapbin, lDerivedFieldContent, dfield, lDerivedField);
          this.ContainValues(df, dfName, extensionName, mapbin);
        }
    }

   public void DirectAssociationModel(List polozkyAM){

   }

   private void DirectMargins(TopicImpl discbin, Element interval) throws MalformedURLException {
       // Vytvorenie instancie DiscretizationBin - leftMargin
       String lMarginValue = interval.attributeValue("leftMargin");
       String rMarginValue = interval.attributeValue("rightMargin");

       // Vytvoreni occurence DiscretizationBin - leftMargin
       generator.createOccurence(discbin, lLeftMargin, lMarginValue);
       generator.createOccurence(discbin, lRightMargin, rMarginValue);
   }

    private void DirectExtension(TopicImpl discbin, Element extension) throws MalformedURLException {
       // Vytvorenie instancie DiscretizationBin - leftMargin
       String extenderValue = extension.attributeValue("extender");

       // Vytvoreni occurence DiscretizationBin - leftMargin
       generator.createOccurence(discbin, lDisplayName, extenderValue);
    }

    private void ContainValues(Element df, String dfName, String extName, TopicImpl mapbin) throws MalformedURLException {
        Element mv = (Element) df.selectSingleNode("./pmml:MapValues");
        String suffix = "";
        String output = mv.attributeValue("outputColumn");

        if(output.equals(dfName+"Agregovane")){
            suffix = "Agregovane";
        }
        List rows = mv.selectNodes("./pmml:InlineTable/pmml:row");
        //System.out.println("ROWS: "+rows);
        Iterator it = rows.iterator();
        while (it.hasNext()){
            Element radek =  (Element) it.next();

            if(suffix.equals("")){
                //List radky = radek.selectNodes("./pmml:"+dfName);
                //Element rowElement = (Element) radky.get(0);
                //Element rowAElement = (Element) radky.get(1);
                //String row = rowElement.getText();
                //String rowA = rowAElement.getText();
                String row = radek.selectSingleNode("./pmml:column").getStringValue() ;
                String rowA = radek.selectSingleNode("./pmml:field").getStringValue();

                String valueName = row;
                if(rowA.equals(extName)){
                    TopicImpl value = generator.createInstance(lValue, valueName, "Value");
                    generator.createAssocAB(aContainValue, mapbin, lValueMappingBin, value, lValue);
                    generator.createOccurence(value, lValue, row);
                }
            }

            else if(suffix.equals("Agregovane")){
                String row = radek.selectSingleNode("./pmml:"+dfName).getText();
                String rowA =  radek.selectSingleNode("./pmml:"+dfName+suffix).getText();
                String valueName = row;
                if(rowA.equals(extName)){
                    TopicImpl value = generator.createInstance(lValue, valueName, "Value");
                    generator.createAssocAB(aContainValue, mapbin, lValueMappingBin, value, lValue );
                    //System.out.println("contain "+ mapbin + " - " +value);
                    generator.createOccurence(value, lValue, row);
                }
            }
        }
    }

}
