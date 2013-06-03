package izi_repository.domain;

import java.util.Map;

/**
 * Domain object representing AssociationRule.
 * 
 * @author Tomas Marek
 * 
 */
public class AssociationRule {

	private String id;
	private String documentId;
	private String antecedentDbaId;
	private String consequentDbaId;
	private String conditionDbaId;
	private Map<String, String> imValues;
	private Map<String, Integer> fourFtTable;

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
	 * @return the documentId
	 */
	public String getDocumentId() {
		return documentId;
	}

	/**
	 * @param documentId
	 *            the documentId to set
	 */
	public void setDocumentId(String documentId) {
		this.documentId = documentId;
	}

	/**
	 * @return the antecedentDbaId
	 */
	public String getAntecedentDbaId() {
		return antecedentDbaId;
	}

	/**
	 * @param antecedentDbaId
	 *            the antecedentDbaId to set
	 */
	public void setAntecedentDbaId(String antecedentDbaId) {
		this.antecedentDbaId = antecedentDbaId;
	}

	/**
	 * @return the consequentDbaId
	 */
	public String getConsequentDbaId() {
		return consequentDbaId;
	}

	/**
	 * @param consequentDbaId
	 *            the consequentDbaId to set
	 */
	public void setConsequentDbaId(String consequentDbaId) {
		this.consequentDbaId = consequentDbaId;
	}

	/**
	 * @return the conditionDbaId
	 */
	public String getConditionDbaId() {
		return conditionDbaId;
	}

	/**
	 * @param conditionDbaId
	 *            the conditionDbaId to set
	 */
	public void setConditionDbaId(String conditionDbaId) {
		this.conditionDbaId = conditionDbaId;
	}

	/**
	 * @return the imValues
	 */
	public Map<String, String> getImValues() {
		return imValues;
	}

	/**
	 * @param imValues
	 *            the imValues to set
	 */
	public void setImValues(Map<String, String> imValues) {
		this.imValues = imValues;
	}

	/**
	 * @return the fourFtTable
	 */
	public Map<String, Integer> getFourFtTable() {
		return fourFtTable;
	}

	/**
	 * @param fourFtTable
	 *            the fourFtTable to set
	 */
	public void setFourFtTable(Map<String, Integer> fourFtTable) {
		this.fourFtTable = fourFtTable;
	}

}
