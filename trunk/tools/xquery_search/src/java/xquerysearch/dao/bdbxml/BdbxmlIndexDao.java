package xquerysearch.dao.bdbxml;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexDeclaration;
import com.sleepycat.dbxml.XmlIndexSpecification;

import xquerysearch.dao.IndexDao;
import xquerysearch.settings.SettingsManager;

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
	public BdbxmlIndexDao(SettingsManager settings) {
		this.settings = settings;
	}
	
	/*
	 * @{InheritDoc}
	 */
	@Override
	public String getAllIndexes() {
		XmlContainer cont = openConnecion(settings.getContainerName());
		XmlIndexSpecification indexSpec = null;
		try {
            indexSpec = cont.getIndexSpecification();
            String indexes = "";
            
            int count = 0;
            XmlIndexDeclaration indexDeclaration = null;
            while ((indexDeclaration = (indexSpec.next())) != null) {
                indexes += "<index>"
                                + "<nodeName>" + indexDeclaration.name + "</nodeName>"
                                + "<indexType>" + indexDeclaration.index + "</indexType>"
                            + "</index>";
                count++;
            }
            String indexesCount = "<indexCount>" + count + "</indexCount>";
            return indexesCount + indexes;
		} catch (XmlException e) {
			logger.warning("Listing indexes failed! - Xml exeption");
			return null;
		} finally {
			indexSpec.delete();
			closeConnection(cont);
		}
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public boolean insertIndex(String index) {
		 XmlContainer cont = openConnecion(settings.getContainerName());
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
	@Override
	public boolean removeIndex(String index) {
		XmlContainer cont = openConnecion(settings.getContainerName());
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
