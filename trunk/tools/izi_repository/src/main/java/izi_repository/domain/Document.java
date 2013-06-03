package izi_repository.domain;

/**
 * General document object - abstract class. 
 * 
 * @author Tomas Marek
 *
 */
public class Document {
	
	private String docId;
	private String docBody;
	// TODO: find out what represents and if needed
	private String docName;
	private String creationTime;
	private String reportUri;
	
	/**
	 * 
	 */
	public Document(String docId, String docBody, String docName, String creationTime, String reportUri) {
		this.docId = docId;
		this.docBody = docBody;
		this.docName = docName;
		this.creationTime = creationTime;
		this.reportUri = reportUri;
	}
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

	public String getDocName() {
		return docName;
	}

	public void setDocName(String docName) {
		this.docName = docName;
	}

	public String getCreationTime() {
		return creationTime;
	}

	public void setCreationTime(String creationTime) {
		this.creationTime = creationTime;
	}

	public String getReportUri() {
		return reportUri;
	}

	public void setReportUri(String reportUri) {
		this.reportUri = reportUri;
	}

	public void setDocId(String docId) {
		this.docId = docId;
	}

	public void setDocBody(String docBody) {
		this.docBody = docBody;
	}

	
}
