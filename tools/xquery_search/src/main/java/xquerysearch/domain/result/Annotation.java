package xquerysearch.domain.result;

/**
 * Domain object representing Annotation element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Annotation {

	private String interestingness;

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
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		return "<Annotation><Interestingness>" + interestingness + "</Interestingness></Annotation>";
	}
}
