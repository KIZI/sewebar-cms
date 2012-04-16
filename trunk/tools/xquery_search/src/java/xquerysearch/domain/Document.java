package xquerysearch.domain;

/**
 * General document object - abstract class. 
 * 
 * @author Tomas Marek
 *
 */
public class Document {
	
	private String docId;
	private String docBody;
	
	/**
	 * 
	 */
	public Document(String docId, String docBody) {
		this.docId = docId;
		this.docBody = docBody;
	}
	
	public String getDocId() {
		return docId;
	}
	
	public String getDocBody() {
		return docBody;
	}
		
}
