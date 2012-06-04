package xquerysearch.dao.bdbxml;

import xquerysearch.dao.IndexDao;
import xquerysearch.domain.Settings;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexSpecification;

/**
 * Implementation of {@link IndexDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlIndexDao extends ConnectionHelper implements IndexDao {

	/**
	 * Constructor
	 * 
	 *  @param settings
	 */
	public BdbxmlIndexDao(Settings settings) {
		this.settings = settings;
	}
	
	/*
	 * @{InheritDoc}
	 */
	public boolean insertIndex(String index) {
		 XmlContainer cont = openConnection(settings.getContainerName());
			String[] indexSplit = index.split(";");
			XmlIndexSpecification indexSpec = null;
			try {
				if (indexSplit.length == 3) {
	                indexSpec = cont.getIndexSpecification();
	                indexSpec.addIndex(indexSplit[0], indexSplit[1], indexSplit[2]);
	                cont.setIndexSpecification(indexSpec);
	                return true;
				} else {
					logger.warning("Adding index \"" + index + "\" failed! - Not 3 params");
					return false;
				}
	    	} catch (XmlException e) {
	    		logger.warning("Adding index \"" + index + "\" failed! - Xml exception");
	    		return false;
	    	} finally {
	    		indexSpec.delete();
	    		closeConnection(cont);
	    	}
	}

	/*
	 * @{InheritDoc}
	 */
	public boolean removeIndex(String index) {
		XmlContainer cont = openConnection(settings.getContainerName());
        String[] indexSplit = index.split(";");
        XmlIndexSpecification indexSpec = null;
        try {
            if (indexSplit.length == 3) {
        		indexSpec = cont.getIndexSpecification();
                indexSpec.deleteIndex(indexSplit[0], indexSplit[1], indexSplit[2]);
                cont.setIndexSpecification(indexSpec);
                return true;
            } else {
            	logger.warning("Removing index \"" + index + "\" failed! - Not 3 params");
            	return false;
            }
        } catch (XmlException e) {
        	logger.warning("Removing index \"" + index + "\" failed! - Xml exception");
        	return false;
        } finally {
        	indexSpec.delete();
        	closeConnection(cont);
        }
	}

}
