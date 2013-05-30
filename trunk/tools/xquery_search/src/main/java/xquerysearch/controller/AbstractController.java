package xquerysearch.controller;

import java.io.IOException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;

import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.logging.event.EventLogger;

/**
 * Abstract class for controllers. Contains some useful methods and logger.
 * 
 * @author Tomas Marek
 *
 */
abstract class AbstractController {
	
	@Autowired
	private EventLogger logger;
	
	/**
	 * Sets response encoding to UTF-8, get writer from response and prints content.
	 * @param content content to print
	 * @param response
	 */
	public void addResponseContent(String content, HttpServletResponse response) {
		try {
			response.setCharacterEncoding("UTF-8");
			response.setContentType("text/xml;charset=UTF-8");
			
			PrintWriter writer = response.getWriter();
			writer.print("<response>" + content + logger.getLog() + "</response>");
			
		} catch (UnsupportedEncodingException e) {
			logger.logInfo(this.getClass().toString(), "Unsuported encoding in response content!");
		} catch (IOException e) {
			logger.logInfo(this.getClass().toString(), "I/O exception in response content!");
		}
	}

}
