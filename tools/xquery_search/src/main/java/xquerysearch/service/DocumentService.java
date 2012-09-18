package xquerysearch.service;

import java.io.File;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.Document;
import xquerysearch.transformation.XSLTTransformer;

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
	
	@Value("$(trans.use)")
	private boolean transform;
	
	@Value("$(trans.qbar.path")
	private String qbarTransPath;
	
	@Value("$(trans.bkef.path)")
	private String bkefTransPath;
	
	@Value("$(trans.pmml.path)")
	private String pmmlTransPath;
	
	public Document getDocumentById(String docId) {
		return dao.getDocumentById(docId); 
	}
	
	public boolean insertDocument(Document document) {
		String documentBody = document.getDocBody();
		File transFile = null;
		if (transform) {
			if (documentBody.toLowerCase().contains("<pmml")) {
				transFile = new File(pmmlTransPath);
            } else if (documentBody.toLowerCase().contains("<annotatedassociationrules>")) {
            	transFile = new File(qbarTransPath);
            } else if (documentBody.toLowerCase().contains("sourcetype=\"bkef\"")) {
            	transFile = new File(bkefTransPath);
            } 
		}
		
		if (transFile != null) {
			String transformedBody = XSLTTransformer.transform(documentBody, transFile, document.getDocId(), document.getCreationTime(), document.getReportUri());
			if (transformedBody != null) {
				document.setDocBody(transformedBody);
			} else {
				// TODO logging here
				return false;
			}
		} else {
			// TODO logging here
			return false;
		}
		
		return dao.insertDocument(document);
	}
	
	public boolean removeDocument(String docId) {
		return dao.removeDocument(docId);
	}

}
