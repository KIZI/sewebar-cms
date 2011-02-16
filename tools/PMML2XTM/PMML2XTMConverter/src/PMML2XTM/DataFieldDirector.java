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
 * @author marek
 */
public class DataFieldDirector {
    private TopicImpl dataf;
    private TopicImpl datav;
    private TopicImpl datat;
    private TopicImpl datao;
   
    private String aHaveDataType = "http://keg.vse.cz/dmo/havedatatype";
    private String aHaveValueType = "http://keg.vse.cz/dmo/havevaluetype";
    private String aHaveFieldContent = "http://keg.vse.cz/dmo/havefieldcontent";
    private String aHaveValueProperty = "http://keg.vse.cz/dmo/havevalueproperty";
    private String aHaveCyclicity = "http://keg.vse.cz/dmo/havecyclicity";
    private String aHaveClosure = "http://keg.vse.cz/dmo/haveclosure";

    private String lDataField = "http://www.dmg.org/PMML-4_0#DataField";
    private String lDataType = "http://keg.vse.cz/dmo/datatype";
    private String lValue = "http://www.dmg.org/PMML-4_0#Value";
    private String lOptype = "http://keg.vse.cz/dmo/optype";
    private String lConsistOfFields = "http://keg.vse.cz/dmo/consistofdataields";
    private String lDataDictionary = "http://www.dmg.org/PMML-4_0#DataDictionary";
    private String lValueProperty = "http://keg.vse.cz/dmo/valueproperty";
    private String lField = "http://keg.vse.cz/dmo/field";
    private String lDisplayName = "http://www.dmg.org/PMML-4_0#displayName";
    private String lDisplayValue = "http://www.dmg.org/PMML-4_0#displayValue";
    private String lOcValue = "http://www.dmg.org/PMML-4_0#value";
    private String lDataFieldContent = "http://keg.vse.cz/dmo/datafieldcontent";
    private String lCyclic = "http://keg.vse.cz/dmo/cyclicity";
    private String lInterval = "http://www.dmg.org/PMML-4_0#Interval";
    private String lIntervalClosureType = "http://keg.vse.cz/dmo/intervaclosuretype";
    private String lIntervalRole = "http://keg.vse.cz/dmo/intervalrole";
    private String lLeftMargin = "http://www.dmg.org/PMML-4_0#leftMargin";
    private String lRightMargin = "http://www.dmg.org/PMML-4_0#rightMargin";

    private final Storage storage;
    private final TopicMapImpl map;
    TMHandler generator;

    DataFieldDirector(Storage storage, TopicMapImpl map) {
        this.storage = storage;
        this.map = map;
        this.generator = new TMHandler(storage, map);
    }

    public void direct(List polozkyDF) throws MalformedURLException, IOException{
    Iterator DFList = polozkyDF.iterator();
    
    TopicImpl dataDictionary = generator.getTopicTypeInstance(lDataDictionary);

    while (DFList.hasNext()){
        org.dom4j.Element df = (org.dom4j.Element)DFList.next();
        
        // Vytvorenie instancie DataField
        String dfName = df.attributeValue("name");
        dataf = generator.createInstance(lDataField, dfName, "DataField");

        generator.createOccurence(dataf, lDisplayName, dfName);

        // Vytvorenie asociacie DataDictionary - DataField

        generator.createAssocAB(lConsistOfFields, dataf, lDataField, dataDictionary, lDataDictionary);

        //System.out.println("<-- DataField Type -->");

        // Vytvorenie instancie DataType
        String dtName = df.attributeValue("dataType");
        datat = generator.createInstance(lDataType, dtName, "DataType");
        // Vytvorenie asociacie DataField - DataType
        generator.createAssocAB(aHaveDataType, dataf, lDataField, datat, lDataType);

        // Vytvorenie instancie Optype
        String otName = df.attributeValue("optype");
        datao = generator.createInstance(lOptype, otName, "OpType");
        // Vytvorenie asociacie DataField - DataType
        generator.createAssocAB(aHaveValueType, dataf, lField, datao, lOptype);

        String cyclic;
        // Vytvorenie instancie Cyclicity
        String cyc;
        
        if(df.attributeValue("isCyclic") == null){
            cyc = "0";
        }
        else{cyc = df.attributeValue("isCyclic");}
        
        if(cyc.equals("1")){
        cyclic = "Is Cyclic";
        }
        else{
        cyclic = "Is Not Cyclic";
        }
        TopicImpl cyclicTopic = generator.createInstance(lCyclic, cyclic, "c");
        // Vytvorenie asociacie DataField - DataType
        generator.createAssocAB(aHaveCyclicity, dataf, lDataField, cyclicTopic, lCyclic);

        if(df.selectNodes("./pmml:Value") != null){
            directValue(df);
        }
        if(df.selectNodes("./pmml:Interval") != null){
            directInterval(df);
        }

        //System.out.println("<-- DataField Value -->");

        }



        //generator.saveMap();
    }

    private void directValue(Element df){

        List datafieldvalue = df.selectNodes("./pmml:Value");
        //System.out.println("Value: "+datafieldvalue);
        Iterator i = datafieldvalue.iterator();

        while (i.hasNext()){
            org.dom4j.Element value = (org.dom4j.Element)i.next();

            // Vytvorenie instancie DataFieldValue
            String valueName = value.attributeValue("value");
            datav = generator.createInstance(lValue, valueName, "Value");
            // Vytvorenie asociacie Datafield DataValue
            generator.createAssocAB(aHaveFieldContent, dataf, lDataField, datav, lDataFieldContent);

            generator.createOccurence(datav, lDisplayValue, valueName);
            generator.createOccurence(datav, lOcValue, valueName);

            String valueProp = value.attributeValue("property");
            //System.out.println("ValueProp: "+ valueProp);
            if(valueProp != null){
                TopicImpl valueProperty = generator.createInstance(lValueProperty, valueProp, "property");
                generator.createAssocAB(aHaveValueProperty, datav, lValue, valueProperty, lValueProperty);
            }

        }
    }

    private void directInterval(Element df){

         List datafieldvalue = df.selectNodes("./pmml:Interval");
         String valueName = df.attributeValue("name");
         //System.out.println("Value: "+datafieldvalue);
         Iterator i = datafieldvalue.iterator();

         while (i.hasNext()){
            org.dom4j.Element value = (org.dom4j.Element)i.next();
            // Vytvorenie instancie DataFieldValue
            datav = generator.createInstance(lInterval, valueName, "Value");
            // Vytvorenie asociacie Datafield DataValue
            generator.createAssocAB(aHaveFieldContent, dataf, lDataField, datav, lDataFieldContent);

            String closure = value.attributeValue("closure");
            //System.out.println("Closure: "+ closure);
            if(closure != null){
                TopicImpl closureTopic = generator.createInstance(lIntervalClosureType, closure, "closure");
                generator.createAssocAB(aHaveClosure, datav, lIntervalRole, closureTopic, lIntervalClosureType);
            }

            String leftMargin = value.attributeValue("leftMargin");
            generator.createOccurence(datav, lLeftMargin, leftMargin);

            String rightMargin = value.attributeValue("leftMargin");
            generator.createOccurence(datav, lRightMargin, leftMargin);

         }
    }

}