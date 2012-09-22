package xquerysearch.mapping;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;

import javax.xml.transform.stream.StreamSource;

import org.springframework.oxm.XmlMappingException;
import org.springframework.oxm.castor.CastorMarshaller;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.DerivedBooleanAttribute;
import xquerysearch.domain.Document;

/**
 * @author Tomas
 *
 */
public class TestCastor {
	
	public void testXmlToObject(CastorMarshaller castorMarshaller, DocumentDao documentDao) {
		Document doc = documentDao.getDocumentById("loose-cedent-test-1");
		
		InputStream bais = new ByteArrayInputStream( doc.getDocBody().getBytes() );
		StreamSource inSource = new StreamSource(bais);
		
		castorMarshaller.setTargetClass(DerivedBooleanAttribute.class);
		
		try {
			DerivedBooleanAttribute dba = (DerivedBooleanAttribute) castorMarshaller.unmarshal(inSource);
			System.out.println("DBA ID: " + dba.getId());
		} catch (XmlMappingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

}
