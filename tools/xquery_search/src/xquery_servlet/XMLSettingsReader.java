package xquery_servlet;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;
import org.xml.sax.SAXException;

/**
 * Trida obstaravajici cteni z XML souboru
 * @author Tomas Marek
 */
public class XMLSettingsReader {

    /**
     * Metoda provadejici cteni z XML dokumentu - ziskani nastaveni
     * @param xmlFile XML soubor s nastavenim
     * @return Jednotlive polozky nastaveni ulozene v poli stringu
     */
    public String[] readSettings(File xmlFile){
            String[] output = new String[8];
            try {
                DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
                DocumentBuilder db = dbf.newDocumentBuilder();
                Document doc = db.parse(xmlFile);
                doc.getDocumentElement().normalize();

                if (doc.getElementsByTagName("envDir") != null) {
                    NodeList envDirList = doc.getElementsByTagName("envDir");
                        if (envDirList.item(0) != null) {
                        Element envDirElement = (Element)envDirList.item(0);
                        NodeList envDir = envDirElement.getChildNodes();
                        Node envDirNode = envDir.item(0);
                        if (envDirNode == null) { output[1] = ""; }
                        else { output[1] = (envDirNode.getNodeValue()); }
                    } else { output[1] = ""; }
                } else { output[1] = ""; }
                
                
                if (doc.getElementsByTagName("queryDir") != null) {
                    NodeList queryDirList = doc.getElementsByTagName("queryDir");
                    if (queryDirList.item(0) != null) {
                        Element queryDirElement = (Element)queryDirList.item(0);
                        NodeList queryDir = queryDirElement.getChildNodes();
                        Node queryDirNode = queryDir.item(0);
                        if (queryDirNode == null) { output[2] = ""; }
                        else { output[2] = (queryDirNode.getNodeValue()); }
                    } else { output[2] = ""; }
                } else { output[2] = ""; }

                if (doc.getElementsByTagName("containerName") != null) {
                    NodeList contNameList = doc.getElementsByTagName("containerName");
                    if (contNameList.item(0) != null) {
                        Element contNameElement = (Element)contNameList.item(0);
                        NodeList contName = contNameElement.getChildNodes();
                        Node containerNameNode = contName.item(0);
                        if (containerNameNode == null) { output[3] = ""; }
                        else { output[3] = (containerNameNode.getNodeValue()); }
                    } else { output[3] = ""; }
                } else { output[3] = ""; }

                if (doc.getElementsByTagName("useTransformation") != null) {
                    NodeList useTransList = doc.getElementsByTagName("useTransformation");
                    if (useTransList.item(0) != null) {
                        Element useTransElement = (Element)useTransList.item(0);
                        NodeList useTrans = useTransElement.getChildNodes();
                        Node useTransNode = useTrans.item(0);
                        if (useTransNode == null) { output[4] = ""; }
                        else { output[4] = (useTransNode.getNodeValue()); }
                    } else { output[4] = ""; }
                } else { output[4] = ""; }

                if (doc.getElementsByTagName("transformationPath") != null) {
                    NodeList transPathList = doc.getElementsByTagName("transformationPath");
                    if (transPathList.item(0) != null) {
                        Element transPathElement = (Element)transPathList.item(0);
                        NodeList transPath = transPathElement.getChildNodes();
                        Node transPathNode = transPath.item(0);
                        if (transPathNode == null) { output[5] = ""; }
                        else { output[5] = (transPathNode.getNodeValue()); }
                    } else { output[5] = ""; }
                } else { output[5] = ""; }

                if (doc.getElementsByTagName("tempDir") != null) {
                    NodeList tempDirList = doc.getElementsByTagName("tempDir");
                    if (tempDirList.item(0) != null) {
                        Element tempDirElement = (Element)tempDirList.item(0);
                        NodeList tempDir = tempDirElement.getChildNodes();
                        Node tempDirNode = tempDir.item(0);
                        if (tempDirNode == null) { output[6] = ""; }
                        else { output[6] = (tempDirNode.getNodeValue()); }
                    } else { output[6] = ""; }
                } else { output[6] = ""; }

                
                if (doc.getElementsByTagName("schemaPath") != null) {
                    NodeList schemaPathList = doc.getElementsByTagName("schemaPath");
                    if (schemaPathList.item(0) != null) {
                        Element schemaPathElement = (Element)schemaPathList.item(0);
                        NodeList schemaPath = schemaPathElement.getChildNodes();
                        Node schemaPathNode = schemaPath.item(0);
                        if (schemaPathNode == null) { output[7] = ""; }
                        else { output[7] = (schemaPathNode.getNodeValue()); }
                    } else { output[7] = ""; }
                } else { output[7] = ""; }
        } catch (ParserConfigurationException e) {
                output[0] += ("ParserConfigurationException: " + e.toString() + "\n");
                //e.printStackTrace();
        } catch (SAXException e) {
                output[0] += ("SAXException: " + e.toString() + "\n");
                //e.printStackTrace();
        } catch (IOException e) {
                output[0] += ("IOException: " + e.toString() + "\n");
                //e.printStackTrace();
        }
        return output;
    }

    /**
     * Metoda zapisujici nastaveni do souboru s nastavenim
     * @param xmlFile soubor s nastavenim
     * @param settings pole nastaveni
     */
    public void writeSettings(File xmlFile, String settings[]){
        String output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
                + "\n<settings>"
                    + "\n\t<envDir>" + settings[0] + "</envDir>"
                    + "\n\t<queryDir>" + settings[1] + "</queryDir>"
                    + "\n\t<containerName>" + settings[2] + "</containerName>"
                    + "\n\t<useTransformation>" + settings[3] + "</useTransformation> <!-- true / false -->"
                    + "\n\t<transformationPath>" + settings[4] + "</transformationPath>"
                    + "\n\t<tempDir>" + settings[5] + "</tempDir>"
                    + "\n\t<schemaPath>" + settings[6] + "</schemaPath>"
                + "\n</settings>";
        try {
            FileOutputStream fos = new FileOutputStream(xmlFile);
            OutputStreamWriter osw = new OutputStreamWriter(fos);
            osw.write(output);
            osw.close();
            fos.close();
        } catch (FileNotFoundException ex) {
            output += "<error>" + ex.toString() + "</error>";
        } catch (IOException ex) {
            output += "<error>" + ex.toString() + "</error>";
        }
    }
}