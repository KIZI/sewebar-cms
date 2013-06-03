package izi_repository.service;

import izi_repository.dao.ResultsDao;
import izi_repository.domain.Query;
import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.ArQuery;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.arbquery.hybridquery.ArHybridBuilderQuery;
import izi_repository.domain.arbquery.tasksetting.ArTsBuilderQuery;
import izi_repository.domain.arbquery.tasksetting.ArTsQuery;
import izi_repository.domain.result.Result;
import izi_repository.domain.result.ResultSet;
import izi_repository.transformation.QueryXpathTaskSettingTransformer;
import izi_repository.transformation.QueryXpathTransformer;
import izi_repository.utils.QuerySettingsUtils;
import izi_repository.utils.QueryUtils;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;


/**
 * This service provides querying to database for results.
 * 
 * @author Tomas Marek
 * 
 */
@Service
public class QueryServiceImpl extends AbstractService implements QueryService {

	private static final int MAX_RESULTS = 500;
	
	@Value("${container.name}")
	protected String containerName;

	@Autowired
	private ResultsDao dao;

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

		int maxResults = QuerySettingsUtils.getMaxResults(settings);
		if (maxResults == 0) {
			maxResults = MAX_RESULTS;
		}
		
		ResultSet resultSet = getResultSet(xpath, maxResults);

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

		int maxResults = QuerySettingsUtils.getMaxResults(settings);
		if (maxResults == 0) {
			maxResults = MAX_RESULTS;
		}
		
		ResultSet resultSet = getResultSet(xpath, maxResults);

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

			String xpath = "/PMML/AssociationRule[count(../TaskSetting[" + arTsXpath + "]) > 0 and "
					+ arXpath + "]";

			ResultSet resultSet = getResultSet(xpath, MAX_RESULTS);

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
