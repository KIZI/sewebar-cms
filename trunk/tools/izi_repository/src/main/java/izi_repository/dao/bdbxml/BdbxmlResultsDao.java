package izi_repository.dao.bdbxml;

import izi_repository.dao.ResultsDao;
import izi_repository.domain.Query;
import izi_repository.domain.result.ResultSet;
import izi_repository.transformation.ResultObjectTransformer;

import java.util.ArrayList;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.oxm.castor.CastorMarshaller;
import org.springframework.stereotype.Repository;


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
	@Qualifier("resultCastor")
	private CastorMarshaller resultCastor;

	/**
	 * @inheritDoc
	 */
	@Override
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
			logger.logWarning(this.getClass().toString(), "Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/**
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
			logger.logWarning(this.getClass().toString(), "Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/**
	 * @{InheritDoc}
	 */
	@Override
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
				resultSet.getResults().add(ResultObjectTransformer.transform(resultCastor, value.asString()));
			}
			return resultSet;
		} catch (XmlException e) {
			logger.logWarning(this.getClass().toString(), "Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}

	/**
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
				resultSet.getResults().add(ResultObjectTransformer.transform(resultCastor, value.asString()));
			}
			return resultSet;
		} catch (XmlException e) {
			logger.logWarning(this.getClass().toString(), "Query failed!");
			return null;
		} finally {
			commitAndClose(trans, cont);
		}
	}
}
