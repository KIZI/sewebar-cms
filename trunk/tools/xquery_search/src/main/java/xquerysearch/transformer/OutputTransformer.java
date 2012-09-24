package xquerysearch.transformer;

import xquerysearch.domain.result.ResultSet;

/**
 * Transformer used to transform result data to response-friendly form. 
 * 
 * @author Tomas Marek
 *
 */
public interface OutputTransformer {

	/**
	 * Transforms {@link ResultSet} to response-friendly form represented as String. 
	 * 
	 * @param resultSet
	 * @return transformed ResultSet
	 */
	public String transformResultSet(ResultSet resultSet);
	
}
