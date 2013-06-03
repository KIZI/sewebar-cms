package xquerysearch.domain.result;

/**
 * BBA domain object for fuzzy analysis purposes.
 * 
 * @author Tomas Marek
 * 
 */
public class BBAForAnalysis extends BBA {

	private boolean isDisjunctive;

	/**
	 * Creates new {@link BBAForAnalysis} from {@link BBA} with disjunctive field set to <tt>false</tt>.
	 * 
	 * @param bba
	 */
	public BBAForAnalysis(BBA bba) {
		this.setDataDictionary(bba.getDataDictionary());
		this.setTransformationDictionary(bba.getTransformationDictionary());
		this.setId(bba.getId());
		this.setDisjunctive(false);
	}

	/**
	 * @return the isDisjunctive
	 */
	public boolean isDisjunctive() {
		return isDisjunctive;
	}

	/**
	 * @param isDisjunctive
	 *            the isDisjunctive to set
	 */
	public void setDisjunctive(boolean isDisjunctive) {
		this.isDisjunctive = isDisjunctive;
	}

}
