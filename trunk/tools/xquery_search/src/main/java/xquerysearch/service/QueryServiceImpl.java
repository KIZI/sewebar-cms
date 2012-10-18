package xquerysearch.service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.util.ArrayList;
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
import xquerysearch.domain.arbquery.ArQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.querysettings.QueryResultsAnalysis;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.arbquery.tasksetting.ArTsQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.fuzzysearch.service.FuzzySearchService;
import xquerysearch.sorting.OutputFuzzySorter;
import xquerysearch.transformation.QueryArBuilderQueryTransformer;
import xquerysearch.transformation.QueryArBuilderQueryTsTransformer;
import xquerysearch.transformation.QueryXpathTaskSettingTransformer;
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
	@Qualifier("arbTsQueryCastor")
	private CastorMarshaller arbTsQueryCastor;

	@Autowired
	private FuzzySearchService fuzzySearchService;

	/**
	 * @{inheritDoc
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
	 * @{inheritDoc
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
	 * @{inheritDoc
	 */
	@Override
	public List<Result> getResultList(String query) {
		ArBuilderQuery arbQuery = null;
		ArTsBuilderQuery arbTsQuery = null;
		String xpath = null;
		QuerySettings settings = null;
		if (query.contains("<Target>TaskSetting</Target>")) {
			arbTsQuery = QueryArBuilderQueryTsTransformer.transform(arbTsQueryCastor, query);
			settings = getQuerySettings(arbTsQuery);
			xpath = QueryXpathTaskSettingTransformer.transformToXpath(arbTsQuery, settings);
		} else {
			arbQuery = QueryArBuilderQueryTransformer.transform(arbQueryCastor, query);
			settings = getQuerySettings(arbQuery);
			xpath = QueryXpathTransformer.transformToXpath(arbQuery, settings);
		}
		// TODO Max Results retrieve from query
		ResultSet resultSet = getResultSet(xpath, 100);
		Set<Result> results = resultSet.getResults();
		if (settings != null) {
			if (settings.getResultsAnalysis().equals(QueryResultsAnalysis.FUZZY.getText())) {
				return OutputFuzzySorter.sortByCompliance(fuzzySearchService.evaluateResults(results,
						arbQuery));
			}
		}
		return new ArrayList<Result>(results);
	}

	/**
	 * @{inheritDoc
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
	 * @{inheritDoc
	 */
	@Override
	public String query(String query) {
		List<String> results = dao.getResultsByQuery(new Query(query));
		return results.toString();
	}

	/**
	 * Helps retrieve {@link QuerySettings} from {@link ArBuilderQuery}.
	 * 
	 * @param query
	 * @return
	 */
	private QuerySettings getQuerySettings(ArBuilderQuery query) {
		if (query != null) {
			ArQuery arQuery = query.getArQuery();
			if (arQuery != null) {
				return arQuery.getQuerySettings();
			}
		}
		return null;
	}
	
	/**
	 * Helps retrieve {@link QuerySettings} from {@link ArTsBuilderQuery}.
	 * 
	 * @param query
	 * @return
	 */
	private QuerySettings getQuerySettings(ArTsBuilderQuery query) {
		if (query != null) {
			ArTsQuery arQuery = query.getArQuery();
			if (arQuery != null) {
				return arQuery.getQuerySettings();
			}
		}
		return null;
	}
}
