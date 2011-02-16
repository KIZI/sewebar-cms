/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package PMML2XTM;

import java.io.IOException;
import java.util.Iterator;
import java.util.List;
import net.ontopia.topicmaps.impl.tmapi2.TopicImpl;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapImpl;
import org.dom4j.Element;

/**
 *
 * @author marek
 */
public class DataMiningTaskDirector {

    private String aBeCreatedBy = "http://keg.vse.cz/dmo/becreatedby";
    private String aBeProducedBy = "http://keg.vse.cz/dmo/producedby";
    private String aDescribes = "http://keg.vse.cz/dmo/describes";
    private String aHaveTimeStamp = "http://keg.vse.cz/dmo/havetimestamp";
    private String aHaveAnnotation = "http://keg.vse.cz/dmo/haveAnnotation";

    private String lDataMiningTask = "http://keg.vse.cz/dmo/dataminingtask";
    private String lAnalyst = "http://keg.vse.cz/dmo/analyst";
    private String lApplication = "http://www.dmg.org/PMML-4_0#application";
    private String lVersion = "http://purl.org/dc/elements/1.1/version";
    private String lDataDictionary = "http://www.dmg.org/PMML-4_0#DataDictionary";
    private String lTaskPart = "http://keg.vse.cz/dmo/taskpart";
    private String lTransformationDictionary = "http://www.dmg.org/PMML-4_0#TransformationDictionary";
    private String lMiningSchema = "http://www.dmg.org/PMML-4_0#MiningSchema";
    private String lTimeStamp = "http://www.dmg.org/PMML-4_0#Timestamp";
    private String lAnnotation = "http://www.dmg.org/PMML-4_0#Annotation";
    private String lAssocRuleRole  = "http://psi.keg.vse.cz/role/AssociationRule";
    private String lMiningAlgorithmSetting = "http://keg.vse.cz/dmo/miningalgorithmsetting";
    private String lNumberOfFields = "http://www.dmg.org/PMML-4_0#numberOfFields";
    private String lAssociationModel = "http://www.dmg.org/PMML-4_0#AssociationModel";

    private final Storage storage;
    private TMHandler generator;
    private final TopicMapImpl topicmap;



    DataMiningTaskDirector(Storage storage, TopicMapImpl map) {
        this.storage = storage;
        this.topicmap = map;

    }

    void direct(Element head, Element dd, Element aModel) throws IOException{

        generator = new TMHandler(storage, topicmap);

        directHeader(head, dd, aModel);

    }

    private void directHeader(Element header, Element dd, Element aModel){
      
       String title = aModel.attributeValue("modelName") ;
       System.out.println("title: "+title);
       TopicImpl dmTask = generator.createInstance(lDataMiningTask, "DataMiningTask - " + title);

       TopicImpl dataDictionary = generator.createInstance(lDataDictionary, "DataDictionary - "+title);

       // Vytvorenie Occurence Number of Fields

       String nof = dd.attributeValue("numberOfFields");
       generator.createOccurence(dataDictionary, lNumberOfFields, nof);
        
       generator.createAssocAB(aDescribes, dmTask, lDataMiningTask, dataDictionary, lTaskPart);

       TopicImpl transformationDictionary = generator.createInstance(lTransformationDictionary, "TransformationDictionary - "+title);

       generator.createAssocAB(aDescribes, dmTask, lDataMiningTask, transformationDictionary, lTaskPart);

       TopicImpl miningAlgorithmSetting = generator.createInstance(lMiningAlgorithmSetting, "MiningAlgorithmSetting - "+title);

       generator.createAssocAB(aDescribes, dmTask, lDataMiningTask, miningAlgorithmSetting, lTaskPart);

       Element application = (Element) header.selectSingleNode("./pmml:Application");
       String appName = application.attributeValue("name");
       String appVersion = application.attributeValue("version");
       System.out.println("APPNAME: "+appName +" / "+ appVersion);
       TopicImpl appNameTopic = generator.createInstance(lApplication, appName);

       generator.createAssocAB(aBeProducedBy, dmTask, lDataMiningTask, appNameTopic, lApplication);

       TopicImpl assocModelTopic = generator.createInstance(lAssociationModel, "AssociationModel - "+title);
       System.out.println("AMODEL: "+ assocModelTopic);
       generator.createAssocAB(aDescribes, dmTask, lDataMiningTask, assocModelTopic, lTaskPart);

       generator.createOccurence(appNameTopic, lVersion, appVersion);

       List extensionList = header.selectNodes("./pmml:Extension[@name='author']");
       Iterator it = extensionList.iterator();

       while(it.hasNext()){
        Element extension = (Element) it.next();
        String autor = extension.attributeValue("value");
        TopicImpl analyst = generator.createInstance(lAnalyst, autor);
        generator.createAssocAB(aBeCreatedBy, dmTask, lDataMiningTask, analyst, lAnalyst);
       }

       TopicImpl miningSchema = generator.createInstance(lMiningSchema, "MiningSchema - "+title);
       generator.createAssocAB(aDescribes, dmTask, lDataMiningTask, miningSchema, lTaskPart);

       Element timestamp = (Element) header.selectSingleNode("./pmml:Timestamp");
       TopicImpl stampTopic = generator.createInstance(lTimeStamp, timestamp.getText(), "ts");
       generator.createAssocAB(aHaveTimeStamp, dmTask, lDataMiningTask, stampTopic, lTimeStamp);

       Element annotation = (Element) header.selectSingleNode("./pmml:Annotation");
       TopicImpl annotationTopic = generator.createInstance(lAnnotation, annotation.getText());
       generator.createAssocAB(aHaveAnnotation, dmTask, lAssocRuleRole, annotationTopic, lAnnotation);
    }

    private void printError(String string) {
        System.out.println(string);
    }
}
