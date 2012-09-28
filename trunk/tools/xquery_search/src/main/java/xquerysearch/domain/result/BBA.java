package xquerysearch.domain.result;

/**
 * Domain object representing BBA element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class BBA {

	private String id;
	private TransformationDictionary transformationDictionary;
	private DataDictionary dataDictionary;

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
	 * @return the transformationDictionary
	 */
	public TransformationDictionary getTransformationDictionary() {
		return transformationDictionary;
	}

	/**
	 * @param transformationDictionary
	 *            the transformationDictionary to set
	 */
	public void setTransformationDictionary(TransformationDictionary transformationDictionary) {
		this.transformationDictionary = transformationDictionary;
	}

	/**
	 * @return the dataDictionary
	 */
	public DataDictionary getDataDictionary() {
		return dataDictionary;
	}

	/**
	 * @param dataDictionary
	 *            the dataDictionary to set
	 */
	public void setDataDictionary(DataDictionary dataDictionary) {
		this.dataDictionary = dataDictionary;
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		String ret = "<BBA id=\"" + id + "\">";
		ret += transformationDictionary.toString();
		ret += "</BBA>";
		return ret;
	}

}
