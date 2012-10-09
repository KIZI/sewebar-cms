package xquerysearch.service;

import java.io.File;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.Document;
import xquerysearch.transformation.XsltTransformer;
import xquerysearch.validation.DocumentValidator;

/**
 * Implementation of {@link DocumentService}.
 * 
 * @author Tomas Marek
 * 
 */
@Service
public class DocumentServiceImpl extends AbstractService implements DocumentService {

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
	 * {@inheritDoc}
	 */
	@Override
	public Document getDocumentById(String docId) {
		return dao.getDocumentById(docId);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public boolean insertDocument(Document document) {
		String documentBody = document.getDocBody();
		File transFile = null;
		if (transform) {
			if (documentBody.toLowerCase().contains("<pmml")) {
				if (DocumentValidator.validate(documentBody, pmmlValidationPath) == false) {
					logger.warn("PMML validation failed!");
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
				logger.warn("XSLT transformation process failed!");
				return false;
			}
		} else {
			logger.warn("XSLT file error!");
			return false;
		}

		return dao.insertDocument(document);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public boolean removeDocument(String docId) {
		return dao.removeDocument(docId);
	}
}
