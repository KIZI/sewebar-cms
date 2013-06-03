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

import xquerysearch.service.AggregationService;
import xquerysearch.service.IndexService;

/**
 * Controller for operations with indexes.
 * 
 * @author Tomas Marek
 * 
 */
@Controller
public class IndexController extends AbstractController {

	@Autowired
	private AggregationService aggregationService;

	@Autowired
	private IndexService indexService;

	@RequestMapping(params = "action=listIndexes", method = RequestMethod.POST)
	public ModelAndView showIndexes(HttpServletRequest request, HttpServletResponse response) {
		List<String[]> indexes = aggregationService.getAllIndexes();
		StringBuffer indexesToResponse = new StringBuffer();
		indexesToResponse.append("<result>");
		for (String[] index : indexes) {
			indexesToResponse.append("<index><name>" + index[0] + "</name><content>" + index[1]
					+ "</content></index>");
		}
		indexesToResponse.append("</result>");
		addResponseContent(indexesToResponse.toString(), response);
		return null;
	}

	@RequestMapping(params = "action=addIndex", method = RequestMethod.POST)
	public ModelAndView addIndex(@RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		if (content.isEmpty()) {
			addResponseContent("<error>Index has to be specified!</error>", response);
			return null;
		}
		Boolean wasInserted = indexService.insertIndex(content);
		String message = "<message>Index " + content + " inserted!</message>";
		if (wasInserted == false) {
			message = "<error>Insertion of index " + content + " failed!</error>";
		}
		addResponseContent(message, response);
		return null;
	}

	@RequestMapping(params = "action=delIndex", method = RequestMethod.POST)
	public ModelAndView delIndex(@RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		if (content.isEmpty()) {
			addResponseContent("<error>Index has to be specified!</error>", response);
			return null;
		}
		Boolean wasRemoved = indexService.removeIndex(content);
		String message = "<message>Index " + content + " removed!</message>";
		if (wasRemoved == false) {
			message = "<error>Removal of index " + content + " failed!</error>";
		}
		addResponseContent(message, response);
		return null;
	}

}
