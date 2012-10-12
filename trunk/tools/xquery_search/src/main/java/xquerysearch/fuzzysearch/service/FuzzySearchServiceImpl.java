package xquerysearch.fuzzysearch.service;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.fuzzysearch.evaluator.FuzzySearchEvaluator;
import xquerysearch.transformation.ArQueryToInternalTransformer;
import xquerysearch.transformation.AssociationRuleToInternalTransformer;

/**
 * Implementation of {@link FuzzySearchService}.
 * 
 * @author Tomas Marek
 * 
 */
public class FuzzySearchServiceImpl implements FuzzySearchService {

	@Autowired
	private FuzzySearchEvaluator evaluator;

	/**
	 * @{inheritDoc
	 */
	@Override
	public ResultSet evaluateResultSet(ResultSet resultSet, ArBuilderQuery query) {
		ArQueryInternal aqi = ArQueryToInternalTransformer.transform(query.getArQuery());
		if (resultSet != null && resultSet.getResults() != null) {
			for (Result result : resultSet.getResults()) {
				AssociationRuleInternal ari = AssociationRuleToInternalTransformer.transform(result.getRule());
				Double compliance = evaluator.evaluate(ari, aqi);
				result.setQueryCompliance(compliance);
			}
		}
		return resultSet;
	}

}
