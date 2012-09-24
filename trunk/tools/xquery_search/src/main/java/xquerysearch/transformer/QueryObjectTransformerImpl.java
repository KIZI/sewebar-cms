package xquerysearch.transformer;

import org.apache.commons.lang.NotImplementedException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.oxm.castor.CastorMarshaller;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.mapping.MappingCastor;

/**
 * {@link QueryObjectTransformer} implementation.
 * 
 * @author Tomas Marek
 *
 */
public class QueryObjectTransformerImpl implements QueryObjectTransformer {

	@Autowired
	private CastorMarshaller arbQueryCastor;
	
	/*
	 * @{InheritDoc}
	 */
	@Override
	public ArBuilderQuery transformQueryToObject(String query) {
		MappingCastor<ArBuilderQuery> castor = new MappingCastor<ArBuilderQuery>();
		return castor.targetToObject(arbQueryCastor, query);
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public ArBuilderQuery transformAndSimplifyQuery(String query) {
		// TODO implement
		throw new NotImplementedException("Not implemented yet...");
	}

}
