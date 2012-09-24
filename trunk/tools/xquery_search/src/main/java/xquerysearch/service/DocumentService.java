package xquerysearch.service;

import java.io.File;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.Document;
import xquerysearch.transformer.XsltTransformer;
import xquerysearch.validation.DocumentValidator;

/**
 * Service for operations with {@link Document}s.
 * 
 * @author Tomas Marek
 * 
 */
@Service
public class DocumentService {

	@Autowired
	private DocumentDao dao;

	@Value("${trans.use}")
	private boolean transform;

	@Value("${trans.qbar.path}")
	private String qbarTransPath;

	@Value("${trans.bkef.path}")
	private String bkefTransPath;

	@Value("${trans.pmml.path}")
	private String pmmlTransPath;

	@Value("${validation.pmml.path}")
	private String pmmlValidationPath;

	/**
	 * Retrieves {@link Document} by id.
	 * 
	 * @param docId
	 * @return found document
	 */
	public Document getDocumentById(String docId) {
		return dao.getDocumentById(docId);
	}

	/**
	 * Inserts {@link Document}.
	 * 
	 * @param document
	 * @return <code>true</code> when successfully inserted, <code>false</code>
	 *         otherwise
	 */
	public boolean insertDocument(Document document) {
		String documentBody = document.getDocBody();
		File transFile = null;
		if (transform) {
			if (documentBody.toLowerCase().contains("<pmml")) {
				if (DocumentValidator.validate(documentBody, pmmlValidationPath) == false) {
					// TODO logging here
					System.out.println("ERR1");
					return false;
				}
				transFile = new File(pmmlTransPath);
			} else if (documentBody.toLowerCase().contains("<annotatedassociationrules>")) {
				transFile = new File(qbarTransPath);
			} else if (documentBody.toLowerCase().contains("sourcetype=\"bkef\"")) {
				transFile = new File(bkefTransPath);
			}
		}

		if (transFile != null) {
			String transformedBody = XsltTransformer.transform(documentBody, transFile, document.getDocId(),
					document.getCreationTime(), document.getReportUri());
			if (transformedBody != null) {
				document.setDocBody(transformedBody);
			} else {
				// TODO logging here
				System.out.println("ERR2");
				return false;
			}
		} else {
			// TODO logging here
			System.out.println("ERR3");
			return false;
		}

		return dao.insertDocument(document);
	}

	/**
	 * Removes {@link Document} specified by id.
	 * 
	 * @param docId
	 * @return <code>true</code> when successfully removed, <code>false</code>
	 *         otherwise
	 */
	public boolean removeDocument(String docId) {
		return dao.removeDocument(docId);
	}
}
