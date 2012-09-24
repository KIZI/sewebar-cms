package xquerysearch.transformer;

import xquerysearch.domain.arbquery.ArBuilderQuery;

/**
 * Transformer used to transform query as object to XPath stored as String.
 * 
 * @author Tomas Marek
 *
 */
public interface QueryXpathTransformer {

	/**
	 * Transforms {@link ArBuilderQuery} to XPath query.
	 * 
	 * @param query
	 * @return XPath query, <code>null</code> if error occurred
	 */
	public String transformToXpath(ArBuilderQuery query);
}
