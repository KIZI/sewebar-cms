package xquerysearch.domain;

/**
 * Domain object representing PMML document. 
 * 
 * @author Tomas Marek
 *
 */
public class PmmlDocument extends Document {

	/**
	 * @param docId
	 * @param docBody
	 */
	public PmmlDocument(String docId, String docBody) {
		super(docId, docBody);
	}

}
