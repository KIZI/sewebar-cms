package xquerysearch.transformation;

import org.springframework.oxm.castor.CastorMarshaller;

import xquerysearch.domain.arbquery.hybridquery.ArHybridBuilderQuery;
import xquerysearch.mapping.MappingCastor;

/**
 * Transformer used to transform incoming query to object.
 * 
 * @author Tomas Marek
 * 
 */
public class QueryArBuilderQueryHybridTransformer {

	private static final MappingCastor<ArHybridBuilderQuery> castor = new MappingCastor<ArHybridBuilderQuery>();

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QueryArBuilderQueryHybridTransformer() {
	}

	/**
	 * Transforms query as String to {@link ArHybridBuilderQuery} object.
	 * 
	 * @param query
	 * @return query as object
	 */
	public static ArHybridBuilderQuery transform(CastorMarshaller arbHybridQueryCastor, final String query) {
		return castor.targetToObject(arbHybridQueryCastor, query);
	}

}
