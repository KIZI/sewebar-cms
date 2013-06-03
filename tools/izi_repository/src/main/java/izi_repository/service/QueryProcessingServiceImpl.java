package izi_repository.service;

import izi_repository.clustering.service.ClusteringService;
import izi_repository.domain.Cluster;
import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.arbquery.hybridquery.ArHybridBuilderQuery;
import izi_repository.domain.arbquery.querysettings.QueryResultsAnalysisType;
import izi_repository.domain.arbquery.tasksetting.ArTsBuilderQuery;
import izi_repository.domain.grouping.Group;
import izi_repository.domain.result.Result;
import izi_repository.fuzzysearch.service.FuzzySearchService;
import izi_repository.grouping.service.GroupingService;
import izi_repository.logging.search.SearchLogger;
import izi_repository.transformation.OutputTransformer;
import izi_repository.transformation.QueryArBuilderQueryHybridTransformer;
import izi_repository.transformation.QueryArBuilderQueryTransformer;
import izi_repository.transformation.QueryArBuilderQueryTsTransformer;
import izi_repository.utils.QueryUtils;

import java.util.List;

import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.oxm.castor.CastorMarshaller;
import org.springframework.stereotype.Service;


/**
 * Implementation of {@link QueryProcessingService}.
 * 
 * @author Tomas Marek
 * 
 */
@Service
public class QueryProcessingServiceImpl implements QueryProcessingService {

	@Autowired
	private QueryService queryService;

	@Autowired
	private AggregationService aggregationService;

	@Autowired
	private FuzzySearchService fuzzyService;

	@Autowired
	private ClusteringService clusteringService;

	@Autowired
	private GroupingService groupingService;

	@Autowired
	@Qualifier("arbQueryCastor")
	private CastorMarshaller arbQueryCastor;

	@Autowired
	@Qualifier("arbTsQueryCastor")
	private CastorMarshaller arbTsQueryCastor;

	@Autowired
	@Qualifier("hybridQueryCastor")
	private CastorMarshaller hybridQueryCastor;

	@Autowired
	private SearchLogger searchLogger;

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String processDirectQuery(String query) {
		return queryService.query(query);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String processQuery(String query, long startTime) {
		searchLogger.logQuery(query, startTime);

		ArBuilderQuery arbQuery = null;
		ArTsBuilderQuery arbTsQuery = null;
		ArHybridBuilderQuery arbHybridQuery = null;
		QuerySettings settings = null;

		if (query.contains("<HybridQuery>")) {
			arbHybridQuery = QueryArBuilderQueryHybridTransformer.transform(hybridQueryCastor, query);
			return processHybridQuery(arbHybridQuery, startTime);
		} else if (query.contains("<Target>TaskSetting</Target>")) {
			arbTsQuery = QueryArBuilderQueryTsTransformer.transform(arbTsQueryCastor, query);
			settings = QueryUtils.getQuerySettings(arbTsQuery);
			if (settings != null) {
				QueryResultsAnalysisType qraType = QueryResultsAnalysisType.convert(settings
						.getResultsAnalysis());
				switch (qraType) {
				case GROUPING:
					return processGrouping(arbTsQuery, settings, startTime);
				case FUZZY:
					return processFuzzy(arbTsQuery, settings, startTime);
				default:
					return processTsQuery(arbTsQuery, settings, startTime);
				}
			}
			return processTsQuery(arbTsQuery, settings, startTime);
		} else {
			arbQuery = QueryArBuilderQueryTransformer.transform(arbQueryCastor, query);
			settings = QueryUtils.getQuerySettings(arbQuery);
		}
		if (settings != null) {
			QueryResultsAnalysisType qraType = QueryResultsAnalysisType
					.convert(settings.getResultsAnalysis());
			switch (qraType) {
			case GROUPING:
				return processGrouping(arbQuery, settings, startTime);
			case FUZZY:
				return processFuzzy(arbQuery, settings, startTime);
			case CLUSTERING:
				return processClustering(arbQuery, settings, startTime);
			default:
				return processDefault(arbQuery, settings, startTime);
			}
		}
		return processDefault(arbQuery, settings, startTime);
	}

	private String processDefault(ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Result> results = queryService.getResultList(arbQuery, settings);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		boolean useLegacy = false;
		if (settings != null) {
			useLegacy = settings.getUseLegacyOutput();
		}
		return processListOfObjects(results, queryTime, startTime, useLegacy);
	}

	private String processGrouping(ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Group> groups = groupingService.getGroupsByQuery(arbQuery, settings);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		boolean useLegacy = false;
		if (settings != null) {
			useLegacy = settings.getUseLegacyOutput();
		}
		return processListOfObjects(groups, queryTime, startTime, useLegacy);
	}

	private String processGrouping(ArTsBuilderQuery arbTsQuery, QuerySettings settings, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Group> groups = groupingService.getGroupsByQuery(arbTsQuery, settings);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		boolean useLegacy = false;
		if (settings != null) {
			useLegacy = settings.getUseLegacyOutput();
		}
		return processListOfObjects(groups, queryTime, startTime, useLegacy);
	}

	private String processFuzzy(ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Result> results = fuzzyService.getFuzzyResultsByQuery(arbQuery, settings);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		boolean useLegacy = false;
		if (settings != null) {
			useLegacy = settings.getUseLegacyOutput();
		}
		return processListOfObjects(results, queryTime, startTime, useLegacy);
	}

	private String processFuzzy(ArTsBuilderQuery arbTsQuery, QuerySettings settings, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Result> results = fuzzyService.getFuzzyResultsByQuery(arbTsQuery, settings);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		boolean useLegacy = false;
		if (settings != null) {
			useLegacy = settings.getUseLegacyOutput();
		}
		return processListOfObjects(results, queryTime, startTime, useLegacy);
	}

	private String processClustering(ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Cluster> results = clusteringService.getClustersByQuery(arbQuery, settings);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		boolean useLegacy = false;
		if (settings != null) {
			useLegacy = settings.getUseLegacyOutput();
		}
		return processListOfObjects(results, queryTime, startTime, useLegacy);
	}

	private String processHybridQuery(ArHybridBuilderQuery hybridQuery, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Result> results = queryService.getResultListByHybridQuery(hybridQuery);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		
		// TODO set legacy output based on received query?
		return processListOfObjects(results, queryTime, startTime, false);
	}

	private String processTsQuery(ArTsBuilderQuery tsQuery, QuerySettings settings, long startTime) {
		long queryStartTime = System.currentTimeMillis();
		List<Result> results = queryService.getResultListByTsQuery(tsQuery, settings);
		long queryTime = System.currentTimeMillis() - queryStartTime;
		boolean useLegacy = false;
		if (settings != null) {
			useLegacy = settings.getUseLegacyOutput();
		}
		return processListOfObjects(results, queryTime, startTime, useLegacy);
	}

	/**
	 * Helping method for transforming given list of objects to
	 * response-friendly form. Result of transformation is appended to
	 * {@link HttpServletResponse}.
	 * 
	 * @param list
	 * @param response
	 * @param queryTime
	 * @param startTime
	 */
	private String processListOfObjects(List<? extends Object> list, long queryTime, long startTime, boolean useLegacyOutput) {
		StringBuffer responseMessage = new StringBuffer();
		Long docCount = aggregationService.getDocumentsCount();
		Long arCount = aggregationService.getAssociationRulesCount();

		if (useLegacyOutput == true) {
			responseMessage.append(OutputTransformer.transformObjectsListToLegacy(list, queryTime, docCount,
					arCount));
		} else {
			responseMessage
					.append(OutputTransformer.transformObjectsList(list, queryTime, docCount, arCount));
		}

		searchLogger.logResult(responseMessage.toString(), startTime);

		long fullTime = System.currentTimeMillis() - startTime;

		return "<result milisecs=\"" + fullTime + "\">" + responseMessage.toString() + "</result>";
	}

}
