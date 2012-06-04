package xquerysearch.controllers;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.Document;

/**
 * Controller for operations with {@link Document}s.
 * 
 * @author Tomas Marek
 *
 */
@Controller
public class DocumentController {

	private DocumentDao dao;
	
	@Autowired
	public void setDocumentDao(DocumentDao documentDao) {
		this.dao = documentDao;
	}
	
	@RequestMapping("/getdocs")
	public ModelAndView showAllDocuments () {
		ModelAndView modelAndView = new ModelAndView();
		modelAndView.setViewName("/views/standardView.jsp");
		return null;
	}
}
