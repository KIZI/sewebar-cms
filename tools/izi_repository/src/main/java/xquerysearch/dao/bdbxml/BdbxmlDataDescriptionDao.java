package xquerysearch.dao.bdbxml;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.dao.DataDescriptionDao;
import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlTransaction;

/**
 * Implementation of {@link DataDescriptionDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlDataDescriptionDao extends AbstractDao implements DataDescriptionDao {
	
	private final String DATA_DESCRIPTION_CONTAINER = "__DataDescriptionCacheContainer";
	private final String DATA_DESCRIPTION_DOCUMENT = "__DataDescriptionCacheDocument";
	
	private String containerName;
	
	@Autowired
	private ResultsDao resultsDao;
	
	/*
	 * @{InheritDoc}
	 */
	public String getDataDescriptionFromData() {
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
    
	List<String> results = resultsDao.getResultsByQuery(new Query(query)); 
	
	 if (results != null) {
        StringBuffer resultsToPrint = new StringBuffer();
     	for (String result : results) {
     		resultsToPrint.append(result);
     	}
     	return resultsToPrint.toString();
     } else {
         return "<error>Data description retrieving failed</error>";
     }
	}

	/*
	 * @{InheritDoc}
	 */
	public String getDataDescriptionFromCache() {
		XmlContainer cont = null;
        XmlTransaction trans = null;
        try {
        	cont = xmlManager.openContainer(DATA_DESCRIPTION_CONTAINER);
        	trans = xmlManager.createTransaction();
    		XmlDocument doc = cont.getDocument(DATA_DESCRIPTION_DOCUMENT);
    		return doc.getContentAsString();
		} catch (XmlException e) {
			logger.logWarning(this.getClass().toString(), "Getting data description failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/*
	 * @{InheritDoc}
	 */
	public boolean saveDataDescriptionIntoCache(String dataDescription) {
		XmlContainer cont = null;
        XmlTransaction trans = null;
        try {
        	cont = xmlManager.openContainer(DATA_DESCRIPTION_CONTAINER);
        	trans = xmlManager.createTransaction();
			cont.putDocument(DATA_DESCRIPTION_DOCUMENT, dataDescription);
			return true;
		} catch (XmlException e) {
			logger.logWarning(this.getClass().toString(), "Saving data description failed!");
			return false;
		} finally {
			commitAndClose(trans, cont);
		}
	}

}
