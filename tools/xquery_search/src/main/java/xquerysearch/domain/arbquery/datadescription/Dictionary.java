package xquerysearch.domain.arbquery.datadescription;

import java.util.List;

/**
 * @author Tomas
 * 
 */
public class Dictionary {

	private String id;
	private String completeness;
	private String sourceFormat;
	private String sourceDictType;
	private String sourceName;
	private boolean isDefault;
	private List<Identifier> identifiers;
	private List<Field> fields;

	/**
	 * @return the id
	 */
	public String getId() {
		return id;
	}

	/**
	 * @param id
	 *            the id to set
	 */
	public void setId(String id) {
		this.id = id;
	}

	/**
	 * @return the completeness
	 */
	public String getCompleteness() {
		return completeness;
	}

	/**
	 * @param completeness
	 *            the completeness to set
	 */
	public void setCompleteness(String completeness) {
		this.completeness = completeness;
	}

	/**
	 * @return the sourceFormat
	 */
	public String getSourceFormat() {
		return sourceFormat;
	}

	/**
	 * @param sourceFormat
	 *            the sourceFormat to set
	 */
	public void setSourceFormat(String sourceFormat) {
		this.sourceFormat = sourceFormat;
	}

	/**
	 * @return the sourceDictType
	 */
	public String getSourceDictType() {
		return sourceDictType;
	}

	/**
	 * @param sourceDictType
	 *            the sourceDictType to set
	 */
	public void setSourceDictType(String sourceDictType) {
		this.sourceDictType = sourceDictType;
	}

	/**
	 * @return the sourceName
	 */
	public String getSourceName() {
		return sourceName;
	}

	/**
	 * @param sourceName
	 *            the sourceName to set
	 */
	public void setSourceName(String sourceName) {
		this.sourceName = sourceName;
	}

	/**
	 * @return the isDefault
	 */
	public boolean isDefault() {
		return isDefault;
	}

	/**
	 * @param isDefault
	 *            the isDefault to set
	 */
	public void setDefault(boolean isDefault) {
		this.isDefault = isDefault;
	}

	/**
	 * @return the identifiers
	 */
	public List<Identifier> getIdentifiers() {
		return identifiers;
	}

	/**
	 * @param identifiers
	 *            the identifiers to set
	 */
	public void setIdentifiers(List<Identifier> identifiers) {
		this.identifiers = identifiers;
	}

	/**
	 * @return the fields
	 */
	public List<Field> getFields() {
		return fields;
	}

	/**
	 * @param fields
	 *            the fields to set
	 */
	public void setFields(List<Field> fields) {
		this.fields = fields;
	}

}
