package xquerysearch.domain.output;

import xquerysearch.utils.OutputUtils;

/**
 * Domain object representing Hit element in output.
 * 
 * @author Tomas Marek
 * 
 */
public class Hit {

	private String ruleId;
	private String docId;
	private String docName;
	private String database;
	private String reportUri;
	private double[][] queryCompliance;
	private AssociationRule associationRule;

	/**
	 * @return the ruleId
	 */
	public String getRuleId() {
		return ruleId;
	}

	/**
	 * @param ruleId
	 *            the ruleId to set
	 */
	public void setRuleId(String ruleId) {
		this.ruleId = ruleId;
	}

	/**
	 * @return the docId
	 */
	public String getDocId() {
		return docId;
	}

	/**
	 * @param docId
	 *            the docId to set
	 */
	public void setDocId(String docId) {
		this.docId = docId;
	}

	/**
	 * @return the docName
	 */
	public String getDocName() {
		return docName;
	}

	/**
	 * @param docName
	 *            the docName to set
	 */
	public void setDocName(String docName) {
		this.docName = docName;
	}

	/**
	 * @return the database
	 */
	public String getDatabase() {
		return database;
	}

	/**
	 * @param database
	 *            the database to set
	 */
	public void setDatabase(String database) {
		this.database = database;
	}

	/**
	 * @return the reportUri
	 */
	public String getReportUri() {
		return reportUri;
	}

	/**
	 * @param reportUri
	 *            the reportUri to set
	 */
	public void setReportUri(String reportUri) {
		this.reportUri = reportUri;
	}

	/**
	 * @return the associationRule
	 */
	public AssociationRule getAssociationRule() {
		return associationRule;
	}

	/**
	 * @param associationRule
	 *            the associationRule to set
	 */
	public void setAssociationRule(AssociationRule associationRule) {
		this.associationRule = associationRule;
	}

	/**
	 * @return the queryCompliance
	 */
	public double[][] getQueryCompliance() {
		return queryCompliance;
	}

	/**
	 * @param queryCompliance
	 *            the queryCompliance to set
	 */
	public void setQueryCompliance(double[][] queryCompliance) {
		this.queryCompliance = queryCompliance;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		String ret = "<Hit ruleId=\"" + ruleId + "\" docId=\"" + docId + "\" docName=\"" + docName
				+ "\" database=\"" + database + "\" reportURI=\"" + reportUri + "\" queryCompliance=\""
				+ OutputUtils.getQueryComplianceForOutput(queryCompliance) + "\">";

		if (associationRule != null) {
			ret += associationRule.toString();
		}

		ret += "</Hit>";

		return ret;
	}

}
