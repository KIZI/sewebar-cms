package xquerysearch.controller;

import java.util.List;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.oxm.castor.CastorMarshaller;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;

import xquerysearch.clustering.service.ClusteringService;
import xquerysearch.domain.Cluster;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.querysettings.QueryResultsAnalysisType;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.result.Result;
import xquerysearch.fuzzysearch.service.FuzzySearchService;
import xquerysearch.grouping.service.GroupingService;
import xquerysearch.service.AggregationService;
import xquerysearch.service.QueryService;
import xquerysearch.transformation.OutputTransformer;
import xquerysearch.transformation.QueryArBuilderQueryTransformer;
import xquerysearch.transformation.QueryArBuilderQueryTsTransformer;
import xquerysearch.utils.QueryUtils;

/**
 * Controller for querying.
 * 
 * @author Tomas Marek
 * 
 */
@Controller
public class QueryController extends AbstractController {

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

	// TODO rename action in jsp
	@RequestMapping(params = "action=useQuery", method = RequestMethod.POST)
	public ModelAndView queryForResult(@RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		if (content.isEmpty()) {
			addResponseContent("<error>Query content has to be entered!</error>", response);
			return null;
		}

		long startTime = System.currentTimeMillis();

		ArBuilderQuery arbQuery = null;
		ArTsBuilderQuery arbTsQuery = null;
		QuerySettings settings = null;

		if (content.contains("<Target>TaskSetting</Target>")) {
			arbTsQuery = QueryArBuilderQueryTsTransformer.transform(arbTsQueryCastor, content);
			settings = QueryUtils.getQuerySettings(arbTsQuery);
			// xpath =
			// QueryXpathTaskSettingTransformer.transformToXpath(arbTsQuery,
			// settings);
		} else {
			arbQuery = QueryArBuilderQueryTransformer.transform(arbQueryCastor, content);
			settings = QueryUtils.getQuerySettings(arbQuery);
		}
		if (settings != null) {
			QueryResultsAnalysisType qraType = QueryResultsAnalysisType
					.convert(settings.getResultsAnalysis());
			switch (qraType) {
			case GROUPING:
				processGrouping(response, arbQuery, settings, startTime);
				break;
			case FUZZY:
				processFuzzy(response, arbQuery, settings, startTime);
				break;
			case CLUSTERING:
				processClustering(response, arbQuery, settings, startTime);
				break;
			default:
				processDefault(response, arbQuery, settings, startTime);
				break;
			}
		} else {
			processDefault(response, arbQuery, settings, startTime);
		}
		return null;
	}

	@RequestMapping(params = "action=directQuery", method = RequestMethod.POST)
	public ModelAndView directQuery(@RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		String results = queryService.query(content);
		addResponseContent("<result>" + results + "</result>", response);
		return null;
	}

	private void processDefault(HttpServletResponse response, ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		StringBuffer responseMessage = new StringBuffer();
		Long docCount = aggregationService.getDocumentsCount();
		Long arCount = aggregationService.getAssociationRulesCount();

		long queryStartTime = System.currentTimeMillis();

		List<Result> results = queryService.getResultList(arbQuery, settings);

		long queryTime = System.currentTimeMillis() - queryStartTime;

		responseMessage.append(OutputTransformer
				.transformResultsInList(results, queryTime, docCount, arCount));

		long fullTime = System.currentTimeMillis() - startTime;

		addResponseContent("<result milisecs=\"" + fullTime + "\">" + responseMessage.toString()
				+ "</result>", response);

	}

	private void processGrouping(HttpServletResponse response, ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		StringBuffer responseMessage = new StringBuffer();
		Long docCount = aggregationService.getDocumentsCount();
		Long arCount = aggregationService.getAssociationRulesCount();

		long queryStartTime = System.currentTimeMillis();

		List<Group> groups = groupingService.getGroupsByQuery(arbQuery, settings);

		long queryTime = System.currentTimeMillis() - queryStartTime;

		responseMessage.append(OutputTransformer.transformResultGroups(groups, queryTime, docCount, arCount));

		long fullTime = System.currentTimeMillis() - startTime;

		addResponseContent("<result milisecs=\"" + fullTime + "\">" + responseMessage.toString()
				+ "</result>", response);
	}

	private void processFuzzy(HttpServletResponse response, ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		StringBuffer responseMessage = new StringBuffer();
		Long docCount = aggregationService.getDocumentsCount();
		Long arCount = aggregationService.getAssociationRulesCount();

		long queryStartTime = System.currentTimeMillis();

		List<Result> results = fuzzyService.getFuzzyResultsByQuery(arbQuery, settings);

		long queryTime = System.currentTimeMillis() - queryStartTime;

		responseMessage.append(OutputTransformer
				.transformResultsInList(results, queryTime, docCount, arCount));

		long fullTime = System.currentTimeMillis() - startTime;

		addResponseContent("<result milisecs=\"" + fullTime + "\">" + responseMessage.toString()
				+ "</result>", response);
	}

	private void processClustering(HttpServletResponse response, ArBuilderQuery arbQuery, QuerySettings settings, long startTime) {
		StringBuffer responseMessage = new StringBuffer();
		Long docCount = aggregationService.getDocumentsCount();
		Long arCount = aggregationService.getAssociationRulesCount();

		long queryStartTime = System.currentTimeMillis();

		List<Cluster> results = clusteringService.getClustersByQuery(arbQuery, settings);

		long queryTime = System.currentTimeMillis() - queryStartTime;

		responseMessage.append(OutputTransformer.transformResultClusters(results, queryTime, docCount,
				arCount));

		long fullTime = System.currentTimeMillis() - startTime;

		addResponseContent("<result milisecs=\"" + fullTime + "\">" + responseMessage.toString()
				+ "</result>", response);
	}
}
