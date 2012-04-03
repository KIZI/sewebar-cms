package xquerysearch.datadescription;

import xquerysearch.db.DbConnectionManager;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlValue;

/**
 * This class is responsible for handling all operations concerning data description.
 * 
 * @author Tomas Marek
 *
 */
public class DataDescriptionHandler {
	
	private String containerName;
	private DbConnectionManager dcm;
	
	/**
	 * 
	 */
	public DataDescriptionHandler(SettingsManager settingsManager) {
		dcm = new DbConnectionManager(settingsManager);
		containerName = settingsManager.getContainerName();
	}
	
	/**
	 * Creates data description from data stored in DB
     * @return DataDescription
     */
    public String getDataDescription() {

        String query = "let $distinctNamesCategorical :="
                    + "\nfor $dataField in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@type != \"continuous\"]/@name/string())"
                    + "\nreturn $dataField"
                + "\n"
                + "\nlet $distinctNamesContinuous :="
                    + "\nfor $dataField in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@type = \"continuous\"]/@name/string())"
                    + "\nreturn $dataField"
                + "\n"
                + "\nlet $transDict := "
                    + "\nfor $name in ($distinctNamesCategorical, $distinctNamesContinuous)"
                    + "\nreturn"
                    + "\n<Field name=\"{$name}\">{"
                    + "\nlet $cats :="
                    + "\nfor $cat in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category/text())"
                    + "\nreturn <Category>{$cat}</Category>"
                    + "\nreturn $cats"
                    + "\n}</Field>"
                    + "\n"
                + "\nlet $dataDict := "
                    + "\nfor $name in $distinctNamesContinuous"
                    + "\nreturn"
                    + "\n<Field name=\"{$name}\">{"
                    + "\nfor $catText in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category/text())"
                    + "\nfor $lm in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[text() = $catText]/@leftMargin)"
                    + "\nlet $rm := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@rightMargin)"
                    + "\nlet $clos := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@closure)"
                    + "\nreturn <Interval leftMargin=\"{$lm}\" rightMargin=\"{$rm}\" closure=\"{$clos}\"/>"
                    + "\n}</Field>"
                + "\n"
                + "\nlet $valueMapping := "
                    + "\nfor $name in $distinctNamesContinuous"
                    + "\nreturn"
                    + "\n"
                    + "\nfor $catText in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category/text())"
                    + "\nfor $lm in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[text() = $catText]/@leftMargin)"
                    + "\nlet $rm := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@rightMargin)"
                    + "\nlet $clos := distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $name]/Category[@leftMargin = $lm]/@closure)"
                    + "\nreturn "
                    + "\n<ValueMapping>"
                    + "\n<Field dictionary=\"DataDictionary\" name=\"{$name}\">"
                    + "\n<Interval leftMargin=\"{$lm}\" rightMargin=\"{$rm}\" closure=\"{$clos}\"/>"
                    + "\n</Field>"
                    + "\n<Field dictionary=\"TransformationDictionary\" name=\"{$name}\">"
                    + "\n<Value>{$catText}</Value>"
                    + "\n</Field>"
                    + "\n</ValueMapping>"
                + "\nreturn"
                + "\n<data:DataDescription xmlns:data=\"http://keg.vse.cz/ns/datadescription0_1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://keg.vse.cz/ns/datadescription0_1 http://sewebar.vse.cz/schemas/DataDescription0_1.xsd\">"
                    + "\n<Dictionary sourceSubType=\"TransformationDictionary\" sourceType = \"PMML\" default=\"true\">"
                    + "\n{$transDict}"
                    + "\n</Dictionary>"
                    + "\n<Dictionary sourceSubType=\"DataDictionary\" sourceType = \"PMML\">"
                    + "\n{$dataDict}"
                    + "\n</Dictionary>"
                    + "\n<DictionaryMapping>"
                    + "\n{$valueMapping}"
                    + "\n</DictionaryMapping>"
                + "\n</data:DataDescription>";

        /*String query = "<dd:DataDescription xmlns:dd=\"http://keg.vse.cz/ns/datadescription0_1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:pmml=\"http://www.dmg.org/PMML-4_0\" xsi:schemaLocation=\"http://keg.vse.cz/ns/datadescription0_1 http://sewebar.vse.cz/schemas/DataDescription0_1.xsd\">"
        	+ "<Dictionary sourceSubType=\"DataDictionary\" sourceType = \"PMML\" default=\"true\">{"
            + "\nfor $field in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField/@name/string()) "
    		+ "\nlet $values :=  for $value in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type != \"continuous\"]/Category/text())"
			+ "\nreturn <Category>{$value}</Category>"
			+ "\nlet $leftMargin := for $LM in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type = \"continuous\"]/Interval/@leftMargin)"
			+ "\nreturn min($LM)"
			+ "\nlet $rightMargin := for $RM in distinct-values(collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type = \"continuous\"]/Interval/@rightMargin)"
			+ "\nreturn max($RM)"
			+ "\nlet $int := if (count($leftMargin) > 0 and count($leftMargin) > 0) then <Interval leftMargin = \"{$leftMargin}\" rightMargin = \"{$rightMargin}\" closure = \"closedClosed\"/> else ()"
			+ "\nlet $intCats := distinct-values(for $DF in collection(\"" + containerName + "\")/PMML/DataDescription/DataField[@name = $field and @type = \"continuous\"]"
			+ "\nwhere max(count($DF/Interval/Category))"
			+ "\nreturn $DF/Interval/Category)"
            + "\nreturn"
            + "\n<Field name=\"{$field}\">"
            + "\n{$values union $int}"
            + "\n{for $IC in $intCats return <Category>{$IC}</Category>}"
            + "\n</Field>}</Dictionary></dd:DataDescription>";
        */
        /*String query =
                "<DataDescription><Dictionary sourceSubType=\"DataDictionary\" sourceType = \"PMML\" default=\"true\">{"
                + "\nfor $field in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field/@name/string())"
                + "\nlet $values :=  for $value in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field[@name = $field and @type != \"continuous\"]/fieldValue/text())"
                + "\nreturn <Category>{$value}</Category>"
                + "\nlet $ints_from := for $int in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field[@name = $field and @type = \"continuous\"]/fieldValue[1]/@from)"
                + "\nreturn $int"
                + "\nlet $ints_to := for $int in distinct-values(collection(\"" + containerName + "\")/PMML/fieldValuesSet/Field[@name = $field and @type = \"continuous\"]/fieldValue[last()]/@to)"
                + "\nreturn $int"
                + "\nlet $ints := if(count($ints_from) > 0 and count($ints_to) > 0) then <Interval closure=\"\" leftMargin=\"{$ints_from}\" rightMargin=\"{$ints_to}\"/> else ()"
                + "\nreturn"
                + "\n<Field name=\"{$field}\">"
                + "\n{$values union $ints}"
                + "\n</Field>}</Dictionary></DataDescription>";*/
        
    	XmlResults results = dcm.query(query);
    	
    	 if (results != null) {
            String result = "";
         	try {    
             	XmlValue value = new XmlValue();
                 while ((value = results.next()) != null) {
                     result += (value.asString());
                 }
                 return result;
             } catch (XmlException e) {
             	return "<error>Querying database failed! - XML exception</error>";
             }
         } else {
             return "<error>Data description retrieving failed</error>";
         }
    }
    
    /**
     * Method for retrieve saved data description from repository
     * @return DataDescription / chyba
     */
    public String getDataDescriptionCache() {
        String dataDescription = dcm.getDataDescription();
        if (dataDescription != null) {
        	return dataDescription;
        } else {
        	return "<error>Getting data description failed!</error>";
        }
    }
    
    /**
     * Saves data description into repository - caching
     * @return message - success/failure
     */
    public String actualizeDataDescriptionCache() {
       boolean saved = dcm.saveDataDescription(getDataDescription());
       if (saved) {
    	   return "<message>Data description successfully saved!</message>";
       } else {
    	   return "<error>Error occured during data description save!</error>";
       }
    }

}
