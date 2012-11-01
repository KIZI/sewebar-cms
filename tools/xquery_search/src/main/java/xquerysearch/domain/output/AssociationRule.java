package xquerysearch.domain.output;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.result.ImValue;

/**
 * Domain object representing AssociationRule element in output.
 * 
 * @author Tomas Marek
 * 
 */
public class AssociationRule {

	private String antecedent;
	private String consequent;
	private String condition;
	private String text;
	private String interestingness;
	private List<ImValue> imValues = new ArrayList<ImValue>();

	/**
	 * @return the antecedent
	 */
	public String getAntecedent() {
		return antecedent;
	}

	/**
	 * @param antecedent
	 *            the antecedent to set
	 */
	public void setAntecedent(String antecedent) {
		this.antecedent = antecedent;
	}

	/**
	 * @return the consequent
	 */
	public String getConsequent() {
		return consequent;
	}

	/**
	 * @param consequent
	 *            the consequent to set
	 */
	public void setConsequent(String consequent) {
		this.consequent = consequent;
	}

	/**
	 * @return the condition
	 */
	public String getCondition() {
		return condition;
	}

	/**
	 * @param condition
	 *            the condition to set
	 */
	public void setCondition(String condition) {
		this.condition = condition;
	}

	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}

	/**
	 * @param text
	 *            the text to set
	 */
	public void setText(String text) {
		this.text = text;
	}

	/**
	 * @return the imValues
	 */
	public List<ImValue> getImValues() {
		return imValues;
	}

	/**
	 * @return the interestingness
	 */
	public String getInterestingness() {
		return interestingness;
	}

	/**
	 * @param interestingness
	 *            the interestingness to set
	 */
	public void setInterestingness(String interestingness) {
		this.interestingness = interestingness;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();

		ret.append("<AssociationRule");
		if (antecedent != null) {
			ret.append(" antecedent=\"" + antecedent + "\"");
		}
		if (consequent != null) {
			ret.append(" consequent=\"" + consequent + "\"");
		}
		if (condition != null) {
			ret.append(" condition=\"" + condition + "\"");
		}
		ret.append(">");
		if (text != null) {
			ret.append("<Text><![CDATA[" + text + "]]></Text>");
		}
		for (ImValue imValue : imValues) {
			ret.append("<IMValue name=\"" + imValue.getName() + "\">" + imValue.getValue() + "</IMValue>");
		}
		if (interestingness != null) {
			ret.append("<Annotation><Interestingness>" + interestingness + "</Interestingness></Annotation>");
		}
		ret.append("</AssociationRule>");
		return ret.toString();
	}
}
