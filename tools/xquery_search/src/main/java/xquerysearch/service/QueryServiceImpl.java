package xquerysearch.service;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.ArQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.hybridquery.ArHybridBuilderQuery;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.arbquery.tasksetting.ArTsQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.fuzzysearch.service.FuzzySearchService;
import xquerysearch.grouping.service.GroupingService;
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
	private FuzzySearchService fuzzySearchService;

	@Autowired
	private GroupingService groupingService;

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
				+ "\n {$ar/parent::node()/TaskSetting}" + "\n {$ar/Text}"
				+ "<Detail>{$ar/child::node() except $ar/Text}</Detail>" + "\n </Hit>";
		return dao.getResultSetByXpath(xpath);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Result> getResultList(ArBuilderQuery query, QuerySettings settings) {
		String xpath = QueryXpathTransformer.transformToXpath(query, settings);
		xpath = "/PMML/" + xpath;

		// TODO Max Results retrieve from query
		ResultSet resultSet = getResultSet(xpath, 100);

		if (resultSet != null) {
			return resultSet.getResults();
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Result> getResultListByTsQuery(ArTsBuilderQuery query, QuerySettings settings) {
		String xpath = QueryXpathTaskSettingTransformer.transformToXpath(query, settings);
		xpath = "/PMML[" + xpath + "]/AssociationRule";

		// TODO Max Results retrieve from query
		ResultSet resultSet = getResultSet(xpath, 100);

		if (resultSet != null) {
			return resultSet.getResults();
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Result> getResultListByHybridQuery(ArHybridBuilderQuery query) {
		if (query == null) {
			return null;
		}

		ArTsQuery arTsQuery = query.getArTsQuery();
		ArQuery arQuery = query.getArQuery();

		if (arTsQuery != null && arQuery != null) {
			QuerySettings arTsSettings = QueryUtils.getQuerySettings(arTsQuery);
			QuerySettings arSettings = QueryUtils.getQuerySettings(arQuery);

			String arTsXpath = QueryXpathTaskSettingTransformer.transformToXpath(arTsQuery, arTsSettings);
			String arXpath = QueryXpathTransformer.transformToXpath(arQuery, arSettings);

			String xpath = "/PMML[TaskSetting[" + arTsXpath + "] and AssociationRule[" + arXpath
					+ "]]/AssociationRule";

			// TODO Max Results retrieve from query
			ResultSet resultSet = getResultSet(xpath, 100);

			if (resultSet != null) {
				return resultSet.getResults();
			}
		}
		return null;
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
		if (results != null) {
			return results.toString();
		} else {
			return null;
		}
	}

}
