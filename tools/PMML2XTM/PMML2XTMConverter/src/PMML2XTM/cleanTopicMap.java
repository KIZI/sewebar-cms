/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package PMML2XTM;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.util.Collection;
import java.util.Iterator;
import net.ontopia.infoset.impl.basic.URILocator;

import net.ontopia.topicmaps.impl.basic.Topic;
import net.ontopia.topicmaps.impl.basic.TopicMap;
import net.ontopia.topicmaps.impl.basic.index.ClassInstanceIndex;
import net.ontopia.topicmaps.xml.XTMTopicMapReader;
import net.ontopia.topicmaps.xml.XTMTopicMapWriter;

import org.tmapi.core.TMAPIException;

/**
 *
 * @author marek
 */
public class cleanTopicMap {

    private TopicMap map;
    private String target = "/home/marek/bakalarka/ontbeta/apache-tomcat/webapps/omnigator/WEB-INF/topicmaps/ar-bko.xtm";
    private String source = "/home/marek/bakalarka/ontbeta/apache-tomcat/webapps/omnigator/WEB-INF/topicmaps/ar-ontology.xtm";

    public void clearTopicMap() throws MalformedURLException, IOException, TMAPIException{

        String[] locat = {
  
  "http://psi.keg.vse.cz/BackgroundKnowledgeQuantifier",
  "http://keg.vse.cz/sma/bacgrkoundtorealdatamapping",
  "http://keg.vse.cz/sma/mappingtype",
  "http://keg.vse.cz/bko/backgroundassociationrule",
  "http://keg.vse.cz/sma/schemamapping",
  "http://keg.vse.cz/sma/metafieldbinnedcontenttoderivedfieldcontentmapping",
  "http://keg.vse.cz/sma/metafieldcontenttofieldcontentmapping",
  "http://keg.vse.cz/sma/metafieldrawcontenttodatafieldcontentmapping",
  "http://keg.vse.cz/sma/schemamapping",
  "http://keg.vse.cz/sma/metafieldtofieldmapping",
  "http://keg.vse.cz/sma/metafieldcontenttofieldcontentmapping",
  "http://keg.vse.cz/bko/backgroundassociationrule",
  "http://keg.vse.cz/bko/backgroundknowledge",
  "http://keg.vse.cz/bko/basicbooleanmetaattribute",
  "http://keg.vse.cz/bko/booleanmetaattribute",
  "http://keg.vse.cz/bko/collation",
  "http://keg.vse.cz/bko/collationsense",
  "http://keg.vse.cz/bko/collationtype",
  "http://keg.vse.cz/bko/derivedbooleanmetaattribute",
  "http://keg.vse.cz/bko/discretizationhint",
  "http://keg.vse.cz/bko/doubleboolgrowth",
  "http://keg.vse.cz/bko/exhaustivenumeration",
  "http://keg.vse.cz/bko/knowledgevalidity",
  "http://keg.vse.cz/bko/metaattributte",
  "http://keg.vse.cz/bko/metaattributedictionary",
  "http://keg.vse.cz/bko/metaattributerelationconstituent",
  "http://keg.vse.cz/bko/metafield",
  "http://keg.vse.cz/bko/metafieldbinnedcontent",
  "http://keg.vse.cz/bko/metafieldcontent",
  "http://keg.vse.cz/bko/enumerationbin",
  "http://keg.vse.cz/bko/metafieldinterval",
  "http://keg.vse.cz/bko/metafieldintervalbin",
  "http://keg.vse.cz/bko/metafieldintervalenumeration",
  "http://keg.vse.cz/bko/metafieldcontent",
  "http://keg.vse.cz/bko/enumerationbin",
  "http://keg.vse.cz/bko/metafieldinterval",
  "http://keg.vse.cz/bko/metafieldintervalbin",
  "http://keg.vse.cz/bko/metafieldintervalenumeration",
  "http://keg.vse.cz/bko/value",
  "http://keg.vse.cz/bko/mutualinfluence",
  "http://keg.vse.cz/bko/pattern",
  "http://keg.vse.cz/bko/preprocessinghint",
  "http://keg.vse.cz/bko/rawcontent",
  "http://keg.vse.cz/bko/variability",

  };
        
        map = this.getMap(source);

        for (int i = 0; i < locat.length; i++) {
            String var = locat[i];
            System.out.println(i);
            runQuery(var);
        }

         new XTMTopicMapWriter(target).write(map);
    }

    private TopicMap getMap(String path) throws IOException{

      XTMTopicMapReader reader = new XTMTopicMapReader(new File(path));
      TopicMap topicmap = (TopicMap) reader.read();
      System.out.println("Map loaded");
      return topicmap;

}

    private void runQuery(String loc) throws MalformedURLException, IOException{

    System.out.println("LOC: "+loc);
    URILocator locator = new URILocator(loc);

    Topic topic = (Topic) map.getTopicBySubjectIdentifier(locator);

    if(topic!=null){
        ClassInstanceIndex cindex = (ClassInstanceIndex) map.getIndex("net.ontopia.topicmaps.core.index.ClassInstanceIndexIF");

        Collection col = cindex.getTopics(topic);

        System.out.println(topic);

        Iterator it = col.iterator();
        while(it.hasNext()){
            Topic tp = (Topic) it.next();
            tp.remove();
        }
    }
    else if(topic==null){
    System.out.println("Chybny Locator: nenachadza sa v TM"+loc);
    }
    }

}

