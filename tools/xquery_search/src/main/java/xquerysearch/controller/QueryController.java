package xquerysearch.controller;

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

import xquerysearch.domain.Query;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.service.QueryService;
import xquerysearch.transformer.QueryObjectTransformer;

/**
 * Controller for querying.
 * 
 * @author Tomas Marek
 * 
 */
@Controller
public class QueryController extends AbstractController {

	@Autowired
	@Qualifier("arbQueryCastor")
	private CastorMarshaller arbQueryCastor;
	
	@Autowired
	private QueryService queryService;

	// TODO rename action in jsp
	@RequestMapping(params = "action=useQuery", method = RequestMethod.POST)
	public ModelAndView queryForResult(@RequestParam String content, HttpServletRequest request,
			HttpServletResponse response) {
		if (content.isEmpty()) {
			addResponseContent("<error>Query content has to be entered!</error>", response);
			return null;
		}
		ArBuilderQuery arbQuery = QueryObjectTransformer.transform(arbQueryCastor, content);
		System.out.println("ANTE: " + arbQuery.getArQuery().getAntecedentSetting());
		System.out.println("CONS: " + arbQuery.getArQuery().getConsequentSetting());

		Query query = new Query(content);
		ResultSet resultSet = queryService.getResultSet(query);
		StringBuffer responseMessage = new StringBuffer();
		if (resultSet != null) {
			for (Result result : resultSet.getResults()) {
				// responseMessage.append(result.getResultBody());
			}
		}
		addResponseContent(responseMessage.toString(), response);
		return null;
	}

}
