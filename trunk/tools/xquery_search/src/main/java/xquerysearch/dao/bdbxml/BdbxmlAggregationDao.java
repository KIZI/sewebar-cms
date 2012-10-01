package xquerysearch.dao.bdbxml;

import java.util.ArrayList;
import java.util.List;

import org.springframework.stereotype.Repository;

import xquerysearch.dao.AggregationDao;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexDeclaration;
import com.sleepycat.dbxml.XmlIndexSpecification;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;
import com.sleepycat.dbxml.XmlValue;

/**
 * Implementation of {@link AggregationDao}.
 * 
 * @author Tomas Marek
 * 
 */
@Repository
public class BdbxmlAggregationDao extends AbstractDao implements AggregationDao {

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
			while (results.hasNext()) {
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
				indexes.add(new String[] { indexDeclaration.name, indexDeclaration.index });
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

	/**
	 * @{inheritDoc
	 */
	@Override
	public Long getDocumentsCount() {
		String count = getStringByXPath("count(collection(\"" + containerName + "\")/PMML)");
		return Long.parseLong(count);
	}

	/**
	 * @{inheritDoc
	 */
	@Override
	public Long getAssociationRulesCount() {
		String count = getStringByXPath("count(collection(\"" + containerName + "\")/PMML/AssociationRule)");
		return Long.parseLong(count);
	}

	/**
	 * Helping method returning single {@link String} value. DB is queried by
	 * XPath query.
	 * 
	 * @param xpath
	 * @return
	 */
	private String getStringByXPath(String xpath) {
		XmlContainer cont = null;
		XmlTransaction trans = null;
		try {
			cont = xmlManager.openContainer(containerName);
			trans = xmlManager.createTransaction();
			XmlQueryContext queryContext = xmlManager.createQueryContext();
			XmlResults dbResults = xmlManager.query(xpath, queryContext);
			if (dbResults.size() != 1) {
				return null;
			}
			XmlValue value = dbResults.next();
			return value.asString();
		} catch (XmlException e) {
			logger.warning("GetStringByXPath method failed! - Xml exeption");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}
}
