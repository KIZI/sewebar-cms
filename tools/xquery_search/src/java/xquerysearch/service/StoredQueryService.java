package xquerysearch.service;

import java.io.File;
import java.util.List;

import xquerysearch.dao.StoredQueryDao;
import xquerysearch.dao.StoredQueryDaoImpl;
import xquerysearch.domain.StoredQuery;
import xquerysearch.settings.SettingsManager;

/**
 * Service for operations with {@link StoredQuery}.
 * 
 * @author Tomas Marek
 *
 */
public class StoredQueryService {
	
	private StoredQueryDao dao;
	
	/**
	 * 
	 */
	public StoredQueryService(SettingsManager settings) {
		dao = new StoredQueryDaoImpl(settings);
	}
	
	/**
     * Metoda pro ulozeni query
     * @param query ukladana query
     * @param id nazev ukladane query
     * @return zprava o ulozeni / chybe
     */
    public String addQuery(String queryBody, String queryId) {
        boolean added = dao.insertQuery(queryId, queryBody);
        
        if (added) {
        	return "<message>New query with id \"" + queryId + "\" added!</message>";
        } else {
        	return "<error>Adding new query failed!</error>";
        }
    }

    /**
     * 
     * @return queries names like {@code <query>queryName</query>}.
     */
    public String getQueriesNames() {
        List<String> queriesNames = dao.getNames();
    	String output = "";
        
        for (String name : queriesNames) { 
            output += "<query>" + name + "</query>";
        }
        return output;
    }

    /**
     * 
     * @param queryId
     * @return message with result of deleting - successful like {@code <message>message</message>}, unsuccessful like {@code <error>message</error>}.
     */
    public String deleteQuery (String queryId) {
        boolean removed = dao.removeQuery(queryId);

        if (removed) {
            return "<message>Query " + queryId + " smazana!</message>";
        } else {
            return "<error>Query neexistuje!</error>";
        }
    }

    /**
     * 
     * @param id id of query to get
     * @return query found by given id, <code>null</code> if error occurs
     */
    public String getQuery(String queryId) {
        return dao.getQueryById(queryId);
    }

}
