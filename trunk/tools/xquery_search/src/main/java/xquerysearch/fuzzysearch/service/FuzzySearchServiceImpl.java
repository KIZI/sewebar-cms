package xquerysearch.fuzzysearch.service;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.result.Result;
import xquerysearch.fuzzysearch.evaluator.FuzzySearchEvaluator;
import xquerysearch.service.QueryService;
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

	@Autowired
	private QueryService queryService;
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Result> getFuzzyResultsByQuery(ArBuilderQuery query, QuerySettings settings) {
		List<Result> results = queryService.getResultList(query, settings);
		ArQueryInternal aqi = ArQueryToInternalTransformer.transform(query.getArQuery());
		if (results != null) {
			for (Result result : results) {
				AssociationRuleInternal ari = AssociationRuleToInternalTransformer
						.transform(result.getRule());
				double[][] compliance = evaluator.evaluate(ari, aqi);
				result.setQueryCompliance(compliance);
			}
		}
		return results;
	}
}
