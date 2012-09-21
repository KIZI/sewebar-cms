package xquerysearch.dao.bdbxml;

import org.springframework.stereotype.Repository;

import xquerysearch.dao.IndexDao;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexSpecification;
import com.sleepycat.dbxml.XmlTransaction;

/**
 * Implementation of {@link IndexDao}.
 * 
 * @author Tomas Marek
 *
 */
@Repository
public class BdbxmlIndexDao extends AbstractDao implements IndexDao {

	private String containerName;
	
	/*
	 * @{InheritDoc}
	 */
	public boolean insertIndex(String index) {
		String[] indexSplit = index.split(";");
		XmlIndexSpecification indexSpec = null;
		XmlContainer cont = null;
        XmlTransaction trans = null;
        try {
        	cont = xmlManager.openContainer(containerName);
        	trans = xmlManager.createTransaction();
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
    		commitAndClose(trans, cont);
    	}
	}

	/*
	 * @{InheritDoc}
	 */
	public boolean removeIndex(String index) {
        String[] indexSplit = index.split(";");
        XmlIndexSpecification indexSpec = null;
        XmlContainer cont = null;
        XmlTransaction trans = null;
        try {
        	cont = xmlManager.openContainer(containerName);
        	trans = xmlManager.createTransaction();
        	
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
        	commitAndClose(trans, cont);
        }
	}

}
