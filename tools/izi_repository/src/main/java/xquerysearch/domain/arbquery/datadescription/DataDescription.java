package xquerysearch.domain.arbquery.datadescription;

import java.util.List;

/**
 * Domain object representing DataDescription element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class DataDescription {

	private List<Dictionary> dictionaries;

	/**
	 * @return the dictionaries
	 */
	public List<Dictionary> getDictionaries() {
		return dictionaries;
	}

	/**
	 * @param dictionaries
	 *            the dictionaries to set
	 */
	public void setDictionaries(List<Dictionary> dictionaries) {
		this.dictionaries = dictionaries;
	}

}
