package xquerysearch.controller;

import java.io.IOException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;

import javax.servlet.http.HttpServletResponse;

import org.apache.log4j.Logger;

/**
 * Abstract class for controllers. Contains some useful methods and logger.
 * 
 * @author Tomas Marek
 *
 */
public abstract class AbstractController {
	
	protected final static Logger logger = Logger.getLogger("controller");
	
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
			writer.print(content);
			
		} catch (UnsupportedEncodingException e) {
			logger.info("Unsuported encoding in response content!");
		} catch (IOException e) {
			logger.info("I/O exception in response content!");
		}
	}

}
