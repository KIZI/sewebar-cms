package xquerysearch.dao;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.ArrayList;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;

import xquerysearch.logging.event.EventLogger;

/**
 * Default implementation of {@link StoredQueryDao}.
 * 
 * @author Tomas Marek
 *
 */
public class StoredQueryDaoImpl implements StoredQueryDao {

	@Autowired
	private EventLogger logger; 
	
	@Value("${dir.queries}")
	private String queriesDirectory;
	/*
	 * @{InheritDoc}
	 */
	public String getQueryById(String queryId) {
		FileReader rdr = null;
        BufferedReader out = null;
        File file = new File(queriesDirectory + queryId + ".txt");
        if (file.exists()) {
            try {
            	String query = "";
        		rdr = new FileReader(file);
                out = new BufferedReader(rdr);
                String radek = out.readLine();
                while (radek != null){
                        query += radek + "\n";
                        radek = out.readLine();
                }
                out.close();
                return query;
            } catch (FileNotFoundException e) {
            	logger.logWarning(this.getClass().toString(), "Getting query with id \"" + queryId + "\" failed! - File not found");
            	return null;
            } catch (IOException e) {
            	logger.logWarning(this.getClass().toString(), "Getting query with id \"" + queryId + "\" failed! - IO exception");
            	return null;
            }
        }
        else {
                return null;
        }
	}

	/*
	 * @{InheritDoc}
	 */
	public boolean insertQuery(String queryId, String queryBody) {
		File file = new File(queriesDirectory + queryId + ".txt");
        if (file.exists()) {
        	return false;
        } else {
        	try {
	            FileOutputStream fos = new FileOutputStream(file);
	            OutputStreamWriter osw = new OutputStreamWriter(fos);
	            osw.write(queryBody);
	            osw.close();
	            return true;
//	            return "<message>New query with id \"" + queryId + "\" added!</message>";
        	} catch (IOException e) {
        		return false;
//        		logger.warning("Adding new query failed! - IO exception");
//        		return "<error>Adding new query failed!</error>";
			}
        }
	}

	/*
	 * @{InheritDoc}
	 */
	public boolean removeQuery(String queryId) {
		File file = new File(queriesDirectory + queryId + ".txt");

        if (file.exists()) {
            file.delete();
            return true; 
        } else {
        	return false;
        }
	}

	/*
	 * @{InheritDoc}
	 */
	public List<String> getNames() {
		List<String> queries = new ArrayList<String>();
        File uploadFolder = new File(queriesDirectory);
        File uploadFiles[] = uploadFolder.listFiles();

        for(int i = 0; i < uploadFiles.length; i++){
            if (uploadFiles[i].isFile()) {
                String fileName = uploadFiles[i].getName();
                String nameParts[] = fileName.split("\\.");
                String outputName = "";
                if (nameParts[nameParts.length-1].toLowerCase().equals("txt")){
                    for (int a = 0; a < nameParts.length-1; a++){
                        outputName += nameParts[a];
                    }
                    queries.add(outputName);
                }
            }
        }
        return queries;
    }

}
