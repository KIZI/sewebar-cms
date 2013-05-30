package xquerysearch.transformation;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;

import javax.xml.transform.TransformerException;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.logging.event.EventLogger;

/**
 * Class providing XSLT transformation features.
 * 
 * @author Tomas Marek
 * 
 */
public class XsltTransformer {
	
	@Autowired
	private EventLogger logger;
	
	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private XsltTransformer() {
	}

	/**
	 * Transforms document (as file) using given XSLT transformation
	 * 
	 * @param xmlFile
	 *            file to transform
	 * @param xsltFile
	 *            file with transformation
	 * @param docID
	 *            id document id (to add into transformed document)
	 * @param creationTime
	 *            creation time (to add into transformed document)
	 * @param reportUri
	 *            report URI (to add into transformed document)
	 * @return transformed document as <code>string</code> or <code>null</code>
	 *         when error occurs
	 */
	public static String transform(File xmlFile, File xsltFile, String docID, String creationTime,
			String reportUri, EventLogger logger) {
		try {
			FileInputStream fis = new FileInputStream(xmlFile);
			return transformInternal(fis, xsltFile, docID, creationTime, reportUri, logger);
		} catch (FileNotFoundException e) {

			return null;
		}
	}

	/**
	 * Transforms document (as string) using given XSLT transformation
	 * 
	 * @param xmlFile
	 *            file to transform
	 * @param xsltFile
	 *            file with transformation
	 * @param docID
	 *            id document id (to add into transformed document)
	 * @param creationTime
	 *            creation time (to add into transformed document)
	 * @param reportUri
	 *            report URI (to add into transformed document)
	 * @return transformed document as <code>string</code> or <code>null</code>
	 *         when error occurs
	 */
	public static String transform(String xmlString, File xsltFile, String docID, String creationTime,
			String reportUri, EventLogger logger) {
		try {
			byte[] bytes = xmlString.getBytes("UTF-8");
			ByteArrayInputStream bais = new ByteArrayInputStream(bytes);

			return transformInternal(bais, xsltFile, docID, creationTime, reportUri, logger);
		} catch (UnsupportedEncodingException e) {
			logEncoding(logger);
			return null;
		}
	}

	/**
	 * 
	 * @param xmlStream
	 * @param xsltFile
	 * @param docID
	 * @param creationTime
	 * @param reportUri
	 * @return
	 */
	private static String transformInternal(InputStream xmlStream, File xsltFile, String docID,
			String creationTime, String reportUri, EventLogger logger) {
		try {
			ByteArrayOutputStream baos = new ByteArrayOutputStream();

			javax.xml.transform.Source xmlSource = new javax.xml.transform.stream.StreamSource(xmlStream);
			javax.xml.transform.Source xsltSource = new javax.xml.transform.stream.StreamSource(xsltFile);
			javax.xml.transform.Result result = new javax.xml.transform.stream.StreamResult(baos);

			// create an instance of TransformerFactory
			javax.xml.transform.TransformerFactory transFact = javax.xml.transform.TransformerFactory
					.newInstance();

			javax.xml.transform.Transformer trans;

			trans = transFact.newTransformer(xsltSource);
			trans.setParameter("joomlaID", docID);
			trans.setParameter("creationTime", creationTime);
			trans.setParameter("reportURI", reportUri);
			trans.transform(xmlSource, result);

			return baos.toString("UTF-8");
		} catch (UnsupportedEncodingException e) {
			logEncoding(logger);
			return null;
		} catch (TransformerException e) {
			logger.logWarning(XsltTransformer.class.getName(), "Error during transformation occured - transformer exception");
			return null;
		}
	}

	/**
	 * Sends warning with unsupported encoding message to logger
	 */
	private static void logEncoding(EventLogger logger) {
		logger.logWarning(XsltTransformer.class.getName(), "Error during transformation occured - unsupported encoding");
	}

}
