package izi_repository.transformation;

import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.mapping.MappingCastor;

import org.springframework.oxm.castor.CastorMarshaller;


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
