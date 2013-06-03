package izi_repository.domain.grouping;

import java.util.ArrayList;
import java.util.List;

/**
 * Domain object representing description of {@link Group}.
 * 
 * @author Tomas Marek
 * 
 */
public class GroupDescription {

	private String fieldRefName;
	private List<String> categories = new ArrayList<String>();
	private List<String> fieldRefs = new ArrayList<String>();
	private List<String> antecedentFieldRefs = new ArrayList<String>();
	private List<String> consequentFieldRefs = new ArrayList<String>();
	private List<String> conditionFieldRefs = new ArrayList<String>();
	private Integer ruleLength;
	private Integer antecedentLength;
	private Integer consequentLength;
	private Integer conditionLength;

	/**
	 * @return the categories
	 */
	public List<String> getCategories() {
		return categories;
	}

	/**
	 * @return the fieldRefName
	 */
	public String getFieldRefName() {
		return fieldRefName;
	}

	/**
	 * @return the fieldRefs
	 */
	public List<String> getFieldRefs() {
		return fieldRefs;
	}

	/**
	 * @return the antecedentFieldRefs
	 */
	public List<String> getAntecedentFieldRefs() {
		return antecedentFieldRefs;
	}

	/**
	 * @return the consequentFieldRefs
	 */
	public List<String> getConsequentFieldRefs() {
		return consequentFieldRefs;
	}

	/**
	 * @return the conditionFieldRefs
	 */
	public List<String> getConditionFieldRefs() {
		return conditionFieldRefs;
	}

	/**
	 * @param fieldRefName
	 *            the fieldRefName to set
	 */
	public void setFieldRefName(String fieldRefName) {
		this.fieldRefName = fieldRefName;
	}

	/**
	 * @return the ruleLength
	 */
	public Integer getRuleLength() {
		return ruleLength;
	}

	/**
	 * @param ruleLength
	 *            the ruleLength to set
	 */
	public void setRuleLength(Integer ruleLength) {
		this.ruleLength = ruleLength;
	}

	/**
	 * @return the antecedentLength
	 */
	public Integer getAntecedentLength() {
		return antecedentLength;
	}

	/**
	 * @param antecedentLength
	 *            the antecedentLength to set
	 */
	public void setAntecedentLength(Integer antecedentLength) {
		this.antecedentLength = antecedentLength;
	}

	/**
	 * @return the consequentLength
	 */
	public Integer getConsequentLength() {
		return consequentLength;
	}

	/**
	 * @param consequentLength
	 *            the consequentLength to set
	 */
	public void setConsequentLength(Integer consequentLength) {
		this.consequentLength = consequentLength;
	}

	/**
	 * @return the conditionLength
	 */
	public Integer getConditionLength() {
		return conditionLength;
	}

	/**
	 * @param conditionLength
	 *            the conditionLength to set
	 */
	public void setConditionLength(Integer conditionLength) {
		this.conditionLength = conditionLength;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("[");
		String connective = "";
		if (fieldRefName != null) {
			ret.append(connective);
			ret.append("FieldRef: " + fieldRefName);
			connective = ", ";
		}
		if (categories.size() > 0) {
			ret.append(connective);
			ret.append("Categories: [");
			ret.append(prepareForOutput(categories));
			ret.append("]");
			connective = ", ";
		}
		if (fieldRefs.size() > 0) {
			ret.append(connective);
			ret.append("FieldRefs: [");
			ret.append(prepareForOutput(fieldRefs));
			ret.append("]");
			connective = ", ";
		}
		if (antecedentFieldRefs.size() > 0) {
			ret.append(connective);
			ret.append("AntecedentFieldRefs: [");
			ret.append(prepareForOutput(antecedentFieldRefs));
			ret.append("]");
			connective = ", ";
		}
		if (consequentFieldRefs.size() > 0) {
			ret.append(connective);
			ret.append("ConsequentFieldRefs: [");
			ret.append(prepareForOutput(consequentFieldRefs));
			ret.append("]");
			connective = ", ";
		}
		if (conditionFieldRefs.size() > 0) {
			ret.append(connective);
			ret.append("ConditionFieldRefs: [");
			ret.append(prepareForOutput(conditionFieldRefs));
			ret.append("]");
			connective = ", ";
		}
		if (ruleLength != null) {
			ret.append(connective);
			ret.append("RuleLength: " + ruleLength);
			connective = ", ";
		}
		if (antecedentLength != null) {
			ret.append(connective);
			ret.append("AntecedentLength: " + antecedentLength);
			connective = ", ";
		}
		if (consequentLength != null) {
			ret.append(connective);
			ret.append("ConsequentLength: " + consequentLength);
			connective = ", ";
		}
		if (conditionLength != null) {
			ret.append(connective);
			ret.append("ConditionLength: " + conditionLength);
			connective = ", ";
		}
		ret.append("]");
		return ret.toString();
	}

	/**
	 * Helper method for transforming list of strings into suitable form for
	 * output.
	 * 
	 * @param items
	 * @return
	 */
	private String prepareForOutput(List<String> items) {
		StringBuffer ret = new StringBuffer();
		for (int i = 0; i < items.size(); i++) {
			if (i > 0) {
				ret.append(", ");
			}
			ret.append(items.get(i));
		}
		return ret.toString();
	}
}
