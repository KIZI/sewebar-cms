package xquerysearch.mapping;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;

import javax.xml.transform.stream.StreamSource;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.oxm.XmlMappingException;
import org.springframework.oxm.castor.CastorMarshaller;

import xquerysearch.logging.event.EventLogger;

/**
 * Class used to map source information to object representation.
 * 
 * @author Tomas Marek
 * 
 */
public class MappingCastor<T> {
	
	@Autowired
	private EventLogger logger;
	
	/**
	 * Maps string source data to domain object specified in {@link CastorMarshaller} bean.
	 * 
	 * @param castorMarshaller
	 * @param source
	 * @return mapped source data as specified domain object
	 */
	@SuppressWarnings("unchecked")
	public T targetToObject(CastorMarshaller castorMarshaller, final String source) {

		InputStream bais = new ByteArrayInputStream(source.getBytes());
		StreamSource inSource = new StreamSource(bais);

		try {
			return (T) castorMarshaller.unmarshal(inSource);
		} catch (XmlMappingException e) {
			logger.logWarning(this.getClass().toString(), "Mapping String to domain object failed! - XmlMappingException");
			return null;
		} catch (IOException e) {
			logger.logWarning(this.getClass().toString(), "Mapping String to domain object failed! - I/O exception");
			return null;
		} 
	}
}