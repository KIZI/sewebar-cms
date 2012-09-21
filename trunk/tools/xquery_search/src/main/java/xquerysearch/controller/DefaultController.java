package xquerysearch.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.servlet.ModelAndView;

/**
 * Controller for default page.
 * 
 * @author Tomas Marek
 *
 */

@Controller
public class DefaultController {
	
	@RequestMapping(value = "/", method = RequestMethod.GET)
	public ModelAndView getDefaultPage() {
		return new ModelAndView("defaultPage");
	}

	@RequestMapping(value = "style.css", method = RequestMethod.GET)
	public ModelAndView getStyle() {
		System.out.println("Give me style called");
		return null;
	}
}
