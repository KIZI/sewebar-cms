package xquerysearch.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.servlet.ModelAndView;

import xquerysearch.domain.Document;

/**
 * Controller for operations with {@link Document}s.
 * 
 * @author Tomas Marek
 *
 */
@Controller
public class DocumentController {

	@RequestMapping("/getdocs")
	public ModelAndView showAllDocuments () {
		System.out.println("Get docs called!");
		ModelAndView modelAndView = new ModelAndView("test");
		return modelAndView;
	}
}
