package xquerysearch.datadescription;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.dao.DataDescriptionDao;

/**
 * This class is responsible for handling all operations concerning data description.
 * 
 * @author Tomas Marek
 *
 */
public class DataDescriptionHandler {
	
	@Autowired
	private DataDescriptionDao dataDescriptionDao;
	
    /**
     * Method for retrieve saved data description from repository
     * @return DataDescription / chyba
     */
    public String getDataDescriptionCache() {
        String dataDescription = dataDescriptionDao.getDataDescriptionFromCache();
        if (dataDescription != null) {
        	return dataDescription;
        } else {
        	return "<error>Getting data description failed!</error>";
        }
    }
    
    /**
     * Saves data description into repository - caching
     * @return message - success/failure
     */
    public String actualizeDataDescriptionCache() {
       boolean saved = dataDescriptionDao.saveDataDescriptionIntoCache(dataDescriptionDao.getDataDescriptionFromData());
       if (saved) {
    	   return "<message>Data description successfully saved!</message>";
       } else {
    	   return "<error>Error occured during data description save!</error>";
       }
    }

}
