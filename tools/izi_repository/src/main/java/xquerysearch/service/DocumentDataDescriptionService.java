package xquerysearch.service;

import xquerysearch.domain.result.datadescription.ResultDataDescription;

/**
 * Service for operations with {@link ResultDataDescription} of document.
 * 
 * @author Tomas Marek
 * 
 */
public interface DocumentDataDescriptionService {

	/**
	 * Retrieves {@link ResultDataDescription} for document specified by its id.
	 * 
	 * @param docId
	 * @return
	 */
	public ResultDataDescription getDataDescriptionByDocId(String docId);
}
