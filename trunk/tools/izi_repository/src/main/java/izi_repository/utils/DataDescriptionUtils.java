package izi_repository.utils;

import izi_repository.domain.arbquery.datadescription.DataDescription;
import izi_repository.domain.arbquery.datadescription.Dictionary;
import izi_repository.domain.arbquery.datadescription.Field;
import izi_repository.domain.result.datadescription.DataField;
import izi_repository.domain.result.datadescription.ResultDataDescription;

import java.util.ArrayList;
import java.util.List;


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
		// TODO rework
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
		if (dataDescription != null && fieldName != null) {
			List<String> categories = getCategories(dataDescription, fieldName);
			return categories.size();
		}
		return 0;
	}

	public static List<String> getCategories(DataDescription dataDescription, String fieldName) {
		List<String> ret = new ArrayList<String>();
		if (dataDescription != null && dataDescription.getDictionaries() != null) {
			for (Dictionary dictionary : dataDescription.getDictionaries()) {
				if (dictionary.getSourceDictType().equals("TransformationDictionary")) {
					ret.addAll(getCategories(dictionary, fieldName));
				}
			}
		}
		return ret;
	}

	public static List<String> getCategories(Dictionary dictionary, String fieldName) {
		List<String> ret = new ArrayList<String>();
		if (dictionary != null && dictionary.getFields() != null) {
			ret.addAll(getCategories(dictionary.getFields(), fieldName));
		}
		return ret;
	}

	public static List<String> getCategories(List<Field> fields, String fieldName) {
		List<String> ret = new ArrayList<String>();
		if (fields != null) {
			for (Field field : fields) {
				if (fieldName != null) {
					if (field.getName().equals(fieldName)) {
						ret.addAll(field.getCategories());
					}
				} else {
					ret.addAll(field.getCategories());
				}
			}
		}
		return ret;
	}

}
