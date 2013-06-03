package xquerysearch.service;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.logging.event.EventLogger;

/**
 * Abstract service - parent child for all services.
 * 
 * @author Tomas Marek
 * 
 */
public class AbstractService {

	@Autowired
	protected EventLogger logger;
}
