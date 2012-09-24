package xquerysearch.dao.bdbxml;

import java.util.ArrayList;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.oxm.castor.CastorMarshaller;
import org.springframework.stereotype.Repository;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.mapping.MappingCastor;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;
import com.sleepycat.dbxml.XmlValue;

/**
 * Implementation of {@link ResultsDao}.
 * 
 * @author Tomas Marek
 * 
 */
@Repository
public class BdbxmlResultsDao extends AbstractDao implements ResultsDao {

	@Autowired
	@Qualifier("resultSetCastor")
	private CastorMarshaller resultSetCastor;

	private static final MappingCastor<ResultSet> mappingCastor = new MappingCastor<ResultSet>();

	/*
	 * @inheritDoc
	 */
	public ResultSet getResultSetByQuery(Query query) {
		ResultSet resultSet = new ResultSet();

		XmlContainer cont = null;
		XmlTransaction trans = null;
		try {
			cont = xmlManager.openContainer(containerName);
			trans = xmlManager.createTransaction();

			XmlQueryContext queryContext = xmlManager.createQueryContext();
			XmlResults dbResults = xmlManager.query(query.getQueryBody(), queryContext);
			XmlValue value = new XmlValue();

			while ((value = dbResults.next()) != null) {
				resultSet = mappingCastor.targetToObject(resultSetCastor, value.asString());
			}
			return resultSet;
		} catch (XmlException e) {
			logger.warning("Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public ResultSet getResultSetByXpath(String xpath) {
		ResultSet resultSet = new ResultSet();

		XmlContainer cont = null;
		XmlTransaction trans = null;
		try {
			cont = xmlManager.openContainer(containerName);
			trans = xmlManager.createTransaction();

			XmlQueryContext queryContext = xmlManager.createQueryContext();
			XmlResults dbResults = xmlManager.query(xpath, queryContext);
			XmlValue value = new XmlValue();
			while ((value = dbResults.next()) != null) {
				resultSet = mappingCastor.targetToObject(resultSetCastor, value.asString());
			}
			return resultSet;
		} catch (XmlException e) {
			logger.warning("Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/*
	 * @inheritDoc
	 */
	public List<String> getResultsByQuery(Query query) {
		List<String> results = new ArrayList<String>();

		XmlContainer cont = null;
		XmlTransaction trans = null;
		try {
			cont = xmlManager.openContainer(containerName);
			trans = xmlManager.createTransaction();

			XmlQueryContext queryContext = xmlManager.createQueryContext();
			XmlResults dbResults = xmlManager.query(query.getQueryBody(), queryContext);
			XmlValue value = new XmlValue();
			while ((value = dbResults.next()) != null) {
				results.add(value.asString());
			}

			return results;
		} catch (XmlException e) {
			logger.warning("Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public List<String> getResultsByXpath(String xpath) {
		List<String> results = new ArrayList<String>();

		XmlContainer cont = null;
		XmlTransaction trans = null;
		try {
			cont = xmlManager.openContainer(containerName);
			trans = xmlManager.createTransaction();

			XmlQueryContext queryContext = xmlManager.createQueryContext();
			XmlResults dbResults = xmlManager.query(xpath, queryContext);
			XmlValue value = new XmlValue();
			while ((value = dbResults.next()) != null) {
				results.add(value.asString());
			}

			return results;
		} catch (XmlException e) {
			logger.warning("Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}
}
