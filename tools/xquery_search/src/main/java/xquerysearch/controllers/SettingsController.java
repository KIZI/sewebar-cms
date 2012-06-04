package xquerysearch.controllers;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.servlet.ModelAndView;

import xquerysearch.domain.Settings;

@Controller
@RequestMapping("/settings.do")
public class SettingsController {
	
	/**
	 * Builds settings page
	 * @param settings
	 * @return settings page as <code>HTML</code>
	 */
	
	public ModelAndView createSettingsPage(Settings settings) {
		return  null;
	}

}
