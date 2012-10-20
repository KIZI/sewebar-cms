package xquerysearch.fuzzysearch.service;

import java.util.Set;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.Result;
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
	public Set<Result> evaluateResults(Set<Result> results, ArBuilderQuery query) {
		ArQueryInternal aqi = ArQueryToInternalTransformer.transform(query.getArQuery());
		if (results != null) {
			for (Result result : results) {
				AssociationRuleInternal ari = AssociationRuleToInternalTransformer.transform(result.getRule());
				Double[][] compliance = evaluator.evaluate(ari, aqi);
				result.setQueryCompliance(compliance);
			}
		}
		return results;
	}

}
