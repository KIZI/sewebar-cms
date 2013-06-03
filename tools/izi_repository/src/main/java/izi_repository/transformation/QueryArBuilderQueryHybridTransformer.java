package izi_repository.transformation;

import izi_repository.domain.arbquery.hybridquery.ArHybridBuilderQuery;
import izi_repository.mapping.MappingCastor;

import org.springframework.oxm.castor.CastorMarshaller;


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
