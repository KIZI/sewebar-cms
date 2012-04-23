package xquerysearch.dao.bdbxml;

import java.util.ArrayList;
import java.util.List;
import java.util.logging.Logger;

import xquerysearch.controllers.MainController;
import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.Result;

import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlValue;

/**
 * Implementation of {@link ResultsDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlResultsDao extends ConnectionHelper implements ResultsDao {

	Logger logger = MainController.getLogger();
	
	/* 
	 * @inheritDoc
	 */
	@Override
	public List<Result> getResultsByQuery(Query query) {
		List<Result> results = new ArrayList<Result>();
		
		openConnecion(settings.getContainerName());
		try {
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
			closeConnection(null);
		}
	}

}
