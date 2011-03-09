package xquery_servlet;

import com.sleepycat.db.Environment;
import com.sleepycat.db.EnvironmentConfig;
import com.sleepycat.db.LockDetectMode;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlManagerConfig;
import java.io.File;
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/**
 * Trida pro zpracovani vstupnich pozadavku a vraceni vysledku
 * @author Tomas Marek
 * @version 1.05 (26.2.2011)
 */
public class XQuery_servlet extends HttpServlet {

    XMLSettingsReader xmlSettings = new XMLSettingsReader();
    /*
     * 0 - envDir
     * 1 - queryDir
     * 2 - containerName
     * 3 - useTransformation
     * 4 - xsltPath
     * 5 - tempDir
     * 6 - error messages
     */

    String[] settings = xmlSettings.readSettings("c:/users/Tomas/Sewebar/dbxml_settings.xml");
    
    //String[] settings = xmlSettings.readSettings("/home/marek/dbxml_settings.xml");



    String envDir = settings[0];
    String queryDir = settings[1];
    String containerName = settings[2];
    String useTransformation = settings[3];
    String xsltPath = settings[4];
    String tempDir = settings[5];
    String settingsError = settings[6];

    //static FileInputStream xsltTrans = xsltPrepare(new File(xsltPath));

    /*static String envDir = "";
    static String queryDir = "";
    static String containerName = "";
    static String useTransformation = "";
    static String xsltPath = "";
    static String tempDir = "";
    static String settingsError = "";*/

    /**
     * Processes requests for both HTTP <code>GET</code> and <code>POST</code> methods.
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        /*
         * Nastaveni zpusobu a kodovani vystupu a vstupu,
         * inicializace promennych pro vystup
         */
        response.setContentType("text/xml;charset=UTF-8");
        response.setCharacterEncoding("UTF-8");
        request.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();
        String output = "";
        double time_start = System.currentTimeMillis();

        if (settingsError != null) {
                output += "<error>Trida: XQuery_servlet | Metoda: processRequest | Chyba: " + settingsError + "</error>";
        } else {
        try {


        // Vytvoreni spojeni s BDB XML

        Environment env = createEnvironment(envDir, false);
        XmlManagerConfig mconfig = new XmlManagerConfig();
        mconfig.setAllowExternalAccess(true);
        XmlManager mgr = new XmlManager(env, mconfig);


         // Parametr action neni vyplnen => error, jinak naplneni promennych
         // a odeslani ke zpracovani

        QueryHandler qh = new QueryHandler(queryDir);
        BDBXMLHandler bh = new BDBXMLHandler(mgr, qh, containerName, useTransformation, xsltPath);
        Tester tester = new Tester();
        ExistDBHandler eh = new ExistDBHandler();

        if (request.getParameter("action").equals("")){
                output += "<error>Trida: XQuery_servlet | Metoda: processRequest | Chyba: Parametr akce neni vyplnen!</error>";
        } else {
                String akce = request.getParameter("action").toString().toLowerCase();
                String promenna = request.getParameter("variable").toString();
                String obsah = request.getParameter("content").toString();

                output += processRequest(akce, promenna, obsah, mgr, qh, bh, eh, tester);
        }

        // Ukonceni spojeni s BDB XML a vycisteni
        mgr.close();
        mgr.delete();
        env.close();
    }
    catch (Throwable ex) {
        //Logger.getLogger(XQuery_servlet.class.getName()).log(Level.SEVERE, null, ex);
        //StringWriter sw = new StringWriter();
        //ex.printStackTrace(new PrintWriter(sw));
        output += "<error>Trida:  XQuery_servlet | Metoda: processRequest | Chyba: " + ex.toString() +"</error>";
        }
    }
        // Vypocet doby zpracovani,
        // vytvoreni a odeslani vystupu
        double time_end = System.currentTimeMillis();
        String cas = Double.toString(((time_end - time_start)));

        if (request.getParameter("action").equals("getDocument")) {
            out.println(output);
        } else {
            out.println("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
            out.println("<result milisecs=\"" + cas + "\">");
            out.println(output);
            out.println("</result>");
        }
    } 

    /** 
     * Handles the HTTP <code>POST</code> method.
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        processRequest(request, response);
    }

    /** 
     * Returns a short description of the servlet.
     * @return a String containing servlet description
     */
    @Override
    public String getServletInfo() {
        return "XQuery servlet slouzi ke komunikaci s Berkeley XML DB";
    }// </editor-fold>

    /*
     * Metoda pro vytvoreni spojujiciho prostredi pro BDB XML,
     * jeho nastaveni
     */
    private static Environment createEnvironment(String home, boolean recover)
    throws Throwable {
            EnvironmentConfig config = new EnvironmentConfig();
            config.setTransactional(true);
            config.setAllowCreate(true);
            config.setInitializeCache(true);
            config.setRunRecovery(recover);
            config.setCacheSize(32 * 1024 * 1024); // 32MB cache
            config.setInitializeLocking(true);
            config.setInitializeLogging(true);
            config.setErrorStream(System.err);
            config.setLockDetectMode(LockDetectMode.MINWRITE);
            config.setLogAutoRemove(true);
            config.setLockTimeout(3);
            config.setLogAutoRemove(true);
            File f = new File(home);
            return new Environment(f, config);
    }

    private static int mapAction (String action){
        /*
         - usequery
         - directquery
         - directquery10
         - addquery
         - getquery
         - deletequery
         - getqueriesnames
         - getdocsnames
         - adddocument
         - adddocumentmultiple
         - getdocument
         - deletedocument
         - addindex
         - completetest
         */
        int returnID = 0;
        if (action.equals("usequery")) returnID = 1; else
        if (action.equals("directquery")) returnID = 2; else
        if (action.equals("directquery10")) returnID = 3; else
        if (action.equals("addquery")) returnID = 4; else
        if (action.equals("getquery")) returnID = 5; else
        if (action.equals("deletequery")) returnID = 6; else
        if (action.equals("getqueriesnames")) returnID = 7; else
        if (action.equals("getdocsnames")) returnID = 8; else
        if (action.equals("adddocument")) returnID = 9; else
        if (action.equals("adddocumentmultiple")) returnID = 10; else
        if (action.equals("getdocument")) returnID = 11; else
        if (action.equals("deletedocument")) returnID = 12; else
        if (action.equals("addindex")) returnID = 13; else
        if (action.equals("completetest")) returnID = 14; else
        if (action.equals("existquery")) returnID = 15; else
        if (action.equals("listin")) returnID = 16; else
        if (action.equals("delindex")) returnID = 17; else
        if (action.equals("getdescription")) returnID = 18;
        return returnID;
	}

	
    /**
     * Metoda provadejici rozbor vstupnich promennych,
     * nasledne vola jednotlive metody
     * @param action Nazev akce, ktera se ma provest
     * @param variable Promenna - vetsinou ID (dokumentu/XQuery)
     * @param content Obsah - vetsinou telo (dokumentu, XQuery, index)
     * @param mgr XmlManager
     * @return Sestaveny vystup
     */
    private String processRequest(String action, String variable, String content, XmlManager mgr, QueryHandler qh, BDBXMLHandler bh, ExistDBHandler eh, Tester tester){
    	int mappedAction = mapAction(action);
        String output = "";
        int except[] = {2,7,8,10,13,14,16,17,18};

        Boolean except_bool = false;
        for (int i = 0; i < except.length; i++){
            if (except[i] == mappedAction) {
                except_bool = true;
            }
        }

        if (except_bool == false && variable.isEmpty()) {
            output += "<error>Neni zadan parametr ID!</error>";
        } else {
        switch (mappedAction) {
            case 0: output += "<error>Zadana akce neexistuje</error>"; break;
            case 1: if (content.equals("")) {
                        output += "<error>Neni zadan obsah query</error>";
                    } else {
                        String dotaz = content.toString();
                        String[] message = bh.query(variable, dotaz, 1);
                        output += message[1].toString();
                    } break;
            case 2: if (content.equals("")) {
                        output += "<error>Query nebyla zadana!</error>";
                    } else {
                        String dotaz = content.toString();
                        String[] message = bh.query("", dotaz, 0); 
                        output += message[1].toString();
                    } break;
            case 3: if (content.equals("")) {
                        output += "<error>Query nebyla zadana!</error>";
                    } else {
                        String dotaz = content.toString();
                        String message = bh.query_10(dotaz);
                        output += message.toString();
                    } break;
            case 4: if (content.equals("")) {
                        output += "<error>Neni zadan obsah query</error>";
                    } else {
                        content = content.toString();
                        output += qh.addQuery(content, variable);
                    } break;
            case 5: output += "<query><![CDATA[" + qh.getQuery(variable)[1].toString() + "]]></query>"; break;
            case 6: output += qh.deleteQuery(variable); break;
            case 7: output += qh.getQueriesNames(); break;
            case 8: output += bh.getDocsNames(); break;
            case 9: if (content.equals("")) {
                        output += "<error>Neni zadan obsah dokumentu</error>";
                    } else {
                        content = content.toString();
                        output += bh.indexDocument(content, variable);
                    } break;
            case 10: if (content.equals("")) {
                        output += "<error>Neni zadano umisteni slozky!</error>";
                    } else {
                        output += bh.indexDocumentMultiple(content); break;
                    } break;
            case 11: if (variable == null){
                        output += "<error>Neni zadan nazev dokumentu</error>";
                    } else {
                        output += bh.getDocument(variable);
                    } break;
            case 12: output += bh.removeDocument(variable); break;
            case 13: if (content.equals("")){
                        output += "<error>Index nebyl zadan!</error>";
                    } else {
                        String dotaz = content.toString();
                        output += bh.addIndex(dotaz);
                    } break;
            case 14: output += tester.runTest(qh, bh, mgr, envDir, queryDir, containerName, useTransformation, xsltPath, tempDir, settingsError); break;
            case 15: break; //output += eh.test(content); break;
            case 16: output += bh.listIndex(); break;
            case 17: if (content.equals("")){
                        output += "<error>Index nebyl zadan!</error>";
                    } else {
                        String dotaz = content.toString();
                        output += bh.delIndex(dotaz);
                    } break;
            case 18: output += bh.getDataDescription(); break;
            default: output += "<error>Zadana akce neexistuje</error>"; break;
            }
        }
	return (output);
    }
}