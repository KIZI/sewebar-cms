package xquerysearch.dao.bdbxml;

import java.util.ArrayList;
import java.util.List;
import java.util.logging.Logger;

import org.springframework.stereotype.Repository;

import xquerysearch.controller.MainController;
import xquerysearch.dao.AggregationDao;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexDeclaration;
import com.sleepycat.dbxml.XmlIndexSpecification;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;

/**
 * Implementation of {@link AggregationDao}.
 * 
 * @author Tomas Marek
 *
 */
@Repository
public class BdbxmlAggregationDao extends AbstractDao implements AggregationDao {

	private Logger logger = MainController.getLogger();
	
	/*
	 * @{InheritDoc}
	 */
	public List<String> getAllDocumentsNames() {
		List<String> names = new ArrayList<String>();
		XmlContainer cont = null;
        XmlTransaction trans = null;
        try {
        	cont = xmlManager.openContainer(containerName);
        	trans = xmlManager.createTransaction();
			XmlResults results = cont.getAllDocuments(null);
			while(results.hasNext()) {			
				names.add(results.next().asDocument().getName());
			}
			return names;
		} catch (XmlException e) {
			logger.warning("Retrieving all documents names failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/*
	 * @{InheritDoc}
	 */
	public List<String[]> getAllIndexes() {
		List<String[]> indexes = new ArrayList<String[]>();
		XmlIndexSpecification indexSpec = null;
		XmlContainer cont = null;
        XmlTransaction trans = null;
        try {
        	cont = xmlManager.openContainer(containerName);
        	trans = xmlManager.createTransaction();
            indexSpec = cont.getIndexSpecification();
            XmlIndexDeclaration indexDeclaration = null;
            while ((indexDeclaration = (indexSpec.next())) != null) {
                indexes.add(new String[]{indexDeclaration.name, indexDeclaration.index});
            }
            return indexes;
		} catch (XmlException e) {
			logger.warning("Listing indexes failed! - Xml exeption");
			return null;
		} finally {
			indexSpec.delete();
			commitAndClose(trans, cont);
		}
	}

}
