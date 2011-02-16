/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package PMML2XTM;

import java.io.IOException;
import net.ontopia.topicmaps.core.TopicMapIF;
import net.ontopia.topicmaps.core.TopicMapReaderIF;
import net.ontopia.topicmaps.impl.tmapi2.MemoryTopicMapSystemImpl;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapImpl;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapSystemFactory;
import net.ontopia.topicmaps.impl.tmapi2.TopicMapSystemIF;
import net.ontopia.topicmaps.utils.ImportExportUtils;
import org.tmapi.core.TMAPIException;

/**
 *
 * @author Marek
 */
public class XTMReader {

public TopicMapImpl getMap(String path) throws IOException, TMAPIException {

    TopicMapReaderIF reader = ImportExportUtils.getReader(path);
    TopicMapIF tm = reader.read();
        // do TMAPI setup
        TopicMapSystemFactory factory = new TopicMapSystemFactory();
        TopicMapSystemIF sys = (TopicMapSystemIF) factory.newTopicMapSystem();
        // create TMAPI topic map object
        TopicMapImpl topicmap = ((MemoryTopicMapSystemImpl) sys).createTopicMap(tm);

        return topicmap;
}

}