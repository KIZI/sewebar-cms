package xquerysearch.utils;

/**
 * Utilities for stored query processing.
 * 
 * @author Tomas Marek
 *
 */
public class StoredQueryUtils {
	
	/**
     * Provides removal of XML file declaration and oxygen declaration.
     * @param queryBody query body to process
     * @return processed query body when successful, otherwise <code>null</code>
     */
    public static String deleteDeclaration(String queryBody) {
        String output = "";
        String splitXMLBegin[] = queryBody.split("([<][?][x][m][l])|([<][?][o][x][y][g][e][n])");
        if (splitXMLBegin.length == 1) {
            output = queryBody;
        } else {
            for (int i = 0; i <= (splitXMLBegin.length - 1); i++) {
                if (i == 0) {
                    output += splitXMLBegin[i];
                } else {
                    String splitXMLEnd[] = splitXMLBegin[i].split("[?][>]");
                    if (splitXMLEnd.length > 1) {
                        String splitXMLBack = splitXMLEnd[1];
                        output += splitXMLBack;
                    }
                }
            }
        }
        return output;
    }

}
