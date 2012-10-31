package xquerysearch.controller;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;

import xquerysearch.service.QueryProcessingService;

/**
 * Controller for querying.
 * 
 * @author Tomas Marek
 * 
 */
@Controller
public class QueryController extends AbstractController {

	@Autowired
	private QueryProcessingService queryProcessingService;

	// TODO rename action in jsp
	@RequestMapping(params = "action=useQuery", method = RequestMethod.POST)
	public ModelAndView queryForResult(@RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		if (content.isEmpty()) {
			addResponseContent("<error>Query content has to be entered!</error>", response);
			return null;
		}

		long startTime = System.currentTimeMillis();

		addResponseContent(queryProcessingService.processQuery(content, startTime), response);

		return null;
	}

	@RequestMapping(params = "action=directQuery", method = RequestMethod.POST)
	public ModelAndView directQuery(@RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		String results = queryProcessingService.processDirectQuery(content);

		addResponseContent("<result>" + results + "</result>", response);

		return null;
	}

}
