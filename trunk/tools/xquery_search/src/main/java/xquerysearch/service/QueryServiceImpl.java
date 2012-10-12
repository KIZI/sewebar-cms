package xquerysearch.service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.util.List;
import java.util.Set;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.oxm.castor.CastorMarshaller;
import org.springframework.stereotype.Service;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.fuzzysearch.service.FuzzySearchService;
import xquerysearch.sorting.OutputFuzzySorter;
import xquerysearch.transformation.QueryObjectTransformer;
import xquerysearch.transformation.QueryXpathTransformer;
import xquerysearch.utils.QueryUtils;

/**
 * This service provides querying to database for results.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class QueryServiceImpl extends AbstractService implements QueryService {
	
	@Value("${container.name}")
	protected String containerName;
	
	@Autowired
	private ResultsDao dao;
	
	@Autowired
	@Qualifier("arbQueryCastor")
	private CastorMarshaller arbQueryCastor;
	
	@Autowired
	private FuzzySearchService fuzzySearchService;
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public ResultSet getResultSet(Query query) {
		ByteArrayOutputStream preparedQuery = QueryUtils.queryPrepare(query.getQueryBody());
		String xpath = QueryUtils.makeXPath(new ByteArrayInputStream(preparedQuery.toByteArray()), false, "sewebar1.dbxml");
        String xquery = "" +
        		"for $ar in subsequence(" + xpath + ", 1, " + 100 + ")"
                + "\n return"
                + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
                    + "\n {$ar/Text}"
                    + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
                + "\n </Hit>" +
            "";
		return dao.getResultSetByXpath(xquery);
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public ResultSet getResultSet(String query) {
		ArBuilderQuery arbQuery = QueryObjectTransformer.transform(arbQueryCastor, query);
		String xpath = QueryXpathTransformer.transformToXpath(arbQuery, containerName);
		xpath = "for $ar in subsequence(" + xpath + ", 1, " + 100 + ")"
                + "\n return"
                + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
                    + "\n {$ar/Text}"
                    + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
                + "\n </Hit>";
		ResultSet resultSet = dao.getResultSetByXpath(xpath);
		return fuzzySearchService.evaluateResultSet(resultSet, arbQuery);
	}

	/**
	 * @{inheritDoc}
	 */
	@Override
	public List<Result> getSortedResults(String query) {
		ResultSet resultSet = getResultSet(query);
		Set<Result> results = resultSet.getResults();
		return OutputFuzzySorter.sortByCompliance(results);
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public String queryForSingleValue(String query) {
		List<String> results = dao.getResultsByXpath(query);
		if (results.size() != 1) {
			return null;
		}
		return results.get(0);
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public String query(String query) {
		List<String> results = dao.getResultsByQuery(new Query(query));
		return results.toString();
	}
}
