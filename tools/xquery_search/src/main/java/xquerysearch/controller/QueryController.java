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

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.querysettings.QueryResultsAnalysis;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.result.Result;
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
			if (settings.getResultsAnalysis() != null
					&& settings.getResultsAnalysis().equals(QueryResultsAnalysis.GROUPING.getText())) {
				processGrouping(response, arbQuery, settings, startTime);
			} else {
				processDefault(response, arbQuery, settings, startTime);
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

		List<Group> groups = queryService.getResultsInGroups(arbQuery, settings);

		long queryTime = System.currentTimeMillis() - queryStartTime;

		responseMessage.append(OutputTransformer.transformResultGroups(groups, queryTime, docCount, arCount));

		long fullTime = System.currentTimeMillis() - startTime;

		addResponseContent("<result milisecs=\"" + fullTime + "\">" + responseMessage.toString()
				+ "</result>", response);
	}
}
