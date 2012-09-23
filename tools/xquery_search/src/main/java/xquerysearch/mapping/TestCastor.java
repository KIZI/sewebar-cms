package xquerysearch.mapping;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;

import javax.xml.transform.stream.StreamSource;

import org.springframework.oxm.XmlMappingException;
import org.springframework.oxm.castor.CastorMarshaller;

import xquerysearch.domain.arbquery.ArBuilderQuery;

/**
 * @author Tomas
 * 
 */
public class TestCastor {

	public ArBuilderQuery testXmlToObject(CastorMarshaller castorMarshaller, String query) {

		InputStream bais = new ByteArrayInputStream(query.getBytes());
		StreamSource inSource = new StreamSource(bais);

		castorMarshaller.setTargetClass(ArBuilderQuery.class);

		try {
			return (ArBuilderQuery) castorMarshaller.unmarshal(inSource);
		} catch (XmlMappingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}
	}

}
