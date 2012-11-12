package xquerysearch.transformation;

import org.springframework.oxm.castor.CastorMarshaller;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.mapping.MappingCastor;

/**
 * Transformer used to transform incoming query to object.
 * 
 * @author Tomas Marek
 * 
 */
public class QueryArBuilderQueryTransformer {

	private static final MappingCastor<ArBuilderQuery> castor = new MappingCastor<ArBuilderQuery>();

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QueryArBuilderQueryTransformer() {
	}

	/**
	 * Transforms query as String to {@link ArBuilderQuery} object.
	 * 
	 * @param query
	 * @return query as object
	 */
	public static ArBuilderQuery transform(CastorMarshaller arbQueryCastor, final String query) {
		return castor.targetToObject(arbQueryCastor, query);
	}

}
