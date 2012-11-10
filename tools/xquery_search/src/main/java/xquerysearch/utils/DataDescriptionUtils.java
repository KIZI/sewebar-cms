package xquerysearch.utils;

import java.util.List;

import xquerysearch.domain.arbquery.datadescription.DataDescription;
import xquerysearch.domain.arbquery.datadescription.Dictionary;
import xquerysearch.domain.arbquery.datadescription.Field;
import xquerysearch.domain.result.datadescription.DataField;
import xquerysearch.domain.result.datadescription.ResultDataDescription;

/**
 * Utility methods for {@link DataDescription} and {@link ResultDataDescription}
 * .
 * 
 * @author Tomas Marek
 * 
 */
public class DataDescriptionUtils {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private DataDescriptionUtils() {
	}

	/**
	 * Retrieve count of category from {@link DataField} specified by name.
	 * 
	 * @param dataDescription
	 * @param fieldName
	 * @return
	 */
	public static int getCategoryCountByFieldName(ResultDataDescription dataDescription, String fieldName) {
		if (dataDescription != null && fieldName != null) {
			List<DataField> dataFields = dataDescription.getDataFields();
			if (dataFields != null) {
				for (DataField dataField : dataFields) {
					if (fieldName.equals(dataField.getName())) {
						return dataField.getCategories().size();
					}
				}
			}
		}
		return 0;
	}

	/**
	 * Retrieve count of category from {@link Field} specified by name.
	 * 
	 * @param dataDescription
	 * @param fieldName
	 * @return
	 */
	public static int getCategoryCountByFieldName(DataDescription dataDescription, String fieldName) {
		// TODO rework
		if (dataDescription != null && fieldName != null) {
			for (Dictionary dictionary : dataDescription.getDictionaries()) {
				if (dictionary.getSourceDictType().equals("TransformationDictionary")) {
					for (Field field : dictionary.getFields()) {
						if (field.getName().equals(fieldName)) {
							return field.getCategories().size();
						}
					}
				}
			}
		}
		return 0;
	}
}
