package izi_repository.domain.result.datadescription;

import java.util.List;

/**
 * Domain object representing data description of document from database.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultDataDescription {

	private int fieldCount;
	private List<DataField> dataFields;

	/**
	 * @return the fieldCount
	 */
	public int getFieldCount() {
		return fieldCount;
	}

	/**
	 * @param fieldCount
	 *            the fieldCount to set
	 */
	public void setFieldCount(int fieldCount) {
		this.fieldCount = fieldCount;
	}

	/**
	 * @return the dataFields
	 */
	public List<DataField> getDataFields() {
		return dataFields;
	}

	/**
	 * @param dataFields
	 *            the dataFields to set
	 */
	public void setDataFields(List<DataField> dataFields) {
		this.dataFields = dataFields;
	}

}
