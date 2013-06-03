package izi_repository.domain.result;

import java.util.List;

/**
 * Domain object representing Rule from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Rule {

	private Cedent antecedent;
	private Cedent consequent;
	private Cedent condition;
	private List<ImValue> imValues;
	private Annotation annotation;
	private FourFtTable fourFtTable;

	/**
	 * @return the antecedent
	 */
	public Cedent getAntecedent() {
		return antecedent;
	}

	/**
	 * @param antecedent
	 *            the antecedent to set
	 */
	public void setAntecedent(Cedent antecedent) {
		this.antecedent = antecedent;
	}

	/**
	 * @return the consequent
	 */
	public Cedent getConsequent() {
		return consequent;
	}

	/**
	 * @param consequent
	 *            the consequent to set
	 */
	public void setConsequent(Cedent consequent) {
		this.consequent = consequent;
	}

	/**
	 * @return the condition
	 */
	public Cedent getCondition() {
		return condition;
	}

	/**
	 * @param condition
	 *            the condition to set
	 */
	public void setCondition(Cedent condition) {
		this.condition = condition;
	}

	/**
	 * @return the imValues
	 */
	public List<ImValue> getImValues() {
		return imValues;
	}

	/**
	 * @param imValues
	 *            the imValues to set
	 */
	public void setImValues(List<ImValue> imValues) {
		this.imValues = imValues;
	}

	/**
	 * @return the annotation
	 */
	public Annotation getAnnotation() {
		return annotation;
	}

	/**
	 * @param annotation
	 *            the annotation to set
	 */
	public void setAnnotation(Annotation annotation) {
		this.annotation = annotation;
	}

	/**
	 * @return the fourFtTable
	 */
	public FourFtTable getFourFtTable() {
		return fourFtTable;
	}

	/**
	 * @param fourFtTable
	 *            the fourFtTable to set
	 */
	public void setFourFtTable(FourFtTable fourFtTable) {
		this.fourFtTable = fourFtTable;
	}

	/**
	 * @{inheritDoc
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<Detail>");
		if (antecedent != null) {
			ret.append(antecedent.toString());
		}
		if (consequent != null) {
			ret.append(consequent.toString());
		}
		if (condition != null) {
			ret.append(condition.toString());
		}
		for (ImValue imValue : imValues) {
			ret.append(imValue.toString());
		}
		if (annotation != null) {
			ret.append(annotation.toString());
		}
		if (fourFtTable != null) {
			ret.append(fourFtTable.toString());
		}
		ret.append("</Detail>");
		return ret.toString();
	}

}
