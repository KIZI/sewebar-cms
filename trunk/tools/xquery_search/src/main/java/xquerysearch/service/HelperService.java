package xquerysearch.service;

import java.util.List;

import xquerysearch.dao.HelperDao;

/**
 * Provides additional informations.
 * 
 * @author Tomas Marek
 *
 */
public class HelperService {
	
	HelperDao dao;
	
	public List<String> getAllDocumentsNames() {
		return dao.getAllDocumentsNames();
	}

	public List<String[]> getAllIndexes() {
		return dao.getAllIndexes();
	}
}
