package xquerysearch.transformer;

import xquerysearch.domain.arbquery.ArBuilderQuery;

/**
 * Transformer used to transform incoming query to object.
 * 
 * @author Tomas Marek
 * 
 */
public interface QueryObjectTransformer {

	/**
	 * Transforms query as String to {@link ArBuilderQuery} object.
	 * 
	 * @param query
	 * @return query as object
	 */
	public ArBuilderQuery transformQueryToObject(String query);

	/**
	 * Transforms query as String to {@link ArBuilderQuery} object and simplifies it.
	 * 
	 * @param query
	 * @return simplified query as object
	 */
	public ArBuilderQuery transformAndSimplifyQuery(String query);
}
