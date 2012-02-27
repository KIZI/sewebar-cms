package xquerysearch;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.util.logging.Logger;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import xquerysearch.settings.SettingsFileUtils;
import xquerysearch.settings.SettingsManager;
import xquerysearch.settings.SettingsPageController;
import xquerysearch.settings.SettingsUtils;

/**
 * Class for main communication to the outside world
 * 
 * @author Tomas Marek
 * @version 1.21 (5.1.2012)
 */
public class CommunicationManager extends HttpServlet {
	private final String ID_MISSING_ERROR = "<error><![CDATA[ID is missing!]]></error>";
	private final String QUERY_MISSING_ERROR = "<error><![CDATA[Query content is missing!]]></error>";
	private final String DOCUMENT_CONTENT_MISSING_ERROR = "<error><![CDATA[Document content is missing!]]></error>";
	private final String DOCUMENT_NAME_MISSING_ERROR = "<error><![CDATA[Document name is missing!]]></error>";
	private final String DOCUMENT_CREATIONTIME_MISSING_ERROR = "<error><![CDATA[Document creation time is missing!]]></error>";
	private final String ACTION_NOT_EXISTS_ERROR = "<error><![CDATA[Action does not exist!]]></error>";
	private final String INDEX_NOT_SPECIFIED_ERROR = "<error><![CDATA[Index is not specified!]]></error>";
	private final String FOLDER_PATH_MISSING_ERROR = "<error><![CDATA[Folder path is missing!]]></error>";
	
	
	public static final Logger logger = Logger.getLogger("xquery_search");
	public static final String SETTINGS_FILE_NAME = "xquery_search_settings.xml";
	private SettingsManager settings;
	private String action;
	private String content;
	private String docId;
	private HttpServletRequest request;

	/**
	 * Method managing requests and responses. Supports <code>GET</code> a <code>POST</code>.
	 * 
	 * @param request 
	 * @param response 
	 * @throws ServletException 
	 * @throws IOException
	 * @throws SAXException 
	 * @throws ParserConfigurationException 
	 */
	protected void processRequest(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		
		response.setCharacterEncoding("UTF-8");
		request.setCharacterEncoding("UTF-8");
		
		this.request = request;
		
		PrintWriter out = response.getWriter();
		String output = "";
		double time_start = System.currentTimeMillis();
		action = request.getParameter("action").toString().toLowerCase();

		// Nacteni nastaveni z konfiguracniho souboru
		
		SettingsUtils xmlSettings = new SettingsUtils();
		if (action.equals("writesettings")) {
			settings.changeSettings(SettingsFileUtils.getSettingsFile(), request);
			output += "<message>Settings updated</message>";
		} else {
			settings = xmlSettings.readSettings(SettingsFileUtils.getSettingsFile());

			try {
				if (request.getParameter("action").equals("")){
					output += "<error><![CDATA[Error: Parametr akce neni vyplnen!]]></error>";
				} else {
					if (action.equals("showsettings")) {
						output += SettingsPageController.createSettingsPage(settings);
					} else {
						docId = request.getParameter("id").toString();
						content = request.getParameter("content").toString();
						
						output += processRequest(action);
					}
				}
			} catch (Throwable ex) {
				StringWriter sw = new StringWriter();
				ex.printStackTrace(new PrintWriter(sw));
//						output += "<error><![Error: " + sw.toString()
//								+ "]]></error>";
				output += "<error>" + ex.toString() + "</error>";
				logger.severe(sw.toString());
			}
		}
		double time_end = System.currentTimeMillis();
		String cas = Double.toString(((time_end - time_start)));
		logger.info("Request: \""+ request.getParameter("action").toString() +"\" | Time spent: "+ cas + "ms");
		if (request.getParameter("action").equals("showsettings")) {
			response.setContentType("text/html;charset=UTF-8");
			out.println(output);
		} else {
			if (request.getParameter("action").equals("getDocument")) {
				response.setContentType("text/xml;charset=UTF-8");
				out.println(output);
			} else {
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
	protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		processRequest(request, response);
	}

	/**
	 * Metoda vraci kratky popis servletu
	 * @return String s popisem servletu
	 */
	@Override
	public String getServletInfo() {
		return "XQuery search provides searching PMML documents stored in Berkeley XML DB";
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
		if (action.equals("usequery")) returnID = 1;
		else if (action.equals("directquery")) returnID = 2;
		else if (action.equals("directquery10")) returnID = 3;
		else if (action.equals("addquery")) returnID = 4;
		else if (action.equals("getquery")) returnID = 5;
		else if (action.equals("deletequery")) returnID = 6;
		else if (action.equals("getqueriesnames")) returnID = 7;
		else if (action.equals("getdocsnames")) returnID = 8;
		else if (action.equals("adddocument")) returnID = 9;
		else if (action.equals("adddocumentmultiple")) returnID = 10;
		else if (action.equals("getdocument")) returnID = 11;
		else if (action.equals("deletedocument")) returnID = 12;
		else if (action.equals("addindex")) returnID = 13;
		else if (action.equals("completetest")) returnID = 14;
		else if (action.equals("existquery")) returnID = 15;
		else if (action.equals("listin")) returnID = 16;
		else if (action.equals("delindex")) returnID = 17;
		else if (action.equals("getdescription")) returnID = 18;
		else if (action.equals("removealldocuments")) returnID = 19;
		else if (action.equals("jaxpquery")) returnID = 20;
		else if (action.equals("actdescription")) returnID = 21;
		return returnID;
	}

	/**
	 * 
	 * @return
	 */
	private String processRequest(String action) {
		// actions -> codes mapping
		int mappedAction = mapAction(action);
		
		BDBXMLHandler bh = new BDBXMLHandler(settings);
		QueryHandler qh = new QueryHandler(settings);
		QueryMaker qm = new QueryMaker(settings);
		
		String output = "";
		// Pole cisel akci, ktere nepotrebuji zadne vstupy nebo pouze vstup content
		int except[] = { 2, 3, 7, 8, 10, 13, 14, 16, 17, 18, 19, 20, 21 };

		Boolean except_bool = false;
		for (int i = 0; i < except.length; i++) {
			if (except[i] == mappedAction) {
				except_bool = true;
			}
		}

		if (except_bool == false && docId.isEmpty()) {
			output += ID_MISSING_ERROR;
		} else {
			switch (mappedAction) {
			case 0: output += ACTION_NOT_EXISTS_ERROR; break;
			case 1:
				if (content.equals("")) {
					output += QUERY_MISSING_ERROR;
				} else {
					boolean restructure = false;
					if (request.getParameter("restructure") != null) {
						restructure = Boolean.valueOf(request.getParameter("restructure").toString().toLowerCase());
					}
				//  String dotaz = content.toString(); output += bh.query(id, dotaz, 1);
					InputStream is1 = new ByteArrayInputStream(qh.queryPrepare(content).toByteArray());
					InputStream is2 = new ByteArrayInputStream(qh.queryPrepare(content).toByteArray());
					InputStream is3 = new ByteArrayInputStream(qh.queryPrepare(content).toByteArray());
					String xpath[] = qm.makeXPath(is1);
					output += bh.queryShortened(xpath[0], restructure, Boolean.parseBoolean(xpath[1]), qm.getMaxResults(is2), is3);
					//output += "<xpath><![CDATA["+ xpath[0]+"]]></xpath><exception>" + xpath[1] + "</exception>";
					//output += qh.queryPrepare(content).toString();
				}
				break;
			case 2:
				if (content.equals("")) {
					output += QUERY_MISSING_ERROR;
				} else {
					String dotaz = content.toString();
					output += bh.query("", dotaz, true);
				}
				break;
			case 3:
				if (content.equals("")) {
					output += QUERY_MISSING_ERROR;
				} else {
					String dotaz = content.toString();
					String message = bh.query_10(dotaz);
					output += message.toString();
				}
				break;
			case 4:
				if (content.equals("")) {
					output += QUERY_MISSING_ERROR;
				} else {
					content = content.toString();
					output += qh.addQuery(content, docId);
				}
				break;
			case 5:
				output += "<query><![CDATA[" + qh.getQuery(docId) + "]]></query>";
				break;
			case 6: output += qh.deleteQuery(docId); break;
			case 7: output += qh.getQueriesNames(); break;
			case 8: output += bh.getDocsNames(); break;
			case 9:
				if (content.equals("")) {
					output += DOCUMENT_CONTENT_MISSING_ERROR;
				} else {
					String docName = "";
					String creationTime = "";
					String reportUri = "";
					
					if (request.getParameter("docName") != null) {
						docName = request.getParameter("docName").toString();
					}
					if (request.getParameter("creationTime") != null) {
						creationTime = request.getParameter("creationTime").toString();
					}
					if (request.getParameter("reportUri") != null) {
						reportUri = request.getParameter("reportUri").toString();
					}
					if (docName.equals("")) {
						output += DOCUMENT_NAME_MISSING_ERROR;
					} else if (creationTime.equals("")) {
						output += DOCUMENT_CREATIONTIME_MISSING_ERROR;
					} else {
						content = content.toString();
						output += bh.indexDocument(content, docId, docName, creationTime, reportUri);
					}
				}
				break;
			case 10:
				if (content.equals("")) {
					output += FOLDER_PATH_MISSING_ERROR;
				} else {
					output += bh.indexDocumentMultiple(content);
					break;
				}
				break;
			case 11:
				if (docId == null) {
					output += DOCUMENT_NAME_MISSING_ERROR;
				} else {
					output += bh.getDocument(docId);
				}
				break;
			case 12: output += bh.removeDocument(docId); break;
			case 13:
				if (content.equals("")) {
					output += INDEX_NOT_SPECIFIED_ERROR;
				} else {
					String dotaz = content.toString();
					output += bh.addIndex(dotaz);
				}
				break;
			case 14: break; // output += tester.runTest();
			case 15: break; // output += eh.test(content); break;
			case 16: output += bh.listIndex(); break;
			case 17:
				if (content.equals("")) {
					output += INDEX_NOT_SPECIFIED_ERROR;
				} else {
					String dotaz = content.toString();
					output += bh.removeIndex(dotaz);
				}
				break;
			case 18: output += bh.getDataDescriptionCache(); break;
			case 19: output += /* bh.removeAllDocuments(); */"<not implemented yet/>"; break;
			case 20: /*
					 * if (content.equals("")) { output +=
					 * "<error><![CDATA[Nebyl zadan dotaz!]]></error>"; } else {
					 * InputStream is = new
					 * ByteArrayInputStream(qh.queryPrepare(
					 * content).toByteArray()); output +=
					 * bh.queryShortened(qm.makeXPath(is)); }
					 */
				break;
			case 21: output += bh.actualizeDataDescriptionCache(); break;
			default: output += ACTION_NOT_EXISTS_ERROR; break;
			}
		}
		return (output);
	}
}