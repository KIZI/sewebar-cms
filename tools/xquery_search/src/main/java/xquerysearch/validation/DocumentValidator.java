package xquerysearch.validation;

import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.util.logging.Logger;

import javax.xml.transform.Source;
import javax.xml.transform.stream.StreamSource;
import javax.xml.validation.Schema;
import javax.xml.validation.SchemaFactory;
import javax.xml.validation.Validator;

import org.xml.sax.SAXException;

import xquerysearch.controller.MainController;

/**
 * 
 * @author Tomas Marek
 */
public class DocumentValidator {
   
	private static Logger logger = MainController.getLogger();
	
    /**
     * Validates given document
     * @param doc document to validate
     * @return if valid returns <code>true</code> else <code>false</code>
     */
    public static boolean validate(String doc, String schemaPath) {
        try {
		    SchemaFactory factory = SchemaFactory.newInstance("http://www.w3.org/2001/XMLSchema");
		    File schemaLocation = new File(schemaPath);
		    Schema schema = factory.newSchema(schemaLocation);
		    Validator validator = schema.newValidator();
		    InputStream is = new ByteArrayInputStream(doc.getBytes("UTF-8"));
		    Source source = new StreamSource(is);
		    validator.validate(source);
		    return true;
        } catch (IOException e) {
        	logger.warning("Validation failed! - IO exception");
        	return false;
        } catch (SAXException e) {
        	logger.warning("Validation failed! - SAX exception");
        	return false;
        }
    }
}
