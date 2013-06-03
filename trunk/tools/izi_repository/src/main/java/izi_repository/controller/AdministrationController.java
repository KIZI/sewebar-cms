package izi_repository.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.servlet.ModelAndView;

/**
 * Controller for administration.
 * 
 * @author Tomas Marek
 *
 */
@Controller
@RequestMapping("/admin")
public class AdministrationController {

	@RequestMapping("/index.do")
	public ModelAndView showPage() {
		System.out.println("Administration");
		return new ModelAndView();
	}
}
