package xquerysearch.transformer;

import xquerysearch.domain.result.ResultSet;

/**
 * Transformer used to transform data retrieved from DB to objects. 
 * 
 * @author Tomas Marek
 *
 */
public interface ResultObjectTransformer {

	/**
	 * Transforms data from DB to {@link ResultSet} object.
	 * 
	 * @param result
	 * @return
	 */
	public ResultSet transform(String result);
}
