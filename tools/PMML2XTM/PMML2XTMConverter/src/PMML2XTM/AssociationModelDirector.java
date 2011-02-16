/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package PMML2XTM;

import java.io.IOException;
import java.net.MalformedURLException;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.SortedSet;
import java.util.TreeMap;
import net.ontopia.topicmaps.impl.tmapi2.TopicImpl;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapImpl;
import org.dom4j.Element;


/**
 *
 * @author marek
 */
public class AssociationModelDirector {

    private String aBeTransformed = "http://keg.vse.cz/dmo/transformed";
    private String aHaveCoefficienType = "http://keg.vse.cz/dmo/havecoefficienttype";
    private String aContainAssociationRules = "http://keg.vse.cz/dmo/containassociationrules";
    private String aHaveAntecedent = "http://keg.vse.cz/dmo/haveantecedent";
    private String aHaveConsequent = "http://keg.vse.cz/dmo/haveconsequent";
    private String aHaveCondition = "http://keg.vse.cz/dmo/havecondition";
    private String aHaveBoolAttrType = "http://keg.vse.cz/dmo/havebooleanattributetype";
    private String aBeDerivedFrom = "http://keg.vse.cz/dmo/bederivedfrom";
    private String aHaveCoeficient = "http://keg.vse.cz/dmo/havecoefficient";
    private String aHaveIntereseMeasureValue = "http://keg.vse.cz/dmo/haveinterestmeasurevalue";
    private String aHaveIMType = "http://keg.vse.cz/dmo/haveinterestmeasuretype";
    private String aHaveConditionSetting = "http://keg.vse.cz/dmo/haveconditionsetting";
    private String aHaveConsequentSetting = "http://keg.vse.cz/dmo/haveconsequentsetting";
    private String aHaveAntecedentSetting = "http://keg.vse.cz/dmo/haveantecedentsetting";
    private String aConsistOfBAS = "http://keg.vse.cz/dmo/consistofbooleanattributesettings";
    private String aHaveFieldUsageType = "http://keg.vse.cz/dmo/havefieldusagetype";
    private String aContainsMiningField = "http://keg.vse.cz/dmo/containminingfields";
    private String aHaveInvalid = "http://keg.vse.cz/dmo/haveinvalidvaluetreatment";
    private String aHaveMissing = "http://keg.vse.cz/dmo/havemissingvaluetreatment";
    private String aHaveARS = "http://keg.vse.cz/dmo/haveassociationrulesetting";
    private String aHaveIMS = "http://keg.vse.cz/dmo/haveinterestmeasuresetting";
    private String aHaveOutlier = "http://keg.vse.cz/outliertreatmentmethod";
    private String aBeUsedAs = "http://keg.vse.cz/dmo/beusedinmodelas";
    private String aBeGreatherThan = "http://keg.vse.cz/dmo/begreaterthan";
    private String aUseSchema = "http://keg.vse.cz/dmo/useschema";
    private String aHaveSuccedent = "";

    private String lMiningField = "http://www.dmg.org/PMML-4_0#MiningField";
    private String lDerivedField = "http://www.dmg.org/PMML-4_0#DerivedField";
    private String lBBASetting = "http://keg.vse.cz/dmo/BasicBooleanAttributeSetting";
    private String lDBASetting = "http://keg.vse.cz/dmo/DerivedBooleanAttributeSetting";
    private String lARSetting = "http://keg.vse.cz/dmo/AssociationRuleSetting";
    private String lCoefficientType = "http://keg.vse.cz/dmo/CoefficientType";
    private String lMinimalLength = "http://keg.vse.cz/dmo/minimallength";
    private String lMaximalLength = "http://keg.vse.cz/dmo/maximallength";
    private String lAssociationRule = "http://www.dmg.org/PMML-4_0#associationrule";
    private String lMiningModel = "http://www.dmg.org/PMML-4_0#MiningModel";
    private String lBooleanAttribute = "http://keg.vse.cz/dmo/booleanattribute";
    private String lBasicBooleanAtribute = "http://keg.vse.cz/dmo/basicbooleanattribute";
    private String lDerivedBooleanAtribute = "http://keg.vse.cz/dmo/derivedbooleanattribute";
    private String lCondition = "http://www.dmg.org/PMML-4_0#condition";
    private String lConnective = "http://keg.vse.cz/dmo/connective";
    private String lUnaryOperation = "http://keg.vse.cz/dmo/unaryoperation";
    private String lCoefficient = "http://keg.vse.cz/dmo/coefficient";
    private String lInterestMeasureValue = "http://keg.vse.cz/dmo/interestmeasurevalue";
    private String lInterestMeasure = "http://keg.vse.cz/dmo/interestmeasure";
    private String lValueLocator = "http://keg.vse.cz/dmo/value";
    private String l4ftquantifier = "http://keg.vse.cz/dmo/4ftquantifier";
    private String lAntecedent = "http://www.dmg.org/PMML-4_0#antecedent";
    private String lConsequent = "http://www.dmg.org/PMML-4_0#consequent";
    private String lBooleanAtributeSetting = "http://keg.vse.cz/dmo/BooleanAttributeSetting";
    private String lInterestMeasureSetting = "http://keg.vse.cz/dmo/interestmeasuresetting";
    private String lMinimumValue = "http://psi.keg.vse.cz/occ/minimunValue";
    private String lFieldUsageType = "http://keg.vse.cz/dmo/fieldusagetype";
    private String lMiningSchema = "http://www.dmg.org/PMML-4_0#MiningSchema";
    private String lMissingValue = "http://keg.vse.cz/dmo/missingvaluetreatmentmethod";
    private String lInvalidValue = "http://keg.vse.cz/dmo/invalidvaluetreatmentmethod";
    private String lMiningAlgorithmSetting = "http://keg.vse.cz/dmo/miningalgorithmsetting";
    private String lID = "http://keg.vse.cz/dmo/id";
    private String lWeight = "http://www.dmg.org/PMML-4_0#weight";
    private String lBARoleType = "http://keg.vse.cz/dmo/booleanattributetyperole";
    private String lEnumeration = "http://keg.vse.cz/dmo/enumeration";
    private String lOutlierValue = "http://keg.vse.cz/dmo/outliertreatmentmethod";
    private String lDataField = "http://www.dmg.org/PMML-4_0#DataField";
    private String lLowValue = "http://www.dmg.org/PMML-4_0#lowValue";
    private String lHighValue = "http://www.dmg.org/PMML-4_0#highValue";
    private String lImportance = "http://www.dmg.org/PMML-4_0#importance";
    private String lLowerValue = "http://keg.vse.cz/dmo/lowervalue";
    private String lGreaterValue = "http://keg.vse.cz/dmo/greatervalue";
    private String lAssociationModel = "http://www.dmg.org/PMML-4_0#AssociationModel";
    private String lNumberOfRules = "http://www.dmg.org/PMML-4_0#numberOfRules";
    private String lSuccedent = "";

    Storage storage;
    Element aModel;
    PMMLReader reader;
    TMHandler generator;
    TopicImpl modelTopic;
    private Map <String, TopicImpl> zoznamValues = new TreeMap<String, TopicImpl>();
    private final TopicMapImpl topicmap;
    
    AssociationModelDirector(Storage storage, TopicMapImpl map) {
        this.storage = storage;
        this.topicmap = map;
    }

    void direct(Element assocModel) throws IOException {
        generator = new TMHandler(storage, topicmap);

        aModel = assocModel;
        modelTopic = generator.getTopicTypeInstance(lAssociationModel);

        List miningField = assocModel.selectNodes("./MiningSchema/MiningField");
        directMiningSchema(miningField);
       
        Element taskSetting = (Element) assocModel.selectSingleNode("./TaskSetting");
        //System.out.println("TaskSetting: "+ taskSetting);
        System.out.println(">>>>>> Direct BBA Setting ------------");
        directBBAtributeSetting(taskSetting);
        System.out.println(">>>>>> Direct DBA Setting ------------");
        directDBAtributeSetting(taskSetting);
        System.out.println(">>>>>> Direct DBA Setting Sub ------------");
        directDBAtributeSub(taskSetting);
        directAssociationRuleSetting();

        List assocRules = assocModel.selectNodes("./AssociationRules/AssociationRule");
        
        System.out.println("----- Processing BBA -----");
        
        directBBA(assocModel);

        System.out.println("----- Processing DBA -----");

        directDBA();

        System.out.println("----- Processing References -----");

        directRef();

        System.out.println("----- Processing Rules -----");

        directAssociationRule(assocRules);

        directInterestMeasureSetting(aModel);

    }

    private void directBBA(Element assocModel) throws MalformedURLException{
       List bba = assocModel.selectNodes("./AssociationRules/BBA");
       Iterator it = bba.iterator();
       Integer num = 0;
       while(it.hasNext()){
           num = num +1;
           System.out.println("Processing BBA: " + num + " / " + bba.size());
           org.dom4j.Element el = (org.dom4j.Element)it.next();
           String id = el.attributeValue("id");
           String name = el.selectSingleNode("./Text").getStringValue();
           //System.out.println("directDerived - Basic id:" + id );
           TopicImpl basTopic = generator.createInstance(lBasicBooleanAtribute, name, "bas");
           generator.createOccurence(basTopic, lID, id);
           directCoefficient("basic", basTopic, id);
           //System.out.println("---- END BBA ----");
       }

    }

    private void directDBA() throws MalformedURLException{
        List itemSet = aModel.selectNodes("./AssociationRules/DBA");
        Iterator it = itemSet.iterator();
        Integer idd = 0;
        while(it.hasNext()){
           System.out.println("< ---- DBA Start");
           idd = idd + 1;
           System.out.println("Processing DBA: "+ idd + "/" + itemSet.size());
           Element derived = (Element) it.next();
           String name = derived.selectSingleNode("Text").getStringValue();
           String id = derived.attributeValue("id");
           String prefix = getRefType(derived);
           TopicImpl derivedTopic = generator.createInstance(lDerivedBooleanAtribute, name, prefix+"das");
           //System.out.println("Store DBA: "+prefix+name);
           generator.createOccurence(derivedTopic, lID, id);
           //System.out.println("Occurence:" + id);
           String type = derived.attributeValue("connective");
           directConnective(type, derivedTopic, name, prefix);
           System.out.println("< ---- DBA End");
        }
    }

    private String getRefType(Element derived){
        String refType = derived.selectSingleNode("BARef").getStringValue();
        String refType1 = refType.substring(0,3);
            //System.out.println("Obsahuje DBA_FTCedent" + refType.contains("DBA_FTCedent") + " refType: " + refType);
        //System.out.println("SUBSTRING:"+ refType);
        String prefix = "";
        if(refType1.equals("BBA")){
            //System.out.println("REFTYPE: BBA");
            prefix = "1";
        }
        else if(refType1.equals("DBA"))
        {
            if(refType.contains("DBA_FTCedent")){
            //System.out.println("refType Cedent 3: " + refType);
            prefix = "3";
            }
            else if(refType.contains("DBA_FTLiteral")){
            //System.out.println("refType Literal 2: " + refType);
            prefix = "2";
            }
        }
        else{
            prefix = refType1;
        }
        return prefix;
        
    }

    private void directBBAtributeSetting(Element extension) throws MalformedURLException{
        List BBA = (List) extension.selectNodes("./BBASettings/BBASetting");
        
        Iterator it = BBA.iterator();
        while (it.hasNext()){
            org.dom4j.Element bba = (org.dom4j.Element)it.next();
            String name = bba.selectSingleNode("./Name").getStringValue();
            TopicImpl BBASetting = generator.createInstance(lBBASetting, name,"BBA");

            Element coefType = (Element) bba.selectSingleNode("./Coefficient/Type");
            TopicImpl coefTypeInstance = generator.createInstance(lCoefficientType, coefType.getStringValue());
            generator.createAssocAB(aHaveCoefficienType, BBASetting, lBBASetting, coefTypeInstance, lCoefficientType);
            if(coefType.getStringValue().equals("One Category")){
                List category = bba.selectNodes("./Coefficient/Category");
                Iterator itCat = category.iterator();
                while(itCat.hasNext()){
                Element el = (Element) itCat.next();
                String coefName = el.getStringValue();

                TopicImpl catTopic = storage.getTopic("Value"+coefName);
                //generator.createAssocAB(aHaveFixedCategory, BBASetting, lBBASetting, coefTypeInstance, lDerivedFieldContent);
                }
            
            }

            Element minimal = (Element) bba.selectSingleNode("./Coefficient/MinimalLength");
            if(minimal != null){
                generator.createOccurence(BBASetting, lMinimalLength, minimal.getStringValue() );
                //System.out.println(minimal.getData() );
            }
            Element maximal = (Element) bba.selectSingleNode("./Coefficient/MaximalLength");
            if(maximal != null){
                generator.createOccurence(BBASetting, lMaximalLength, maximal.getStringValue());
                //System.out.println(maximal.toString());
            }
        }
    }

    private void directDBAtributeSetting(Element extension) throws MalformedURLException{
        List DBA = (List) extension.selectNodes("./DBASettings/DBASetting");
        Iterator it = DBA.iterator();
        String conType = null;
        String lValue = null;
        while (it.hasNext()){
            
                org.dom4j.Element dba = (org.dom4j.Element) it.next();
                String name = dba.selectSingleNode("Name").getStringValue();
                TopicImpl DBASetting = generator.createInstance(lDBASetting, name, "DBA");

                if(dba.attributeValue("type").equals("Literal") ){
                    if(dba.selectSingleNode("./LiteralSign").getStringValue().equals("Both")){
                    conType = "Negation";
                    lValue = lUnaryOperation;
                    }
                    else{
                    conType = "Literal";
                    lValue = lUnaryOperation;
                    }
                }
                else{
                conType = dba.attributeValue("type");
                lValue = lConnective;
                }
                
                TopicImpl condTopic = generator.createInstance(lValue, conType, "con");

                generator.createAssocAB(aHaveBoolAttrType, DBASetting, lDBASetting, condTopic, lBARoleType);

                Element minimal = (Element) dba.selectSingleNode("./MinimalLength");
                if (minimal != null) {
                    generator.createOccurence(DBASetting, lMinimalLength, minimal.getStringValue());
                 
                }
                Element maximal = (Element) dba.selectSingleNode("./MaximalLength");
                if (maximal != null) {
                    generator.createOccurence(DBASetting, lMaximalLength, maximal.getStringValue());
                    
                }
        }
    }

    private void directDBAtributeSub(Element extension) throws MalformedURLException{
        List BBA = (List) extension.selectNodes("./DBASettings/DBASetting");
        Iterator it = BBA.iterator();
        
        while (it.hasNext()){
                org.dom4j.Element bba = (org.dom4j.Element) it.next();
                String name = bba.selectSingleNode("./Name").getStringValue();
                TopicImpl DBASetting = storage.getTopic("DBA"+name);
                Iterator it2 = bba.selectNodes("./BASettingRef").iterator();
                while(it2.hasNext()){
                Element BAId = (Element) it2.next();
                TopicImpl subTopic = getBooleanAttributeInstance(BAId.getStringValue(), getBAType(BAId.getStringValue()));
                generator.createAssocAB(aConsistOfBAS, DBASetting, lDBASetting, subTopic, lBooleanAtributeSetting);
                }
        }
    }

    private void directMiningSchema(List miningFields) throws MalformedURLException{
        Iterator MFList = miningFields.iterator();
        TopicImpl miningSchema = generator.getTopicTypeInstance(lMiningSchema);

        generator.createAssocAB(aUseSchema, miningSchema, lMiningSchema, modelTopic, lMiningModel);
        while (MFList.hasNext()){
            org.dom4j.Element df = (org.dom4j.Element)MFList.next();
            String name = df.attributeValue("name");
            TopicImpl derivedField = storage.getTopic("DerivedField"+name);

            TopicImpl miningField = generator.createInstance(lMiningField, name);
            generator.createAssocAB(aBeTransformed, derivedField, lDerivedField, miningField, lMiningField );
            generator.createAssocAB(aContainsMiningField, miningSchema, lMiningSchema, miningField, lMiningField);

            TopicImpl dataField = storage.getTopic("DataField"+name);

            generator.createAssocAB(aBeUsedAs, dataField, lDataField, miningField,lMiningField);

            String lowValue = df.attributeValue("lowValue");
            String highValue = df.attributeValue("highValue");
            String importance = df.attributeValue("importance");

            generator.createOccurence(miningField, lLowValue, lowValue);
            generator.createOccurence(miningField, lHighValue, highValue);
            generator.createOccurence(miningField, lImportance, importance);

            String usageName = df.attributeValue("usageType");
            String missingVTM = df.attributeValue("missingValueTreatment");
            String invalidVTM = df.attributeValue("invalidValueTreatment");
            String outlierVTM = df.attributeValue("outliers");

            if(usageName==(null)){
            System.out.println(name +"  nema usageType");
            }
            else{
                TopicImpl usageTopic = generator.createInstance(lFieldUsageType, usageName, "usage");
            generator.createAssocAB(aHaveFieldUsageType, miningField, lMiningField, usageTopic, lFieldUsageType);
            }
            if(missingVTM==(null)){
            System.out.println(name +"  nema missing Value");
            }
            else{
                TopicImpl missingTopic = generator.createInstance(lMissingValue, missingVTM, "mising");
            generator.createAssocAB(aHaveMissing, miningField, lMiningField, missingTopic, lEnumeration);
            }
            if(invalidVTM==(null)){
            System.out.println(name +"  nema invalid Value");
            }
            else{
                TopicImpl invalidTopic = generator.createInstance(lInvalidValue, invalidVTM, "invalid");
            generator.createAssocAB(aHaveInvalid, miningField, lMiningField, invalidTopic, lInvalidValue);
            }

            if(outlierVTM==(null)){
            System.out.println(name +"  nema invalid Value");
            }
            else{
                TopicImpl outlierTopic = generator.createInstance(lOutlierValue, outlierVTM, "outlier");
            generator.createAssocAB(aHaveOutlier, miningField, lMiningField, outlierTopic, lOutlierValue);
            }    
        }
    }

    private void directAssociationRule(List assocRules) throws MalformedURLException {
        
        Iterator it = assocRules.iterator();
        int id = 0;
        Integer pocetRules = assocRules.size();
        //System.out.println("RNumber: "+ pocetRules);
        generator.createOccurence(modelTopic, lNumberOfRules, pocetRules.toString());

        while (it.hasNext()){
            org.dom4j.Element polozka = (org.dom4j.Element)it.next();
            id = id+1;
            System.out.println("AssociationRule: " + id + " / " + pocetRules );

            String antecedentID = polozka.attributeValue("antecedent");
            String antecedentName = getItemItemSetName(antecedentID);

            String consequentID = polozka.attributeValue("consequent");
            String consequentName = getItemItemSetName(consequentID);

            String conditionID  = polozka.attributeValue("condition");
            String conditionName = "";
            String conditionLomitko = "";
            if(conditionID != null){
                conditionName = getItemItemSetName(conditionID);
                conditionLomitko = " / " + conditionName;
            }

            String name = antecedentName + "=>" + consequentName + conditionLomitko;

            TopicImpl assocRule = generator.createInstance(lAssociationRule, name);
            //System.out.println("AssocRule: " + name);
            generator.createAssocAB(aContainAssociationRules, modelTopic, lMiningModel, assocRule, lAssociationRule);
            generator.createOccurence(assocRule, lID, ""+id+"");

            //System.out.println("Processing Antecedent: "+ antecedentName);
            directCedent(assocRule, antecedentName, antecedentID, aHaveAntecedent, lAntecedent);
            //System.out.println("Processing consequent: "+ consequentName);

            directCedent(assocRule, consequentName, consequentID, aHaveConsequent, lConsequent);
            
            if(conditionID != null){
            //System.out.println("Processing Condition: "+ conditionName);
            directCedent(assocRule, conditionName, conditionID, aHaveCondition, lCondition);
            }
            //System.out.println("Processing InterestMeasures");
            directInterestMeasures(assocRule, name, polozka);
        }

    }

    private void directAssociationRuleSetting() throws MalformedURLException{
            Element polozka = (Element) aModel.selectSingleNode("./TaskSetting");

            String consequentID = polozka.selectSingleNode("./ConsequentSetting").getText();
            String antecedentID = polozka.selectSingleNode("./AntecedentSetting").getText();

            String conditionID = null;
            String conditionName = null;
            String conditionType = null;

            if( polozka.selectSingleNode("./ConditionSetting") != null ){
            conditionID =  polozka.selectSingleNode("./ConditionSetting").getText();
            conditionName = getBooleanAttributeName(conditionID);
            conditionType = getBAType(conditionID);
            }
            
            String consequentName = getBooleanAttributeName(consequentID);

            String antecedentName = getBooleanAttributeName(antecedentID);
            

            String consequentType = getBAType(consequentID);
            String antecedentType = getBAType(antecedentID);
            

            //System.out.println("Test: " + consequentID + "-" +consequentType+ "-" +consequentName );

            String name = antecedentName + " => " + consequentName + " / "+ conditionName;

            TopicImpl ARSTopic = generator.createInstance(lARSetting, name, "ARS");

            TopicImpl miningSetting = generator.getTopicTypeInstance(lMiningAlgorithmSetting);
            generator.createAssocAB(aHaveARS, ARSTopic, lARSetting, miningSetting, lMiningAlgorithmSetting);

            TopicImpl consequentTopic = getBooleanAttributeInstance(consequentID, consequentType);
            //System.out.println("CVal: "+consequentID+" "+consequentType);
            //System.out.println("ConsTopic: "+consequentTopic);
      
            generator.createAssocAB(aHaveConsequentSetting, ARSTopic, lARSetting, consequentTopic, lBooleanAtributeSetting);
            
            TopicImpl antecedentTopic = getBooleanAttributeInstance(antecedentID, antecedentType);
            
            generator.createAssocAB(aHaveAntecedentSetting, ARSTopic, lARSetting, antecedentTopic, lBooleanAtributeSetting);

            if( polozka.selectSingleNode("./ConditionSetting") != null ){
            TopicImpl conditionTopic = getBooleanAttributeInstance(conditionID, conditionType);
            generator.createAssocAB(aHaveConditionSetting, ARSTopic, lARSetting, conditionTopic, lBooleanAtributeSetting);
            }
            storage.getTopic("");
    }

    private String getItemItemSetName(String itemId){
    String retValue = null;
    if(aModel.selectSingleNode("./AssociationRules/BBA[@id='"+itemId+"']") != null) {
        Element itemB = (Element) aModel.selectSingleNode("./AssociationRules/BBA[@id='"+itemId+"']");
        retValue = itemB.selectSingleNode("./Text").getStringValue();
    }
    else if(aModel.selectSingleNode("./AssociationRules/DBA[@id='"+itemId+"']") != null) {
        Element itemB = (Element) aModel.selectSingleNode("./AssociationRules/DBA[@id='"+itemId+"']");
        retValue = itemB.selectSingleNode("./Text").getStringValue();
    }
    else{
    System.out.println("Chyba pri ziskavani BA Name z Ba ID!");
    }
    return retValue;
    }

    private String getBooleanType(String id){
        
        List item = aModel.selectNodes("./AssociationRules/BBA");
        Iterator IT = item.iterator();
        while (IT.hasNext()){
            Element itemA = (org.dom4j.Element) IT.next();
            String itemId = itemA.attributeValue("id");

            if(itemId.equals(id)){
                return "basic";
            }
        }
    
        List itemSet = aModel.selectNodes("./AssociationRules/DBA");
        Iterator IT2 = itemSet.iterator();
        while (IT2.hasNext()){
            Element itemB = (org.dom4j.Element) IT2.next();
            String itemSetId = itemB.attributeValue("id");
            
            if(itemSetId.equals(id)){
                return "derived";
            }
        }
        return null;
    }

    private void directCedent(TopicImpl assocRule, String cedentName, String cedentID, String aLoc, String tLoc ) throws MalformedURLException{

        TopicImpl cedentTopic = null;
        String cedentType = cedentID.substring(0, 3);
        if(cedentType.equals("BBA")){
            cedentTopic = storage.getTopic("bas"+cedentName);
            //System.out.println("Cedent basic name: "+ cedentName);
            generator.createAssocAB(aLoc, assocRule, lAssociationRule, cedentTopic, tLoc);
            //generator.createOccurence(cedentTopic, lID, cedentID);
        }
        else if(cedentType.equals("DBA")){
            Element atribute = (Element) aModel.selectSingleNode("./AssociationRules/DBA[@id='"+cedentID+"']");
            String prefix = getRefType(atribute);
            cedentTopic = storage.getTopic(prefix+"das"+cedentName);
            //System.out.println("Cedent derived name: "+prefix+cedentName);
            generator.createAssocAB(aLoc, assocRule, lAssociationRule, cedentTopic, tLoc);
            //generator.createOccurence(cedentTopic, lID, cedentID);
        }
        else{
        System.out.println("Chybny Cedent");
        }
    }

    private void directConnective(String type, TopicImpl derivedTopic, String derivedName, String prefix) throws MalformedURLException {
        System.out.println("< -------- Connective Start");
        String lValue;
        if(type.equals("Negation")){
            lValue = lUnaryOperation;
            }
        else{
            lValue = lConnective;
            }
        //System.out.println("DeOc: "+type);

        if(storage.isAssoc("conn"+type+prefix+derivedName) == false){
            TopicImpl condTopic = generator.createInstance(lValue, type, "con");
            generator.createAssocAB(aHaveBoolAttrType, derivedTopic, lDerivedBooleanAtribute, condTopic, lBARoleType);
            //System.out.println("Stored - vytvorena nova assoc");
            storage.insertAssoc("conn"+type+prefix+derivedName);
        }
        else{
            //System.out.println("Assoc uz jeeee");
        }
        System.out.println("< -------- Connective End");
    }

    private void directRef() throws MalformedURLException{
        List dba = aModel.selectNodes("./AssociationRules/DBA");
        Iterator it = dba.iterator();
        Integer pocet = 0;
        Integer dbabla = 0;
        Integer bbabla = 0;
        while(it.hasNext()){
            pocet = pocet+1;
            System.out.println("Processing reference: " + pocet +" / " + dba.size());
            Element derived = (Element) it.next();
            String derivedName = derived.selectSingleNode("Text").getStringValue();
            String prefix = getRefType(derived);
            TopicImpl derivedTopic = storage.getTopic(prefix+"das"+derivedName);
            List ref = derived.selectNodes("BARef");

            Iterator it2 = ref.iterator();
            Integer bla = 0;
            
            while(it2.hasNext()){
                bla = bla + 1;
                //System.out.println(bla);
                Element el = (Element) it2.next();
                String id = el.getStringValue();
                    if(aModel.selectSingleNode("./AssociationRules/BBA[@id='"+id+"']") != null){
                        bbabla = bbabla + 1;
                        //System.out.println("bbabla: "+ bbabla);
                        Element ela = (Element) aModel.selectSingleNode("./AssociationRules/BBA[@id='"+id+"']");
                        
                        String bbaName = ela.selectSingleNode("Text").getStringValue();
                        if(storage.isAssoc("bba"+bbaName+derivedName) == false){
                            TopicImpl basTopic = storage.getTopic("bas"+bbaName);
                            generator.createAssocAB(aBeDerivedFrom, derivedTopic, lDerivedBooleanAtribute, basTopic, lBooleanAttribute);
                            //System.out.println("Stored - vytvorena nova assoc");
                            storage.insertAssoc("bba"+bbaName+derivedName);
                        }
                        else{
                            //System.out.println("Assoc uz jeeee");
                        }
                    }
                    else if(aModel.selectSingleNode("./AssociationRules/DBA[@id='"+id+"']") != null ){
                        dbabla = dbabla + 1;
                        //System.out.println("DBABLA: "+dbabla);
                        //System.out.println("ID: "+id);
                        Element ela = (Element) aModel.selectSingleNode("./AssociationRules/DBA[@id='"+id+"']");
                        String prefixD = getRefType(ela);
                        String dbaName = ela.selectSingleNode("Text").getStringValue();
                        if(storage.isAssoc("dba"+dbaName+prefixD+derivedName) == false){
                            TopicImpl derTopic = storage.getTopic(prefixD+"das"+dbaName);
                            generator.createAssocAB(aBeDerivedFrom, derivedTopic, lDerivedBooleanAtribute, derTopic, lBooleanAttribute);
                            storage.insertAssoc("dba"+dbaName+prefixD+derivedName);
                        }
                        else{
                            //System.out.println("Assoc uz jeeee");
                        }
                    }
                    else{
                        System.out.println("Chyba: basic - derived"+ derivedName + " -- " + id);
                    }

                }
            }
    }
    
    private void directCoefficient(String type, TopicImpl basTopic, String id) throws MalformedURLException {
       Element item;
       if(type.equals("basic")){
       item = (Element) aModel.selectSingleNode("./AssociationRules/BBA[@id='"+id+"']");
       }
       else{
       item = (Element) aModel.selectSingleNode("./AssociationRules/DBA[@id='"+id+"']");
       }
           String itID = item.attributeValue("id");
           if(itID.equals(id)){
                List catRef = item.selectNodes("CatRef");
                Iterator it2 = catRef.iterator();
                String bName = item.selectSingleNode("Text").getStringValue();
                while(it2.hasNext()){
                Element ext = (Element) it2.next();

                        String extName = ext.getStringValue();

                        TopicImpl top = storage.getTopic("discBin"+extName);
                        if(top != null){
                            if(storage.isAssoc("coef"+extName+bName) == false){
                                generator.createAssocAB(aHaveCoeficient, basTopic, lBasicBooleanAtribute, top, lCoefficient);
                                //System.out.println("COEF Stored - vytvorena nova assoc");
                                storage.insertAssoc("coef"+extName+bName);
                            }
                            else{
                                //System.out.println("Assoc uz jeeee");
                            }
                        }
                    
                        TopicImpl vtop = storage.getTopic("vmBin"+extName);
                        if(vtop != null){
                        if(storage.isAssoc("coef"+extName+bName) == false){
                                generator.createAssocAB(aHaveCoeficient, basTopic, lBasicBooleanAtribute, vtop, lCoefficient);
                                //System.out.println("COEF Stored - vytvorena nova assoc");
                                storage.insertAssoc("coef"+extName+bName);
                            }
                            else{
                                //System.out.println("Assoc uz jeeee");
                            }
                        }

                        TopicImpl value = storage.getTopic("Value"+extName);
                        if(value != null){
                        if(storage.isAssoc("coef"+extName+bName) == false){
                                generator.createAssocAB(aHaveCoeficient, basTopic, lBasicBooleanAtribute, value, lCoefficient);
                                //System.out.println("COEF Stored - vytvorena nova assoc");
                                storage.insertAssoc("coef"+extName+bName);
                            }
                            else{
                                //System.out.println("Assoc uz jeeee");
                            }
                        }
                }
          }
    }

    private void directInterestMeasures(TopicImpl assocRule, String meno, Element polozka) throws MalformedURLException {
        List extensions = polozka.selectNodes("./IMValue");
        Iterator it = extensions.iterator();
        while(it.hasNext()){
                Element imv = (Element) it.next();
                String val = imv.getStringValue();
                String extender = imv.attributeValue("name");
                createIMV(assocRule, extender, extender, meno, val);
        }
    }

    private void createIMV(TopicImpl assocRule, String suffix, String fttype, String name, String occurence) throws MalformedURLException{
        TopicImpl interestMV = generator.createInstance(lInterestMeasureValue,suffix + ": " + name);
        generator.createAssocAB(aHaveIntereseMeasureValue, assocRule, lAssociationRule, interestMV, lInterestMeasureValue);
        generator.createOccurence(interestMV, lValueLocator, occurence);
        TopicImpl FTQuantifier = generator.createInstance(l4ftquantifier, fttype);
        generator.createAssocAB(aHaveIMType, FTQuantifier, lInterestMeasure, interestMV, lInterestMeasureValue);
        //createOrder(occurence, interestMV);
        zoznamValues.put(occurence, interestMV);
    }

    private void createOrder(String novaVal, TopicImpl nova){
        SortedSet sortedset= (SortedSet) zoznamValues.keySet();
        Iterator si = sortedset.iterator();
        while (si.hasNext()){
            String storedVal = (String) si.next();
            float novaValInt = Float.valueOf(novaVal).floatValue();
            float storedValInt = Float.valueOf(storedVal).floatValue();
            TopicImpl stored = zoznamValues.get( storedVal );
            if(novaValInt > storedValInt){
                generator.createAssocAB(aBeGreatherThan, nova, lGreaterValue, stored, lLowerValue);
            }
            if(novaValInt < storedValInt){
                generator.createAssocAB(aBeGreatherThan, nova, lLowerValue, stored,lGreaterValue);
            }
        }
    }

    private String getBAType(String id){
        List bbas = aModel.selectNodes("./TaskSetting/BBASettings/BBASetting");
        List dbas = aModel.selectNodes("./TaskSetting/DBASettings/DBASetting");
        String ret = null;

        Iterator it = bbas.iterator();

        while(it.hasNext()){
            Element bba = (Element) it.next();
            if(bba.attributeValue("id").equals(id)){
                ret = "B";
            }
        }

        Iterator it2 = dbas.iterator();
        while(it2.hasNext()){
            Element dba = (Element) it2.next();
            if(dba.attributeValue("id").equals(id)){
                ret = "D";
            }
        }
        return ret;
    }

    private String getBooleanAttributeName(String id) {
        List bbas = aModel.selectNodes("./TaskSetting/BBASettings/BBASetting");
        List dbas = aModel.selectNodes("./TaskSetting/DBASettings/DBASetting");
        String ret = null;

        Iterator it = bbas.iterator();

        while(it.hasNext()){
            Element bba = (Element) it.next();
            if(bba.attributeValue("id").equals(id)){
                ret = bba.selectSingleNode("./Name").getStringValue();
            }
        }

        Iterator it2 = dbas.iterator();
        while(it2.hasNext()){
            Element dba = (Element) it2.next();
            if(dba.attributeValue("id").equals(id)){
                ret = dba.selectSingleNode("./Name").getStringValue();
            }
        }
        return ret;
    }

    private TopicImpl getBooleanAttributeInstance(String id, String type){
        List bbas = aModel.selectNodes("./TaskSetting/BBASettings/BBASetting");
        List dbas = aModel.selectNodes("./TaskSetting/DBASettings/DBASetting");
        TopicImpl ret = null;


        if(type.equals("B")){
            Iterator it = bbas.iterator();
        while(it.hasNext()){
            Element bba = (Element) it.next();
            if(bba.attributeValue("id").equals(id)){
                ret = storage.getTopic("BBA"+bba.selectSingleNode("./Name").getStringValue() );
                //System.out.println("BBA: "+bba.selectSingleNode("./Name").getStringValue());
            }
        }
        }

        else if(type.equals("D")){
        Iterator it2 = dbas.iterator();
        while(it2.hasNext()){
            Element dba = (Element) it2.next();
            if(dba.attributeValue("id").equals(id)){
                ret = storage.getTopic("DBA"+dba.selectSingleNode("./Name").getStringValue() );
                //System.out.println("DBA: "+dba.selectSingleNode("./Name").getStringValue());
                //System.out.println("DBAT: "+ret);
            }
        }
        }
        return ret;
    }

    private void directInterestMeasureSetting(Element aModel) throws MalformedURLException {
        
        TopicImpl miningSetting = generator.getTopicTypeInstance(lMiningAlgorithmSetting);
        Iterator extensions = aModel.selectNodes("./TaskSetting/InterestMeasureSetting/InterestMeasureThreshold").iterator();
        TopicImpl kvantTopic;
        while(extensions.hasNext()){
            Element ext = (Element) extensions.next();

            if(ext.selectSingleNode("./InterestMeasure") == null){
            Element formula = (Element) ext.selectSingleNode("./Formula");
            String name = formula.attributeValue("name");
            name = name + "(" + formula.getStringValue() + ")";

            kvantTopic = generator.createInstance(lInterestMeasureSetting,name,"IMS");
            generator.createOccurence(kvantTopic, lMinimumValue, ext.selectSingleNode("./Threshold").getStringValue());

            }
            else{
            
            kvantTopic = generator.createInstance(lInterestMeasureSetting,ext.selectSingleNode("./InterestMeasure").getStringValue(),"IMS");
            generator.createOccurence(kvantTopic, lMinimumValue, ext.selectSingleNode("./Threshold").getStringValue());
            }

            generator.createAssocAB(aHaveIMS,kvantTopic, lInterestMeasureSetting, miningSetting, lMiningAlgorithmSetting);
        }
    }
}
