package xquerysearch.mapping;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.logging.Logger;

import javax.xml.transform.stream.StreamSource;

import org.springframework.oxm.XmlMappingException;
import org.springframework.oxm.castor.CastorMarshaller;

/**
 * Class used to map source information to object representation.
 * 
 * @author Tomas Marek
 * 
 */
public class MappingCastor<T> {
	
	private static final Logger log = Logger.getLogger("MappingCastor");
	
	/**
	 * Maps string source data to domain object specified in {@link CastorMarshaller} bean.
	 * 
	 * @param castorMarshaller
	 * @param source
	 * @return mapped source data as specified domain object
	 */
	@SuppressWarnings("unchecked")
	public T targetToObject(CastorMarshaller castorMarshaller, String source) {

		InputStream bais = new ByteArrayInputStream(source.getBytes());
		StreamSource inSource = new StreamSource(bais);

		try {
			return (T) castorMarshaller.unmarshal(inSource);
		} catch (XmlMappingException e) {
			log.warning("Mapping String to domain object failed! - XmlMappingException");
			return null;
		} catch (IOException e) {
			log.warning("Mapping String to domain object failed! - I/O exception");
			return null;
		} 
	}
}