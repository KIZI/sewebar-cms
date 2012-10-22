package xquerysearch.service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.util.ArrayList;
import java.util.List;
import java.util.Set;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.querysettings.QueryResultsAnalysis;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.fuzzysearch.service.FuzzySearchService;
import xquerysearch.grouping.service.GroupingService;
import xquerysearch.sorting.OutputFuzzySorter;
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
	private FuzzySearchService fuzzySearchService;
	
	@Autowired
	private GroupingService groupingService;

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ResultSet getResultSet(Query query) {
		ByteArrayOutputStream preparedQuery = QueryUtils.queryPrepare(query.getQueryBody());
		String xpath = QueryUtils.makeXPath(new ByteArrayInputStream(preparedQuery.toByteArray()), false,
				containerName);
		String xquery = ""
				+ "for $ar in subsequence("
				+ xpath
				+ ", 1, "
				+ 100
				+ ")"
				+ "\n return"
				+ "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
				+ "\n {$ar/Text}" + "<Detail>{$ar/child::node() except $ar/Text}</Detail>" + "\n </Hit>" + "";
		return dao.getResultSetByXpath(xquery);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ResultSet getResultSet(String xpath, int maxResults) {
		xpath = "for $ar in subsequence(collection(\""
				+ containerName
				+ "\")"
				+ xpath
				+ ", 1, "
				+ maxResults
				+ ")"
				+ "\n return"
				+ "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
				+ "\n {$ar/parent::node()/TaskSetting}"
				+ "\n {$ar/Text}" + "<Detail>{$ar/child::node() except $ar/Text}</Detail>" + "\n </Hit>";
		return dao.getResultSetByXpath(xpath);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Result> getResultList(ArBuilderQuery query, QuerySettings settings) {
		String xpath = QueryXpathTransformer.transformToXpath(query, settings);
		
		// TODO Max Results retrieve from query
		ResultSet resultSet = getResultSet(xpath, 100);
		Set<Result> results = resultSet.getResults();
		if (settings != null) {
			if (settings.getResultsAnalysis().equals(QueryResultsAnalysis.FUZZY.getText())) {
				return OutputFuzzySorter.sortByCompliance(fuzzySearchService.evaluateResults(results,
						query));
			}
		}
		return new ArrayList<Result>(results);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Group> getResultsInGroups(ArBuilderQuery query, QuerySettings settings) {
		List<Result> results = getResultList(query, settings);
		return groupingService.groupBy(results, settings.getParams());
	}
	
	/**
	 * {@inheritDoc}
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
	 * {@inheritDoc}
	 */
	@Override
	public String query(String query) {
		List<String> results = dao.getResultsByQuery(new Query(query));
		return results.toString();
	}

}
