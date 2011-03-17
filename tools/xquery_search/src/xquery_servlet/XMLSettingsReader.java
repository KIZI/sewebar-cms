package xquery_servlet;

import java.io.File;
import java.io.IOException;

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
     * Metoda pro vnejsi komunikaci
     * @param file XML soubor pro precteni
     * @return Pole stringu obsahujici jednotlive hodnoty ziskane z XML souboru
     */
    public String[] readSettings(String file){
            File settingsFile = new File(file);
            return readXMLFile(settingsFile);
    }

    /**
     * Metoda provadejici cteni z XML dokumentu - ziskani nastaveni
     * @param xmlFile XML soubor s nastavenim
     * @return Jednotlive polozky nastaveni ulozene v poli stringu
     */
    private static String[] readXMLFile(File xmlFile){
            String[] output = new String[7];
            try {
                DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
                DocumentBuilder db = dbf.newDocumentBuilder();
                Document doc = db.parse(xmlFile);
                doc.getDocumentElement().normalize();

                NodeList envDirList = doc.getElementsByTagName("envDir");
                Element envDirElement = (Element)envDirList.item(0);
                NodeList envDir = envDirElement.getChildNodes();
                output[0] = (((Node) envDir.item(0)).getNodeValue());

                NodeList queryDirList = doc.getElementsByTagName("queryDir");
                Element queryDirElement = (Element)queryDirList.item(0);
                NodeList queryDir = queryDirElement.getChildNodes();
                output[1] = (((Node) queryDir.item(0)).getNodeValue());

                NodeList contNameList = doc.getElementsByTagName("containerName");
                Element contNameElement = (Element)contNameList.item(0);
                NodeList contName = contNameElement.getChildNodes();
                output[2] = (((Node) contName.item(0)).getNodeValue());

                NodeList useTransList = doc.getElementsByTagName("useTransformation");
                Element useTransElement = (Element)useTransList.item(0);
                NodeList useTrans = useTransElement.getChildNodes();
                output[3] = (((Node) useTrans.item(0)).getNodeValue());

                NodeList transPathList = doc.getElementsByTagName("transformationPath");
                Element transPathElement = (Element)transPathList.item(0);
                NodeList transPath = transPathElement.getChildNodes();
                output[4] = (((Node) transPath.item(0)).getNodeValue());

                NodeList tempDirList = doc.getElementsByTagName("tempDir");
                Element tempDirElement = (Element)tempDirList.item(0);
                NodeList tempDir = tempDirElement.getChildNodes();
                output[5] = (((Node) tempDir.item(0)).getNodeValue());

        } catch (ParserConfigurationException e) {
                output[6] += ("ParserConfigurationException: " + e.toString() + "\n");
                //e.printStackTrace();
        } catch (SAXException e) {
                output[6] += ("SAXException: " + e.toString() + "\n");
                //e.printStackTrace();
        } catch (IOException e) {
                output[6] += ("IOException: " + e.toString() + "\n");
                //e.printStackTrace();
        }
        return output;
    }
}
