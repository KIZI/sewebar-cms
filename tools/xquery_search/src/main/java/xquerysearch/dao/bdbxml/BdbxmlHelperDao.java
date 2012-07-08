package xquerysearch.dao.bdbxml;

import java.util.ArrayList;
import java.util.List;
import java.util.logging.Logger;

import xquerysearch.controller.MainController;
import xquerysearch.dao.HelperDao;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexDeclaration;
import com.sleepycat.dbxml.XmlIndexSpecification;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;
import com.sleepycat.dbxml.XmlValue;

/**
 * Implementation of {@link HelperDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlHelperDao extends ConnectionHelper implements HelperDao {

	private Logger logger = MainController.getLogger();
	
	private String containerName;
	
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
			XmlValue document = null;
			while((document = results.next()) != null) {
				names.add(document.getNodeValue());
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
