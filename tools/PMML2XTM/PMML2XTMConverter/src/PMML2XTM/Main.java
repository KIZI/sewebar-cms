/*

 */

package PMML2XTM;

import java.io.IOException;
import java.util.List;
import javax.xml.parsers.ParserConfigurationException;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapImpl;
import org.dom4j.DocumentException;
import org.dom4j.Element;
import org.jaxen.JaxenException;
import org.tmapi.core.TMAPIException;
import org.xml.sax.SAXException;

/**
 *
 * @author Marek
 */

public class Main {

   
    private static String outputfile;
    private static String ontologyfile;
    private static String pmmlfile;
    private static Storage storage;
    private static PMMLReader pmmlreader;
    private static TopicMapImpl topicmap;
    /**
     * @param args the command line arguments
     */

    public static void main(String[] args) throws ParserConfigurationException, SAXException, IOException, DocumentException, TMAPIException, JaxenException{
        
        //outputfile = "/home/marek/bakalarka/ontbeta/apache-tomcat/webapps/omnigator/WEB-INF/topicmaps/Q1.xtm"; // Vystupny subor
        //ontologyfile = "/home/marek/bakalarka/ontbeta/apache-tomcat/webapps/omnigator/WEB-INF/topicmaps/test(ar-new)_clean.xtm"; // Subor s cistou ontologiou
        //pmmlfile = "/home/marek/bakalarka/Q.pmml"; // PMML subor
        storage = new Storage();
        pmmlreader = new PMMLReader();

        if(args.length != 0){
        ontologyfile = args[0];
        outputfile = args[1];
        Integer inputcount = args.length - 2;
        Integer arg = 2;
        XTMReader onto = new XTMReader();
        topicmap = (TopicMapImpl) onto.getMap(ontologyfile);
        System.out.println("TOPICMAP: "+ topicmap);
        while(arg <= inputcount+1){
        System.out.println("Processing file: " + (arg - 1) + " / " + inputcount);
        pmmlfile = args[arg];
        transform();
        System.out.println("Processing finished: " + (arg - 1) + " / " + inputcount);
        arg = arg + 1;
        }
        }
        else if(args.length == 0){
            System.out.println("Spustenie s defaultnymi parametrami: rules.pmml, ontology.xtm, output.xtm");
            outputfile = "realdata/o/s2-ClientAAILoanIMPLIEDCONDYear31-71xtm"; // Vystupny subor
            ontologyfile = "ontology.xtm"; // Subor s cistou ontologiou
            String[] files = {"realdata/s2-ClientAAILoanIMPLIEDCONDYear31-41.xml","realdata/s2-ClientAAILoanIMPLIEDCONDYear41-51.xml","realdata/s2-ClientAAILoanIMPLIEDCONDYear51-61.xml","realdata/s2-ClientAAILoanIMPLIEDCONDYear61-71.xml"};
            Integer inputcount = files.length;
            Integer arg = 0;
            XTMReader onto = new XTMReader();
            topicmap = (TopicMapImpl) onto.getMap(ontologyfile);
            while(arg < inputcount){
            System.out.println("Processing file: " + (arg+1) + " / " + inputcount);
            pmmlfile = files[arg];
            transform();
            arg = arg + 1;

        }
        }
        else{
        System.out.println("Program je potreba spustit s nasledujicimi parametramia: pmmlsubor.pmml cistaontologia.xtm vystupnaontologia.xtm ");
        }
        //cleanTopicMap clean = new cleanTopicMap();
        //clean.clearTopicMap();
    }

    private static void transform() throws ParserConfigurationException, SAXException, IOException, DocumentException, TMAPIException, JaxenException {
        
        pmmlreader.openfile(pmmlfile);

        List dataF = pmmlreader.getDataField();
        List derivedF = pmmlreader.getDerivedField();
        Element associationModel = pmmlreader.getAssociationModel();
        Element dataMiningTask = pmmlreader.getHeader();
        Element dataDictionary = pmmlreader.getDataDictionary();

        System.out.println(" ----------- Data Mining Task Director ------------");
        DataMiningTaskDirector dmDirector = new DataMiningTaskDirector(storage, topicmap);
        dmDirector.direct(dataMiningTask, dataDictionary, associationModel);

        System.out.println(" ----------- Data Field Director ------------");

        DataFieldDirector dataFDirector = new DataFieldDirector(storage, topicmap);
        dataFDirector.direct(dataF);

        System.out.println(" ----------- Derived Field Task Director ------------");

        DerivedFieldDirector derivedFDirect = new DerivedFieldDirector(storage, topicmap);
        derivedFDirect.direct(derivedF);

        System.out.println(" ----------- Association Model Director ------------");

        AssociationModelDirector amDirector = new AssociationModelDirector(storage, topicmap);
        amDirector.direct(associationModel);

        TMHandler handler = new TMHandler(storage, topicmap);
        handler.saveMap(outputfile);

        //storage.printSet();
        
    }

}
