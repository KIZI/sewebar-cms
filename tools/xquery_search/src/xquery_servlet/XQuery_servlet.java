package xquery_servlet;

import com.sleepycat.db.Environment;
import com.sleepycat.db.EnvironmentConfig;
import com.sleepycat.db.LockDetectMode;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlManagerConfig;
import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.io.StringWriter;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/**
 * Trida pro zpracovani vstupnich pozadavku a vraceni vysledku
 * @author Tomas Marek
 * @version 1.17 (8.9.2011)
 */
public class XQuery_servlet extends HttpServlet {

    /**
	 * 
	 */
	private static final long serialVersionUID = 1L;


	/**
     * Metoda zpracovavajici vstup a vytvarejici vystup. Podporuje <code>GET</code> a <code>POST</code> metody.
     * @param request prijaty pozadavek
     * @param response vytvorena odpoved (vystup)
     * @throws ServletException chyba tykajici se servletu
     * @throws IOException I/O chyba
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        /*
         * Nastaveni zpusobu a kodovani vystupu a vstupu,
         * inicializace promennych pro vystup
         */
        response.setCharacterEncoding("UTF-8");
        request.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();
        String output = "";
        double time_start = System.currentTimeMillis();        
        
    	// Nacteni nastaveni z konfiguracniho souboru
        XMLSettingsReader xmlSettings = new XMLSettingsReader();
        String getSettings[] = getSettingsFile();
        if (getSettings[0].toString().equals("1")) {
            output += getSettings[2].toString();
        } else {
            if (request.getParameter("action").toString().toLowerCase().equals("writesettings")) {
                changeSettings(xmlSettings, new File(getSettings[1].toString()), request);
                output += "<message>Nastaveni zmeneno</message>";
            } else
            {
                String[] settings = xmlSettings.readSettings(new File(getSettings[1].toString()));

                /* Popis vracenych poli
                 * 0 - error messages
                 * 1 - envDir
                 * 2 - queryDir
                 * 3 - containerName
                 * 4 - useTransformation
                 * 5 - xsltPath
                 * 6 - tempDir
                 * 7 - schemaPath
                 */
                String settingsError = settings[0];
                String envDir = settings[1];
                String queryDir = settings[2];
                String containerName = settings[3];
                String useTransformation = settings[4];
                String xsltPathPMML = settings[5];
                String xsltPathBKEF = settings[6];
                String tempDir = settings[7];
                String schemaPath = settings[8];

                if (settingsError != null) {
                        output += "<error><![CDATA[Error: " + settingsError + "]]></error>";
                } else {
                        try {
                        // Parametr action neni vyplnen => error, jinak naplneni promennych a odeslani ke zpracovani
                        if (request.getParameter("action").equals("")){
                                output += "<error><![CDATA[Error: Parametr akce neni vyplnen!]]></error>";
                        } else {
                            String action = request.getParameter("action").toString().toLowerCase();
                            if (action.equals("showsettings")) {
                                    output += createSettingsPage(settings);
                            } else {
                                // Vytvoreni spojeni s BDB XML
                                Environment env = createEnvironment(envDir, false);
                                XmlManagerConfig mconfig = new XmlManagerConfig();
                                mconfig.setAllowExternalAccess(true);
                                XmlManager mgr = new XmlManager(env, mconfig);

                                // Vytvoreni instanci trid QueryHandler, BDBXMLHandler a Tester
                                QueryHandler qh = new QueryHandler(queryDir);
                                SchemaChecker sc = new SchemaChecker(schemaPath);
                                BDBXMLHandler bh = new BDBXMLHandler(mgr, qh, sc, containerName, useTransformation, xsltPathPMML, xsltPathBKEF);
                                QueryMaker qm = new QueryMaker(containerName);
                                Tester tester = new Tester(qh, bh, mgr, envDir, queryDir, containerName, useTransformation, xsltPathPMML, xsltPathBKEF, tempDir, settingsError);

                                String id = request.getParameter("id").toString();
                                String content = request.getParameter("content").toString();
                                String docName = "";
                                String creationTime = "";
                                String reportUri = "";
                                boolean restructure = false;
                                if (request.getParameter("docName") != null) {
                                    docName = request.getParameter("docName").toString();
                                }
                                if (request.getParameter("creationTime") != null) {
                                    creationTime = request.getParameter("creationTime").toString();
                                }
                                if (request.getParameter("reportUri") != null) {
                                    reportUri = request.getParameter("reportUri").toString();
                                }
                                if (request.getParameter("restructure") != null) {
                                    if (request.getParameter("restructure").toString().toLowerCase().equals("true")){
                                        restructure = true;
                                    }
                                }
                                output += processRequest(action, id, docName, creationTime, reportUri, restructure, content, mgr, qh, bh, qm, tester);


                                // Ukonceni spojeni s BDB XML a vycisteni
                                if (mgr != null) {
                                    mgr.close();
                                }
                            }
                        }
                }
                catch (Throwable ex) {
                    StringWriter sw = new StringWriter();
                    ex.printStackTrace(new PrintWriter(sw));
                    output += "<error><![Error: " + sw.toString() +"]]></error>";
                    }
                }
            }
        }
        // Vypocet doby zpracovani,
        // vytvoreni a odeslani vystupu
        double time_end = System.currentTimeMillis();
        String cas = Double.toString(((time_end - time_start)));

        // Pokud je pozadavek na zobrazeni dokumentu -> nepridava se XML deklarace a obalovy element s casem
        if (request.getParameter("action").equals("showsettings")) {
        	response.setContentType("text/html;charset=UTF-8");
        	out.println(output);
        } else {
	        if (request.getParameter("action").equals("getDocument")) {
	        	response.setContentType("text/xml;charset=UTF-8");
	        	out.println(output);
	        } else {
	        	// Vypsani vystupu
	        	response.setContentType("text/xml;charset=UTF-8");
	            out.println("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	            out.println("<result milisecs=\"" + cas + "\">");
	            out.println(output);
	            out.println("</result>");
	        }
        }
    } 

    /** 
     * Metoda zpracovavajici HTTP <code>POST</code> metodu.
     * @param request dotaz na servlet
     * @param response odpoved servletu
     * @throws ServletException chyby tykajici se servletu
     * @throws IOException I/O chyby
     */
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        processRequest(request, response);
    }

    /** 
     * Metoda vraci kratky popis servletu
     * @return String s popisem servletu
     */
    @Override
    public String getServletInfo() {
        return "XQuery servlet slouzi ke komunikaci s Berkeley XML DB";
    }// </editor-fold>

    /**
     * Metoda vytvarejici prostredi pro spojeni s XML databazi
     * @param home umisteni DB
     * @param recover true/false pouziti recovery (standartne false)
     * @return nastavene prostredi pro spojeni s XML DB
     * @throws Throwable
     */
    private static Environment createEnvironment(String home, boolean recover)
    throws Throwable {
            EnvironmentConfig config = new EnvironmentConfig();
            config.setTransactional(true);
            config.setAllowCreate(true);
            config.setInitializeCache(true);
            config.setRunRecovery(recover);
            config.setCacheSize(128 * 1024 * 1024); // 128MB cache
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

    /**
      * Metoda slouzi k sestaveni stranky s nastavenim
      * @param settings pole nastaveni
      * @return html stranka s nastavenim
      */
    private String createSettingsPage(String[] settings){
    	String output = "";
        String TR = " selected=\"selected\"";
        String FL = "";
        if (settings[4] != null) {
            if (settings[4].toString().equals("false")) {
                TR = "";
                FL = " selected=\"selected\"";
            } else {
                TR = " selected=\"selected\"";
                FL = "";
            }
        }
    	output += 
            "\n<html><body>" +
            "\n<h2>Nastavení</h2> " +
                "\n<div id=\"settings\">" +
                "\n<table>" +
                "\n<form method=\"post\" action=\"xquery_servlet\">" +
                    "\n<input type=\"hidden\" name=\"action\" value=\"writesettings\">" +
                    "\n<tr><td>DB Environment directory:</td><td><input type=\"text\" name=\"envDir\" value=\""+ settings[1] +"\" size=\"100\"></td></tr>" +
                    "\n<tr><td>Query directory:</td><td><input type=\"text\" name=\"queryDir\" value=\""+ settings[2] +"\" size=\"100\"></td></tr>" +
                    "\n<tr><td>Container name:</td><td><input type=\"text\" name=\"containerName\" value=\""+ settings[3] +"\" size=\"100\"></td></tr>" +
                    "\n<tr><td>Use transformation:</td><td><select name=\"useTransformation\">" +
                        "\n<option value=\"true\""+TR+">True</option>" +
                        "\n<option value=\"false\""+FL+">False</option>" +
                    "\n</select></td></tr>" +
                    "\n<tr><td>XSLT path for PMML:</td><td><input type=\"text\" name=\"xsltPathPMML\" value=\""+ settings[5] +"\" size=\"100\"></td></tr>" +
                "\n<tr><td>XSLT path for BKEF:</td><td><input type=\"text\" name=\"xsltPathBKEF\" value=\""+ settings[6] +"\" size=\"100\"></td></tr>" +
                    "\n<tr><td>Temporary directory:</td><td><input type=\"text\" name=\"tempDir\" value=\""+ settings[7] +"\" size=\"100\"></td></tr>" +
                    "\n<tr><td>Schema Path:</td><td><input type=\"text\" name=\"schemaPath\" value=\""+ settings[8] +"\" size=\"100\"></td></tr>" +
                    "\n<tr><td></td><td><input type=\"submit\" value=\"Upravit nastaveni\"></td></tr>" +
                    "\n</form>" +
                    "\n</table>" +
                "\n</div></body></html>";
    	return output;
    }

    /**
     * Metoda pro nacteni dat z html formulare ze stranky s nastavenim a odeslani nastaveni k zapisu do souboru
     * @param sr instance tridy XMLSettingsReader
     * @param settingsFile soubor s nastavenim
     * @param request prijaty http request
     */
    private void changeSettings(XMLSettingsReader sr, File settingsFile, HttpServletRequest request){
        String[] settings = new String[8];
        settings[0] = request.getParameter("envDir").toString();
        settings[1] = request.getParameter("queryDir").toString();
        settings[2] = request.getParameter("containerName").toString();
        settings[3] = request.getParameter("useTransformation").toString();
        settings[4] = request.getParameter("xsltPathPMML").toString();
        settings[5] = request.getParameter("xsltPathBKEF").toString();
        settings[6] = request.getParameter("tempDir").toString();
        settings[7] = request.getParameter("schemaPath").toString();
        sr.writeSettings(settingsFile, settings);
    }
    
    /**
     * Metoda obsluhujici konfiguracni soubor
     * @return zprava / chyba
     */
    private String[] getSettingsFile(){
    	String output[] = new String[3];
    	output[0] = "0";
    	File settingFile = new File("xquery_search_settings.xml");
		try {
			if (!settingFile.exists()) {
				settingFile.createNewFile();
                                String newFileOutput = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
                                    + "\n<settings>"
                                        + "\n\t<envDir></envDir>"
                                        + "\n\t<queryDir></queryDir>"
                                        + "\n\t<containerName></containerName>"
                                        + "\n\t<useTransformation></useTransformation> <!-- true / false -->"
                                        + "\n\t<transformationPathPMML></transformationPathPMML>"
                                        + "\n\t<transformationPathBKEF></transformationPathBKEF>"
                                        + "\n\t<tempDir></tempDir>"
                                        + "\n\t<schemaPath></schemaPath>"
                                    + "\n</settings>";

                                FileOutputStream fos = new FileOutputStream(settingFile);
                                OutputStreamWriter osw = new OutputStreamWriter(fos);
                                osw.write(newFileOutput);
                                osw.close();
                                fos.close();
				output[0] = "1";
				output[1] = settingFile.getAbsolutePath(); 
				output[2] += "<message>Novy konfiguracni soubor vytvoren: " + settingFile.getAbsolutePath() + "</message>";
			} else {
				output[0] = "0";
				output[1] = settingFile.getAbsolutePath();
			}
		} catch (IOException e) {
			output[0] = "1";
			output[2] += "<error><![CDATA[" + e.toString() + "]]></error>";
		}
    	return output;
    }
    
    /**
     * Metoda pro namapovani nazvu akce na cislo -> jednodussi pouziti v rozhodovani, kterou metodu pouzit
     * @param action nazev akce
     * @return cislo akce
     */
    private static int mapAction (String action){
        /* Vypis nazvu metod z index.jsp
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
         - existquery (not used)
         - listin
         - delindex
         - getdescription
         - removealldocuments
         - jaxpquery
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
        if (action.equals("getdescription")) returnID = 18; else
        if (action.equals("removealldocuments")) returnID = 19; else
        if (action.equals("jaxpquery")) returnID = 20; else
        if (action.equals("actdescription")) returnID = 21;
        return returnID;
	}

	
    /**
     * Metoda provadejici rozbor vstupnich promennych,
     * nasledne vola jednotlive metody
     * @param action nazev akce, ktera se ma provest
     * @param id vetsinou ID (dokumentu/XQuery)
     * @param docName nazev dokumentu pro ulozeni v XMLDB
     * @param creationTime cas a datum vytvoreni dokumentu
     * @param reportUri URI adresa daneho dokumentu
     * @param restructure boolean hodnota urcujici, zda se ma provest restrukturalizace vysledku query
     * @param content vetsinou telo (dokumentu, XQuery, index)
     * @param mgr XmlManager instance XMLManager
     * @param qh instance tridy QueryHandler
     * @param bh instance tridy BDBXMLHandler
     * @param tester instance tridy Tester
     * @return predpripraveny vystup
     */
    private String processRequest(String action, String id, String docName, String creationTime, String reportUri, boolean restructure, String content, XmlManager mgr, QueryHandler qh, BDBXMLHandler bh, QueryMaker qm, Tester tester) throws IOException{
    	// Namapovani akce na cisla 
    	int mappedAction = mapAction(action);
        String output = "";
        // Pole cisel akci, ktere nepotrebuji zadne vstupy nebo pouze vstup content 
        int except[] = {2,3,7,8,10,13,14,16,17,18,19,20,21};

        Boolean except_bool = false;
        for (int i = 0; i < except.length; i++){
            if (except[i] == mappedAction) {
                except_bool = true;
            }
        }

        if (except_bool == false && id.isEmpty()) {
            output += "<error><![CDATA[Neni zadan parametr ID!]]></error>";
        } else {
        switch (mappedAction) {
            case 0: output += "<error><![CDATA[Zadana akce neexistuje]]></error>"; break;
            case 1: if (content.equals("")) {
                        output += "<error><![CDATA[Neni zadan obsah query]]></error>";
                    } else {
                        /*String dotaz = content.toString();
                        output += bh.query(id, dotaz, 1);*/
                        InputStream is1 = new ByteArrayInputStream(qh.queryPrepare(content).toByteArray());
                        InputStream is2 = new ByteArrayInputStream(qh.queryPrepare(content).toByteArray());
                        output += bh.queryShortened(qm.makeXPath(is1), restructure, qm.getMaxResults(is2));
                        //output += "<xpath><![CDATA["+ qm.makeXPath(is)+"]]></xpath>";
                        //output += qh.queryPrepare(content).toString();
                    } break;
            case 2: if (content.equals("")) {
                        output += "<error><![CDATA[Query nebyla zadana!]]></error>";
                    } else {
                        String dotaz = content.toString();
                        output += bh.query("", dotaz, 0);
                    } break;
            case 3: if (content.equals("")) {
                        output += "<error><![CDATA[Query nebyla zadana!]]></error>";
                    } else {
                        String dotaz = content.toString();
                        String message = bh.query_10(dotaz);
                        output += message.toString();
                    } break;
            case 4: if (content.equals("")) {
                        output += "<error><![CDATA[Neni zadan obsah query]]></error>";
                    } else {
                        content = content.toString();
                        output += qh.addQuery(content, id);
                    } break;
            case 5: output += "<query><![CDATA[" + qh.getQuery(id)[1].toString() + "]]></query>"; break;
            case 6: output += qh.deleteQuery(id); break;
            case 7: output += qh.getQueriesNames(); break;
            case 8: output += bh.getDocsNames(); break;
            case 9: if (content.equals("")) {
                        output += "<error><![CDATA[Neni zadan obsah dokumentu]]></error>";
                    } else if (docName.equals("")) {
                        output += "<error><![CDATA[Neni zadan nazev dokumentu]]></error>";
                    } else if (creationTime.equals("")) {
                        output += "<error><![CDATA[Neni zadan datum vytvoreni dokumentu]]></error>";
                    } else {
                        content = content.toString();
                        output += bh.indexDocument(content, id, docName, creationTime, reportUri);
                    } break;
            case 10: if (content.equals("")) {
                        output += "<error><![CDATA[Neni zadano umisteni slozky!]]></error>";
                    } else {
                        output += bh.indexDocumentMultiple(content); break;
                    } break;
            case 11: if (id == null){
                        output += "<error><![CDATA[Neni zadan nazev dokumentu]]></error>";
                    } else {
                        output += bh.getDocument(id);
                    } break;
            case 12: output += bh.removeDocument(id); break;
            case 13: if (content.equals("")){
                        output += "<error><![CDATA[Index nebyl zadan!]]></error>";
                    } else {
                        String dotaz = content.toString();
                        output += bh.addIndex(dotaz);
                    } break;
            case 14: output += tester.runTest(); break;
            case 15: break; //output += eh.test(content); break;
            case 16: output += bh.listIndex(); break;
            case 17: if (content.equals("")){
                        output += "<error><![CDATA[Index nebyl zadan!]]></error>";
                    } else {
                        String dotaz = content.toString();
                        output += bh.delIndex(dotaz);
                    } break;
            case 18: output += bh.getDataDescriptionCache(); break;
            case 19: output += /*bh.removeAllDocuments();*/"<not implemented yet/>"; break;
            case 20: /*if (content.equals("")) {
                        output += "<error><![CDATA[Nebyl zadan dotaz!]]></error>";
                    } else {
                        InputStream is = new ByteArrayInputStream(qh.queryPrepare(content).toByteArray());
                        output += bh.queryShortened(qm.makeXPath(is));
                    }*/ break;
            case 21: output += bh.actualizeDataDescriptionCache(); break;
            default: output += "<error><![CDATA[Zadana akce neexistuje]]></error>"; break;
            }
        }
	return (output);
    }
}