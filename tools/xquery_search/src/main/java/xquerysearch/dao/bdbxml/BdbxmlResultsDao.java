package xquerysearch.dao.bdbxml;

import java.util.ArrayList;
import java.util.List;

import org.springframework.stereotype.Repository;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.Result;

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

	/*
	 * @inheritDoc
	 */
	public List<Result> getResultsByQuery(Query query) {
		List<Result> results = new ArrayList<Result>();

		XmlContainer cont = null;
		XmlTransaction trans = null;
		try {
			cont = xmlManager.openContainer(containerName);
			trans = xmlManager.createTransaction();

			XmlQueryContext queryContext = xmlManager.createQueryContext();
			XmlResults dbResults = xmlManager.query(query.getQueryBody(), queryContext);
			XmlValue value = new XmlValue();
			while ((value = dbResults.next()) != null) {
				results.add(new Result(value.asString()));
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
	public List<Result> getResultsByXpath(String xpath) {
		List<Result> results = new ArrayList<Result>();

		XmlContainer cont = null;
		XmlTransaction trans = null;
		try {
			cont = xmlManager.openContainer(containerName);
			trans = xmlManager.createTransaction();

			XmlQueryContext queryContext = xmlManager.createQueryContext();
			XmlResults dbResults = xmlManager.query(xpath, queryContext);
			XmlValue value = new XmlValue();
			while ((value = dbResults.next()) != null) {
				results.add(new Result(value.asString()));
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
