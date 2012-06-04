package xquerysearch.dao.bdbxml;

import java.util.ArrayList;
import java.util.List;
import java.util.logging.Logger;

import xquerysearch.controllers.MainController;
import xquerysearch.dao.HelperDao;
import xquerysearch.domain.Settings;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexDeclaration;
import com.sleepycat.dbxml.XmlIndexSpecification;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlValue;

/**
 * Implementation of {@link HelperDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlHelperDao extends ConnectionHelper implements HelperDao {

	private Settings settings;
	private Logger logger = MainController.getLogger();
	
	/*
	 * @{InheritDoc}
	 */
	public List<String> getAllDocumentsNames() {
		List<String> names = new ArrayList<String>();
		XmlContainer cont = openConnection(settings.getContainerName());
		try {
			XmlResults results = cont.getAllDocuments(null);
			XmlValue document = null;
			while((document = results.next()) != null) {
				names.add(document.getNodeValue());
			}
			return names;
		} catch (XmlException e) {
			logger.warning("Retrieving all documents names failed!");
			return null;
		}
	}

	/*
	 * @{InheritDoc}
	 */
	public List<String[]> getAllIndexes() {
		List<String[]> indexes = new ArrayList<String[]>();
		XmlContainer cont = openConnection(settings.getContainerName());
		XmlIndexSpecification indexSpec = null;
		try {
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
			closeConnection(cont);
		}
	}

}
