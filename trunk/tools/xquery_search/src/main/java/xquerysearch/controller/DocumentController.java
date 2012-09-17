package xquerysearch.controller;

import java.util.List;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;

import xquerysearch.dao.AggregationDao;
import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.Document;

/**
 * Controller for operations with {@link Document}s.
 * 
 * @author Tomas Marek
 *
 */
@Controller
public class DocumentController extends AbstractController {

	@Autowired
	private DocumentDao documentDao;
	
	@Autowired
	private AggregationDao helperDao;
	
	@RequestMapping(params = "action=getDocsNames", method = RequestMethod.POST)
	public void showAllDocuments (HttpServletRequest request, HttpServletResponse response) {
		List<String> names = helperDao.getAllDocumentsNames();
		StringBuffer namesToResponse = new StringBuffer();
		namesToResponse.append("<result>");
		for (String name : names) {
			namesToResponse.append("<document>" + name + "</document>");
		}
		namesToResponse.append("</result>");
		addResponseContent(namesToResponse.toString(), response);
	}
	
	@RequestMapping(params = "action=getDocument", method = RequestMethod.POST)
	public void getDocument(@RequestParam String id, HttpServletRequest request, HttpServletResponse response) {
		Document document = documentDao.getDocumentById(id);
		addResponseContent(document.getDocBody(), response);
	}
}
