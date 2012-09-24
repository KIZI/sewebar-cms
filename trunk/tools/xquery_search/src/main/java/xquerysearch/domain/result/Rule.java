package xquerysearch.domain.result;

import java.util.Set;

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
	private Set<ImValue> imValues;
	private Annotation annotation;

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
	public Set<ImValue> getImValues() {
		return imValues;
	}

	/**
	 * @param imValues
	 *            the imValues to set
	 */
	public void setImValues(Set<ImValue> imValues) {
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

}
