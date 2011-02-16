/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package PMML2XTM;
import java.io.IOException;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.List;
import net.ontopia.topicmaps.impl.tmapi2.AssociationImpl;
import net.ontopia.topicmaps.impl.tmapi2.OccurrenceImpl;
import net.ontopia.topicmaps.impl.tmapi2.TopicImpl;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapImpl;
import net.ontopia.topicmaps.impl.tmapi2.index.TypeInstanceIndexImpl;
import net.ontopia.topicmaps.xml.XTM2TopicMapWriter;

import org.tmapi.core.Locator;
import org.tmapi.index.TypeInstanceIndex;

/**
 *
 * @author Marek
 */

public class TMHandler {

  TopicMapImpl topicmap;
  
  List associations = new ArrayList();

  private final Storage storage;
  
    TMHandler(Storage storage, TopicMapImpl topicmap){
        this.storage = storage;
        this.topicmap = topicmap;
    }


  public void createAssocAB(String assocLocator, TopicImpl topicIA, String ALoc, TopicImpl topicIB, String BLoc){
     
     Locator locAssoc = topicmap.createLocator(assocLocator);
     TopicImpl assoctype = topicmap.getTopicBySubjectIdentifier(locAssoc);

     Locator locA = topicmap.createLocator(ALoc);
     TopicImpl topicTypeA = topicmap.getTopicBySubjectIdentifier(locA);

     Locator locB = topicmap.createLocator(BLoc);
     TopicImpl topicTypeB = topicmap.getTopicBySubjectIdentifier(locB);
     
     AssociationImpl assoc = null;
     
     if(assoctype==null){
         System.out.println("Chybny lokator Asociacie: "+assocLocator);
     }

     else{ assoc = (AssociationImpl) topicmap.createAssociation(assoctype);
        if(topicTypeA==null ^ topicIA==null){
            System.out.println("TopicTypeA: "+ topicTypeA + " TopicIA " + topicIA);
            System.out.println("Chybny A lokator: "+ALoc+" Asociacia: "+ assoc + " Typ: " + assoctype);
        }
        else{ assoc.createRole(topicTypeA, topicIA); }
        if(topicTypeB==null ^ topicIB==null){
            System.out.println("TopicTypeB: "+ topicTypeB + " TopicIB " + topicIB);
            System.out.println("Chybny B lokator: "+BLoc+" Asociacia: "+ assoc + " Typ: " + assoctype);
        }
        else{
         //System.out.println("TTBtopic: " + topicTypeB);
         //System.out.println("TIBtopic: " + topicIB);
         assoc.createRole(topicTypeB, topicIB); }
        }
    }

  public TopicImpl createInstance(String Loc, String name){
      Locator locator = topicmap.createLocator(Loc);
      TopicImpl topicInstance = null;
        if(storage.isStored(name) == true){
            topicInstance = storage.getTopic(name);}
        else{
         topicInstance = (TopicImpl) topicmap.createTopic();
         storage.insertTopic(topicInstance,name);
         topicInstance.createName(name);
         TopicImpl topicType = (TopicImpl) topicmap.getTopicBySubjectIdentifier((Locator) locator);
         if(topicType==null){
            System.out.println("Chybny lokator: "+locator+" . Topic type: "+ topicInstance + " " + name);}
         else{topicInstance.addType(topicType); }
       }
      return topicInstance;
    }


   public TopicImpl createInstance(String Loc, String name, String prefix){
      TopicImpl topicInstance = null;
      Locator locator = topicmap.createLocator(Loc);
      if(storage.isStored(prefix+name) == true){
        topicInstance = storage.getTopic(prefix+name);
      }
      else{
      topicInstance = (TopicImpl) topicmap.createTopic();
      storage.insertTopic(topicInstance,prefix+name);
      topicInstance.createName(name);
      TopicImpl topicType = (TopicImpl) topicmap.getTopicBySubjectIdentifier((Locator) locator);
      if(topicType==null){
        System.out.println("Chybny lokator: "+locator+" . Topic type: "+ topicInstance + " " + name);}
      else if(name==null){
        System.out.println("Chybne meno pre loc: "+locator+" . Topic type: "+ topicInstance + " " + name);
      }
      else{topicInstance.addType(topicType); }
      }
      return topicInstance;
    }

  
  public TopicImpl createTopic(Locator locator, String prefix, String name){
      TopicImpl topicInstance = (TopicImpl) topicmap.createTopic();
      storage.insertTopic(topicInstance,prefix+name);
      topicInstance.createName(name);
      TopicImpl topicType = (TopicImpl) topicmap.getTopicBySubjectIdentifier((Locator) locator);
      if(topicType==null){
        System.out.println("Chybny lokator: "+locator+" . Topic type: "+ topicInstance + " " + name);}
      else{topicInstance.addType(topicType); }
      return topicInstance;
  }

  public void createOccurence(TopicImpl topic, String Loc, String value){
    
    Locator locator = topicmap.createLocator(Loc);
    TopicImpl topicType = topicmap.getTopicBySubjectIdentifier(locator);

    if(topicType==null){
      
      System.out.println("Chybny lokator vyskytu: "+locator);
    }
    else{
        
        OccurrenceImpl oc = (OccurrenceImpl) topic.createOccurrence(topicType, value);
    }
  }

  public TopicImpl getTopicTypeInstance(String loc) throws MalformedURLException{
    TopicImpl returnTopic = null;
    Locator locator = topicmap.createLocator(loc);
    TopicImpl topic = topicmap.getTopicBySubjectIdentifier(locator);
    TypeInstanceIndexImpl cindex = (TypeInstanceIndexImpl) topicmap.getIndex(TypeInstanceIndex.class);
    Iterator it = cindex.getTopics(topic).iterator();
    while(it.hasNext()){
        TopicImpl tp = (TopicImpl) it.next();
        returnTopic = tp;
    }
    return returnTopic;
  }

  public Collection getClassTopics(String loc) {
    Locator locator = topicmap.createLocator(loc);
    TopicImpl topic = topicmap.getTopicBySubjectIdentifier(locator);
    TypeInstanceIndexImpl cindex = (TypeInstanceIndexImpl) topicmap.getIndex(TypeInstanceIndex.class);
    Collection col = cindex.getTopics(topic);
    return col;    
  }

  public void saveMap(String file) throws IOException{
      XTM2TopicMapWriter writer = new XTM2TopicMapWriter(file);
      writer.write(((TopicMapImpl) topicmap).getWrapped());
  }

}
