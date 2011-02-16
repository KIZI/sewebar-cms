/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package PMML2XTM;

import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;
import net.ontopia.topicmaps.impl.tmapi2.TopicImpl;


/**
 *
 * @author marek
 */

public class Storage {
private Map <String, TopicImpl> zoznamTopic = new HashMap<String, TopicImpl>();
private Set vztahy = new HashSet();

  public void insertAssoc(String vztah){
  vztahy.add(vztah);
  }

  public boolean isAssoc(String vztah){
    //System.out.println("Vztahy: "+vztahy.contains(vztah));
    return vztahy.contains(vztah);
  }

  public void insertTopic(TopicImpl topic,String name){
    zoznamTopic.put(name, topic);
    //System.out.println("Insert topic: "+ name + "   -   "  +topic );
  }

  public TopicImpl getTopic(String name){
    TopicImpl topic = null;
    topic = (TopicImpl) zoznamTopic.get(name);
    //System.out.println("Get topic: "+ name + "   -   "  +topic );
    return topic;
  }


  public void printSet(){
    System.out.println(zoznamTopic.entrySet());
    System.out.println("Keys "+zoznamTopic.keySet());
  }


  public boolean isStored(String topicName){
    boolean stored = false;
    TopicImpl topic = (TopicImpl) zoznamTopic.get(topicName);
    if(topic != null){
        stored = true;
    }
    return stored;
  }

}