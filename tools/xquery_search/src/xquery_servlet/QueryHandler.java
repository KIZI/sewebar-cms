package xquery_servlet;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.OutputStreamWriter;

/**
 * Trida umoznuje praci s dotazy - ukladani, mazani atd.
 * @author Tomas Marek
 */
public class QueryHandler {
    String queryDir;
    
    /**
     * Konstruktor instance tridy
     * @param queryDir slozka obsahujici ulozene query
     */
    public QueryHandler(String queryDir) {
        this.queryDir = queryDir;
    }
    
    /**
     * Metoda pro ulozeni query
     * @param query ukladana query
     * @param id nazev ukladane query
     * @return zprava o ulozeni / chybe
     */
    public String addQuery(String query, String id){
        File file = new File(queryDir + id + ".txt");
        String output = "";
        try {
                if (file.exists()) {
                output = "<error>Query jiz existuje!</error>";
                }
                else {
                        FileOutputStream fos = new FileOutputStream(file);
                        OutputStreamWriter osw = new OutputStreamWriter(fos);
                        osw.write(query);
                        osw.close();
                output = "<message>Query " + id + " ulozena!</message>";
                }
        } catch (IOException e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro ziskani nazvu ulozenych XQuery
     * @return seznam ulozenych XQuery
     */
    public String getQueriesNames(){
        String output = "";
        File uploadFolder = new File(queryDir);
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
                    output += "<query>" + outputName + "</query>";
                }
            }
        }
        return output;
    }

    /**
     * Metoda pro vymazani ulozene XQuery
     * @param id ID ulozene XQuery
     * @return zprava - vymazana/nenalezena
     */
    public String deleteQuery (String id) {
        String output = "";
        File file = new File(queryDir + id + ".txt");

        if (file.exists()) {
                file.delete();
                output = "<message>Query " + id + " smazana!</message>";
        }
        else {
                output = "<error>Query neexistuje!</error>";
        }
        return output;
    }

    /**
     * Metoda pro ziskani ulozene XQuery
     * @param id ID ulozene XQuery
     * @return vracena XQuery/Zprava - nenalezena
     */
    public String[] getQuery(String id){
        FileReader rdr = null;
        BufferedReader out = null;
        File file = new File(queryDir + id + ".txt");
        String output[] = new String[2];
        output[1] = "";
        try {
                if (file.exists()) {
                        rdr = new FileReader(file);
                        out = new BufferedReader(rdr);
                        String radek = out.readLine();
                        while (radek != null){
                                output[1] += radek + "\n";
                                radek = out.readLine();
                        }
                        output[0] = "0";
                        out.close();
                }
                else {
                        output[1] = "<error>Query neexistuje!</error>";
                        output[0] = "1";
                }
        } catch (IOException e) {
                output[1] = "<error>"+e.toString()+"</error>";
                output[0] = "1";
        }
        return output;
    }
}
