package xquerysearch.settings;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.logging.Logger;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

import xquerysearch.controllers.MainController;

/**
 * Class for reading application settings stored in XML
 * @author Tomas Marek
 */
public class SettingsUtils {

	private static Logger logger = MainController.getLogger();
	
    /**
     * Method for reading settings
     * @param xmlFile XML settings file
     * @return settings manager
     */
    public SettingsManager readSettings(File xmlFile) {
        
    		SettingsManager setMan = new SettingsManager(); 
    	
    		Document doc = null;
    		
    		try {
	            DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
	            DocumentBuilder db = dbf.newDocumentBuilder();
	            doc = db.parse(xmlFile);
	            doc.getDocumentElement().normalize();
    		} catch (ParserConfigurationException e) {
    			logger.warning("Settings reading failed! - Parser configuration exception");
    		} catch (IOException e) {
    			logger.warning("Settings reading failed! - IO exception");
    		} catch (SAXException e) {
    			logger.warning("Settings reading failed! - SAX exception");
    		}

            setMan.setEnvironmentDirectory(getSettingNode("envDir", doc));
            setMan.setQueriesDirectory(getSettingNode("queryDir", doc));
            setMan.setContainerName(getSettingNode("containerName", doc));
            setMan.setUseTransformation(Boolean.valueOf(getSettingNode("useTransformation", doc)));
            setMan.setPmmlTransformationPath(getSettingNode("transformationPathPMML", doc));
            setMan.setBkefTransformationPath(getSettingNode("transformationPathBKEF", doc));
            setMan.setTemporaryDirectory(getSettingNode("tempDir", doc));
            setMan.setValidationSchemaPath(getSettingNode("schemaPath", doc));
            
            logger.info("Settings reading done");
            return setMan;
    }

    /**
     * Method for writing settings into XML file 
     * @param xmlFile settings file
     * @param settings settings
     */
    public static void writeSettings(File xmlFile, SettingsManager setMan) {
        String output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
            + "\n<settings>"
                + "\n\t<envDir>" + setMan.getEnvironmentDirectory() + "</envDir>"
                + "\n\t<queryDir>" + setMan.getQueriesDirectory() + "</queryDir>"
                + "\n\t<containerName>" + setMan.getContainerName() + "</containerName>"
                + "\n\t<useTransformation>" + String.valueOf(setMan.isUseTransformation()) + "</useTransformation> <!-- true / false -->"
                + "\n\t<transformationPathPMML>" + setMan.getPmmlTransformationPath() + "</transformationPathPMML>"
                + "\n\t<transformationPathBKEF>" + setMan.getBkefTransformationPath() + "</transformationPathBKEF>"
                + "\n\t<tempDir>" + setMan.getTemporaryDirectory() + "</tempDir>"
                + "\n\t<schemaPath>" + setMan.getBkefTransformationPath() + "</schemaPath>"
            + "\n</settings>";
        try {
	        FileOutputStream fos = new FileOutputStream(xmlFile);
	        OutputStreamWriter osw = new OutputStreamWriter(fos);
	        osw.write(output);
	        osw.close();
	        fos.close();
	        logger.info("Settings successfully saved!");
        } catch (IOException e) {
        	
        }
    }
    
    /**
     * Method for reading single setting node
     * @param nodeName setting node name
     * @param doc parsed document
     * @return setting value if found, else <code>NULL</code>
     */
    private String getSettingNode(String nodeName, Document doc) {
    	try {
            NodeList topNodeList = doc.getElementsByTagName(nodeName);
            Element topElement = (Element)topNodeList.item(0);
            NodeList nodeList = topElement.getChildNodes();
            Node node = nodeList.item(0);
 
//        	CommunicationManager.logger.info("Setting " + nodeName + " reading successful!");
        	return (node.getNodeValue());
        } catch (NullPointerException e) {
        	logger.info("Setting " + nodeName + " reading FAILED!");
        	return null;
        }
            
    }
    
}
