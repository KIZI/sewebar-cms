package xquerysearch.controller;

import java.util.List;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;

import xquerysearch.domain.result.Result;
import xquerysearch.service.AggregationService;
import xquerysearch.service.QueryService;
import xquerysearch.transformation.OutputTransformer;

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

	// TODO rename action in jsp
	@RequestMapping(params = "action=useQuery", method = RequestMethod.POST)
	public ModelAndView queryForResult(@RequestParam String content, HttpServletRequest request,
			HttpServletResponse response) {
		if (content.isEmpty()) {
			addResponseContent("<error>Query content has to be entered!</error>", response);
			return null;
		}

		long startTime = System.currentTimeMillis();

		// ResultSet resultSet = queryService.getResultSet(content);
		// StringBuffer responseMessage = new StringBuffer();
		// if (resultSet != null) {
		// for (Result result : resultSet.getResults()) {
		// responseMessage.append(result.toString());
		// }
		// }
		StringBuffer responseMessage = new StringBuffer();

		List<Result> results = queryService.getResultList(content);
		long queryTime = System.currentTimeMillis() - startTime;
		Long docCount = aggregationService.getDocumentsCount();
		Long arCount = aggregationService.getAssociationRulesCount();

		responseMessage.append(OutputTransformer
				.transformResultsInList(results, queryTime, docCount, arCount));
		long fullTime = System.currentTimeMillis() - startTime;
		addResponseContent("<result milisecs=\"" + fullTime + "\">" + responseMessage.toString()
				+ "</result>", response);
		return null;
	}
	
	@RequestMapping(params = "action=directQuery", method = RequestMethod.POST)
	public ModelAndView directQuery(@RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		String results = queryService.query(content);
		addResponseContent("<result>" + results + "</result>", response);
		return null;
	}

}
