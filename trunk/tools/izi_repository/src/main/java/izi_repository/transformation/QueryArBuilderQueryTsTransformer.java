package izi_repository.transformation;

import izi_repository.domain.arbquery.tasksetting.ArTsBuilderQuery;
import izi_repository.mapping.MappingCastor;

import org.springframework.oxm.castor.CastorMarshaller;


/**
 * Transformer used to transform incoming query to object.
 * 
 * @author Tomas Marek
 * 
 */
public class QueryArBuilderQueryTsTransformer {

	private static final MappingCastor<ArTsBuilderQuery> castor = new MappingCastor<ArTsBuilderQuery>();

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QueryArBuilderQueryTsTransformer() {
	}

	/**
	 * Transforms query as String to {@link ArTsBuilderQuery} object.
	 * 
	 * @param query
	 * @return query as object
	 */
	public static ArTsBuilderQuery transform(CastorMarshaller arbTsQueryCastor, final String query) {
		return castor.targetToObject(arbTsQueryCastor, query);
	}

}
