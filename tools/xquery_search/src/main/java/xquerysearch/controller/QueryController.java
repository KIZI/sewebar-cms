package xquerysearch.controller;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;

import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.service.QueryServiceImpl;

/**
 * Controller for querying.
 * 
 * @author Tomas Marek
 * 
 */
@Controller
public class QueryController extends AbstractController {

	@Autowired
	private QueryServiceImpl queryService;

	// TODO rename action in jsp
	@RequestMapping(params = "action=useQuery", method = RequestMethod.POST)
	public ModelAndView queryForResult(@RequestParam String content, HttpServletRequest request,
			HttpServletResponse response) {
		if (content.isEmpty()) {
			addResponseContent("<error>Query content has to be entered!</error>", response);
			return null;
		}
		ResultSet resultSet = queryService.getResultSet(content);
		StringBuffer responseMessage = new StringBuffer();
		if (resultSet != null) {
			for (Result result : resultSet.getResults()) {
				responseMessage.append(result.toString());
			}
		}
		addResponseContent("<result>" + responseMessage.toString() + "</result>", response);
		return null;
	}

}
