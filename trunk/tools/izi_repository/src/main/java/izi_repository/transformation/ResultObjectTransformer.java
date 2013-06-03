package izi_repository.transformation;

import izi_repository.domain.result.Result;
import izi_repository.mapping.MappingCastor;

import org.springframework.oxm.castor.CastorMarshaller;


/**
 * Transformer used to transform data retrieved from DB to objects.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultObjectTransformer {

	private static final MappingCastor<Result> mappingCastor = new MappingCastor<Result>();

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ResultObjectTransformer() {
	}

	/**
	 * Transforms data from DB to {@link Result} object.
	 * 
	 * @param result
	 * @return
	 */
	public static Result transform(CastorMarshaller resultCastor, final String result) {
		return mappingCastor.targetToObject(resultCastor, result);
	}

}
