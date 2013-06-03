package izi_repository.service;

import izi_repository.logging.event.EventLogger;

import org.springframework.beans.factory.annotation.Autowired;


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
