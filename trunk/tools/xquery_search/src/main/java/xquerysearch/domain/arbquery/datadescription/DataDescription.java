package xquerysearch.domain.arbquery.datadescription;

import java.util.Set;

/**
 * Domain object representing DataDescription element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class DataDescription {

	private Set<Dictionary> dictionaries;

	/**
	 * @return the dictionaries
	 */
	public Set<Dictionary> getDictionaries() {
		return dictionaries;
	}

	/**
	 * @param dictionaries
	 *            the dictionaries to set
	 */
	public void setDictionaries(Set<Dictionary> dictionaries) {
		this.dictionaries = dictionaries;
	}

}
